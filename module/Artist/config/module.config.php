<?php
namespace Artist;
return array(
     'controllers' => array(
         'invokables' => array(
             'Artist\Controller\Artist' => 'Artist\Controller\ArtistController',
         ),
     ),
	 
	 // The following section is new and should be added to your file
     'router' => array(
         'routes' => array(
             'artist' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/artist[/:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
						 '__NAMESPACE__' => 'Artist\Controller',
                         'controller' => 'Artist',
                         'action'     => 'index',
                     ),
                 ),
             ),
         ),
     ),

     'view_manager' => array(
         'template_path_stack' => array(
             'artist' => __DIR__ . '/../view',
         ),
     ),
	 // Doctrine config
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        )
    ),
 );