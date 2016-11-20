<?php
namespace Aggregator\Component;
	
class Youtube extends \Aggregator\Component\AggregatorAbstract
	{
		/**
		 * ID de la chaine youtube
		 * @var string
		 */
		private $_chaineID;
		
		/**
		 * developper Key
		 * @var string
		 */
		private $_devKEY;
		
		/**
		 * nombre limite des vidéos
		 * @var number
		 */
		private $_limit;
		
		/**
		 * objet de l'api youtube
		 * @var object
		 */
		private $_youtube;
		
		/**
		 * Base url d'une video youtube
		 * @var const
		 */
		const BASE_URI = 'http://www.youtube.com/watch';
		
		/**
		 * constructeur
		 * @param config
		 */
		public function __construct($conf)
		{
			$this->_chaineID 	= $conf['chaineID'];
			$this->_devKEY 		= $conf['devKEY'];
			$this->_limit 		= $conf['limit'];
		}
		
		/**
		 * get instance de l'objet youtube
		 * @return object
		 */
		private function _getInstanceYT(){
			if(!is_object($this->_youtube)){
				require_once(__DIR__ .'/../lib/Google/src/Google_Client.php');
				require_once(__DIR__ .'/../lib/Google/src/contrib/Google_YouTubeService.php');
				$client = new \Google_Client();
				$client->setDeveloperKey($this->_devKEY);
				$youtube = new \Google_YoutubeService($client);
			}else $youtube = $this->_youtube;
			return $youtube;
		}
		
		/**
		 * la valeur de retour de cette fonction est n videos
		 * on instance l'objet youtube et on appelle la fonction getlist
		 * @return array
		 */
		public function getPosts(){
			$this->_youtube = $this->_getInstanceYT();
			return $this->_getList();
		}
		
		
		/**
		 * permet de recuperer la liste des videos relatives a une chaine youtube
		 * @return array
		 */
		private function _getList(){
			try {
				$searchResponse = $this->_youtube->search->listSearch('snippet', array(
						'channelId' => $this->_chaineID,
				  		'maxResults' => $this->_limit
						//'q' => 'escarpins'
				));
				$videos = $this->_loop($searchResponse);
			} catch (Google_ServiceException $e) {
				$this->setException($e->getMessage());
			} catch (Google_Exception $e) {
				$this->setException($e->getMessage());
			}
			return $videos;
		}
		
		
		/**
		 * parcourir le resultat
		 * @see Uzik_Aggregator_Abstract::_loop()
		 * @param array $searchResponse
		 * @return array
		 */
		protected function _loop($searchResponse){
			$videos = array();
			foreach ($searchResponse['items'] as $searchResult) {
				if($searchResult['id']['kind'] == 'youtube#video'){
					$videos[] = $this->_parse($searchResult['id']['videoId']);
				}
			}
			return $videos;
		}
		
		/**
		 * extraction de données relative a une video
		 * @param string $id
		 * @return array
		 */
		protected function _parse($id){
			$info = $this->_youtube->videos->listVideos($id,'snippet,statistics');
			$video = $info['items'][0]['snippet'];
			$stat = $info['items'][0]['statistics'];
			$options = array(
					'v' => $info['items'][0]['id']
			);
			return array(
					'type' => 'youtube',
					'date' => strtotime($video['publishedAt']),
					'message' => $this->_parseURL($video['description']),
					'source' => $this->_getAttachement($video),
					'lien' => parent::_getUrl(self::BASE_URI, $options),
					'likes' => $stat['viewCount'],
					'comments' => $stat['commentCount']
			);
		}
		
		/**
		 * @see Uzik_Aggregator_Abstract::_getAttachement()
		 */
		protected function _getAttachement($video){
			return array(
							'small' => $video['thumbnails']['default']['url'],
							'medium' => $video['thumbnails']['medium']['url'],
							'large' => $video['thumbnails']['high']['url']
							);
		}
	}
?>