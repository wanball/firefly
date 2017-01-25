<?php
//first row
$sql = "SELECT sys_menu_name_loc , sys_menu_icon , mod_cms_config_depth FROM sys_menu LEFT JOIN sys_menu_name ON sys_menu_id = sys_menu_name_pid LEFT JOIN mod_cms_config ON sys_menu_module_key = mod_cms_config_module_key WHERE sys_menu_name_lang = '".$_SESSION['language']."' AND sys_menu_module_key = '".$moduleKey."'";
$stmt_permisson = $conn->prepare($sql);
$stmt_permisson->execute();
$row_permisson = $stmt_permisson->fetch();	
$breadcrumb_name = $row_permisson['sys_menu_name_loc'];
$breadcrumb_icon = $row_permisson['sys_menu_icon'];
$breadcrumb_active = '';
$config_depth = $row_permisson['mod_cms_config_depth'];

//level
if(isset($_GET['l'])){
	$level = intval($_GET['l']);
}else{
	$level = 1;
}
//parent
if(isset($_GET['p'])){
	$parent = intval($_GET['p']);
}else{
	$parent = 0;
}

$moduleEncode = base64_encode($menu_id.'|mod_cms|index.php|'.$moduleKey);

if($level == 0){
	$breadcrumb_active = '<li>'.$row_permisson['sys_menu_name_loc'].'</li>';	
}else{
	$breadcrumb_active  = '<li>';
	$breadcrumb_active .= '<a href="home.php?m='.$moduleEncode.'">';
	$breadcrumb_active .= $row_permisson['sys_menu_name_loc'];
	$breadcrumb_active .= '</a>';	
	$breadcrumb_active .= '</li>';	
}



for($index = 2; $index <= $level; $index++){
	
	if($index != $level){
		$breadcrumb_level = ($index-1);
		$cms_group_id = "(SELECT mod_cms_group_parent FROM mod_cms_group WHERE mod_cms_group_id = ".$parent." AND mod_cms_group_module_key = '".$moduleKey."' AND mod_cms_group_level = ".($level-1).")";
	}else{
		$breadcrumb_level = ($level-1);
		$cms_group_id = $parent;
	}
	
	
	$sql = "SELECT mod_cms_group_id , mod_cms_group_name_loc FROM mod_cms_group LEFT JOIN mod_cms_group_name ON mod_cms_group_id = mod_cms_group_name_pid WHERE mod_cms_group_id = ".$cms_group_id." AND mod_cms_group_module_key = '".$moduleKey."' AND mod_cms_group_level = ".$breadcrumb_level." AND mod_cms_group_name_lang = '".$_SESSION['language']."'";
	$stmt_breadcrumb = $conn->prepare($sql);
	$stmt_breadcrumb->execute();
	$row_breadcrumb = $stmt_breadcrumb->fetch();
	$href_page = 'home.php?l='.$index.'&amp;p='.$row_breadcrumb['mod_cms_group_id'].'&amp;m='.$moduleEncode;	
	$breadcrumb_active .= '<li>';
	$breadcrumb_active .= '<a href="'.$href_page.'">';
	$breadcrumb_active .= $row_breadcrumb['mod_cms_group_name_loc'];
	$breadcrumb_active .= '</a>';	
	$breadcrumb_active .= '</li>';	
	
	$breadcrumb_name = $row_breadcrumb['mod_cms_group_name_loc'];
}
unset($breadcrumb_level);
unset($cms_group_id);



$href_page = 'home.php?l='.$level.'&amp;p='.$parent.'&amp;m='.$moduleEncode; 

//btn create
if($level == $config_depth){
	$btn_create['link'] = 'home.php?l='.$level.'&amp;p='.$parent.'&amp;m='.base64_encode($menu_id.'|mod_cms|create_content.php|'.$moduleKey); 
	$btn_create['name'] = $language['cms_create_content'];
}else{
	$btn_create['link'] = 'home.php?l='.$level.'&amp;p='.$parent.'&amp;m='.base64_encode($menu_id.'|mod_cms|create_group.php|'.$moduleKey); 
	$btn_create['name'] = $language['cms_create_group'];
}

$_plugin_list .= '

<!-- iCheck -->
<link rel="stylesheet" href="template/plugins/iCheck/flat/blue.css">
<script src="template/plugins/iCheck/icheck.min.js"></script>

<!-- timeago -->
<script src="plugins/timeago/jquery.timeago.min.js"></script>';
if($_SESSION['language'] == 'th'){
$_plugin_list .= '
<script src="plugins/timeago/jquery.timeago.th.js"></script>';	
}
$_plugin_list .= '
<link rel="stylesheet" href="mod_contact/contact.css" />
<script src="mod_contact/contact.js"></script>
';
?>

<?php /* Content Header (Page header) */?>
<section class="content-header">
   <h1>
      <?php echo $breadcrumb_name?>
      <small><?php /*Optional description*/?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="home.php"><i class="fa fa-<?php echo $breadcrumb_icon?>"></i> <?php echo $language['home']?></a></li>
      <?php echo $breadcrumb_active?>
   </ol>
