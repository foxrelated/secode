<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: likesettings.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

	//header("Content-type: text/css");
	$view = Zend_Registry::isRegistered( 'Zend_View' ) ? Zend_Registry::get( 'Zend_View' ) : null ;

	//GET THE CDN PATH.
	$cdn_path = Engine_Api::_()->sitelike()->getCdnPath();

	//GET IMAGE SETTING.
	$image_id = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.thumbsup.image' , 0 ) ;

	//CHECK FOR IMAGE ID.
	if(!empty($image_id)) {

		//GET THE IMAGE PATH.
		$img_path = Engine_Api::_()->storage()->get($image_id, '')->getHref();
		if($cdn_path == "") {
			$image_likethumbsup = 'http://' . $_SERVER['HTTP_HOST'] . $img_path;
		}	else {
			$img_cdn_path = str_replace($cdn_path, '',  $img_path);
			$image_likethumbsup = $cdn_path. $img_cdn_path;
		}
	}	else {

		//FOR DEFAULT IMAGE.
		$image_likethumbsup = $view->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/images/thumb-up.png' ;
	}

	//GET IMAGE SETTING.
	$image_id = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.thumbsdown.image' , 0 ) ;

	//CHECK FOR IMAGE ID.
	if(!empty($image_id)) {

		//GET THE IMAGE PATH.
		$img_path = Engine_Api::_()->storage()->get($image_id, '')->getHref();
		if($cdn_path == "") {
			$image_likethumbsdown = 'http://' . $_SERVER['HTTP_HOST'] . $img_path;
		}	else {
			$img_cdn_path = str_replace($cdn_path, '',  $img_path);
			$image_likethumbsdown = $cdn_path. $img_cdn_path;
		}
	}	else {

		//FOR DEFAULT IMAGE.
		$image_likethumbsdown = $view->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/images/thumb-down.png' ;
	}

	//GET THE BACKGROUND COLOR.
	$background_color = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.background.color' , '#f1f2f1' ) ;

	//GET THE BACKGROUND HAOUR COLOR.
	$background_haour_color = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.background.haourcolor' , '#f1f2f1' ) ;

	//GET THE TEXT HAOUR COLOR.
	$text_haour_color = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting('like.haour.color' , '#666666');

	//GET THE TEXT COLOR.
	$text_color = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.text.color' , '#666666' ) ;
?>
<style type="text/css">
  .sitelike_button a{
    background-color:<?php echo $background_color ; ?>;
    background-position:bottom;
    padding:3px 5px;
    text-decoration:none;
    font-weight:bolder;
    border:1px solid #cecece;
    clear:both;
    font-size:11px;
    float:left;
    outline:none;
  }
  .sitelike_button a{
    background-color:<?php echo $background_color ; ?>;
    background-position:bottom;
    padding:3px 5px;
    text-decoration:none;
    font-weight:bolder;
    border:1px solid #cecece;
    clear:both;
    font-size:11px;
    float:left;
    outline:none;
  }
  .sitelike_button a:hover	{
    background-color:<?php echo $background_haour_color ; ?>;
  }
  .sitelike_button a i.like_thumbup_icon	{
    background-image: url(<?php echo $image_likethumbsup ; ?>);
  }
  .sitelike_button a i.like_thumbdown_icon	{
    background-image: url(<?php echo $image_likethumbsdown ; ?>);
  }
  .sitelike_button a span	{
    color:<?php echo $text_color ; ?>;
  }
  .sitelike_button a:hover span	{
    color:<?php echo $text_haour_color ; ?>;
  }
  /* like button end here */
</style>