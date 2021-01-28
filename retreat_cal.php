<?php
require_once("mysql_connect.php");
require_once("functions/activecalendar.php");
$myurl=$_SERVER['PHP_SELF'];
$arrowBack="<img src=\"images/back.png\" border=\"0\" alt=\"&lt;&lt;\" />"; // use png arrow back
$arrowForw="<img src=\"images/forward.png\" border=\"0\" alt=\"&gt;&gt;\" />"; // use png arrow forward
extract($_GET);
$Q = "SELECT j.RetreatDate1, j.RetreatDate2, c.name FROM journal j LEFT JOIN clients c ON j.clientID = c.id WHERE YEAR(j.RetreatDate1) = $yearID AND j.Category = 'retreat' ORDER BY j.RetreatDate1 ASC";
$R = mysqli_query($dbc, $Q);
$cal=new activeCalendar($yearID);
$cal->enableYearNav($myurl,$arrowBack,$arrowForw); // enables navigation controls
$cal->enableDayLinks($myurl);
while($row = mysqli_fetch_array($R)) {
	$year = date('Y', strtotime($row[0]));
	$month = date('m', strtotime($row[0]));
	$day = date('d', strtotime($row[0]));
	$cal->myHover = $row[2];
	$cal->setEvent("$year", "$month", "$day");
	if($row[0] != $row[1]) {
		$year = date('Y', strtotime($row[1]));
        	$month = date('m', strtotime($row[1]));
        	$day = date('d', strtotime($row[1]));
        	$cal->setEvent("$year", "$month", "$day");
	}
}
include("topbar.php");
?> 
<link rel="stylesheet" type="text/css" href="css/cal_plain.css" />
<div id="journal_output">
<br /><br /><br />
<center>
<?php print $cal->showYear(3); // '3' sets 3 months in each row ?>
<br />
</center>
<table style="margin-left:150px;">
<?php
$R = mysqli_query($dbc, $Q);
while($row = mysqli_fetch_array($R)) {
	echo "<tr><td>".date("m/d",strtotime($row[0]))."</td>";
	echo "<td>". $row[2]."</td></tr>";
} ?>
</table>
</div>
</body>
</html>
<script>
$(document).ready(function(){
	$('a[href][title]').qtip({
		content: {
			text: false
		},
		style: 'cream',
		show: 'mouseover',
		hide: 'mouseout'
	});
});
</script>
