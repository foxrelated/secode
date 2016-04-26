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
<ul class="sitegroup_sidebar_list">
	<?php  foreach ($this->userListings as $sitegroup): ?>
		<li>
			<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id,$sitegroup->getSlug()), $this->itemPhoto($sitegroup, 'thumb.icon'),array('title' => $sitegroup->getTitle())) ?>
			<div class='sitegroup_sidebar_list_info'>
				<div class='sitegroup_sidebar_list_title'>
					<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id,$sitegroup->getSlug()), Engine_Api::_()->sitegroup()->truncation($sitegroup->getTitle()), array('title' => $sitegroup->getTitle())) ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>