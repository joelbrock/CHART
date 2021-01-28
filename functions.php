<?php
////////////////////////////////////////////////////////
//
//		FUNCTIONS GO HERE. . . .
//
////////////////////////////////////////////////////////

function thisQ() {
	if (is_int(date('n')/3)) $qtr = date('n')/3;
	else $qtr = floor(date('n')/3);
	if (date('z') < 14) $qtr = 4;	

	return $qtr;
}
 
// Create a function for escaping the data.
function escape_data ($data) {
	global $dbc;
	
	// Address Magic Quotes.
	if (ini_get('magic_quotes_gpc')) {
		$data = stripslashes($data);
	}
	
	// Check for mysql_real_escape_string() support.
	if (function_exists('mysql_real_escape_string')) {
		global $dbc; // Need the connection.
		$data = mysqli_real_escape_string (trim($data), $dbc);
	} else {
		$data = mysqli_escape_string (trim($data));
	}
	
	// Return the escaped value.
	return $data;
} // End of function.
function db_send($cols,$vals,$table,$type,$where='',$display='') {
	global $dbc;
	if($type=='insert'){
		$c_sql=implode(',',$cols);
		$v_sql='';
		$i=0;
			while(isset($vals[$i])){
				if($i>0) $v_sql.=',';
				if($vals[$i]=='NULL')
					$v_sql.="".$vals[$i]."";
				else
					$v_sql.="'".$vals[$i]."'";
				$i++;
			}
		$sql="INSERT INTO $table ($c_sql) VALUES ($v_sql)";
	} elseif($type=='update' && $where!=''){
		$v_sql='';
		$i=0;
			while(isset($vals[$i])){
				if($i>0) $v_sql.=',';
				$v_sql.=$cols[$i]."=".($vals[$i]=='NULL'?$vals[$i]:"'".$vals[$i]."'");
				$i++;
			}
		$sql="UPDATE $table SET $v_sql WHERE $where";
	} elseif($type=='delete'){
		if(!is_array($cols))
			$sql="DELETE FROM $table WHERE $cols=$vals";
		else {
			$sql="DELETE FROM $table WHERE ";
			$i=0;
			while(isset($vals[$i])){
				$sql.=($i>0?" AND ":'').$cols[$i]."=".($vals[$i]=='NULL'?$vals[$i]:"'".$vals[$i]."'");
				$i++;
			}
		}
	}
	
	if($display==1) echo $sql;
	return mysqli_query($dbc, $sql);
}


function short_name($name) {
	$words = explode(" ", $name);
	$first = $words[0];
	$last = $words[1];
    $init = strtoupper(substr($last, 0, 1));
	$string = $first . " " . $init . ".";
	return $string;
}

function debug_p($var, $title) 
{
    print "<p>$title</p><pre>";
    print_r($var);
    print "</pre>";
}
/*== getSlug() ==
	returns an SEO-friendly string for use in a URL
====*/
function getSlug($text)
{
	$text=str_replace(' ','-',strtolower(trim($text)));
	return ereg_replace("[^A-Za-z0-9-]", "", str_replace(' ','-',$text));
}

function client_health($client) {
	global $dbc;
	
	$thisQ = ceil(date('n')/3);
	
	$query = "SELECT j.ClientID as cID, SUM(j.Hours) as hours, (CASE WHEN c.total_hours = 0 THEN 15 ELSE c.total_hours END) AS total, COUNT(*) as count
		FROM journal j, clients c WHERE j.ClientID = c.id AND j.ClientID = ". $client . " AND YEAR(j.Date) = " . date('Y') . " AND j.Billable = 1 GROUP BY cID";
	// echo $query;
	$result = mysqli_query($dbc, $query);
	$row = mysqli_fetch_array($result);
	
	$hours = (!$row['hours'])?1:$row['hours'];
	$rtotal = (!$row['total'])?1:$row['total'];
	// echo "hours: $hours / total: $rtotal";
	// $tot = ($row['total'] == 0.00) ? 15 : $row['total'];
	// $total = ($tot / 4) * $thisQ;
	// $sub = $hours / $total;
	$tot = $hours / $rtotal;
	$mos = date('m') / 12;
	$ct = $row['count'] * $mos;
	$sub = $tot / $mos;
	// echo "Q: $thisQ, mos: $mos, hours: $hours, tot: $tot, total: ".$row['total'].", count: ".$row['count'].", ct: $ct, sub: $sub";

	if ($sub < .5) {
		$health = 1;
	} elseif ($sub >= .66) {
		$health = 3;
	} elseif ($sub < 0.66) {
		$health = 2;
	} else {
		$health = 0;
	}

	// if ($sub > 1.2) {
	// 	$health = 1;
	// } elseif ($sub <= 1.2 && $sub >= 0.8) {
	// 	$health = 3;
	// } elseif ($sub < 0.8) {
	// 	$health = 2;
	// }

	return $health;
}

