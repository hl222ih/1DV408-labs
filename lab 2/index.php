<?php

require_once("src/RenderHTML.php");
require_once("src/LoginController.php");

session_start();
date_default_timezone_set("Europe/Stockholm");
setlocale(LC_ALL, "sv_SE");

$controller = new LoginController();
$body = $controller->HandleAccounts();

$view = new HTMLRenderer();
$view->RenderHTML($body);