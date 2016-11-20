<?php
namespace Aggregator\Component;
	
class Flickr extends \Aggregator\Component\AggregatorAbstract
	{
		/**
		 * ID de l'utilisateur
		 * @var string
		 */
		private $_userID;
		
		/**
		 * API key
		 * @var string
		 */
		private $_apiKey;
		
		/**
		 * Nombre limite des resultat
		 * @var number
		 */
		private $_limit;
		
		/**
		 * path de l'api flickr
		 * @var string
		 */
		const API_URI = 'http://api.flickr.com/services/rest/';
		
		/**
		 * path d'une photo flickr
		 * @var string
		 */
		const BASE_URI = 'http://www.flickr.com/photos/';
		
		/**
		 * constructeur
		 * @param Zend_config_ini $conf
		 */
		public function __construct($conf)
		{
			$this->_userID 		= $conf['userID'];
			$this->_apiKey		= $conf['apiKey'];
			$this->_limit 		= $conf['limit'];
		}
		
		/**
		 * la valeur de retour de cette fonction est n photos flickr
		 * @return StdClass
		 */
		public function getPosts(){
			$options = array(
					'method' => 'flickr.people.getPublicPhotos',
					'api_key' => $this->_apiKey,
					'user_id' => $this->_userID,
					'extras' => 'date_upload%2Cviews%2Curl_q',
					'per_page' => $this->_limit,
					'format' => 'json',
					'nojsoncallback' => 1
			);
			$url = self::_getUrl(self::API_URI, $options);
			$result = $this->_curlResponse($url);
			return $this->_loop($result->photos->photo);
		}
		
		/**
		 * parcourir le resultat
		 * @param stdClass $searchResponse
		 * @return array
		 */
		protected function _loop($searchResponse){
			$posts = array();
			foreach ($searchResponse as $searchResult){
				$posts[] = $this->_parse($searchResult);
			}
			return $posts;
		}
		
		/**
		 * extaction de donnees
		 * @param stdClass $post
		 * @return array
		 */
		protected function _parse($post){
			return array(
					'type' => 'flickr',
					'date' => $post->dateupload,
					'message' => parent::_parseURL($post->title),
					'source' => $this->_getAttachement($post),
					'lien' => self::BASE_URI.$this->_userID.'/'.$post->id,
					'likes' => $post->views,
					'comments' => ''
			);
		}
		
		/**
		 * @see Uzik_Aggregator_Abstract::_getAttachement()
		 */
		protected function _getAttachement($post){
			return array(
								'small' 	=> $post->url_q,
								'medium' 	=> $post->url_q,
								'large' 	=> str_replace('_q.','.',$post->url_q)
							);
		}
	}
?>