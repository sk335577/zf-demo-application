<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Controller\BaseController\BaseActionController;

class IndexController extends BaseActionController {

    public function indexAction() {
        return false;
    }

    public function loginAction() {

        $this->prepareRequest();

        if ($this->settings['method'] == 'POST') {
            echo "<pre>";
            print_r($this->settings);
            echo "</pre>";
            die;
        } else {
            if ($this->settings['method'] == 'GET') {
                $viewModel = new ViewModel();
                $this->layout('admin/layout/login');
                return $viewModel;
            }
        }
    }

}
