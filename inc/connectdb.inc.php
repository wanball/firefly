<?php
/*================ FunctionDB ====================*/
$conn;
ConnectDB();
//เชื่อมฐานข้อมูล
function ConnectDB() {
	global $conn;

	try { 
		if(_DATA_BASE_TYPE_ == 'mysql'){
			
		$conn = new PDO ("mysql:host="._DATA_BASE_HOST_.";dbname="._DATA_BASE_NAME_,_DATA_BASE_USER_,_DATA_BASE_PASS_); 
			
		}else if(_DATA_BASE_TYPE_ == 'mssql'){
			
		$conn = new PDO("sqlsrv:server="._DATA_BASE_HOST_.";Database="._DATA_BASE_NAME_,_DATA_BASE_USER_,_DATA_BASE_PASS_);
			
		}
		$conn -> exec("SET CHARACTER SET utf8");
	} catch (PDOException $e) { 
		header('Location:'._BACK_OFFICE_PATH_.'404.html');
		//echo $e;
	}

	return $conn;
}
//เลิกติดต่อฐานข้อมูล
function CloseDB() {
	global $conn;
	$conn = null;
}

?>