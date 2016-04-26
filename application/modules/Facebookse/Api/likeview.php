<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    facebookse
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: likeview.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
  $image_likethumbsup = Engine_Api::_()->facebookse()->getDefaultLikeUnlikeIcon('like');
  $image_likethumbsdown = Engine_Api::_()->facebookse()->getDefaultLikeUnlikeIcon('unlike');

	//GET THE BACKGROUND COLOR.
	$background_color = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'fblike.background.color' , '#f1f2f1' ) ;

	//GET THE BACKGROUND HAOUR COLOR.
	$background_haour_color = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'fblike.background.haourcolor' , '#f1f2f1' ) ;

	//GET THE TEXT HAOUR COLOR.
	$text_haour_color = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting('fblike.haour.color' , '#666666');

	//GET THE TEXT COLOR.
	$text_color = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'fblike.text.color' , '#666666' ) ;
?>
<style type="text/css">
  .facebookse_button a{
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
  .facebookse_button a{
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
  .facebookse_button a:hover	{
    background-color:<?php echo $background_haour_color ; ?>;
  }
  .facebookse_button a i.like_thumbup_icon	{
    background-image: url(<?php echo $image_likethumbsup ; ?>);
  }
  .facebookse_button a i.like_thumbdown_icon	{
    background-image: url(<?php echo $image_likethumbsdown ; ?>);
  }
  .facebookse_button a span	{
    color:<?php echo $text_color ; ?>;
  }
  .facebookse_button a:hover span	{
    color:<?php echo $text_haour_color ; ?>;
  }
  /* like button end here */
</style>