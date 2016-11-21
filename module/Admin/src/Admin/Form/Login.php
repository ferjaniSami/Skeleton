<?php

namespace Admin\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("admin-form-login")
 * @Annotation\Attributes({"class":"m-t"})
 */
class Login
{
    public function __construct()
    {
        _('Login');
        _('Password');
        _('Remember Me ?');
        _('Submit');
    }

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Login"})
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Login"})
     */
    public $login;

    /**
     * @Annotation\Type("Zend\Form\Element\Password")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Password"})
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Password"})
     */
    public $password;

    /**
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({"label":"Remember Me ?"})
     */
    public $rememberme;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit", "class":"btn btn-white block full-width m-b"})
     */
    public $submit;
}
