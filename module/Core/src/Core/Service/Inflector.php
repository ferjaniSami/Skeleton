<?php

namespace Core\Service;

class Inflector
{
	public static function filter($rules, $str)
	{
		if(!is_array($rules)){
			$rules = (array) $rules;
		}

		$inflector = new \Zend\Filter\Inflector(':str');
        $inflector->setRules(array(
            ':str'  => $rules
        ));
        
        return $inflector->filter(array('str' => $str));
	}
}