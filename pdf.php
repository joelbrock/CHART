<?php
use setasign\Fpdi\Fpdi;

require("mysql_connect.php");
require_once('fpdf/fpdf.php');
// require_once('fpdf/fpdf_merge/fpdf_merge.php');
require_once('fpdi/src/autoload.php');

$clientID = $_REQUEST['clientID'];
$staffID = $_REQUEST['staffID'];
$reportID=1;

$rptquery = mysqli_query($dbc, "SELECT * FROM report_content c WHERE c.id = " . $reportID . " LIMIT 1");
$rc=mysqli_fetch_assoc($rptquery);

// $thisQ = ($_GET['thatQ']) ? $_GET['thatQ'] : $thisQ;
// $thatY = ($_GET['thatY']) ? $_GET['thatY'] : date('Y');

class PDF extends FPDF
{
	protected $_tplIdx;

	//Page header
	public function Header()
	{
		// global $client,$thisQ;
		//Logo
		// $this->Image('images/pdf_header.png',5,5,200);
		// $this->Ln(32);
	}

	//Page footer
	public function Footer()
	{
		//Position at 1.5 cm from bottom
		// $this->SetY(-15);
		// $this->SetFont('Arial','',8);
		//Page number
		// $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
		// $this->Line(10,$this->GetY(), 190, $this->GetY());
	}

