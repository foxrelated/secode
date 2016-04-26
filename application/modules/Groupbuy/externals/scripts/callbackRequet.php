<?php

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));  
include APPLICATION_PATH . '/application/modules/Groupbuy/cli.php'; 
include_once 'cart/logging.php';
define('ALLOWED_REFERRER', '');
define('LOG_DOWNLOADS',true);
define('LOG_FILE','download.log');


ini_set('display_errors',1);
ini_set('error_reporting',-1);
ini_set('display_startup_error',1);




try
{
	$log = new Logging();
	$log->write(print_r($_REQUEST,true));
    $action = $_REQUEST['action'];
	
	
	
}catch(Exception $e){
	
}
