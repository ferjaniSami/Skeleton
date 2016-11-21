<?php
namespace Aggregator\Component;

class Pinterest extends \Aggregator\Component\AggregatorAbstract
	{
		/**
		 * username du compte pinterest
		 * @var string
		 */
		private $_userName;
		
		/**
		 * nombre limite des posts
		 * @var number
		 */
		private $_limit;
		/**
		 * URL de recuperation de flux RSS
		 * @var string
		 */
		const API_URI = 'http://pinterest.com/';
		/**
		 * URL d'un PIN
		 * @var string
		 */
		const BASE_URI = 'http://pinterest.com/pin/';
		
		/**
		 * constructeur
		 * @param Zend_config_ini $conf
		 */
		public function __construct($conf)
		{
			$this->_userName 	= $conf['userName'];
			$this->_limit		= $conf['limit'];
		}
		
		/**
		 * la valeur de retour de cette fonction est n post(s) pinterest
		 * @return array
		 */
		public function getPosts(){
			$url = parent::_getUrl(self::API_URI, array(), $this->_userName.'/feed.rss');
			// Fetch the latest Slashdot headlines
			try {
				$feed = \Zend\Feed\Reader\Reader::import($url);
			} catch (Zend\Feed\Reader\Exception\RuntimeException $e) {
				// feed import failed
				echo "Exception caught importing feed: {$e->getMessage()}\n";
				exit;
			}
		    $data = array();
	 		$i = 0;
			foreach ($feed as $entry) {
				if($i == $this->_limit) break;
			    $data[] = array(
			        'title'        => $entry->getTitle(),
			        'description'  => $entry->getDescription(),
			        'dateModified' => $entry->getDateModified(),
			        'authors'       => $entry->getAuthors(),
			        'link'         => $entry->getLink(),
			        'content'      => $entry->getContent()
			    );
			    $i++;
			}
			return $this->_loop($data);
		}
		
		/**
		 * Parcourir le resultat
		 * @see Uzik_Aggregator_Abstract::_loop()
		 * @param array
		 * @return array
		 */
		protected function _loop($result){
			$posts = array();
			foreach ($result as $row){
				$posts[] = $this->_parse($row);
			}
			return $posts;
		}
		/**
		 * extraction de donnees
		 * @see Uzik_Aggregator_Abstract::_parse()
		 * @param array
		 * @return array
		 */
		protected function _parse($entry){
			$dom = new \DOMDocument();
			@$dom->loadHTML($entry['description']);
			$src = $dom->getElementsByTagName('img')->item(0)->getAttribute('src');
			$legend = utf8_decode($dom->getElementsByTagName('p')->item(1)->textContent);
			return array(
					'type' => 'pinterest',
					'date' => $entry['dateModified']->getTimestamp(),
					'message' => $legend,
					'source' => $this->_getAttachement($src),
					'lien' => $entry['link'],
					'likes' => 0,
					'comments' => 0
			);
		}
		
		/**
		 * @see Uzik_Aggregator_Abstract::_getAttachement()
		 */
		protected function _getAttachement($src){
			return array(
							'small' => str_replace('/192x/', '/70x/', $src),
							'medium' => $src,
							'large' => str_replace('/192x/', '/550x/', $src)
					);
		}
		
		/**
		 * changer href de l'url
		 * @param string $text
		 * @return string
		 */
		private function _verifURL($text){
			return str_replace('href="/pin/', 'target="_blanc" href="'.self::BASE_URI, str_replace('href=\'/pin/', 'target=\'_blanc\' href=\''.self::BASE_URI, $text));
		}
	}
?>