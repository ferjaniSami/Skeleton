<?php

namespace Admin\Entity\Repository;

use Database\Entity\Repository\EntityRepository;

class UserRepository extends EntityRepository
{

	public function getTerritoriesAndLocales(\Admin\Entity\User $user)
	{	
		$user_acl_locales = $user_acl_langs = $user_acl_territories = array();

		if($user->getSuperAdmin()){
			$territories = $this->getEntityManager()->getRepository('Admin\Entity\Territory')->findAll();
	        foreach($territories as $territory){
				$_langs         = \Zend\Json\Json::decode($territory->getLangs());
				$user_acl_langs = array_unique(array_merge($user_acl_langs, $_langs));

				foreach($_langs as $_lang){
					$user_acl_locales[] = $territory->getUrlCode() . '-' . $_lang;
				}
				$user_acl_territories[] = $territory->getUrlCode();
	        }
		}else{
			$user_roles_ids = $user->getRoles() === null ? array() : \Zend\Json\Json::decode($user->getRoles()) ;

		    foreach($user_roles_ids as $id){
		        if(null !== ($_role = $this->getEntityManager()->getRepository('Admin\Entity\Role')->findOneBy(array('id' => $id, 'status' => 1)))){
		            $user_acl_locales = array_unique(array_merge($user_acl_locales, \Zend\Json\Json::decode($_role->getTerritories())));
		            $user_acl_langs   = array_unique(array_merge($user_acl_langs, \Zend\Json\Json::decode($_role->getLangs())));
		        }
		    }

		    foreach($user_acl_locales as $_locale){
		    	@list($_territory, $_lang) = explode('-', $_locale);
		    	if(isset($_territory) && !empty($_territory)){
		    		$user_acl_territories[] = $_territory;
		    	}
		    }
    	}

        return array(
        	'territories' => array_unique($user_acl_territories),
        	'locales'     => array_unique(array_merge($user_acl_langs, $user_acl_locales))
        );
	}
}