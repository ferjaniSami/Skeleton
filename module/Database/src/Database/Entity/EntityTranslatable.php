<?php

namespace Database\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\MappedSuperclass
 */
abstract class EntityTranslatable
{
    /**
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    * @ORM\Column(name="id", type="bigint", nullable=false)
    */
    private $id;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener's locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    protected $locale;

    /** @ORM\Column(name="locked", type="boolean", nullable=true, options={"default":"0"}) */
    private $locked;

    /** @ORM\Column(name="author", type="bigint", nullable=true) */
    private $author;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(name="author_translation", type="bigint", nullable=true)
     */
    private $author_translation;

    /** @ORM\Column(name="status", type="boolean", nullable=false) */
    private $status;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(name="status_translation", type="boolean", nullable=false)
     */
    private $status_translation;

    /**
    * @Gedmo\Timestampable(on="create")
    * @ORM\Column(name="created_at", type="datetime", nullable=false)
    */
    private $created_at;

    /**
    * @Gedmo\Timestampable(on="update")
    * @ORM\Column(name="modified_at", type="datetime", nullable=false)
    */
    private $modified_at;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return EntityTranslatable
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set author
     *
     * @param integer $author
     * @return EntityTranslatable
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return integer
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set author translation
     *
     * @param integer $author_translation
     * @return EntityTranslatable
     */
    public function setAuthorTranslation($authorTranslation)
    {
        $this->author_translation = $authorTranslation;

        return $this;
    }

    /**
     * Get author translation
     *
     * @return integer
     */
    public function getAuthorTranslation()
    {
        return $this->author_translation;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return EntityTranslatable
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status translation
     *
     * @param boolean $status
     * @return EntityTranslatable
     */
    public function setStatusTranslation($statusTranslation)
    {
        $this->status_translation = $statusTranslation;

        return $this;
    }

    /**
     * Get status translation
     *
     * @return boolean
     */
    public function getStatusTranslation()
    {
        return $this->status_translation;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return EntityTranslatable
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set modified_at
     *
     * @param \DateTime $modifiedAt
     * @return EntityTranslatable
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modified_at = $modifiedAt;

        return $this;
    }

    /**
     * Get modified_at
     *
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modified_at;
    }

    /**
     * Set translatable locale
     *
     * @param string $locale
     * @return EntityTranslatable
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
