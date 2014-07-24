<?php
/**
 * MyBB 1.8
 * Copyright 2014 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybb.com
 * License: http://www.mybb.com/about/license
 *
 */

class api {

	//the action we want mybb to perform
	protected $act;
	
	//do we have errors?
	protected $errors = array();
	
	//did we have success?
	protected $success;
	
	//do we have a message to display?
	protected $result;
	
	public function run()
	{
		$act = $_GET['act'];
		
		//define valid actions
		$valid_actions = array('register_user');

		//see if we have a valid action defined
		if (!in_array($act, $valid_actions))
		{
			//we do not have a valid action defined, prevent this script from running
			$this->errors[] = array('unauthorized' => 'You did not provide a valid action.');
			$this->success = false;
			echo $this->output_result();
			exit();
			
		}
		
		$this->act = $act;
				
		return $this->$act();
		
		
	}
	
	public function output_result()
	{
		//see if we have an error 
		if (!$this->success)
		{
			//an error has occurred
			return json_encode(array('success' => $this->success, 'errors' => $this->errors));
		}
		else
		{	
			//an error has not occurred
			return json_encode(array('success' => $this->success, 'result' => $this->result));
		}
	}
	
	public function register_user()
	{
		$username = $_GET['username'];
		$password = $_GET['password'];
		$email = $_GET['email'];
	
		//make sure we have a username, password, and email
		if (!isset($username) || !isset($password) || !isset($email))
		{
			//we do not have a username, password, or email
			$this->errors[] = array('unauthorized' => 'Either a username, password, or email was not provided.');
			$this->success = false;
			echo $this->output_result();
			exit();
		}
		else
		{
			require_once  MYBB_ROOT."inc/datahandlers/user.php";
			$userhandler = new UserDataHandler("insert");
			$user = array(
				"username" => $username,
				"password" => $password,
				"password2" => $password,
				"email" => $email,
				"email2" => $email,    
				"usergroup" => 2,
			);
			$userhandler->set_data($user);
			if($userhandler->validate_user()) {
				$newuser = $userhandler->insert_user();
				
				//the user was successfully registered
				$this->success = true;
				$this->result = "The user has been registered successfully.";
				
				//output results, and exit
				echo $this->output_result();
				exit();
			} 
		}
	}
	
	
	
}


?>
