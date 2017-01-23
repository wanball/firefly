    <?php /* sidebar: style can be found in sidebar.less */?>
    <section class="sidebar">

      <?php /* Sidebar user panel (optional) */?>
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo $_SESSION['MEMBER_AVATAR']?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo $_SESSION['MEMBER_NAME']?></p>
          <?php /* Status */?>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>

      <?php /* search form (Optional) */?>
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <?php /* /.search form */?>

      <?php /* Sidebar Menu */?>
      <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        <?php /* Optionally, you can add icons to the links */?>
        <li id="menu0"><a href="home.php"><i class="fa fa-dashboard"></i> <span><?php echo $language['dashboard']?></span></a></li>
        
        <?php //menu 
$sql = "	        
SELECT sys_menu.*,
       sys_menu_name.sys_menu_name_loc,
       sys_menu_permisson.sys_menu_permisson_staus,

  (SELECT COUNT(sys_menu_id)
   FROM sys_menu
   LEFT JOIN sys_menu_permisson ON sys_menu_id = sys_menu_permisson_menu_id
   WHERE sys_menu_permisson_staus > 0
     AND sys_menu_parent = sys_menu_id) AS count_parent
FROM sys_menu
LEFT JOIN sys_menu_name ON sys_menu_id = sys_menu_name_pid
LEFT JOIN sys_menu_permisson ON sys_menu_id = sys_menu_permisson_menu_id
WHERE sys_menu_name_lang = '".$_SESSION['language']."'
  AND sys_menu_permisson_staus > 0 ORDER BY sys_menu_order ASC";
$stmt_menu = $conn->prepare($sql);
$stmt_menu->execute();
while($row_menu = $stmt_menu->fetch()){  
    $url = base64_encode($row_menu['sys_menu_id'].'|'.$row_menu['sys_menu_module_type'].'|index.php|'.$row_menu['sys_menu_module_key']); 
        ?>
        <li id="menu<?php echo $row_menu['sys_menu_id']?>">
        	<a href="home.php?m=<?php echo $url?>">
	        	<i class="fa fa-<?php echo $row_menu['sys_menu_icon']?>"></i>
	        	<span><?php echo $row_menu['sys_menu_name_loc']?></span>
	        </a>
	    </li>
<?php } ?>
        <li><a href="#"><i class="fa fa-link"></i> <span>Another Link</span></a></li>
        <li class="treeview">
          <a href="#"><i class="fa fa-link"></i> <span>Multilevel</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="#">Link in level 2</a></li>
            <li><a href="#">Link in level 2</a></li>
          </ul>
        </li>
        <li class="header">LABELS</li>
        <li><a href="library.php" id="galleryBtn"><i class="fa fa-circle-o text-lime"></i> <span><?php echo $language['gallery']?></span></a></li>
        <li><a href="#"><i class="fa fa-circle-o text-red"></i> <span>Important</span></a></li>
        <li><a href="#"><i class="fa fa-circle-o text-yellow"></i> <span>Warning</span></a></li>
        <li><a href="#"><i class="fa fa-circle-o text-aqua"></i> <span>Information</span></a></li>
      </ul>
      <?php /* /.sidebar-menu */?>
    </section>
    <?php /* /.sidebar */?>