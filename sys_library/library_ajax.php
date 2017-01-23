<?php
require ("../inc/config.inc.php");
require ("../inc/connectdb.inc.php");
require ("../inc/function.inc.php");

$name = trim($_POST['name']);
if($name == 'rename_dir'){
	$userid = intval($_SESSION['MEMBER_ID']);
	$dir_id = intval($_POST['level']);  
	$name = trim(strip_tags($_POST['new_name']));

	$update = "";
			
	$update[] = "sys_file_dir_name 		= :name";
		
	$sql = "UPDATE sys_file_dir SET " . implode(",", $update) . " WHERE sys_file_dir_id = ".$dir_id;				
	$stmt = $conn->prepare($sql);
	$stmt->bindParam(':name', $name);
			
	$stmt->execute();	
				
	logAccess('ได้เปลียน โฟเดอร์ ชื่อ '.$_POST['old_name'].' กลายเป็น '.$_POST['new_name']);
	
}else if($name == 'del_dir'){	
	
	$target_dir1 = '../'._UPLOAD_DIR_;
	$target_dir2 = $target_dir1."filemanager";
	checkDir($target_dir1);
	checkDir($target_dir2);
			
	$level = intval($_POST['level']);
	$sql = "SELECT sys_file_dir_level , sys_file_dir_path FROM sys_file_dir WHERE sys_file_dir_key = 'library' AND sys_file_dir_id = ".$level;
	$stmt_dir = $conn->prepare($sql);
	$stmt_dir->execute();
	$row_dir = $stmt_dir->fetch();
	
	$back = $row_dir['sys_file_dir_level'];
	
	find_and_remove_dir($level);

	//remove file in dir
	$sql = "SELECT sys_file_id FROM sys_file WHERE sys_file_dir_key = 'library' AND sys_file_dir_id = ".$level;
	$stmt_file = $conn->prepare($sql);
	$stmt_file->execute();
	while($row_file = $stmt_file->fetch()){
		find_and_remove_image($row_file['sys_file_id'],$target_dir2);
	}

	$sql_file = "DELETE FROM sys_file_dir WHERE sys_file_dir_id = ".$level;	
	$stmt_file = $conn->prepare($sql_file);
	$stmt_file->execute();	
			
	$path = $target_dir2.'/'.$row_dir['sys_file_dir_path'];
	chmod($path, 0777);
	rmdir($path);
		
	chmod($target_dir2, 0755);
	chmod($target_dir1, 0755);	
	
	logAccess('ได้ลบ โฟเดอร์ ชื่อ '.$_POST['dir_name']);
	
	//OPTIMIZE
	$sql_file = "OPTIMIZE TABLE sys_file; OPTIMIZE TABLE sys_file_dir; OPTIMIZE TABLE sys_file_image;"; 
	$stmt_file = $conn->prepare($sql_file);
	$stmt_file->execute();	
	
	
	echo $back;
	
}else if($name == 'rename_file'){
	$userid = intval($_SESSION['MEMBER_ID']);
	$file_id = intval($_POST['level']);  
	$name = trim(strip_tags($_POST['new_name']));

	$update = "";
			
	$update[] = "sys_file_name 		= :name";
		
	$sql = "UPDATE sys_file SET " . implode(",", $update) . " WHERE sys_file_id = ".$file_id;			
	$stmt = $conn->prepare($sql);
	$stmt->bindParam(':name', $name);
			
	$stmt->execute();	
				
	logAccess('ได้เปลียน ไฟล์ ชื่อ '.$_POST['old_name'].' กลายเป็น '.$_POST['new_name']);
	
}else if($name == 'del_file'){	
	
	$target_dir1 = '../'._UPLOAD_DIR_;
	$target_dir2 = $target_dir1."filemanager";
	checkDir($target_dir1);
	checkDir($target_dir2);
			
	$preview_id = intval($_POST['level']);

	$back = find_and_remove_image($preview_id,$target_dir2);
	
	chmod($target_dir2, 0755);
	chmod($target_dir1, 0755);	
	
	logAccess('ได้ลบ ไฟล์ ชื่อ '.$_POST['dir_name']);
	
	echo $back;
		
}else if($name != ''){
	$userid = intval($_SESSION['MEMBER_ID']); 	
	if(isset($_POST['level'])){
		$level = intval($_POST['level']);
	}else{
		echo 0;
		exit();
	}
	
	$sql = "SELECT sys_file_dir_path FROM sys_file_dir WHERE sys_file_dir_key = 'library' AND sys_file_dir_name = :name AND sys_file_dir_level = ".$level;
	$stmt_dir = $conn->prepare($sql);
	$stmt_dir->bindParam(':name', $name);
	$stmt_dir->execute();
	$contentCount = $stmt_dir->rowCount();
	
	if($contentCount > 0){
		echo 2;
		exit();
	}else{
	
		$target_dir1 = '../'._UPLOAD_DIR_;
		$target_dir2 = $target_dir1."filemanager";
		checkDir($target_dir1);
		checkDir($target_dir2);
		
		$dir_name = time().'_'.rand(111,999);
		$path_name = $target_dir2.'/'.$dir_name;
		
		checkDir($path_name);
		
		unset($insert);
		$insert['sys_file_dir_key'] 		= "'library'";
		$insert['sys_file_dir_name'] 		= ":name";
		$insert['sys_file_dir_path'] 		= ":path";
		$insert['sys_file_dir_level'] 		= ":level";
		$insert['sys_file_dir_createby'] 	= "'".$userid."'";
		$insert['sys_file_dir_createDate'] 	= "NOW()";		
		
		$sql = "INSERT INTO sys_file_dir (" . implode(",", array_keys($insert)) . ") VALUES (" . implode(",", array_values($insert)) . ")";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':name', $name);
		$stmt->bindParam(':path', $dir_name);
		$stmt->bindParam(':level', $level, PDO::PARAM_INT);
	
		$stmt->execute();	
		
		
		CloseDB();
		chmod($path_name, 0755);
		chmod($target_dir2, 0755);
		chmod($target_dir1, 0755);	
		
		logAccess('ได้สร้าง โฟเดอร์ ชื่อ '.$name.' ('.$dir_name.') ขึ้นมา');
		
		echo 1;
	}
	
}else{
	echo 0;
}

