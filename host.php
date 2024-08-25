<?php

try {
    $db = new PDO('mysql:host=localhost; dbname=correction', 'root', '');
    $db->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAME 'utf8'");
} catch (Exception $e) {
    echo 'Impossible de se connecter à la base de données.';
    die;
}

session_start();