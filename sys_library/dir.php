<?php

if($level > 0){
	$sql = "SELECT sys_file_dir_name , sys_file_dir_level FROM sys_file_dir WHERE sys_file_dir_key = 'library' AND sys_file_dir_id = ".$level;
	$stmt_back = $conn->prepare($sql);
	$stmt_back->execute();
	$count = $stmt_back->rowCount();
	
	if($count == 0){
		echo '<script>';
		echo 'window.location.href = "library.php"';
		echo '</script>';
		exit();
	}else{	
		$row_back = $stmt_back->fetch();
		
		$dir_name =  $row_back['sys_file_dir_name'];
		$dir_level =  $row_back['sys_file_dir_level'];
	}	
	
}
								


//order select
$order_by_select =  array('','','','','','','','','','','');
$order_by[0] = 'sys_file_dir_name ASC';
$order_by[1] = 'sys_file_name ASC';
	
if(isset($_COOKIE['order_by'])) {
	$cookie_value = intval($_COOKIE['order_by']);
	
	switch ($cookie_value) {
	    case 1  :  $order_by[1] = 'sys_file_name ASC';    	 	$order_by_select[1] = 'selected="selected"'; break;
	    case 2  :  $order_by[1] = 'sys_file_name DESC';    	 	$order_by_select[2] = 'selected="selected"'; $order_by[0] = 'sys_file_dir_name DESC'; break;
	    case 3  :  $order_by[1] = 'sys_file_createDate ASC';  	$order_by_select[3] = 'selected="selected"'; $order_by[0] = 'sys_file_dir_createDate ASC'; break;
	    case 4  :  $order_by[1] = 'sys_file_createDate DESC';  	$order_by_select[4] = 'selected="selected"'; $order_by[0] = 'sys_file_dir_createDate DESC'; break;
	    case 5  :  $order_by[1] = 'sys_file_image_size ASC';  	$order_by_select[5] = 'selected="selected"'; break;
	    case 6  :  $order_by[1] = 'sys_file_image_size DESC';  	$order_by_select[6] = 'selected="selected"'; break;
	    case 7  :  $order_by[1] = 'sys_file_image_ext ASC';   	$order_by_select[7] = 'selected="selected"'; break;
	    case 8  :  $order_by[1] = 'sys_file_image_ext DESC';   	$order_by_select[8] = 'selected="selected"'; break;
	    case 9  :  $order_by[1] = 'sys_file_image_width ASC'; 	$order_by_select[9] = 'selected="selected"'; break;
	    case 10 :  $order_by[1] = 'sys_file_image_width DESC'; 	$order_by_select[10] = 'selected="selected"'; break;
	}	
	
	setcookie('order_by', $cookie_value, time() + 10000, "/"); 
}	
							
//folder
$dir_count = 0;
$display_data = '';	
$sql = "SELECT * FROM sys_file_dir WHERE sys_file_dir_key = 'library' AND sys_file_dir_level = :level AND sys_file_dir_name LIKE :search ORDER BY ".$order_by[0];
$stmt_dir = $conn->prepare($sql);
$stmt_dir->bindParam(':level', $level, PDO::PARAM_INT);
$stmt_dir->bindParam(':search', $text_search, PDO::PARAM_STR);
$stmt_dir->execute();
while($row_dir = $stmt_dir->fetch()){
	
	$dir = $target_dir2.'/'.$row_dir['sys_file_dir_path'];
	checkDir($dir);
	
	$display_data .= '<div class="col-sm-2 col-xs-3 img-wrap">';
	$display_data .= '<a href="library.php?l='.$row_dir['sys_file_dir_id'].'"><img src="images/folder.svg" alt=" " /> </a>';
	$display_data .= '<span><a href="library.php?l='.$row_dir['sys_file_dir_id'].'">'.$row_dir['sys_file_dir_name'].'</a></span>';
	$display_data .= '<div>dir</div>';
	$display_data .= '<div>'.dateShow('en','d','M','y','','',$row_dir['sys_file_dir_createDate']).'</div>';
	$display_data .= '<div></div>';
	$display_data .= '<div></div>';
	$display_data .= '<div></div>';
	$display_data .= '</div>';	
	
	chmod($dir, 0755);
	$dir_count++;
}	

//photo
$photo_count = 0;
$sql = "SELECT sys_file.* , sys_file_dir_path , sys_file_image.* FROM sys_file LEFT JOIN sys_file_dir ON sys_file.sys_file_dir_id = sys_file_dir.sys_file_dir_id LEFT JOIN sys_file_image ON sys_file_image_pid = sys_file_id WHERE sys_file_type = 'image' AND sys_file.sys_file_dir_key = 'library' AND sys_file_image_type = 'O' AND sys_file.sys_file_dir_id = :level ";


