<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: partial_widget.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($this->sitegroup->group_id, $this->sitegroup->owner_id, $this->sitegroup->getSlug()), $this->itemPhoto($this->sitegroup, 'thumb.icon'), array('title' => $this->sitegroup->getTitle())) ?>
	<div class='sitegroup_sidebar_list_info'>
		<div class='sitegroup_sidebar_list_title'>
			<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($this->sitegroup->group_id, $this->sitegroup->owner_id, $this->sitegroup->getSlug()), Engine_Api::_()->sitegroup()->truncation($this->sitegroup->getTitle()), array('title' => $this->sitegroup->getTitle())) ?>
		</div>
		<div class='sitegroup_sidebar_list_details'>