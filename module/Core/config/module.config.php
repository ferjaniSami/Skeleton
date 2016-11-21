<?php
return array(
	'service_manager' => array(
        'services' => array(
            'Core\Locale'    => new Core\Service\Locale(),
        ),
    ),
    'controller_plugins' => array(
        'factories' => array(
            'Core\Controller\Plugin\Translate' => function(\Zend\Mvc\Controller\PluginManager $pluginManager){
                return new \Core\Controller\Plugin\Translate($pluginManager);
            }
        ),
        'aliases' => array(
            'translate' => 'Core\Controller\Plugin\Translate',
        )
    ),
);