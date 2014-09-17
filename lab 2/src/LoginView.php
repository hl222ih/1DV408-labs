<?php

require_once("src/LoginModel.php");

class LoginView
{
	private $model;

	public function __construct(LoginModel $model)
	{
		$this->model = $model;
	}

	//Date and time needs to be displayed here aswell
	public function GenerateHTML()
	{
		
		//Login form.
		if (!$this->model->IsLoggedIn()) 
		{
			$HTMLString = 	"<form name='f1' method='post' action=''>
							<h3>Använder</h3>
							<input type='text' name='username'>
							<h3>Lösenord</h3>
							<input type='password' name='password'>
							<input type='submit' value='Logga in' name='doLogin'>
							</form>";
		}
		else
		{
			$username = $this->model->GetUsername();
			$HTMLString = 	"<h2>$username är inloggad!</h2>
							<form name='f2' method='post' action=''>
							<input type='submit' value='Logga ut' name='doLogout'>
							</form>";
		}

		//Grab the feedback mesesage
		$feedbackMsg = $this->model->GetFeedbackMessage();

		//Add the feedback message if there is one.
		if (!$feedbackMsg == "") 
		{
			$HTMLString .= $feedbackMsg;
		}

		$HTMLString .= "<br/><br/>" . strftime("%A,") . " den " . strftime("%d ") . strftime("%B ") . "år " . strftime("%Y. ") . "Klockan är " . strftime("[%X].");
		return $HTMLString;
	}
}