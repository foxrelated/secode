<?php

###############################################################
# File Download 1.31
###############################################################
###############################################################
# Sample call:
#    download.php?f=phptutorial.zip
#
# Sample call (browser will try to save with new file name):
#    download.php?f=phptutorial.zip&fc=php123tutorial.zip
###############################################################

// Allow direct file download (hotlinking)?
// Empty - allow hotlinking
// If set to nonempty value (Example: example.com) will only allow downloads when referrer contains this text
define('ALLOWED_REFERRER', '');

// Download folder, i.e. folder where you keep all files for download.
// MUST end with slash (i.e. "/" )
define('BASE_DIR','../../../../../public/mp3music_song');
//echo $_SERVER['DOCUMENT_ROOT'];
//$currentDirectory = array_pop(explode("/", getcwd()));
//echo $currentDirectory;
//echo BASE_DIR;
// log downloads?  true/false
//define('LOG_DOWNLOADS',true);
// log file name
//define('LOG_FILE','downloads.log');
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));
include APPLICATION_PATH . '/application/modules/Mp3music/cli.php';  
   $translate= Zend_Registry::get('Zend_Translate');
   $song = null;
   $album = null;
   if(isset($_GET['idsong']))
    {
        $song_id = $_GET['idsong'];
        $song     = Engine_Api::_()->getItem('mp3music_album_song', $song_id);
        if(!$song || !Mp3music_Api_Core::checkDownload($song, 'song'))
        {
            $url = curPageURL();
            $index = strpos($url,"/application/");
            $core_path = substr($url,0,$index);
            header('Location:'.$core_path.'/login');
            return;
        }
        $song->download_count = $song->download_count + 1;
        $song->save(); 
    } 
	if(isset($_GET['idalbum']))
    {
    	$album_id = $_GET['idalbum'];
        $album    = Engine_Api::_()->getItem('mp3music_album', $album_id);
		if(!$album || !Mp3music_Api_Core::checkDownload($album, 'album'))
        {
        	$url = curPageURL();
            $index = strpos($url,"/application/");
            $core_path = substr($url,0,$index);
            header('Location:'.$core_path.'/login');
            return;
        }
        $album->download_count = $album->download_count + 1;
        $album->save(); 
	}
// Allowed extensions list in format 'extension' => 'mime type'
// If myme type is set to empty string then script will try to detect mime type 
// itself, which would only work if you have Mimetype or Fileinfo extensions
// installed on server.
$allowed_ext = array (

  // archives
  'zip' => 'application/zip',

  // documents
  'pdf' => 'application/pdf',
  'doc' => 'application/msword',
  'xls' => 'application/vnd.ms-excel',
  'ppt' => 'application/vnd.ms-powerpoint',
  
  // executables
  'exe' => 'application/octet-stream',

  // images
  'gif' => 'image/gif',
  'png' => 'image/png',
  'jpg' => 'image/jpeg',
  'jpeg' => 'image/jpeg',

  // audio
  'mp3' => 'audio/mpeg',
  'wav' => 'audio/x-wav',

  // video
  'mpeg' => 'video/mpeg',
  'mpg' => 'video/mpeg',
  'mpe' => 'video/mpeg',
  'mov' => 'video/quicktime',
  'avi' => 'video/x-msvideo'
);



####################################################################
###  DO NOT CHANGE BELOW
####################################################################

// If hotlinking not allowed then make hackers think there are some server problems
if (ALLOWED_REFERRER !== ''
&& (!isset($_SERVER['HTTP_REFERER']) || strpos(strtoupper($_SERVER['HTTP_REFERER']),strtoupper(ALLOWED_REFERRER)) === false)
) {
  die($translate->translate("Internal server error. Please contact system administrator."));
}

// Make sure program execution doesn't time out
// Set maximum script execution time in seconds (0 means no limit)
set_time_limit(0);

