<?php

namespace Application\View\Helper\Factory;

use Application\View\Helper\StructuredDataHelper;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Api\V1\Rest\Service\ReviewsService;

class StructuredDataHelperFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        return new StructuredDataHelper(
                $container->get('Config'), $container->get(AdapterInterface::class), $container->get('Router'), $container->get('Request'), $container->get(ReviewsService::class)
        );
    }

}
