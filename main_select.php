<?php
echo "<div id='main_select'>\n";
$coder = mysqli_query($dbc, "SELECT * FROM clients ".($userinfo['admin']!='1'?" LEFT JOIN staff_clients ON clients.id=staff_clients.clientID WHERE staff_clients.staffID='{$userinfo['id']}'":''));
	
		 // -- CODEMENU START
		 if(mysqli_num_rows($coder)>0){
		 echo "<form method=\"GET\" action=\"$PHP_SELF\" id='search_c' style='display:inline'>\n";
			 echo "<select name=\"clientID\" onchange='document.forms[\"search_c\"].submit();'>\n";
			 echo "<option value=''>Sort by Client</option>";
			 while ($code = (mysqli_fetch_assoc($coder))) {
				if($clientID==$code['id']) $thisClient=$code;
				echo "<option value=\"" . $code['id'] . "\" ".($clientID==$code['id']?'selected="selected"':'').">" . $code['name'];
				if ($code['code']) {
					echo " - " . $code['code'];
				}
				echo "</option>\n";
			 }
			 echo "</select>\n";
		echo "</form>";
		 }
		 // -- CODEMENU ENDS
		 
		if($userinfo['admin']==1){
			$staffr = mysqli_query($dbc, "SELECT * FROM staff");
			 
		 if(mysqli_num_rows($staffr)>0){
			 // -- STAFFMENU START
		 echo "<form method=\"GET\" action=\"$PHP_SELF\" id='search_s' style='display:inline; margin-right:20px;'>\n";
			 echo "<select name=\"staffID\" onchange='document.forms[\"search_s\"].submit();'>\n";
			 echo "<option value=''>Sort by Staff</option>";
			 while ($staff = (mysqli_fetch_assoc($staffr))) {
				if($staffID==$staff['id']) $thisStaff=$staff;
				$fullname = $staff['firstname'] . " " . $staff['lastname'];
				echo "<option value=\"" . $staff['id'] . "\" ".($thisStaff['id']==$staff['id']?'selected="selected"':'').">" . $fullname;
				echo "</option>\n";
			 }
			 echo "</select>\n";
			 // -- STAFFMENU ENDS
		echo "</form>";
		 }
			if(!empty($staffID) && !empty($thisStaff)) echo " | <a href='staff_profile.php?staffID={$staffID}' class='main_btn'>edit {$thisStaff['firstname']}</a>";
			if(!empty($clientID) && !empty($thisClient)) {
				echo " | <a href='client_profile.php?clientID={$clientID}' class='main_btn'>edit {$thisClient['name']}</a>";
				if(mysqli_num_rows($result)>0)
					echo " | <a href='pdf.php?clientID={$clientID}' class='main_btn'>create report for {$thisClient['name']}</a>";
			}
			 
			 // echo "<input type=\"submit\" name=\"submit\" value=\"Add\">\n";
		}
	 echo "</form>";
	echo "</div>";  //  end main_select
	
?>