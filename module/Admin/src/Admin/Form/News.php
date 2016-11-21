<?php

namespace Admin\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("admin-form-news")
 */
class News
{
    public function __construct()
    {
        _('Title');
        _('Status');
        _('Submit');
    }

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"250"}})
     * @Annotation\Options({"label":"Title"})
     * @Annotation\Attributes({"id":"title"})
     */
    public $title;

    /**
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({"label":"Status", "label_options":{"always_wrap":"always_wrap"}})
     * @Annotation\Attributes({"id":"status"})
     */
    public $status;

    /**
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({"label":"Status", "label_options":{"always_wrap":"always_wrap"}})
     * @Annotation\Attributes({"id":"status_translation"})
     */
    public $status_translation;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit", "class":"btn btn-white"})
     */
    public $submit;
}
