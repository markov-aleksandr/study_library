<?php

namespace Application\Controllers;

use Application\Core\Controller;

class MainController extends Controller
{
    public function actionIndex()
    {
        $this->view->generate('main-view.php', 'template-view.php');
    }

}