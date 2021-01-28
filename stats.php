<?php
require("mysql_connect.php");

include_once('topbar.php');


?>
<html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<link rel="stylesheet" href="css/style.css" type="text/css" charset="utf-8">
<!--<link rel="stylesheet" href="css/tablesort.css" type="text/css" charset="utf-8">-->
<script src="js/jquery.js" type="text/javascript" charset="utf-8"></script>
<script src="js/jquery-ui.js" type="text/javascript" charset="utf-8"></script>

</head>
<body>

<div id="wrapper">
<div id="client_profile">
<br /><br /><br />
<?php
$ctR = mysqli_query($dbc, ("SELECT coop FROM attendance WHERE year = YEAR(CURDATE()) GROUP BY coop");
$ct = mysqli_num_rows($ctR);


$ctNR = mysqli_query($dbc, ("SELECT a.coop FROM attendance a, ncga_coops n WHERE a.clientID = n.clientID AND n.in_ncga <> '' 
	 AND a.year = YEAR(CURDATE()) GROUP BY a.coop");
$ctN = mysqli_num_rows($ctNR);

array_push($inNCGA, $ctN);
$ctNRt = mysqli_query($dbc, ("SELECT coop_name FROM ncga_coops WHERE in_ncga <> ''");
$ctNt = mysqli_num_rows($ctNRt);


$inCBLD = array();
$ctCR = mysqli_query($dbc, ("SELECT a.coop FROM attendance a, ncga_coops n WHERE a.clientID = n.clientID AND n.in_cbld <> '' 
	 AND a.year = YEAR(CURDATE()) GROUP BY a.coop");
$ctC = mysqli_num_rows($ctCR);

array_push($inCBLD, $ctC);
$ctCRt = mysqli_query($dbc, ("SELECT coop_name FROM ncga_coops WHERE in_cbld <> ''");
$ctCt = mysqli_num_rows($ctCRt);

echo "<div class='stat_box'><p># of Co-ops</p><span class='big'>$ct</span></div>";
echo "<div class='stat_box'><p># of NCGA Co-ops</p><span class='big'>$ctN</span><span class='of'>/$ctNt</span></div>";
echo "<div class='stat_box'><p># of CBLD Co-ops</p><span class='big'>$ctC</span><span class='of'>/$ctCt</span></div>";

$eventQ = "SELECT event_name, eventid FROM events WHERE YEAR(event_date) = YEAR(CURDATE()) ORDER BY event_date";
$eventR = mysqli_query($dbc, ($eventQ);

echo "<table border=1 cellspacing=0 class='stats_tbl'><tr><td width=160></td>";
$events = array();
while ($col = mysqli_fetch_assoc($eventR)) {
	array_push($events, $col['eventid']);
	echo "<th>" . $col['event_name'] . "</th>";
}
// print_r($events);
echo "</tr><tr><td class='label'>Registrants</td>";
foreach($events as $e) {
	// echo "eventID: " . $e;
	$regR = mysqli_query($dbc, ("SELECT COUNT(coop) FROM attendance WHERE eventID = $e AND year = YEAR(CURDATE())");
	$reg = mysqli_fetch_row($regR);
	echo "<td class='data'>" . $reg[0] . "</td>";	
}

echo "</tr><tr><td class='label'>Attended</td>";
foreach($events as $e) {
	$attR = mysqli_query($dbc, ("SELECT COUNT(coop) FROM attendance WHERE eventID = $e AND att <> '' AND year = YEAR(CURDATE())");
	$att = mysqli_fetch_row($attR);
	echo "<td class='data'>" . $att[0] . "</td>";
}

echo "</tr><tr><td colspan=50>&nbsp;</td>";

echo "</tr><tr><td class='label'>Board Member</td>";
foreach($events as $e) {
	$dirR = mysqli_query($dbc, ("SELECT COUNT(coop) FROM attendance WHERE eventID = $e AND title IN('Director','Office','Chair/President') AND year = YEAR(CURDATE())");
	$dir = mysqli_fetch_row($dirR);
	echo "<td class='data'>" . $dir[0] . "</td>";
}

echo "</tr><tr><td class='label'>Staff Member</td>";
foreach($events as $e) {
	$staR = mysqli_query($dbc, ("SELECT COUNT(coop) FROM attendance WHERE eventID = $e AND title IN('GM','Other') AND year = YEAR(CURDATE())");
	$sta = mysqli_fetch_row($staR);
	echo "<td class='data'>" . $sta[0] . "</td>";
}

echo "</tr><tr><td colspan=50>&nbsp;</td>";

echo "</tr><tr><td class='label'># of Co-ops</td>";
foreach($events as $e) {
	$ctR = mysqli_query($dbc, ("SELECT coop FROM attendance WHERE eventID = $e AND year = YEAR(CURDATE()) GROUP BY coop");
	$ct = mysqli_num_rows($ctR);
	echo "<td class='data'>" . $ct . "</td>";
}

echo "</tr><tr><td class='label'># of NCGA Co-ops</td>";
$inNCGA = array();
foreach($events as $e) {
	$ctNR = mysqli_query($dbc, ("SELECT a.coop FROM attendance a, ncga_coops n WHERE a.clientID = n.clientID AND n.in_ncga <> '' 
		AND a.eventID = $e AND a.year = YEAR(CURDATE()) GROUP BY a.coop");
	$ctN = mysqli_num_rows($ctNR);
	echo "<td class='data'>" . $ctN . "</td>";
	array_push($inNCGA, $ctN);
}

echo "</tr><tr><td class='label'># of CBLD Co-ops</td>";
$inCBLD = array();
foreach($events as $e) {
	$ctCR = mysqli_query($dbc, ("SELECT a.coop FROM attendance a, ncga_coops n WHERE a.clientID = n.clientID AND n.in_cbld <> '' 
		AND a.eventID = $e AND a.year = YEAR(CURDATE()) GROUP BY a.coop");
	$ctC = mysqli_num_rows($ctCR);
	echo "<td class='data'>" . $ctC . "</td>";
	array_push($inCBLD, $ctC);
	
}

echo "</tr><tr>";

echo "</tr></table>";
echo "<br /><br />";

$listR = mysqli_query($dbc, ("select a.coop, n.in_ncga, n.in_cbld FROM attendance a LEFT OUTER JOIN ncga_coops n ON a.clientID = n.clientID group by a.coop");
echo "<table border=1 cellspacing=0>";
while ($row = mysqli_fetch_assoc($listR)) {
	echo "<tr><td>".$row['coop']."</td><td>".$row['in_cbld']."</td><td>".$row['in_ncga']."</td></tr>";
}
echo "</table>";

?>
</div></div>
</body>
</html>