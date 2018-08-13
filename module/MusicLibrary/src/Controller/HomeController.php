<?php

namespace MusicLibrary\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class HomeController extends AbstractActionController {

    public function homeAction() {
        return new ViewModel();
    }

}
