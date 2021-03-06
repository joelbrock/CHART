<?php
require("mysql_connect.php");
require('fpdf/fpdf.php');
$clientID = $_REQUEST['clientID'];
$staffID = $_REQUEST['staffID'];
$reportID=1;

$rptquery = mysqli_query($dbc, "SELECT * FROM report_content c WHERE c.id = " . $reportID . " LIMIT 1");
$rc=mysqli_fetch_assoc($rptquery);

// $thisQ = ($_GET['thatQ']) ? $_GET['thatQ'] : $thisQ;
// $thatY = ($_GET['thatY']) ? $_GET['thatY'] : date('Y');

class PDF extends FPDF
{
//Page header
	function Header()
	{
		// global $client,$thisQ;
		//Logo
		// $this->Image('images/pdf_header.png',5,5,200);
		// $this->Ln(32);
	}

	//Page footer
	function Footer()
	{
		//Position at 1.5 cm from bottom
		// $this->SetY(-15);
		// $this->SetFont('Arial','',8);
		//Page number
		// $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
		// $this->Line(10,$this->GetY(), 190, $this->GetY());
	}

	function jEntryIntro($data,$rc,$in)
	{
		$clientID = $_REQUEST['clientID'];
		$intro_default = "Here is your CBLD quarterly report.  Please have a look and let me know if you have any questions.  And keep up the great work!";

		global $client,$thisQ;
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
			$this->PutLink("http://library.cdsconsulting.coop/", "CDS CC Library");
			$this->Ln(7);
			$this->SetFont('Arial','B',10);
			$this->Cell(8);
			$this->PutLink("http://library.cdsconsulting.coop/policy-register-template-users-guide/", "Policy Register Template Users' Guide");
			$this->Ln(7);
			$this->Cell(8);
			$this->PutLink("http://library.cdsconsulting.coop/video-field-guide-membership-growth/", "Video Field Guide on Membership Growth"); 
			$this->Ln(7);
			$this->SetFont('Arial','B',10);
			$this->Cell(8);
			$this->PutLink("http://library.cdsconsulting.coop/strategic-alliances-and-consolidations-exploring-cooperation-among-cooperatives/", "Strategic Alliances and Consolidations");
			$this->Ln(7);
			$this->SetFont('Arial','B',10);
			$this->Cell(8);
			$this->Write(11,"And ");
			$this->PutLink("http://library.cdsconsulting.coop/category/cooperative-cafe/", "over 200 short video recordings");
			$this->Write(11," from the Cooperative Cafes");
			$this->Ln(3);
			$this->Cell(8);
			$this->Write(11," focused on Courageous Leadership.");
			$this->Ln(7);

		} elseif (preg_match($pattern3, $rc)) {
			$this->SetFont('Arial','B',11);
			$this->Write(11,"New Connections Articles -- from the ");
			$this->PutLink("http://library.cdsconsulting.coop/category/articles/connections/", "Connections Archive");
			$this->Ln(6);
			$this->SetFont('Arial','B',10);
			$this->Cell(8);
			$this->PutLink("http://library.cdsconsulting.coop/so-our-co-ops-are-mostly-white-now-what/", "So Our Co-ops are Mostly White, Now What?");
			$this->Ln(4);
			$this->Cell(8);
			$this->SetFont('Arial','I',9);
			$this->Write(10,"August, 2016");
			$this->Ln(6);
			$this->SetFont('Arial','B',10);
			$this->Cell(8);
			$this->PutLink("http://library.cdsconsulting.coop/what-is-board-holism-or-speaking-with-one-voice/", "What is Board Holism Or Speaking with One Voice?");
			$this->Ln(4);
			$this->Cell(8);
			$this->SetFont('Arial','I',9);
			$this->Write(10,"October, 2016");
			$this->Ln(6);
			$this->SetFont('Arial','B',10);
			$this->Cell(8);
			$this->PutLink("http://library.cdsconsulting.coop/including-members-in-the-ends-dialogues/", "Including Members in the Ends Dialogues");
			$this->Ln(4);
			$this->Cell(8);
			$this->SetFont('Arial','I',9);
			$this->Write(10,"November, 2016");
			$this->Ln(6);
			$this->SetFont('Arial','B',10);
			$this->Cell(8);
			$this->PutLink("http://library.cdsconsulting.coop/co-op-fair-brings-community-together/", "Co-op Fair Brings Community Together");
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
		$cblQ = "SELECT a.coop, a.lastname FROM attendance a, clients c WHERE (a.event = 'CBL' OR a.event = '101') AND a.year = $printY AND a.att <> ''
			AND a.clientID = $clientID GROUP BY a.id";
		$cblR = mysqli_query($dbc, $cblQ);
		$attCBL = (mysqli_num_rows($cblR)==0) ? "None" : mysqli_num_rows($cblR);
		$this->Cell( 20, 12, "$attCBL", 0, 0, 'L');
		$this->Ln(6);
		$this->SetFont('Arial','',12);
		$this->Cell( 50, 12, "Applied Governance: ", 0, 0, 'L' );
		$this->SetFont('Arial','B',12);
		// $ltQ = "SELECT a.coop, a.lastname FROM attendance a, clients c WHERE a.event = 'AG' AND a.year = $printY AND a.att <> ''
			// AND SUBSTR( a.coop, 1, LENGTH( c.name ) ) =  '".$client['name']."' GROUP BY a.id";
		$ltQ = "SELECT a.coop, a.lastname FROM attendance a, clients c WHERE a.event = 'AG' AND a.year = $printY AND a.att <> ''
			AND a.clientID = $clientID GROUP BY a.id";
		$ltR = mysqli_query($dbc, $ltQ);
		$attLT = (mysqli_num_rows($ltR)==0) ? "None" : mysqli_num_rows($ltR);
		$this->Cell( 20, 12, "$attLT", 0, 0, 'L');
		$this->Ln(6);
		$this->SetFont('Arial','',12);
		$this->Cell( 50, 12, "Cooperative Cafe: ", 0, 0, 'L' );
		$this->SetFont('Arial','B',12);
	//	$scsQ = "SELECT a.coop, a.lastname FROM attendance a, clients c WHERE a.event = 'CC' AND a.year = $printY AND a.att <> ''
	//		AND SUBSTR( a.coop, 1, LENGTH( c.name ) ) =  '".$client['name']."' GROUP BY a.id";
		$scsQ = "SELECT a.coop, a.lastname FROM attendance a, clients c WHERE a.event = 'CC' AND a.year = $printY AND a.att <> ''
			AND a.clientID = $clientID GROUP BY a.id";
		$scsR = mysqli_query($dbc, $scsQ);
		$attSCS = (mysqli_num_rows($scsR)==0) ? "None" : mysqli_num_rows($scsR);
		$this->Cell( 20, 12, "$attSCS", 0, 0, 'L');
		$this->Ln(6);

	}


