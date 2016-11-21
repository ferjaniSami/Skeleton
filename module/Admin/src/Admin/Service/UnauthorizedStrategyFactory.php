<?php

namespace Admin\Service;

use Admin\View\UnauthorizedStrategy;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class UnauthorizedStrategyFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \Admin\View\UnauthorizedStrategy
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new UnauthorizedStrategy('admin/disconnect', 'admin/error/unauthorized');
    }
}
