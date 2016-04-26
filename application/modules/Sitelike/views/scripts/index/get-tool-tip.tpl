<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="sitelike_members_popup user_likes_feed_popup">
	<div class="top">
		<div class="heading"><?php echo $this->heading." ".$this->translate('liked.')?></div>
	</div>	
	<div class="likes_popup_content">
	  <?php foreach ($this->getMyLikedItems as $item): ?>
	    <div class="sitelike_popup_user_likes_list">
		    <div class="item_thumb">
		    	<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'),array('target'=>'blank')) ?>
		    </div>
		    <div class="item_details">
		    	<div class="item_title">
		    		<?php echo $this->htmlLink($item->getHref(), $item->getTitle(),array('target'=>'blank'));?>
		    	</div>	
		    </div>
	    </div>
	  <?php endforeach;?>
	</div>
</div>	
<div class="sitelike_members_popup_bottom">
	<button  onclick='javascript:parent.Smoothbox.close()' ><?php echo $this->translate('Close') ?></button>
</div>
