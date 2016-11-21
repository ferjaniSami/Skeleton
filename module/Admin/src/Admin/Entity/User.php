<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Database\Entity\Entity;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Admin\Entity\Repository\UserRepository")
 */
class User extends Entity
{
    /** @ORM\Column(name="mail", type="string", length=100, nullable=false) */
    protected $mail;

    /** @ORM\Column(name="login", type="string", length=50, nullable=false) */
    protected $login;

    /** @ORM\Column(name="password", type="string", length=100, nullable=false) */
    protected $password;

    /** @ORM\Column(name="name", type="string", length=100, nullable=false) */
    protected $name;

    /** @ORM\Column(name="super_admin", type="boolean", nullable=true, options={"default":"0"}) */
    protected $super_admin;

    /** @ORM\Column(name="roles", type="text", nullable=true) */
    protected $roles;

    /**
     * Set mail
     *
     * @param string $mail
     * @return User
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string 
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set login
     *
     * @param string $login
     * @return User
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string 
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = sha1($password);

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
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
     * Set super_admin
     *
     * @param boolean $superAdmin
     * @return User
     */
    public function setSuperAdmin($superAdmin)
    {
        $this->super_admin = $superAdmin;

        return $this;
    }

    /**
     * Get super_admin
     *
     * @return boolean 
     */
    public function getSuperAdmin()
    {
        return $this->super_admin;
    }

    /**
     * Set roles
     *
     * @param string $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return string 
     */
    public function getRoles()
    {
        return $this->roles;
    }
}
