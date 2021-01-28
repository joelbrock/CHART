<?
function db_send($cols,$vals,$table,$type,$where='',$display='')
{
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

function client_health($client) {
	
	$query = "SELECT j.ClientID as cID, SUM(j.Hours) as hours, c.total_hours AS total FROM journal j, clients c WHERE j.ClientID = c.id AND j.ClientID = $client  AND YEAR(j.Date) = " . date('Y') . "GROUP BY cID";
	$result = mysqli_query($dbc, $query);
	$row = mysqli_fetch_array($result);
	
	$hours = $row['hours'];
	$total = ($row['total'] / 4) * $thisQ;
	$sub = $hours / $total;
	
	if ($sub > 1.2) {
		$health = 1;
	} elseif ($sub <= 1.2 && $sub >= 0.8) {
		$health = 3;
	} elseif ($sub < 0.8) {
		$health = 2;
	}

	return $health;
}

?>