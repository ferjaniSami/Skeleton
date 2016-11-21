<?php

namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\Controller\PluginManager;

class Translate extends AbstractPlugin {

	private $_translator;

    public function __construct(PluginManager $pluginManager){
        $this->_translator = $pluginManager->getServiceLocator()->get('Translator'); 
    }

    public function __invoke($message, $textDomain = 'default', $locale = null) {
        return $this->_translator->translate($message, $textDomain, $locale);
    }
}