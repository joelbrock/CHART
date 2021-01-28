<?php
require("mysql_connect.php");
$reportID=1;
$get_r=mysqli_query($dbc, ("SELECT * FROM report_content WHERE id='$reportID'");
$report=mysqli_fetch_assoc($get_r);
if (($_POST['submit'])) {
	$cols[]='`content-1`';$vals[]=mysqli_real_escape_string($dbc, $_REQUEST['content-1']);
	$cols[]='`content-2`';$vals[]=mysqli_real_escape_string($dbc, $_REQUEST['content-2']);
	$cols[]='`content-3`';$vals[]=mysqli_real_escape_string($dbc, $_REQUEST['content-3']);
	$cols[]='`intro_default`';$vals[]=mysqli_real_escape_string($dbc, $_REQUEST['intro_default']);
	if(!empty($report)){
		$upd=db_send($cols,$vals,'report_content','update',"id='$reportID'",0);//die();
	} else {
		$ins=db_send($cols,$vals,'report_content','insert','',0);
		$reportID=mysql_insert_id();
	}//die();
		header('Location: report_profile.php');
} else{ 
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
</head>
<body>

<?php

include_once('topbar.php');

echo "<br /><br />";
	
echo "<h2>Report Profile</h2>";

?>
<div id="wrapper">
	<div id="client_profile">
		<form method='POST' action='<?php echo $_SERVER['PHP_SELF']?>' style="width:680px;" class='aligncenter' autocomplete="off">


		<div class="form_field">
			<h3>Default intro text</h3>
			<textarea name="intro_default" cols="80" rows="3"><?php echo @$report['intro_default']?></textarea>
		</div>
		<br>
		<h3>Due to limitations to text-formatting in the PDF report generator, we are instead using preformatted short-tags to add custom messages to the reports.  For changes / updates to these please contact <a href="mailto:techsupport@cdsconsulting.coop">support</a>.</h3>
		<h3>Available short tags:  <strong>%PROGRAM%</strong> ,  <strong>%LATEST%</strong> ,  <strong>%SCHEDULE%</strong>,  <strong>%CONNECTIONS%</strong></h3>
		<div style="float:left; margin:52px 0 10px 0;"><img src="images/report_image.jpg" alt="" width="220" border="0" /></div>
		<div class="form_field" style="float:right; margin-left:0;">
			<h3>Content Block 1</h3>
			<textarea name="content-1" cols="60" rows="10"><?php echo @$report['content-1']?></textarea>
		</div>
		<div class="form_field" style="float:right; margin-left:0;">
			<h3>Content Block 2</h3>
			<textarea name="content-2" cols="60" rows="10"><?php echo @$report['content-2']?></textarea>
		</div>
		<div class="form_field" style="float:right; margin-left:0;">
				<h3>Content Block 3</h3>
				<textarea name="content-3" cols="60" rows="10"><?php echo @$report['content-3']?></textarea>
				<br><br>
				<input type="hidden" name="reportID" value="<?php echo @$reportID?>" />
				<input type="submit" name="submit" value="Save >>">
		</div>
		<p><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></p>
		</form>
	</div>
</div>
<?
 
// echo "<input type=\"submit\" name=\"submit\" value=\"Add\">\n";

?>

</body>
</html>
<? }?>