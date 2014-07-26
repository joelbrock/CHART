<?php
require("mysql_connect.php");

// $autocompletedata = "";
// $results = mysql_query("SELECT name FROM clients");
// while ($row = mysql_fetch_row($results)) {
// 	$autocompletedata .= $row[0] . " ";
// }

if ($_GET['delete'] == 'delete') 
	$jid = $_GET['jid'];
	$delR = mysql_query("DELETE FROM journal WHERE id = $jid");
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
<script src="js/tablesort.js" type="text/javascript" charset="utf-8"></script>
<script src="js/picnet.table.filter.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" language="Javascript">

function confirmclick(cid,jid) {
    var r = confirm ('This action cannot be undone. Are you sure you want to perform this action?');
    if (r) {
              location.href = 'dashboard.php?delete=delete&clientID='+cid+'&jid='+jid;
    }
}

</script>


<style>
#wrapper {margin:0 !important;width:100%;height:100%;}
#panel {
	background: #754c24;
	height: 200px;
	display: none;
}
.slide {
	position:absolute;
	left:0px;
	top:50%;
	margin: 0;
	padding: 0;
	border-left: solid 4px #422410;
	background: url(images/btn-slide.gif) no-repeat center top;
}
.btn-slide {
	background: url(images/white-arrow.gif) no-repeat right -50px;
	text-align: center;
	width: 31px;
	height: 144px;
	padding: 10px 10px 0 0;
	margin: 0 auto;
	display: block;
	font: bold 120%/100% Arial, Helvetica, sans-serif;
	color: #fff;
	text-decoration: none;
	-webkit-transform: rotate(90deg);
	-moz-transform: rotate(90deg);
	-o-transform: rotate(90deg);
	writing-mode: lr-tb;

}
.active {
	background-position: right 12px;
}
#output { width: 100%; }

.textblock a:hover { cursor:pointer; text-decoration:none; }

.year-marker {	text-align: center; font-weight: bold; font-size:1.3em; border: 1px solid black;}
</style>
<script>
function removeTag() {
    $.get("somepage.php");
    return false;
}
$(document).ready(function() {
	var options = {
		clearFiltersControls: [$('#clearfilters')]
	};
	var options1 = {
		additionalFilterTriggers: [$('#quickfind')],
		clearFiltersControls: [$('#cleanfilters')],
		matchingRow: function(state, tr, textTokens) {
			if (!state || !state.id) { return true; }
				var val =  tr.children('td:eq(2)').text();
				var valC =  tr.children('td:eq(1)').text();
								
					switch (state.id) {
						case 'quickfind': {val.toLowerCase();valC.toLowerCase();if(val.match(state.value) || valC.match(state.value))return true; };
						default: return true;
					}
		}
	};
	
	$('#output').tableFilter(options1);
});
</script>
<script>
$(document).ready(function(){

	$(".btn-slide").click(function(){
	  $("#panel").slideToggle("slow");
	  $(this).toggleClass("active");return false;
	});
	
});
</script>
</head>
<body>

<?php
include_once('topbar.php');

$staffID = $_GET['staffID'];
if(empty($staffID))
	$client_ID = $_GET['clientID'];

