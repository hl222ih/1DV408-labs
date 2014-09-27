<?php

class LoginView
{
	private $model;
	private $feedbackMessage = "";

    private $postUsernameKey = "View::Username";
    private $postPasswordKey = "View::Password";
    private $postRepeatPasswordKey = "View::RepeatPassword";
    private $postLoginButtonKey = "View::LoginButton";
    private $postLogoutButtonKey = "View::LogoutButton";
    private $postRegisterButtonKey = "View::RegisterButton";
    private $postRememberMeCheckboxKey = "View::RememberCheckbox";

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
		if (isset($_POST[$this->postUsernameKey]))
		{ 
			return $_POST[$this-postUsernameKey];
		}
		return false;
	}

	public function GetPasswordInput()
	{
		if (isset($_POST[$this->postPasswordKey]))
		{ 
			return $_POST[$this->postPasswordKey];
		}
		return false;
	}

	public function DidUserRequestLogout()
	{
		if (isset($_POST[$this->postLogoutButtonKey]))
		{
			return true;
		}
		return false;
	}

	public function DidUserRequestLogin()
	{
		if (isset($_POST[$this->postLoginButtonKey]))
		{
			return true;
		}
		return false;
	}

    public function DidUserWantToRegister()
    {
        if (isset($_POST[$this->$postRegisterButtonKey]))
        {
            return true;
        }
        return false;
    }

	public function RememberMe()
	{
		if (isset($_POST[$this->postRememberMeCheckboxKey]))
			return true;
		return false;
	}

	public function GenerateHTML($isLoggedIn)
	{
		if (!$isLoggedIn)
		{
            if (isset($_GET['register'])) {
                //Register form.
                $HTMLString =   '
            <h2>Ej inloggad, Registrerar användare</h2>
            <form action="' . $_SERVER['PHP_SELF'] . '" method="post">
                <fieldset>
                    <legend>Registrera ny användare - Skriv in användarnamn och lösenord</legend>'
                . ' <label for="usernameId">Namn:</label>
                    <input type="text" name="' . $this->postUsernameKey . '" id="usernameId" value="' . $this->GetUsernameInput() . '" autofocus />
                    <br /><label for="passwordId">Lösenord:</label>
                    <input type="password" name="' . $this->postPasswordKey . '" id="passwordId" />
                    <br /><label for="repeatPasswordId">Repetera lösenord:</label>
                    <input type="password" name="' . $this->postRepeatPasswordKey . '" id="repeatPasswordId" />
                    <br /><label for="registerId">Skicka:</label>
                    <input type="submit" id="registerId" name="' . $this->postRegisterButtonKey . '" value="Registrera" />
                </fieldset>
            </form>
            ';
            } else {
                //Login form.
                $HTMLString = 	"
                <h1>Laborationskod hk222gn</h1>
                <p><a href='?register'>Registrera ny användare</a></p>
                <h2>Ej inloggad</h2>
                <form name='f1' method='post' action='?login'>
                <h3>Användarnamn</h3>
                <input type='text' name='" . $this->postUsernameKey . "' value='" . $this->GetUsernameInput() . "'>
                <h3>Lösenord</h3>
                <input type='password' name='" . $this->postPasswordKey . "'>
                <input type='submit' value='Logga in' name='" . $this->postLoginButtonKey . "'>
                <h3>Kom ihåg mig!</h3>
                <input type ='checkbox' name='" . $this->postRememberMeCheckboxKey . "' value='1'>
                </form>";
            }
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
		$this->RenderHTML($HTMLString);
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