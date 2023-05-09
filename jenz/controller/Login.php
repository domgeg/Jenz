<?php
namespace controller;

//require_once ("../view/loginView.html");
require_once ("../model/Api.php");
//session_start();
class Login
{
	public function __construct() {

		$this->getData();
	}

	public function getData()
	{
		if(isset($_SESSION['access_token']))
		{
			session_destroy();
		}
		if(isset($_POST['submit']))
		{
			$api = new \Api();
			$api->getToken($_POST['email'], $_POST['password']);

			if(isset($_SESSION['access_token'])) header("Location: Index.php");
		}
	}
}

new Login();
echo file_get_contents("../view/loginView.html");