if (!$client_ID && !$staffID) { $staffID = $userinfo['id']; }//add authentication
// 
// $sqlstaff = ($staffID == 'ALL') ? "" : "";
// if ($staffID != 'ALL') $sqladmin = (!$staffID) ? "AND j.clientID = $clientID" : "AND j.StaffID = $staffID";
// else $sqladmin = "";
// $sqlgroup = (!$staffID) ? "j.ClientID, j.Date" : "j.ClientID, j.Date";
$sqlrange = "";
$sqlyear = "";
$sqlgroup = "";
$sqlgroup = "";
switch ($_GET['range']) {
	case 'today':
		$sqlrange = "AND j.Date = CURDATE()";
		break;
	case 'this_week':
		$sqlrange = "AND WEEK(j.Date) = WEEK(CURDATE())";
		break;
	case 'this_month':
		$sqlrange = "AND MONTH(j.Date) = MONTH(CURDATE())";
		break;
	case 'this_quarter':
		$sqlrange = "AND QUARTER(j.Date) = QUARTER(CURDATE())";
		break;
	case 'prev_quarter':
		$sqlrange = "AND QUARTER(j.Date) = QUARTER(CURDATE()) - 1";
		break;
	case 'prev_year':
		$sqlyear = "AND YEAR(j.Date) = YEAR(CURDATE()) - 1";
		break;
	case 'this_year':
		$sqlyear = "AND YEAR(j.Date) = YEAR(CURDATE())";
		break;
	default: //this_year
		$sqlyear = "AND YEAR(j.Date) = YEAR(CURDATE())";		
}
if ($staffID == 'ALL') {
	$sqljoin = "";
	$sqlgroup = "INNER JOIN (
	  		SELECT MAX(Date) as MaxDate, ClientID
	  		FROM journal GROUP BY ClientID
		) j2 ON j.ClientID = j2.ClientID
		AND j.Date = j2.MaxDate";
	$single = False;
} elseif($staffID =='SUPERALL') {
	$sqljoin = "";
	$sqlgroup = "GROUP BY j.id";
	$single = True;
} elseif (!$staffID) {
	$sqljoin = "AND j.clientID = $client_ID";
	$sqlgroup = "GROUP BY j.id";
	$single = True;
} else {
	$sqljoin = "AND j.StaffID = $staffID";
	$sqlgroup = "INNER JOIN (
	  		SELECT MAX(created) as MaxDate, ClientID
	  		FROM journal GROUP BY ClientID
		) j2 ON j.ClientID = j2.ClientID
		AND j.created = j2.MaxDate";
	$single = False;
}

