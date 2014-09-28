<?php

Namespace View;

require_once("src/model/LoginModel.php");

use \Model\LoginModel as Model;

class LoginView {
	private $model;

    private $postUsernameKey = "View::Username";
    private $postPasswordKey = "View::Password";
    private $cookieUsernameKey = "View::Username";
    private $cookiePasswordKey = "View::Password";
    private $postRepeatPasswordKey = "View::RepeatPassword";
    private $postLoginButtonKey = "View::LoginButton";
    private $postLogoutButtonKey = "View::LogoutButton";
    private $postRegisterButtonKey = "View::RegisterButton";
    private $postRememberMeCheckboxKey = "View::RememberCheckbox";

	public function __construct(Model $model) {
		$this->model = $model;
	}

	public function getUserAgent() {
		return $_SERVER['HTTP_USER_AGENT'];
	}

	public function getUserIP() {
		return $_SERVER['REMOTE_ADDR'];
	}

	public function getUsernameInput() {
		return (isset($_POST[$this->postUsernameKey]) ? $_POST[$this->postUsernameKey] : "");
	}

	public function getPasswordInput() {
		return (isset($_POST[$this->postPasswordKey]) ? $_POST[$this->postPasswordKey] : "");
	}

	public function didUserRequestLogout() {
		return (isset($_POST[$this->postLogoutButtonKey]));
	}

	public function didUserRequestLogin() {
		return (isset($_POST[$this->postLoginButtonKey]));
	}

    public function didUserRequestRegisterNewUser() {
        return (isset($_POST[$this->$postRegisterButtonKey]));
    }

	public function getRememberMeInput() {
		return (isset($_POST[$this->postRememberMeCheckboxKey]));
	}

	public function renderHtml($isLoggedIn, $feedbackMessage = "") {
        $HtmlString = "<h1>Laborationskod hk222gn (labb2) ► hl222ih (labb4)</h1>";

		if (!$isLoggedIn) {
            if (isset($_GET['register'])) {
                //Register form.
                $HtmlString .=   '
            <h2>Ej inloggad, Registrerar användare</h2>
            <form action="' . $_SERVER['PHP_SELF'] . '" method="post">
                <fieldset>
                    <legend>Registrera ny användare - Skriv in användarnamn och lösenord</legend>'
                    . ($feedbackMessage ? $feedbackMessage : "")
                    . ' <label for="usernameId">Namn:</label>
                    <input type="text" name="' . $this->postUsernameKey . '" id="usernameId" value="' . $this->getUsernameInput() . '" autofocus />
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
                $HtmlString .= 	"
                <h2>Ej inloggad</h2>
                <p><a href='?register'>Registrera ny användare</a></p>
                <form name='f1' method='post' action='?login'>"
                . ($feedbackMessage ? $feedbackMessage : '') . "<h3>Användarnamn</h3>
                <input type='text' name='" . $this->postUsernameKey . "' value='" . $this->getUsernameInput() . "'>
                <h3>Lösenord</h3>
                <input type='password' name='" . $this->postPasswordKey . "'>
                <input type='submit' value='Logga in' name='" . $this->postLoginButtonKey . "'>
                <h3>Kom ihåg mig!</h3>
                <input type ='checkbox' name='" . $this->postRememberMeCheckboxKey . "' value='1'>
                </form>";
            }
		} else {
			$username = $this->model->getSessionUsername();
			$HtmlString .= 	"<h2>$username är inloggad</h2>
							<form name='f2' method='post' action='?logout'>"
							. ($feedbackMessage ? $feedbackMessage : '') .
							"<input type='submit' value='Logga ut' name='doLogout'>
							</form>";
		}

		$day = utf8_encode(strftime("%A"));

		$HtmlString .= "<br/><br/>" . strftime("$day, den %d %B år %Y. Klockan är [%X]."); //gmdate("[H:i:s].", time() + 2 * 60 * 60)

        echo 	"
				<!DOCTYPE html>
				<html lang=\"sv\">
				<head>
					<title>Lab2</title>
					<meta charset=\"utf-8\">
				</head>
				<body>
					$HtmlString
				</body>
				</html>";
	}

    public function setUserCookies($username, $password, $expirationTime) {
        setcookie($this->cookieUsernameKey, $username, $expirationTime);
        setcookie($this->cookiePasswordKey, $password, $expirationTime);
    }

    public function unsetUserCookies() {
	 	setcookie($this->cookieUsernameKey, "", 1);
        setcookie($this->cookiePasswordKey, "", 1);
    }

    public function areCookiesSet() {
        return (isset($_COOKIE[$this->cookiePasswordKey]) &&
            isset($_COOKIE[$this->cookieUsernameKey]));
    }

    public function getCookieUsername() {
    	return (isset($_COOKIE[$this->cookieUsernameKey]) ? $_COOKIE[$this->cookieUsernameKey] : "");
    }

    public function getCookiePassword() {
    	return (isset($_COOKIE[$this->cookiePasswordKey]) ? $_COOKIE[$this->cookiePasswordKey] : "");
    }
}