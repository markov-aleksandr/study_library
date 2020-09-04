<?php
require 'header.php';
if (isset($_GET['pages'])){
    $page = htmlentities($_GET['pages']);
    $path = './pages/'.$page.'.php';
    if (file_exists($path)){
        include($path);
        die();
    }
}else{
    include('./pages/home.php');
}


require 'footer.php';