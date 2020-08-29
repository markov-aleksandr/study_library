<?php
$host = 'localhost';
$db = 'Library';
$user = 'root';
$password = '';
$charset = 'utf8';
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO( "mysql:host=$host;dbname=$db;charset=$charset","$user", "$password");
