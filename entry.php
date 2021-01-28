<?php
require("mysql_connect.php");
$clientID = $_REQUEST['clientID'];
// $clientID = $_POST['clientID'];
$staffID = ($admin==false || empty($_REQUEST['staffID'])?$userinfo['id']:$_REQUEST['staffID']);
if ($_GET['jid']) {
	$jid = $_GET['jid'];
	$query = "SELECT * FROM journal WHERE id = ".$jid;
	$result = mysqli_query($dbc, $query);
	$fill = mysqli_fetch_assoc($result);
	$clientID = $fill['ClientID'];
}
if (!empty($clientID)){
	$query = "SELECT *, c.id as ClientID, c.total_hours AS totalhours FROM clients c LEFT JOIN journal j ON c.id = j.ClientID WHERE c.id = " . $clientID . " ORDER BY created DESC LIMIT 1";
	// echo "$query";
	$result = mysqli_query($dbc, $query);
	$row=mysqli_fetch_assoc($result); 
	
	if (!$row['ClientID']) {
		empty($clientID); echo "No client found.";
//		header('Location: entry.php');
	} else {
		$hoursq = "SELECT SUM(Hours) FROM journal WHERE ClientID = " . $clientID . " AND YEAR(created) = YEAR(curdate()) AND (MONTH(created) >= (".(3*($thisQ-1)+1).") AND MONTH(created)<=(".(3*$thisQ)."))";
		$hoursr = mysqli_query($dbc, $hoursq);
		$hours = mysqli_fetch_row($hoursr);
		$hoursTotal = $hours['0'];
		if ($hoursTotal >= ($row['q_hours']/3) && $hoursTotal < (2*$row['q_hours']/3)) { $fcolor = '#ff9900'; }
		if ($hoursTotal >= (2*$row['q_hours']/3)) { $fcolor = '#cc0033'; }
	}
}
?>
<head>
<link rel="stylesheet" href="css/style.css" type="text/css" charset="utf-8"></link>
<!--<link rel="stylesheet" href="css/tablesort.css" type="text/css" charset="utf-8">-->
<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" charset="utf-8"></link>
<!-- <script src="http://ajax.microsoft.com/ajax/jquery/jquery-1.4.2.min.js" type="text/javascript"></script> -->
<script src="js/jquery.js" type="text/javascript" charset="utf-8"></script>
<script src="js/jquery-ui.js" type="text/javascript" charset="utf-8"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/1.4.5/numeral.min.js"></script>

<script>

	$(function() {
		$( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
	});

    var c=0;
    var t;
    var timer_is_on= false;

    function timedCount() 
	{
		document.getElementById('timer').value=numeral(c).format('00:00:00');
		c++;
		if (timer_is_on) {
			t= setTimeout(timedCount,1000);
		}
	}

	function doTimer() 
	{
		if (!timer_is_on) {
			timer_is_on=true;
			timedCount();
		} else {
			clearTimeout(t);
			timer_is_on=false;
		}
	}
		
	function popOut() {
	    var myWindow = window.open(document.URL, "CHART Track", "width=1060,height=650");
	}
		
//	$('textarea').autoResize();

</script>

<style>
	#last { 
		display:none; 
	}
