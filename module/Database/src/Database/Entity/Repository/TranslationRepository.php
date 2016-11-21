<?php

namespace Database\Entity\Repository;

use Doctrine\ORM\Query;

use Gedmo\Translatable\Entity\Repository\TranslationRepository as GedmoTranslationRepository;

class TranslationRepository extends GedmoTranslationRepository
{

    // findTranslations($entity)
    // findObjectByTranslatedField($field, $value, $class);
    // findTranslationsByObjectId($id);

	public function findEntitiesByLocales(array $locales)
    {
        $entities = array();
        $translationMeta = $this->getClassMetadata();

        $qb = $this->_em->createQueryBuilder();
        $qb->select('trans.foreignKey, trans.objectClass')
        	->from($translationMeta->rootEntityName, 'trans')
        	->where('trans.locale IN (:locales)')
        	->groupBy('trans.foreignKey')
        	->orderBy('trans.foreignKey');

        $data = $qb->getQuery()->execute(
            array('locales' => $locales),
            Query::HYDRATE_ARRAY
        );

        if($data && is_array($data) && count($data)){
            foreach($data as $row){
                if(null !== ($entity = $this->_em->find($row['objectClass'], $row['foreignKey']))){
                	$entities[] = $entity;
                }
            }
        }

        return $entities;
    }

    public function findTranslationsByObjectIdAndLocales($id, array $locales)
    {
        $result = array();
        if($id){
            $translationMeta = $this->getClassMetadata();

            $qb = $this->_em->createQueryBuilder();
            $qb->select('trans.content, trans.field, trans.locale')
                ->from($translationMeta->rootEntityName, 'trans')
                ->where('trans.foreignKey = :entityId')
                ->andWhere('trans.locale IN (:locales)')
                ->orderBy('trans.locale');

            $q = $qb->getQuery();
            $data = $q->execute(
                array(
                    'entityId' => $id,
                    'locales'  => $locales
                ),
                Query::HYDRATE_ARRAY
            );

            if($data && is_array($data) && count($data)){
                foreach($data as $row){
                    $result[$row['locale']][$row['field']] = $row['content'];
                }
            }
        }
        return $result;
    }

    public function getTranslatableFieldsByClass($className)
    {
        $translationMeta = $this->getClassMetadata();

        $qb = $this->_em->createQueryBuilder();
        $qb->select('trans.field')
            ->from($translationMeta->rootEntityName, 'trans')
            ->where('trans.objectClass = :entityClass')
            ->groupBy('trans.field');

        $q = $qb->getQuery();

        $data = $q->execute(
            array('entityClass' => $className),
            Query::HYDRATE_ARRAY
        );

        $result = array();
        if($data && is_array($data) && count($data)){
            foreach($data as $row){
                $result[] = $row['field'];
            }
        }

        return $result;
    }
}