<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: mutualfriend.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="sm-content-list">
    <ul class='sm-ui-lists'  data-role="listview" data-icon="false"> 
			<?php foreach ($this->friend_obj as $friend_info) { ?>
			
			<li>
		<a href="<?php echo $friend_info->getHref() ?>">
          <?php echo $this->itemPhoto($friend_info, 'thumb.icon') ?>
          <div class="ui-list-content">
            <h3><?php echo $friend_info->getTitle() ?></h3>
          </div>	
        </a>
   </li>
    <?php } ?>
    </ul>
    
</div>