<?php

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
                         'controller' => 'Artist\Controller\Artist',
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
 );