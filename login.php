<?php 
require_once('./db.php');
if ("LOGIN" == $_POST["log_action"]) {
	// login
	if ($password !== $_POST["pw"]) {
		echo "Password is wrong!";
	} else {
		$_SESSION['login'] = "YES";
		echo "Login";
	}
} else {
	// log out
	if (session_destroy()) {
		echo 'Logout';
	}
}
?>
