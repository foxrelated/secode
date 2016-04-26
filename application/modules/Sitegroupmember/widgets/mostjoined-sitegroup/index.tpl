<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<ul class="sitegroup_sidebar_list">
	<?php foreach ($this->sitegroups as $sitegroup): ?>
		<li>
			<?php  $this->partial()->setObjectKey('sitegroup');
				echo $this->partial('application/modules/Sitegroup/views/scripts/partial_widget.tpl', $sitegroup); ?>
				<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.member.title' , 1);
				if ($sitegroup->member_title && $memberTitle) : ?>
				<?php if ($sitegroup->member_count == 1) : ?><?php echo $sitegroup->member_count . ' member'; ?> <?php else: ?>	<?php echo $sitegroup->member_count . ' ' .  $sitegroup->member_title; ?><?php endif; ?>
				<?php else : ?>
				<?php echo $this->translate(array('%s member', '%s members', $sitegroup->member_count), $this->locale()->toNumber($sitegroup->member_count)) ?>
				<?php endif; ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>