<?php
namespace Aggregator\Component;

class Twitter extends \Aggregator\Component\AggregatorAbstract
	{
		/**
		 * username du compte twitter
		 * @var string
		 */
		private $_userName;
		
		/**
		 * userid
		 * @var int
		 */
		private $_userID;
		
		/**
		 * Nombre limite des posts
		 * @var number
		 */
		private $_limit;

		/**
		 * eliminer les espaces du username
		 * @var bolean
		 */
		private $_trim;
		
		/**
		 * Twitter application consumer key
		 * @var string
		 */
		private $_consumerKey;
		
		/**
		 * Twitter application secret consumer key
		 * @var string
		 */
		private $_consumerSecret;
		
		/**
		 * twitter application access token
		 * @var string
		 */
		private $_accessToken;
		
		/**
		 * twitter application secret access token
		 * @var string
		 */
		private $_accessTokenSecret;
		
		/**
		 * path de l'api twitter
		 * @var string
		 */
		const API_URI = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
		
		/**
		 * twitter path
		 * @var bolean
		 */
		const BASE_URI = 'https://twitter.com/';
		
		/**
		 * constructeur
		 * @param Zend_config_ini $conf
		 */
		public function __construct($conf)
		{
			$this->_userName 			= $conf['userName'];
			$this->_userID				= $conf['userID'];
			$this->_trim 				= $conf['trimUserName'];
			$this->_limit 				= $conf['limit'];
			$this->_consumerKey 		= $conf['consumerKey'];
			$this->_consumerSecret 		= $conf['consumerSecret'];
			$this->_accessToken 		= $conf['accessToken'];
			$this->_accessTokenSecret	= $conf['accessTokenSecret'];
		}
		
		/**
		 * la valeur de retour de cette fonction est n tweets
		 * les retweets ne sont pas inclus
		 * @return array
		 */
		public function getPosts(){
			$options = array(
					'user_id' => $this->_userID,
					'screen_name' => $this->_userName,
					'count' => 50,
					'exclude_replies' => true,
					'trim_user' => $this->_trim
			);
			$url = parent::_getUrl(self::API_URI, $options);
			$connection = $this->_getConnectionWithAccessToken($this->_consumerKey, $this->_consumerSecret, $this->_accessToken, $this->_accessTokenSecret);
			$tweets = $connection->get($url);
    		return $this->_loop($tweets, $this->_userName);
		}
		
		/**
		 * Twitter OAuth
		 * @param string $cons_key
		 * @param string $cons_secret
		 * @param string $oauth_token
		 * @param string $oauth_token_secret
		 * @return TwitterOAuth
		 */
		private function _getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
			require_once(__DIR__ .'/../lib/Twitter/Twitteroauth.php');
			$connection = new \TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
			return $connection;
		}
		
		/**
		 * Parcourir le resultat
		 * @see Uzik_Aggregator_Abstract::_loop()
		 * @param stdClass $result
		 * @param string $compte
		 * @return array
		 */
		protected function _loop($result,$compte = false){
			$posts = array();
			$i = 0;
			foreach ($result as $row){
				if(substr($row->text,0,4) != 'RT @'){
					$i++;
					$posts[] = $this->_parse($row,$compte);
				}
				if($i == $this->_limit){
					break;
				}
			}
			return $posts;
		}
		
		/**
		 * extraction de donnees
		 * @param stdClass $entry
		 * @param string $compte
		 * @return array
		 */
		protected function _parse($entry,$compte = false){
				return array(
						'type' => 'tweet',
						'date' => strtotime($entry->created_at),
						'message' => $this->_parseProfile($this->_parseURL($entry->text)),
						'source' => $entry->source,
						'lien' => self::BASE_URI.$compte.'/status/'.$entry->id_str,
						'likes' => $entry->retweet_count,
						'comments' => $entry->favorite_count
				);
		}
		
		/**
		 * remplacer les profils et tags twitter par des liens.
		 * @param string
		 * @return string
		 */
		private function _parseProfile($text){
			$text = preg_replace('/@([a-z0-9_]+)/i', '<a href="'.self::BASE_URI.'$1" target="_blank">@$1</a>', $text);
			return preg_replace('/#([a-z0-9_]+)/i', '<a href="'.self::BASE_URI.'search?q=#$1&src=hash" target="_blank">#$1</a>', $text);
		}
		
		/**
		 * @see Uzik_Aggregator_Abstract::_getAttachement()
		 */
		protected function _getAttachement($post){
			return array();
		}
	}
?>