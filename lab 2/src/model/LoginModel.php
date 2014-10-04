<?php

namespace Model;

require_once("src/dal/LoginDal.php");

use \Dal\LoginDal as Dal;

class LoginModel {
	private static $sessionUsernameKey = "Model::LoggedIn";
	private static $sessionUserAgentKey = "Model::UserAgent";
	private static $sessionUserIPKey = "Model::UserIP";
    private static $sessionFeedbackMessageKey = "Model::FeedbackMessage";
    private static $sessionLastPostedUsername = "Model::LastPostedUsername";

    private $dal;

    public function __construct() {
        $this->dal = new Dal("users.txt");
    }

    public function getAndUnsetFeedbackMessage() {
        $feedbackMessage = "";
        if (isset($_SESSION[self::$sessionFeedbackMessageKey])) {
            $feedbackMessage = $_SESSION[self::$sessionFeedbackMessageKey];
            unset($_SESSION[self::$sessionFeedbackMessageKey]);
        }
        return $feedbackMessage;
    }

    private function setFeedbackMessage($message) {
        $_SESSION[self::$sessionFeedbackMessageKey] = $message;
    }

    private function appendToFeedbackMessage($message) {
        if (isset($_SESSION[self::$sessionFeedbackMessageKey]))
            $_SESSION[self::$sessionFeedbackMessageKey] .= $message;
        else
            $this->setFeedbackMessage($message);
    }

	private function storeSessionInfo($username) {
        $_SESSION[self::$sessionUsernameKey] = $username;
        $_SESSION[self::$sessionUserAgentKey] = $_SERVER['HTTP_USER_AGENT'];
		$_SESSION[self::$sessionUserIPKey] = $_SERVER['REMOTE_ADDR'];
	}

	private function getSessionUserAgent() {
		return (isset($_SESSION[self::$sessionUserAgentKey]) ? $_SESSION[self::$sessionUserAgentKey] : false);
	}

	private function getSessionUserIP() {
		return (isset($_SESSION[self::$sessionUserIPKey]) ? $_SESSION[self::$sessionUserIPKey] : false);
	}

    public function getSessionUsername() {
        return (isset($_SESSION[self::$sessionUsernameKey]) ? $_SESSION[self::$sessionUsernameKey] : false);
    }

	public function isLoggedIn() {
		return (isset($_SESSION[self::$sessionUsernameKey]) &&
            $this->getSessionUserAgent() === $_SERVER['HTTP_USER_AGENT'] &&
            $this->getSessionUserIP() === $_SERVER['REMOTE_ADDR']);
        //would actually prefer a simple variable $isLoggedIn true/false (which is checked upon get/post
        //but keeping the main logic from the code I was continuing with from lab2.
	}

	public function logout() {
		if (isset($_SESSION[self::$sessionUsernameKey]))
			unset($_SESSION[self::$sessionUsernameKey]);

        $this->setFeedbackMessage("Du har nu loggat ut");
        unset($_SESSION[self::$sessionLastPostedUsername]);
    }

    public function getLastPostedUsername() {
        return (isset($_SESSION[self::$sessionLastPostedUsername]) ? $_SESSION[self::$sessionLastPostedUsername] : "");
    }

    private function setLastPostedUsername($lastPostedUsername) {
        $_SESSION[self::$sessionLastPostedUsername] = $lastPostedUsername;
    }

	public function login($username, $password, $fromCookie, $rememberMe = false)
	{
        //Remove surrounding whitespace
        $username = trim($username);

        $this->setLastPostedUsername($username);

		if (!$username) {
            $this->setFeedbackMessage("Användarnamn saknas");
        } else if (!$password) {
            $this->setFeedbackMessage("Lösenord saknas");
        } else if ($this->validateUser($username, $password, $fromCookie))
		{
			//Store session info = login user
            $this->storeSessionInfo($username);

            $this->setFeedbackMessage("Inloggning lyckades");

			if ($fromCookie) {
                $this->appendToFeedbackMessage(" via cookies");
            } else if ($rememberMe) {

                $cookiePassword = $this->encryptCookiePassword($password);
                //cookie expiration: 30 days (30*24*60*60)
                $cookieExpirationTime = time() + 2592000;

                $this->storeCookieInfo($cookieExpirationTime, $cookiePassword);

                $this->appendToFeedbackMessage(" och vi kommer ihåg dig nästa gång");
            }
		} else {
			if ($fromCookie)
                $this->setFeedbackMessage("Felaktig information i cookie");
			else
                $this->setFeedbackMessage("Fel användarnamn och/eller lösenord!");
		}
	}

    /**
     * Checks if the username and password exists and are correct.
     * @param string $username (username from post or cookie)
     * @param string $password (password from post or hashed password from cookie)
     * @param bool $fromCookie (true if login details were passed from cookie, false if login form was used)
     * @return bool (true if validate was successful, otherwise false)
     */
    private function validateUser($username, $password, $fromCookie) {
        $isValidated = false;

        if ($fromCookie) {
            if ($this->dal->getUserCookieExpiration($username) > time()
                && $this->dal->getUserCookiePassword($username) == $password)
                $isValidated = true;
        } else if ($this->dal->getUserPassword($username) == $this->encryptPassword($password, $username)) //$username as salt
            $isValidated = true;

		return $isValidated;
	}

    public function encryptCookiePassword($password) {
        $salt = $_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'];
        return md5($salt.$password);
    }
    private function encryptPassword($password, $salt) {
        return md5($salt.$password);
    }

    public function getCookieExpirationTime() {
        return $this->dal->getUserCookieExpiration($this->getSessionUsername());
    }

    private function storeCookieInfo($cookieExpirationTime, $cookiePassword) {
        $this->dal->setCookieInfo($this->getSessionUsername(), $cookieExpirationTime, $cookiePassword);
    }

    public function getCookiePassword() {
        return $this->dal->getUserCookiePassword($this->getSessionUsername());
    }

    public function registerNewUser($username, $password, $repeatedPassword) {
        $username = trim($username);
        $password = trim($password);
        $repeatedPassword = trim($repeatedPassword);

        $this->setLastPostedUsername($username);

        $isSuccess = false;

        if (mb_strlen($username) < 3) {
            $this->setFeedbackMessage("Användarnamnet har för få tecken. Minst 3 tecken");
        }

        if (mb_strlen($password) < 6) {
            if (isset($_SESSION[self::$sessionFeedbackMessageKey]))
                $this->appendToFeedbackMessage("<br />");
            $this->appendToFeedbackMessage("Lösenordet har för få tecken. Minst 6 tecken");
        }

        if (!isset($_SESSION[self::$sessionFeedbackMessageKey]) && $password != $repeatedPassword) {
            $this->setFeedbackMessage("Lösenorden matchar inte.");
        }

        if (!isset($_SESSION[self::$sessionFeedbackMessageKey])) {
            try {
                $this->dal->addUser($username, $this->encryptPassword($password, $username));
                $this->setFeedbackMessage("Registrering av ny användare lyckades");
                $isSuccess = true;
            } catch (\Dal\IllegalUsernameException $e) {
                $this->setFeedbackMessage("Användarnamnet innehåller otillåtna tecken");
                $this->setLastPostedUsername($this->dal->cleanUsername($username));
            } catch (\Dal\AlreadyExistsException $e) {
                $this->setFeedbackMessage("Användarnamnet är redan upptaget");
            } catch (\Exception $e) {
                $this->setFeedbackMessage("Ett oväntat fel inträffade");
            }
        }

        return $isSuccess;
    }
}