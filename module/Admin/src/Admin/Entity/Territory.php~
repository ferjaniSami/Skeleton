<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Database\Entity\Entity;

/**
 * Territory
 *
 * @ORM\Table(name="territories")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Admin\Entity\Repository\TerritoryRepository")
 */
class Territory extends Entity
{
    /** @ORM\Column(name="name", type="string", length=50, nullable=false) */
    protected $name;

    /** @ORM\Column(name="url_code", type="string", length=10, nullable=false) */
    protected $url_code;

    /** @ORM\Column(name="langs", type="text", nullable=false) */
    protected $langs;

    /** @ORM\Column(name="default_lang", type="string", length=3, nullable=false) */
    protected $default_lang;

    /**
     * Set name
     *
     * @param string $name
     * @return Territory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set url_code
     *
     * @param string $urlCode
     * @return Territory
     */
    public function setUrlCode($urlCode)
    {
        $this->url_code = $urlCode;

        return $this;
    }

    /**
     * Get url_code
     *
     * @return string 
     */
    public function getUrlCode()
    {
        return $this->url_code;
    }

    /**
     * Set langs
     *
     * @param string $langs
     * @return Territory
     */
    public function setLangs($langs)
    {
        $this->langs = $langs;

        return $this;
    }

    /**
     * Get langs
     *
     * @return string 
     */
    public function getLangs()
    {
        return $this->langs;
    }

    /**
     * Set default_lang
     *
     * @param string $defaultLang
     * @return Territory
     */
    public function setDefaultLang($defaultLang)
    {
        $this->default_lang = $defaultLang;

        return $this;
    }

    /**
     * Get default_lang
     *
     * @return string 
     */
    public function getDefaultLang()
    {
        return $this->default_lang;
    }
}
