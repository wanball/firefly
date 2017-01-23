<?php
//user staus
$profileID = intval($_GET['profile']);
if($profileID == 0){
	$profileID = $_SESSION['MEMBER_ID'];
}	
$sql = "SELECT mod_user_online_pid FROM mod_user_online WHERE (mod_user_online_datetime > NOW() - INTERVAL 10 MINUTE) AND mod_user_online_pid = ".$profileID;
$stmt = $conn->prepare($sql);
$stmt->execute();		
$count = $stmt->rowCount();
if($count == 1){
	$online_staus = '<i class="fa fa-circle text-success"></i> '.$language['online'];
}else{
	$online_staus = '<i class="fa fa-circle text-danger"></i> '.$language['offline'];
}

$sql = "SELECT * FROM mod_user WHERE mod_user_id = ".$profileID;
$stmt = $conn->prepare($sql);
$stmt->execute();		
$row = $stmt->fetch();

$path_avatar = _UPLOAD_DIR_.'avatar/user_'.$row['mod_user_id'].'/current';
if(file_exists($path_avatar.'/'.$row['mod_user_avatar'])){
	$path_avatar = $path_avatar.'/'.$row['mod_user_avatar'];
}else{
	$path_avatar = 'images/blank_avatar.svg';
}

$user_email = My_Decode($row['mod_user_email'],_HASH_KEY_);
$dateIso = DateISO8601($row['mod_user_lastdate']);
$dateShow = dateShow('en','d','F','Y','H','i',$row['mod_user_lastdate']);

$_plugin_list .= '
<!-- Croppie -->
<link rel="stylesheet" href="plugins/Croppie/croppie.css" />
<script src="plugins/Croppie/croppie.min.js"></script>

<!-- exif -->
<script src="plugins/exif.js"></script>

<!-- timeago -->
<script src="plugins/timeago/jquery.timeago.min.js"></script>';
if($_SESSION['language'] == 'th'){
$_plugin_list .= '
<script src="plugins/timeago/jquery.timeago.th.js"></script>';	
}

$_plugin_list .= '
<link rel="stylesheet" href="mod_profile/profile.css" />
<script src="mod_profile/profile.js"></script>
<script>
 var warning_text1 = \''.$language['pass-warning2'].'\';
 var warning_text2 = \''.$language['pass-warning3'].'\';
 var warning_text3 = \''.$language['pass-warning4'].'\';
 var warning_text4 = \''.$language['profile_email_warning'].'\';
';

if(isset($_GET['error1'])){
	$_plugin_list .= "swal('".$language['error']."', warning_text1, 'error');";
}else if(isset($_GET['error2'])){
	$_plugin_list .= "swal('".$language['error']."', warning_text2, 'error');";
}else if(isset($_GET['error3'])){
	$_plugin_list .= "swal('".$language['error']."', warning_text3, 'error');";
}else if(isset($_GET['success'])){
	$_plugin_list .= "swal('".$language['success']."', '".$language['pass-success']."', 'success');";
}else if(isset($_GET['update'])){
	$_plugin_list .= "swal('".$language['success']."', '".$language['profile_update']."', 'success');";
}



