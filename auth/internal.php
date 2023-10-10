<?php
mysqli_report(MYSQLI_REPORT_OFF);
require_once('mysql_connect.php');
$login = false;
// $admin = false;
if(!function_exists('thisQ')) require_once('functions.php');
//GET LOGGED IN USER
if (isset($_SESSION['username'])){
	$result = mysqli_query($dbc, "SELECT a.* FROM staff a WHERE a.email='" . mysqli_real_escape_string($dbc, 
	$_SESSION['username']) . "' AND a.password='" . mysqli_real_escape_string($dbc, $_SESSION['password']) . "' AND a.password IS NOT NULL LIMIT 1") or die(mysqli_error($result));
	if (@mysqli_num_rows($result) != 0) {
		$userinfo = mysqli_fetch_assoc($result);
		$login = true;
		// if($userinfo['admin']==1) $admin=true;
		$admin = ($userinfo['admin']==1) ? true : false;
		$hoursq = "SELECT * FROM journal WHERE ".($admin==true?"":"StaffID='{$userinfo['id']}'")." AND YEAR(Date) = YEAR(curdate()) AND (MONTH(Date) >= (".(3*(thisQ()-1)+1).") AND MONTH(Date)<=(".(3*thisQ())."))";
		$hoursr = mysqli_query($dbc, $hoursq); 
		// echo "thisQ: " . thisQ();
		// echo $hoursq.mysql_error();
		if($hoursr && @mysqli_num_rows($hoursr)>0)$report=true;
	} else {
		session_regenerate_id();
		session_destroy();
		unset($login);
		session_start();
		$_SESSION['wherefrom']=$_SERVER['REQUEST_URI'];
	} 
}
//CHECK PERMISSIONS TO PROCEED
$path = $_SERVER['SCRIPT_NAME'];
$path=explode('/',$path);
$filename=$path[sizeof($path)-1];
if(!$login && $filename!='index.php') header('Location: index.php');
elseif($login==true  && $filename=='index.php') header('Location: dashboard.php');
?>