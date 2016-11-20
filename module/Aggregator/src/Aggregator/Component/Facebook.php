<?php
namespace Aggregator\Component;

class Facebook extends \Aggregator\Component\AggregatorAbstract
	{
		
		/**
		 * ID de la page facebook
		 * @var string
		 */
		private $_pageID;
		
		/**
		 * ID de l'application facebook
		 * @var int
		 */
		private $_appID;
		
		/**
		 * clé secret de l'application facebook
		 * @var string
		 */
		private $_appSecret;
		
		/**
		 * access token
		 * @var string
		 */
		private $_token;
		/**
		 * Nombre limite des posts
		 * @var number
		 */
		private $_limit;
		
		/**
		 * objet facebook
		 * @var object
		 */
		private $_facebook;
		
		/**
		 * Facebook url
		 * @var string
		 */
		const BASE_URI = 'https://www.facebook.com/';
		
		/**
		 * Auth URL
		 * @var string
		 */
		const API_URI = 'https://graph.facebook.com/oauth/access_token';
		
		/**
		 * Host (graph facebook)
		 * @param string
		 */
		const HOST_URI = 'graph.facebook.com';
		
		/**
		 * constructeur
		 * @param Zend_config_ini $conf
		 */
		public function __construct($conf)
		{
			$this->_pageID 		= $conf['pageID'];
			$this->_appID 		= $conf['appID'];
			$this->_appSecret 	= $conf['appSecret'];
			$this->_limit 		= $conf['limit'];
			$this->_token		= isset($conf['token']) ? $conf['token'] : null;
		}
		
		//get instance de l'objet facebook
		/**
		 * get Singleton instance de la classe facebook
		 * @return boolean
		 */
		private function _getInstanceFB(){
			if(!is_object($this->_facebook)){
				require_once(__DIR__ .'/../lib/Facebook/facebook.php');
				$this->_facebook = new \Facebook(array(
						'appId'  => $this->_appID,
						'secret' => $this->_appSecret
				));
			}
			return true;
		}
		
		/**
		 * la valeur de retour de cette fonction est n post(s) facebook
		 * @param string $path
		 * @param string $section
		 * @return array
		 */
		public function getPosts(){
			try {
				//limit +10 a cause du filtrage
				$url = parent::_getUrl('/', array('limit' => $this->_limit + 10), $this->_pageID.'/feed');
				$this->_getInstanceFB();
				/*if(!$this->_verifToken())
					return null;
				else{*/
					$result = $this->_facebook->api($url);
					return $this->_loop($result['data']);
				//}
			}catch(FacebookApiException $e) {
				$this->setException(json_encode($e->getResult()));
				return null;
			}
		}
		
		/**
		 * verifier l'existance du token
		 * @return boolean
		 */
		private function _verifToken(){
			if(isset($this->_token)){
				$token = $this->_getPermanetToken();
				if($token){
					$this->_updateToken($token, $path, $section);
					return true;
				}
				else return false;
			}else return true;
		}
		/**
		 * generer un access token permanant a travers un access token temporaire
		 * @param string $token
		 * @return string|boolean
		 */
		private function _getPermanetToken(){
			$options = array(
					'client_id' => $this->_appID,
					'client_secret' => $this->_appSecret,
					'grant_type' => 'fb_exchange_token',
					'fb_exchange_token' => $this->_token
			);
			$graph_url   = parent::_getUrl(self::API_URI, $options);
			$ch = curl_init($graph_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: '.self::HOST_URI));
			$access_token = curl_exec($ch);
			curl_close($ch);
			if(strstr($access_token, "access_token=")){
				parse_str($access_token, $output);
				$access_token = $output['access_token'];
				$this->_facebook->setAccessToken($access_token);
				return $access_token;
			}else return false;
		}
		
		/**
		 * MAJ le token facebook
		 *
		 * @param string $token
		 * @param string $id
		 * @return void
		 */
		private function _updateToken($token, $path, $section){
			$config = new Zend_Config_Ini($path,null,array('skipExtends' => true,'allowModifications' => true));
			$config->$section->facebook->token = $token;
			$writer = new Zend_Config_Writer_Ini(array('config' => $config, 'filename' => $path));
			$writer->write();
		}
		
		/**
		 * Parcourir le resultat
		 * @see Uzik_Aggregator_Abstract::_loop()
		 * @param array $result
		 * @return array
		 */
		protected function _loop($result){
			$posts = array();
			$i = 0;
			foreach($result as $row){
				$isAdmin = explode('_',$row['id']);
				if($isAdmin[0] == $row['from']['id']){
					$i++;
					$posts[] = $this->_parse($row);
				}
				if($i == $this->_limit){
					break;
				}
			}
			return $posts;
		}
		
		/**
		 * return le nombre des j'aimes et commentaires facebook
		 * @param array $entry
		 * @param string $type
		 * @return number
		 */
		private function _getCount($entry,$type){
			if(isset($entry[$type]['count'])) return $entry[$type]['count'];
			else return 0;
		}
		
		/**
		 * return le message facebook pour chaque post
		 * @param array $entry
		 * @return string
		 */
		private function _getMessage($entry){
			if(isset($entry['message'])) return parent::_parseURL($entry['message']);
			elseif(isset($entry['story'])) return parent::_parseURL($entry['story']);
			else return '';
		}
		
		/**
		 * Extraction de donnees
		 * @see Uzik_Aggregator_Abstract::_parse()
		 * @param array $entry
		 * @return array
		 */
		protected function _parse($entry){
			if($entry['type'] == 'status'){
				return  array(
						'type' => 'fb_status',
						'date' => strtotime($entry['created_time']),
						'message' => self::_getMessage($entry),
						'source' => '',
						'lien' => self::BASE_URI.$entry['id'],
						'likes' => self::_getCount($entry, 'likes'),
						'comments' => self::_getCount($entry, 'comments')
				);
			}elseif($entry['type'] == 'link'){
				return array(
						'type' => 'fb_link',
						'date' => strtotime($entry['created_time']),
						'message' => $entry['message'],
						'source' => $entry['link'],
						'lien' => self::BASE_URI.$entry['id'],
						'likes' => self::_getCount($entry, 'likes'),
						'comments' => self::_getCount($entry, 'comments')
				);
			}elseif($entry['type'] == 'photo'){
				return array(
						'type' => 'fb_photo',
						'date' => strtotime($entry['created_time']),
						'message' => self::_getMessage($entry),
						'source' => $this->_getAttachement($entry['picture']),
						'lien' => $entry['link'],
						'likes' => self::_getCount($entry, 'likes'),
						'comments' => self::_getCount($entry, 'comments')
				);
			}elseif($entry['type'] == 'video'){
				return array(
						'type' => 'fb_video',
						'date' => strtotime($entry['created_time']),
						'message' => $entry['description'],
						'source' => $this->_getAttachement($entry['picture']),
						'lien' => self::BASE_URI.$entry['id'],
						'likes' => self::_getCount($entry, 'likes'),
						'comments' => self::_getCount($entry, 'comments')
				);
			}
		}
		
		/**
		 * @see Uzik_Aggregator_Abstract::_getAttachement()
		 */
		protected function _getAttachement($src){
			return array(
					'small' => $src,
					'medium' => str_replace('_s.','_n.',$src),
					'large' => str_replace('_s.','_b.',$src)
			);
		}
	}
?>