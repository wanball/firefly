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

		$sql_pattern = "SELECT mod_cms_pattern_id , mod_cms_pattern_format FROM mod_cms_pattern WHERE mod_cms_pattern_modulekey = '".$moduleKey."' AND mod_cms_pattern_parent = 0 ORDER BY mod_cms_pattern_order ASC"; 
		$stmt_pattern = $conn->prepare($sql_pattern);
		$stmt_pattern->execute();
		while($row_pattern = $stmt_pattern->fetch()){
			$pattern_id = $row_pattern['mod_cms_pattern_id'];


			unset($insert);

			if($row_pattern['mod_cms_pattern_format'] <= 23){

				$patter_name = 'pattern_'.$pattern_id;

				switch ($row_pattern['mod_cms_pattern_format']) {
				case 3:
				case 5:
					$loc = '';
					if(isset($_POST[$patter_name]) && is_array($_POST[$patter_name])){
						$loc = implode("|:|",$_POST[$patter_name]);
					}
					break;
				case 4:
					$loc = '';
					if(isset($_POST[$patter_name])){
						$loc = trim($_POST[$patter_name]);
					}
					break;
				case 8:
					if($_POST[$patter_name] == '0.00'){
						$loc = '';
					}else{
						$loc = trim($_POST[$patter_name]);
					}
					break;
				case 22:
					$patter_name = 'pattern_district_'.$pattern_id;
					$loc = trim($_POST[$patter_name]);
					break;
				case 23:
					$patter_name = 'pattern_sub_district_'.$pattern_id;
					$loc = trim($_POST[$patter_name]);
					break;
				default:
					$loc = trim($_POST[$patter_name]);
					break;
				}

				if($loc != ''){

					$insert['mod_cms_data_pid'] 		= $pid;
					$insert['mod_cms_data_pattern'] 	= ":pattern";
					$insert['mod_cms_data_lang'] 		= ":language";
					$insert['mod_cms_data_loc'] 		= ":loc";
						
					$sql = "INSERT INTO mod_cms_data (" . implode(",", array_keys($insert)) . ") VALUES (" . implode(",", array_values($insert)) . ")";
					
					$stmt = $conn->prepare($sql);
					$stmt->bindParam(':language', $language, PDO::PARAM_STR, 2);
					$stmt->bindParam(':loc', $loc, PDO::PARAM_STR);
					$stmt->bindParam(':pattern', $pattern_id, PDO::PARAM_INT);

					$stmt->execute();	
				}
			}else{
				$target_dir1 = '../'._UPLOAD_DIR_;
				$target_dir2 = $target_dir1."cms";
				checkDir($target_dir1);
				checkDir($target_dir2); 

					if($row_pattern['mod_cms_pattern_format']==26){

					}else{
						$patter_name = 'fileUpload_'.$pattern_id;
						if(is_array($_POST[$patter_name])){
							foreach ($_POST[$patter_name] as $key => $value) {

								$loc = trim($value);
								$type = getExt($loc);
								$type = getMimes($type);	
								$order_item = ($key+1);	
								//move_file
								rename($target_dir1."temp/".$loc, $target_dir1."cms/".$loc);

								unset($insert);
								$insert['mod_cms_attachment_pid'] 		= $pid;
								$insert['mod_cms_attachment_pattern'] 	= ":pattern";
								$insert['mod_cms_attachment_lang'] 		= ":language";
								$insert['mod_cms_attachment_loc'] 		= ":loc";
								$insert['mod_cms_attachment_type'] 		= ":type";
								$insert['mod_cms_attachment_order'] 		= ":order_item";
									
								$sql = "INSERT INTO mod_cms_attachment (" . implode(",", array_keys($insert)) . ") VALUES (" . implode(",", array_values($insert)) . ")";
								
								$stmt = $conn->prepare($sql);
								$stmt->bindParam(':language', $language, PDO::PARAM_STR, 2);
								$stmt->bindParam(':loc', $loc, PDO::PARAM_STR);
								$stmt->bindParam(':pattern', $pattern_id, PDO::PARAM_INT);
								$stmt->bindParam(':type', $type, PDO::PARAM_STR);
								$stmt->bindParam(':order_item', $order_item, PDO::PARAM_INT);

								$stmt->execute();
							}							
						}
					}

            	chmod($target_dir2 , 0755);
            	chmod($target_dir1 , 0755);
			
			}			
		}

		$url = base64_encode($_POST['menu'].'|mod_cms|view_content.php|'.$moduleKey); 
		
		header('Location: ../home.php?l='.$level.'&p='.$parent.'&m='.$url);
					
	}
}	
CloseDB();
?>