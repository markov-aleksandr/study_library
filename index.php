<?php
var_dump($_SERVER['REQUEST_URI']);
ini_set('display_errors', 1);
use Application\Core\Route;

require_once 'vendor/autoload.php';

Route::start();

