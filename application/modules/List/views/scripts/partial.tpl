<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: partial.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php echo $this->htmlLink($this->list->getHref(), $this->itemPhoto($this->list, 'thumb.icon')) ?>

<div class='seaocore_sidebar_list_info'>
	<div class='seaocore_sidebar_list_title'>
		<?php echo $this->htmlLink($this->list->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($this->list->getTitle(), 19), array('title' => $this->list->getTitle())) ?>
	</div>
	<div class='seaocore_sidebar_list_details'>