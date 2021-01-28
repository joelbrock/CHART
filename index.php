<?php
require("mysql_connect.php");
	$clientID = $_REQUEST['clientID'];
	$staffID = $_REQUEST['staffID'];

	if(!empty($_REQUEST['email'])){
		
	if (isset($_SESSION['username'])){
	$username=$_SESSION['username'];
	} else {
	$username=$_POST['email'];
	}
	$password=sha1($_POST['password']);
	//$password=$_POST['password'];
	$Sql="Select a.* from staff a where a.email='$username' and password='$password' AND password IS NOT NULL LIMIT 1";
	$result=mysqli_query($dbc, $Sql); 
	$row=mysqli_fetch_array($result);
	$userid = $row['id'];
	$auth=mysqli_num_rows($result);
		if($auth==1 || $row['id']==1){
				if(isset($_POST['remember']) && $_COOKIE['remember']!=$_POST['username']){
					setcookie("remember", $_POST['username'], time()+60*60*24*100, "/");
				} elseif(!isset($_POST['remember']) && isset($_COOKIE['remember']) && @$_COOKIE['remember']==$_POST['username']){
					setcookie("remember", '', time()-60*60*24*100, "/");
				}
			  $_SESSION['username'] = $username;
			  $_SESSION['password'] = $password;
			  $_SESSION['userid']    = $userid;
			  $sessionid = session_id();
			  $user_now = time();
		
		if ($_POST['referer']!=''){
		header("Location:".$_POST['referer']);
		} else {
		header("Location: dashboard.php");
		}
		} else 
			$login_error=true;;
	}
?>
<head>
<link rel="stylesheet" href="css/style.css" type="text/css" charset="utf-8"></link>
<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" charset="utf-8"></link>
<script src="js/jquery.js" type="text/javascript" charset="utf-8"></script>
<script src="js/jquery-ui.js" type="text/javascript" charset="utf-8"></script>

<body>
<?php
			
		echo "<form method='POST' action='$PHP_SELF'>\n";
		echo "<table border=0 cellspacing=0 id='login'>";
		echo "<tr><td colspan=2 align='center'><img src='images/CBLDlogo.png' alt='CBLD Logo' width=300 border=0 /></td></tr>";
		// echo "<tr><td colspan=2><span class='cname'>Login</span></td></tr>";
		echo "<tr><td colspan=2>&nbsp;</td></tr>";
		
		if($login_error==true)echo "<tr><td colspan=2 align='center'><span id='alert'>login error... please try again</span></td></tr><tr><td colspan=2>&nbsp;</td></tr>";
		
		echo "<tr><td class='label'>Email: </td><td><input name='email' size=35 /></td></tr>";
		echo "<tr><td class='label'>Password: </td><td><input name='password' type='password' size=35 /></td></tr>";
		echo "<tr><td colspan=2>&nbsp;</td></tr>";
		echo "<tr><td colspan=2 align='right'><input type=\"submit\" name=\"submit\" value=\"Submit\" /></td></tr>";
		echo "</table>";
		echo "</form>";

?>
<script>
$("#unfold").click(function () {
	$("#last").slideToggle("slow");
});
function switchType(type){
	if(type=='quarterly'){
	 $('#row_nts, #row_ntc').hide();
	 $('#row_nq, #row_ni').show(); 
	} else {
	 $('#row_nts, #row_ntc').show();
	 $('#row_nq, #row_ni').hide(); 
	}
}
</script>
</body>