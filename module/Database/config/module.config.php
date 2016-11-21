<?php

return array(
    'doctrine' => array(
        'driver' => array(
            'database_metadata_driver' => array(
                  'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                  'cache' => 'array',
                  'paths' => array(__DIR__ . '/../src/Database/Entity')
            ),
            // 'translatable_metadata_driver' => array(
            //     'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
            //     'cache' => 'array',
            //     'paths' => array(
            //         'vendor/gedmo/doctrine-extensions/lib/Gedmo/Translatable/Entity',
            //     )
            // ),
            'orm_default' => array(
                  'drivers' => array(
                    'Database\Entity'           => 'database_metadata_driver',
                    // 'Gedmo\Translatable\Entity' => 'translatable_metadata_driver'
                )
            )
        ),
        'eventmanager' => array(
            'orm_default' => array(
                'subscribers' => array(
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\SoftDeleteable\SoftDeleteableListener',
                    'Gedmo\Translatable\TranslatableListener',
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'services' => array(
            'EntityHydrator' => new Database\Service\EntityHydrator(),
        ),
    ),
);
