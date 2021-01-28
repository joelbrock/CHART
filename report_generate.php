<?php
require("mysql_connect.php");
?>
<link rel="stylesheet" href="css/style.css" type="text/css" charset="utf-8"></link>
<?php
$clientID = $_REQUEST['clientID'];
$staffID = $_REQUEST['staffID'];
$reportID=1;

$thisQ = ($_GET['thatQ']) ? $_GET['thatQ'] : thisQ();
$thatY = ($_GET['thatY']) ? $_GET['thatY'] : date('Y');
if (date('z') < 14) $thatY--;

$query = "SELECT * FROM clients c ORDER BY c.name";
// echo $query;
$result = mysqli_query($dbc, ($query);
while($row=mysqli_fetch_assoc($result)){
	$c[]=$row;
}
//print_r($c);
?>
<form action="<?php echo  $_SERVER['PHP_SELF'] ?>" method="GET">
<select name="thatQ">
	<option value=1<?php echo $thisQ == 1 ? ' SELECTED' : ''; ?>>Q1</option>
	<option value=2<?php echo $thisQ == 2 ? ' SELECTED' : ''; ?>>Q2</option>
	<option value=3<?php echo $thisQ == 3 ? ' SELECTED' : ''; ?>>Q3</option>
	<option value=4<?php echo $thisQ == 4 ? ' SELECTED' : ''; ?>>Q4</option>
</select>
<select name="thatY" id="thatY">
<?php
$yearR = mysqli_query($dbc, ("SELECT YEAR(MIN(Date)), YEAR(MAX(Date)) FROM journal");
list($yr1, $yr2) = mysqli_fetch_row($yearR);
for ($i = $yr1; $i <= $yr2; $i++) {
	echo "<option value='" . $i . "'";
	echo ($i == $thatY) ? " SELECTED" : "";
	echo ">" . $i . "</option>";
}
?>
</select>
<input type="submit" value="submit" />
</form>
<br />
<?php

echo "<ul>";
foreach($c as $client) {
	$clientID=$client['id'];
	$hours_ty = "SELECT SUM(Hours) FROM journal WHERE ClientID = " . $clientID . ($admin==false?" AND StaffID='{$userinfo['id']}'":'') . " AND YEAR(Date) = $thatY AND (MONTH(Date) <= (".(3*($thisQ)+1)."))";//hours used this year prior to Q
	// echo $hours_ty;
	$hours_tyr = mysqli_query($dbc, ($hours_ty);
	$client['hours_ty'] = mysqli_fetch_row($hours_tyr);
	$client['hrs']['total'] = round($client['hours_ty']['0'],2);
	$hoursq = "SELECT *, DATE_FORMAT(`created`,'%c/%e/%Y') as created_fmt FROM journal
		WHERE ClientID = " . $clientID . ($admin==false?" AND StaffID='{$userinfo['id']}'":'') . " AND YEAR(Date) = $thatY
		AND (MONTH(Date) >= (".(3*($thisQ-1)+1).") AND MONTH(Date)<=(".((3*$thisQ)+1)."))
		ORDER BY Category='quarterly' DESC, Date DESC ";
	// echo $hoursq;
	$hoursr = mysqli_query($dbc, ($hoursq);
	if(mysqli_num_rows($hoursr)==0)continue;///YOU ARE HERE!!!!!!!!
	echo "<li><a href='pdf.php?clientID={$client['id']}&thatQ=$thisQ&thatY=$thatY' target='_blank'>{$client['name']}</a></li>";
	//print checkbox for this client
}
echo "</ul>";
//print_r($hours);
// echo "<br><b><a href='pdf.php?clientID=all' target='_blank'>Print All Reports</a></b>";
//print checkbox for ALL

?>

