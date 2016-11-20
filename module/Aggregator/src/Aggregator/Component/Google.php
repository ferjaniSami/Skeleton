<?php
namespace Aggregator\Component;

class Google extends \Aggregator\Component\AggregatorAbstract
	{
		
		/**
		 * ID d'un membre google plus
		 * @var int
		 */
		private $_userID;
		
		/**
		 * dev key
		 * @var string
		 */
		private $_devKEY;
		
		/**
		 * nombre limite des posts
		 * @var number
		 */
		private $_limit;
		
		/**
		 * url de l'api google plus
		 * @var string
		 */
		const API_URI = 'https://www.googleapis.com/plus/v1/people/';
		
		/**
		 * constructeur
		 * @param Zend_config_ini $conf
		 */
		public function __construct($conf)
		{
			$this->_userID 		= $conf['userID'];
			$this->_devKEY 		= $conf['devKEY'];
			$this->_limit 		= $conf['limit'];
		}
		
		
		/**
		 * la valeur de retour de cette fonction est n post(s) facebook
		 * @return array
		 */
		public function getPosts(){
			$options = array(
					'maxResults' => $this->_limit,
					'key' => $this->_devKEY
			);
			$result = parent::_fileGetContent(parent::_getUrl(self::API_URI, $options,$this->_userID.'/activities/public'));
			return $this->_loop($result);
		}
		
		/**
		 * parcourir le resultat
		 * @see Uzik_Aggregator_Abstract::_loop()
		 * @param StdClass $searchResponse
		 * @return array
		 */
		protected function _loop($searchResponse){
			$posts = array();
			foreach ($searchResponse->items as $searchResult) {
				$posts[] = $this->_parse($searchResult);
			}
			return $posts;
		}
		
		/**
		 * extraction de donnees
		 * @see Uzik_Aggregator_Abstract::_parse()
		 * @param StdClass $post
		 * @return array
		 */
		protected function _parse($post){
			$attach = $post->object->attachments[0];
			$src = $this->_getSource($attach);
			return array('type' => 'google',
					'date' => strtotime($post->published),
					'message' => $post->object->content.' '.$this->_getMessage($attach),
					'source' => $this->_getAttachement($src),
					'lien' => $post->url,
					'likes' => $post->object->plusoners->totalItems,
					'comments' => $post->object->replies->totalItems
			);
		}
		
		/**
		 * @see Uzik_Aggregator_Abstract::_getAttachement()
		 */
		protected function _getAttachement($src){
			return array(
							'small' 	=> $this->_replace($src, 100),
							'medium' 	=> $this->_replace($src, 250),
							'large' 	=> $src
					);
		}
		
		/**
		 * selection d'une image
		 * @param StdClass $attach
		 * @return string
		 */
		private function _getSource($attach){
			if($attach->objectType != null && ($attach->objectType == 'photo' || $attach->objectType == 'video'))
				return $attach->image->url;
			elseif ($attach->objectType != null && $attach->objectType == 'album'){
				return $attach->thumbnails[0]->image->url;
			}
			else return '';
		}
		
		/**
		 * renvoie le message d'un post google plus
		 * @param StdClass $attach
		 * @return string
		 */
		private function _getMessage($attach){
			if($attach->objectType != null && $attach->objectType == 'article' )
				return $attach->content.' '.parent::_parseURL($attach->url);
			else return '';
		}
		
		/**
		 * str_replace
		 * @param string $str
		 * @param int $size
		 * @return string
		 */
		private function _replace($str, $size){
			$search = array('w379-h379', 'w506-h750');
			$replace = array('w'.$size.'-h'.$size, 'w'.$size.'-h'.$size);
			return str_replace($search, $replace, $str);
		}
	}
?>