if(isset($_POST['text_search'])){
	$text_show = $_POST['text_search'];
	$text_search = "%$text_show%";
	$sql .= "AND sys_file_name LIKE ".$conn->quote($text_search);
	$text_show = htmlentities($text_show, ENT_COMPAT,'UTF-8');
}else{
	$text_show = '';
}

$sql .= " ORDER BY ".$order_by[1];

$stmt_dir = $conn->prepare($sql);
$stmt_dir->bindParam(':level', $level, PDO::PARAM_INT);
$stmt_dir->execute();
while($row_dir = $stmt_dir->fetch()){
	
	$dir = '';
	$file = '';
	if($row_dir['sys_file_dir_path'] != ''){
		$dir = $row_dir['sys_file_dir_path'].'/';
	}
	
	$file = $target_dir2.'/'.$dir.$row_dir['sys_file_path'];
	if(file_exists($file)){
	
		$file_thumb = $target_dir2.'/'.$dir.'thumbnail_'.$row_dir['sys_file_path'];
		
		if(!file_exists($file_thumb)){
			//create thumb
			include_once('plugins/abeautifulsite/SimpleImage.php');
			$img = new abeautifulsite\SimpleImage($file);			
			$img->thumbnail(240, 240)->save($file_thumb);	
		}	
	
	
		$display_data .= '<div class="col-sm-2 col-xs-3 img-wrap" id="row_'.$row_dir['sys_file_id'].'">';
		$display_data .= '<a href="library.php?preview='.$row_dir['sys_file_id'].'"><img src="'.$file_thumb.'" alt=" " class="imageShow" /> </a>';
		$display_data .= '<span><a href="library.php?preview='.$row_dir['sys_file_id'].'">'.$row_dir['sys_file_name'].'</a></span>';
		$display_data .= '<div>'.$row_dir['sys_file_image_ext'].'</div>';
		$display_data .= '<div>'.dateShow('en','d','M','y','','',$row_dir['sys_file_createDate']).'</div>';
		$display_data .= '<div>'.formatSizeUnits($row_dir['sys_file_image_size']).'</div>';
		$display_data .= '<div>'.$row_dir['sys_file_image_width'].'x'.$row_dir['sys_file_image_height'].'</div>';
		$display_data .= '<div><button type="button" class="btn btn-success dir_remove_btn" title="'.$language['delete'].'" data-id="'.$row_dir['sys_file_id'].'"><i class="fa fa-trash"></i></button></div>';
		$display_data .= '</div>';	
	
		$photo_count++;
	}	
}	
unset($file_thumb);
unset($file);
unset($dir);
unset($row_dir);
unset($stmt_dir);
unset($sql);

$_plugin_list = '
<script>
 var warning_text1 = \''.$language['name_alerady'].'\';
 var warning_text2 = \''.$language['error'].'\';
 var warning_text3 = \''.$language['create_new_folder'].'\';
 var warning_text4 = \''.$language['success'].'\';
 var warning_text5 = \''.$language['rename_folder'].'\';
 var warning_text6 = \''.$language['remove_folder'].'\';
 var warning_text7 = \''.$language['remove_file'].'\';
</script>

<script src="plugins/js.cookie.min.js"></script>
<script src="sys_library/script.js"></script>
';

