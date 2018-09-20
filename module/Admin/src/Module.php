<?php

namespace Admin;

use Zend\Mvc\MvcEvent;

class Module {

    const VERSION = '3.0.3-dev';

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e) {
        $application = $e->getApplication();
        $application->getEventManager()->attach('render', array($this, 'setLayoutTitle'));
    }

    public function setLayoutTitle(MvcEvent $e) {

        $matches = $e->getRouteMatch();
        $action = $matches->getParam('action');
        $controller = $matches->getParam('controller');
        $module = __NAMESPACE__;
        $siteName = 'Zend Framework';

        // Getting the view helper manager from the application service manager
        $viewHelperManager = $e->getApplication()->getServiceManager()->get('ViewHelperManager');

        // Getting the headTitle helper from the view helper manager
        $headTitleHelper = $viewHelperManager->get('headTitle');

        // Setting a separator string for segments
        $headTitleHelper->setSeparator(' - ');

        // Setting the action, controller, module and site name as title segments
        $headTitleHelper->append($action);
        $headTitleHelper->append($controller);
        $headTitleHelper->append($module);
        $headTitleHelper->append($siteName);
    }

}
