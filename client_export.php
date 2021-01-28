<?php
require("mysql_connect.php");
require('../library/fpdf/fpdf.php');
$clientID = $_REQUEST['clientID'];
$staffID = $_REQUEST['staffID'];
$reportID=1;

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
function jEntry($k,$data,$in){
	global $thisQ;

	$this->SetFont('Arial','',10);
	$hr = ($data['Hours'] == 1)?"hr":"hrs";
	$this->SetFont('Arial','',10);
	$this->MultiCell(175,5.25,strftime("%D",strtotime($data['Date'])).' - '.$data['Hours'] . $hr . ' - ' .$data['ClientNote']);
	$this->Ln(3);
}
function jEntries($hours,$in){
	foreach($hours as $k=>$h){
		$this->jEntry($k,$h,$in);
		// $this->Ln(-3);
	}
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
	global $userinfo,$thisQ;
	$clientID = $_REQUEST['clientID'];
	$this->AliasNbPages();
	$this->AddPage();
	
	$in = 6;
	
	$this->SetFont('Arial','B',14);
	$this->Cell( 120, 12, $client['name'], 0, 0, 'L' );
	$this->SetFont('Arial','',12);
	$this->Cell( 40, 12, "YTD-".date('Y'), 0, 0, 'L' );
	$this->Cell( 40, 12, date('n/j/Y'), 0, 0, 'L' );
	$this->SetLineWidth(0.8);
	$this->Line( 10, 52 ,190 ,52 );
	$this->Ln(8);
	
	$this->SetFont('Arial','',12);
	$staffnameR = mysqli_query($dbc, "SELECT s.firstname, s.lastname FROM staff s, staff_clients c WHERE s.id = c.StaffID AND c.ClientID = ".$clientID);
	$staffname = mysqli_fetch_row($staffnameR);
	$this->Cell( 55, 12, "CBLD Consultant: ".$staffname[0]." ".$staffname[1], 0, 0, 'L' );
	$this->Ln(8);	
	
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
	$this->Ln(8);

	$this->SetFont('Arial','',12);
	$this->Cell($in);
	$this->Cell( 45, 12, "Board Retreat:  ", 0, 0, 'L' );
	$this->SetFont('Arial','',12);

	$retQ = "SELECT RetreatDate1, RetreatDate2, RetreatNote FROM journal WHERE YEAR(Date) = YEAR(CURDATE()) AND Category = 'retreat' AND ClientID = " . $clientID . " ORDER BY Date DESC LIMIT 1";
	// echo $retQ;
	$retR = mysqli_query($dbc, $retQ);
	$ret = mysqli_fetch_row($retR);

	if (mysqli_num_rows($retR) == 1) {
		//($ret[0] != '0000-00-00' || $client['RetreatDate'] != '0000-00-00') {
		$ret_date = $ret[0];
		$longdate = strftime('%A %B %e, %Y',strtotime($ret_date));
		$this->Cell( 20, 12, $longdate, 0, 0, 'B');
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
		$this->MultiCell( 175, 5.25, $ret[2]);
	}

	$this->Ln(6);
	$this->SetFont('Arial','B',12);
	$this->Cell( 55, 12, "Hours Details:", 0, 0, 'L' );
	$this->Ln(8);

	$this->jEntries($client['hours'],$in);

	$this->Ln(10);
			
	$this->Output($filename,$dest);
}
}
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
			$hours_ty = "SELECT SUM(Hours) FROM journal WHERE ClientID = " . $clientID . ($admin==false?" AND StaffID='{$userinfo['id']}'":'') . " AND YEAR(Date) = $thatY";
			$hours_tyr = mysqli_query($dbc, $hours_ty);
			$client['hours_ty'] = mysqli_fetch_row($hours_tyr);
			$client['hrs']['total'] = round($client['hours_ty']['0'],2);
			$hoursq = "SELECT *, DATE_FORMAT(`Date`,'%c/%e/%Y') as created_fmt FROM journal WHERE ClientID = " . $clientID . ($admin==false?" AND StaffID='{$userinfo['id']}'":'') . " AND YEAR(Date) = $thatY ORDER BY Date";
			$hoursr = mysqli_query($dbc, $hoursq); 
			if(mysqli_num_rows($hoursr)==0)continue;
			$hoursQ=0;
			while($h=mysqli_fetch_assoc($hoursr)){
				$h['Hours']=round($h['Hours'],2);
				$client['hours'][] = $h; 
				// $client['hrs']['total']+=$h['Hours'];
				$hoursQ+=$h['Hours'];
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