?>	
            <div class="box box-success">
                <div class="box-header">
                    <div class="row">
                        <div class="col-sm-6 col-xs-12">
                            <div class="btn-group">
                                <button type="button" class="btn btn-success" id="home_btn" title="<?php echo $language['home']?>"><i class="fa fa-home"></i>
                                </button>
                            <?php if($level > 0){ ?>
								<button type="button" class="btn btn-success" id="back_btn" data-id="<?php echo $dir_level?>" title="<?php echo $language['back']?>"><i class="fa fa-chevron-circle-left"></i></button>  
                            <?php }  ?> 
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-success" id="new_dir_btn"  title="<?php echo $language['file_new_folder']?>"><i class="fa fa-folder"></i>
                                </button>
                                <button type="button" class="btn btn-success" id="new_file_btn" title="<?php echo $language['file_upload']?>" data-url="?l=<?php echo $level?>&amp;image"><i class="fa fa-cloud-upload"></i>
                                </button> 
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-success disabled" id="show_gallery" title="<?php echo $language['gallery']?>"><i class="fa fa-th-large"></i>
                                </button>
                                <button type="button" class="btn btn-success" id="show_list" title="<?php echo $language['file_list']?>"><i class="fa fa-th-list"></i>
                                </button>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-success" id="refresh_btn" title="<?php echo $language['refresh']?>"><i class="fa fa-refresh"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-sm-3 col-xs-6">
                            <select class="form-control" id="orderControl">
                                <option value="0" <?php echo $order_by_select[0]?> >
                                    <?php echo $language['sorting']?>
                                </option>
                                <option value="1" <?php echo $order_by_select[1]?> >
                                    <?php echo $language['filename'].' '.$language['sorting_low_to_high']?>
                                </option>
                                <option value="2" <?php echo $order_by_select[2]?> >
                                    <?php echo $language['filename'].' '.$language['sorting_high_to_low']?>
                                </option>
                                <option value="3" <?php echo $order_by_select[3]?> >
                                    <?php echo $language['date'].' '.$language['sorting_low_to_high']?>
                                </option>
                                <option value="4" <?php echo $order_by_select[4]?> >
                                    <?php echo $language['date'].' '.$language['sorting_high_to_low']?>
                                </option>
                                <option value="5" <?php echo $order_by_select[5]?> >
                                    <?php echo $language['size'].' '.$language['sorting_low_to_high']?>
                                </option>
                                <option value="6" <?php echo $order_by_select[6]?> >
                                    <?php echo $language['size'].' '.$language['sorting_high_to_low']?>
                                </option>
                                <option value="7" <?php echo $order_by_select[7]?> >
                                    <?php echo $language['type'].' '.$language['sorting_low_to_high']?>
                                </option>
                                <option value="8" <?php echo $order_by_select[8]?> >
                                    <?php echo $language['type'].' '.$language['sorting_high_to_low']?>
                                </option>
                                <option value="9" <?php echo $order_by_select[9]?> >
                                    <?php echo $language['dimension'].' '.$language['sorting_low_to_high']?>
                                </option>
                                <option value="10" <?php echo $order_by_select[10]?> >
                                    <?php echo $language['dimension'].' '.$language['sorting_high_to_low']?>
                                </option>
                            </select>
                        </div>

                        <div class="col-sm-3 col-xs-6">
                            <div class="box-tools">
	                            <form action="?<?php echo $_SERVER['QUERY_STRING']?>" method="post" name="seach_form">
                                <div class="input-group input-group-sm search_box">
                                    <input name="text_search" class="form-control pull-right" placeholder="<?php echo $language['search']?>" type="seach" value="<?php echo $text_show?>">

                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
	                            </form>
                            </div>
                        </div>
                    </div>
                    <?php if($level > 0){  ?>
                    <form method="post" action="?" id="edit_dirname">
                    <div class="row">
                    	<div class="col-sm-10  col-xs-8">
	                    	<h4 class="dirname"><?php echo $dir_name?></h4> 
	                    	<input class="dirname" type="text" name="dir_title" value="<?php echo $dir_name?>">
                    	</div>
                    	<div class="col-sm-2  col-xs-4 dirnameBtn">	 
                            <div class="btn-group pull-right">     
	                        	<button type="button" class="btn btn-success" id="edit_btn" title="<?php echo $language['edit']?>"><i class="fa fa-pencil"></i></button>
                            	<button type="button" class="btn btn-success" id="del_btn" title="<?php echo $language['delete']?>"><i class="fa fa-trash"></i></button>
                            </div>
                            <div class="btn-group pull-right hideBtn"> 	
	                        	<button type="button" class="btn btn-success" id="save_btn" title="<?php echo $language['submit_btn']?>"><i class="fa fa-floppy-o"></i></button>
                            	<button type="button" class="btn btn-success" id="cancel_btn" title="<?php echo $language['reset_btn']?>"><i class="fa fa-ban"></i></button> 
                            </div>	
	                    	<input type="submit" name="save" value="save">
                    	</div>
                	</div>
                    </form>
                	<?php } ?>
                </div>
                <div class="box-body">
	                <div class="row" id="block_list_title">
		                <div class="col-sm-12">
			                <div><?php echo $language['filename']?></div>
			                <div><?php echo $language['type']?></div>
			                <div><?php echo $language['date']?></div>
			                <div><?php echo $language['size']?></div>
			                <div><?php echo $language['dimension']?></div>
			                <div><?php echo $language['operation']?></div>
		                </div>
	                </div>
                    <div class="row" id="block_display">
                        <?php 
	                        echo $display_data;
	                        unset($display_data);
	                    ?>
                    </div>
                    <div class="row totalFile">
	                    <small>( 
	                    <?php 
		                    echo $photo_count;
		                    echo ' ';
		                    echo $language['file'];
		                    echo ' - ';
		                    echo $dir_count;
		                    echo ' ';
		                    echo $language['folder'];
		                ?>
		                )</small>
                    </div>
                </div>
            </div>