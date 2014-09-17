<?php

class LoginModel
{
	private $userIP; //the IP the user logged in with.
	private $feedbackMessage = "";

	public function IsLoggedIn()
	{
		if (!isset($_SESSION['loggedIn']))
		{
			return false;
		}

		return @$_SESSION['loggedIn'];
	}

	public function Logout()
	{
		if (isset($_SESSION['loggedIn'])) 
		{
			unset($_SESSION['loggedIn']);
			return true;
		}
		return false;
	}

	public function Login($username)
	{
		$_SESSION['loggedIn'] = $username;

		$userIP = $_SERVER['REMOTE_ADDR'];
	}

	public function GetUsername()
	{
		return $_SESSION['loggedIn'];
	}

	public function SetFeedbackMessage($msg)
	{
		$this->feedbackMessage = $msg;
	}

	public function GetFeedbackMessage()
	{
		return $this->feedbackMessage;
	}
}