<?php

namespace Application\Controllers;

use Application\Core\Controller;

class Error404Controller extends Controller
{

    function action_index()
    {
        $this->view->generate('404-view.php', 'template-view.php');
    }
}