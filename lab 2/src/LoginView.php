<?php

class LoginView
{
	private $model;
	private $feedbackMessage = "";

	public function __construct(LoginModel $model)
	{
		$this->model = $model;
	}

	public function GetUserAgent()
	{
		return $_SERVER['HTTP_USER_AGENT'];
	}

	public function GetUserIP()
	{
		return $_SERVER['REMOTE_ADDR'];
	}

	public function GetUsernameInput()
	{
		if (isset($_POST['username'])) 
		{ 
			return $_POST['username'];
		}
		return false;
	}

	public function GetPasswordInput()
	{
		if (isset($_POST['password'])) 
		{ 
			return $_POST['password'];
		}
		return false;
	}

	public function DidUserRequestLogout()
	{
		if (isset($_POST['doLogout']))
		{
			return true;
		}
		return false;
	}

	public function DidUserRequestLogin()
	{
		if (isset($_POST['doLogin'])) 
		{
			return true;
		}
		return false;
	}

	public function RememberMe()
	{
		if (isset($_POST['rememberMe']))
			return true;
		return false;
	}

	public function GenerateHTML($loginStatus)
	{
		//Login form.
		if (!$loginStatus)
		{
			$nameInput = $this->GetUsernameInput();
			$HTMLString = 	"<h1>Laborationskod hk222gn</h1>
							<form name='f1' method='post' action='?login'>
							<h3>Användarnamn</h3>
							<input type='text' name='username' value='$nameInput'>
							<h3>Lösenord</h3>
							<input type='password' name='password'>
							<input type='submit' value='Logga in' name='doLogin'>
							<h3>Kom ihåg mig!</h3>
							<input type ='checkbox' name='rememberMe' value='1'>
							</form>";
		}
		else
		{
			$username = $this->model->GetUsername();
			$HTMLString = 	"<h2>$username är inloggad!</h2>
							<form name='f2' method='post' action='?logout'>
							<input type='submit' value='Logga ut' name='doLogout'>
							</form>";
		}

		//Grab the feedback mesesage
		$feedbackMsg = $this->GetFeedbackMessage();

		//Add the feedback message if there is one.
		if (!$feedbackMsg == "") 
		{
			$HTMLString .= $feedbackMsg;
		}

		$day = utf8_encode(strftime("%A"));

		$HTMLString .= "<br/><br/>" . strftime("$day, den %d %B år %Y. Klockan är [%X]."); //gmdate("[H:i:s].", time() + 2 * 60 * 60)
		return $HTMLString;
	}

	public function SetFeedbackMessage($msg)
	{
		$this->feedbackMessage = $msg;
	}

	public function GetFeedbackMessage()
	{
		return $this->feedbackMessage;
	}

    public function SaveUserCookie($us, $pw)
    {
    	$cookieExpirationTime = time() + 60 * 60 * 24;

    	if ($this->AreCookiesSet()) 
    	{
    		$this->UnsetUserCookies();
    	}
        setcookie('username', $us, $cookieExpirationTime);
        setcookie('password', $pw, $cookieExpirationTime);

        $this->model->StoreCookieExpirationTime($cookieExpirationTime);

        return "Inloggningen lyckades och vi kommer ihåg dig nästa gång!";
    }

    public function UnsetUserCookies()
    {
    	unset($_COOKIE['username']);
		unset($_COOKIE['password']);
	 	setcookie('username', "", time() - 3600);
        setcookie('password', "", time() - 3600);
    }

    public function AreCookiesSet()
    {
    	if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) 
    	{
    		return true;
    	}
    	return false;
    }

    public function GetUsernameCookie()
    {
    	if (isset($_COOKIE['username'])) 
    	{
    		return $_COOKIE['username'];
    	}
    	return "";
    }

    public function GetPasswordCookie()
    {
    	if (isset($_COOKIE['password'])) 
    	{
    		return $_COOKIE['password'];
    	}
    	return "";
    }

}