<?php
require ("../inc/config.inc.php");
require ("../inc/connectdb.inc.php");
require ("../inc/function.inc.php");

if(isset($_POST['pid'])){
    $language = language_data();

        $id = intval($_POST['pid']);
        $sql = "SELECT mod_cms_pattern_format , mod_cms_pattern_placeholder  FROM mod_cms_pattern WHERE mod_cms_pattern_id = ".$id; 
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $target_dir1 = '../'._UPLOAD_DIR_;
        $target_dir2 = $target_dir1."temp";
        checkDir($target_dir1);
        checkDir($target_dir2); 

        $max_size = file_upload_max_size();
        $max_size = ceil($max_size * 0.90);

        $Success = array();
        $errors = array();

        if($row['mod_cms_pattern_format'] == 24){ //config ext
            $expension  = str_replace('.','',$row['mod_cms_pattern_placeholder']);
            $expensions = explode(',',$expension);
            unset($expension);
        }else if($row['mod_cms_pattern_format'] == 25){ //image only
            $expensions[] = 'jpg';
            $expensions[] = 'jpeg';
            $expensions[] = 'png';
            $expensions[] = 'gif';
            $expensions[] = 'tiff';
            $expensions[] = 'bmp';
        }

        $error_text[0] = $language['ext_error'] . natural_language_join($expensions,$language['or']); 
        $error_text[1] = $language['size_error'].formatSizeUnits($max_size); 

        $pattent_name = "pattern_".$id;
        foreach ($_FILES[$pattent_name]['name'] as $key => $file_name){

            $file_size =$_FILES[$pattent_name]['size'][$key];

            $file_tmp =$_FILES[$pattent_name]['tmp_name'][$key];

            $file_type = $_FILES[$pattent_name]['type'][$key];

            $file_ext = pathinfo($file_name,PATHINFO_EXTENSION);
            $file_ext = trim($file_ext);
            $file_ext = strtolower($file_ext);
            
            if(in_array($file_ext,$expensions)=== false){
                $errors[]= $error_text[0];
            }
            
            if($file_size > $max_size){
                $errors[]= $error_text[1];
            }
            
            if(empty($errors)==true){
                $new_name = time().'_'.rand(1111,9999).'.'.$file_ext;
                move_uploaded_file($file_tmp,$target_dir2.'/'.$new_name);
                $Success[$key]['file'] = $new_name;
                $Success[$key]['name'] = $file_name;
                $Success[$key]['ext'] = $file_ext;
                $Success[$key]['type'] = $file_type;
                $Success[$key]['size'] = formatSizeUnits($file_size);

                chmod($target_dir2.'/'.$new_name , 0755);
            }

        }      

    $data = array();
    $data['success'] = $Success;
    $data['errors'] = $errors;
    $data['path'] = _UPLOAD_DIR_."temp/";

    if($row['mod_cms_pattern_format'] == 24){ //config ext
        echo '<script>
        var obj = ' . json_encode($data) . ';
            parent.returnTempFile(obj);
        </script>';
    }else if($row['mod_cms_pattern_format'] == 25){ //image only
        echo '<script>
        var obj = ' . json_encode($data) . ';
            parent.returnGalleryFile(obj);
        </script>';
    }
    unset($row);

    exit();
}else if(isset($_POST['type'])){
    $type = $_POST['type'];
    $id = intval($_POST['id']);
    if($id > 0){
        if($type == 'District'){
            if($_SESSION['language'] == 'th'){
                $sql = "SELECT DISTRICT_ID , DISTRICT_DESC_LOC AS DISTRICT_DESC FROM mas_district WHERE PROVINCE_ID = ".$id; 
            }else{
                $sql = "SELECT DISTRICT_ID , DISTRICT_DESC_ENG AS DISTRICT_DESC FROM mas_district WHERE PROVINCE_ID = ".$id; 
            }
        }else if($type == 'SubDistrict'){
            if($_SESSION['language'] == 'th'){
                $sql = "SELECT SUB_DISTRICT_ID , SUB_DISTRICT_DESC_LOC AS SUB_DISTRICT_DESC FROM mas_sub_district WHERE DISTRICT_ID = ".$id; 
            }else{
                $sql = "SELECT SUB_DISTRICT_ID , SUB_DISTRICT_DESC_ENG AS SUB_DISTRICT_DESC FROM mas_sub_district WHERE DISTRICT_ID = ".$id; 
            }
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
		$rows = array();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$rows[] = $row;
		}
		header('Content-type: application/json; charset=utf-8');
		print json_encode($rows);	
    }
    exit();
}else if(isset($_POST['clear'])){
    isset($_POST['name']) ? $name = $_POST['name'] : $name = '';
    isset($_POST['clear']) ? $clear = $_POST['clear'] : $clear = '';

    if($clear == 'clearTemp'){
        $target_dir1 = '../'._UPLOAD_DIR_;
        $target_dir2 = $target_dir1."temp";
        checkDir($target_dir1);
        checkDir($target_dir2); 

        $file_name = $target_dir2.'/'.$name;
        if(file_exists($file_name) && $name != ''){
            chmod($file_name , 0777);
            unlink($file_name);
        }
            chmod($target_dir2 , 0755);
            chmod($target_dir1 , 0755);
    }
    exit();
} 
die('<script>
    parent.returnTempError();
</script>'
);
CloseDB();
?>