if (!$song && !$album) {
  die($translate->translate("Please specify file name for download."));
}

// Check in subfolders too
function curPageURL() {
         $pageURL = 'http';
 if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
     $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}// find_file
if($song)
{
	// Nullbyte hack fix
	if (strpos($song->getFilePath(), "\0") !== FALSE) die('');
	
	// get full file path (including subfolders)
	$file_path = $song->getFilePath();
	
	$fname = basename($file_path);
	$str_pos = strpos($fname,'?');
	if($str_pos)
		$fname = substr($fname,0,$str_pos);

	$index = strpos($file_path,"/modules/Mp3music/externals/scripts/");
	if($index)
	{
		$url = curPageURL();
		$index = strpos($url,"/application/");
    	$core_path = substr($url,0,$index);
		$file = Engine_Api::_()->getItem('storage_file', $song->file_id);
		$file_path = $core_path."/".$file->storage_path;
	}
	
	$tmp_file = APPLICATION_PATH . '/public/temporary/' . $song->song_id.".mp3";
	copy($file_path, $tmp_file);
	
	if (!is_file($tmp_file)) 
	{
	  die($translate->translate("File does not exist. Make sure you specified correct file name.")); 
	}
	
	// file size in bytes
	$fsize = filesize($tmp_file); 
	
	// file extension
	$fext = strtolower(substr(strrchr($fname,"."),1));
	
	// check if allowed extension
	if (!array_key_exists($fext, $allowed_ext)) {
	  die($translate->translate("Not allowed file type.")); 
	}
	
	// get mime type
	if ($allowed_ext[$fext] == '') {
	  $mtype = '';
	  // mime type is not set, get from server settings
	  if (function_exists('mime_content_type')) {
	    $mtype = mime_content_type($tmp_file);
	  }
	  else if (function_exists('finfo_file')) {
	    $finfo = finfo_open(FILEINFO_MIME); // return mime type
	    $mtype = finfo_file($finfo, $tmp_file);
	    finfo_close($finfo);  
	  }
	  if ($mtype == '') {
	    $mtype = "application/force-download";
	  }
	}
	else {
	  // get mime type defined by admin
	  $mtype = $allowed_ext[$fext];
	}
	
	// Browser will try to save file with this filename, regardless original filename.
	// You can override it if needed.
	
	if (empty($song->title)) {
	  $asfname = $fname;
	}
	else {
	  // remove some bad chars
	  $asfname = str_replace(array('"',"'",'\\','/'), '',$song->title.".mp3");
	  if ($asfname === '') $asfname = 'YouNetMusic';
	}
	
	// set headers
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-Type: $mtype");
	header("Content-Disposition: attachment; filename=\"$asfname\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " . $fsize);
	
	// download
	// @readfile($file_path);
	$file = @fopen($tmp_file,"rb");
	if ($file) {
	  while(!feof($file)) {
	    print(fread($file, 1024*8));
	    flush();
	    if (connection_status()!=0) {
	      @fclose($file);
	      die();
	    }
	  }
	  @fclose($file);
	  @unlink($tmp_file);
	}
	
	// log downloads
	if (!LOG_DOWNLOADS) die();
	
	$f = @fopen(LOG_FILE, 'a+');
	if ($f) {
	  @fputs($f, date("m.d.Y g:ia")."  ".$_SERVER['REMOTE_ADDR']."  ".$song_id."\n");
	  @fclose($f);
	}
}
else if($album)
{
	 function remove_sign_string($str) 
     {
      $sign = array("à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă", "ằ", "ắ", "ặ", "ẳ", "ẵ", "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề", "ế", "ệ", "ể", "ễ", "ì", "í", "ị", "ỉ", "ĩ", "ò", "ó", "ọ", "ỏ",
          "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ", "ờ", "ớ", "ợ", "ở", "ỡ", "ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ", "ỳ", "ý", "ỵ", "ỷ", "ỹ", "đ", "À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ",
          "Ẫ", "Ă", "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ", "È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ", "Ì", "Í", "Ị", "Ỉ", "Ĩ", "Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ", "Ờ", "Ớ", "Ợ", "Ở", "Ỡ",
          "Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ", "Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ", "Đ", "ê", "ù", "à");

      $unsign = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "i", "i", "i", "i", "i", "o", "o", "o", "o",
          "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "y", "y", "y", "y", "y", "d", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A",
          "A", "A", "A", "A", "A", "A", "A", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O",
          "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "Y", "Y", "Y", "Y", "Y", "D", "e", "u", "a");
      return str_replace($sign, $unsign, $str);
    }	
	function zipFiles($file_names,$archive_file_name,$file_path)
	{
	      
	      $zip = new ZipArchive();
	
	      if ($zip->open($file_path.$archive_file_name, ZIPARCHIVE::CREATE )!==TRUE) 
	      {
	        exit("cannot open <$archive_file_name>\n");
	      }
	
	      if(is_array($file_names))
		      foreach($file_names as $files)
		      {
		        $zip->addFile($files['url'], $files['title'].'.mp3');
		      }
	      $zip->close();
	    
	      return $archive_file_name;
	}
	//used when zip some file from some other album
	function createFileName()
	{
	      $sid = 'abcdefghiklmnopqstvxuyz0123456789ABCDEFGHIKLMNOPQSTVXUYZ';
	      $max =  strlen($sid) - 1;
	      $res = "";
	      for($i = 0; $i<16; ++$i)
	      {
	          $res .=  $sid[mt_rand(0, $max)];
	      }  
	      return $res;
	}
	//create name file download
	//$namedownload = createFileName().'.zip';
	$namedownload = remove_sign_string($album->title).'.zip';
	$file_path = APPLICATION_PATH . '/public/temporary/';
	$songs = $album->getSongs();
	$file_names=array();
  	$iKey=0;
	foreach ($songs as $song) 
	{
		if(Mp3music_Api_Core::checkDownload($song, 'song'))
        {
			$song_path = $song->getFilePath();
			$fname = basename($song_path);
			$str_pos = strpos($fname,'?');
			if($str_pos)
				$fname = substr($fname,0,$str_pos);
			$index = strpos($song_path,"/modules/Mp3music/externals/scripts/");
			if($index)
			{
				$url = curPageURL();
				$index = strpos($url,"/application/");
		    	$core_path = substr($url,0,$index);
				$file = Engine_Api::_()->getItem('storage_file', $song->file_id);
				$song_path = $core_path."/".$file->storage_path;
			}
			
			$tmp_file = $file_path. $song->song_id.".mp3";
			copy($song_path, $tmp_file);
			
	        $file_names[$iKey]['url']= $tmp_file;
	    	$file_names[$iKey]['title']=remove_sign_string($song->title);
	    	$iKey++;
		}
	}
	if($iKey <= 0)
	{
		die($translate->translate("No songs or albums are found!"));
	}
	$linkdownload = zipFiles($file_names,$namedownload,$file_path);
	$linkdownload = $file_path.$linkdownload;

	$fsize = @filesize($linkdownload);
	$fls= substr($namedownload, 0 , -4);
	// set headers
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-Type: application/zip");
	header("Content-Disposition: attachment; filename=\"$namedownload\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " . $fsize);
	
	// download
	// @readfile($file_path);
	foreach($file_names as $files)
    {
        @unlink($files['url']);
    }
	$file = @fopen($linkdownload,"rb");
	@unlink($linkdownload);
	if ($file) {
	  while(!feof($file)) {
	    print(fread($file, 1024*8));
	    flush();
	    if (connection_status()!=0) {
	      @fclose($file);
	      @unlink($linkdownload);
		  die();
	    }
	  }
	  @fclose($file);
	  @unlink($linkdownload);
	}
}

?>