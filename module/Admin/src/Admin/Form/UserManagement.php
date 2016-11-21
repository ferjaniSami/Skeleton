<?php

namespace Admin\Form;

use Zend\Form\Annotation;

use Admin\Form\User as User;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("admin-form-user-management")
 */
class UserManagement extends User
{
    public function __construct()
    {
        _('Super admin');
        _('Roles');
        _('Status');
    }

    /**
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({"label":"Status", "label_options":{"always_wrap":"always_wrap"}})
     * @Annotation\Attributes({"id":"status"})
     */
    public $status;

    /**
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({"label":"Super admin", "label_options":{"always_wrap":"always_wrap"}})
     * @Annotation\Attributes({"id":"super_admin"})
     */
    public $super_admin;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Roles"})
     * @Annotation\Attributes({"id":"roles", "multiple":"multiple"})
     */
    public $roles;
}
