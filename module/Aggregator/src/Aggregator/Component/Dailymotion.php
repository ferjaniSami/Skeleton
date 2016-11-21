<?php
namespace Aggregator\Component;

class Dailymotion extends \Aggregator\Component\AggregatorAbstract
	{
		/**
		 * username (ident)
		 * @var string
		 */
		private $_userID;
		
		/**
		 * Nombre limite des resultats
		 * @var number
		 */
		private $_limit;
		
		/**
		 * objet dailymotion
		 * @var object
		 */
		private $_dailymotion;
		
		/**
		 * constructeur
		 * @param Zend_config_ini $conf
		 */
		public function __construct($conf)
		{
			$this->_userID 		= $conf['userID'];
			$this->_limit 		= $conf['limit'];
		}
		
		/**
		 * get Singleton instance de la classe dailymotion
		 * @return boolean
		 */
		private function getIstance(){
			if(!is_object($this->_dailymotion)){
				require_once(__DIR__ .'/../lib/Dailymotion/dailymotion.php');
				$this->_dailymotion = new \Dailymotion();
			}
			return true;
		}
		
		/**
		 * la valeur de retour de cette fonction est n videos dailymotion
		 * @return array
		 */
		public function getPosts(){
			$this->getIstance();
			$fields = array('id', 'created_time', 'title','description','thumbnail_120_url','thumbnail_240_url','thumbnail_480_url', 'rating','views_total','url');
			$result = $this->_dailymotion->get('/user/'.$this->_userID.'/videos',array('limit' => $this->_limit, 'fields' => $fields));
			return $this->_loop($result);
		}
		
		/**
		 * Parcourir le resultat
		 * @see Uzik_Aggregator_Abstract::_loop()
		 * @param array $searchResponse
		 * @return array
		 */
		protected function _loop($searchResponse){
			$posts = array();
			foreach ($searchResponse['list'] as $searchResult) {
				$posts[] = $this->_parse($searchResult);
			}
			return $posts;
		}
		
		/**
		 * extraction de donnees
		 * @see Uzik_Aggregator_Abstract::_parse()
		 * @param array $post
		 * @return array
		 */
		protected function _parse($post){
			return array(
					'type' => 'dailymotion',
					'date' => $post['created_time'],
					'message' => parent::_parseURL($post['title'].' '.$post['description']),
					'source' => $this->_getAttachement($post),
					'lien' => $post['url'],
					'likes' => $post['rating'],
					'comments' => $post['views_total']
			);
		}
		
		/**
		 * @see Uzik_Aggregator_Abstract::_getAttachement()
		 */
		protected function _getAttachement($post){
			return array(
							'small' => $post['thumbnail_120_url'],
							'medium' => $post['thumbnail_240_url'],
							'large' => $post['thumbnail_480_url']
							);
		}
	}
?>