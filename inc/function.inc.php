<?php
function find_ip()
{
    //ตรวจสอบ IP
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } else if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function checkDir($dir)
{
    //เช็ค dir
    if (!is_dir($dir)) {
        mkdir($dir, 0777);
    } else {
        chmod($dir, 0777);
    }
}

function checkLogFile()
{
    //เช็ค log
    //create or open (if exists) the database
    $db = new SQLite3(_LOG_FILE_);
    $db->exec('CREATE TABLE if not exists  "log" ("log_id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL , "log_ip" VARCHAR, "log_action" TEXT, "log_datetime" DATETIME)');
    
}
function logAccess($action)
{
    
    $insert['log_ip']       = "'" . find_ip() . "'";
    $insert['log_action']   = "'" . $action . "'";
    $insert['log_datetime'] = "'" . date('Y-m-d H:i:s') . "'";
    
    $sql = "INSERT INTO log (" . implode(",", array_keys($insert)) . ") VALUES (" . implode(",", array_values($insert)) . ")";
    
    if (file_exists(_LOG_FILE_)) {
        $path_log = _LOG_FILE_;
    } else {
        $path_log = '../' . _LOG_FILE_;
    }
    
    $db = new SQLite3($path_log);
    $db->exec($sql);
}
function userOnline($pid)
{
    global $conn;
    $pid = intval($pid);
    
    $stmt = $conn->prepare("SELECT * FROM mod_user_online WHERE mod_user_online_pid = " . $pid);
    $stmt->execute();
    $count = $stmt->rowCount();
    if ($count == 0) {
        $insert                             = array();
        $insert['mod_user_online_ip']       = "'" . find_ip() . "'";
        $insert['mod_user_online_pid']      = "'" . $pid . "'";
        $insert['mod_user_online_datetime'] = "NOW()";
        
        $sql = "INSERT INTO mod_user_online (" . implode(",", array_keys($insert)) . ") VALUES (" . implode(",", array_values($insert)) . ")";
    } else {
        $update   = "";
        $update[] = "mod_user_online_pid 		= '" . $pid . "'";
        $update[] = "mod_user_online_ip 		= '" . find_ip() . "'";
        $update[] = "mod_user_online_datetime 	= NOW()";
        
        $sql = "UPDATE mod_user_online SET  " . implode(",", $update) . " WHERE mod_user_online_pid = " . $pid;
    }
    $stmt = $conn->prepare($sql);
    $stmt->execute();
}
function random_pic($dir)
{
    //สุ่มรูปภาพ
    $files = glob($dir . '/*.*');
    $file  = array_rand($files);
    return $files[$file];
}

