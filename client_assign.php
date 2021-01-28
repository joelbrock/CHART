<?php
require("mysql_connect.php");

//who is assigned already?
	$get_s=mysqli_query($dbc, ("SELECT * FROM staff ORDER BY lastname ASC,firstname ASC");
		while($s=mysqli_fetch_assoc($get_s))
			$s_meta[$s['id']]=$s;
	$get_c=mysqli_query($dbc, ("SELECT * FROM clients ORDER BY name ASC");
		while($c=mysqli_fetch_assoc($get_c))
			$c_meta[$c['id']]=$c;
	$get_assign=mysqli_query($dbc, ("SELECT * FROM staff_clients");
		while($sc=mysqli_fetch_assoc($get_assign))
			$c_assign[$sc['clientID']][]=$sc['staffID'];
	$get_s_assign=mysqli_query($dbc, ("SELECT * FROM staff_clients ORDER BY clientID");
		while($cs=mysqli_fetch_assoc($get_s_assign))
			$s_assign[$cs['staffID']][]=$cs['clientID'];
			// print_r($c_assign);
//			print_r($_POST);

if ($_POST['submit']) {
	if($_POST['remove']==1){
		$del=mysqli_query($dbc, ("DELETE FROM staff_clients WHERE staffID='{$_POST['staffID']}' AND clientID='{$_POST['clientID']}'");
		if($del) $msg='success'; else $msg='error';
		die($msg);
	} else {
		//is this already assigned?
		if(!isset($c_assign[$_POST['codemenu']]) || !in_array($_POST['staffmenu'],$c_assign[$_POST['codemenu']])){
			$ins=mysqli_query($dbc, ("INSERT INTO staff_clients (`staffID`,`clientID`) VALUES ('".$_POST['staffmenu']."','".$_POST['codemenu']."')");
			$c_assign[$_POST['codemenu']][]=$_POST['staffmenu'];
		}
		if (in_array($_POST['staffmenu'],$c_assign[$_POST['codemenu']]) || $ins) {		
			echo "<font size=2 color=green>Successfully added: <b>";
			echo $c_meta[$_POST['codemenu']]['name'] . "</b> to <b>";	
			echo $s_meta[$_POST['staffmenu']]['firstname'].' '.$s_meta[$_POST['staffmenu']]['lastname']. "</b>'s client field in staff_clients</font><br>";
			// echo $query;
		} else {
			echo "<font color=red>ERROR!</font>";
			print mysql_error();
		}
	}
}

?>
<html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<link rel="stylesheet" href="css/style.css" type="text/css" charset="utf-8">
<!--<link rel="stylesheet" href="css/tablesort.css" type="text/css" charset="utf-8">-->
<!-- <script src="http://ajax.microsoft.com/ajax/jquery/jquery-1.4.2.min.js" type="text/javascript"></script> -->
<script src="js/jquery.js" type="text/javascript" charset="utf-8"></script>
<script src="js/jquery-ui.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
function removeAssignment(c,s){
	var vars='submit=1&remove=1&clientID='+c+'&staffID='+s;
		$.post('client_assign.php', vars, function(response) {
		alert(response);
		if(response.match(/^success/)) $('#s-'+s+'_c-'+c).fadeOut();
	});

	return false;
}
</script>
</head>
<body>

<?php
include_once('topbar.php');
?>
<div id="wrapper">
<div id="client_profile">
<?php
// echo "<div class='container'>";
echo "<form method=\"POST\" action=\"client_assign.php\" style='padding:0 100px;'>\n";
echo "<br /><br /><br />";
// -- STAFFMENU START
echo "<h2>Add a Staff Assignment</h2>";
echo "<br />";
echo "<select name=\"staffmenu\">\n<option>Select Staff</option>";
foreach ($s_meta as $sid=>$staff) { 
	$fullname = $staff['firstname'] . " " . $staff['lastname'];
	echo "<option value=\"" . $sid . "\" ";
	if ($sid == $_POST['staffmenu']) { echo "SELECTED"; }
	echo ">" . $fullname;
	echo "</option>\n";
}
echo "</select>\n";
// -- STAFFMENU ENDS


// -- CODEMENU START
echo "<select name=\"codemenu\">\n<option>Select Client</option>";
foreach ($c_meta as $cid=>$code) {
	echo "<option value=\"" . $cid . "\">";
	if ($code['code']) {
		echo $code['code'] . " - ";
	}
	echo $code['name'];
	echo "</option>\n";
}
echo "</select>\n";
echo "<input type=\"submit\" name=\"submit\" value=\"Add\">\n";
// -- CODEMENU ENDS
echo "<br /><br /><br />";

echo "<h2>".(!empty($c_assign)?"Current Assignments":"No Assigments")."</h2>";
echo "<p style='text-align: center;'><a href='#cli'>View by client</a> or <a href='#con'>by consultant</a></p>";
echo "<a name='con'>&nbsp;</a><br /><br /><br /><h1>Assignments by Consultant</h1><br />";
$i=0;
// foreach ($c_meta as $c=>$client)
foreach ($s_meta as $s=>$staff)
if(isset($s_assign[$s])) { 
	$cs=$s_assign[$s];
	echo "<hr /><h3>".$s_meta[$s]['firstname'].' '.$s_meta[$s]['lastname']."</h3>";
	// echo "<div class='client_ct'>".."</div>";
	foreach($cs as $k=>$c){
		echo "<div id='s-{$s}_c-{$c}'>".$c_meta[$c]['name'].(!empty($c_meta[$c]['code'])?" - ".$c_meta[$c]['code']:'')." <sup><a href='#' class='remove' onclick='return removeAssignment({$c},{$s})'>x</a></sup></div>";
	}
	echo "<br>";
	$i++;
}

echo "<a name='cli'>&nbsp;</a><br /><br /><br /><br /><h1>Assignments by Client</h1>";
echo "<p style='text-align: center;'><a href='#cli'>View by client</a> or <a href='#con'>by consultant</a></p><br />";
$i=0;
foreach ($c_meta as $c=>$client)
if(isset($c_assign[$c])) { 
	$sc=$c_assign[$c];
	echo "<h3>".$c_meta[$c]['name'].(!empty($c_meta[$c]['code'])?" - ".$c_meta[$c]['code']:'')."</h3>";
	foreach($sc as $k=>$s){
		echo "<div id='s-{$s}_c-{$c}'>".$s_meta[$s]['firstname'].' '.$s_meta[$s]['lastname']." <sup><a href='#' class='remove' onclick='return removeAssignment({$c},{$s})'>x</a></sup></div>";
	}
	echo "<br>";
	$i++;
}




echo "</form>";

// echo "</div>";

?>
</div></div>
</body>
</html>