<?php

/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthday
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<div class="generic_layout_container layout_birthday_show_birthdays"><h3><?php echo $this->translate($this->title_widget) ?></h3>
 <?php 
  include_once APPLICATION_PATH . '/application/modules/Birthday/views/scripts/_birthdaylist.tpl';
 ?>
</div>