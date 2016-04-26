<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="sitegroup_viewgroups_head" id='thumb_icon' style="display:none;">
	<?php echo $this->htmlLink($this->sitegroup->getHref(), $this->itemPhoto($this->sitegroup, 'thumb.icon', '', array('align' => 'left'))) ?>
	<?php if (!empty($this->showTitle )) : ?>
		<h2>	
			<?php echo $this->sitegroup->getTitle(); ?>	
		</h2>  
  <?php endif ; ?>
</div>

