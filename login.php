<?php
	//Start session
	session_start();
	
	//Array to store validation errors
	$errmsg_arr = array();
	
	//Validation error flag
	$errflag = false;
	
	//Connect to database
	include('connect.php');
	
	
	//Function to sanitize values received from the form. Prevents SQL injection
	function clean($str) {
		$str = @trim($str);
		return $str;
	}
	
	//Sanitize the POST values
	$login = clean($_POST['username'], $conn);
	$password = clean($_POST['password'], $conn);
	
	//Input Validations
	if($login == '') {
		$errmsg_arr[] = 'Username missing';
		$errflag = true;
	}
	if($password == '') {
		$errmsg_arr[] = 'Password missing';
		$errflag = true;
	}
	
	//If there are input validations, redirect back to the login form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header("location: index.php");
		exit();
	}
	
	//Create query
	$qry = $db->prepare("SELECT * FROM user WHERE username = ? AND password = ?");
	$qry->execute([$login, $password]);
	$result = $qry;
	
	//Check whether the query was successful or not
	if($result->rowCount() > 0) {
		//Login Successful
		session_regenerate_id();
		$member = $result->fetch(PDO::FETCH_ASSOC);
		$_SESSION['SESS_MEMBER_ID'] = $member['id'];
		$_SESSION['SESS_FIRST_NAME'] = $member['name'];
		$_SESSION['SESS_LAST_NAME'] = $member['position'];
		//$_SESSION['SESS_PRO_PIC'] = $member['profImage'];
		session_write_close();
		header("location: main/index.php");
		exit();
	}else {
		//Login failed
		header("location: index.php");
		exit();
	}
?>