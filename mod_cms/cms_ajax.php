<?php
require ("../inc/config.inc.php");
require ("../inc/connectdb.inc.php");
require ("../inc/function.inc.php");

if(isset($_POST['pid'])){
    $language = language_data();

        $id = intval($_POST['pid']);
        $sql = "SELECT mod_cms_pattern_placeholder  FROM mod_cms_pattern WHERE mod_cms_pattern_id = ".$id; 
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $expension  = str_replace('.','',$row['mod_cms_pattern_placeholder']);
        $expensions = explode(',',$expension);
        unset($expension);
        unset($row);

        $target_dir1 = '../'._UPLOAD_DIR_;
        $target_dir2 = $target_dir1."temp";
        checkDir($target_dir1);
        checkDir($target_dir2); 

        $max_size = file_upload_max_size();
        $max_size = ceil($max_size * 0.90);

        $Success = array();
        $errors = array();
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
            }

        }      

    $data = array();
    $data['success'] = $Success;
    $data['errors'] = $errors;

    echo '<script>
    var obj = ' . json_encode($data) . ';
        parent.returnTempFile(obj);
    </script>';


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
} 
die('<script>
    parent.returnTempError();
</script>'
);
CloseDB();
?>