<?php

namespace Application\View\Helper\Factory;

use Application\View\Helper\ApplicationHelper;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Db\Adapter\AdapterInterface;

class ApplicationHelperFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        return new ApplicationHelper($container->get('Application')->getMvcEvent()->getRouteMatch(), $container->get('Router'), $container->get('Request'));
    }

}
