<?php
namespace Aggregator\Component;

class Vimeo extends \Aggregator\Component\AggregatorAbstract
	{
		/**
		 * username (ident)
		 * @var string
		 */
		private $_userID;
		
		/**
		 * nombre limite des posts
		 * @var number
		 */
		private $_limit;
		
		/**
		 * URL de l'api vimeo
		 * @var string
		 */
		const API_URI = 'http://vimeo.com/api/v2/';
		
		/**
		 * Constructeur
		 * @param Zend_Config_Ini $conf
		 */
		public function __construct($conf)
		{
			$this->_userID 		= $conf['userID'];
			$this->_limit 		= $conf['limit'];
		}
		
		/**
		 * la valeur de retour de cette fonction est n posts
		 * @return array
		 */
		public function getPosts(){
			$url = parent::_getUrl(self::API_URI, array(),$this->_userID.'/videos.json');
			$result = $this->_curlResponse($url);
			return $this->_loop($result);
		}
		
		/**
		 * parcourir le resultat
		 * @see Uzik_Aggregator_Abstract::_loop()
		 * @param StdClass
		 * @return array
		 */
		protected function _loop($searchResponse){
			$posts = array();
			$i = 0;
			foreach ($searchResponse as $searchResult){
				if($i < $this->_limit)
				$posts[] = $this->_parse($searchResult);
				else break;
				$i++;
			}
			return $posts;
		}
		
		/**
		 * Extraction de donnees
		 * @see Uzik_Aggregator_Abstract::_parse()
		 * @param StdClass
		 * @return array
		 */
		protected function _parse($post){
			return array(
					'type' => 'vimeo',
					'date' => strtotime($post->upload_date),
					'message' => parent::_parseURL($post->title.' '.$post->description),
					'source' => $this->_getAttachement($post),
					'lien' => $post->url,
					'likes' => $post->stats_number_of_plays,
					'comments' => $post->stats_number_of_comments
			);
		}
		
		/**
		 * @see Uzik_Aggregator_Abstract::_getAttachement()
		 */
		protected function _getAttachement($post){
			return array(
							'small' 	=> $post->thumbnail_small,
							'medium' 	=> $post->thumbnail_medium,
							'large' 	=> $post->thumbnail_large
					);
		}
	}
?>