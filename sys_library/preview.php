 <?php
$preview_id = intval($_GET['preview']);

if($preview_id > 0){

	$sql = "SELECT sys_file.* , sys_file_dir_path FROM sys_file LEFT JOIN sys_file_dir ON sys_file.sys_file_dir_id = sys_file_dir.sys_file_dir_id WHERE sys_file_type = 'image' AND sys_file.sys_file_dir_key = 'library' AND sys_file.sys_file_id = ".$preview_id." ORDER BY sys_file_name ASC";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$count = $stmt->rowCount();
	
	if($count == 0){
		header('Location: library.php');
	}else{	
		$row = $stmt->fetch();
		$file_name =  $row['sys_file_name'];
		$file_path =  $row['sys_file_path'];
		$dir_level =  $row['sys_file_dir_id'];
		
		$target_dir = _UPLOAD_DIR_."filemanager";
		$full_dir = _FULL_UPLOAD_DIR_."filemanager";
		
		if($row['sys_file_dir_path'] != ''){
			$target_dir .= "/".$row['sys_file_dir_path'];
			$full_dir .= "/".$row['sys_file_dir_path'];
		}
		
	}	
}else{	
	header('Location: library.php');
}

$_plugin_list = '
<script>
 var warning_text1 = \''.$language['name_alerady'].'\';
 var warning_text2 = \''.$language['error'].'\';
 var warning_text3 = \''.$language['create_new_folder'].'\';
 var warning_text4 = \''.$language['success'].'\';
 var warning_text5 = \''.$language['rename_file'].'\';
 var warning_text6 = \''.$language['remove_file'].'\';
</script>
<!-- toastr -->
<link href="plugins/toastr/toastr.min.css" rel="stylesheet" />
<script src="plugins/toastr/toastr.min.js"></script>

<script src="plugins/clipboard.min.js"></script>
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
								<button type="button" class="btn btn-success" id="back_btn" data-id="<?php echo $dir_level?>" title="<?php echo $language['back']?>"><i class="fa fa-chevron-circle-left"></i></button>  
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-success" id="refresh_btn" title="<?php echo $language['refresh']?>"><i class="fa fa-refresh"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <form method="post" action="?" id="edit_imagename">
                    <div class="row">
                    	<div class="col-sm-10  col-xs-8">
	                    	<h4 class="filename"><?php echo $file_name?></h4> 
	                    	<input class="filename" type="text" name="file_title" value="<?php echo $file_name?>">
                    	</div>
                    	<div class="col-sm-2  col-xs-4 dirnameBtn">	 
                            <div class="btn-group pull-right">     
	                        	<button type="button" class="btn btn-success" id="edit_btn" title="<?php echo $language['edit']?>"><i class="fa fa-pencil"></i></button>
                            	<button type="button" class="btn btn-success" id="remove_btn" title="<?php echo $language['delete']?>"><i class="fa fa-trash"></i></button>
                            </div>
                            <div class="btn-group pull-right hideBtn"> 	
	                        	<button type="button" class="btn btn-success" id="save_btn" title="<?php echo $language['submit_btn']?>"><i class="fa fa-floppy-o"></i></button>
                            	<button type="button" class="btn btn-success" id="cancel_btn" title="<?php echo $language['reset_btn']?>"><i class="fa fa-ban"></i></button> 
                            </div>	
	                    	<input type="hidden" name="preview_id" id="preview_id" value="<?php echo $preview_id?>">
	                    	<input type="submit" name="save" value="save">
                    	</div>
                	</div>
                    </form>
                </div>
                <div class="box-body box-body-preview">
                    <div class="row">
						<div class="col-xs-12 preview-image">
							<?php
								$sql = 'SELECT * FROM sys_file_image WHERE sys_file_image_pid = '.$preview_id.' ORDER BY sys_file_image_height DESC';
								$stmt2 = $conn->prepare($sql);
								$stmt2 -> execute();
								
								while($row2 = $stmt2->fetch()){
									
									if($row2['sys_file_image_type'] == 'O'){
										$check_file = $target_dir.'/'.$file_path;
										$image = $file_path;
									}else{
										$ext = '.'.$row2['sys_file_image_ext'];
										$image = $file_path.'_'.$row2['sys_file_image_type'];
										$image = str_replace($ext,'',$image);
										$image .= $ext;
										$check_file = $target_dir.'/'.$image;
									}
									
									if(file_exists($check_file)){
										$data_image[$row2['sys_file_image_type']]['width'] 	= $row2['sys_file_image_width'];
										$data_image[$row2['sys_file_image_type']]['height'] = $row2['sys_file_image_height'];
										$data_image[$row2['sys_file_image_type']]['size'] 	= $row2['sys_file_image_size'];
										$data_image[$row2['sys_file_image_type']]['path'] 	= $full_dir.'/'.$image;
									}
								}
								unset($check_file);
								
								echo '<img id="preview_image" src="';
								echo '../'.$target_dir.'/'.$file_path;
								echo '" alt="';
								echo $file_name;
								echo '" />';
							?>
						</div>
						<div class="col-xs-12 preview-detail">
							<div class="row CopyPath">
							<?php 
								$arr = array(
								    "O" => $language['original'],
								    "X" => 'UHD 16:9',
								    "L" => 'HD 16:9',
								    "M" => 'Wide 16:10',
								    "S" => 'Standard 3:2'
								);
								foreach ($arr as $key => $value) {
									if (array_key_exists($key,$data_image)) {
							?>	
								<div class="col-xs-12 col-sm-3">
									<?php
										echo $value;
										echo ' ';
										echo $data_image[$key]['width'];
										echo 'x';
										echo $data_image[$key]['height'];
										echo ' ';
										echo '(';
										echo formatSizeUnits($data_image[$key]['size']);
										echo ')';
									?>
								</div>
								<div class="col-xs-10 col-sm-8">
									<input type="text" name="size-<?php echo $key?>" id="size-<?php echo $key?>" value="<?php echo $data_image[$key]['path']?>" />
								</div>	
								<div class="col-xs-1 col-sm-1">
                            	<button type="button" class="btn btn-success" title="<?php echo $language['copy']?>" data-clipboard-target="#size-<?php echo $key?>"><i class="fa fa-clipboard"></i></button>
								</div>	
							<?php }
							} ?>	
							</div>	
						</div>		
                    </div>
                </div>
            </div>