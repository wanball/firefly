<?php
require ("../../inc/config.inc.php");	
header('Content-Type: application/javascript');
header("Cache-Control: must-revalidate");
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 1))); // 1 min
?>		
$(document).ready(function(){


	new jPlayerPlaylist({
		jPlayer: "#jquery_jplayer",
		cssSelectorAncestor: "#jp_container"
	}, [
<?php
	$dir = '../../music';
	$ignored = array('.', '..', '.svn', '.htaccess');
    foreach (scandir($dir) as $file) {

        if (!in_array($file, $ignored)){
	        $name  = str_replace('-',' ',$file);
	        $name  = str_replace('_',' ',$name);

         echo '{';
         echo 'title:"'.$name.'",';
         echo 'free:true,';
         echo 'mp3:"'._BACK_OFFICE_PATH_.'music/'.$file.'"';
         echo '},';
         echo "\n";
        }
    }
?>			

	], {
		swfPath: "plugins/jPlayer/jplayer",
		supplied: "oga, mp3",
		wmode: "window",
		useStateClassSkin: true,
		autoBlur: false,
		smoothPlayBar: true,
		keyEnabled: true
	});
});	
 