function client_health_dots($client,$size) {
	$health = client_health($client);
	switch ($health) {
		case 1:
			$health_status = "red";
			break;
		case 2:
			$health_status = "yellow";
			break;
		case 3:
			$health_status = "green";
			break;
		case 4:
			$health_status = "no";
			break;
	}
	// $dot_div = "<div class='".$health_status."_dot'></div>";
	$dot_div = "<img src=\"images/" . $health_status . "dot.gif\" alt=\"health\" width=\"$size\" height=\"$size\" border=\"0\" /><font style='display:none;'>$health</font>\n";
	return $dot_div;
}
function client_health_color($client) {
	$health = client_health($client);
	switch ($health) {
		case 1:
			$health_status = "red";
			break;
		case 2:
			$health_status = "yellow";
			break;
		case 3:
			$health_status = "green";
			break;
	}

	return $health_status;
}

function contact_pattern($client) {
	global $dbc;
	// $client = ($client)?$client:$_POST['id'];
	// print_r($client);
	// echo $client[0];
	$health = 0;
	$thisQ = ceil(date('n')/3);
	$countQ = "SELECT SUM(Hours) AS total, COUNT(Hours) AS ct FROM journal WHERE ClientID = " . $client . " AND YEAR(Date) = " . date('Y');
	// echo $countQ;
	$countR = mysqli_query($dbc, $countQ);
	$row = mysqli_fetch_assoc($countR);
	$count = ($row['ct']=='' || $row['ct']==0) ? 1 : $row['ct'];
	$total = $row['total'];
	
	// $sub = ($thisQ * 3) / $count;
	$sub = ($count / 12) / (date('m') / 12);
	return $sub;
}

function contact_pattern_dots($client,$size) {
	$sub = round(contact_pattern($client),2);
	// echo $sub;
	if ($sub < 0.34) {
		$health = 1;
		$health_status = "red";
	} elseif ($sub >= 0.75) {
		$health = 3;
		$health_status = "green";
	} elseif ($sub > 0.50 && $sub < 0.75) {
		$health = 2;
		$health_status = "yellow";
	} else {
		$health = 0;
		$health_status = "no";
	}
	$dot_div = "<img src=\"images/" . $health_status . "dot.gif\" alt=\"health\" width=\"$size\" height=\"$size\" border=\"0\" /><font style='display:none;'>$health</font>\n";
	return $dot_div;
	// return $sub;
}

function mini_dash($client,$align) {
	global $dbc;
	$thisQ = ceil(date('n')/3);
	$thisY = date('Y');
	if (date('z') < 15) {
		$thisQ = 4;
		$thisY = $thisY - 1;
	}
	$hoursq = "SELECT SUM(Hours) FROM journal WHERE ClientID = " . $client . " 
		AND Billable = 1 AND YEAR(Date) = " . $thisY;
	$hoursr = mysqli_query($dbc, $hoursq);
	$hours = mysqli_fetch_row($hoursr);
	$hoursTotal = $hours[0];
	if ($hoursTotal >= ($row['q_hours']/3) && $hoursTotal < (2*$row['q_hours']/3)) { $fcolor = '#ff9900'; }
	if ($hoursTotal >= (2*$row['q_hours']/3)) { $fcolor = '#cc0033'; }
	
	$totr = mysqli_query($dbc, "SELECT * FROM clients WHERE ID = $client");
	$tots = mysqli_fetch_assoc($totr);
	$tot = $tots['total_hours'];
	$hoursleft = $tot - $hoursTotal;
	// echo $tot . " - " . $hoursTotal;
	echo "<table border=0 align='$align' width=350 valign='top'><tr>";
	echo "<td align='$align'><span class='label'>$thisY (Q$thisQ YTD) Hrs. Used: </span><span class='resp' style='color: $fcolor;'><b>" . round($hoursTotal,2) . 
		"</b></span><span class='label'> of </span><span class='resp'><b>" . round($tot,2) . "</b></span> / <span class='resp' style='color: $fcolor;'><b>"
		 . round($hoursleft,2) . "</b></span> <span class='label'>remain</span></td>";
	echo "</tr><tr>";
	echo "<td align='$align'><span class='label'>Contact pattern: </span><span class='resp'><b>" . contact_pattern_dots($client,15) . "</b></span></td>";
	echo "</tr></table>";
}
function acronymize($string) {
	$words = explode(" ", $string);
	$letters = "";
	foreach ($words as $value) {
	    $letters .= strtoupper(substr($value, 0, 1));
	}
	return $letters;
}

?>
