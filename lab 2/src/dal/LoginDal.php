<?php

namespace Dal;

class IllegalUsernameException extends \Exception{}
class AlreadyExistException extends \Exception{}

class LoginDal {
    private $filePath;

    public function __construct($filename) {
        $directory = "data";

        try {
            if (!file_exists($directory))
                mkdir($directory);
        } catch (\Exception $e) {
            throw new \Exception("Couldn't create directory");
        }

        if (!preg_match('/^(?:[a-zA-Z0-9_\-.])+(?!\.)$/', $filename))
            throw new \Exception("Invalid filename. Only use a-z, A-Z, 0-9, -, _ and . not ending with a .");

        try {
            $filePath = $directory."/".$filename;
            if (!file_exists($filePath)) {
                fopen($directory."/".$filename, "w"); //creates an empty file if it doesn't exist.
            }
            $this->filePath = $filePath;
        } catch (\Exception $e) {
            throw new \Exception("Failed to create the file for storing data.");
        }
    }

    public function getUserPassword($username) {
        $userData = $this->getUserData();

        if (isset($userData[$username])) {
            return $userData[$username]['password'];
        }
    }

    public function addUser($username, $password) {

        if (!preg_match('/^(?:[a-zA-Z0-9_\-ÅÄÖåäö])+$/', $username))
            throw new IllegalUsernameException("Invalid username. Only a-z, A-Z, åäöÅÄÖ, 0-9, - and _ are allowed characters.");

        $userData = $this->getUserData();

        if (!array_key_exists($username, $userData)) {
            $userData[$username]['password'] = $password;
            $this->setUserData($userData);
        } else {
            throw new AlreadyExistException("A user with that name already exist.");
        }
    }

    private function getUserData() {
        return json_decode(file_get_contents($this->filePath), true);
    }

    private function setUserData($data) {
        file_put_contents($this->filePath, json_encode($data));
    }
    public function setCookieInfo($username, $cookieExpiration, $cookiePassword) {
        $userData = $this->getUserData();
        if (isset($userData[$username])) {
            $userData[$username]['cookieExpiration'] = $cookieExpiration;
            $userData[$username]['cookiePassword'] = $cookiePassword;
        }
        $this->setUserData($userData);
    }

    public function getUserCookieExpiration($username) {
        //TODO: hämta från fil
        $userData = $this->getUserData();
        if (isset($userData[$username])) {
            return $userData[$username]['cookieExpiration'];
        }
    }

    public function getUserCookiePassword($username) {
        //TODO: hämta från fil
        $userData = $this->getUserData();
        if (isset($userData[$username])) {
            return $userData[$username]['cookiePassword'];
        }
    }
}