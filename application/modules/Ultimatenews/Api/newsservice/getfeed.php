<?php defined('DS') or define('DS',DIRECTORY_SEPARATOR);require_once 'library'.DS.'RSSFeed'.DS.'YnRSSReader.php';class _QFCJ0{ public function _O1DR6() { if (key_exists('uri',$_GET)) { $_QfOo8=$_GET['uri'];$_QfOo8=urldecode($_QfOo8);$_QfOo8=htmlspecialchars_decode($_QfOo8);} else { return 'no RSS link to get data.';} if(empty($_QfOo8)) { return 'invalid URL input';} $_QfoII=new YnRSSReader();$_QfC10=$_QfoII->parseRSSFeeds($_QfOo8);$_QfCtj=array();$_QfCl0=$_QfC10->get_items();$_QfCtj['item_count']=count($_QfCl0);$_QfCtj['logo']=$_QfC10-> get_image_url();$_QfCtj['favicon']=$_QfC10-> get_favicon();return $_QfCtj;} } $_QfiJI=new _QFCJ0();$_QfioQ=$_QfiJI->_O1DR6();echo json_encode($_QfioQ);
