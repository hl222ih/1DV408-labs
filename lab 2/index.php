<?php

require_once("src/controller/LoginController.php");

use \Controller\LoginController as Controller;

session_start();
date_default_timezone_set("Europe/Stockholm");
setlocale(LC_ALL, "sv_SE");

$controller = new Controller();
$controller->start();
