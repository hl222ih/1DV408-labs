<?php

namespace Model;

require_once("src/dal/LoginDal.php");

use \Dal\LoginDal as Dal;

class LoginModel {
	private static $sessionUsername = "Model::LoggedIn";
	private static $sessionUserAgent = "Model::UserAgent";
	private static $sessionUserIP = "Model::UserIP";
    private $dal;
    private $feedbackMessage;

    public function __construct() {
        $this->dal = new Dal("users.txt");
    }

    public function getFeedback() {
        return ($this->feedbackMessage ? $this->feedbackMessage : "");
    }
	private function storeSessionInfo($username) {
        $_SESSION[self::$sessionUsername] = $username;
        $_SESSION[self::$sessionUserAgent] = $_SERVER['HTTP_USER_AGENT'];
		$_SESSION[self::$sessionUserIP] = $_SERVER['REMOTE_ADDR'];
	}

	private function getSessionUserAgent() {
		return (isset($_SESSION[self::$sessionUserAgent]) ? $_SESSION[self::$sessionUserAgent] : false);
	}

	private function getSessionUserIP() {
		return (isset($_SESSION[self::$sessionUserIP]) ? $_SESSION[self::$sessionUserIP] : false);
	}

    public function getSessionUsername() {
        return (isset($_SESSION[self::$sessionUsername]) ? $_SESSION[self::$sessionUsername] : false);
    }
	public function isLoggedIn() {
		return (isset($_SESSION[self::$sessionUsername]) &&
            $this->getSessionUserAgent() === $_SERVER['HTTP_USER_AGENT'] &&
            $this->getSessionUserIP() === $_SERVER['REMOTE_ADDR']);
	}

	public function logout() {
		if (isset($_SESSION[self::$sessionUsername]))
		{
			unset($_SESSION[self::$sessionUsername]);
		}
        $this->feedbackMessage = "Du har nu loggat ut";
	}

	public function login($username, $password, $fromCookie, $rememberMe = false)
	{
        //Remove surrounding whitespace
        $username = trim($username);

		if (!$username) {
            $this->feedbackMessage = "Användarnamn saknas";
        } else if (!$password) {
            $this->feedbackMessage = "Lösenord saknas";
        } else if ($this->validateUser($username, $password, $fromCookie))
		{
			//Store session info = user is logged in
            $this->storeSessionInfo($username);

            $this->feedbackMessage = "Inloggning lyckades";

			if ($fromCookie) {
				$this->feedbackMessage .= " via cookies";
            } else if ($rememberMe) {

                $cookiePassword = $this->encryptCookiePassword($password);
                //cookie expiration: 30 days (30*24*60*60)
                $cookieExpirationTime = time() + 2592000;

                $this->storeCookieInfo($cookieExpirationTime, $cookiePassword);

                $this->feedbackMessage .= " och vi kommer ihåg dig nästa gång";
            }
		} else {
			if ($fromCookie)
                $this->feedbackMessage = "Felaktig information i cookie";
			else
                $this->feedbackMessage = "Fel användarnamn och/eller lösenord!";
		}
	}

	//Checks if the username and password exists and are correct.
    /**
     *
     * @param string $username (username from post or cookie)
     * @param string $password (password from post or hashed password from cookie)
     * @param bool $fromCookie (true if login details were passed from cookie, false if login form was used)
     * @return bool (true if validate was successful, otherwise false)
     */
    private function validateUser($username, $password, $fromCookie) {
        $isValidated = false;

        if ($username == $this->getSessionUsername()) {
            if ($fromCookie) {
                if ($this->dal->getCookieExpiration($username) < time()
                    && $this->dal->getCookiePassword($username) == $password)
                    $isValidated = true;
            } else {
                if ($this->dal->getPassword($username) == $this->encryptPassword($password))
                    $isValidated = true;
            }
        }

		return $isValidated;
	}

    public function encryptCookiePassword($password) {
        $salt = $_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'];
        return md5($salt.$password);
    }
    private function encryptPassword($password) {
        $salt = $this->getSessionUsername();
        return md5($salt.$password);
    }

    public function getCookieExpirationTime() {
        return $this->dal->getCookieExpiration($this->getSessionUsername());
    }

    private function storeCookieInfo($cookieExpirationTime, $cookiePassword) {
        $this->dal->setCookieInfo($this->getSessionUsername(), $cookieExpirationTime, $cookiePassword);
    }

    public function getCookiePassword() {
        return $this->dal->getCookiePassword($this->getSessionUsername());
    }
}