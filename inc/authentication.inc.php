<?php		
	$query = $_SERVER['PHP_SELF'];
	$path = pathinfo( $query );
	if(isset($_SESSION['MEMBER_ID'])){
		userOnline($_SESSION['MEMBER_ID']);
		if($path['basename'] != 'home.php'){
			header('Location: home.php');
		}
	}else{
		if($path['basename'] != 'lockscreen.php'){
			header('Location: index.php');
		}	
	}
?>	