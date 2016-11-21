<?php

namespace Admin\Entity\Repository;

use Database\Entity\Repository\EntityRepository;

class TerritoryRepository extends EntityRepository
{
	public function getLocalesByTerritory(\Admin\Entity\Territory $territory, $withNames = false)
	{
		$_locales = array();
        $_territoryLangs = \Zend\Json\Json::decode($territory->getLangs());
        foreach($_territoryLangs as $_territoryLang){
            $_territoryLocale = $territory->getUrlCode() . '-' . $_territoryLang;
            $_locale = $_territoryLang;

            if($withNames){
            	$_locales[$_territoryLocale] = \Locale::getDisplayLanguage($_territoryLang) . ' (' . $territory->getName() . ')';
            	$_locales[$_locale]			 = \Locale::getDisplayLanguage($_territoryLang);
            }else{
            	$_locales[] = $_territoryLocale;
            	$_locales[] = $_locale;
            }
        }

        return $_locales;
	}
}