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

<div class="sm-content-list" id="sub_groups">
	<ul data-role="listview" data-icon="arrow-r">
		<?php foreach( $this->userListings as $sitegroup ): ?>
			<li>
				<a href="<?php echo Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id,$sitegroup->getSlug()); ?>">
					<?php echo $this->itemPhoto($sitegroup, 'thumb.icon'); ?>
					<h3><?php echo $sitegroup->getTitle() ?></h3>
        </a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ($this->userListings->count() > 1): ?>
		<?php
		echo $this->paginationAjaxControl(
						$this->userListings, $this->identity, "sub_groups");
		?>
  <?php endif;?>
</div>