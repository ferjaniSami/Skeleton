<?php

namespace Admin\Service;

use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Doctrine\Common\Util\Inflector;

use Admin\Exception\UnauthorizedException;

class Acl extends ZendAcl
{

    const SUFFIX_ACTION     = 'Action';
    const SUFFIX_CONTROLLER = 'Controller';

    const ERROR_UNAUTHORIZED = 'error-unauthorized';

    /**
     * @var array
     */
    protected $skipActionsList = array('notFoundAction', 'getMethodFromAction');

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Default role
     */
    const DEFAULT_ROLE = 'guest';

    /**
     * @param array                                         $config
     * @param \Zend\ServiceManager\ServiceLocatorInterface  $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->config         = $serviceLocator->get('Config');
        $this->entityManager  = $serviceLocator->get('Db');
    }

    /**
     * @param \Zend\Mvc\MvcEvent $event
     * @return mixed
     */
    public function check(MvcEvent $event, $redirection = false)
    {
        $user       = $this->serviceLocator->get('Auth')->getIdentity();
        $role       = $user === null ? static::DEFAULT_ROLE : $user->getLogin() ;

        $resources  = $this->getResourcesRules();
        $controller = $event->getRouteMatch()->getParam('controller');
        $action     = $event->getRouteMatch()->getParam('action');

        $this->addRole(new Role($role));

        $resource = $controller;
        $this->addResource(new Resource($resource));
        if(isset($resources[$controller])){
            $resource_controller = $resources[$controller];
            if(!in_array($action, $resource_controller)){
                $this->allow($role, $resource, $action);
            }
        }else{
            $this->allow($role, $resource, $action);
        }

        if($user !== null){
            if($user->getSuperAdmin()){
                foreach($this->getResourcesRules() as $_resource => $_actions){
                    foreach($_actions as $_action){
                        if(!$this->hasResource($_resource)) {
                            $this->addResource(new Resource($_resource));
                        }
                        $this->allow($role, $_resource, $_action);
                    }
                }
            }else{
                $user_roles_ids = \Zend\Json\Json::decode($user->getRoles());
                foreach($user_roles_ids as $id){
                    if(null !== ($_role = $this->entityManager->getRepository('Admin\Entity\Role')->findOneBy(array('id' => $id, 'status' => 1)))){
                        $_acl = \Zend\Json\Json::decode($_role->getAcl());
                        foreach($_acl as $__acl){
                            list($_controller, $_action) = explode('_', $__acl);
                            $_resource = str_replace('-', '\\', $_controller);
                            if(!$this->hasResource($_resource)) {
                                $this->addResource(new Resource($_resource));
                            }
                            $this->allow($role, $_resource, $_action);
                        }
                    }
                }
            }
        }

        if($this->isAllowed($role, $resource, $action)) {
            return true;
        }else{
            if($redirection === false){
                return false;
            }
            if($user === null){
                $event->getApplication()->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', MvcEvent::EVENT_DISPATCH, function ($e) {
                    $controller = $e->getTarget();
                    $controller->plugin('redirect')->toRoute(
                        'admin/default',
                        array(
                            'action'     => 'login',
                            'controller' => 'user'
                        )
                    );
                }, 100);
            }else{
                $event->setError(static::ERROR_UNAUTHORIZED);
                $event->setParam('identity', $user);
                $event->setParam('controller', $controller);
                $event->setParam('action', $action);

                $errorMessage = sprintf(_('You are not authorized to access %s:%s'), $controller, $action);
                $event->setParam('exception', new UnauthorizedException($errorMessage));

                /* @var $app \Zend\Mvc\ApplicationInterface */
                $app = $event->getTarget();
                $app->getEventManager()->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
            }
        }

    }

    public function getResourcesRules()
    {
        $actions = array();

        if(isset($this->config['controllers']['invokables']) && isset($this->config['controllers']['acl'])){
            $controllers = $this->config['controllers']['invokables'];
            $acls        = $this->config['controllers']['acl'];
            foreach($controllers as $key => $moduleClass){
                $tmpArray = get_class_methods($moduleClass);
                foreach($tmpArray as $action){
                    if(substr_compare($action, static::SUFFIX_ACTION, -strlen(static::SUFFIX_ACTION)) === 0 && !in_array($action, $this->skipActionsList)){
                        $controllerName = substr($moduleClass, 0, strlen($moduleClass) - strlen(static::SUFFIX_CONTROLLER));
                        $actionName     = substr($action, 0, strlen($action) - strlen(static::SUFFIX_ACTION));

                        $include = isset($acls[$controllerName]['includes']) && is_array($acls[$controllerName]['includes']) && !empty($acls[$controllerName]['includes']) ? ( in_array($actionName, $acls[$controllerName]['includes']) ? true : false ) : true ;
                        $exclude = isset($acls[$controllerName]['excludes']) && is_array($acls[$controllerName]['excludes']) && !empty($acls[$controllerName]['excludes']) ? ( in_array($actionName, $acls[$controllerName]['excludes']) ? true : false ) : false ;
                        if(isset($acls[$controllerName]) && $include && !$exclude){
                            $actions[$controllerName][] = \Core\Service\Inflector::filter(array('Word\CamelCaseToUnderscore', 'Word\UnderscoreToDash', 'StringToLower'), $actionName);
                        }
                    }
                }
            }
        }

        return $actions;
    }

}
