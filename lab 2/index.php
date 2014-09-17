<?php

require_once("src/RenderHTML.php");
require_once("src/LoginController.php");

session_start();
setlocale(LC_ALL, "sv");

$controller = new LoginController();
$body = $controller->HandleAccounts();

$view = new HTMLRenderer();
$view->RenderHTML($body);