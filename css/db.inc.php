<?php

require 'vendor/autoload.php';

$DB = new MysqliDb(array(
    'host' => 'localhost',
    'username' => 'smap2',
    'password' => 'dmonster',
    'db' => 'smap2_db',
    'port' => 3306,
    'prefix' => '',
    'charset' => 'utf8mb4'));
