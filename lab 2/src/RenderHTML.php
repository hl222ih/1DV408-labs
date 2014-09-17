<?php

class HTMLRenderer
{
	public function RenderHTML($body = "", $head = "")
	{
		if ($body === NULL) 
		{
			throw new \Exception("RenderHTML does not allow a NULL body.");
		}

		echo 	"
				<!DOCTYPE html>
				<html>
				<head>
					<meta charset=\"utf-8\">
					$head
				</head>
				<body>
					$body
				</body>
				</html>";
	}
}