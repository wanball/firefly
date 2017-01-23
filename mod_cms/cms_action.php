<?php
require ("../inc/config.inc.php");
require ("../inc/connectdb.inc.php");
require ("../inc/function.inc.php");

$action = $_POST['action'];
$userid = intval($_SESSION['MEMBER_ID']); 	
$moduleKey  = $_POST['moduleKey'];

if($userid == 0){
	header('Location: ../404.html');
}else if(isset($_POST['moduleKey'])){
	if($action == 'create_group'){
		$level = intval($_POST['level']);
		$parent = intval($_POST['parent']);
		
		unset($insert);
		$insert['mod_cms_group_parent'] 	= ":parent";
		$insert['mod_cms_group_module_key'] = ":moduleKey";
		$insert['mod_cms_group_level'] 		= ":level";
		$insert['mod_cms_group_notes'] 		= ":notes";
		$insert['mod_cms_group_createby'] 	= "'".$userid."'";
		$insert['mod_cms_group_createdate'] = "NOW()";		
		
		$sql = "INSERT INTO mod_cms_group (" . implode(",", array_keys($insert)) . ") VALUES (" . implode(",", array_values($insert)) . ")";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':parent', $parent , PDO::PARAM_INT);
		$stmt->bindParam(':moduleKey', $moduleKey);
		$stmt->bindParam(':level', $level, PDO::PARAM_INT);
		$stmt->bindParam(':notes', $_POST['notes']);

		$stmt->execute();
		$pid = $conn->lastInsertId();	

		foreach($_POST['group_name'] as $key => $value) {
			unset($insert);
			$insert['mod_cms_group_name_pid'] 	= intval($pid);
			$insert['mod_cms_group_name_lang'] 	= ":lang";
			$insert['mod_cms_group_name_loc'] 	= ":loc";		
			
			$sql = "INSERT INTO mod_cms_group_name (" . implode(",", array_keys($insert)) . ") VALUES (" . implode(",", array_values($insert)) . ")";
			
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':lang', $key);
			$stmt->bindParam(':loc', $value);	
			$stmt->execute();	
		}		
		
		$url = base64_encode($_POST['menu'].'|mod_cms|index.php|'.$moduleKey); 
		
		header('Location: ../home.php?l='.$level.'&p='.$parent.'&m='.$url);
	}else if($action == 'create_content'){
		$level = intval($_POST['level']);
		$parent = intval($_POST['parent']);

		if($_POST['start_date'] == ''){
			$start_date = '0000-00-00 00:00:00';
		}else{
			$start_date = DateToDB('d/m/Y H:i',$_POST['start_date']);
		}

		if($_POST['end_date'] == ''){
			$end_date = '0000-00-00 00:00:00';
		}else{
			$end_date = DateToDB('d/m/Y H:i',$_POST['end_date']);
		}

		switch ($_POST['staus_post']) {
			case "public" : $staus = 'public'; break;
			case "pending": $staus = 'pending'; break;
			case "private": $staus = 'private'; break;
		}
		
		unset($insert);
		$insert['mod_cms_post_cid'] 		= ":parent";
		$insert['mod_cms_post_module_key'] 	= ":moduleKey";
		$insert['mod_cms_post_start_date'] 	= ":start_date";
		$insert['mod_cms_post_end_date'] 	= ":end_date";
		$insert['mod_cms_post_createby'] 	= "'".$userid."'";
		$insert['mod_cms_post_createdate'] 	= "NOW()";	
		$insert['mod_cms_post_staus'] 		= ":staus";
			
		$sql = "INSERT INTO mod_cms_post (" . implode(",", array_keys($insert)) . ") VALUES (" . implode(",", array_values($insert)) . ")";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':parent', $parent , PDO::PARAM_INT);
		$stmt->bindParam(':moduleKey', $moduleKey);
		$stmt->bindParam(':start_date', $start_date);
		$stmt->bindParam(':end_date', $end_date);
		$stmt->bindParam(':staus', $staus);

		$stmt->execute();
		$pid = $conn->lastInsertId();		
		
		$language 	= $_POST['language_post'];
		$loc 		= $_POST['post_title'];

		//insert title
		unset($insert);
		$insert['mod_cms_data_pid'] 		= $pid;
		$insert['mod_cms_data_pattern'] 	= "0";
		$insert['mod_cms_data_lang'] 		= ":language";
		$insert['mod_cms_data_loc'] 		= ":loc";
			
		$sql = "INSERT INTO mod_cms_data (" . implode(",", array_keys($insert)) . ") VALUES (" . implode(",", array_values($insert)) . ")";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':language', $language, PDO::PARAM_STR, 2);
		$stmt->bindParam(':loc', $loc, PDO::PARAM_STR);

		$stmt->execute();			
		
		$url = base64_encode($_POST['menu'].'|mod_cms|view_content.php|'.$moduleKey); 
		
		header('Location: ../home.php?l='.$level.'&p='.$parent.'&m='.$url);
					
	}
}	
CloseDB();
?>