</section>

    <? /* Main content */ ?>
    <section class="content">
      <div class="row">
        <? /* /.col */ ?>
        <div class="col-md-12">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Inbox</h3>

              <div class="box-tools pull-right">
                <div class="has-feedback">
                  <input type="text" class="form-control input-sm" placeholder="Search Mail">
                  <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>
              </div>
              <? /* /.box-tools */ ?>
            </div>
            <? /* /.box-header */ ?>
            <div class="box-body no-padding">
              <div class="mailbox-controls">
                <? /* Check all button */ ?>
                <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                </button>
                <div class="btn-group">
                  <button type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                  <button type="button" class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                  <button type="button" class="btn btn-default btn-sm"><i class="fa fa-share"></i></button>
                </div>
                <? /* /.btn-group */ ?>
                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                <div class="pull-right">
				<?php
				    $perpage = 20;
				    if(isset($_GET['pg'])){
						$pagging = intval($_GET['pg']);
					}else{
						$pagging = 0;
					}
					$page = $pagging;
					if($pagging > 0){
						$pagging = ($pagging-1) * $perpage;
					} 
					 	                
	                $sql = "SELECT mod_cotact_id , mod_cotact_title , mod_cotact_detail , mod_cotact_createDate , mod_cotact_staus , mod_cotact_fav_and_parent , (
    CASE mod_cotact_memberStaus
        WHEN 'guest'  THEN (SELECT mod_cotact_guest_name FROM mod_cotact_guest WHERE mod_cotact_guest_id = mod_cotact_createBy ) 
        WHEN 'member' THEN 'member'
        WHEN 'system' THEN (SELECT mod_user_name FROM mod_user WHERE mod_user_id = mod_cotact_createBy)
    END) AS owner_name,
    
    (SELECT COUNT(mod_cotact_type) FROM mod_cotact AS B WHERE mod_cotact_type = 'reply' AND B.mod_cotact_fav_and_parent = A.mod_cotact_id AND mod_cotact_staus != 'delete') AS Reply ,
    (SELECT COUNT(mod_cotact_attachment_pid) FROM mod_cotact_attachment WHERE mod_cotact_attachment_pid = A.mod_cotact_id ) AS Attachment
    
					FROM mod_cotact AS A
					WHERE mod_cotact_module_key = '".$moduleKey."'
					  AND mod_cotact_type = 'post'
					  AND mod_cotact_staus != 'delete'";
					  
					if(isset($_POST['table_search'])){ //bindValue search
						$sql .= " AND mod_cotact_title LIKE :keywords";
						$sql .= " AND mod_cotact_detail LIKE :keywords";
					}   
					
	                $stmt_group = $conn->prepare($sql);
	                
	                if(isset($_POST['table_search'])){ //bindValue search
	                	$stmt_group->bindValue(':keywords', '%' . $_POST['table_search'] . '%');
	                }
					$stmt_group->execute();
					$contentCount = $stmt_group->rowCount();
					$sql = $sql . " ORDER BY mod_cotact_createDate DESC LIMIT ".$pagging.",".$perpage;
					    

				?>
							                
                  <?php 
	                  echo $pagging+1;
	                  echo '-';
	                  echo $pagging+$perpage;
	                  echo '/';
	                  echo $contentCount; 
	              ?>
                  <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                  </div>
                  <? /* /.btn-group */ ?>
                </div>
                <? /* /.pull-right */ ?>
              </div>
              <div class="table-responsive mailbox-messages">
                <table class="table table-hover table-striped">
                  <tbody>
                <?php	
	                $stmt_group = $conn->prepare($sql);
					$stmt_group->execute();			
					while($row_group = $stmt_group->fetch()){ 
						$detail = strRip($row_group['mod_cotact_detail'],100);
						
						$read_staus = '';
						if($row_group['mod_cotact_staus'] == 'read'){
							$read_staus = 'class="read"';
						}
						
						$fav_staus = 'fa-star-o';
						if($row_group['mod_cotact_fav_and_parent'] == 1){
							$fav_staus = 'fa-star';
						}
						
						$reply_staus = '';
						if($row_group['Reply'] > 0){
							$reply_staus = '<i class="fa fa-reply"></i>';
						}
						
						$attachment_staus = '';
						if($row_group['Attachment'] > 0){
							$attachment_staus = '<i class="fa fa-paperclip"></i>';
						}
						
						
				?>			                  
                  <tr <?php echo $read_staus; ?>>
                    <td class="mailbox-checkbox"><input type="checkbox"></td>
                    <td class="mailbox-star"><a href="#"><i class="fa <?php echo $fav_staus; ?> text-yellow"></i></a></td>
                    <td class="mailbox-reply"><?php echo $reply_staus; ?></td>
                    <td class="mailbox-name"><a href="read-mail.html"><?php echo $row_group['owner_name']; ?></a></td>
                    <td class="mailbox-subject">
	                    <span><b><?php echo $row_group['mod_cotact_title']; ?></b> <?php echo $detail; ?></span>
                    </td>
                    <td class="mailbox-attachment"><?php echo $attachment_staus; ?></td>
                    <td class="mailbox-date">
                      <time class="timeago" datetime="<?php echo DateISO8601($row_group['mod_cotact_createDate']); ?>">
                        <?php echo dateShow($_SESSION['language'],'d','M','Y','','',$row_group['mod_cotact_createDate']); ?>
                      </time>
                      </td>
                  </tr>
				<?php } ?>  
                  </tbody>
                </table>
                <? /* /.table */ ?>
              </div>
              <? /* /.mail-box-messages */ ?>
            </div>
            <? /* /.box-body */ ?>
            <div class="box-footer no-padding">
              <div class="mailbox-controls">
                <? /* Check all button */ ?>
                <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                </button>
                <div class="btn-group">
                  <button type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                  <button type="button" class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                  <button type="button" class="btn btn-default btn-sm"><i class="fa fa-share"></i></button>
                </div>
                <? /* /.btn-group */ ?>
                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                <div class="pull-right">
                  1-50/200
                  <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                  </div>
                  <? /* /.btn-group */ ?>
                </div>
                <? /* /.pull-right */ ?>
              </div>
            </div>
          </div>
          <? /* /. box */ ?>
        </div>
        <? /* /.col */ ?>
      </div>
      <? /* /.row */ ?>
    </section>
    <? /* /.content */ ?>