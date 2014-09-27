<?php

require_once("src/LoginController.php");

session_start();
date_default_timezone_set("Europe/Stockholm");
setlocale(LC_ALL, "sv_SE");

$controller = new LoginController();
$controller->HandleAccounts();
