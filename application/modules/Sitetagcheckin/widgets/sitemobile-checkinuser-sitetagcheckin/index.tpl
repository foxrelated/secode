<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="sm-content-list">
	<h4><?php echo $this->translate(array("%s $this->checkedin_heading", "%s $this->checkedin_heading", $this->check_in_user_count), $this->locale()->toNumber($this->check_in_user_count)); ?></h4>
	<ul id="profile_checkins" data-role="listview" data-icon="arrow-r">
		<?php foreach ($this->results as $user): ?>
			<li>
				<a href="<?php echo $user->getHref(); ?>">
					<?php echo $this->itemPhoto($user, 'thumb.icon'); ?>
					<h3><?php echo $user->getTitle() ?></h3>
          <p><?php echo $this->timestamp($user->location_modified_date) ?></p>
				</a> 
			</li>
		<?php endforeach; ?>
		<?php if ($this->results->count() > 1): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->results, $this->identity, "profile_checkins");
			?>
		<?php endif; ?>
	</ul>
</div>