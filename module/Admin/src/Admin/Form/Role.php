<?php

namespace Admin\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("admin-form-role")
 */
class Role
{
    public function __construct()
    {
        _('Label');
        _('Description');
        _('Acl');
        _('Territories');
        _('Langs');
        _('Name');
        _('Status');
        _('Submit');
    }

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"20"}})
     * @Annotation\Options({"label":"Label"})
     * @Annotation\Attributes({"id":"label"})
     */
    public $label;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"100"}})
     * @Annotation\Options({"label":"Description"})
     * @Annotation\Attributes({"id":"description"})
     */
    public $description;

    /**
     * @Annotation\Type("Zend\Form\Element\MultiCheckbox")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Acl", "disable_inarray_validator":"true"})
     */
    public $acl;

    /**
     * @Annotation\Type("Zend\Form\Element\MultiCheckbox")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Territories", "disable_inarray_validator":"true"})
     */
    public $territories;

    /**
     * @Annotation\Type("Zend\Form\Element\MultiCheckbox")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Langs", "disable_inarray_validator":"true"})
     */
    public $langs;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Filter({"name":"StringToLower"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"5", "max":"20"}})
     * @Annotation\Options({"label":"Name"})
     * @Annotation\Attributes({"id":"name"})
     */
    public $name;

    /**
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({"label":"Status", "label_options":{"always_wrap":"always_wrap"}})
     * @Annotation\Attributes({"id":"status"})
     */
    public $status;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit", "class":"btn btn-white"})
     */
    public $submit;

}
