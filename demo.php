<?php
header('Content-Type: text/html; charset=utf-8');
require ("inc/config.inc.php");
require ("inc/connectdb.inc.php");
require ("inc/function.inc.php");

for($i=0;$i<=100000;$i++){
	logAccess('ลองใส่ข้อมูล');
}

?>