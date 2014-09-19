<?php

class HTMLRenderer
{
	public function RenderHTML($body = "", $head = "")
	{
		if ($body === NULL) 
		{
			$body = "NULL";
		}

		echo 	"
				<!DOCTYPE html>
				<html lang=\"sv\">
				<head>
					<title>Lab2</title>
					<meta charset=\"utf-8\">
					$head
				</head>
				<body>
					$body
				</body>
				</html>";
	}
}
//TODO LIST:
//UC 3.5 -> 3-6 är inte korrekt implementerade helller.

//Spara en textfil med användarnamnet ist? 

//Flytta ut alla feedback meddelanden till en funktion i Loginview?