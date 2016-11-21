<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Admin\Controller\Index'       => 'Admin\Controller\IndexController',
            'Admin\Controller\User'        => 'Admin\Controller\UserController',
            'Admin\Controller\Users'       => 'Admin\Controller\UsersController',
            'Admin\Controller\Roles'       => 'Admin\Controller\RolesController',
            'Admin\Controller\Territories' => 'Admin\Controller\TerritoriesController',
            'Admin\Controller\News'        => 'Admin\Controller\NewsController',
        ),
        'acl' => array(
            'Admin\Controller\Index'       => array(),
            'Admin\Controller\User'        => array(
                'includes' => array(),
                'excludes' => array('login', 'logout')
            ),
            'Admin\Controller\Users'       => array(),
            'Admin\Controller\Roles'       => array(),
            'Admin\Controller\Territories' => array(),
            'Admin\Controller\News'        => array(),
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/admin',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]][/]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            // 'wildcard' => array(
                            //     'type' => 'Wildcard',
                            // ),
                            'params' => array(
                                'type' => 'regex',
                                'options' => array(
                                    'regex' => '(?<params>.*)',
                                    'defaults' => array(
                                    ),
                                    'spec' => '%params%',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'template_map' => array(
            'admin/layout'             => __DIR__ . '/../view/layout/layout.phtml',
            'admin/disconnect'         => __DIR__ . '/../view/layout/disconnect.phtml',
            'admin/error/404'          => __DIR__ . '/../view/error/404.phtml',
            'admin/error/index'        => __DIR__ . '/../view/error/index.phtml',
            'admin/error/unauthorized' => __DIR__ . '/../view/error/unauthorized.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        ),
    ),
    'module_layouts' => array(
        'Admin'   => 'admin/layout'
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                __DIR__ . '/../public',
            ),
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'        => 'gettext',
                'base_dir'    => __DIR__ . '/../language',
                'pattern'     => '%s.mo',
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Authentication\AuthenticationService' => function ($serviceManager) {
                return $serviceManager->get('doctrine.authenticationservice.orm_default');
            },
            'Admin\Service\Acl'               => 'Admin\Service\AclFactory',
            'Admin\View\UnauthorizedStrategy' => 'Admin\Service\UnauthorizedStrategyFactory',
            'Admin\Navigation'                => 'Admin\Service\Navigation',
        ),
        'aliases' => array(
            'Db'         => 'Doctrine\ORM\EntityManager',
            'Auth'       => 'Zend\Authentication\AuthenticationService',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'adminFormRow'      => 'Admin\View\Helper\Form\FormRow',
            'adminFormRowLabel' => 'Admin\View\Helper\Form\FormRowLabel',
            'formLabel'         => 'Admin\View\Helper\Form\FormLabel',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'admin_driver' => array(
                  'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                  'cache' => 'array',
                  'paths' => array(__DIR__ . '/../src/Admin/Entity')
            ),
            'orm_default' => array(
                  'drivers' => array(
                    'Admin\Entity' => 'admin_driver'
                )
            )
        ),
        'authentication' => array(
            'orm_default' => array(
                'object_manager'      => 'Doctrine\ORM\EntityManager',
                'identity_class'      => 'Admin\Entity\User',
                'identity_property'   => 'login',
                'credential_property' => 'password',
                'credential_callable' => function (\Admin\Entity\User $user, $password) {
                    if ($user->getPassword() == sha1($password) && $user->getStatus() == 1) {
                        return true;
                    } else {
                        return false;
                    }
                },
            ),
        ),
    ),
    'navigation' => array(
        'admin' => array(
            'dashboard' => array(
                'label'     => 'Dashboard',
                'icon'      => 'fa fa-th-large',
                'route'     => 'admin',
                'resource'  => 'Admin\Controller\Index',
                'privilege' => 'index',
            ),
            'content' => array(
                'label'     => 'Content',
                'icon'      => 'fa fa-list-alt',
                'uri'       => '#',
                'pages'     => array(
                    'content/news' => array(
                        'label'      => 'News',
                        'route'      => 'admin/default',
                        'action'     => 'index',
                        'controller' => 'news',
                        'resource'   => 'Admin\Controller\News',
                        'privilege'  => 'index',
                    ),
                ),
            ),
            'management' => array(
                'label'     => 'Management',
                'icon'      => 'fa fa-users',
                'uri'       => '#',
                'pages'     => array(
                    'management/users' => array(
                        'label'      => 'Users',
                        'route'      => 'admin/default',
                        'action'     => 'index',
                        'controller' => 'users',
                        'resource'   => 'Admin\Controller\Users',
                        'privilege'  => 'index',
                    ),
                    'management/roles' => array(
                        'label'      => 'Roles',
                        'route'      => 'admin/default',
                        'action'     => 'index',
                        'controller' => 'roles',
                        'resource'   => 'Admin\Controller\Roles',
                        'privilege'  => 'index',
                    ),
                ),
            ),
            'localisation' => array(
                'label'     => 'Localisation',
                'icon'      => 'fa fa-language',
                'uri'       => '#',
                'pages'     => array(
                    'localisation/territories' => array(
                        'label'      => 'Territories',
                        'route'      => 'admin/default',
                        'action'     => 'index',
                        'controller' => 'territories',
                        'resource'   => 'Admin\Controller\Territories',
                        'privilege'  => 'index',
                    ),
                ),
            ),
        ),
     ),
);
