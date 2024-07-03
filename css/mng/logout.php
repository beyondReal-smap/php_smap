<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

session_unset();
session_destroy();
unset($_SESSION);
unset($_COOKIE);

gotourl("./login.php");

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
