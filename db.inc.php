<?php

require 'vendor/autoload.php';

$DB = new MysqliDb(array(
    'host' => 'localhost',
    'username' => 'smap',
    'password' => 'dmonster',
    'db' => 'smap_db',
    'port' => 3306,
    'prefix' => '',
    'charset' => 'utf8mb4'));