/*encryption function*/
function My_Encode($string, $key)
{
    error_reporting(error_reporting() & ~E_NOTICE);
    $key    = hash('sha512', $key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
    for ($i = 0; $i < $strLen; $i++) {
        $ordStr = ord(substr($string, $i, 1));
        if ($j == $keyLen) {
            $j = 0;
        }
        $ordKey = ord(substr($key, $j, 1));
        $j++;
        $hash .= strrev(base_convert(dechex($ordStr + $ordKey), 16, 36));
    }
    return trim($hash);
}
function My_Decode($string, $key)
{
    error_reporting(error_reporting() & ~E_NOTICE);
    $key    = hash('sha512', $key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
    for ($i = 0; $i < $strLen; $i += 2) {
        $ordStr = hexdec(base_convert(strrev(substr($string, $i, 2)), 36, 16));
        if ($j == $keyLen) {
            $j = 0;
        }
        $ordKey = ord(substr($key, $j, 1));
        $j++;
        $hash .= chr($ordStr - $ordKey);
    }
    return trim($hash);
}
/*encryption function*/

function authenticationLocation()
{
    error_reporting(error_reporting() & ~E_NOTICE);
    $details = json_decode(file_get_contents("http://ipinfo.io/" . find_ip() . "/json"));
    $country = $details->country; // -> "Mountain View"	
    if (_COUNTRY_CODE_ != $country) {
        warningmail();
    }
    
    return $country;
}
function warningmail()
{
    
}
function language_data($lang = 'th')
{
    
    if (isset($_SESSION['language'])) {
        $lang = $_SESSION['language'];
    } else {
        $_SESSION['language'] = $lang;
    }
    
    switch ($lang) {
        case 'en':
            require('language.en.inc.php');
            break;
        default:
            require('language.th.inc.php');
            break;
    }
    return $language;
}
function dateShow($lang, $format_day, $format_month, $format_year, $format_hour, $format_minutes, $date_source)
{
    $date = new DateTime($date_source);
    if ($lang == 'th') {
        $dateShow = '';
        if ($format_day != '') {
            $dateShow .= $date->format($format_day);
            $dateShow .= ' ';
        }
        if ($format_month != '') {
            $Month_source = $date->format('m');
            
            if ($format_month == 'M') {
                $dateShow .= DateShortTH($Month_source);
            } else if ($format_month == 'F') {
                $dateShow .= DateLongTH($Month_source);
            } else {
                $dateShow .= $date->format($format_month);
            }
            
            $dateShow .= ' ';
        }
        if ($format_year != '') {
            $year_source = $date->format('Y');
            $year_source += 543;
            
            if ($format_year == 'y') {
                $dateShow .= substr($year_source, -2);
            } else {
                $dateShow .= $year_source;
            }
        }
    } else {
        $dateShow = $date->format($format_day . ' ' . $format_month . ' ' . $format_year);
    }
    
    //time
    if ($format_minutes != '') {
        $dateShow .= ' ';
        $dateShow .= $date->format($format_hour . ':' . $format_minutes);
    }
    
    
    return trim($dateShow);
}
function DateShortTH($Month_source)
{
    switch ($Month_source) {
        case "01":
            $myMonth = "ม.ค.";
            break;
        case "02":
            $myMonth = "ก.พ.";
            break;
        case "03":
            $myMonth = "มี.ค.";
            break;
        case "04":
            $myMonth = "เม.ย.";
            break;
        case "05":
            $myMonth = "พ.ค.";
            break;
        case "06":
            $myMonth = "มิ.ย.";
            break;
        case "07":
            $myMonth = "ก.ค.";
            break;
        case "08":
            $myMonth = "ส.ค.";
            break;
        case "09":
            $myMonth = "ก.ย.";
            break;
        case "10":
            $myMonth = "ต.ค.";
            break;
        case "11":
            $myMonth = "พ.ย.";
            break;
        case "12":
            $myMonth = "ธ.ค.";
            break;
    }
    return $myMonth;
}
function DateLongTH($Month_source)
{
    switch ($Month_source) {
        case "01":
            $myMonth = "มกราคม";
            break;
        case "02":
            $myMonth = "กุมภาพันธ์";
            break;
        case "03":
            $myMonth = "มีนาคม";
            break;
        case "04":
            $myMonth = "เมษายน";
            break;
        case "05":
            $myMonth = "พฤษภาคม";
            break;
        case "06":
            $myMonth = "มิถุนายน";
            break;
        case "07":
            $myMonth = "กรกฎาคม";
            break;
        case "08":
            $myMonth = "สิงหาคม";
            break;
        case "09":
            $myMonth = "กันยายน";
            break;
        case "10":
            $myMonth = "ตุลาคม";
            break;
        case "11":
            $myMonth = "พฤศจิกายน";
            break;
        case "12":
            $myMonth = "ธันวาคม";
            break;
    }
    return $myMonth;
}
function DateISO8601($date_source)
{
    $datetime = new DateTime($date_source);
    return $datetime->format('c');
}
function DateToDB($date_format, $date_source)
{
    $datetime = DateTime::createFromFormat($date_format, $date_source);
    return $datetime->format('Y-m-d H:i:s');
}
function is_image($path)
{
    $a          = @getimagesize($path);
    $image_type = $a[2];
    
    if (in_array($image_type, array(
        IMAGETYPE_GIF,
        IMAGETYPE_JPEG,
        IMAGETYPE_PNG,
        IMAGETYPE_BMP
    ))) {
        return true;
    }
    return false;
}
function GetFlagByName($name)
{
    if (file_exists('images/flags/' . $name . '.png')) {
        return 'images/flags/' . $name . '.png';
    } else {
        if (file_exists('images/flags2/' . $name . '.png')) {
            return 'images/flags2/' . $name . '.png';
        } else {
            return false;
        }
    }
}
function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' kB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    
    return $bytes;
}
function file_upload_max_size()
{
    static $max_size = -1;
    
    if ($max_size < 0) {
        // Start with post_max_size. 
        $max_size = parse_size(ini_get('post_max_size'));
        
        // If upload_max_size is less, then reduce. Except if upload_max_size is
        // zero, which indicates no limit.
        $upload_max = parse_size(ini_get('upload_max_filesize'));
        if ($upload_max > 0 && $upload_max < $max_size) {
            $max_size = $upload_max;
        }
    }
    return $max_size;
}

function parse_size($size)
{
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
    $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
    if ($unit) {
        // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
        return round($size);
    }
}
function natural_language_join(array $list, $conjunction = 'and')
{
	$conjunction = ' '.$conjunction.' ';
    $last  = array_slice($list, -1);
    $first = join(', ', array_slice($list, 0, -1));
    $both  = array_filter(array_merge(array(
        $first
    ), $last), 'strlen');
    return join($conjunction, $both);
}
?>