<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Database\Entity\Entity;

/**
 * Role
 *
 * @ORM\Table(name="roles")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Role extends Entity
{
    /** @ORM\Column(name="label", type="string", length=20, nullable=false) */
    protected $label;

    /** @ORM\Column(name="description", type="string", length=100, nullable=true) */
    protected $description;

    /** @ORM\Column(name="name", type="string", length=20, nullable=false) */
    protected $name;

    /** @ORM\Column(name="acl", type="text", nullable=false) */
    protected $acl;

    /** @ORM\Column(name="territories", type="text", nullable=false) */
    protected $territories;

    /** @ORM\Column(name="langs", type="text", nullable=false) */
    protected $langs;

    /**
     * Set label
     *
     * @param string $label
     * @return Role
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Role
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Role
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
     * Set acl
     *
     * @param string $acl
     * @return Role
     */
    public function setAcl($acl)
    {
        $this->acl = $acl;

        return $this;
    }

    /**
     * Get acl
     *
     * @return string 
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * Set territories
     *
     * @param string $territories
     * @return Role
     */
    public function setTerritories($territories)
    {
        $this->territories = $territories;

        return $this;
    }

    /**
     * Get territories
     *
     * @return string 
     */
    public function getTerritories()
    {
        return $this->territories;
    }

    /**
     * Set langs
     *
     * @param string $langs
     * @return Role
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
}
