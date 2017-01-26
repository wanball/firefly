<?php
require ("../inc/config.inc.php");
require ("../inc/connectdb.inc.php");
require ("../inc/function.inc.php");

if(isset($_SESSION['MEMBER_ID'])){
	if($_POST['type'] == 'delete'){
		$id =  intval($_POST['id']);
		//update
		$update = "";
		
		$update[] = "mod_cotact_staus 		= 'delete'";
			
		$sql = "UPDATE mod_cotact SET  " . implode(",", $update) . " WHERE mod_cotact_id = ".$id;				
		$stmt = $conn->prepare($sql);
			
		$stmt->execute();	
				
		logAccess('ลบการติดต่อ เลขที่ '.$id);		
	}	
}
CloseDB();
?>