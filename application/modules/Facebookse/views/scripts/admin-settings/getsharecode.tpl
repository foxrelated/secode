<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: getsharecode.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

//GENERATING THE LIKE BUTTON CODE FOR LIKE BUTTON.

$share_url = '';
$share_type = '';

if (!empty($this->share_button['share_url'])) {
  $share_url = 'href= "' . $this->share_button['share_url'] . '" ';
}

if (!empty($this->share_button['share_type'])) {
  $share_type = 'type= "' . $this->share_button['share_type'] . '" ';
}

$share =  '<fb:share-button ' .  $share_url .  $share_type . '></fb:share-button>';?>

<textarea name='share_textarea' id="txtArea"  value='' style='width:500px; height:100px;'><?php echo $share;?></textarea>