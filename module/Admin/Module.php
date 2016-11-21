<?php
namespace Admin;

use Zend\ModuleManager\Feature\DependencyIndicatorInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManager;
use Zend\Validator\AbstractValidator;

class Module implements
    DependencyIndicatorInterface,
    AutoloaderProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getModuleDependencies()
    {
        return array(
            'Core',
            'Database'
        );
    }

    public function onBootstrap(MvcEvent $event)
    {
        $eventManager = $event->getApplication()->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'protectArea'), -100);
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array($this, 'loadConfiguration'), 100);

        $unauthorizedStrategy = $event->getApplication()->getServiceManager()->get('Admin\View\UnauthorizedStrategy');
        $eventManager->attach($unauthorizedStrategy);

        $eventManager->getSharedManager()->attach('Zend\Mvc\Controller\AbstractController', MvcEvent::EVENT_DISPATCH, function ($event) {
            if($event->getRouteMatch()->getParam('action') == 'not-found'){
                $viewModel = $event->getViewModel();
                $viewModel->setTemplate('admin/disconnect');
            }
        }, -100);

        $translator = $event->getApplication()->getServiceManager()->get('translator');
        $translator->addTranslationFile(
            'phpArray',
            'vendor/zendframework/zendframework/resources/languages/' . \Locale::getPrimaryLanguage($translator->getLocale()) . '/Zend_Validate.php',
            'default',
            $translator->getLocale()
        );
        AbstractValidator::setDefaultTranslator($translator);
    }

    public function protectArea(MvcEvent $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();
        $acl = $serviceManager->get('Admin\Service\Acl');
        $acl->check($event, true);
        $viewModel = $event->getViewModel();
        $viewModel->acl = $acl;
    }

    public function loadConfiguration(MvcEvent $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();

        $controller = $event->getRouteMatch()->getParam('controller');
        if (0 !== strpos($controller, __NAMESPACE__, 0)) {
            //if not this module
            return;
        }

        //if this module
        $exceptionstrategy = $serviceManager->get('ViewManager')->getExceptionStrategy();
        $exceptionstrategy->setExceptionTemplate('admin/error/index');
        $routenotfoundstrategy = $serviceManager->get('ViewManager')->getRouteNotFoundStrategy();
        $routenotfoundstrategy->setNotFoundTemplate('admin/error/404');
        $viewModel = $event->getViewModel();
        $viewModel->setTemplate('admin/disconnect');
    }
}
