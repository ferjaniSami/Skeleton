<?php

namespace Admin\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AclFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \Admin\Service\Acl
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Acl($serviceLocator);
    }
}
