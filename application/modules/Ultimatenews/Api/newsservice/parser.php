<?php header('HTTP/1.1 200 OK');header('Cache-Control: max-age=0');header('Content-Type: text/html; charset=utf-8');defined('DS') or define('DS',DIRECTORY_SEPARATOR);defined('PS') or define('PS',PATH_SEPARATOR);define('ROOT_PATH',dirname(__FILE__));define('CACHE_DIR',ROOT_PATH.'/cache');define('CACHE_ENABLED',is_dir(CACHE_DIR) && is_writeable(CACHE_DIR));define('CACHE_LIFETIME',3600);define('CURL_TIMEOUT',300);date_default_timezone_set('UTC');set_time_limit(0);set_include_path(implode(PATH_SEPARATOR,array( ROOT_PATH.'/library',ROOT_PATH.'/library/RSSFeed' )));require_once 'library/RSSFeed/YnRSSReader.php';require_once 'library/Readability/Readability.php';require_once 'library/NewsParser.php';$_QfOo8=$_REQUEST['uri'];$_QfOo8=html_entity_decode(urldecode(urldecode($_QfOo8)));if(!$_QfOo8){ exit('invalid param uri:');} $_Q86i8=TRUE;if(isset($_REQUEST['rssfeed']) && $_REQUEST['rssfeed']==1) { $_Q86i8=FALSE;} if(CACHE_ENABLED) { $_Q8tC6=CACHE_DIR.'/'.sha1($_QfOo8.'#10.'.intval($_Q86i8));if (file_exists($_Q8tC6)) { if(filemtime($_Q8tC6)+CACHE_LIFETIME > time()) { $_Q8Q1I =file_get_contents($_Q8tC6);if($_Q8Q1I){ echo $_Q8Q1I;exit(0);} } } } $_Q8tiL=new _QFC6F();$_Q8flt=$_Q8tiL-> _O1FJC($_QfOo8,10,$_Q86i8);$_Q8Q1I=json_encode(array( 'rows'=> $_Q8flt,'total'=> count($_Q8flt),));if(CACHE_ENABLED) { if ($_Q8Q1I) { if ($_Q8O80=fopen($_Q8tC6,'w')) { fwrite($_Q8O80,$_Q8Q1I);fclose($_Q8O80);} } } echo $_Q8Q1I;
