<?php
//first row
$sql = "SELECT sys_menu_name_loc , sys_menu_icon , mod_cms_config_depth , mod_cms_config_lang FROM sys_menu LEFT JOIN sys_menu_name ON sys_menu_id = sys_menu_name_pid LEFT JOIN mod_cms_config ON sys_menu_module_key = mod_cms_config_module_key WHERE sys_menu_name_lang = '".$_SESSION['language']."' AND sys_menu_module_key = '".$moduleKey."'";
$stmt_permisson = $conn->prepare($sql);
$stmt_permisson->execute();
$row_permisson = $stmt_permisson->fetch();	
$breadcrumb_name = $row_permisson['sys_menu_name_loc'];
$breadcrumb_icon = $row_permisson['sys_menu_icon'];
$breadcrumb_active = '';
$config_depth = $row_permisson['mod_cms_config_depth'];
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

//find max sizeof
$max_size = file_upload_max_size();
$max_size = ceil($max_size * 0.90);
$max_size = $language['size_error'].formatSizeUnits($max_size);

$_plugin_list .= '
<!-- Slimscroll -->
<script src="template/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="template/plugins/fastclick/fastclick.js"></script>
<!-- Moment.js  -->
<script src="plugins/moment.min.js"></script>
<!-- Select2 -->
<link rel="stylesheet" href="template/plugins/select2/select2.min.css">
<script src="template/plugins/select2/select2.full.min.js"></script>
<!-- Bootstrap Color Picker -->
<link rel="stylesheet" href="plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
<script src="plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<!-- date-range-picker -->
<link rel="stylesheet" href="template/plugins/daterangepicker/daterangepicker.css">
<script src="template/plugins/daterangepicker/daterangepicker.js"></script>
<!-- bootstrap datetimepicker -->
<link rel="stylesheet" href="plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
<script src="plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
<script src="plugins/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.'.$_SESSION['language'].'.js"></script>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css">
<script src="plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="plugins/bootstrap-datepicker/locales/bootstrap-datepicker.'.$_SESSION['language'].'.min.js"></script>
<!-- CK Editor -->
<script src="//cdn.ckeditor.com/4.6.1/standard/ckeditor.js"></script>
<!-- InputMask -->
<script src="plugins/jquery.inputmask.bundle.min.js"></script>
<!-- Colorbox  -->
<link rel="stylesheet" href="plugins/colorbox/colorbox.css">
<script src="plugins/colorbox/jquery.colorbox-min.js"></script>
<!-- timeago -->
<script src="plugins/timeago/jquery.timeago.min.js"></script>  
';

if($_SESSION['language'] == 'th'){
$_plugin_list .= '
<script src="plugins/timeago/jquery.timeago.th.js"></script>';	
}

$_plugin_list .= '
<link rel="stylesheet" href="mod_cms/cms.css" />
<script src="mod_cms/cms.js"></script>
<script>
var warning_text1 = "'.$language['error'].'";
var warning_text2 = "'.$language['limit_error'].'";
var warning_text3 = "'.$language['upload_failed'].'";
var warning_text4 = "'.$max_size.'";
var warning_text5 = "'.$language['url_incorrect'].'";
var and_text = "'.$language['and'].'";
</script>
';

