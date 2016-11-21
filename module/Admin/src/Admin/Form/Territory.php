<?php

namespace Admin\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("admin-form-territory")
 */
class Territory
{
    public function __construct()
    {
        _('Name');
        _('Url code');
        _('Langs');
        _('Default lang');
        _('Status');
        _('Submit');
    }

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"50"}})
     * @Annotation\Options({"label":"Name"})
     * @Annotation\Attributes({"id":"name"})
     */
    public $name;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Filter({"name":"StringToLower"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"10"}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^\w+$/"}})
     * @Annotation\Options({"label":"Url code"})
     * @Annotation\Attributes({"id":"url_code"})
     */
    public $url_code;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Options({"label":"Langs"})
     * @Annotation\Attributes({"id":"langs", "multiple":"multiple"})
     */
    public $langs;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Options({"label":"Default lang", "disable_inarray_validator":"true"})
     * @Annotation\Attributes({"id":"default_lang"})
     */
    public $default_lang;

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
