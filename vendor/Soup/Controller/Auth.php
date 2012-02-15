<?php

namespace Soup\Controller;

class Auth extends \Soup\Controller {
	
	protected $auth_login_url = '/login';
	protected $auth_public_actions = array();
	
	public function before_filter(){

        if(!$this->app["session"]->read('Auth', null) && !in_array($this->invoked_action, $this->auth_public_actions)) {
        	$this->redirect($this->auth_login_url);
        	return false;
    	}
    	return true;
    }

}