	protected function jEntryIntro($data,$rc,$in)
	{
		$clientID = $_REQUEST['clientID'];
		$intro_default = "Here is your CBLD quarterly report.  Please have a look and let me know if you have any questions.  And keep up the great work!";

		global $dbc,$client,$thisQ;
		$introq = "SELECT Intro FROM journal WHERE Category = 'quarterly' AND ClientID = " . $clientID . "
			AND YEAR(Date) = '".$_GET['thatY']."' AND QUARTER(Date) = '".$_GET['thatQ']."'
			ORDER BY Date DESC LIMIT 1";
		$intror = mysqli_query($dbc, $introq);
		// echo $introq;
		$row = mysqli_fetch_row($intror);

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
	protected function jEntry($k,$data,$in){
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
	protected function jEntries($hours,$in){
		foreach($hours as $k=>$h){
			$this->jEntry($k,$h,$in);
			// $this->Ln(-3);
		}
	}
	protected function content_filter($rc) {
		$pattern0 = '/%LATEST%/';
		$pattern1 = '/%SCHEDULE%/';
		$pattern2 = '/%PROGRAM%/';
		$pattern3 = '/%CONNECTIONS%/';

		if (preg_match($pattern0, $rc)) {
			// $this->Write( 2, $replace);
			$this->SetFont('Arial','B',11);
			$this->Write(11,"The Newest Resources Available to You on the ");
			$this->PutLink("http://library.columinate.coop/", "CDS CC Library");
			$this->Ln(7);
			$this->SetFont('Arial','B',10);
			$this->Cell(8);
			$this->PutLink("http://library.columinate.coop/policy-register-template-users-guide/", "Policy Register Template Users' Guide");
			$this->Ln(7);
			$this->Cell(8);
			$this->PutLink("http://library.columinate.coop/video-field-guide-membership-growth/", "Video Field Guide on Membership Growth"); 
			$this->Ln(7);
			$this->SetFont('Arial','B',10);
			$this->Cell(8);
			$this->PutLink("http://library.columinate.coop/strategic-alliances-and-consolidations-exploring-cooperation-among-cooperatives/", "Strategic Alliances and Consolidations");
			$this->Ln(7);
			$this->SetFont('Arial','B',10);
			$this->Cell(8);
			$this->Write(11,"And ");
			$this->PutLink("http://library.columinate.coop/category/cooperative-cafe/", "over 200 short video recordings");
			$this->Write(11," from the Cooperative Cafes");
			$this->Ln(3);
			$this->Cell(8);
			$this->Write(11," focused on Courageous Leadership.");
			$this->Ln(7);

		} elseif (preg_match($pattern3, $rc)) {
			$this->SetFont('Arial','B',11);
			$this->Write(11,"New Connections Articles -- from the ");
			$this->PutLink("http://library.columinate.coop/category/articles/connections/", "Connections Archive");
			$this->Ln(6);
			$this->SetFont('Arial','B',10);
			$this->Cell(8);
			$this->PutLink("http://library.columinate.coop/so-our-co-ops-are-mostly-white-now-what/", "So Our Co-ops are Mostly White, Now What?");
			$this->Ln(4);
			$this->Cell(8);
			$this->SetFont('Arial','I',9);
			$this->Write(10,"August, 2016");
			$this->Ln(6);
			$this->SetFont('Arial','B',10);
			$this->Cell(8);
			$this->PutLink("http://library.columinate.coop/what-is-board-holism-or-speaking-with-one-voice/", "What is Board Holism Or Speaking with One Voice?");
			$this->Ln(4);
			$this->Cell(8);
			$this->SetFont('Arial','I',9);
			$this->Write(10,"October, 2016");
			$this->Ln(6);
			$this->SetFont('Arial','B',10);
			$this->Cell(8);
			$this->PutLink("http://library.columinate.coop/including-members-in-the-ends-dialogues/", "Including Members in the Ends Dialogues");
			$this->Ln(4);
			$this->Cell(8);
			$this->SetFont('Arial','I',9);
			$this->Write(10,"November, 2016");
			$this->Ln(6);
			$this->SetFont('Arial','B',10);
			$this->Cell(8);
			$this->PutLink("http://library.columinate.coop/co-op-fair-brings-community-together/", "Co-op Fair Brings Community Together");
			$this->Ln(4);
			$this->Cell(8);
			$this->SetFont('Arial','I',9);
			$this->Write(10,"November, 2016");


		} elseif (preg_match($pattern1, $rc)) {


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
	protected function event_attendance($client) {
		global $dbc,$userinfo;
		$clientID = $_REQUEST['clientID'];
		$printY = (!isset($_GET['thatY'])) ? date('Y') : $_GET['thatY'];

		$this->SetFont('Arial','B',12);
		$this->Cell( 45, 12, "Event Registrations", 0, 0, 'L' );
		// $this->Ln(6);
		// $this->SetFont('Arial','I',10);
		// $this->Write(12, "NOTE: Being a Great Employer 6/30 event attendance data will be available on the Q3 report.");
		
		$this->SetFont('Arial','',12);
		$this->Ln(6);
		$this->Cell( 60, 12, "Virtual CBL 101: ", 0, 0, 'L' );
		$this->SetFont('Arial','B',12);
		// $cblQ = "SELECT a.coop, a.lastname FROM attendance a, clients c WHERE a.event = 'CBL' AND a.year = $printY AND a.att <> ''
		// 	AND SUBSTR( a.coop, 1, LENGTH( c.name ) ) =  '".$client['name']."' GROUP BY a.id";
		$cblQ = "SELECT a.coop, a.lastname FROM attendance a, clients c WHERE (a.event = 'CBL' OR a.event = '101') AND a.year = $printY
			AND a.clientID = $clientID GROUP BY a.id";
		$cblR = mysqli_query($dbc, $cblQ);
		$attCBL = (mysqli_num_rows($cblR)==0) ? "None" : mysqli_num_rows($cblR);
		$this->Cell( 20, 12, "$attCBL", 0, 0, 'L');
		$this->Ln(6);
		$this->SetFont('Arial','',12);
		$this->Cell( 60, 12, "CBLD Topical Webinars: ", 0, 0, 'L' );
		$this->SetFont('Arial','B',12);
		$ltQ = "SELECT a.coop, a.lastname FROM attendance a, clients c WHERE (a.event = 'TOP' OR a.event = 'WEB') AND a.year = $printY 
			AND a.clientID = $clientID GROUP BY a.id";
		$ltR = mysqli_query($dbc, $ltQ);
		$attLT = (mysqli_num_rows($ltR)==0) ? "None" : mysqli_num_rows($ltR);
		$this->Cell( 20, 12, "$attLT", 0, 0, 'L');
		$this->Ln(6);
		// $this->SetFont('Arial','',12);
		// $this->Cell( 60, 12, "Cooperate@Home Webinars: ", 0, 0, 'L' );
		// $this->SetFont('Arial','B',12);
		// $webQ = "SELECT a.coop, a.lastname FROM attendance a, clients c WHERE a.event = 'WEB' AND a.year = $printY AND a.att <> ''
		// 	AND a.clientID = $clientID GROUP BY a.id";
		// $webR = mysqli_query($dbc, $webQ);
		// $attWEB = (mysqli_num_rows($webR)==0) ? "None" : mysqli_num_rows($webR);
		// $this->Cell( 20, 12, "$attWEB", 0, 0, 'L');
		// $this->Ln(6);
		$this->SetFont('Arial','',12);
		$this->Cell( 60, 12, "Cooperative Cafe: ", 0, 0, 'L' );
		$this->SetFont('Arial','B',12);
		$scsQ = "SELECT a.coop, a.lastname FROM attendance a, clients c WHERE a.event = 'CC' AND a.year = $printY 
			AND a.clientID = $clientID GROUP BY a.id";
		$scsR = mysqli_query($dbc, $scsQ);
		$attSCS = (mysqli_num_rows($scsR)==0) ? "None" : mysqli_num_rows($scsR);
		$this->Cell( 20, 12, "$attSCS", 0, 0, 'L');
		$this->Ln(6);

	}


	protected function PutLink($URL,$txt,$line=10)
	{
	    //Put a hyperlink
	    $this->SetTextColor(0,0,255);
	    $this->Write($line,$txt,$URL);
	    $this->SetTextColor(0);
	}
	
	protected function LoadData($file)
	{
	    // Read file lines
	    $lines = file($file);
	    $data = array();
	    foreach($lines as $line)
	        $data[] = explode(';',trim($line));
	    return $data;
	}
	
	protected function ImprovedTable($header, $data)
	{
	    // Column widths
	    $w = array(20, 85, 20);
	    // Header
	    for($i=0;$i<count($header);$i++)
	        $this->Cell($w[$i],7,$header[$i],0,0,'C');
	    $this->Ln();
	    // Data
	    foreach($data as $row)
	    {
	        $this->Cell($w[0],6,$row[0]);
			if($row[3]) {
				$this->SetTextColor(0,0,255);
		        $this->Cell($w[1],6,$row[1],0,0,'L',false,$row[3]);
				$this->SetTextColor(0);
			} else {
				$this->Cell($w[1],6,$row[1]);
			}
	        $this->Cell($w[2],6,$row[2]);
	        $this->Ln();
	    }
	    // Closing line
	    $this->Cell(array_sum($w),0,'');
	}
	
	
	//Report
	public function Report($client,$filename,$dest='I')
	{
		global $dbc,$userinfo,$thisQ,$rc;
		$clientID = $_REQUEST['clientID'];
		$this->AliasNbPages();
		$this->AddPage();

		$in = 6;
		$printQ = (!isset($_GET['thatQ'])) ? thisQ() : $_GET['thatQ'];
		$printY = (!isset($_GET['thatY'])) ? date('Y') : $_GET['thatY'];
		$nolimit = $client['program'] == "CBLD Unlimited" ? TRUE : FALSE;

		$this->Image('images/columinate-letterhead-v2.png',5,3,200);
		$this->Ln(32);
		$this->SetY(46);
		$this->SetFont('Arial','B',14);
		$this->Cell( 120, 12, $client['name'], 0, 0, 'L' );
		$this->SetFont('Arial','',12);
		$this->Cell( 40, 12, "Q".$printQ."-".$printY, 0, 0, 'L' );
		$this->Cell( 40, 12, date('n/j/Y'), 0, 0, 'L' );
		$this->SetLineWidth(0.8);
		$this->Line( 11, 56 ,192 ,56 );
		$this->Ln(8);

		$this->SetFont('Arial','',12);
		$this->jEntryIntro($client,$rc,$in);

		$this->SetFont('Arial','B',12);
		$this->Cell( 55, 12, "Ongoing Support:", 0, 0, 'L' );
		$this->Ln(8);
		$this->Cell($in);
		$this->SetFont('Arial','',12);
		if ($nolimit === TRUE) {
			$this->Cell( 55, 12, "Hours Utilized", 0, 0, 'L' );
		} else {
			$this->Cell( 55, 12, "Hours Utilized (of ".$client['total_hours'].")", 0, 0, 'L' );
		}
		$this->SetFont('Arial','B',14);
		$this->Cell( 40, 12, $client['hrs']['total'], 0, 0, 'L' );
		if ($nolimit === FALSE) {
			$this->SetFont('Arial','',12);
			$this->Cell( 40, 12, "Hours Remaining", 0, 0, 'L' );
			$this->SetFont('Arial','B',14);
			if ($client['hrs']['alert']=='med') { $fcolor = '#cc0033';
				$this->SetTextColor(204,0,51);
			}//if we are over hours
			elseif ($client['hrs']['alert']=='high') { $fcolor = '#ff9900';
				$this->SetTextColor(255,153,0);
			}//if
			$this->Cell( 30, 12, number_format($client['hrs']['R'],2), 0, 0, 'L' );
		}
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
		if ($nolimit === FALSE) {
			$this->Cell( 94, 12, "On track for balanced use of hours for the year? ", 0, 0, 'L' );
			$this->SetFont('Arial','B',12);
			$this->Cell( 20, 12, ($client['hrs']['alert']=='low'?'yes':'no'), 0, 0, 'L');
		}
		$this->SetFont('Arial','B',12);
		$this->Ln(6);

		$this->jEntries($client['hours'],$in);

		$this->Ln(4);

		$this->SetFont('Arial','B',12);
		$this->Cell( 45, 12, "Board Retreat:  ", 0, 0, 'L' );
		$this->SetFont('Arial','',12);

		$retQ = "SELECT RetreatDate1, RetreatDate2, RetreatNote FROM journal WHERE YEAR(Date) = '" . $printY . "' AND Category = 'retreat' AND ClientID = " . $clientID . " ORDER BY Date DESC LIMIT 1";
		// echo $retQ;
		$retR = mysqli_query($dbc, $retQ);
		$ret = mysqli_fetch_row($retR);

		//if (mysqli_num_rows($retR) == 1) {
		if ($ret) {
			//($ret[0] != '0000-00-00' || $client['RetreatDate'] != '0000-00-00') {
			$ret_date = $ret[0];
			if($ret_date == "0000-00-00")
				$longdate = "to be decided.";
			else
				$longdate = strftime('%A %B %e, %Y',strtotime($ret_date));
			$this->Cell( 20, 12, $longdate, 0, 0, 'L');
		} else {
			$prdateQ = "SELECT RetreatDate FROM clients WHERE id = " . $clientID;
			$prdateR = mysqli_query($dbc, $prdateQ);
			list($prdate) = mysqli_fetch_row($prdateR);
			if ($prdate != '0000-00-00') {
				$longdate = strftime('%A %B %e, %Y',strtotime($prdate));
				$this->Cell( 20, 12, $longdate, 0, 0, 'B');
			} else {
				$this->Cell( 20, 12, "to be decided", 0, 0, 'L');
			}
		}

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

		$staffnameR = mysqli_query($dbc, "SELECT s.firstname, s.lastname FROM staff s, staff_clients c 
			WHERE s.id = c.StaffID AND c.ClientID = ".$clientID);
		$cons_ct = mysqli_num_rows($staffnameR);
		if ($cons_ct > 1) {
			$staffnames = "";
			$i = 1;
			while($names=mysqli_fetch_row($staffnameR)) {
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
			$staffname = mysqli_fetch_row($staffnameR);
			$this->Cell( 35, 12, $staffname[0]." ".$staffname[1].", CBLD Consultant", 0, 0, 'L' );
		}

		$this->Ln(7);
		$this->SetFont('Arial','B',12);
		$this->Cell( 90, 12, '', 0, 0, 'L' );
		$this->PutLink('http://columinate.coop','Columinate');
		$this->Write( 10,' | ');
		$this->PutLink('http://columinate.coop/cbld','CBLD Program');
		$this->Ln(10);
		
		// $this->Image('images/columinate-footer.png',0,248,50);
		// END PAGE 1 --------------------

		//
		//	PAGE 2
		//


		$this->AddPage();
		$this->Image('images/2023Q1.jpg',0,10,210);
		
		// $this->Link(110,20,98,72,'https://youtu.be/cFlJV4LzI7g');

		$this->Link(2,54,72,14,'https://columinate.coop/events');
		
		$this->Link(8,178,88,78,'https://columinate.coop/events');

		$this->Link(110,238,76,9,'mailto:cbld_enrollment@columinate.coop');
		
		// $this->Link(2,235,208,32,'https://columinate.coop/library');


		// PRINT IT!
		$this->Output($filename,$dest);
	}	// END Report
}	// END PDF


if(!empty($clientID)){
	if($clientID=='all'){
		$action='batch';
		$query = "SELECT * FROM clients c";
		// echo $query;
		$result = mysqli_query($dbc, $query);
		while($row=mysqli_fetch_assoc($result)){
			$c[]=$row;
		}
		//print_r($c);

	} else {
		$query = "SELECT * FROM clients c WHERE c.id = " . $clientID . " LIMIT 1";
		// echo $query;
		$action='single';
		$result = mysqli_query($dbc, $query);
		$c[]=$row=mysqli_fetch_assoc($result);
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
		$staffnameR = mysqli_query($dbc, "SELECT s.firstname, s.lastname FROM staff s, staff_clients c 
			WHERE s.id = c.StaffID AND c.ClientID = ".$clientID);
		$cons_ct = mysqli_num_rows($staffnameR);
		// $hours_ty = "SELECT SUM(Hours) FROM journal
		// 	WHERE ClientID = " . $clientID . (($admin==false||$cons_ct==1)?"
		// 	AND StaffID='{$userinfo['id']}'":'') . "
		// 	AND YEAR(Date) = $thatY AND (MONTH(Date) < (".(3*($thisQ-1)+1)."))
		// 	AND Category <> 'reset' AND Billable <> 0";
		$hours_ty = "SELECT SUM(Hours) FROM journal 
			WHERE ClientID = " . $clientID . " 
			AND YEAR(Date) = $thatY 
			AND (MONTH(Date) < (".(3*($thisQ-1)+1).")) 
			AND Category <> 'reset' 
			AND Billable <> 0";
		$hours_tyr = mysqli_query($dbc, $hours_ty);
		$client['hours_ty'] = mysqli_fetch_row($hours_tyr);
		$client['hrs']['total'] = round($client['hours_ty']['0'],2);
		// $hoursq = "SELECT *, DATE_FORMAT(`Date`,'%c/%e/%Y') as created_fmt FROM journal
		// 	WHERE ClientID = " . $clientID . ($admin==false?"
		// 	AND StaffID='{$userinfo['id']}'":'') . "
		// 	AND YEAR(Date) = $thatY AND (MONTH(Date) >= (".(3*($thisQ-1)+1).")
		// 	AND MONTH(Date)<=(".(3*$thisQ).")) AND Billable = 1
		// 	ORDER BY Category='quarterly' DESC, Date DESC ";
		$hoursq = "SELECT *, DATE_FORMAT(`Date`,'%c/%e/%Y') as created_fmt FROM journal 
			WHERE ClientID = " . $clientID . " 
			AND YEAR(Date) = $thatY 
			AND (MONTH(Date) >= (".(3*($thisQ-1)+1).") 
			AND MONTH(Date)<=(".(3*$thisQ).")) 
			AND (Billable = 1 OR Category = 'quarterly') 
			ORDER BY Category='quarterly' DESC, Date DESC ";
		$hoursr = mysqli_query($dbc, $hoursq);
		if(mysqli_num_rows($hoursr)==0)continue;
		$hoursQ=0;
		while($h=mysqli_fetch_assoc($hoursr)){
			$h['Hours']=round($h['Hours'],2);
			$client['hours'][] = $h; $client['hrs']['total']+=$h['Hours']; $hoursQ+=$h['Hours'];
			$client['hrs']['cat'][$h['Category']]=(isset($client['hrs']['cat'][$h['Category']])?$client['hrs']['cat'][$h['Category']]:0)+1;
		}
		$client['hrs']['R'] = $client['total_hours']-$client['hrs']['total']; //hours remaining (yearly)
		$client['q_hours']=$client['total_hours']/4; //est hours per q

		if($client['hrs']['total'] > $client['total_hours'] || ($client['hrs']['total'] == $client['total_hours'] && $thisQ<4))$client['hrs']['alert']='med';
		elseif ($thisQ<4 && $client['hrs']['total'] > ($row['q_hours']*$thisQ) && $client['hrs']['total'] < (2*$row['q_hours']/3)) $client['hrs']['alert']='high';
		else $client['hrs']['alert']='low';
			$filename=($action=='batch'?$_SERVER['DOCUMENT_ROOT'].'/reports/':'').'CBLD_'.$thatY.'_Q'.$thisQ.'-'.getSlug($client['name']).'.pdf'; if($action=='batch')$filenames[]=$filename;
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