</style>
<body>
<?php
include_once('topbar.php');
echo "<br /><br />";
if (($_POST['submit']) || ($_POST['addnew'])) {
	foreach ($_POST AS $key => $value) {
		if(!empty($value) && !isset($$key))
			$$key = $value;
	}
	$clientID = (!$clientID) ? $_POST['clientID'] : $clientID;
	$reportR = mysqli_query($dbc, "SELECT * FROM report_content");
	$report = mysqli_fetch_row($reportR);
	
	$Billable = ($Billable == 'on') ? 1 : 0;
	if (strpos($Hours,':')) {
		$str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $Hours);
		sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
		$time_seconds = $hours * 3600 + $minutes * 60 + $seconds;
		$Hours = number_format(($time_seconds / 60) / 60, 2);
	}
	
	if ($_GET['jid']) {
		$update = "UPDATE journal SET Hours = '$Hours', 
			Billable = '$Billable', 
			TeamNote = '".mysqli_real_escape_string($dbc, $TeamNote)."', 
			ClientNote = '".mysqli_real_escape_string($dbc, $ClientNote). "', 
			RetreatNote = '".mysqli_real_escape_string($dbc, $RetreatNote). "', 
			RetreatDate1 = '$RetreatDate1',
			RetreatDate2 = '$RetreatDate2',
			QtrInc = '$QtrInc', 
			Quarterly = '".mysqli_real_escape_string($dbc, $Quarterly)."', 
			Intro = '".mysqli_real_escape_string($dbc, $Intro)."',
			Date = '$Date',
			Category = '$Category',
			created = NOW()
		WHERE id = $jid";
	} else {
		$update = "INSERT INTO journal (ClientID, StaffID, Flags, Hours, Billable, TeamNote, ClientNote, RetreatNote, RetreatDate1, RetreatDate2, QtrInc, Quarterly, Intro, Date, Category, created) VALUES
			($clientID, $staffID, '$Flags', '$Hours', '$Billable', '". mysqli_real_escape_string($dbc, $TeamNote) . "', '". mysqli_real_escape_string($dbc, $ClientNote)."', '". mysqli_real_escape_string($dbc, $RetreatNote)."', '$RetreatDate1', '$RetreatDate2', '$QtrInc', '".mysqli_real_escape_string($dbc, $Quarterly)."', '".mysqli_real_escape_string($dbc, $Intro)."', '$Date', '$Category', NOW())";
	}
	// echo $update;
	if (!mysqli_query($dbc, $update)) {
		die('Error: ' . mysqli_error($update).$update);
	}
	$thisid=mysqli_insert_id($dbc);
			//TAGS
			$csv_flags=array();
			$tags=explode(',',$Flags);
			$i=0;
			while(!empty($tags[$i])){
				$tag=trim($tags[$i]);
				$chk=mysqli_query($dbc, "SELECT * FROM flags WHERE flag_title='$tag'");
				if(mysqli_num_rows($chk)==0){
					$cols=array("flag_title");$vals=array("$tag");
					$ins=db_send($cols,$vals,"flags",'insert');
					$tagID=mysqli_insert_id();
				} else {
					$row_t=mysqli_fetch_array($chk);
					$tagID=$row_t['flag_id'];
				}
					$csv_flags[]=$tagID;
				$chk=mysqli_query($dbc, "SELECT * FROM journal_flags WHERE flag_id='$tagID' AND journal_id='$thisid'");
				if(mysqli_num_rows($chk)==0){
					$cols=array('flag_id','journal_id');$vals=array("$tagID","$thisid");
					$ins=db_send($cols,$vals,'journal_flags','insert');
				}
				
				$i++;
			}
			$csv_flags=implode(',',$csv_flags);
			if(empty($csv_flags)) $csv_flags="''";
			$del=mysqli_query($dbc, "DELETE FROM journal_flags WHERE journal_id='$thisid' AND flag_id NOT IN ($csv_flags)");
	echo "<div class='add_entry aligncenter'><h1>1 record added</h1>";
	echo "<p><a href='".$PHP_SELF ."?clientID=".$clientID." '>Add another entry for ".$row['name']."</a></p>";
	// echo "<p><a href='".$PHP_SELF."'>Add an entry for another client</a></p>";
	echo "<p><a href='dashboard.php?staffID=".$staffID."'>View all your clients entries</a></p>";
	echo "</div>";
	// debug_p($_REQUEST, "all the data coming in");

	
} elseif ($_GET['addFirst'] == 1) {
	// echo "<br /><br /><br /><br />" . $clientID;
	
} elseif (empty($_POST['clientID']) && empty($_GET['clientID'])) {

	$staffr = mysqli_query($dbc, "SELECT * FROM staff");
	$codeq = "SELECT * FROM clients".($userinfo['admin']!='1'?" LEFT JOIN staff_clients ON clients.id=staff_clients.clientID WHERE staff_clients.staffID='{$userinfo['id']}' AND clients.active = 1":'')." ORDER BY clients.name";
	// echo $codeq;
	$coder = mysqli_query($dbc, $codeq);
	if(mysqli_num_rows($coder)==0) echo "<div id='journal_output'><div class='empty'><br /><h2>You have not been assigned to a client.</h2></div></div>";
	else {
		
		echo "<form method='POST' action='$PHP_SELF'>\n";
	
		echo "<br /><br /><br /> ";
	
		// -- CODEMENU START
		echo "<div class='aligncenter' style='width:400px;'><select name=\"clientID\">\n";
		while ($code = (mysqli_fetch_assoc($coder))) {
			echo "<option value=\"" . $code['id'] . "\" ".($_REQUEST['clientID']==$code['id']?"selected='selected'":'').">";
			if ($code['code']) { echo $code['code'] . " &mdash; "; }
			echo $code['name'];
			echo "</option>\n";
		}
		echo "</select>\n";
		// -- CODEMENU ENDS
		echo "<input type=\"submit\" name=\"next\" value=\"Next >>\">\n";
		echo "</div></form>";
	}
	// debug_p($_REQUEST, "all the data coming in");

} 
elseif (!empty($_POST['clientID']) || !empty($_GET['clientID'])) {
// else {
	$clientID = ($_POST['clientID']) ? $_POST['clientID'] : $_GET['clientID'];
	// echo "<br /><br /><br /><br />ClientID: " . $clientID;
	// $c = mysql_query("SELECT * FROM clients WHERE id='".$clientID."'") or die(mysql_error());
	// if(mysql_num_rows($c)>0)$client=mysql_fetch_array($c);
	// // $hoursq = "SELECT SUM(Hours) FROM journal WHERE ClientID = " . $clientID . " AND YEAR(created) = YEAR(curdate())";
	// // echo $hoursq;
	// $hoursr = @mysql_query($hoursq);
	// if(mysql_num_rows($hoursr)>0)$hours = mysql_fetch_row($hoursr);
	// 
	// $hoursTotal = $hours['0'];
	// if ($hoursTotal >= 15 && $hoursTotal < 18) { $fcolor = '#ff9900'; }
	// if ($hoursTotal >= 18) { $fcolor = '#cc0033'; }
	// 
	// $hoursleft = $row['totalhours'] - $hoursTotal;
	
	$staffq = "SELECT * FROM staff s, staff_clients c WHERE s.id = c.staffID AND c.clientID = " . $clientID;
	$staffr = mysqli_query($dbc, $staffq);
	$clientq = "SELECT * FROM clients WHERE id = " . $clientID;
	$clientr = mysqli_query($dbc, $clientq);
	// echo $clientq;
	// echo $staffq;
	$staff = mysqli_fetch_assoc($staffr);

	$fcolor = client_health_color($clientID);
	
	echo "<div id='wrapper'><div id='client_profile'>";
	
	echo "<form method='POST' action='$PHP_SELF'>\n";
	echo "<table border=0 cellspacing=0 id='track' class='aligncenter'><tr>";
	$code = (!$row['code']) ? '' : $row['code'];
	$page_head = ($code=='') ? $row['name'] : $code . " &mdash; " . $row['name']; 
	echo "<td colspan=2 valign='bottom'><span class='cname'>$page_head</span>&nbsp;&nbsp;" . client_health_dots($clientID,20);
	echo "<a href='#' class='glyphicon glyphicon-new-window' onclick='popOut()' style='float:right;font-size: 14px;'>&uarr; pop-out</a>";
	echo "</td></tr><tr><td colspan=2>";
	echo "<table border=0 width=360 align='left' valign='top'><tr><td>";
	echo "<p class='info'><span class='label'>Consultant:</span>";
	if(mysqli_num_rows($staffr)>>1) {
		while ($staff = mysqli_fetch_assoc($staffr)) {
			echo $staff['firstname'] . " " . $staff['lastname'] . " & ";
		}
	} else {
		echo $staff['firstname'] . " " . $staff['lastname'];
	}
	echo "</p>";
	
	$client = mysqli_fetch_assoc($clientr) or DIE (mysqli_error($clientr));
	
	$url = (!$client['url']) ? '#' : $client['url'];
	
	if ($url == '#') {
		echo "<p class='label'>Co-op Homepage</p>";
	} else {
		echo "<p class='label'><a href='" . $url . "' target='_BLANK'>Co-op Homepage</a></p>";
	}
	echo "<p class='info'><span class='label'>GM: </span>".$client['gm_name']." / <a href='tel:".$client['gm_contact']."'>" . $client['gm_contact'] . "</a> / <a href='mailto:".$client['gm_email']."'>".$client['gm_email']."</a></p>";
	echo "<p class='info'><span class='label'>Chair: </span>".$client['chair_name']." / ".$client['chair_contact']." / <a href='mailto:".$client['chair_email']."'>".$client['chair_email']."</a></p>";
	echo "<p class='info'><span class='label'>Board: </span>".$client['board_name']." / ".$client['board_contact']." / <a href='mailto:".$client['board_email']."'>".$client['board_email']."</a></p>";				
	echo "</td></tr></table>";
	
	mini_dash($clientID,'right');
	// $align = 'right';
	// $hoursq = "SELECT SUM(Hours) FROM journal WHERE ClientID = " . $clientID . " AND YEAR(created) = YEAR(curdate()) AND (MONTH(created) >= (".(3*($thisQ-1)+1).") AND MONTH(created)<=(".(3*$thisQ)."))";
	// $hoursr = mysql_query($hoursq);
	// $hours = mysql_fetch_row($hoursr);
	// $hoursTotal = $hours['0'];
	// // if ($hoursTotal >= ($row['q_hours']/3) && $hoursTotal < (2*$row['q_hours']/3)) { $fcolor = '#ff9900'; }
	// // if ($hoursTotal >= (2*$row['q_hours']/3)) { $fcolor = '#cc0033'; }
	// $fcolor = client_health_color($clientID);
	// $totr = mysql_query("SELECT * FROM clients WHERE ID = $clientID");
	// $client = mysql_fetch_assoc($totr);
	// $tot = $client['total_hours'];
	// $hoursleft = $tot - $hoursTotal;
	// echo "<table border=0 align='$align' width=280 valign='top'><tr>";
	// echo "<td align='$align'><span class='label'>Hrs. Used (Q".$thisQ."): </span><span class='resp' style='color: $fcolor;'><b>" . round($hoursTotal,2) . "</b></span><span class='label'> of </span><span class='resp'><b>" . round($tot,2) . "</b></span> / <span class='resp' style='color: $fcolor;'><b>" . round($hoursleft,2) . "</b></span> <span class='label'>remain</span></td>";
	// echo "</tr><tr>";
	// echo "<td align='$align'><span class='label'>Contact pattern: </span><span class='resp'><b>" . contact_pattern_dots($clientID,15) . "</b></span></td>";
	// echo "</tr></table>";
	
?>
	</td></tr>
	
	<tr><td colspan=2><span class='label'>Date: </span><input type='text' name='Date' class='datepicker' value="<?php echo  ($fill['Date']) ? $fill['Date'] : date("Y-m-d")?>" size=12 />
	&nbsp;&nbsp;&nbsp;&nbsp;<span class='label'>Log Time (Hours): </span><input name='Hours' id="timer" value='<?php echo  ($jid) ? $fill['Hours'] : 0; ?>' />
	<input type="button" value="Start/Stop Timer" onclick="doTimer();" />
	</td>
	</tr>
	<tr>
		<td class='label'>Category: </td><td>
		<table class="tbl" border="0" style='height: 25px; padding:0; margin:0; border:none;'>
			<tr>
				<td style='border:none;'><select name='Category' onchange='switchType(this.value)'>
					<option value='call' <?php echo  ($fill['Category'] == 'call') ? "SELECTED" : ""; ?>>Call *</option>
					<option value='quarterly' <?php echo  ($fill['Category'] == 'quarterly') ? "SELECTED" : ""; ?>>Quarterly *</option>
					<option value='retreat' <?php echo  ($fill['Category'] == 'retreat') ? "SELECTED" : ""; ?>>Retreat *</option>
					<option value='research' <?php echo  ($fill['Category'] == 'research') ? "SELECTED" : ""; ?>>Research</option>
					<option value='email' <?php echo  ($fill['Category'] == 'email') ? "SELECTED" : ""; ?>>Email</option>
					<option value='consult' <?php echo  ($fill['Category'] == 'consult') ? "SELECTED" : ""; ?>>Consult/Meeting</option>
					<option value='internal' <?php echo  ($fill['Category'] == 'internal') ? "SELECTED" : ""; ?>>Internal</option>
					<option value='adjust' <?php echo  ($fill['Category'] == 'adjust') ? "SELECTED" : ""; ?>>Hours Adjustment</option>
				</select></td>
				<td style='border:none;'>&nbsp;&nbsp;&nbsp;&nbsp;
					<input type='hidden' name='Billable' value='0' />
					<input type='checkbox' id='Billable' name='Billable' <?php echo ($fill['Billable']==0 && ($jid)) ? "" : "CHECKED"; ?> /></td>
				<td class='label' style='border:none;'><label for='Billable'>Billable</label>
					<?php //echo $fill['Billable']; ?>
				</td>
			</tr>
		</table>
		
	</td>
	</tr>
<!--<tr><td class='label'>Flags: </td><td><textarea rows=1 cols=110 name='Flags'></textarea></td></tr> -->
	<tr id='row_nts'><td class='label'>Notes to Self/Team: </td><td><textarea rows=3 cols=80 name='TeamNote'><?php echo  ($jid) ? $fill['TeamNote'] : ''; ?></textarea></td></tr>
	<tr id='row_ntc'><td class='label'>Notes to Client: </td><td><textarea rows=6 cols=80 name='ClientNote'><?php echo  ($jid) ? $fill['ClientNote'] : ''; ?></textarea>
		<div id='QtrInc_cont'><input type='checkbox' name='QtrInc' id='QtrInc' value='1' /> 
		<label for='QtrInc'>Include notes on Quarterly Report?</label></div></td></tr>
	<tr id='row_nq' style='display:none;'><td class='label'>Quarterly Notes: </td><td><textarea rows=6 cols=80 name='Quarterly'><?php echo  ($jid) ? $fill['Quarterly'] : ''; ?></textarea></td></tr>
	<tr id='row_ni' style='display:none;'><td class='label'>Personal intro note: </td><td><textarea rows=3 cols=80 name='Intro'><?php echo  ($jid) ? $fill['Intro'] : $report['intro_default']; ?></textarea></td></tr>
	<tr id='row_nr' style='display:none;'><td class='label'>Retreat Notes: </td><td><textarea rows=6 cols=80 name='RetreatNote'><?php echo  ($jid) ? $fill['RetreatNote'] : ''; ?></textarea></td></td></tr>
	<tr id='row_rd' style='display:none;'><td colspan=2><span class='label'>Retreat Date Start: </span><input type='text' name='RetreatDate1' class='datepicker' value="<?php echo  ($fill['RetreatDate1']) ? $fill['RetreatDate1'] : '' ?>" size=12 />	
		<span class='label'>Retreat Date End: </span><input type='text' name='RetreatDate2' class='datepicker' value="<?php echo  ($fill['RetreatDate2']) ? $fill['RetreatDate2'] : '' ?>" size=12 /></tr>

	<input type='hidden' name='staffID' value='<?php echo $row['StaffID']?>' />
	<input type='hidden' name='clientID' value='<?php echo $clientID?>' />
	<tr><td colspan=2 align='center'><input class='aligncenter' type="submit" name="submit" value="Submit" /><br /></td></tr>
	</table>
	

	<div id='unfold' class='aligncenter'>See Last Entry</div><br />
	<div id='last'>
	<table border=0 cellspacing=0 id='track' class='aligncenter'><tr>
	<?php
	echo "<tr><td colspan=2><span class='label'>Last entry: </span><span class='resp'>" . date('Y-m-d', strtotime($row['created'])) . "</span></td></tr>";
	// echo "<tr><td colspan=2><span class='label'>Flags: </span><span class='resptxt'>" . substr($row['Flags'], 0, 120) . "... </span></td></tr>";
	echo "<tr><td colspan=2><span class='label'>Team Notes: </span><span class='resptxt'>" . $row['TeamNote'] /* substr($row['TeamNote'], 0, 120) */ . "... </span></td></tr>";
	echo "<tr><td colspan=2><span class='label'>Client Notes: </span><span class='resptxt'>" . substr($row['ClientNote'], 0, 120) . "... </span></td></tr>";
	echo "</table>";
	echo "</div> <!-- /last -->";
	
	echo "</form>";
	
	echo "</div></div>";
}





// echo "<input type=\"submit\" name=\"submit\" value=\"Add\">\n";






// print_r($row);
// debug_p($_REQUEST, "all the data coming in");
?>
<script>
$("#unfold").click(function () {
	$("#last").slideToggle("slow");
});
function switchType(type){
	if(type=='quarterly'){
	 $('#row_nts, #row_ntc, #row_nr').hide();
	 $('#row_nq, #row_ni').show(); 
	}else if(type=='retreat'){ 
	 $('#row_nq,#row_ntc, #row_ni').hide();
	 $('#row_nr, #row_rd').show();
	}else{
	 $('#row_nts, #row_ntc').show();
	 $('#row_nq, #row_ni, #row_nr, #row_rd').hide(); 
	}
}
$(function(){
	switchType('<?php echo  $fill['Category'] ?>');
});

</script>
</body>
