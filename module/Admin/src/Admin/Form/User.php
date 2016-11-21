<?php

namespace Admin\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("admin-form-user")
 */
class User
{
    public function __construct()
    {
        _('Name');
        _('Mail');
        _('Login');
        _('Password');
        _('Password confirmation');
        _('Submit');
    }

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"100"}})
     * @Annotation\Options({"label":"Name"})
     * @Annotation\Attributes({"id":"name"})
     */
    public $name;

    /**
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"100"}})
     * @Annotation\Validator({"name":"EmailAddress"})
     * @Annotation\Options({"label":"Mail"})
     * @Annotation\Attributes({"id":"mail"})
     */
    public $mail;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Filter({"name":"StringToLower"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"4", "max":"50"}})
     * @Annotation\Options({"label":"Login"})
     * @Annotation\Attributes({"id":"login"})
     */
    public $login;

    /**
     * @Annotation\Type("Zend\Form\Element\Password")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"4", "max":"25"}})
     * @Annotation\Options({"label":"Password"})
     * @Annotation\Attributes({"id":"password"})
     */
    public $password;

    /**
     * @Annotation\Type("Zend\Form\Element\Password")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"Identical", "options":{"token":"password"}})
     * @Annotation\Options({"label":"Password confirmation"})
     * @Annotation\Attributes({"id":"password2"})
     */
    public $password2;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit", "class":"btn btn-white"})
     */
    public $submit;
}
