<?php
namespace Aggregator\Component;
	abstract class AggregatorAbstract
	{
		
		/**
		 * Force les classes filles à définir ces méthodes
		 * @param array $posts
		 */
		abstract protected function _loop($posts);
		abstract protected function _parse($post);
		/**
		 * return 3 img (small, medium, large)
		 * @param array | stdclass $post
		 */
		abstract protected function _getAttachement($post);
		
		
		/**
		 * méthode commune set exception
		 * @param string $msg
		 * @throws Uzik_Aggregator_Exception
		 */
		public function setException($msg){
			throw new \RuntimeException($msg);
		}
		
		/**
		 * remplacer les url par des liens (balise <a>)
		 * @param string $text
		 * @return string
		 */
		protected function _parseURL($text){
			$text = preg_replace('!(http|ftp|scp)(s)?:\/\/[a-zA-Z0-9.?&_\-/=]+!', '<a href="\\0" target="_blank">\\0</a>',$text);
			return str_replace('." target="_blank"', '" target="_blank"', $text);
		}
		
		/**
		 * file get content
		 * @param string $url
		 * @param bolean $ssl
		 * @return stdClass
		 */
		protected function _fileGetContent($url,$ssl = false){
			if($ssl){
				if(!extension_loaded('openssl')){
					$this->setException('This class requires the php extension open_ssl to work.');
					$result = null;
				}
				else $result = json_decode(file_get_contents($url));
			}else $result = json_decode(file_get_contents($url));
			return $result;
		}
		
		/**
		 * requette curl
		 * @param string $url
		 * @return stdClass
		 */
		protected function _curlResponse($url){
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$result = json_decode(curl_exec($curl));
			curl_close($curl);
			return $result;
		}
		
		/**
		 * renvoie un url complet avec ces params
		 * @param string $baseUrl
		 * @param array $params
		 * @param string $concat
		 * @return string
		 */
		protected function _getUrl($baseUrl, $params, $concat = false){
			$var = $concat ? $concat.'?' : '?';
			foreach ($params as $key => $val){
				$var .= $key.'='.$val.'&';
			}
			$var = substr($var,0,-1);
			return $baseUrl.$var;
		}
	}
?>