<?php
require_once('mysql_connect.php');
?>
<!DOCTYPE html>
<HTML>
<head>
<link rel="stylesheet" href="css/style.css" type="text/css" charset="utf-8"></link>
<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" charset="utf-8"></link>
<!-- <script src="js/jquery.js" type="text/javascript" charset="utf-8"></script> -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="js/jquery-ui.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="js/jquery.hoverIntent.minified.js"></script>
<script src="js/jquery.dropdown.js" type="text/javascript" charset="utf-8"></script>
<script src="js/jquery.fixedheadertable.min.js"></script>

<script src="js/jquery.qtip.min.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="css/jquery.qtip.min.css" type="text/css" charset="utf-8">

<style>
iframe#externalSite.externalSite {
	width: 95%;
	height:95%;
	font-size:1em;
	line-height:1.2em;
	list-style:circle;
}


</style>
<script type="text/javascript" charset="utf-8">
$(function() {
    $('.opener').click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var horizontalPadding = 30;
        var verticalPadding = 30;
        $('<div id="outerdiv"><iframe id="externalSite" class="externalSite" src="report_generate.php" />').dialog({
            title: ($this.attr('title')) ? $this.attr('title') : 'My Report Generator',
            autoOpen: true,
            width: 400,
            height: 500,
            modal: true,
            resizable: true,
            autoResize: true,
            overlay: {
                opacity: 0.9,
                background: "black"
            }
        }).width(400 - horizontalPadding).height(500 - verticalPadding); 
    });
});
</script>

</head>
<body>
<?php

$clientID = $_REQUEST['clientID'];

$query = "SELECT j.created AS datetime, 
		c.name AS coopname, 
		j.TeamNote as teamnotes, 
		CONCAT(s.firstname,\" \",s.lastname) AS consultant, 
		j.StaffID AS staffID
 	FROM journal j, staff s, clients c 
	WHERE j.ClientID = c.id AND j.StaffID = s.id
	HAVING staffID = $staffID";

// echo "<br><br><br><br>" . $query;

$result = mysql_query($query);

//echo "<form method='post' action='#'>\n";
echo "<div class='banner'>\n";
// echo "<div id='searchbox'><input id=\"chooseclient\" /></div>";
// echo "<div id='track_btn'><a href='entry.php'>TRACK! (Q".thisQ().'-'.date('Y').")</a></div>";

echo "<div class='track_btn'><ul class='dropdown'><li><a href='#'>TRACK!</a><ul class='sub_menu'>";
$clientr = mysql_query("SELECT * FROM clients LEFT JOIN staff_clients ON clients.id=staff_clients.clientID WHERE staff_clients.staffID='{$userinfo['id']}' ORDER BY clients.name");

while ($clientRow = mysql_fetch_assoc($clientr)) {
	echo "<li><a href='entry.php?clientID=".$clientRow['id']."'>".acronymize($clientRow['name'])."</a></li>";
}
echo "</ul></li></ul></div>";

$greetings = array("Welcome","Bienvenidos","Hello","Hiya","Wilkommen","Shalom","Bon jour","Howdy","Lookin' good","Hola","Konichiwa","Aloha","Mahalo","Bienvenue");
$greeting = array_rand($greetings);
echo "<div id='user_btn'>".$greetings[$greeting]." <a href='staff_profile.php?staffID={$userinfo['id']}'>".$userinfo['firstname']."</a>!  
	<span style='font-size:8.5px; color: #88888;' >cID: $clientID</span>";

$rcolor = (REPORT_READY) ? "green" : "red";
$rpt_title = (REPORT_READY) ? "Reports are ready to go.  Get to it!" : "Reports not yet ready, sorry";
echo "&mdash; Reports: <a href='#' title='".$rpt_title."'><img src='images/".$rcolor."dot.gif' alt='Report readiness indicator' height=12 border=0 class='dot_bg' /></a></div>";
	
echo "<div class='top_btns'> <ul class='dropdown'>";
echo "<li><a href='#'>OPTIONS</a>
				<ul class=\"sub_menu\">\n";	

if (REPORT_READY) {
	if(($report) || ($admin)){
		echo "<li><a href='#' class='opener'>Generate ".($admin?"Full":"My")." Quarterly Report</a></li>\n";
	} 
} else {
	echo "<li><b>Reports Arent Ready Yet.</b></li>\n";
}
if(!empty($clientID)){
	if(!$client) {
		$c = mysql_query("SELECT * FROM clients WHERE id='".$clientID."'");
		if(mysql_num_rows($c)>0)$client=mysql_fetch_array($c);
	}
	if($clientID){
		// echo "<li><a href='dashboard.php?clientID=".$client['id']."'>View all for $client[name]</a></li>\n";
		// if ($userinfo['id'] == $staffID || ($admin)) 
			echo "<li><a href='client_profile.php?clientID=".$client['id']."'>Edit Client Profile</a></li>\n";
	}
}
if($admin){
	// if (!$_GET['staffID']) echo "<li><a href='client_profile.php?clientID=$clientID>Edit Client Profileeeee</a></li>\n";
	echo "<li><a href='client_assign.php'>Edit Staff Assignments</a></li>\n";
	echo "<li><a href='report_profile.php'>Edit Report Profile</a></li>\n";
	// echo "<li><a href='attendance.php'>Event Attendance</a></li>\n";
}

// echo "<li><a href='entry.php".(isset($_REQUEST['clientID'])?"?clientID=".$_REQUEST['clientID']:'')."'>Track Hours</a></li>\n";

echo "<li><a href='logout.php'>Logout.</a>";
echo "</ul></li>";
echo "<li><a href='#'>CONSULTANTS</a>
	<ul class=\"sub_menu\">\n";	
$staffr = mysql_query("SELECT * FROM staff ORDER BY firstname");
if ($admin) echo "<li><a href='dashboard.php?staffID=SUPERALL'>--Super ALL --</a></li>\n";
echo "<li><a href='dashboard.php?staffID=ALL'>-- ALL --</a></li>\n";
while ($staffRow = mysql_fetch_assoc($staffr)) {
	$fullname = $staffRow['firstname'] . " " . $staffRow['lastname'];
	echo "<li><a href=dashboard.php?staffID=" . $staffRow['id'] . ">" . $fullname . "</a></li>\n";
}
echo "</ul></li>";

echo "<li><a href='#'>CLIENTS</a>
		<ul class=\"sub_menu\">\n";
$clientr = mysql_query("SELECT * FROM clients".($userinfo['admin']!='1'?" LEFT JOIN staff_clients ON clients.id=staff_clients.clientID WHERE staff_clients.staffID='{$userinfo['id']}' ORDER BY clients.name":' ORDER BY clients.name'));

while ($clientRow = mysql_fetch_assoc($clientr)) {
	echo "<li><a href=dashboard.php?clientID=" . $clientRow['id'] . ">" . ucwords($clientRow['name']) . "</a></li>\n";
}
echo "</ul></li></ul></div></div>";
echo "</p></div>\n";
?>