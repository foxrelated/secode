<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<ul class="sitepage_browse_sitepage_day">
	<li>
		<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->dayitem->page_id, $this->dayitem->owner_id, $this->dayitem->getSlug()), $this->itemPhoto($this->dayitem, 'thumb.profile')) ?>
		<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->dayitem->page_id, $this->dayitem->owner_id, $this->dayitem->getSlug()), $this->dayitem->getTitle(), array('title' => $this->dayitem->getTitle())) ?>
	</li>
</ul>