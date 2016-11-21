<?php
namespace Aggregator\Component;

class Validate
{
	protected $_config;
	private static $_requiredParamFacebook = array('pageID', 'appID', 'appSecret', 'limit');
	private static $_requiredParamTwitter = array('userName', 'userID', 'trimUserName', 'consumerKey', 'consumerSecret', 'accessToken', 'accessTokenSecret', 'limit');
	private static $_requiredParamInstagram = array('userID', 'appID', 'token', 'limit');
	private static $_requiredParamGoogle = array('userID', 'devKEY', 'limit');
	private static $_requiredParamYoutube = array('chaineID', 'devKEY', 'limit');
	private static $_requiredParamDailymotion = array('userID', 'limit');
	private static $_requiredParamFlickr = array('userID', 'apiKey', 'limit');
	private static $_requiredParamPinterest = array('userName', 'activite', 'limit');
	private static $_requiredParamVimeo = array('userID', 'limit');
	public function __construct($config){
		$this->setConfig($config);
	}
	
	public function setConfig($config){
		$this->_config = $config;
	}
	
	public function getConfig(){
		return $this->_config;
	}
	
	public function isValidConfig(){
		$validate = false;
		if(!empty($this->_config['cache']['lifetime']) && !empty($this->_config['cache']['cachename'])){
			$validate = true;
		}else{
			$validate = false;
			throw new \RuntimeException('Le param lifetime ou cache n\'existe pas dans la configuration.');
		}
		if(!empty($this->_config['modules'])){
			foreach ($this->_config['modules'] as $module){
				if(!empty($this->_config[$module])){
					if($this->$module($this->_config[$module])){
						$validate = true;
					}else{
						$validate = false;
						break;
					}
				}else{
					$validate = false;
					throw new \RuntimeException('Module non reconnue.');
					break;
				}
			}
		}else{
			$validate = false;
		}
		return $validate;
	}
	
	public function __call($name, $arguments){
		$function = '_requiredParam'.ucfirst($name);
		$valid = false;
		if(isset(self::$$function) && is_array(self::$$function)){
			foreach (self::$$function as $val){
				if(!empty($arguments[0][$val]) || array_key_exists($val, $arguments[0])){
					$valid = true;
				}else{
					$valid = false;
					throw new \RuntimeException('Le param '.$val.' n\'existe pas dans la configuration de '.$name.'.');
					break;
				}
			}
		}else{
			throw new \RuntimeException('Module non reconnue ou required params non déclaré dans le fichier validate.php.');
		}
		return $valid;
	}
}