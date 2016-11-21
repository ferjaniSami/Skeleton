<?php

namespace Database\Service;

use Doctrine\Common\Util\Inflector;

class EntityHydrator
{
    public function hydrate(&$entity, array $data)
    {
        foreach($data as $property => $value){
            $setter = sprintf('set%s', ucfirst(Inflector::camelize($property)));
            if(method_exists($entity, $setter)){
                $entity->$setter($value);
            }
        }
    }
}
