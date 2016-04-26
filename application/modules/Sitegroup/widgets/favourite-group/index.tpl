<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 6508 2010-06-22 23:41:11Z shaun $
 * @author     John
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<ul class="sitegroup_sidebar_list">
	<?php  foreach ($this->userListings as $sitegroup): ?>
		<li>
			<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id_for, $sitegroup->owner_id,$sitegroup->getSlug()), $this->itemPhoto($sitegroup, 'thumb.icon'),array('title' => $sitegroup->getTitle())) ?>
			<div class='sitegroup_sidebar_list_info'>
				<div class='sitegroup_sidebar_list_title'>
					<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id_for, $sitegroup->owner_id,$sitegroup->getSlug()), Engine_Api::_()->sitegroup()->truncation($sitegroup->getTitle()), array('title' => $sitegroup->getTitle())) ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>