function find_and_remove_dir($id){
	global $conn;
	$level = intval($id);

	$target_dir = '../'._UPLOAD_DIR_."filemanager";
		
	$sql = "SELECT sys_file_dir_id , sys_file_dir_path FROM sys_file_dir WHERE sys_file_dir_key = 'library' AND sys_file_dir_level = ".$level;
	$stmt_dir = $conn->prepare($sql);
	$stmt_dir->execute();
	while($row_dir = $stmt_dir->fetch()){
	
		$dir_id = $row_dir['sys_file_dir_id'];
		$path = $row_dir['sys_file_dir_path'];
		
		checkDir($target_dir.'/'.$path);
		
		$sql = "SELECT sys_file_id , sys_file_path FROM sys_file WHERE sys_file_dir_key = 'library' AND sys_file_dir_id = ".$dir_id;
		$stmt_file = $conn->prepare($sql);
		$stmt_file->execute();
		while($row_file = $stmt_file->fetch()){	
			
			$sql_list = "SELECT sys_file_image_type , sys_file_image_ext FROM sys_file_image WHERE sys_file_image_type != 'O' AND sys_file_image_pid = ".$row_file['sys_file_id']; 
			$stmt_list = $conn->prepare($sql_list);
			$stmt_list->execute();
			while($row_list = $stmt_list->fetch()){	
				
				$find = '.'.$row_list['sys_file_image_ext'];
				$replace = '_'.$row_list['sys_file_image_type'].'.'.$row_list['sys_file_image_ext'];
				
				$file = $target_dir.'/'.$path.'/'.str_replace($find,$replace,$row_file['sys_file_path']);
				if(file_exists($file)){
					@chmod($file, 0777);
					@unlink($file);
				}
			}
			
			$file = $target_dir.'/'.$path.'/'.$row_file['sys_file_path'];
			@chmod($file, 0777);
			@unlink($file);
			
			$file = $target_dir.'/'.$path.'/thumbnail_'.$row_file['sys_file_path'];
			@chmod($file, 0777);
			@unlink($file);
			
			
			$sql_list = "DELETE FROM sys_file_image WHERE sys_file_image_pid = ".$row_file['sys_file_id'];	
			$stmt_list = $conn->prepare($sql_list);
			$stmt_list->execute();	
		}	

		$sql_file = "DELETE FROM sys_file WHERE sys_file_dir_id = ".$row_dir['sys_file_dir_id'];	
		$stmt_file = $conn->prepare($sql_file);
		$stmt_file->execute();	

		$sql_file = "DELETE FROM sys_file_dir WHERE sys_file_dir_id = ".$dir_id;	
		$stmt_file = $conn->prepare($sql_file);
		$stmt_file->execute();	
							
		rmdir($target_dir.'/'.$path);

		find_and_remove_dir($dir_id);

	}
		
}

function find_and_remove_image($preview_id,$target_dir2){
	global $conn;
	$sql = "SELECT sys_file.sys_file_dir_id , sys_file_dir.sys_file_dir_path , sys_file_path FROM sys_file LEFT JOIN sys_file_dir ON sys_file_dir.sys_file_dir_id = sys_file.sys_file_dir_id WHERE sys_file.sys_file_dir_key = 'library' AND sys_file_id = ".$preview_id;
	$stmt_dir = $conn->prepare($sql);
	$stmt_dir->execute();
	$row_dir = $stmt_dir->fetch();
	
	$back = $row_dir['sys_file_dir_id'];
	
	if($row_dir['sys_file_dir_path'] != ''){
		$target_dir = $target_dir2."/".$row_dir['sys_file_dir_path'];
		checkDir($target_dir);
	}else{
		$target_dir = $target_dir2;
	}
		
	$file_path =  $row_dir['sys_file_path'];	
		
	$sql = 'SELECT * FROM sys_file_image WHERE sys_file_image_pid = '.$preview_id.' ORDER BY sys_file_image_height DESC';
	$stmt2 = $conn->prepare($sql);
	$stmt2 -> execute();
								
	while($row2 = $stmt2->fetch()){
								
		if($row2['sys_file_image_type'] == 'O'){
			$check_file = $target_dir.'/'.$file_path;
			$image = $file_path;
		}else{
			$ext = '.'.$row2['sys_file_image_ext'];
			$image = $file_path.'_'.$row2['sys_file_image_type'];
			$image = str_replace($ext,'',$image);
			$image .= $ext;
			$check_file = $target_dir.'/'.$image;
		}			
		if(file_exists($check_file)){
			@chmod($check_file, 0777);
			@unlink($check_file);
		}
	}
	
	//thumbnail
	$file = $target_dir.'/thumbnail_'.$file_path;
	if(file_exists($file)){
		@chmod($file, 0777);
		@unlink($file);
	}

	$sql_list = "DELETE FROM sys_file_image WHERE sys_file_image_pid = ".$preview_id;	
	$stmt_list = $conn->prepare($sql_list);
	$stmt_list->execute();	

	$sql_file = "DELETE FROM sys_file WHERE sys_file_id = ".$preview_id;	
	$stmt_file = $conn->prepare($sql_file);
	$stmt_file->execute();	
					
	chmod($target_dir, 0755);
	
	return $back;
}	