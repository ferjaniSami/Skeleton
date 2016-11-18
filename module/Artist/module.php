<?php
namespace Artist;

 use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
 use Zend\ModuleManager\Feature\ConfigProviderInterface;
 // Add these import statements:
 use Artist\Model\Artist;
 use Artist\Model\ArtistTable;
 use Zend\Db\ResultSet\ResultSet;
 use Zend\Db\TableGateway\TableGateway;


 class Module implements AutoloaderProviderInterface, ConfigProviderInterface
 {
     public function getAutoloaderConfig()
     {
         return array(
             'Zend\Loader\ClassMapAutoloader' => array(
                 __DIR__ . '/autoload_classmap.php',
             ),
             'Zend\Loader\StandardAutoloader' => array(
                 'namespaces' => array(
                     __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                 ),
             ),
         );
     }

     public function getConfig()
     {
         return include __DIR__ . '/config/module.config.php';
     }
	 
	 // Add this method:
     public function getServiceConfig()
     {
         return array(
             'factories' => array(
                 'Artist\Model\ArtistTable' =>  function($sm) {
                     $tableGateway = $sm->get('ArtistTableGateway');
                     $table = new ArtistTable($tableGateway);
                     return $table;
                 },
                 'ArtistTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new Artist());
                     return new TableGateway('artist', $dbAdapter, null, $resultSetPrototype);
                 },
             ),
         );
     }

 }