$displayVideo = 0;
?>
    <section class="content-header">
      <h1>
        <?php echo $language['cms_create_content']?>
      </h1>
      <ol class="breadcrumb">
	      <li><a href="home.php"><i class="fa fa-<?php echo $breadcrumb_icon?>"></i> <?php echo $language['home']?></a></li>
	      <?php echo $breadcrumb_active?>
	      <li><?php echo $language['cms_create_content']?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
		<form method="post" action="mod_cms/cms_action.php" enctype="multipart/form-data" id="main_form" >
      <div class="row">
	      <div class="col-md-9">
	         <div class="box box-primary">
	            <div class="box-body">
	                <div class="form-group">
	                  <label><?php echo $language['add_new_post']?></label>
	                  <input type="text" class="form-control" placeholder="<?php echo $language['enter_title_here']?>" name="post_title" spellcheck="true" autocomplete="off">
	                </div>

									<?php
									$sql_pattern = "SELECT * FROM mod_cms_pattern WHERE mod_cms_pattern_modulekey = '".$moduleKey."' AND mod_cms_pattern_parent = 0 ORDER BY mod_cms_pattern_order ASC"; 
									$stmt_pattern = $conn->prepare($sql_pattern);
									$stmt_pattern->execute();
									while($row_pattern = $stmt_pattern->fetch()){

											$pattern_id 	= $row_pattern['mod_cms_pattern_id'];
											$title 			= $row_pattern['mod_cms_pattern_title'];
											$value 			= $row_pattern['mod_cms_pattern_value'];
											$required 		= $row_pattern['mod_cms_pattern_required'];
											$read_only 		= $row_pattern['mod_cms_pattern_read_only'];
											$label 			= $row_pattern['mod_cms_pattern_label'];
											$placeholder 	= $row_pattern['mod_cms_pattern_placeholder'];

											echo '<div class="form-group">';
											echo '<label>';
											echo $title;
											echo '</label>';

											$attribute = ' ';
											if($required == 1){
												$attribute .= 'required="required" ';
											}

											//textbox
											if($row_pattern['mod_cms_pattern_format'] == 1){

												if($read_only == 1){
													$attribute .= 'readonly="readonly" ';
												}
												if($placeholder != ''){
													$attribute .= 'placeholder="'.$placeholder.'" ';
												}	
												if($value != ''){
													$attribute .= 'value="'.$value.'" ';
												}	

												echo '<input type="text" name="pattern_'.$pattern_id.'" class="form-control"'.$attribute.'>';
												
												//Select	
											}else if($row_pattern['mod_cms_pattern_format'] == 2){

												if($read_only == 1){
													$attribute .= 'disabled="disabled" ';
												}

												echo '<select name="pattern_'.$pattern_id.'" class="form-control"'.$attribute.'>';
												echo  "\n";

												$sql_pattern_parent = "SELECT mod_cms_pattern_title , mod_cms_pattern_value FROM mod_cms_pattern WHERE mod_cms_pattern_modulekey = '".$moduleKey."' AND mod_cms_pattern_format = 2 AND mod_cms_pattern_parent = ".$pattern_id." ORDER BY mod_cms_pattern_order ASC"; 
												$stmt_pattern_parent = $conn->prepare($sql_pattern_parent);
												$stmt_pattern_parent->execute();
												while($row_pattern_parent = $stmt_pattern_parent->fetch()){
															echo '<option value="'.$row_pattern_parent['mod_cms_pattern_value'].'">';
															echo $row_pattern_parent['mod_cms_pattern_title'];
															echo '</option>';
															echo  "\n";
												}	
												echo '</select>';

												//Select multiple	
											}else if($row_pattern['mod_cms_pattern_format'] == 3){

													if($read_only == 1){
														$attribute .= 'disabled="disabled" ';
													}

													echo '<select multiple name="pattern_'.$pattern_id.'[]" class="form-control"'.$attribute.'>';
													echo  "\n";

													$sql_pattern_parent = "SELECT mod_cms_pattern_title , mod_cms_pattern_value FROM mod_cms_pattern WHERE mod_cms_pattern_modulekey = '".$moduleKey."' AND mod_cms_pattern_format = 3 AND mod_cms_pattern_parent = ".$pattern_id." ORDER BY mod_cms_pattern_order ASC"; 
													$stmt_pattern_parent = $conn->prepare($sql_pattern_parent);
													$stmt_pattern_parent->execute();
													while($row_pattern_parent = $stmt_pattern_parent->fetch()){
																echo '<option value="'.$row_pattern_parent['mod_cms_pattern_value'].'">';
																echo $row_pattern_parent['mod_cms_pattern_title'];
																echo '</option>';
																echo  "\n";
													}	
													echo '</select>';

												//Radio
											}else if($row_pattern['mod_cms_pattern_format'] == 4){
													$form_name = 'pattern_'.$pattern_id;

													if($required == 1){
														$attribute = ' checked ';
													}	

													$sql_pattern_parent = "SELECT mod_cms_pattern_title , mod_cms_pattern_value , mod_cms_pattern_read_only FROM mod_cms_pattern WHERE mod_cms_pattern_modulekey = '".$moduleKey."' AND mod_cms_pattern_format = 4 AND mod_cms_pattern_parent = ".$pattern_id." ORDER BY mod_cms_pattern_order ASC"; 
													$stmt_pattern_parent = $conn->prepare($sql_pattern_parent);
													$stmt_pattern_parent->execute();
													while($row_pattern_parent = $stmt_pattern_parent->fetch()){
																if($row_pattern_parent['mod_cms_pattern_read_only'] == 1){
																	$attribute .= 'disabled="disabled" ';
																}
																																																		
																echo '<div class="radio">';
																echo '<label>';
																echo '<input type="radio" name="'.$form_name.'" value="'.$row_pattern_parent['mod_cms_pattern_value'].'"'.$attribute.'>';
																echo $row_pattern_parent['mod_cms_pattern_title'];
																echo '</label>';
																echo '</div>';
																echo  "\n";

																$attribute = ' ';
													}	

												//checkbox
											}else if($row_pattern['mod_cms_pattern_format'] == 5){
													$form_name = 'pattern_'.$pattern_id;

													$sql_pattern_parent = "SELECT mod_cms_pattern_title , mod_cms_pattern_value , mod_cms_pattern_read_only FROM mod_cms_pattern WHERE mod_cms_pattern_modulekey = '".$moduleKey."' AND mod_cms_pattern_format = 5 AND mod_cms_pattern_parent = ".$pattern_id." ORDER BY mod_cms_pattern_order ASC"; 
													$stmt_pattern_parent = $conn->prepare($sql_pattern_parent);
													$stmt_pattern_parent->execute();
													while($row_pattern_parent = $stmt_pattern_parent->fetch()){
																/*
																if($row_pattern_parent['mod_cms_pattern_read_only'] == 1){
																	$attribute .= 'disabled="disabled" ';
																}*/
																																																	
																echo '<div class="checkbox">';
																echo '<label>';
																echo '<input type="checkbox" name="'.$form_name.'" value="'.$row_pattern_parent['mod_cms_pattern_value'].'"'.$attribute.'>';
																echo $row_pattern_parent['mod_cms_pattern_title'];
																echo '</label>';
																echo '</div>';
																echo  "\n";

																$attribute = ' ';
													}							
											//texteditor
											}else if(
												($row_pattern['mod_cms_pattern_format'] == 6)
														OR
												($row_pattern['mod_cms_pattern_format'] == 7)
											){

												$class = "";
												if($row_pattern['mod_cms_pattern_format'] == 6){
													$class = "ckeditor";
												}

												if($read_only == 1){
													$attribute .= 'disabled="disabled" ';
												}
												if($placeholder != ''){
													$attribute .= 'placeholder="'.$placeholder.'" ';
												}	
												if($value != ''){
													$attribute .= 'value="'.$value.'" ';
												}	

												echo '<textarea class="textarea_form form-control '.$class.'" rows="3" name="pattern_'.$pattern_id.'"'.$attribute.'></textarea>';
												
												//Currency
											}else if($row_pattern['mod_cms_pattern_format'] == 8){

												if($read_only == 1){
													$attribute .= 'readonly="readonly" ';
												}
												if($value != ''){
													$attribute .= 'value="'.$value.'" ';
												}	
												
												echo '<div class="input-group">';
												echo '<span class="input-group-addon">'.$placeholder.'</span>';
												echo '<input type="tel" name="pattern_'.$pattern_id.'" class="currency form-control"'.$attribute.'>';
												echo '</div>';
			
												//Calendar
											}else if($row_pattern['mod_cms_pattern_format'] == 9){

												if($read_only == 1){
													$attribute .= 'disabled="disabled" ';
												}
												if($value != ''){
													$attribute .= 'value="'.$value.'" ';
												}	

												echo '<div class="input-group date">';
												echo '<div class="input-group-addon">';
												echo '<i class="fa fa-calendar"></i>';
												echo '</div>';
												echo '<input type="date" name="pattern_'.$pattern_id.'" class="form-control pull-right datepicker"'.$attribute.'>';
												echo '</div>';	

												//Date Range
											}else if($row_pattern['mod_cms_pattern_format'] == 10){

												if($read_only == 1){
													$attribute .= 'disabled="disabled" ';
												}
												if($value != ''){
													$attribute .= 'value="'.$value.'" ';
												}	

												echo '<div class="input-group">';
												echo '<div class="input-group-addon">';
												echo '<i class="fa fa-calendar"></i>';
												echo '</div>';
												echo '<input type="text" name="pattern_'.$pattern_id.'" class="form-control pull-right daterange"'.$attribute.'>';
												echo '</div>';	

												//Date time
											}else if($row_pattern['mod_cms_pattern_format'] == 11){

												if($read_only == 1){
													$attribute .= 'disabled="disabled" ';
												}
												if($value != ''){
													$attribute .= 'value="'.$value.'" ';
												}	

												echo '<div class="input-group date">';
												echo '<div class="input-group-addon">';
												echo '<i class="fa fa-calendar"></i>';
												echo '</div>';
												echo '<input type="date" name="pattern_'.$pattern_id.'" class="form-control pull-right datetime"'.$attribute.'>';
												echo '</div>';	

												//Date time Range
											}else if($row_pattern['mod_cms_pattern_format'] == 12){

												if($read_only == 1){
													$attribute .= 'disabled="disabled" ';
												}
												if($value != ''){
													$attribute .= 'value="'.$value.'" ';
												}	

												echo '<div class="input-group">';
												echo '<div class="input-group-addon">';
												echo '<i class="fa fa-calendar"></i>';
												echo '</div>';
												echo '<input type="text" name="pattern_'.$pattern_id.'" class="form-control pull-right datetimerange"'.$attribute.'>';
												echo '</div>';	

												//Email
											}else if($row_pattern['mod_cms_pattern_format'] == 13){

												if($read_only == 1){
													$attribute .= 'readonly="readonly" ';
												}
												if($value != ''){
													$attribute .= 'value="'.$value.'" ';
												}	

												echo '<div class="input-group">';
												echo '<div class="input-group-addon">';
												echo '<i class="fa fa-envelope"></i>';
												echo '</div>';
												echo '<input type="email" name="pattern_'.$pattern_id.'" class="form-control pull-right"'.$attribute.'>';
												echo '</div>';	

												//number
											}else if($row_pattern['mod_cms_pattern_format'] == 14){

												if($read_only == 1){
													$attribute .= 'readonly="readonly" ';
												}
												if($value != ''){
													$attribute .= 'value="'.$value.'" ';
												}	

												echo '<input type="tel" name="pattern_'.$pattern_id.'" class="form-control decimal"'.$attribute.'>';

												//IP Address
											}else if($row_pattern['mod_cms_pattern_format'] == 15){

												if($read_only == 1){
													$attribute .= 'readonly="readonly" ';
												}
												if($value != ''){
													$attribute .= 'value="'.$value.'" ';
												}	

												echo '<div class="input-group">';
												echo '<div class="input-group-addon">';
												echo '<i class="fa fa-laptop"></i>';
												echo '</div>';
												echo '<input type="text" name="pattern_'.$pattern_id.'" class="form-control ip_mask"'.$attribute.'>';
												echo '</div>';	

												//Link
											}else if($row_pattern['mod_cms_pattern_format'] == 16){

												if($read_only == 1){
													$attribute .= 'readonly="readonly" ';
												}
												if($value != ''){
													$attribute .= 'value="'.$value.'" ';
												}	

												echo '<div class="input-group">';
												echo '<div class="input-group-addon">';
												echo '<i class="fa fa-link"></i>';
												echo '</div>';
												echo '<input type="text" name="pattern_'.$pattern_id.'" class="form-control external_link"'.$attribute.'>';
												echo '</div>';	

												//color
											}else if($row_pattern['mod_cms_pattern_format'] == 17){

												if($read_only == 1){
													$attribute .= 'readonly="readonly" ';
												}
												if($value != ''){
													$attribute .= 'value="'.$value.'" ';
												}	

												echo '<div class="input-group colorpicker-component color_picker">';
												echo '<input type="text" name="pattern_'.$pattern_id.'" class="form-control color_picker"'.$attribute.'>';
												echo '<div class="input-group-addon">';
												echo '<i></i>';
												echo '</div>';
												echo '</div>';	
												
												//continent	
											}else if($row_pattern['mod_cms_pattern_format'] == 18){

												if($read_only == 1){
													$attribute .= 'disabled="disabled" ';
												}

												echo '<select name="pattern_'.$pattern_id.'" class="form-control"'.$attribute.'>';
												echo  "\n";

												if($_SESSION['language'] == 'th'){
												$sql_pattern_parent = "SELECT CONTINENT_CODE , CONTINENT_DESC_LOC AS CONTINENT_DESC FROM mas_continent ORDER BY CONTINENT_DESC_LOC ASC "; 
												}else{
												$sql_pattern_parent = "SELECT CONTINENT_CODE , CONTINENT_DESC_ENG AS CONTINENT_DESC FROM mas_continent ORDER BY CONTINENT_DESC_ENG ASC "; 
												}
												$stmt_pattern_parent = $conn->prepare($sql_pattern_parent);
												$stmt_pattern_parent->execute();
												while($row_pattern_parent = $stmt_pattern_parent->fetch()){
													if($value == $row_pattern_parent['CONTINENT_CODE']){
														$selected = '" selected="selected';
													}else{
														$selected = '';
													}	

															echo '<option value="'.$row_pattern_parent['CONTINENT_CODE'].$selected.'">';
															echo $row_pattern_parent['CONTINENT_DESC'];
															echo '</option>';
															echo  "\n";
												}	
												echo '</select>';
												
												//countries	
											}else if($row_pattern['mod_cms_pattern_format'] == 19){

												if($read_only == 1){
													$attribute .= 'disabled="disabled" ';
												}

												$optgroup = '';
												echo '<select name="pattern_'.$pattern_id.'" class="form-control"'.$attribute.'>';
												echo  "\n";

												if($_SESSION['language'] == 'th'){
												$sql_pattern_parent = "SELECT COUNTRY_CODE , COUNTRY_DESC_LOC AS COUNTRY_DESC , CONTINENT_CODE , CONTINENT_DESC_LOC AS CONTINENT_DESC"; 
												}else{
												$sql_pattern_parent = "SELECT COUNTRY_CODE , COUNTRY_DESC_ENG AS COUNTRY_DESC , CONTINENT_CODE , CONTINENT_DESC_ENG AS CONTINENT_DESC"; 
												}

												$sql_pattern_parent .= ' FROM mas_countries LEFT JOIN mas_continent ON CONTINENT = CONTINENT_CODE ORDER BY CONTINENT ASC , COUNTRY_DESC_LOC ASC';
												$stmt_pattern_parent = $conn->prepare($sql_pattern_parent);
												$stmt_pattern_parent->execute();
												while($row_pattern_parent = $stmt_pattern_parent->fetch()){
													if($value == $row_pattern_parent['COUNTRY_CODE']){
														$selected = '" selected="selected';
													}else{
														$selected = '';
													}	
													if($optgroup != $row_pattern_parent['CONTINENT_CODE']){
															if($optgroup != '') echo "</optgroup>\n";
															echo '<optgroup label="'.$row_pattern_parent['CONTINENT_DESC'].'">';
															echo '<option value="'.$row_pattern_parent['COUNTRY_CODE'].$selected.'">';
															echo $row_pattern_parent['COUNTRY_DESC'];
															echo '</option>';
															$optgroup = $row_pattern_parent['CONTINENT_CODE'];
													}else{
															echo '<option value="'.$row_pattern_parent['COUNTRY_CODE'].$selected.'">';
															echo $row_pattern_parent['COUNTRY_DESC'];
															echo '</option>';
													}		
															echo  "\n";
												}
												echo "</optgroup>\n";	
												echo '</select>';
												
												//region	
											}else if($row_pattern['mod_cms_pattern_format'] == 20){

												if($read_only == 1){
													$attribute .= 'disabled="disabled" ';
												}

												echo '<select name="pattern_'.$pattern_id.'" class="form-control"'.$attribute.'>';
												echo  "\n";

												if($_SESSION['language'] == 'th'){
												$sql_pattern_parent = "SELECT REGION_ID , REGION_DESC_LOC AS REGION_DESC FROM mas_region"; 
												}else{
												$sql_pattern_parent = "SELECT REGION_ID , REGION_DESC_ENG AS REGION_DESC FROM mas_region"; 
												}
												$stmt_pattern_parent = $conn->prepare($sql_pattern_parent);
												$stmt_pattern_parent->execute();
												while($row_pattern_parent = $stmt_pattern_parent->fetch()){
													if($value == $row_pattern_parent['REGION_ID']){
														$selected = '" selected="selected';
													}else{
														$selected = '';
													}	
															echo '<option value="'.$row_pattern_parent['REGION_ID'].$selected.'">';
															echo $row_pattern_parent['REGION_DESC'];
															echo '</option>';
															echo  "\n";
												}	
												echo '</select>';
												
												//province	
											}else if($row_pattern['mod_cms_pattern_format'] == 21){

												if($read_only == 1){
													$attribute .= 'disabled="disabled" ';
												}

												$optgroup = '';
												echo '<select name="pattern_'.$pattern_id.'" class="form-control"'.$attribute.'>';
												echo  "\n";

												if($_SESSION['language'] == 'th'){
												$sql_pattern_parent = "SELECT PROVINCE_ID , PROVINCE_DESC_LOC AS PROVINCE_DESC , mas_region.REGION_ID , REGION_DESC_LOC AS REGION_DESC"; 
												}else{
												$sql_pattern_parent = "SELECT PROVINCE_ID , PROVINCE_DESC_ENG AS PROVINCE_DESC , mas_region.REGION_ID , REGION_DESC_ENG AS REGION_DESC"; 
												}

												$sql_pattern_parent .= ' FROM mas_province LEFT JOIN mas_region ON mas_province.REGION_ID = mas_region.REGION_ID ORDER BY mas_region.REGION_ID ASC , PROVINCE_DESC';
												$stmt_pattern_parent = $conn->prepare($sql_pattern_parent);
												$stmt_pattern_parent->execute();
												while($row_pattern_parent = $stmt_pattern_parent->fetch()){
													if($value == $row_pattern_parent['PROVINCE_ID']){
														$selected = '" selected="selected';
													}else{
														$selected = '';
													}	
													if($optgroup != $row_pattern_parent['REGION_ID']){
															if($optgroup != '') echo "</optgroup>\n";
															echo '<optgroup label="'.$row_pattern_parent['REGION_DESC'].$selected.'">';
															echo '<option value="'.$row_pattern_parent['PROVINCE_ID'].'">';
															echo $row_pattern_parent['PROVINCE_DESC'];
															echo '</option>';
															$optgroup = $row_pattern_parent['REGION_ID'];
													}else{
															echo '<option value="'.$row_pattern_parent['PROVINCE_ID'].$selected.'">';
															echo $row_pattern_parent['PROVINCE_DESC'];
															echo '</option>';
													}		
															echo  "\n";
												}
												echo "</optgroup>\n";	
												echo '</select>';
												
												//District	
											}else if($row_pattern['mod_cms_pattern_format'] == 22){

												if($read_only == 1){
													$attribute .= 'disabled="disabled" ';
												}
												
												if($value > 0){
													$sql_province_id = "SELECT PROVINCE_ID  FROM mas_district WHERE DISTRICT_ID = ".$value;
													$stmt_pattern_parent = $conn->prepare($sql_province_id);
													$stmt_pattern_parent->execute();
													$row_pattern_parent = $stmt_pattern_parent->fetch();
													$province_id = $row_pattern_parent['PROVINCE_ID'];													
												}else{
													$province_id = 0;
												}

												$optgroup = '';
												echo '<select name="pattern_province_'.$pattern_id.'" class="select_province form-control"'.$attribute.'>';
												echo  "\n";

												if($_SESSION['language'] == 'th'){
												$sql_pattern_parent = "SELECT PROVINCE_ID , PROVINCE_DESC_LOC AS PROVINCE_DESC , mas_region.REGION_ID , REGION_DESC_LOC AS REGION_DESC"; 
												}else{
												$sql_pattern_parent = "SELECT PROVINCE_ID , PROVINCE_DESC_ENG AS PROVINCE_DESC , mas_region.REGION_ID , REGION_DESC_ENG AS REGION_DESC"; 
												}

												$sql_pattern_parent .= ' FROM mas_province LEFT JOIN mas_region ON mas_province.REGION_ID = mas_region.REGION_ID ORDER BY mas_region.REGION_ID ASC , PROVINCE_DESC';
												$stmt_pattern_parent = $conn->prepare($sql_pattern_parent);
												$stmt_pattern_parent->execute();
												while($row_pattern_parent = $stmt_pattern_parent->fetch()){

													if($province_id == 0){
														$province_id = $row_pattern_parent['PROVINCE_ID'];
													}
													if($province_id == $row_pattern_parent['PROVINCE_ID']){
														$selected = '" selected="selected';
													}else{
														$selected = '';
													}

													if($optgroup != $row_pattern_parent['REGION_ID']){
															if($optgroup != '') echo "</optgroup>\n";
															echo '<optgroup label="'.$row_pattern_parent['REGION_DESC'].'">';
															echo '<option value="'.$row_pattern_parent['PROVINCE_ID'].$selected.'">';
															echo $row_pattern_parent['PROVINCE_DESC'];
															echo '</option>';
															$optgroup = $row_pattern_parent['REGION_ID'];
													}else{
															echo '<option value="'.$row_pattern_parent['PROVINCE_ID'].$selected.'">';
															echo $row_pattern_parent['PROVINCE_DESC'];
															echo '</option>';
													}		
															echo  "\n";
												}
												echo "</optgroup>\n";	
												echo '</select>';
												echo '<span class="help-block"></span>';
												echo '<select name="pattern_district_'.$pattern_id.'" class="form-control"'.$attribute.'>';
												echo  "\n";

												if($_SESSION['language'] == 'th'){
												$sql_pattern_parent = "SELECT DISTRICT_ID , DISTRICT_DESC_LOC AS DISTRICT_DESC FROM mas_district WHERE PROVINCE_ID = ".$province_id; 
												}else{
												$sql_pattern_parent = "SELECT DISTRICT_ID , DISTRICT_DESC_ENG AS DISTRICT_DESC FROM mas_district WHERE PROVINCE_ID = ".$province_id; 
												}
												$stmt_pattern_parent = $conn->prepare($sql_pattern_parent);
												$stmt_pattern_parent->execute();
												while($row_pattern_parent = $stmt_pattern_parent->fetch()){
													if($value == $row_pattern_parent['DISTRICT_ID']){
														$selected = '" selected="selected';
													}else{
														$selected = '';
													}	
															echo '<option value="'.$row_pattern_parent['DISTRICT_ID'].$selected.'">';
															echo $row_pattern_parent['DISTRICT_DESC'];
															echo '</option>';
															echo  "\n";
												}	
												echo '</select>';
												
												//Sub District	
											}else if($row_pattern['mod_cms_pattern_format'] == 23){

												if($read_only == 1){
													$attribute .= 'disabled="disabled" ';
												}
												
												if($value > 0){
													$sql_province_id = "SELECT mas_district.DISTRICT_ID , mas_district.PROVINCE_ID  FROM mas_sub_district LEFT JOIN mas_district ON mas_sub_district.DISTRICT_ID = mas_district.DISTRICT_ID  WHERE SUB_DISTRICT_ID = ".$value;
													$stmt_pattern_parent = $conn->prepare($sql_province_id);
													$stmt_pattern_parent->execute();
													$row_pattern_parent = $stmt_pattern_parent->fetch();
													$province_id = $row_pattern_parent['PROVINCE_ID'];	
													$district_id = $row_pattern_parent['DISTRICT_ID'];												
												}else{
													$province_id = 0;
													$district_id = 0;
												}

												$optgroup = '';
												echo '<select name="pattern_province_'.$pattern_id.'" class="select_province form-control"'.$attribute.'>';
												echo  "\n";

												if($_SESSION['language'] == 'th'){
												$sql_pattern_parent = "SELECT PROVINCE_ID , PROVINCE_DESC_LOC AS PROVINCE_DESC , mas_region.REGION_ID , REGION_DESC_LOC AS REGION_DESC"; 
												}else{
												$sql_pattern_parent = "SELECT PROVINCE_ID , PROVINCE_DESC_ENG AS PROVINCE_DESC , mas_region.REGION_ID , REGION_DESC_ENG AS REGION_DESC"; 
												}

												$sql_pattern_parent .= ' FROM mas_province LEFT JOIN mas_region ON mas_province.REGION_ID = mas_region.REGION_ID ORDER BY mas_region.REGION_ID ASC , PROVINCE_DESC';
												$stmt_pattern_parent = $conn->prepare($sql_pattern_parent);
												$stmt_pattern_parent->execute();
												while($row_pattern_parent = $stmt_pattern_parent->fetch()){

													if($province_id == 0){
														$province_id = $row_pattern_parent['PROVINCE_ID'];
													}
													if($province_id == $row_pattern_parent['PROVINCE_ID']){
														$selected = '" selected="selected';
													}else{
														$selected = '';
													}
													
													if($optgroup != $row_pattern_parent['REGION_ID']){
															if($optgroup != '') echo "</optgroup>\n";
															echo '<optgroup label="'.$row_pattern_parent['REGION_DESC'].'">';
															echo '<option value="'.$row_pattern_parent['PROVINCE_ID'].$selected.'">';
															echo $row_pattern_parent['PROVINCE_DESC'];
															echo '</option>';
															$optgroup = $row_pattern_parent['REGION_ID'];
													}else{
															echo '<option value="'.$row_pattern_parent['PROVINCE_ID'].$selected.'">';
															echo $row_pattern_parent['PROVINCE_DESC'];
															echo '</option>';
													}		
															echo  "\n";
												}
												echo "</optgroup>\n";	
												echo '</select>';
												echo '<span class="help-block"></span>';
												echo '<select name="pattern_district_'.$pattern_id.'" class="select_district form-control"'.$attribute.'>';
												echo  "\n";

												if($_SESSION['language'] == 'th'){
												$sql_pattern_parent = "SELECT DISTRICT_ID , DISTRICT_DESC_LOC AS DISTRICT_DESC FROM mas_district WHERE PROVINCE_ID = ".$province_id; 
												}else{
												$sql_pattern_parent = "SELECT DISTRICT_ID , DISTRICT_DESC_ENG AS DISTRICT_DESC FROM mas_district WHERE PROVINCE_ID = ".$province_id; 
												}
												$stmt_pattern_parent = $conn->prepare($sql_pattern_parent);
												$stmt_pattern_parent->execute();
												while($row_pattern_parent = $stmt_pattern_parent->fetch()){
													if($district_id == $row_pattern_parent['DISTRICT_ID']){
														$selected = '" selected="selected';
													}else{
														$selected = '';
													}	
															echo '<option value="'.$row_pattern_parent['DISTRICT_ID'].$selected.'">';
															echo $row_pattern_parent['DISTRICT_DESC'];
															echo '</option>';
															echo  "\n";
												}	
												echo '</select>';
												echo '<span class="help-block"></span>';
												echo '<select name="pattern_sub_district_'.$pattern_id.'" class="form-control"'.$attribute.'>';
												echo  "\n";

												if($_SESSION['language'] == 'th'){
												$sql_pattern_parent = "SELECT SUB_DISTRICT_ID , SUB_DISTRICT_DESC_LOC AS SUB_DISTRICT_DESC FROM mas_sub_district WHERE DISTRICT_ID = ".$district_id; 
												}else{
												$sql_pattern_parent = "SELECT SUB_DISTRICT_ID , SUB_DISTRICT_DESC_ENG AS SUB_DISTRICT_DESC FROM mas_sub_district WHERE DISTRICT_ID = ".$district_id; 
												}
												$stmt_pattern_parent = $conn->prepare($sql_pattern_parent);
												$stmt_pattern_parent->execute();
												while($row_pattern_parent = $stmt_pattern_parent->fetch()){
													if($value == $row_pattern_parent['SUB_DISTRICT_ID']){
														$selected = '" selected="selected';
													}else{
														$selected = '';
													}	
															echo '<option value="'.$row_pattern_parent['SUB_DISTRICT_ID'].$selected.'">';
															echo $row_pattern_parent['SUB_DISTRICT_DESC'];
															echo '</option>';
															echo  "\n";
												}	
												echo '</select>';
											
											//File Input	
											}else if($row_pattern['mod_cms_pattern_format'] == 24){
												/* label */
												$expension  = str_replace('.','',$placeholder);
												$expensions = explode(',',$expension);
												unset($expension);

												$label_upload = '<ul class="upload_condition">';
												if($label != ''){
													$label_upload .=  '<li>';
													$label_upload .=  $label;
													$label_upload .=  '</li>';
												}
													$label_upload .=  '<li>';
													$label_upload .=  $language['ext_support'] . natural_language_join($expensions,$language['or']);
													$label_upload .=  '</li>';
													$label_upload .=  '<li>';
													$label_upload .=  $max_size;
													$label_upload .=  '</li>';

												if($value > 1){

													$label_upload .=  '<li>';
													$label_upload .=  str_replace('|:NUM:|',$value,$language['limit_error']);
													$label_upload .=  '</li>';

													$value = $value.'" multiple="multiple';
												}

												echo '<ul class="displayUpload clearfix" id="display_'.$pattern_id.'"></ul>';
												echo '<input type="file" class="inputUpload" id="pattern_'.$pattern_id.'" name="pattern_'.$pattern_id.'[]" data-limit="'.$value.'" accept="'.$placeholder.'">';
												echo '<img src="images/bar-loading.gif" alt="loading" class="upload_Progress" id="Progress_'.$pattern_id.'" >';


												
												$label_upload .= "</ul>";

												$label = $label_upload;

											
											//File Gallery	
											}else if($row_pattern['mod_cms_pattern_format'] == 25){

												$label_upload = '<ul class="upload_condition">';
												if($label != ''){
													$label_upload .=  '<li>';
													$label_upload .=  $label;
													$label_upload .=  '</li>';
												}
													$label_upload .=  '<li>';
													$label_upload .=  $language['image_support'];
													$label_upload .=  '</li>';
													$label_upload .=  '<li>';
													$label_upload .=  $max_size;
													$label_upload .=  '</li>';

												if($value > 1){

													$label_upload .=  '<li>';
													$label_upload .=  str_replace('|:NUM:|',$value,$language['limit_error']);
													$label_upload .=  '</li>';

													$value = $value.'" multiple="multiple';
												}
																								
												echo '<ul class="displayUpload displayGallery clearfix" id="display_'.$pattern_id.'"></ul>';
												echo '<input type="file" class="inputGallery" id="pattern_'.$pattern_id.'" name="pattern_'.$pattern_id.'[]" data-limit="'.$value.'" accept="image/*">';
												echo '<img src="images/bar-loading.gif" alt="loading" class="upload_Progress" id="Progress_'.$pattern_id.'" >';
												$label_upload .= "</ul>";

												$label = $label_upload;		

											
											//File Video	
											}else if($row_pattern['mod_cms_pattern_format'] == 26){
												$label_upload = '<ul class="upload_condition">';
												if($label != ''){
													$label_upload .=  '<li>';
													$label_upload .=  $label;
													$label_upload .=  '</li>';
												}
													$label_upload .=  '<li>';
													$label_upload .=  $language['video_support'];
													$label_upload .=  '</li>';
													$label_upload .=  '<li>';
													$label_upload .=  $max_size;
													$label_upload .=  '</li>';

												if($value > 1){

													$label_upload .=  '<li>';
													$label_upload .=  str_replace('|:NUM:|',$value,$language['limit_error']);
													$label_upload .=  '</li>';

													$value = $value.'" multiple="multiple';
												}
												$label_upload .= "</ul>";

												echo '<ul class="displayUpload displayVideo clearfix" id="display_'.$pattern_id.'"></ul>';
												echo '<div class="nav-tabs-custom">';
												echo '<ul class="nav nav-tabs">';
												echo '<li class="active"><a href="#tab_'.$pattern_id.'_1" data-toggle="tab">'.$language['video_upload'].'</a></li>';
												echo '<li><a href="#tab_'.$pattern_id.'_2" data-toggle="tab">'.$language['video_embed'].'</a></li>';
												echo '</ul>';
												echo '<div class="tab-content">';
												echo '<div class="tab-pane active" id="tab_'.$pattern_id.'_1">';
												echo '<input type="file" class="inputVideo" id="pattern_'.$pattern_id.'" name="pattern_'.$pattern_id.'[]" data-limit="'.$value.'" accept="video/*">';
												echo '<img src="images/bar-loading.gif" alt="loading" class="upload_Progress" id="Progress_'.$pattern_id.'" >';
												echo $label_upload;
												echo '</div>';
												echo '<div class="tab-pane clearfix" id="tab_'.$pattern_id.'_2">';
												echo '<div class="row">';
												echo '<div class="col-lg-12"><div class="input-group">';
												echo '<input type="text" name="pattern_'.$pattern_id.'_input_video" class="form-control"'.$attribute.'>';
												echo '<span class="input-group-addon addVideo_btn" onclick="addVideo('.$pattern_id.');"><i class="fa fa-plus-square"></i></span>';
												echo '</div></div>';
												echo '</div>';
												echo '<div class="row">';
												echo '<div class="col-lg-3 radio video_choose"><label><input name="videoType_'.$pattern_id.'" value="youtube" type="radio" checked="checked"> Youtube </label></div>';
												echo '<div class="col-lg-3 radio video_choose"><label><input name="videoType_'.$pattern_id.'" value="facebook" type="radio"> Facebook </label></div>';
												echo '<div class="col-lg-3 radio video_choose"><label><input name="videoType_'.$pattern_id.'" value="viemo" type="radio"> Viemo </label></div>';
												echo '<div class="col-lg-3 radio video_choose"><label><input name="videoType_'.$pattern_id.'" value="link" type="radio"> Link </label></div>';
												echo '</div>';
												echo '</div>';
												echo '</div>';
												echo '</div>';

                  
												$label = '';
												$displayVideo++;	
											}

												if($label != ''){
													echo '<span class="help-block">';
													echo $label;
													echo '</span>';
												}

												echo '</div>';
												echo  "\n";
									}	
									?>
	
	
	            </div>		         
	         </div>
	      </div>   
	      <div class="col-md-3">
	         <div class="box box-success">
		        <div class="box-body">
		              <div class="form-group language_switch">
		                <label><?=$language['language']?></label>
		                <select name="language_post" class="form-control select2" style="width: 100%;">
			              <?php 
				            foreach ($config_lang as $key => $value) { 
				              $sql_lang = "SELECT * FROM mas_languages WHERE mas_languages_iso = '".$value."'";
				              $stmt_lang = $conn->prepare($sql_lang);
											$stmt_lang->execute();
											$row_lang = $stmt_lang->fetch();
											
											echo '<option';
											echo ' value="';
											echo $row_lang['mas_languages_iso'];
											echo '"';
											echo ' lang="';
											echo GetFlagByName($row_lang['mas_languages_flag']);
											echo '" ';
											echo '>';
											echo $row_lang['mas_languages_native'];
											echo '</option>';		          
											echo "\n";
										}
			              ?> 	
		                </select>
		              </div>
		        </div>	
	         </div>
					 <div class="box">
            	<div class="box-header with-border">
								<h3 class="box-title"><?php echo $language['publish']?></h3>

								<div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
									</button>
								</div>
							</div>
					 		 <div class="box-body">
									<div class="form-horizontal">
										<?php /* php ให้ใส่ thumbnail ใน class ด้วย */ ?>
										<img class="cms_thumbnail" src="images/cms_thumbnail.png" alt="">
										<div class="form-group">
											<label class="col-sm-3 control-label"><?php echo $language['status']?></label>
											<div class="col-sm-9">
															<select class="form-control" name="staus_post">
																	<option value="public"><?php echo $language['public']?></option>
																	<option value="pending"><?php echo $language['pending_review']?></option>
																	<option value="private"><?php echo $language['private']?></option>
															</select>
											</div>
										</div>	
										<div class="form-group">
											<label class="col-sm-3 control-label"><?php echo $language['publish']?></label>
											<div class="col-sm-9">
															<select class="form-control" name="scheduled">
																	<option value="immediately"><?php echo $language['immediately']?></option>
																	<option value="scheduled"><?php echo $language['scheduled']?></option>
															</select>
											</div>
										</div>
										<div class="form-group scheduled_tab">
												<label class="col-sm-3 control-label"><?php echo $language['start_date']?></label>
												<div class="col-sm-9 input-group datetime_box">
													<input type="text" name="start_date" class="form-control datetime" placeholder="<?php echo $language['start_date']?>" value=""></input>
													<div class="input-group-addon add-on">
														<i class="fa fa-calendar" ></i>
													</div>
												</div>
										</div>
										<div class="form-group scheduled_tab">
												<label class="col-sm-3 control-label"><?php echo $language['end_date']?></label>
												<div class="col-sm-9 input-group datetime_box">
													<input type="text" name="end_date" class="form-control datetime" placeholder="<?php echo $language['end_date']?>" value=""></input>
													<div class="input-group-addon add-on">
														<i class="fa fa-calendar"></i>
													</div>
												</div>
										</div>	
									</div>										
								</div>
								<div class="box-footer">
                	<button type="reset" class="btn btn-default" id="btn_content_back"><?php echo $language['cancel_btn']?></button>
                	<button type="submit" name="submit_btn" class="btn btn-info pull-right"><?php echo $language['submit_btn']?></button>
                  <input type="hidden" name="level" value="<?php echo $level?>" />
                	<input type="hidden" name="parent" value="<?php echo $parent?>" />
                  <input type="hidden" name="moduleKey" value="<?php echo $moduleKey?>" />
                  <input type="hidden" name="menu" value="<?php echo $menu_id?>" />
                  <input type="hidden" name="action" value="create_content" />
								</div>
					 </div>
	      </div>   
	       
      </div>
			</form>
			
<form action="#" id="action_frame" class="dNone" name="action_frame" method="post" enctype="multipart/form-data" target="action_iframe">
<iframe id="action_iframe" name="action_iframe" src="#"></iframe>
</form>			
</section>
<?php if($displayVideo > 0){
$_plugin_list .= '
<script src="https://apis.google.com/js/client.js?onload=googleApiClientReady"></script>
<script src="mod_cms/cms_api.php"></script>';

echo '<div class="dNone"><div id="inline_video"></div></div>';
} ?>     
  
