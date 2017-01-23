<?php
// A list of permitted file extensions
$allowed = array('png', 'jpg', 'gif','jpeg');
require ("../inc/config.inc.php");
require ("../inc/connectdb.inc.php");
require ("../inc/function.inc.php");

if(isset($_FILES['upload']) && $_FILES['upload']['error'] == 0){



$userid = intval($_SESSION['MEMBER_ID']); 	
	
	$original_name = $_FILES['upload']['name'];
	$extension = pathinfo($original_name, PATHINFO_EXTENSION);
	$extension = strtolower($extension);
	
	$original_name_without_ext = str_replace('.'.$extension,'',$original_name);

	if(!in_array($extension, $allowed)){
		echo '{"status":"error","log":"error1"}';
		logAccess('ได้พยายาม อัพโหลดไฟล์ ');
		exit;
	}
	
	if(isset($_GET['l'])){
		$level = intval($_GET['l']);
		
		$target_dir = '../'._UPLOAD_DIR_."filemanager";
		checkDir($target_dir);	
		
		if($level > 0){
		
			$sql = "SELECT sys_file_dir_path FROM sys_file_dir WHERE sys_file_dir_key = 'library' AND sys_file_dir_id = ".$level;
			$stmt_back = $conn->prepare($sql);
			$stmt_back->execute();
			$row_back = $stmt_back->fetch();
				
			$target_dir = $target_dir."/".$row_back['sys_file_dir_path'];
		
		}
		
		$name['O'] = time().'_'.rand(1111,9999);
		$name['X'] = $name['O'].'_X.'.$extension;
		$name['L'] = $name['O'].'_L.'.$extension;
		$name['M'] = $name['O'].'_M.'.$extension;
		$name['S'] = $name['O'].'_S.'.$extension;
		$name['O'] = $name['O'].'.'.$extension;
										
		checkDir($target_dir);	
			
		//	
		if(move_uploaded_file($_FILES['upload']['tmp_name'], $target_dir.'/'.$name['O'])){

			unset($insert);
			$insert['sys_file_dir_key'] 		= "'library'";
			$insert['sys_file_dir_id'] 			= $level;
			$insert['sys_file_name'] 			= ":name";
			$insert['sys_file_path'] 			= ":path";
			$insert['sys_file_type'] 			= "'image'";
			$insert['sys_file_createby'] 		= "'".$userid."'";
			$insert['sys_file_createDate'] 		= "NOW()";		
			
			$sql = "INSERT INTO sys_file (" . implode(",", array_keys($insert)) . ") VALUES (" . implode(",", array_values($insert)) . ")";
			
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':name', $original_name_without_ext);
			$stmt->bindParam(':path', $name['O']);
		
			$stmt->execute();	
			$pid = $conn->lastInsertId();	
		
			//resize
			include('../plugins/abeautifulsite/SimpleImage.php');
			
			$img = new abeautifulsite\SimpleImage($target_dir.'/'.$name['O']);
			
			$file_name = $target_dir.'/'.$name['O'];
			$width = $img->get_width();
			$height = $img->get_height();
			$type = 'O';
						
			unset($insert);
			$insert['sys_file_image_pid'] 		= $pid;
			$insert['sys_file_image_type'] 		= ":type";
			$insert['sys_file_image_size'] 		= ":size";
			$insert['sys_file_image_width'] 	= ":width";
			$insert['sys_file_image_height'] 	= ":height";
			$insert['sys_file_image_ext'] 		= "'".$extension."'";
			
			$sql = "INSERT INTO sys_file_image (" . implode(",", array_keys($insert)) . ") VALUES (" . implode(",", array_values($insert)) . ")";
			
			$file_size = filesize($file_name);

			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':type', $type , PDO::PARAM_STR, 1);
			$stmt->bindParam(':size', $file_size , PDO::PARAM_INT);
			$stmt->bindParam(':width', $width , PDO::PARAM_INT);
			$stmt->bindParam(':height', $height , PDO::PARAM_INT);
		
			$stmt->execute();	
			
			if($height > 2160){
				$file_name = $target_dir.'/'.$name['X'];
				$height = 2160;
				$img->fit_to_height($height)->save($file_name);
				$width = $img->get_width();
				$type = 'X';
				$file_size = filesize($file_name);
				
				$stmt = $conn->prepare($sql);
				$stmt->bindParam(':type', $type , PDO::PARAM_STR, 1);
				$stmt->bindParam(':size', $file_size , PDO::PARAM_INT);
				$stmt->bindParam(':width', $width , PDO::PARAM_INT);
				$stmt->bindParam(':height', $height , PDO::PARAM_INT);
			
				$stmt->execute();
			}
			if($height > 1080){
				$file_name = $target_dir.'/'.$name['L'];
				$height = 1080;
				$img->fit_to_height($height)->save($file_name);
				$width = $img->get_width();
				$type = 'L';
				$file_size = filesize($file_name);
				
				$stmt = $conn->prepare($sql);
				$stmt->bindParam(':type', $type , PDO::PARAM_STR, 1);
				$stmt->bindParam(':size', $file_size , PDO::PARAM_INT);
				$stmt->bindParam(':width', $width , PDO::PARAM_INT);
				$stmt->bindParam(':height', $height , PDO::PARAM_INT);
			
				$stmt->execute();
			}	
			if($height > 720){
				$file_name = $target_dir.'/'.$name['M'];
				$height = 720;
				$img->fit_to_height($height)->save($file_name);
				$width = $img->get_width();
				$type = 'M';
				$file_size = filesize($file_name);
				
				$stmt = $conn->prepare($sql);
				$stmt->bindParam(':type', $type , PDO::PARAM_STR, 1);
				$stmt->bindParam(':size', $file_size , PDO::PARAM_INT);
				$stmt->bindParam(':width', $width , PDO::PARAM_INT);
				$stmt->bindParam(':height', $height , PDO::PARAM_INT);
			
				$stmt->execute();
			}	
			if($height > 480){
				$file_name = $target_dir.'/'.$name['S'];
				$height = 480;
				$img->fit_to_height($height)->save($file_name);
				$width = $img->get_width();
				$type = 'S';
				$file_size = filesize($file_name);
				
				$stmt = $conn->prepare($sql);
				$stmt->bindParam(':type', $type , PDO::PARAM_STR, 1);
				$stmt->bindParam(':size', $file_size , PDO::PARAM_INT);
				$stmt->bindParam(':width', $width , PDO::PARAM_INT);
				$stmt->bindParam(':height', $height , PDO::PARAM_INT);
			
				$stmt->execute();
			}
			
			$img->thumbnail(240, 240)->save($target_dir.'/thumbnail_'.$name['O']);		
			
			chmod($target_dir, 0755);
						
			echo '{"status":"success"}';
			logAccess('ได้สร้าง อัพโหลดไฟล์ '.$original_name.' ('.$name['O'].') ขึ้นมา');
			//echo $target_dir.'/'.$name;
			exit;
		}else{
			chmod($target_dir, 0755);
			
			echo '{"status":"error","log":"error2"}';
			logAccess('ได้พยายาม อัพโหลดไฟล์ ');
			exit;
		}
	}else{
		echo '{"status":"error","log":"error3"}';
		logAccess('ได้พยายาม อัพโหลดไฟล์ ');
		exit;
	}


CloseDB();		
}

echo '{"status":"error","log":"error4"}';
logAccess('ได้พยายาม อัพโหลดไฟล์ ');
exit;