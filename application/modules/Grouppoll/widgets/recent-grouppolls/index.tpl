<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<ul class="seaocore_sidebar_list">
  <?php foreach ($this->listRecentPolls as $grouppoll): ?>
    <li>
      <?php 
	        // THERE SOME SIMILAR CODE IN WIDGETS LIKE COMMENTS AND VIEWS AND PHOTO ITEM.
				  include APPLICATION_PATH . '/application/modules/Grouppoll/views/scripts/pollWidgets.tpl';
				?> 
    </li>
  <?php endforeach; ?>
</ul>
