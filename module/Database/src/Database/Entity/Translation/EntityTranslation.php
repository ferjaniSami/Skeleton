<?php

namespace Database\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\MappedSuperclass
 */
abstract class EntityTranslation
{
    /**
     * @var integer $id
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string $locale
     *
     * @ORM\Column(type="string", length=8)
     */
    private $locale;

    /**
     * @var string $objectClass
     *
     * @ORM\Column(name="object_class", type="string", length=255)
     */
    private $objectClass;

    /**
     * @var string $field
     *
     * @ORM\Column(type="string", length=32)
     */
    private $field;

    /**
     * @var string $foreignKey
     *
     * @ORM\Column(name="foreign_key", type="string", length=64)
     */
    private $foreignKey;

    /**
     * @var string $content
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return AbstractTranslation
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Get locale
     *
     * @return string $locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set field
     *
     * @param string $field
     * @return AbstractTranslation
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * Get field
     *
     * @return string $field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set object class
     *
     * @param string $objectClass
     * @return AbstractTranslation
     */
    public function setObjectClass($objectClass)
    {
        $this->objectClass = $objectClass;
        return $this;
    }

    /**
     * Get objectClass
     *
     * @return string $objectClass
     */
    public function getObjectClass()
    {
        return $this->objectClass;
    }

    /**
     * Set foreignKey
     *
     * @param string $foreignKey
     * @return AbstractTranslation
     */
    public function setForeignKey($foreignKey)
    {
        $this->foreignKey = $foreignKey;
        return $this;
    }

    /**
     * Get foreignKey
     *
     * @return string $foreignKey
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return AbstractTranslation
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get content
     *
     * @return string $content
     */
    public function getContent()
    {
        return $this->content;
    }
}