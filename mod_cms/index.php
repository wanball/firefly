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
<!-- Slimscroll -->
<script src="template/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="template/plugins/fastclick/fastclick.js"></script>

<!-- timeago -->
<script src="plugins/timeago/jquery.timeago.min.js"></script>';
if($_SESSION['language'] == 'th'){
$_plugin_list .= '
<script src="plugins/timeago/jquery.timeago.th.js"></script>';	
}

$_plugin_list .= '
<link rel="stylesheet" href="mod_cms/cms.css" />
<script src="mod_cms/cms.js"></script>
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

<!-- Main content -->
<section class="content" id="cms_page">
   <!-- /.row -->
   <div class="row">
      <div class="col-xs-12">
         <div class="box">
            <div class="box-header">
               <a class="box-title btn btn-primary col-xs-2" href="<?php echo $btn_create['link']?>"><span><?php echo $btn_create['name']?></span></a>
               <div class="box-tools pull-right">
	              <form method="post" action="<?php echo $href_page?>" name="search_form">  
                  <div class="input-group input-group-sm">  
                     <input type="search" name="table_search" class="form-control pull-right" placeholder="<?php echo $language['search']?>">
                     <div class="input-group-btn">
                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                     </div> 
                  </div>
	              </form>  
               </div>
               <div class="clearfix"></div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
               <table class="table table-hover" id="group_table">
                  <tr>
                     <th><?php echo $language['title']?></th>
                     <th><?php echo $language['author']?></th>
                     <th><?php echo $language['date']?></th>  
                     <th><?php echo $language['action']?></th>                   
                  </tr>
                <?php
				    $perpage = 10;
				    if(isset($_GET['pg'])){
						$pagging = intval($_GET['pg']);
					}else{
						$pagging = 0;
					}
					$page = $pagging;
					if($pagging > 0){
						$pagging = ($pagging-1) * $perpage;
					} 
					 	                
	                $sql = "SELECT mod_cms_group_id,
					       mod_cms_group_name_loc,
					       mod_cms_group_createdate,
					       mod_user_name
					FROM mod_cms_group
					LEFT JOIN mod_cms_group_name ON mod_cms_group_id = mod_cms_group_name_pid
					LEFT JOIN mod_user ON mod_user_id = mod_cms_group_createby
					WHERE mod_cms_group_parent = ".$parent."
					  AND mod_cms_group_module_key = '".$moduleKey."'
					  AND mod_cms_group_level = ".$level."
					  AND mod_cms_group_name_lang = '".$_SESSION['language']."'";
					  
					if(isset($_POST['table_search'])){ //bindValue search
						$sql .= " AND mod_cms_group_name_loc LIKE :keywords";
					}   
					
	                $stmt_group = $conn->prepare($sql);
	                
	                if(isset($_POST['table_search'])){ //bindValue search
	                	$stmt_group->bindValue(':keywords', '%' . $_POST['table_search'] . '%');
	                }
					$stmt_group->execute();
					$contentCount = $stmt_group->rowCount();
					$sql = $sql . " LIMIT ".$pagging.",".$perpage;
					    
					$stmt_group = $conn->prepare($sql);
	                
	                if(isset($_POST['table_search'])){ //bindValue search
	                	$stmt_group->bindValue(':keywords', '%' . $_POST['table_search'] . '%');
	                }
	                
					$stmt_group->execute();
					$pageCount = $stmt_group->rowCount();
					
					while($row_group = $stmt_group->fetch()){ 
						$dateIso = DateISO8601($row_group['mod_cms_group_createdate']);
						$dateShow = dateShow('en','d','F','Y','H','i',$row_group['mod_cms_group_createdate']);
						$href_page2 = 'home.php?l='.($level+1).'&amp;p='.$row_group['mod_cms_group_id'].'&amp;m='.$moduleEncode; 

				?>
                  <tr>
                     <td><a href="<?php echo $href_page2?>"><?php echo $row_group['mod_cms_group_name_loc']?></a></td>
                     <td><?php echo $row_group['mod_user_name']?></td>
                     <td><time class="timeago" datetime="<?php echo $dateIso?>"><?php echo $dateShow?></time></td>
                     <td>
	                     <div>
						 	<a class="btn btn-primary" title="<?php echo $language['edit']?>">
						 	<i class="fa fa-pencil-square-o"></i>
						 	</a>
						 	<a class="btn btn-primary" title="<?php echo $language['delete']?>">
						 	<i class="fa fa-trash-o"></i>
						 	</a>
						</div>
					</td>
                  </tr>
				<?php	} ?>  
               </table>
            </div>
            <!-- /.box-body -->
            <div class="box-footer no-padding">
              <div class="mailbox-controls">
                <div class="pull-right">
	            <?php //pagging
		          $totalPage = ceil($contentCount/$perpage);
				  
				  if(($page-1) == 0){
					  $href_prev = '#';
					  $class_prev = 'disabled';
				  }else{					  
					  $href_prev = $href_page.'&amp;pg='.($page-1);
					  $class_prev = '';
				  }
				  if($page == $totalPage){
					  $href_next = '#';
					  $class_next = 'disabled';
				  }else{					  
					  $href_next = $href_page.'&amp;pg='.($page+1);
					  $class_next = '';
				  }
				  
				  //show detail
				  echo $pagging+1;
				  echo '-';
				  echo $pagging+$pageCount;
				  echo '/';
				  echo $contentCount;
				?>  
                  <div class="btn-group">
                    <a class="btn btn-default btn-sm <?php echo $class_prev?>" href="<?php echo $href_prev?>"><i class="fa fa-chevron-left"></i></a>
                    <a class="btn btn-default btn-sm <?php echo $class_next?>" href="<?php echo $href_next?>"><i class="fa fa-chevron-right"></i></a>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.pull-right -->
                <div class="clearfix"></div>
              </div>
            </div>
          <!-- /. box -->
         </div>
      </div>
   </div>
</section>       