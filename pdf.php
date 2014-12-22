<?php
require("mysql_connect.php");
// require($_SERVER['DOCUMENT_ROOT'].'/library/fpdf/fpdf.php');
require('../library/fpdf/fpdf.php');
$clientID = $_REQUEST['clientID'];
$staffID = $_REQUEST['staffID'];
$reportID=1;

$rptquery = mysql_query("SELECT * FROM report_content c WHERE c.id = " . $reportID . " LIMIT 1");
$rc=mysql_fetch_assoc($rptquery);

// $thisQ = ($_GET['thatQ']) ? $_GET['thatQ'] : $thisQ;
// $thatY = ($_GET['thatY']) ? $_GET['thatY'] : date('Y');

class PDF extends FPDF
{
//Page header
function Header()
{
	global $client,$thisQ;
	//Logo
	$this->Image('../images/pdf_header.png',5,5,200);
	$this->Ln(32);
}

//Page footer
function Footer()
{
	//Position at 1.5 cm from bottom
	$this->SetY(-15);
	//Arial italic 8
	// $this->SetFont('Arial','I',8);
	//Page number
	// $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	// $this->Line(10,$this->GetY(), 190, $this->GetY());
}
function jEntryIntro($data,$rc,$in){
	$clientID = $_REQUEST['clientID'];
	$intro_default = "Here is your CBLD quarterly report.  Please have a look and let me know if you have any questions.  And keep up the great work!";
	
	global $client,$thisQ;
	$introq = "SELECT Intro FROM journal WHERE Category = 'quarterly' AND ClientID = " . $clientID . " 
		AND YEAR(Date) = '".$_GET['thatY']."' AND QUARTER(Date) = '".$_GET['thatQ']."'
		ORDER BY Date DESC LIMIT 1";
	$intror = mysql_query($introq);
	// echo $introq;
	$row = mysql_fetch_row($intror);
	
	$this->Ln(6);
	
	$this->Cell($in);
	$this->SetFont('Arial','',12);
	if ($row[0] == '' || (!$row[0])) {
		$this->Write(6, $intro_default);
		$this->Ln(6);
	} else {
		$this->MultiCell(175, 5.25, stripslashes($row[0]));
	
		// 
		// if($data['Category']=='quarterly'){
		// 	$this->Write(20,$data['Intro']);
		// } else {
		// 	$this->Write(20, $rc['intro_default']);
		// }
	}
}
function jEntry($k,$data,$in){
	global $thisQ;
	$printQ = (!isset($_GET['thatQ'])) ? thisQ() : $_GET['thatQ'];
	
	if($data['Category']=='quarterly' && $data['Quarterly'] != ""){
		$this->Write(10, "Quarterly Note:");
		$this->Ln(9);
		$this->Cell($in);
		$this->SetFont('Arial','',12);
		// $this->Write(10,$data['Quarterly']);
		$this->MultiCell(175,5.25,stripslashes($data['Quarterly']));
		$this->Ln(6);

	} else {//this is a call or research entry
		if($k==0){			
			$this->SetFont('Arial','B',12);
			$this->Cell(0,12,'Q' . $printQ . ' Note:',0,1);	
			$this->Ln(6);
			
		}
		// $this->Cell($in);
		// $this->SetLeftMargin($in);
		$this->SetFont('Arial','',10);
		if ($data['QtrInc'] == 1) {
			$hr = ($data['Hours'] == 1)?"hr":"hrs";
			// $this->SetFont('Arial','B',10);
			// $this->Write(8,$data['created_fmt'].' - '.$data['Hours'] . $hr . ' - ');
			$this->SetFont('Arial','',10);
			// $this->Write(8,$data['ClientNote']);
			$this->MultiCell(175,5.25,strftime("%D",strtotime($data['Date'])).' - '
				.$data['Hours'] . $hr . ' - ' .stripslashes($data['ClientNote']));
			$this->Ln(3);
		}
		// $this->Ln(10);
	}
	// $this->Ln(10);
}
function jEntries($hours,$in){
	foreach($hours as $k=>$h){
		$this->jEntry($k,$h,$in);
		// $this->Ln(-3);
	}
}
function content_filter($rc) {
	$pattern0 = '/%LATEST%/';
	$pattern1 = '/%SCHEDULE%/';
	$pattern2 = '/%PROGRAM%/';
	$pattern3 = '/%CONNECTIONS%/';
	


	if (preg_match($pattern0, $rc)) {
		// $this->Write( 2, $replace);
		$this->SetFont('Arial','B',11);
		$this->Write(11,"The Newest Resources Available to You on the ");
		$this->PutLink("http://cdsconsulting.coop/cbldlibrary", "CBLD Library");
		$this->Ln(7);
		$this->SetFont('Arial','B',10);
		$this->Cell(8);
		$this->PutLink("http://cdsconsulting.coop/newsletter/article/1481", "Featured: Share Values and Grow Community");
		$this->Ln(7);	
		$this->Cell(8);
		$this->PutLink("http://cdsconsulting.coop/newsletter/article/1471", "Featured: The Co-op Difference Starts with Staff");
		$this->Ln(7);
		$this->SetFont('Arial','B',10);
		$this->Cell(8);
		$this->PutLink("http://cdsconsulting.coop/newsletter/article/1462", "Featured: Peoples Expands: A Tale of Two Co-ops");
		$this->Ln(7);
		$this->SetFont('Arial','B',10);
		$this->Cell(8);
		$this->Write(11,"And ");
		$this->PutLink("http://www.cdsconsulting.coop/co-op_cafe", "dozens of short video recordings");
		$this->Write(11," from the Cooperative Cafes");
		$this->Ln(3);
		$this->Cell(8);
		$this->Write(11," focused on growing our co-ops.");
		$this->Ln(7);
	
	} elseif (preg_match($pattern3, $rc)) {
		$this->SetFont('Arial','B',11);
		$this->Write(11,"New Connections Articles -- from the ");
		$this->PutLink("http://www.cdsconsulting.coop/newsletter", "Connections Archive");
		$this->Ln(6);
		$this->SetFont('Arial','B',10);
		$this->Cell(8);
		$this->PutLink("http://cdsconsulting.coop/newsletter/article/1446", "It's Time to Pull Out Your 2015 Calendars!");
		$this->Ln(4);
		$this->Cell(8);
		$this->SetFont('Arial','I',9);
		$this->Write(10,"September, 2014");
		$this->Ln(6);
		$this->SetFont('Arial','B',10);
		$this->Cell(8);
		$this->PutLink("http://cdsconsulting.coop/newsletter/article/1479", "Improve Trust and Teamwork Throughout Growth");
		$this->Ln(4);
		$this->Cell(8);
		$this->SetFont('Arial','I',9);
		$this->Write(10,"December, 2014");
		$this->Ln(6);
		$this->SetFont('Arial','B',10);
		$this->Cell(8);
		$this->PutLink("http://cdsconsulting.coop/newsletter/article/1480", "Great Idea: Thriving in the New Competitive Landscape-Co-op Cafe 2015");
		$this->Ln(4);
		$this->Cell(8);
		$this->SetFont('Arial','I',9);
		$this->Write(10,"December, 2014");
		$this->Ln(6);
		$this->SetFont('Arial','B',10);
		$this->Cell(8);
		$this->PutLink("http://cdsconsulting.coop/newsletter/article/1470", "Great Idea: Board President Succession Planning");
		$this->Ln(4);
		$this->Cell(8);
		$this->SetFont('Arial','I',9);
		$this->Write(10,"November, 2014");

			
	} elseif (preg_match($pattern1, $rc)) {
		$this->Image('images/2015_events_grid.png',12,145,180);
		
	} elseif (preg_match($pattern2,$rc)) {
		$this->SetFont('Arial','B',11);
		$this->Write(11,"The CBLD Program Includes");
		$this->Ln(6);
		$this->SetFont('Arial','',10);
		$this->Cell(8);
		$this->Write(10,"Ongoing support of up to 15 hours of consulting time");
		$this->Ln(5);
		$this->Cell(8);
		$this->Write(10,"Planning and facilitating a one-day retreat");
		$this->Ln(5);
		$this->Cell(8);
		$this->Write(10,"In-person CBL 101s");
		$this->Ln(5);
		$this->Cell(8);
		$this->Write(10,"CBLD Resources");
		// $this->Ln(4);
		
	} else {
		$this->Write( 6, $rc);
	}
}
function event_attendance($client) {
	global $userinfo;
	$clientID = $_REQUEST['clientID'];
	$printY = (!isset($_GET['thatY'])) ? date('Y') : $_GET['thatY'];
	
	$this->SetFont('Arial','B',12);
	$this->Cell( 45, 12, "In-Person Event Attendance", 0, 0, 'L' );
	$this->SetFont('Arial','',12);
	$this->Ln(6);
	$this->Cell( 50, 12, "CBL 101: ", 0, 0, 'L' );
	$this->SetFont('Arial','B',12);
	// $cblQ = "SELECT a.coop, a.lastname FROM attendance a, clients c WHERE a.event = 'CBL' AND a.year = $printY AND a.att <> '' 
	// 	AND SUBSTR( a.coop, 1, LENGTH( c.name ) ) =  '".$client['name']."' GROUP BY a.id";
	$cblQ = "SELECT a.coop, a.lastname FROM attendance a, clients c WHERE a.event = 'CBL' AND a.year = $printY AND a.att <> '' 
		AND a.clientID = $clientID GROUP BY a.id";
	$cblR = mysql_query($cblQ);
	$attCBL = (mysql_num_rows($cblR)==0) ? "None" : mysql_num_rows($cblR);
	$this->Cell( 20, 12, "$attCBL", 0, 0, 'L');
	$this->Ln(6);
	$this->SetFont('Arial','',12);
	$this->Cell( 50, 12, "Leadership Training: ", 0, 0, 'L' );
	$this->SetFont('Arial','B',12);
	// $ltQ = "SELECT a.coop, a.lastname FROM attendance a, clients c WHERE a.event = 'LT' AND a.year = $printY AND a.att <> ''
	// 	AND SUBSTR( a.coop, 1, LENGTH( c.name ) ) =  '".$client['name']."' GROUP BY a.id"; 
	$ltQ = "SELECT a.coop, a.lastname FROM attendance a, clients c WHERE a.event = 'LT' AND a.year = $printY AND a.att <> ''
		AND a.clientID = $clientID GROUP BY a.id"; 
	$ltR = mysql_query($ltQ);
	$attLT = (mysql_num_rows($ltR)==0) ? "None" : mysql_num_rows($ltR);
	$this->Cell( 20, 12, "$attLT", 0, 0, 'L');
	$this->Ln(6);
	$this->SetFont('Arial','',12);
	$this->Cell( 50, 12, "Cooperative Cafe: ", 0, 0, 'L' );
	$this->SetFont('Arial','B',12);
//	$scsQ = "SELECT a.coop, a.lastname FROM attendance a, clients c WHERE a.event = 'CC' AND a.year = $printY AND a.att <> ''
//		AND SUBSTR( a.coop, 1, LENGTH( c.name ) ) =  '".$client['name']."' GROUP BY a.id"; 
	$scsQ = "SELECT a.coop, a.lastname FROM attendance a, clients c WHERE a.event = 'CC' AND a.year = $printY AND a.att <> ''
		AND a.clientID = $clientID GROUP BY a.id"; 
	$scsR = mysql_query($scsQ);
	$attSCS = (mysql_num_rows($scsR)==0) ? "None" : mysql_num_rows($scsR);
	$this->Cell( 20, 12, "$attSCS", 0, 0, 'L');
	$this->Ln(6);
	
}


function PutLink($URL,$txt)
{
    //Put a hyperlink
    $this->SetTextColor(0,0,255);
    $this->Write(10,$txt,$URL);
    $this->SetTextColor(0);
}
//Report
function Report($client,$filename,$dest='I')
{
	global $userinfo,$thisQ,$rc;
	$clientID = $_REQUEST['clientID'];
	$this->AliasNbPages();
	$this->AddPage();
	
	$in = 6;
	$printQ = (!isset($_GET['thatQ'])) ? thisQ() : $_GET['thatQ'];
	$printY = (!isset($_GET['thatY'])) ? date('Y') : $_GET['thatY'];
	
	$this->SetFont('Arial','B',14);
	$this->Cell( 120, 12, $client['name'], 0, 0, 'L' );
	$this->SetFont('Arial','',12);
	$this->Cell( 40, 12, "Q".$printQ."-".$printY, 0, 0, 'L' );
	$this->Cell( 40, 12, date('n/j/Y'), 0, 0, 'L' );
	$this->SetLineWidth(0.8);
	$this->Line( 10, 52 ,190 ,52 );
	$this->Ln(8);

	// $this->SetFont('Arial','',11);
	// $this->MultiCell(200, 6, $rc['content-1'], 0, 'L');
	// // $this->Ln(10);

	$this->SetFont('Arial','',12);
	// $this->Write(20, $rc['intro_default']);
	// $this->Write(20, $client['Intro']);
	$this->jEntryIntro($client,$rc,$in);
	
	$this->SetFont('Arial','B',12);
	$this->Cell( 55, 12, "Ongoing Support:", 0, 0, 'L' );
	$this->Ln(8);
	$this->Cell($in);
	$this->SetFont('Arial','',12);
	$this->Cell( 55, 12, "Hours Utilized (of ".$client['total_hours'].")", 0, 0, 'L' );
	$this->SetFont('Arial','B',14);
	$this->Cell( 40, 12, $client['hrs']['total'], 0, 0, 'L' );
	$this->SetFont('Arial','',12);
	$this->Cell( 40, 12, "Hours Remaining", 0, 0, 'L' );
	$this->SetFont('Arial','B',14);
	if ($client['hrs']['alert']=='med') { $fcolor = '#cc0033'; 
		$this->SetTextColor(204,0,51);
	}//if we are over hours
	elseif ($client['hrs']['alert']=='high') { $fcolor = '#ff9900'; 
		$this->SetTextColor(255,153,0);
	}//if 
	$this->Cell( 30, 12, $client['hrs']['R'], 0, 0, 'L' );
	$this->SetFont('Arial','',14);
	$this->SetTextColor(0,0,0);
	$this->Ln(6);

	$this->Cell($in);
	$this->SetFont('Arial','',12);
	$this->Cell( 90, 12, "Do we have an established pattern of contact? ", 0, 0, 'L' );
	$this->SetFont('Arial','B',12);
	$this->Cell( 20, 12, ($client['hrs']['cat']>=2?'yes':'no'), 0, 0, 'L');
	$this->SetFont('Arial','',12);
	$this->Ln(6);

	$this->Cell($in);
	$this->Cell( 94, 12, "On track for balanced use of hours for the year? ", 0, 0, 'L' );
	$this->SetFont('Arial','B',12);
	$this->Cell( 20, 12, ($client['hrs']['alert']=='low'?'yes':'no'), 0, 0, 'L');
	$this->SetFont('Arial','B',12);
	$this->Ln(6);

	$this->jEntries($client['hours'],$in);

	$this->Ln(4);
	
	$this->SetFont('Arial','B',12);
	$this->Cell( 45, 12, "Board Retreat:  ", 0, 0, 'L' );
	$this->SetFont('Arial','',12);

	$retQ = "SELECT RetreatDate1, RetreatDate2, RetreatNote FROM journal WHERE YEAR(Date) = YEAR(CURDATE()) AND Category = 'retreat' AND ClientID = " . $clientID . " ORDER BY Date DESC LIMIT 1";
	// echo $retQ;
	$retR = mysql_query($retQ);
	$ret = mysql_fetch_row($retR);

	if (mysql_num_rows($retR) == 1) {
		//($ret[0] != '0000-00-00' || $client['RetreatDate'] != '0000-00-00') {
		$ret_date = $ret[0];
		$longdate = strftime('%A %B %e, %Y',strtotime($ret_date));
		$this->Cell( 20, 12, $longdate, 0, 0, 'B');
	} else {
		$prdateQ = "SELECT RetreatDate FROM clients WHERE id = " . $clientID;
		$prdateR = mysql_query($prdateQ);
		list($prdate) = mysql_fetch_row($prdateR);
		if ($prdate != '0000-00-00') {
			$longdate = strftime('%A %B %e, %Y',strtotime($prdate));
			$this->Cell( 20, 12, $longdate, 0, 0, 'B');
		} else {
			$this->Cell( 20, 12, "to be decided", 0, 0, 'L');
		}
	}


	// if (!isset($client['RetreatDate']) || $client['RetreatDate'] == '0000-00-00') {
	// 	if (!isset($ret[0]) || $ret[0] == '0000-00-00')
	// 		$this->Cell( 20, 12, "to be decided", 0, 0, 'L');
	// } else {
	// 	$ret_date = ($ret[0] == '0000-00-00') ? $client['RetreatDate'] : $ret[0];
	// 	$longdate = strftime('%A %B %e, %Y',strtotime($ret_date));
	// 	$this->Cell( 20, 12, $longdate, 0, 0, 'B');
	// }


	if ($ret[2] != '') {
		$this->Ln(10);
		$this->Cell($in);
		$this->MultiCell( 175, 5.25, stripslashes($ret[2]));
	}
	$this->Ln(9);

	$this->Write(20, $this->event_attendance($client));
	$this->Ln(9);

	$this->SetFont('Arial','B',12);
	$this->Cell( 90, 12, '', 0, 0, 'L' );

	$staffnameR = mysql_query("SELECT s.firstname, s.lastname FROM staff s, staff_clients c WHERE s.id = c.StaffID AND c.ClientID = ".$clientID);
	$cons_ct = mysql_num_rows($staffnameR);
	if ($cons_ct > 1) {
		$staffnames = "";
		$i = 1;
		while($names=mysql_fetch_row($staffnameR)) {
	        $sep = ($i < $cons_ct) ? " & " : "";
	        $staffnames .= $names[0]." ".$names[1].$sep;
	        $i++;
		}
		$this->Cell( 80, 12, $staffnames, 0, 0, 'L');
		$this->Ln(7);
		$this->Cell( 90, 12, '', 0, 0, 'L' );
		$this->SetFont('Arial','I',12);
		$this->Cell( 35, 12, "CBLD Consultants", 0, 0, 'I');
	} else {
		$staffname = mysql_fetch_row($staffnameR);
		$this->Cell( 35, 12, $staffname[0]." ".$staffname[1].", CBLD Consultant", 0, 0, 'L' );
	}

	$this->Ln(7);
	$this->SetFont('Arial','B',12);
	$this->Cell( 90, 12, '', 0, 0, 'L' );
	$this->PutLink('http://cdsconsulting.coop','CDS Consulting Co-op');
	$this->Write( 10,' | ');
	$this->PutLink('http://cdssconsulting.coop/cbld','CBLD Program');
	$this->Ln(10);

	$this->AddPage();		
	
	$this->SetLineWidth(0.8);
	$this->Line( 10, $this->GetY() ,190 ,$this->GetY() );
	$this->Ln(2);
	
	$this->Image( '../images/CBLD_logo_full.gif', 152, $this->GetY(), 40 );
	$this->Image( '../images/findus.jpg', 152, $this->GetY() + 68, 40, 0, 'jpg', 'https://www.facebook.com/CDSConsultingCoop' );
	$this->Image( '../images/theCooperativeCafe-title.jpg', 152, $this->GetY() + 82, 40, 0, 'jpg', 'http://www.cdsconsulting.coop/co-op_cafe' );	
	
	// $this->SetFont('Arial','',11);
	// $this->MultiCell(200, 6, $this->content_filter($rc['content-1']), 0, 'L');
	$this->Write(20, $this->content_filter($rc['content-1']));
	$this->Ln(9);

	// $this->SetFont('Arial','',12);
	$this->Write(20, $this->content_filter($rc['content-2']));
	$this->Ln(9);

	// $this->SetFont('Arial','',12);
	$this->Write(20, $this->content_filter($rc['content-3']));
	// $this->Ln(4);
			
	$this->Output($filename,$dest);
}
}
	if(!empty($clientID)){
		if($clientID=='all'){
			$action='batch';
			$query = "SELECT * FROM clients c";
			// echo $query;
			$result = mysql_query($query);
			while($row=mysql_fetch_assoc($result)){
				$c[]=$row; 
			}
			//print_r($c);
	
		} else {
			$query = "SELECT * FROM clients c WHERE c.id = " . $clientID . " LIMIT 1";
			// echo $query;
			$action='single';
			$result = mysql_query($query);
			$c[]=$row=mysql_fetch_assoc($result); 
			//print_r($c);
			if (!$row['id']) {
				empty($clientID);
				header('Location: dashboard.php');
			} 
		}
		foreach($c as $client) {
			$clientID=$client['id'];
			$thisQ = ($_GET['thatQ']) ? $_GET['thatQ'] : $thisQ;
			$thatY = ($_GET['thatY']) ? $_GET['thatY'] : date('Y');
			$hours_ty = "SELECT SUM(Hours) FROM journal WHERE ClientID = " . $clientID . ($admin==false?" AND StaffID='{$userinfo['id']}'":'') . " AND YEAR(Date) = $thatY AND (MONTH(Date) < (".(3*($thisQ-1)+1).")) AND Category <> 'reset'";//hours used this year prior to Q
			$hours_tyr = mysql_query($hours_ty);
			$client['hours_ty'] = mysql_fetch_row($hours_tyr);
			$client['hrs']['total'] = round($client['hours_ty']['0'],2);
			$hoursq = "SELECT *, DATE_FORMAT(`Date`,'%c/%e/%Y') as created_fmt FROM journal WHERE ClientID = " . $clientID . ($admin==false?" AND StaffID='{$userinfo['id']}'":'') . " AND YEAR(Date) = $thatY AND (MONTH(Date) >= (".(3*($thisQ-1)+1).") AND MONTH(Date)<=(".(3*$thisQ).")) ORDER BY Category='quarterly' DESC, Date DESC ";
			$hoursr = mysql_query($hoursq); 
			if(mysql_num_rows($hoursr)==0)continue;
			$hoursQ=0;
			while($h=mysql_fetch_assoc($hoursr)){
				$h['Hours']=round($h['Hours'],2);
				$client['hours'][] = $h; $client['hrs']['total']+=$h['Hours']; $hoursQ+=$h['Hours'];
				$client['hrs']['cat'][$h['Category']]=(isset($client['hrs']['cat'][$h['Category']])?$client['hrs']['cat'][$h['Category']]:0)+1;
			}
			$client['hrs']['R'] = $client['total_hours']-$client['hrs']['total']; //hours remaining (yearly)
			$client['q_hours']=$client['total_hours']/4; //est hours per q
			
			if($client['hrs']['total'] > $client['total_hours'] || ($client['hrs']['total'] == $client['total_hours'] && $thisQ<4))$client['hrs']['alert']='med';
			elseif ($thisQ<4 && $client['hrs']['total'] > ($row['q_hours']*$thisQ) && $client['hrs']['total'] < (2*$row['q_hours']/3)) $client['hrs']['alert']='high'; 
			else $client['hrs']['alert']='low';
				$filename=($action=='batch'?$_SERVER['DOCUMENT_ROOT'].'/reports/':'').'CBLD_'.$thatY.'_Q'.thisQ().'-'.getSlug($client['name']).'.pdf'; if($action=='batch')$filenames[]=$filename;
				$pdf=new PDF();
				$pdf->Report($client,$filename,($action=='batch'?'F':'I'));

			}
			if($action=='batch'){
			require_once($_SERVER['DOCUMENT_ROOT'].'/library/pclzip.lib.php');
			$zname='CBLD-Q'.$thisQ.'-all.zip'; $zname_full=($action=='batch'?$_SERVER['DOCUMENT_ROOT'].'/reports/':'').$zname;
			  $archive = new PclZip($zname_full);
			  $v_list = $archive->create($filenames,PCLZIP_OPT_REMOVE_PATH, $_SERVER['DOCUMENT_ROOT'].'/reports/');
				if ($v_list > 0) {
					header("Content-type: application/octet-stream");
					header("Content-disposition: attachment; filename=$zname");
					readfile($zname_full);			
				} else {
					die ("An error occurred.");
				}
			}
	}


?>