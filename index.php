<?php
require ("inc/config.inc.php");
require ("inc/connectdb.inc.php");
require ("inc/function.inc.php");
$language = language_data();

//goto lockscreen
if(isset($_COOKIE['remember_user'])){
	header('Location: lockscreen.php');
}
if(isset($_SESSION['MEMBER_ID'])){
	header('Location: home.php');
}

require ("plugins/Mobile_Detect.php");
$detect = new Mobile_Detect;
if ( $detect->isMobile() ) { 	
 	$background = 'id="backgroundMobile" ';
 	$background .= 'style="background-image: url(\'images/mobile-bg.jpg\')"';
    
}else{
	$background = 'style="background-image: url(\''.random_pic('gallery').'\')"';
}

$uid_display = '';
$check_staus = false;

if(isset($_POST['send'])){
	
	if(!isset($_SESSION['sigin_fail'])){
		$_SESSION['sigin_fail'] = 0;
	}
	
	$uid_display = $_POST['uid'];
	$my_user = My_Encode($_POST['uid'],_HASH_KEY_);
	$my_pass = crypt($_POST['pid'], '$6$rounds=5000$usesomesillystringforsalt$');
	
	if(isset($_POST['remember'])){
		if($_POST['remember'] == 1){
			$check_staus = true;
		}
	}
	
	//check country
	if(!isset($_SESSION['country'])){
		$_SESSION['country'] = authenticationLocation();	
	}
	
	if(isset($_POST['captcha'])){
		if (strcmp(trim($_POST['captcha']), $_SESSION['captcha_validate']) !== 0) {
			$captcha_check = false;	
		}else{
			$captcha_check = true;	
		}
	}else{
		$captcha_check = true;	
	}
	if($captcha_check){
		$stmt = $conn->prepare("SELECT * FROM mod_user WHERE mod_user_email = :email AND mod_user_staus = 1");
		$stmt->bindParam(':email', $my_user);
		try{
		    $stmt->execute();
		    $count = $stmt->rowCount();
		    if($count == 1){
			    $row = $stmt->fetch();
				if (strcmp($row['mod_user_pass'], $my_pass) !== 0) {
					$_SESSION['sigin_fail']++;
					logAccess('รหัสผ่านไม่ถูกต้อง');
				}else{
					$_SESSION['sigin_fail'] = 0;
					logAccess('ล็อคอินสำเร็จ');
					userOnline($row['mod_user_id']);
					
					if($check_staus){
						setcookie('remember_user', $row['mod_user_id'], time() + (86400 * 365), "/");	
					}
					
					//update
					$update = "";
					$update[] = "mod_user_lastdate 	= NOW()";
			
					$sql = "UPDATE mod_user SET  " . implode(",", $update) . " WHERE mod_user_id = ".$row['mod_user_id'];				
					$stmt = $conn->prepare($sql);
					$stmt->execute();		
					
					$path_avatar = _UPLOAD_DIR_.'avatar/user_'.$row['mod_user_id'].'/current';
					if(file_exists($path_avatar.'/'.$row['mod_user_avatar'])){
						$path_avatar = $path_avatar.'/'.$row['mod_user_avatar'];
					}else{
						$path_avatar = 'images/blank_avatar.svg';
					}
					
					$_SESSION['MEMBER_ID'] = $row['mod_user_id'];
					$_SESSION['MEMBER_NAME'] = $row['mod_user_name'];
					$_SESSION['MEMBER_AVATAR'] = $path_avatar;
					$_SESSION['MEMBER_POSITION'] = $row['mod_user_position'];
					$_SESSION['MEMBER_CREATE'] = $row['mod_user_createdate'];
					
					header('Location: home.php');
				}		    
		    }else{
				$_SESSION['sigin_fail']++;
				logAccess('ไม่มีอีเมล์นี้');
		    }
		    
		}catch(PDOException $e){
			$_SESSION['sigin_fail']++;
			logAccess('ไม่สามารถเชื่อมต่อ db ได้');
		}
	}		
	
}


	if(isset($_SESSION['sigin_fail']) AND (intval($_SESSION['sigin_fail']) >= 3)){ 
	    $script_recaptcha = '<script src="https://www.google.com/recaptcha/api.js"></script>';
	    $disabled = 'disabled="disabled"';
	}else{
		$script_recaptcha = '';
		$disabled = '';
	}
	      
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require ("inc/meta.inc.php"); ?>
</head>
<body class="hold-transition login-page" <?php echo $background?>>
<div class="login-box">
  <div class="login-logo">
    <a href="<?php echo _BACK_OFFICE_PATH_?>"><b>WEBENGINE</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg"><?php echo $language['sign_in_to_start']?></p>

    <form action="?" method="post">
      <div class="form-group has-feedback">
        <input type="email" name="uid" class="form-control" placeholder="<?php echo $language['email']?>" required="" value="<?php echo $uid_display?>">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="pid" class="form-control" placeholder="<?php echo $language['password']?>" required="" <?php /* pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" oninvalid="setCustomValidity('<?php echo $language['pass-warning']?>')" onchange="try{setCustomValidity('')}catch(e){}" */ ?> >
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox" name="remember" value="1"> <?php echo $language['remember_me']?>
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" name="send" id="btnSend" class="btn btn-primary btn-block btn-flat" <?php echo $disabled?> ><?php echo $language['sign_in']?></button>
        </div>
        <!-- /.col -->
      </div>
      <?php if($script_recaptcha != ''){ ?>
      <div class="row">
        <div class="col-xs-12">
	        <div class="g-recaptcha" data-callback="recaptchaCallback" data-sitekey="<?php echo _reCAPTCHA_ID_?>"></div>
	        <input type="hidden" name="captcha" value="" id="captcha" required="">
        </div>
      </div>
      <?php } ?>
    </form>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- REQUIRED JS SCRIPTS -->

<?php require ("inc/plugins.inc.php"); ?>   
<?php echo $script_recaptcha?>
<!-- iCheck -->
<script src="template/plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%',
      activeClass: 'active',
    });
<?php if($check_staus){ ?>   
    $('input').iCheck('check');
<?php } ?>    
  });
<?php if($script_recaptcha != ''){ ?>
function recaptchaCallback() {
	$.post( "captcha-ajax.php", function( data ) {
	  $( "#captcha" ).val( data );
	  $('#btnSend').attr('disabled',false);
	});
};
<?php } ?>  
</script>
</body>
</html>
<?php 
CloseDB();
checkLogFile();	
?>