	function PutLink($URL,$txt,$line=10)
	{
	    //Put a hyperlink
	    $this->SetTextColor(0,0,255);
	    $this->Write($line,$txt,$URL);
	    $this->SetTextColor(0);
	}
	
	function LoadData($file)
	{
	    // Read file lines
	    $lines = file($file);
	    $data = array();
	    foreach($lines as $line)
	        $data[] = explode(';',trim($line));
	    return $data;
	}
	
	function ImprovedTable($header, $data)
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
	function Report($client,$filename,$dest='I')
	{
		global $userinfo,$thisQ,$rc;
		$clientID = $_REQUEST['clientID'];
		$this->AliasNbPages();
		$this->AddPage();

		$in = 6;
		$printQ = (!isset($_GET['thatQ'])) ? thisQ() : $_GET['thatQ'];
		$printY = (!isset($_GET['thatY'])) ? date('Y') : $_GET['thatY'];

		$this->Image('images/pdf_header.png',5,5,200);
		$this->Ln(32);

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
		$this->Cell( 30, 12, number_format($client['hrs']['R'],2), 0, 0, 'L' );
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
		$this->PutLink('http://cdsconsulting.coop','CDS Consulting Co-op');
		$this->Write( 10,' | ');
		$this->PutLink('http://cdsconsulting.coop/cbld','CBLD Program');
		$this->Ln(10);
		// END PAGE 1 --------------------

		//
		//	PAGE 2
		//
		$this->AddPage();
		
		$this->SetFont('Arial','B',16);
		$library = '';
		$library .= $this->Cell(130,14,'What\'s New in the',0,0,'C');
		$library .= $this->Ln(2);
		$library .= $this->Image('images/CDS-CC-Library-Header.png',20,20,108,18,'png', 'http://library.cdsconsulting.coop');
			
		$this->MultiCell(130,24,$library,0,'C');
		$this->Ln(5);
		$this->MultiCell(130,0,'',0,'C');
		$header = array('Date', 'Title', 'Author');
		$data = $this->LoadData('library-resources.txt');
		$this->SetFont('Arial','',10);
		$this->ImprovedTable($header,$data);
		
		// $this->Image('images/testimonial-chris-dilley.png',150,28,50,0);
		// $this->Image('images/testimonial-molly-obrien.png',150,92,50,0);
		// $this->Image('images/quilt001.png',155,10,40);
		// $this->Image('images/quilt002.png',155,50,40);
		// $this->Image('images/quilt003.png',155,90,40);
		// $this->Image('images/quilt004.png',155,130,40);
		// $this->Image('images/quilt005.png',165,174,40);
		$this->SetLineWidth(0.1);
		$this->Line(150,10,150,173);
		$this->Line(10,173,200,173);
		
		$this->Image('images/Coop-Cafe-logo.png',155,13,40,0);
		// $this->Image('images/cafe-best-of-2016-banner.png',10,174,190,0,'png','http://library.cdsconsulting.coop/best-of-cooperative-cafe-2016/');
		
		
		$this->SetXY(152,54);
		$this->SetFont('Arial','',10);
		
		$this->MultiCell(50,6,'See pictures and quilts from recent Cooperative Cafes in ',0,'L');
		$this->SetXY(154,62);
		$this->PutLink('https://library.cdsconsulting.coop/co-op-cafe-durham-2019/','Durham, NC, 2019',12);
		$this->SetXY(154,68);
		$this->PutLink('https://library.cdsconsulting.coop/co-op-cafe-sacramento-2109/','Sacramento, CA, 2019',12);
		$this->SetXY(154,74);
		$this->PutLink('https://library.cdsconsulting.coop/co-op-cafe-madison-2018/','Madison, WI, 2018',12);
		$this->SetXY(154,80);
		$this->PutLink('https://library.cdsconsulting.coop/co-op-cafe-philadelphia-2018/','Philadelphia, PA, 2018',12);
		$this->SetXY(154,86);
		$this->PutLink('https://library.cdsconsulting.coop/co-op-cafe-keene-2018/','Keene, NH, 2018',12);
		$this->SetXY(154,92);
		$this->PutLink('https://library.cdsconsulting.coop/co-op-cafe-portland-2018/','Portland, OR, 2018',12);
		$this->SetXY(154,98);
		$this->PutLink('#',' ',12);
		$this->SetXY(154,104);
		$this->PutLink('https://library.cdsconsulting.coop/expand-the-vision-of-we/','Best of Co-op Cafe 2018!',12);

		$this->Image('images/cafe-pic-3.jpg',152,118,47);

		/** TESTIMONIAL
		$testimonial1 = 'I love events like this!  Not only does it allow you to hear what your board members and customers really care about, but it also allows you to bring the reality of what it takes to run a successful co-op into the conversation.  Don\'t miss this opportunity to engage with your board and owners in a day of strategic thinking.';
		$testimonial2 = '--John Lacaria, Interim General Manager, Eastside Food Co-op';
		$this->SetTextColor(255,255,255);
		$this->SetFont('Arial','B',10);
		$this->SetFillColor(242,137,0);
		$this->SetXY(152,90);
		$this->MultiCell(50,5,$testimonial1,0,'L',true);
		$this->SetFont('Arial','',10);
		$this->SetX(152);		
		$this->MultiCell(50,5,$testimonial2,0,'R',true);	
		$this->SetTextColor(0);
				
		$this->SetFont('Arial','B',18);
		$this->Cell(0,20,'Library Resource Highlights',0,0,'C');
		$this->Ln(4);
		*/
		
		$this->Image('images/quilt002.png',10,186,45);
		$this->Image('images/quilt004.png',10,231,45);
		// $this->Image('images/quilt005.png',165,174,40);
		
		$this->SetXY(70,188);
		$this->SetFont('Arial','B',12);
		$this->SetLineWidth(0.1);
		$this->MultiCell(130,88,'',1,'C');
		$this->SetXY(72,186);
		$this->MultiCell(128,12,'Being Amazing: Resources',0,'C');
		
		$para1 = 'This seasons focus of \'Being Amazing\' has many facets to it.  We\'d like to highlight just a few key resources that you can find in our free online resource library that we think really get at the root of operations excellence.  Co-ops with a strong foundation of mutual trust and respect between Management and Board are far liklier to succeed at their goals which is why we encourage you to review these resources and make sure that your Board is utilizing these tools.';

		$this->SetXY(72,196);
		$this->SetFont('Arial','',10);
		$this->MultiCell(125,4,$para1,0,'L');
		$this->SetXY(72,225);
		$this->PutLink('https://library.cdsconsulting.coop/gain-sales-and-impact-from-great-storytelling/ ','Gain Sales and Impact through Great Storytelling',10);
		$this->SetXY(72,232);
		$this->PutLink('https://library.cdsconsulting.coop/cooperative-ends-impact-and-telling-the-story/','Cooperative Ends, Impact, and Telling the Story');
		$this->SetXY(72,239);
		$this->PutLink('https://library.cdsconsulting.coop/customers-the-heart-of-the-co-op/','Customers: The Heart of the Co-op - Dave Olson');		
		$this->SetXY(72,246);
		$this->PutLink('https://library.cdsconsulting.coop/how-to-be-an-effective-board-president/','How to be a Great Board President');
		$this->SetXY(72,253);
		$this->PutLink('https://library.cdsconsulting.coop/participation-own-use-serve-and-belong/','Own, Use, Serve, Belong: A new paradigm for Participation');
		$this->SetXY(72,260);
		$this->PutLink('https://library.cdsconsulting.coop/the-magic-of-commitment/','The Magic of Commitment',10);
		$this->Write(10,' ');
		
		

		
		// ---------------------------

		//
		//	PAGE 3
		//
		$this->AddPage();
		
		// PAGE 3 COLUMN 1 (LEFT) --
		// $this->SetFont('Arial','B',18);
		// $this->Cell(0,20,'Library Resource Highlights',0,0,'C');
		// $this->Ln(4);
		// $this->SetY(26);
		$this->SetFont('Arial','B',12);
		$this->SetLineWidth(0.1);
		$this->MultiCell(132,114,'',1,'C');
		$this->SetXY(10,10);
		$this->MultiCell(130,12,'Being Amazing: Tools',0,'C');
		
		$para2 = 'Now that you\'ve read up on some key resources on \'Being Amazing\' we have some critical tools to share that no Board should do without.  These include the CBLD Policy Register template, a complete starting point for any Co-op board looking to get up and running using Policy Governance.  As well as the GM Report Support resources, a complete guide for GMs to understanding and composing great monitoring reports!  We\'ve done all the hard work and compiled all the nitty-gritty.  Just download and adapt to meet the unique needs of your own unique cooperative.';

		$this->SetX(12);
		$this->SetFont('Arial','',10);
		$this->MultiCell(125,4,$para2,0,'L');
		$this->SetX(12);
		$this->SetFont('Arial','B',10);
		$this->MultiCell(125,8,'Here are some essential tools:',0,'L');
		$this->SetFont('Arial','',10);
		$this->Ln(0);
		$this->SetX(12);
		$this->PutLink('http://library.cdsconsulting.coop/gm-report-support-page/','GM Report Support',10);
		$this->Ln(4);
		$this->SetX(14);
		$this->Write(10,'A complete guide to monitoring reporting');
		$this->Ln(7);
		$this->SetX(12);
		$this->PutLink('https://library.cdsconsulting.coop/gm-report-support-02-intro-to-limitations-monitoring-financial-conditions-and-activities/','Monitoring Report Support: Financial Conditions Sample Monitoring Report',10);
		$this->Ln(7);
		$this->SetX(12);
		$this->PutLink('https://library.cdsconsulting.coop/gm-report-support-03-asset-protection-planning-and-budgeting/','Monitoring Report Support: Planning and Budgeting',10);
		$this->Ln(7);
		$this->SetX(12);
		$this->PutLink('https://library.cdsconsulting.coop/4-pillars-of-cooperative-governance-self-assessment-tool/','Cooperative Governance Self Assessment',10);
		$this->Ln(7);
		$this->SetX(12);
		$this->PutLink('https://library.cdsconsulting.coop/annual-planning-calendar/','Annual Planning Calendar',10);
		$this->Ln(7);
		$this->SetX(12);
		$this->PutLink('https://library.cdsconsulting.coop/board-agenda-template/','Board Agenda Template',10);
		$this->Ln(7);
		$this->SetX(12);
		$this->PutLink('https://library.cdsconsulting.coop/cultivating-a-culture-of-appreciation/','Cultivating a Culture of Appreciation',10);
 
		
		// ---------------------------		
		
		// PAGE 3 - COLUMN 2 (RIGHT) -
		// $this->Image('images/pop-logo-stacked.png',144,24,44,0,'png','http://cdsconsulting.coop/participation/power_of_participation/');
		// $this->SetXY(144,54);
		// $pop_text = 'CDS Consulting Co-op is now piloting the Power of Participation (POP!) Program to give co-ops the support they need to enhance owner and customer engagement, invigorate staff commitment to co-op outcomes, and positively impact their competitive position in the marketplace.';
		// $this->MultiCell(45,4,$pop_text,0,'L');
		// $pop_text2 = 'For more information contact: 802-380-3824 or MarkGoehring@cdsconsulting.coop.';
		// $this->SetXY(144,108);
		// $this->MultiCell(45,4,$pop_text2,0,'L');
		
		// $this->Image('images/quilt001.png',150,15,45);
		// $this->Image('images/quilt003.png',150,70,45);
		
		// PAGE 3 - EVENT DESCRIPTIONS
		$this->SetXY(145,10);
		// $this->SetFont('Arial','B',9);
		// $this->MultiCell(54,4,'CBL 101: Foundation for Board Leadership',0,'C');

		$this->Image('images/CBL-logo.JPG',160,6,25,0,'jpg','http://cdsconsulting.coop/cooperative_governance/cbl_101/');	
		$this->SetXY(145,29);
		$this->SetFont('Arial','I',8);
		$this->MultiCell(57,3,'This class provides a strong foundation for directors by covering these topics: Co-op principles, values & history, legal roles & responsibilities, co-op governance & financial understanding.',0,'C');
		
		$this->Image('images/AG-logo.jpg',158,45,28,0,'jpg','http://cdsconsulting.coop/cooperative_governance/building_a_great_relationship_workshop/');
		$this->SetXY(145,70);
		$this->SetFont('Arial','I',8);
		$this->MultiCell(57,3,'2019 Themes: Building a great relationship with your GM. And Everyone Welcome.',0,'C');

		$this->Image('images/cafe-logo.jpg',161,78,27,0,'jpg','http://cdsconsulting.coop/cooperative_governance/cooperative_cafe/');
		$this->SetXY(145,104);
		$this->SetFont('Arial','I',8);
		$this->MultiCell(57,3,'This event provides a day of highly interactive & engaging strategic conversations designed to build shared understanding and alignment at your co-op & among co-ops.',0,'C');
				
		
		// ---------------------------
		
		// EVENTS GRID ---------------
		$this->Image('images/2019_events_grid_q1.png',22,125,164);
		// ---------------------------
		
		
		// PAGE 3 FOOTER -------------
		$this->SetFont('Arial','',10);
		$this->SetXY(45,264);
		$this->Write(10,'CDS Consulting Co-op | ');
		$this->SetTextColor(0,0,255);
		$this->Write(10,"www.cdsconsulting.coop", "http://www.cdsconsulting.coop/");
		$this->SetTextColor(0);
		$this->Write(10,' | (802) 387-6013');

		$this->Ln(10);
		$this->SetX(80);
		$this->Image( 'images/circle-f.png', $this->GetX(), $this->GetY(), 8, 8, 'png', 'https://www.facebook.com/CDSConsultingCoop' );
		$this->Image( 'images/circle-t.png', $this->GetX() + 10, $this->GetY(), 8, 8, 'png', 'https://www.twitter.com/CDS_CC' );
		$this->Image( 'images/circle-li.png', $this->GetX() + 20, $this->GetY(), 8, 8, 'png', 'https://www.linkedin.com/company-beta/394986/' );
		$this->Image( 'images/circle-yt.png', $this->GetX() + 30, $this->GetY(), 8, 8, 'png', 'https://www.youtube.com/channel/UCSxZK1K5-cemEO1DB5hPigw' );	
		// ---------------------------

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
