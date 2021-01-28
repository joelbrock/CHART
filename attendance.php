<?php require("mysql_connect.php"); 

$get_c=mysqli_query($dbc, ("SELECT * FROM clients WHERE active = 1 ORDER BY name ASC") or die(mysql_error());
while($c=mysqli_fetch_assoc($get_c)) $c_meta[$c['id']]=$c;

if ($_POST['submit']) {
	if($_POST['remove']==1){
		$del=mysqli_query($dbc, ("INSERT INTO event_attendance VALUES('{$_POST['clientID']}', '{$_POST['eventid']}', -1)");
		if($del) $msg='success'; else $msg='error';
		die($msg);
	} elseif($_POST['add']==1) {
		$add=mysqli_query($dbc, ("INSERT INTO event_attendance VALUES('{$_POST['clientID']}', '{$_POST['eventid']}', 1)");
		if($add) $msg='success'; else $msg='error';
		die($msg);
	} else {
		echo "<font color=red>ERROR!</font>";
		print mysql_error();
	}
}



?>
<?php include_once('topbar.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
	$('#att').fixedHeaderTable({
		footer: true,
		altClass: 'altrow',
		cloneHeadToFoot: true,
		autoShow: true
	});	
	
	$("form div").append('<div class="inc button">+</div><div class="dec button">-</div>');

    $(".button").click(function() {
        var $button = $(this);
        var oldValue = $button.parent().find("input").val();
    
        if ($button.text() == "+") {
    	  var newVal = parseFloat(oldValue) + 1;
    	  // AJAX save would go here
    	} else {
    	  // Don't allow decrementing below zero
    	  if (oldValue >= 1) {
    	      var newVal = parseFloat(oldValue) - 1;
    	      // AJAX save would go here
    	  }
    	}
    	$button.parent().find("input").val(newVal);
    });
});
</script>

<br /><br /><br /><br /><br />
<div id="wrapper">
<?php
$staffID = $_REQUEST['staffID'];
// $arr = array();
$s = mysqli_query($dbc, ("SELECT * FROM clients WHERE active = 1 ORDER BY name ASC") or die(mysql_error());
// while ($sq = mysqli_fetch_assoc($s)) {
	// array_push($arr,$sq['clientID']);
// }

// $arr0 = implode(",",$arr);
// print_r($arr0);
// if(mysqli_num_rows($s)>0)$row=mysqli_fetch_array($s);
// $q = "SELECT * FROM clients WHERE id IN (" . $arr0 . ")";
// $qr = mysqli_query($dbc, ($q);

// echo "<form action='' method='post'>";
echo "<table id='att' border=1 cellspacing=0 cellpadding=4>\n<thead>\n";
echo "<tr><th>ID</th><th>&nbsp;</th>";
$vq = mysqli_query($dbc, ("SELECT * FROM events ORDER BY eventid");
while ($event = mysqli_fetch_assoc($vq)) {
	echo "<th>". substr($event['event_name'],0,4) . substr($event['event_name'],-4) ."</th>";
}
echo "</tr></thead>\n<tbody>";
while ($row = mysqli_fetch_assoc($s)) {
	echo "<tr>\n<td>".$row['id']."</td>\n<td>".$row['name']."</td>\n";
	
	$vq = mysqli_query($dbc, ("SELECT * FROM events ORDER BY eventid");
	while ($event = mysqli_fetch_assoc($vq)) {
		$e = "SELECT SUM(count) FROM event_attendance WHERE coop = ".$row['id']." AND eventid = " . $event['eventid'];
		$eq = mysqli_query($dbc, ($e);
		$er = mysqli_fetch_row($eq);
		$class = $row['id']."-".$event['eventid'];
		$val = (!$er[0])?0:$er[0];
		echo "<td align='left'><div><input type='text' name='$class' id='$class' value='$val' size=3 /></div>";
		echo "</td>\n";
	}
	
	echo "</tr>\n";
}
?>
</tbody>
</table>
<!-- </form> -->
</div>

</body>
</html>