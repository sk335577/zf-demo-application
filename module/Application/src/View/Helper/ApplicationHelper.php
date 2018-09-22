<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Router;
use Zend\Router\Http\TreeRouteStack;
use Zend\Http\Request;
use Zend\Router\RouteMatch;

class ApplicationHelper extends AbstractHelper {

    protected $route_match;
    protected $router;
    protected $request;

    public function __construct(RouteMatch $route_match, TreeRouteStack $router, Request $request) {
        $this->route_match = $route_match;
        $this->router = $router;
        $this->request = $request;
    }

    public function getControllerAsJSScript() {
        $controller_parts = explode('\\', $this->route_match->getParam('controller'));
        return strtolower(preg_replace('/(?<!^)[A-Z]+|(?<!^|\d)[\d]+/', '-' . '$0', $controller_parts[count($controller_parts) - 1]));
    }

    public function getControllerActionAsJSScript() {
        $action_parts = explode('\\', $this->route_match->getParam('action'));
        return strtolower(preg_replace('/(?<!^)[A-Z]+|(?<!^|\d)[\d]+/', '-' . '$0', $action_parts[count($action_parts) - 1]));
    }

}
