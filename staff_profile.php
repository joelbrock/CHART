<?php
require("mysql_connect.php");
$staffID = $_REQUEST['staffID'];
$staffID = $_REQUEST['staffID'];
if(!empty($staffID))	{
	$s = mysqli_query($dbc, "SELECT * FROM staff WHERE id='".$staffID."'") or die(mysql_error());
	if(mysqli_num_rows($s)>0)$staff=mysqli_fetch_array($s);
}

if (($_POST['submit'])) {
	$cols[]='firstname';$vals[]=mysqli_real_escape_string($dbc, $_REQUEST['firstname']);
	$cols[]='lastname';$vals[]=mysqli_real_escape_string($dbc, $_REQUEST['lastname']);
	$cols[]='email';$vals[]=mysqli_real_escape_string($dbc, $_REQUEST['email']);
	$cols[]='title';$vals[]=mysqli_real_escape_string($dbc, $_REQUEST['title']);
	if(!empty($_REQUEST['staff_password'])){$cols[]='password';$vals[]=sha1($_REQUEST['staff_password']);}
	$cols[]='phone';$vals[]=mysqli_real_escape_string($dbc, $_REQUEST['phone']);
	$cols[]='admin';$vals[]=mysqli_real_escape_string($dbc, $_REQUEST['admin']);
	$cols[]='address';$vals[]=mysqli_real_escape_string($dbc, $_REQUEST['address']);
	$cols[]='city';$vals[]=mysqli_real_escape_string($dbc, $_REQUEST['city']);
	$cols[]='state';$vals[]=mysqli_real_escape_string($dbc, $_REQUEST['state']);
	$cols[]='zip';$vals[]=mysqli_real_escape_string($dbc, $_REQUEST['zip']);
	if(!empty($staffID)){
		$upd=db_send($cols,$vals,'staff','update',"id='$staffID'",1);//die();
	} else {
		$ins=db_send($cols,$vals,'staff','insert');
		$staffID=mysql_insert_id();
	}
	if($staffID==$userinfo['id'] && !empty($_REQUEST['staff_password']))$_SESSION['password']=sha1($_REQUEST['staff_password']);
		header('Location: staff_profile.php?staffID='.$staffID);
} else{ ?>
<html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<link rel="stylesheet" href="css/style.css" type="text/css" charset="utf-8">
<!--<link rel="stylesheet" href="css/tablesort.css" type="text/css" charset="utf-8">-->
<!-- <script src="http://ajax.microsoft.com/ajax/jquery/jquery-1.4.2.min.js" type="text/javascript"></script> -->
<script src="js/jquery.js" type="text/javascript" charset="utf-8"></script>
<script src="js/jquery-ui.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>

<?php

include_once('topbar.php');

echo "<br /><br />";
	
echo "<h2>" . $staff['firstname'] . " " . $staff['lastname'] . " &mdash; Staff Profile</h2>";

?>
<div id="wrapper">
<div id="client_profile">
<form method='POST' action='<?php echo $_SERVER['PHP_SELF']?>' autocomplete="off">
<div id="left_col">
	<div class="form_field">
		<h3>Staff Name</h3>
		<input type="text" name="firstname" value="<?php echo $staff['firstname']?>" style="margin-right:10px">
		<input type="text" name="lastname" value="<?php echo @$staff['lastname']?>">
	</div>
	<div class="form_field">
		<h3>Title</h3>
		<input type="text" name="title" value="<?php echo @$staff['title']?>" size=30>
	</div>
	<div class="form_field">
		<h3>Email</h3>
		<input type="text" name="email" value="<?php echo @$staff['email']?>" size=30>&nbsp;<span class="email_link"><a href="mailto:<?php echo @$staff['email']?>">open new email</a></span>
	</div>
	<div class="form_field">
		<h3>Phone</h3>
		<input type="text" name="phone" value="<?php echo @$staff['phone']?>">
	</div>
	<br /><br />
	<? if($admin && $userinfo['id']!=$staffID){?>
		<div class="form_field">
			<h3>Admin?</h3>
			<select name="admin">
				<option value="0">no</option>
				<option value="1" <?php echo ($staff['admin']==1?"selected='selected'":'')?>>yes</option>
			</select>
		</div>
	<? }?>
</div>  <!-- end left col -->
<div id="right_col">
	<div class="form_field">
		<h3>Address</h3>
		<input type="text" name="address" value="<?php echo @$staff['address']?>" size="60">
	</div>
	<div class="form_field">
		<table border="0" width="">
			<tr>
				<td><h3>City</h3>
				<input type="text" name="city" value="<?php echo @$staff['city']?>" size="37"></td>
				<td><h3>State</h3>
				<input type="text" name="state" value="<?php echo @$staff['state']?>" size="2"></td>
				<td><h3>Zip</h3>
				<input type="text" name="zip" value="<?php echo @$staff['zip']?>" size="10"></td>
			</tr>
		</table>
	</div>
	<br><br>
	<div class="form_field">
		<h3><?php echo (!empty($staff['password'])?'Change ':'')?>Password</h3>
		<input type="password" name="staff_password" value="">
	</div>

</div> <!-- end right col -->
<div id="other">
<?php
	$get_s=mysqli_query($dbc, "SELECT * FROM staff WHERE id=$staffID");
		while($s=mysqli_fetch_assoc($get_s))
			$s_meta[$s['id']]=$s;
	$get_c=mysqli_query($dbc, "SELECT * FROM clients ORDER BY name ASC");
		while($c=mysqli_fetch_assoc($get_c))
			$c_meta[$c['id']]=$c;
	$get_assign=mysqli_query($dbc, "SELECT * FROM staff_clients WHERE staffID=$staffID");
		while($sc=mysqli_fetch_assoc($get_assign))
			$c_assign[$sc['clientID']][]=$sc['staffID'];

	echo "<h2>".(!empty($c_assign)?"Current Assignments":"No Assigments");
	if ($admin) echo "<span style='font-weight:normal; font-size:50%;margin-left:8px;'><a href='client_assign.php'>Edit Assignments</a></span>";
	echo "</h2>";
?>
	<ul>
		
		<?php
			$i=0;
			foreach ($c_meta as $c=>$client)
			if(isset($c_assign[$c])){ $sc=$c_assign[$c];
				echo "<li>".$c_meta[$c]['name'].(!empty($c_meta[$c]['code'])?" - ".$c_meta[$c]['code']:'')."</li>";
				// foreach($sc as $k=>$s){
				// 	echo "<div id='s-{$s}_c-{$c}'>".$s_meta[$s]['firstname'].' '.$s_meta[$s]['lastname']." <sup><a href='#' class='remove' onclick='return removeAssignment({$c},{$s})'>x</a></sup></div>";
				// }
				echo "<br>";
			$i++;
			}
		?>
		
	</ul>
	
	
	<div class="form_field" align="center">
		<input type="hidden" name="staffID" value="<?php echo @$staffID?>" />
		<input type="submit" name="submit" value="Save >>">
	</div>
</div>
<br /><br />

</form>
<?php

echo "</div></div>";  // end staff_profile div
// echo "<input type=\"submit\" name=\"submit\" value=\"Add\">\n";
// debug_p($staff,'all the data coming in');
?>
</body>
</html>
<? }?>