$_plugin_list .= '
</script>
';
?>
    <?php /* Content Header (Page header) */?>
    <section class="content-header">
      <h1>
        <?php echo $language['profile']?>
        <small><?php /*Optional description*/?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> <?php echo $language['home']?></a></li>
        <li class="active"><?php echo $language['user_profile']?></li>
      </ol>
    </section>

    <?php /* Main content */?>
    <section class="content">

      <div class="row">
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo $path_avatar?>" alt="<?php echo $row['mod_user_name']?>">

              <h3 class="profile-username text-center"><?php echo $row['mod_user_name']?></h3>

              <p class="text-muted text-center"><?php echo $row['mod_user_position']?></p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b><?php echo $language['staus']?></b> <a class="pull-right"><?php echo $online_staus?></a>
                </li>
                <li class="list-group-item">
                  <b><?php echo $language['email']?></b> <a class="pull-right" href="mailto:<?php echo $user_email?>"><?php echo $user_email?></a>
                </li>
                <li class="list-group-item">
                  <b><?php echo $language['lastlogin']?></b> 
                  	<a class="pull-right">
	                  	<time class="timeago" datetime="<?php echo $dateIso?>"><?php echo $dateShow?></time>
	                </a>
                </li>
              </ul>

            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->		              
        </div>  
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#timeline" data-toggle="tab"><?php echo $language['timeline']?></a></li>
              <li><a href="#settings" data-toggle="tab"><?php echo $language['setting']?></a></li>
              <li><a href="#avatar" data-toggle="tab"><?php echo $language['update_avatar']?></a></li>
              <li><a href="#changepass" data-toggle="tab"><?php echo $language['password']?></a></li>
            </ul>
            <div class="tab-content">
              
              <!-- /.tab-pane -->
              <div class="active tab-pane" id="timeline">
                <!-- The timeline -->
                <ul class="timeline timeline-inverse">
                  <!-- timeline time label -->
                  <li class="time-label">
                        <span class="bg-red">
                          10 Feb. 2014
                        </span>
                  </li>
                  <!-- /.timeline-label -->
                  <!-- timeline item -->
                  <li>
                    <i class="fa fa-envelope bg-blue"></i>

                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>

                      <h3 class="timeline-header"><a href="#">Support Team</a> sent you an email</h3>

                      <div class="timeline-body">
                        Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                        weebly ning heekya handango imeem plugg dopplr jibjab, movity
                        jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                        quora plaxo ideeli hulu weebly balihoo...
                      </div>
                      <div class="timeline-footer">
                        <a class="btn btn-primary btn-xs">Read more</a>
                        <a class="btn btn-danger btn-xs">Delete</a>
                      </div>
                    </div>
                  </li>
                  <!-- END timeline item -->
                  <!-- timeline item -->
                  <li>
                    <i class="fa fa-user bg-aqua"></i>

                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock-o"></i> 5 mins ago</span>

                      <h3 class="timeline-header no-border"><a href="#">Sarah Young</a> accepted your friend request
                      </h3>
                    </div>
                  </li>
                  <!-- END timeline item -->
                  <!-- timeline item -->
                  <li>
                    <i class="fa fa-comments bg-yellow"></i>

                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock-o"></i> 27 mins ago</span>

                      <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>

                      <div class="timeline-body">
                        Take me to your leader!
                        Switzerland is small and neutral!
                        We are more like Germany, ambitious and misunderstood!
                      </div>
                      <div class="timeline-footer">
                        <a class="btn btn-warning btn-flat btn-xs">View comment</a>
                      </div>
                    </div>
                  </li>
                  <!-- END timeline item -->
                  <!-- timeline time label -->
                  <li class="time-label">
                        <span class="bg-green">
                          3 Jan. 2014
                        </span>
                  </li>
                  <!-- /.timeline-label -->
                  <!-- timeline item -->
                  <li>
                    <i class="fa fa-camera bg-purple"></i>

                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock-o"></i> 2 days ago</span>

                      <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>

                      <div class="timeline-body">
                        <img src="http://placehold.it/150x100" alt="..." class="margin">
                        <img src="http://placehold.it/150x100" alt="..." class="margin">
                        <img src="http://placehold.it/150x100" alt="..." class="margin">
                        <img src="http://placehold.it/150x100" alt="..." class="margin">
                      </div>
                    </div>
                  </li>
                  <!-- END timeline item -->
                  <li>
                    <i class="fa fa-clock-o bg-gray"></i>
                  </li>
                </ul>
              </div>
              <!-- /.tab-pane -->

              <div class="tab-pane" id="settings">

	                <form class="form-horizontal" method="post" action="mod_profile/profile-action.php">
	                  <div class="form-group">
	                    <label for="inputName" class="col-sm-2 control-label"><?php echo $language['profile_name']?></label>
	
	                    <div class="col-sm-10">
	                      <input type="text" class="form-control" name="Profile_Name" id="Profile_Name" placeholder="<?php echo $language['profile_name']?>" value="<?php echo $row['mod_user_name']?>" >
	                    </div>
	                  </div>
	                  <div class="form-group">
	                    <label for="inputEmail" class="col-sm-2 control-label"><?php echo $language['email']?></label>
	
	                    <div class="col-sm-10">
	                      <input type="email" class="form-control" name="Profile_Email" id="Profile_Email" placeholder="<?php echo $language['email']?>" value="<?php echo $user_email?>" readonly>
	                    </div>
	                  </div>
	                  <div class="form-group">
	                    <label for="inputName" class="col-sm-2 control-label"><?php echo $language['profile_postition']?></label>
	
	                    <div class="col-sm-10">
	                      <input type="text" class="form-control" name="Profile_Postition" id="Profile_Postition" placeholder="<?php echo $language['profile_postition']?>" value="<?php echo $row['mod_user_position']?>">
	                    </div>
	                  </div>
	                  <div class="form-group">
	                    <label for="inputName" class="col-sm-2 control-label"><?php echo $language['profile_phone']?></label>
	
	                    <div class="col-sm-10">
	                      <input type="text" class="form-control" name="Profile_Phone" id="Profile_Phone" placeholder="<?php echo $language['profile_phone']?>" value="<?php echo $row['mod_user_phone']?>">
	                    </div>
	                  </div>
	                  <div class="form-group">
	                    <label for="inputExperience" class="col-sm-2 control-label"><?php echo $language['profile_other']?></label>
	
	                    <div class="col-sm-10">
	                      <textarea class="form-control" id="Profile_Other" name="Profile_Other" placeholder="<?php echo $language['profile_other']?>"><?php echo $row['mod_user_other']?></textarea>
	                    </div>
	                  </div>

	                  <div class="form-group">
	                    <div class="col-sm-offset-2 col-sm-10">
		                  <input type="hidden" value="change_profile" name="action" id="action"  >
		                  <input type="hidden" value="<?php echo $profileID?>" name="MID" >
	                      <button type="submit" class="btn btn-danger">Submit</button>
	                    </div>
	                  </div>
	                </form>

              </div>
              <!-- /.tab-pane -->

              <div class="tab-pane" id="avatar">
			      <div class="row">
			        <div class="col-md-4 col-md-offset-4">
					    <div class="upload-msg"><?php echo $language['choose_file']?></div>
					    <div id="upload-demo"></div>
					    <div class="actions">
					        <a class="btn btn-primary btn-lg file-btn col-md-4">
					            <span>Upload</span>
					            <input type="file" id="upload" value="Choose a file" accept="images/*" />
					        </a>
					        <button class="upload-result btn btn-primary btn-lg col-md-4 pull-right">Save</button>
					        <div class="clearfix"></div>
					    </div>			        
			        </div>
			      </div>    	              
              </div>    
              <!-- /.tab-pane -->

              <div class="tab-pane" id="changepass">

	                <form class="form-horizontal" method="post" action="mod_profile/profile-action.php" onsubmit="return checkForm(this);">
	                  <div class="form-group">
	                    <label for="inputName" class="col-sm-2 control-label"><?php echo $language['password']?></label>
	
	                    <div class="col-sm-10 textChangePass">
		                    
	                      <?php
		                      if($row['mod_user_updatepass'] == '0000-00-00 00:00:00'){
			                     echo '<span class="text-red">';
			                     echo $language['notChangePass']; 
			                     echo '</span>';
		                      }else{
			                    $dateIso = DateISO8601($row['mod_user_updatepass']);
								$dateShow = dateShow('en','d','F','Y','H','i',$row['mod_user_updatepass']);
								
								$newEndingDate = strtotime(date("Y-m-d", strtotime($row['mod_user_updatepass'])) . " + 365 day");
								
								if($newEndingDate < strtotime(date("Y-m-d"))){
									echo '<span class="text-red">';
								}else{
									echo '<span>';
								}
								
		                      	echo $language['lastChangePass'];
		                      	echo ' ';
		                      	echo '<time class="timeago" datetime="'.$dateIso.'">'.$dateShow.'</time>';
			                    echo '</span>';
		                      }	
		                    ?>
		                    </p>
	                    </div>
	                  </div>
	                  <div class="form-group">
	                    <label for="inputEmail" class="col-sm-2 control-label"><?php echo $language['current']?></label>
	
	                    <div class="col-sm-10" id="password1">
	                      <input type="password" class="form-control" name="password1" required="required" placeholder="*************" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" oninvalid="setCustomValidity('<?php echo $language['pass-warning']?>')" onchange="try{setCustomValidity('')}catch(e){}">
	                      <span class="help-block"></span>
	                    </div>
	                  </div>
	                  <div class="form-group">
	                    <label for="inputName" class="col-sm-2 control-label"><?php echo $language['new']?></label>
	
	                    <div class="col-sm-10" id="password2">
	                      <input type="password" class="form-control" name="password2" required="required" placeholder="*************" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" oninvalid="setCustomValidity('<?php echo $language['pass-warning']?>')" onchange="try{setCustomValidity('')}catch(e){}">
	                      <span class="help-block"></span>
	                    </div>
	                  </div>
	                  <div class="form-group">
	                    <label for="inputExperience" class="col-sm-2 control-label"><?php echo $language['re-type-new']?></label>
	
	                    <div class="col-sm-10" id="password3">
	                      <input type="password" class="form-control" name="password3" required="required" placeholder="*************" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" oninvalid="setCustomValidity('<?php echo $language['pass-warning']?>')" onchange="try{setCustomValidity('')}catch(e){}">
	                      <span class="help-block"></span>
	                    </div>
	                  </div>

	                  <div class="form-group">
	                    <div class="col-sm-offset-2 col-sm-10">
		                  <input type="hidden" value="0" name="pass_staus" id="pass_staus"  >
		                  <input type="hidden" value="change_pass" name="action" id="action"  >
		                  <input type="hidden" value="<?php echo $profileID?>" name="MID" >
	                      <button type="submit" class="btn btn-danger">Submit</button>
	                    </div>
	                  </div>
	                </form>

              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
 
 

                 
        </div>  
    </section>
    <?php /* /.content */?>