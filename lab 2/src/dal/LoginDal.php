<?php

namespace Dal;

class IllegalUsernameException extends \Exception{}
class AlreadyExistsException extends \Exception{}

class LoginDal {
    private $filePath;
    const ALLOWED_USERNAME_CHARACTER = '[a-zA-Z0-9_\-ÅÄÖåäö]';

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

        return (isset($userData[$username]) ? $userData[$username]['password'] : false);
    }

    public function addUser($username, $password) {

        if (!preg_match('/^'. self::ALLOWED_USERNAME_CHARACTER .'+$/', $username))
            throw new IllegalUsernameException("Invalid username. Only a-z, A-Z, åäöÅÄÖ, 0-9, - and _ are allowed characters.");

        $userData = $this->getUserData();

        if (!array_key_exists($username, $userData)) {
            $userData[$username]['password'] = $password;
            $this->setUserData($userData);
        } else {
            throw new AlreadyExistsException("A user with that name already exist.");
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
        $userData = $this->getUserData();
        if (isset($userData[$username]) && isset($userData[$username]['cookieExpiration'])) {
            return $userData[$username]['cookieExpiration'];
        } else {
            return false;
        }
    }

    public function getUserCookiePassword($username) {
        $userData = $this->getUserData();
        if (isset($userData[$username]) && isset($userData[$username]['cookiePassword'])) {
            return $userData[$username]['cookiePassword'];
        } else {
            return false;
        }
    }

    /**
     * Cleans a username from invalid characters
     * @param $username
     * @return mixed the username cleaned from invalid characters
     */
    public function cleanUsername($username) {
        $username = strip_tags($username);
        $disallowedUserCharacter = preg_replace('/\[/', '[^', self::ALLOWED_USERNAME_CHARACTER);

        return preg_replace('/' . $disallowedUserCharacter.'/', '', $username);
    }
}