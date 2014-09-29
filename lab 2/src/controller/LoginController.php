<?php

namespace Controller;

require_once("src/view/LoginView.php");
require_once("src/model/LoginModel.php");

use \Model\LoginModel as Model;
use \View\LoginView as View;

class LoginController {
	private $view;
	private $model;

	public function __construct() {
		$this->model = new Model();
		$this->view = new View($this->model);
	}

	public function start()
	{
   		if ($this->model->isLoggedIn()) {
			//Checks if "doLogout" is sent in the post, if it is and the user is actually logged in, log the user out...
			if ($this->view->didUserRequestLogout()) {
				$this->model->logout();
				$this->view->unsetUserCookies();
			}
		} else {
			//...then we check if the user requested to login....
			if ($this->view->didUserRequestLogin()) {
				$this->model->login($this->view->getUsernameInput(),
                    $this->view->getPasswordInput(),
                    false,
                    $this->view->getRememberMeInput());

                if ($this->model->isLoggedIn()) {
                    if ($this->view->getRememberMeInput())
                    {
                        $this->view->setUserCookies(
                            $this->model->getSessionUsername(),
                            $this->model->getCookiePassword(),
                            $this->model->getCookieExpirationTime());
                    }
                }
			} else if ($this->view->didUserRequestRegisterNewUser()) {
                if ($this->model->registerNewUser($this->view->getUsernameInput(),
                    $this->view->getPasswordInput(),
                    $this->view->getRepeatedPasswordInput())) {

                    //redirect to login view
                    header('location: ' . "?login");
                    die;
                }
            } else if ($this->view->areCookiesSet()) {
                //...if he didn't press the login button but he has saved credentials, log him in using cookies.
				$this->model->login($this->view->getCookieUsername(), $this->view->getCookiePassword(), true);
			}
		}

        if (!$this->model->isLoggedIn()) {
            $this->view->unsetUserCookies();
        }

        $this->view->renderHtml($this->model->isLoggedIn(), $this->model->getAndUnsetFeedbackMessage());
	}
}