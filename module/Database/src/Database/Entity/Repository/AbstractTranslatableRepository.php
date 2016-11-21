<?php

namespace Database\Entity\Repository;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;

use Gedmo\Translatable\TranslatableListener;

class AbstractTranslatableRepository extends BaseEntityRepository
{
	 /**
     * @var string Default locale
     */
    protected $defaultLocale;

    /**
     * Sets default locale
     *
     * @param string $locale
     */
    public function setDefaultLocale($locale)
    {
        $this->defaultLocale = $locale;
    }

    /**
     * Returns translated one (or null if not found) result for given locale
     *
     * @param QueryBuilder $qb            A Doctrine query builder instance
     * @param string       $locale        A locale name
     * @param string       $hydrationMode A Doctrine results hydration mode
     *
     * @return QueryBuilder
     */
    public function getOneOrNullResult(QueryBuilder $qb, $locale = null, $default = null, $empty = null, $hydrationMode = null)
    {
        return $this->getTranslatedQuery($qb, $locale, $default, $empty)->getOneOrNullResult($hydrationMode);
    }

    /**
     * Returns translated results for given locale
     *
     * @param QueryBuilder $qb            A Doctrine query builder instance
     * @param string       $locale        A locale name
     * @param string       $hydrationMode A Doctrine results hydration mode
     *
     * @return QueryBuilder
     */
    public function getResult(QueryBuilder $qb, $locale = null, $default = null, $empty = null, $hydrationMode = AbstractQuery::HYDRATE_OBJECT)
    {
        return $this->getTranslatedQuery($qb, $locale, $default, $empty)->getResult($hydrationMode);
    }

    /**
     * Returns translated array results for given locale
     *
     * @param QueryBuilder $qb     A Doctrine query builder instance
     * @param string       $locale A locale name
     *
     * @return QueryBuilder
     */
    public function getArrayResult(QueryBuilder $qb, $locale = null, $default = null, $empty = null)
    {
        return $this->getTranslatedQuery($qb, $locale, $default, $empty)->getArrayResult();
    }

    /**
     * Returns translated single result for given locale
     *
     * @param QueryBuilder $qb            A Doctrine query builder instance
     * @param string       $locale        A locale name
     * @param string       $hydrationMode A Doctrine results hydration mode
     *
     * @return QueryBuilder
     */
    public function getSingleResult(QueryBuilder $qb, $locale = null, $default = null, $empty = null, $hydrationMode = null)
    {
        return $this->getTranslatedQuery($qb, $locale, $default, $empty)->getSingleResult($hydrationMode);
    }

    /**
     * Returns translated scalar result for given locale
     *
     * @param QueryBuilder $qb     A Doctrine query builder instance
     * @param string       $locale A locale name
     *
     * @return QueryBuilder
     */
    public function getScalarResult(QueryBuilder $qb, $locale = null, $default = null, $empty = null)
    {
        return $this->getTranslatedQuery($qb, $locale, $default, $empty)->getScalarResult();
    }

    /**
     * Returns translated single scalar result for given locale
     *
     * @param QueryBuilder $qb     A Doctrine query builder instance
     * @param string       $locale A locale name
     *
     * @return QueryBuilder
     */
    public function getSingleScalarResult(QueryBuilder $qb, $locale = null, $default = null, $empty = null)
    {
        return $this->getTranslatedQuery($qb, $locale, $default, $empty)->getSingleScalarResult();
    }

    /**
     * Returns translated Doctrine query instance
     *
     * @param QueryBuilder $qb     A Doctrine query builder instance
     * @param string       $locale A locale name
     *
     * @return Query
     */
    protected function getTranslatedQuery(QueryBuilder $qb, $locale = null, $default = null, $empty = null)
    {
        $locale = null === $locale ? $this->defaultLocale : $locale;

        $query = $qb->getQuery();

        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);
        
        if($empty === null || $empty === false){
            // $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_INNER_JOIN, true);
        }

        if($default === true){
            $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, true);
            $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_INNER_JOIN, false);
        }

        // echo $query->getSql(); exit;

        return $query;
    }
}