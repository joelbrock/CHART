<?php
require("mysql_connect.php");
$clientID = $_REQUEST['clientID'];
$staffID = $_REQUEST['staffID'];
if(!empty($clientID))	{
	$c = mysql_query("SELECT * FROM clients WHERE id='".$clientID."'") or die(mysql_error());
	if(mysql_num_rows($c)>0)$client=mysql_fetch_array($c);
	
	$y = mysql_query("SELECT SUM(Hours) as hoursYTD FROM journal WHERE ClientID = $clientID AND YEAR(Date) = YEAR(CURDATE()) GROUP BY ClientID") or die(mysql_error());
	if(mysql_num_rows($y)>0)$hoursYTD=mysql_fetch_array($y);
	
	$q = mysql_query("SELECT SUM(Hours) as hoursQ FROM journal WHERE ClientID = $clientID AND QUARTER(Date) = QUARTER(CURDATE()) GROUP BY ClientID") or die(mysql_error());
	if(mysql_num_rows($q)>0)$hoursQ=mysql_fetch_array($q);
}

if (($_POST['submit']) || ($_POST['addnew'])) {
	if(!empty($_REQUEST['code'])){$cols[]='code';$vals[]=mysql_escape_string($_REQUEST['code']);}
	if(!empty($_REQUEST['name'])){$cols[]='name';$vals[]=mysql_escape_string($_REQUEST['name']);}
	$cols[]='total_hours';$vals[]=mysql_escape_string($_REQUEST['total_hours']);
	$cols[]='address';$vals[]=mysql_escape_string($_REQUEST['address']);
	$cols[]='city';$vals[]=mysql_escape_string($_REQUEST['city']);
	$cols[]='state';$vals[]=mysql_escape_string($_REQUEST['state']);
	$cols[]='zip';$vals[]=mysql_escape_string($_REQUEST['zip']);
	$cols[]='url';$vals[]=mysql_escape_string($_REQUEST['url']);
	$cols[]='contact_details';$vals[]=mysql_escape_string($_REQUEST['contact_details']);
	$cols[]='gm_name';$vals[]=mysql_escape_string($_REQUEST['gm_name']);
	$cols[]='gm_contact';$vals[]=mysql_escape_string($_REQUEST['gm_contact']);
	$cols[]='gm_email';$vals[]=mysql_escape_string($_REQUEST['gm_email']);
	$cols[]='chair_name';$vals[]=mysql_escape_string($_REQUEST['chair_name']);
	$cols[]='chair_contact';$vals[]=mysql_escape_string($_REQUEST['chair_contact']);
	$cols[]='board_name';$vals[]=mysql_escape_string($_REQUEST['board_name']);
	$cols[]='board_contact';$vals[]=mysql_escape_string($_REQUEST['board_contact']);
	$cols[]='chair_email';$vals[]=mysql_escape_string($_REQUEST['chair_email']);
	$cols[]='RetreatDate';$vals[]=mysql_escape_string($_REQUEST['RetreatDate']);
	$cols[]='ExpireDate';$vals[]=mysql_escape_string($_REQUEST['ExpireDate']);
	$cols[]='RetreatDesc';$vals[]=mysql_escape_string($_REQUEST['RetreatDesc']);
	$cols[]='UsingPG';$vals[]=mysql_escape_string($_REQUEST['UsingPG']);
	$cols[]='Expansion';$vals[]=mysql_escape_string($_REQUEST['Expansion']);
	$cols[]='NewGM';$vals[]=mysql_escape_string($_REQUEST['NewGM']);
	$cols[]='Retain';$vals[]=mysql_escape_string($_REQUEST['Retain']);
	$cols[]='board_email';$vals[]=mysql_escape_string($_REQUEST['board_email']);
	if ($_POST['Expansion'] == 0) { $Expansion = 0; } else { $Expansion = 1; }
	$cols[]='Expansion';$vals[]="$Expansion";
	if ($_POST['NewGM'] == 0) { $NewGM = 0; } else { $NewGM = 1; }
	$cols[]='NewGM';$vals[]="$NewGM";
	if ($_POST['BalancedHrsUse'] == 0) { $BalancedHrsUse = 0; } else { $BalancedHrsUse = 1; }
	$cols[]='BalancedHrsUse';$vals[]="$BalancedHrsUse";
	if ($_POST['Retain'] == 0) { $Retain = 0; } else { $Retain = 1; }
	$cols[]='Retain';$vals[]="$Retain";
	if(!empty($clientID)){
		$upd=db_send($cols,$vals,'clients','update',"id='$clientID'",1);
	} else {
		$ins=db_send($cols,$vals,'clients','insert');
		$clientID=mysql_insert_id();
	}
	if($_REQUEST['RetreatDate']!=$client['RetreatDate'] && $client['RetreatDate']>'0000-00-00'){//repos
		$cols_rr[]='RetreatDate';$vals_rr[]=$client['RetreatDate'];
		$cols_rr[]='clientID';$vals_rr[]=$clientID;
		$ins=db_send($cols_rr,$vals_rr,'clients_retreat_repos','insert');
	}
		header('Location: client_profile.php?clientID='.$clientID);
} else{ ?>
<html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<!-- <link rel="stylesheet" href="css/style.css" type="text/css" charset="utf-8"></link> -->
<!-- <link rel="stylesheet" href="css/tablesort.css" type="text/css" charset="utf-8"> -->
<!-- <script src="js/jquery.tablesorter.js" type="text/javascript" charset="utf-8"></script> -->
<!-- <link rel="stylesheet" href="css/jquery-ui.css" type="text/css" charset="utf-8"></link> -->
<!-- <script src="http://ajax.microsoft.com/ajax/jquery/jquery-1.4.2.min.js" type="text/javascript"></script> -->
<!-- <script src="js/jquery.js" type="text/javascript" charset="utf-8"></script> -->
<!-- <script src="js/jquery-ui.js" type="text/javascript" charset="utf-8"></script> -->


</head>
<body>

<?php

include_once('topbar.php');
// include ('main_select.php');


echo "<br /><br />";
	
echo "<h2>" . $client['name'] . " &mdash; Client Profile</h2>";

?>
<div id="wrapper">

<div id="client_profile">


<!-- <div id="mini_dash">
Hours used YTD: <?=number_format($hoursYTD['hoursYTD'],2)?><br />
Hours used Q<?=$thisQ?>: <?=number_format($hoursQ['hoursQ'],2)?>
</div> -->
<div style="margin-right:60px;">
	<?php mini_dash($clientID,'right'); ?>
</div>
<form method='POST' action='<?=$_SERVER['PHP_SELF']?>'>

<div id="left_col">
	<div class="form_field">
		<h3>Client Name</h3>
		<input type="text" name="name" value="<?=$client['name']?>" size="60">
	</div>
	<div class="form_field">
		<h3>Address</h3>
		<input type="text" name="address" value="<?=@$client['address']?>" size="60">
	</div>
	<div class="form_field">
		<table border="0" width="">
			<tr>
				<td><h3>City</h3>
				<input type="text" name="city" value="<?=@$client['city']?>" size="37"></td>
				<td><h3>State</h3>
				<input type="text" name="state" value="<?=@$client['state']?>" size="2"></td>
				<td><h3>Zip</h3>
				<input type="text" name="zip" value="<?=@$client['zip']?>" size="10"></td>
			</tr>
		</table>
		

	</div>
	<div class="form_field">
		<h3>Website</h3>
		<input type="text" name="url" value="<?=@$client['url']?>" size="40">
	</div>
	<div class="form_field">
		<h3>GM Name</h3>
		<input type="text" name="gm_name" value="<?=@$client['gm_name']?>" size="40">
	</div>
	<div class="form_field">
		<table border="0" width="">
			<tr>
				<td><h3>GM Phone</h3>
				<input type="text" name="gm_contact" value="<?=@$client['gm_contact']?>" size="16"></td>
				<td><h3>GM Email</h3>
				<input type="text" name="gm_email" value="<?=@$client['gm_email']?>" size="40">
				</td>
			</tr>
		</table>
	</div>
	<div class="form_field">
		<h3>Chair Name</h3>
		<input type="text" name="chair_name" value="<?=@$client['chair_name']?>" size="40">
	</div>
	<div class="form_field">
		<table border="0" width="">
			<tr>
				<td><h3>Chair Phone</h3>
				<input type="text" name="chair_contact" value="<?=@$client['chair_contact']?>" size="16"></td>
				<td><h3>Chair Email</h3>
				<input type="text" name="chair_email" value="<?=@$client['chair_email']?>" size="40"></td>
			</tr>
		</table>
	</div>
		<div class="form_field">
		<h3>Board Name</h3>
		<input type="text" name="board_name" value="<?=@$client['board_name']?>" size="40">
	</div>
	<div class="form_field">
		<table border="0" width="">
			<tr>
				<td><h3>Board Phone</h3>
				<input type="text" name="board_contact" value="<?=@$client['board_contact']?>" size="16"></td>
				<td><h3>Board Email</h3>
				<input type="text" name="board_email" value="<?=@$client['board_email']?>" size="40"></td>
			</tr>
		</table>
	</div>
</div>

 <!-- RIGHT COLUMN -->
<div id="right_col">
	<div class="form_field">
		<h3>Client Code (3 letters)</h3>
		<input type="text" name="code" value="<?=@$client['code']?>" size="3">
	</div>
	<div class="form_field">
		<h3><?= date('Y'); ?> Annual Support Hours (Total)</h3>
		<input type="text" name="total_hours" value="<?=@$client['total_hours']?>">
	</div>
	<div class="form_field">
		<h3>Contract Expires (yyyy-mm-dd)</h3>
		<input type="text" name="ExpireDate" class="datepicker" value="<?=@$client['ExpireDate']?>">
	</div>
	<div class="form_field">
		<h3>Retreat Date (yyyy-mm-dd)</h3>
		<input type="text" name="RetreatDate" class="datepicker" value="<?=@$client['RetreatDate']?>">
	</div>
	<div class="form_field">
		<h3>Retreat Description</h3>
		<textarea name="RetreatDesc" cols=40 rows=4><?=@$client['RetreatDesc']?></textarea>
	</div>
	<br /><br />
		<div class="form_field">
		<h3><input type="checkbox" name="UsingPG" id="UsingPG" value="1" <?=(@$client['UsingPG']==1?'checked="checked"':'')?>>  <label for="UsingPG">Using PG?</label></h3>
	</div>
		<div class="form_field">
		<h3><input type="checkbox" name="Expansion" id="Expansion" value="1" <?=(@$client['Expansion']==1?'checked="checked"':'')?>>  <label for="Expansion">Expansion</label></h3>
	</div>
		<div class="form_field">
		<h3><input type="checkbox" name="NewGM" id="NewGM" value="1" <?=(@$client['NewGM']==1?'checked="checked"':'')?>>  <label for="NewGM">New GM</label></h3>
	</div>
		<div class="form_field">
		<h3><input type="checkbox" name="Retain" id="Retain" value="1" <?=(@$client['Retain']==1?'checked="checked"':'')?>>  <label for="Retain">Retain in <?=(date('Y') + 1)?>?</label></h3>
	</div>
</div>

<div id="other">
<br />
<div class="form_field">
<input type="hidden" name="clientID" value="<?=@$clientID?>" />
<input type="submit" name="submit" value="Save >>">
</div>
</div>
</div>
</div>
</form>

<script>
$("#unfold").click(function () {
	$("#last").slideToggle("slow");
});
</script>
</body>
</html>
<? }?>