<?php

namespace Application\Controller\BaseController;

use Zend\Stdlib\RequestInterface as Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\EventManager\EventManagerInterface;

class BaseActionController extends AbstractActionController {

    protected $settings = [];

    protected function isRequestContentTypeJson(Request $request, $contentType = 'json') {
        /** @var $headerContentType \Zend\Http\Header\ContentType */
        $headerContentType = $request->getHeaders()->get('content-type');
        if (!$headerContentType) {
            return false;
        }

        $requestedContentType = $headerContentType->getFieldValue();
        if (false !== strpos($requestedContentType, ';')) {
            $headerData = explode(';', $requestedContentType);
            $requestedContentType = array_shift($headerData);
        }
        $requestedContentType = trim($requestedContentType);
        if (array_key_exists($contentType, ['application/hal+json', 'application/json'])) {
            foreach ($this->contentTypes[$contentType] as $contentTypeValue) {
                if (stripos($contentTypeValue, $requestedContentType) === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function mysqlEscapeCustom($input) {

        if (is_array($input))
            return array_map(__METHOD__, $input);

        if (!empty($input) && is_string($input)) {
            $input = trim($input);
            $input = strip_tags($input);
            $input = addslashes($input);
//            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $input);
            return $input;
        }

        return $input;
    }

    protected function prepareRequest() {

        foreach ($this->params()->fromRoute() as $k => $v) {
            $this->settings['route_params'][$k] = $v;
        }

        foreach ($this->params()->fromHeader() as $k => $v) {
//            $this->settings['header_params'][$k] = addcslashes($v, '*/\\');
            $this->settings['header_params'][$k] = $v;
        }

        $this->settings["method"] = strtoupper($this->getRequest()->getMethod());

        $this->settings["post"] = array();
        $this->settings["get"] = array();
        $this->settings["files"] = array();

        if (in_array($this->settings["method"], array('POST', 'PATCH', 'PUT'))) {

            $this->settings["files"] = $this->getRequest()->getFiles()->toArray();

            if ($this->isRequestContentTypeJson($this->getRequest())) {
                $request_data = ($this->jsonDecode($this->getRequest()->getContent()));
            } else {
                $request_data = $this->getRequest()->getPost()->toArray();
            }

            if (!empty($request_data)) {
                foreach ($request_data as $k => $v) {
                    $this->settings["post"][$k] = $this->mysqlEscapeCustom($v);
                }
            }
        } else {
            if (in_array($this->settings["method"], array('GET', 'DELETE'))) {
                $request_data = $this->params()->fromQuery();
                if (!empty($request_data)) {
                    foreach ($request_data as $k => $v) {
                        $this->settings["get"][$k] = $this->mysqlEscapeCustom($v);
                    }
                }
            }
        }
    }

}
