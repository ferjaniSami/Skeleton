<?php

namespace Aggregator\Service;

//class to validate aggregator config
use \Aggregator\Component\Validate;

class Aggregator
{
	
	/**
	 * tableau des posts
	 * @var array
	 */
	protected static $_post	= array();
	
	/**
	 * Path du dossier cache
	 * @var string
	 */
	protected $_cache;
	
	/**
	 * Config
	 * @array
	 */
	protected $_config;
	
	public function __construct($config){
		$this->setConfig($config);
		$this->setCache(__DIR__ .'/../../../../../data/cache/aggregator');
		$validate = new Validate($this->getConfig());
		$validate->isValidConfig();
	}
	//getter
	public function getCache(){
		return $this->_cache;
	}
	public function getConfig(){
		return $this->_config;
	}
	//setter
	public function setCache($cache){
		$this->_cache = $cache;
	}
	public function setConfig($config){
		$this->_config = $config;
	}
	
	/**
	 * permettre de recuperer les posts(publications)
	 *
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 */
	public function getPosts($offset, $limit){
		return $this->_cache($this->_config['cache']['cachename'], $offset, $limit);
	}
	
	/**
	 * recuperer les reseaux sociaux actives
	 *
	 * @param stdClass $config
	 * @return array
	 */
	private static function _getActivatedModule($config){
		return $config['modules'];
	}
	
	/**
	 * si cache valide on recupere le resultat du cache sinon on met a jour le cache
	 * @param string $id
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 */
	private function _cache($id, $offset, $limit){
		$cacheId = 'cache'.$id;
		if(!is_dir($this->getCache())) mkdir($this->getCache(),0777,true);
		$cache   = \Zend\Cache\StorageFactory::factory(array(
				'adapter' => array(
						'name' => 'filesystem',
						'options' => array(
								'ttl' => $this->_config['cache']['lifetime'] * 3600,
								'cache_dir' => $this->getCache()
						),
				),
				'plugins' => array(
						// Don't throw exceptions on cache errors
						'exception_handler' => array(
								'throw_exceptions' => false
						),
				)
		));
		$result = $cache->getItem($cacheId, $success);
		if (!$success) {
			$modules = self::_getActivatedModule($this->_config);
			foreach ($modules as $module){
				self::_addPost($this->_getSocialPosts($module, $this->_config[$module], $id));
			}
			self::$_post = self::_sortPost(self::$_post);
			$cache->setItem($cacheId, serialize(self::$_post));
		}else{
			self::$_post = unserialize($result);
		}
		if(count(self::$_post) >= $offset){
			return array_slice(self::$_post, $offset, $limit);
		}else{
			return null;
		}
	}
	
	/**
	 * permettre de recuperer les posts d'un reseau social
	 *
	 * @param string $name
	 * @param stdClass $conf
	 * @param string $section
	 * @return array
	 */
	private function _getSocialPosts($name, $conf, $section){
		$class = '\\Aggregator\Component\\'.ucfirst($name);
		$$name = new $class($conf);
		return $$name->getPosts();
	}
	
	/**
	 * trier un tableau
	 *
	 * @param array $posts
	 * @return array
	 */
	private static function _sortPost($posts){
		if(!empty($posts)){
			$sortArray = array(); 
			foreach($posts as $post){
				foreach($post as $key=>$value){
					if(!isset($sortArray[$key])){
						$sortArray[$key] = array();
					}
					$sortArray[$key][] = $value;
				}
			} 
			$orderby = "date"; 
			array_multisort($sortArray[$orderby],SORT_DESC,$posts);
		}
		return $posts;
	}
	
	/**
	 * add post
	 *
	 * @param array $posts
	 * @return void
	 */
	private static function _addPost($posts){
		//bug php 5.4.3
		if(!empty($posts)){
			foreach ($posts as $post){
				self::$_post[] = $post;
			}
		}
	}
}

?>