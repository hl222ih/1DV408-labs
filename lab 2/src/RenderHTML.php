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