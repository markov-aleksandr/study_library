<?php

namespace Application\Controllers;

use Applicatiom\Models\CreateModel;
use Application\Core\Controller;

class CreateController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new CreateModel();
    }

    public function actionIndex()
    {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $language = $_POST['lang'];
        $isSubmit = $_POST['submit'];

        $this->model->createBook($title, $author, $language, $isSubmit);
        $this->view->generate('create-view.php', 'template-view.php');
    }

}
