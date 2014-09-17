<?php

require_once("src/LoginView.php");
require_once("src/LoginModel.php");

class LoginController
{
	private $view;
	private $model;
	private $tempPW = 1234;
	private $tempUSER = "admin";

	public function __construct()
	{
		$this->model = new LoginModel();
		$this->view = new LoginView($this->model);
	}

	public function HandleAccounts()
	{
		//Checks if "doLogout" is sent in the post, if it is, logout the user.
		if (!empty($_POST['doLogout']) && $this->model->IsLoggedIn())
		{
			$this->model->LogOut();
			$this->model->SetFeedbackMessage("You've been logged out successfully!");

			return $this->view->GenerateHTML();
		}

		//Else the user is logging in, so we check if he has a username and password in the fields.
		if (!$this->model->IsLoggedIn() && !empty($_POST['doLogin']))
		{
			if (empty($_POST['username'])) 
			{
				//Show the user that he has to unput a username.
				$this->model->SetFeedbackMessage("Please enter a username!");

				return $this->view->GenerateHTML();
			}

			if (empty($_POST['password'])) 
			{
				//Show the user that he has to input a password.
				$this->model->SetFeedbackMessage("Please enter a password!");

				return $this->view->GenerateHTML();
			}
			//Remove whitespace.
			$login = trim($_POST['username']);
			$pass = trim($_POST['password']);

			//We login the user
			if($this->CheckUserLogin($login, $pass))
			{
				$this->model->Login($login);
				$this->model->SetFeedbackMessage("Login successfull!");
			}
			else //We give the user a error message incase the account doesn't exist.
				$this->model->SetFeedbackMessage("Incorrect username and/or password!");
		}

		return $this->view->GenerateHTML();
	}

	//Checks if the username and password exists and are correct.
	public function CheckUserLogin($user, $pw)
	{
		if ($user == $this->tempUSER && $pw == $this->tempPW) 
		{
			return true;
		}

		return false;
	}
}