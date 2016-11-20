<?php
namespace Aggregator\Component;
class Instagram extends \Aggregator\Component\AggregatorAbstract
	{
		/**
		 * ID un ulilisateur instagram
		 * @var string
		 */
		private $_userID;
		
		/**
		 * ID de l'application instagram(comme facebook)
		 * @var string
		 */
		private $_appID;
		
		/**
		 * API access token
		 * @var string
		 */
		private $_token;
		
		/**
		 * nombre limite des posts
		 * @var number
		 */
		private $_limit;
		
		/**
		 * SEARCH URL(for user)
		 * @var const
		 */
		const SEARCH_URI = 'https://api.instagram.com/v1/users/search';
		
		/**
		 * USER URL
		 * @var const
		 */
		const USER_URI = 'https://api.instagram.com/v1/users/';
		/**
		 * constructeur
		 * @param Zend_config_ini $conf
		 */
		public function __construct($conf)
		{
			$this->_userID	 		= $conf['userID'];
			$this->_appID			= $conf['appID'];
			$this->_token 			= $conf['token'];
			$this->_limit 			= $conf['limit'];
		}
		
		/**
		 * la valeur de retour de cette fonction est n post(s) instagram
		 * @return array
		 */
		public function getPosts(){
			return $this->_getUserMedia();
		}
		
	/**
	 * get userID from username
	 * @return int
	 */
	private function _getUserIDFromUserName(){
        if(strlen($this->_userID)>0 && strlen($this->_token)>0){
            //Search for the username
            $options = array(
            		'q' => $this->_userID,
            		'access_token' => $this->_token
            );
            $useridquery = parent::_fileGetContent(parent::_getUrl(self::SEARCH_URI, $options),true);
            if(!empty($useridquery) && $useridquery->meta->code=='200' && $useridquery->data[0]->id>0){
                //Found
                $this->_userID = $useridquery->data[0]->id;
            } else {
                //Not found
                $this->setException('getUserIDFromUserName');
            }
        } else {
            $this->setException('empty username or access token');
        }
    }
    
    /**
     * permet de recuperer les posts d'un utilisateur à partir de son id
     * @return stdClass
     */
    private function _getUserMedia(){
    	$this->_getUserIDFromUserName();
        if($this->_userID > 0 && strlen($this->_token)>0){
        	$options = array(
        		'access_token' => $this->_token
        	);
            $shots = parent::_fileGetContent(parent::_getUrl(self::USER_URI, $options, $this->_userID.'/media/recent'), true);
            if($shots->meta->code=='200'){
                return $this->_loop($shots);
            } else {
                $this->setException('getUserMedia');
                return null;
            }
        } else {
            $this->setException('empty username or access token');
            return null;
        }
    }
    
    /**
	 * parcourir le resultat
	 * @param stdClass $shots
	 * @return array
	 */
    protected function _loop($shots){
    	$posts = array();
        if(!empty($shots->data)){
        	foreach($shots->data as $istg){
        		$posts[] = $this->_parse($istg);   
            }
        } else {
            $this->setException('simpleDisplay');
        }
        return $posts;
    }
    
    /**
     * extraction de données
     * @param stdClass $post
     * @return array
     */
    protected function _parse($post){
    	return array(
    			'type' => 'instagram',
    			'date' => $post->created_time,
    			'message' => $post->caption->text,
    			'source' => $this->_getAttachement($post->images),
    			'lien' => $post->link,
    			'likes' => $post->likes->count,
    			'comments' => $post->comments->count
    	);
    }
    
    /**
     * @see Uzik_Aggregator_Abstract::_getAttachement()
     */
    protected function _getAttachement($post){
    	return array(
    					'small' 	=> $post->low_resolution->url,
    					'medium' 	=> $post->thumbnail->url,
    					'large' 	=> $post->standard_resolution->url
    					);
    }
}
?>