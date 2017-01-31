<?php
	session_start();
	date_default_timezone_set('Asia/Bangkok');
	define('_DATA_BASE_TYPE_','mysql');
	define('_DATA_BASE_HOST_','localhost');
	/*------------ Online ------------
	define('_DATA_BASE_NAME_','wanball_web');
	define('_DATA_BASE_USER_','wanball_web');
	define('_DATA_BASE_PASS_','y2SwsV4v');
	/*------------ Offline ------------ */
	define('_DATA_BASE_NAME_','webengine_db');
	define('_DATA_BASE_USER_','user');
	define('_DATA_BASE_PASS_','1234');
	/*------------ Page Config ------------*/
	define('_TITTLE_SITE_','Alpha');	
	define('_FULL_SITE_PATH_','http://192.168.1.125/');
	define('_BACK_OFFICE_PATH_','http://192.168.1.125/webengine/');
	define('_LOG_FILE_','log.sqlite');
	define('_UPLOAD_DIR_','../upload/');
	define('_FULL_UPLOAD_DIR_','http://192.168.1.125/upload/');
	define('_COUNTRY_CODE_','TH');
	/*------------ E-Mail ------------*/
	define('_MAIL_HOST_','mail.campaignactivity.com');
	define('_MAIL_PORT_','25');
	define('_MAIL_USER_','system@campaignactivity.com');
	define('_MAIL_PASS_','system');
	define('_MAIL_RECEIVER_','webmaster@campaignactivity.com');
	define('_HASH_KEY_','c21e4b9725fcd5fb461fb4f15b1c4dd7');
	/*------------ reCAPTCHA ID ------------*/
	define('_reCAPTCHA_ID_','6LcYgwcUAAAAANzYov_yw7BLAhTVLQh77DK-VbX4');
	define('_reCAPTCHA_SECRET_','6LcYgwcUAAAAAEyqt0esKBBjq1tanRByjedXWD7s');
	/*------------ google ID ------------*/
	define('_KEY_FILE_LOCATION_','webengine-b673f8bcb48b.json');
	define('_YOUTUBE_KEY_','AIzaSyD1ohS4NPMQY2fFSDILS2okg9fm2Bg4RUE');
	/*------------ facebook ID ------------*/
	define('_FB_APP_ID_','379204692436012');
	define('_FB_SECRET_','d7e036243386c6d2b64b76ed443d92e2');
	define('_FB_PAGE_TOKEN_','379204692436012|qmW8BSSftA1AHQ9I7Jy5dvBsuBI');
	
?>