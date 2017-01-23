<?php
//first row
$sql = "SELECT sys_menu_name_loc , sys_menu_icon , mod_cms_config_lang FROM sys_menu LEFT JOIN sys_menu_name ON sys_menu_id = sys_menu_name_pid LEFT JOIN mod_cms_config ON sys_menu_module_key = mod_cms_config_module_key WHERE sys_menu_name_lang = '".$_SESSION['language']."' AND sys_menu_module_key = '".$moduleKey."'";
$stmt_permisson = $conn->prepare($sql);
$stmt_permisson->execute();
$row_permisson = $stmt_permisson->fetch();	
$breadcrumb_name = $row_permisson['sys_menu_name_loc'];
$breadcrumb_icon = $row_permisson['sys_menu_icon'];

$breadcrumb_active[0] = '<li>'.$row_permisson['sys_menu_name_loc'].'</li>';

$config_lang = explode('|', $row_permisson['mod_cms_config_lang']);

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
?>	
<?php /* Content Header (Page header) */?>
<section class="content-header">
   <h1>
      <?php echo $breadcrumb_name?>
      <small><?php /*Optional description*/?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="home.php"><i class="fa fa-<?php echo $breadcrumb_icon?>"></i> <?php echo $language['home']?></a></li>
      <?php echo $breadcrumb_active[0]?>
      <li class="active"><?php echo $language['cms_create_group']?></li>
   </ol>
</section>

<!-- Main content -->
<section class="content" id="cms_page">
   <!-- /.row -->
   <div class="row">
      <div class="col-xs-12">
         <div class="box box-warning">
            <div class="box-header with-border">
               <h3 class="box-title"><?php echo $language['cms_create_group']?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
               <form role="form" name="form1" method="post" action="mod_cms/cms_action.php">
                  <!-- text input -->
	              <?php foreach ($config_lang as $key => $value) { 
		              $sql_lang = "SELECT mas_languages_native , mas_languages_name FROM mas_languages WHERE mas_languages_iso = '".$value."'";
		              $stmt_lang = $conn->prepare($sql_lang);
					  $stmt_lang->execute();
					  $row_lang = $stmt_lang->fetch();
	              ?> 
                  <div class="form-group">   
                     <label><?php echo $language['group_name']?> (<?php echo $row_lang['mas_languages_native']?>)</label>
                     <input type="text" class="form-control" name="group_name[<?php echo $value?>]"  placeholder="<?php echo $row_lang['mas_languages_name']?>" required >
                  </div>
                  <?php } ?>
                  <!-- textarea -->
                  <div class="form-group">
                     <label><?php echo $language['notes']?></label>
                     <textarea class="form-control" rows="3" name="notes"></textarea>
                     <input type="hidden" name="level" value="<?php echo $level?>" />
                     <input type="hidden" name="parent" value="<?php echo $parent?>" />
                     <input type="hidden" name="moduleKey" value="<?php echo $moduleKey?>" />
                     <input type="hidden" name="menu" value="<?php echo $menu_id?>" />
                     <input type="hidden" name="action" value="create_group" />
                  </div>
	              <!-- /.box-body -->
	              <div class="box-footer">
	                <button type="button" class="btn btn-default" id="btn_back"><?php echo $language['cancel_btn']?></button>
	                <button type="submit" name="submit_btn" class="btn btn-primary pull-right"><?php echo $language['submit_btn']?></button>
	              </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>