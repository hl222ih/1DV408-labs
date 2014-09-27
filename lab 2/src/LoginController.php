<?php

require_once("src/LoginView.php");
require_once("src/LoginModel.php");

class LoginController
{
	private $view;
	private $model;

	public function __construct()
	{
		$this->model = new LoginModel();
		$this->view = new LoginView($this->model);
	}

	public function HandleAccounts()
	{
		if ($this->model->IsLoggedIn($this->view->GetUserAgent(), $this->view->GetUserIP())) 
		{
			//Checks if "doLogout" is sent in the post, if it is and the user is actually logged in, log the user out...
			if ($this->view->DidUserRequestLogout())
			{
				$feedback = $this->model->LogOut();
				$this->view->UnsetUserCookies();
				$this->view->SetFeedbackMessage($feedback);
			}
		}
		else
		{
			//...then we check if the user requested to login....
			if ($this->view->DidUserRequestLogin())
			{
				$feedback = $this->model->Login($this->view->GetUsernameInput(), $this->view->GetPasswordInput());
                $this->model->SaveUserSpecificInformation($this->view->GetUserAgent(), $this->view->GetUserIP());
                if ($this->model->IsLoggedIn($this->view->GetUserAgent(), $this->view->GetUserIP())) {

                    if ($this->view->RememberMe())
                    {
                        //Create a one time use password for the cookie.
                        $user = $this->view->GetUsernameInput();
                        $pw = $this->model->CreateOneTimePassword($user);

                        //Save in cookie
                        $feedback = $this->view->SaveUserCookie($user, $pw);
                    }
                }
                $this->view->SetFeedbackmessage($feedback);
			}

			//...if he didn't press the login button but he has saved cridentials, log him in using cookies.
			if ($this->view->AreCookiesSet() && !$this->view->DidUserRequestLogin()) 
			{
				$feedback = $this->model->Login($this->view->GetUsernameCookie(), $this->view->GetPasswordCookie(), true);

				$this->model->SaveUserSpecificInformation($this->view->GetUserAgent(), $this->view->GetUserIP());

				$this->view->SetFeedbackmessage($feedback);
			}
		}

		$this->view->GenerateHTML($this->model->IsLoggedIn($this->view->GetUserAgent(), $this->view->GetUserIP()));
	}
}