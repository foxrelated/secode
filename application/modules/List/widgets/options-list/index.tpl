<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<div id="profile_options">
  <?php
		echo $this->navigation()
      ->menu()
      ->setContainer($this->gutterNavigation)
      ->setUlClass('navigation lists_gutter_options')
      ->render();
  ?>
</div>