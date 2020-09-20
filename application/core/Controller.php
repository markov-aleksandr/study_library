<?php

namespace Application\Core;

class Controller
{
    public $view;
    public $model;

    public function __construct()
    {
        $this->view = new View();
    }
}