$query = "SELECT j.created AS created,
 		j.Date AS date,
		c.code AS code,
		c.name AS coopname,
		j.Category AS cat, 
		j.ClientNote as clientnote, 
		j.TeamNote as teamnote,
		j.RetreatNote AS retreatnote,
		j.Quarterly AS quarterly,
		j.Hours AS hours,
		c.total_hours AS totalhours,
		CONCAT(s.firstname,\" \",s.lastname) AS consultant, 
		j.StaffID AS staffID, 
		j.ClientID AS clientID,
		j.id AS jid
 	FROM journal j 
	JOIN staff s ON j.StaffID = s.id 
	JOIN clients c ON j.ClientID = c.id 	
	$sqlyear
	$sqljoin
	$sqlrange
	$sqlgroup
	ORDER BY j.Date DESC";

// echo "<br><br><br>".$query;

$result = mysql_query($query) or die(mysql_error());

// include ('main_select.php');


?>
<!-- <div id='wrapper'>
<div id='panel'>
	<iframe src="entry.php"></iframe>
</div>
<p class='slide'>
	<a href="#" class='btn-slide'>Track</a>
</p> -->
<?php
echo "<div id='journal_output'>";	


echo "<div id='datebar'><a onClick='remove' href='dashboard.php?staffID=".$staffID."&clientID=".$clientID."&range=today'>
	today</a>";
echo "<a href='dashboard.php?staffID=".$staffID."&clientID=".$clientID."&range=this_week'>this week</a>";
echo "<a href='dashboard.php?staffID=".$staffID."&clientID=".$clientID."&range=this_month'>this month</a>";
echo "<a style='margin-right:0' href='dashboard.php?staffID=".$staffID."&clientID=".$clientID."&range=prev_quarter' 
	alt='Previous Quarter'><<</a>";
echo "<a href='dashboard.php?staffID=".$staffID."&clientID=".$clientID."&range=this_quarter'>this quarter</a>";
echo "<a style='margin-right:0' href='dashboard.php?staffID=".$staffID."&clientID=".$clientID."&range=prev_year' 
	alt='Previous Year'><<</a>";
echo "<a href='dashboard.php?staffID=".$staffID."&clientID=".$clientID."&range=this_year'>this year</a>";
echo "</div>";

$group_on = ($_GET['range']) ? $_GET['range'] : 'this_year';
$tags = array(
	'range' => ($group_on) ? $group_on : 0,
	'staffID' => (is_numeric($staffID)) ? 'consultant' : 0,
	'clientID' => ($clientID) ? 'client' : 0
);

echo "<div id='grouped'>";

// print_r($tags); 
foreach ($tags as $p => $v) {
	if ($v != 0 || $v != '')
		echo "<div class='sorttag'><a href='dashboard.php?$p='><span class='ex'>x</span> $v</a></div>";
}

echo "</div>";

if(mysql_num_rows($result)>0){
	// echo "<div id='quickfindbox'>Quick Find: <input type='text' id='quickfind' />&nbsp;&nbsp;<a id='cleanfilters' href='#'>Clear Filters</a></div>";
	// echo "<div id='dashheader'>Selected client: " "</div>";
	echo "<div class='journal'>\n
	<table id='output' class='sortable-onload-0 rowstyle-alt'>\n
	<thead>\n
		<tr>\n
			<th class='sortable-text'>Co-op</th>\n

			<th class='sortable-numeric' filter-type='ddl'>Health</th>\n
			<!-- <th class='sortable-numeric' filter-type='ddl'>Contact</th> -->\n 
			<th class='sortable-text' filter-type='ddl'>Consultant</th>\n
			<!-- <th class='sortable-numeric'>used</th> -->\n
			<!-- <th class='sortable-numeric'>tot</th> -->\n
			<th class='sortable-numeric'>time</th>\n
			<th class='sortable-numeric'>rem</th>\n	
			<!-- <th class='sortable-text'>cat</th> -->\n
			<th>Client Notes</th>\n
			<th class='sortable-numeric'>Retreat</th>\n			
			<th class='sortable-numeric'>Date</th>\n
			<th></th>\n
		</tr>\n
	</thead>\n<tbody>\n";
		while ($row = mysql_fetch_assoc($result)) {
//			$yrmark = '2011-01-01';
			// if ((date('Y',strtotime($row['date'])) != date('Y',strtotime('Y',$yrmark))) && $single == True)
			// 	echo "<tr><td colspan=7 class='year-marker'><span>"
			// 		. date('Y',strtotime($row['date'])) . "</span></td><td>" 
			// 		. date('Y',strtotime($row['date'])) . "-01-01</td><td>&nbsp;</td></tr>";
			// $yrmark = $row['date'];
			
			if($single) { 
				$query1 = "SELECT ROUND(SUM(Hours),2) FROM journal WHERE ClientID = " . $row['clientID'] . " 
					AND YEAR(Date) = YEAR(NOW()) AND Date <= '" . $row['date'] . "'";
//			if ($single == True)
//				$query1 = "SELECT ROUND(SUM(Hours),2) FROM journal WHERE ClientID = " . $row['clientID'] . " 
//					AND Date <= '". $row['date'] ."'";
			} else {
				$query1 = "SELECT ROUND(SUM(j.Hours),2) FROM journal j WHERE j.ClientID = " . $row['clientID'] . " $sqlyear";
			}
//			echo $query1;
			$result1 = mysql_query($query1);
			$tot = mysql_fetch_row($result1);
			
			echo "<tr>\n";
			echo "<td><a id='opener' href='dashboard.php?clientID=".$row['clientID']."' title='Show all for ".$row['coopname']."'>";
			$code = ($row['code']=='')?acronymize($row['coopname']):$row['code']; 
			echo $code . "</a></td>\n";
			echo "<td align='center'>" . client_health_dots($row['clientID'], 14) . "</td>\n
<!--				<td align='center'>" . contact_pattern_dots($row['clientID'], 14) . "</td>\n  -->";

			echo "<td class='name'><a href='dashboard.php?staffID=".$row['staffID']."' title='Show all for ".$row['consultant']."'>" . short_name($row['consultant']) . "</a></td>\n";
			// echo "<td align='right'>" . rtrim($row['totalhours'],'.0') . "</td>";
			// echo "<td align='right'>" . $rem ."</td>";

			if($single == True) {
				echo "<td align='right'>" . $row['hours'] ."</td>";
			} else {
				$hrs = ($tot[0]==0) ? 0 : rtrim($tot[0],'.0');
				echo "<td align='right'>$hrs</td>";
			}
			$rem = $row['totalhours'] - $tot[0];
			$left = ((($row['totalhours'] - $tot[0]) / $row['totalhours']) * 100);
			echo "<td align='right'>" . number_format($rem,2) . " | " . number_format($left,0) ."%</td>";			
			
			// echo "<td align='center'>".substr($row['cat'],0,6)."</td>";
			echo "<td><p class='textblock'>";
			$col = 60;
			switch($row['cat']) {
				case "email";
				case "internal":
				case "consult":
				case "research":
				case "call":
					$note_text =  ($row['clientnote'] != '') ? "client: ".substr($row['clientnote'],0,$col) : (($row['teamnote'] != '') ? "team: ".substr($row['teamnote'],0,$col) : "");
					$full_text = "team note: " . htmlspecialchars($row['teamnote']) . "<br /><br />client note: " . htmlspecialchars($row['clientnote']);
					$dots = (strlen($row['clientnote']) > $col || strlen($row['teamnote']) > $col) ? "..." : ""; 
					break;
				case "quarterly":
					$note_text =  "qrtly: ".substr($row['quarterly'],0,$col);
					$full_text = htmlspecialchars($row['quarterly']);
					$dots = (strlen($row['quarterly']) > $col) ? "..." : "";
					break;
				case "retreat":
					$note_text = "retreat: ".substr($row['retreatnote'],0,$col);
					$full_text = htmlspecialchars($row['retreatnote']);
					$dots = (strlen($row['retreatnote']) > $col) ? "..." : "";
					break;
			}

			$atag = ($dots == "...") ? "<a href='#' title=\"$full_text\">" : "";
			$ctag = ($dots == "...") ? "</a>" : "";

			echo $atag.$note_text.$dots.$ctag;
			// echo ($row['clientnote'] != '') ? "client: ".substr($row['clientnote'],0,100) : (($row['teamnote'] != '') ? "team: ".substr($row['teamnote'],0,100) : "");
			// echo "</p></td>\n";
			echo "</p></td>\n";

			$rdateQ = "SELECT MAX(RetreatDate1), MAX(RetreatDate2) FROM journal 
				WHERE YEAR(Date) = YEAR(CURDATE()) AND Category = 'retreat' AND ClientID = " . $row['clientID'];
			// echo $rdateQ;
			$rdateR = mysql_query($rdateQ);
			list($rdate1, $rdate2) = mysql_fetch_row($rdateR);
			
			if ($rdate1 == NULL || $rdate1 == '0000-00-00') {  
				$prdateQ = "SELECT RetreatDate FROM clients WHERE id = " . $row['clientID'];
				$prdateR = mysql_query($prdateQ);
				list($prdate) = mysql_fetch_row($prdateR);
				if ($prdate != '0000-00-00') {
					$spit = "&#10004; ".date('m/d', strtotime($prdate));
					$color = 'green';
				} else {
					$spit = '<b>&times;</b>';
					$color = 'red';
				}
			} elseif($rdate1 != '0000-00-00') {
				$spit = "&#10004; ".date('m/d', strtotime($rdate1));
				$color = 'green';
			} else {
				$spit = '<b>&times;</b>';
				$color = 'red';
			}
			echo "<td align='left' style='color:$color'>$spit</td>";



			echo "<td align='center'>" . date('m/d/y', strtotime($row['date'])) . "</td>\n";
			echo "<td>";
			if ((($userinfo['admin'] == 1) || ($row['staffID'] == $userinfo['id'])))
				echo "<p style='font-size:0.85em;'>
					<a href='entry.php?jid=".$row['jid']."&clientID=".$row['clientID']."'>[edit]</a>  ";
				if ($single == True) 
					echo "<a href='javascript:void(0);' name='delete' value='delete' 
						onClick='confirmclick(".$row['clientID'].",".$row['jid'].")'>[x]</a>";			
			echo "</p></td>";
			echo "</tr>\n";
		}
echo "</tbody>\n</table>\n</div>\n";
} else {
			echo "<div class='empty'><h1>No results</h1>";
			echo "<p>Choose a Client or Consultant from the pulldown menus to view data.  <em>Or...</em></p>
				<h2><a href=\"entry.php\">Start Tracking</a></h2></div>\n";
}
echo "</div>";

//debug_p($_REQUEST, "all the data coming in");
?>
</div> <!-- wrapper -->
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
