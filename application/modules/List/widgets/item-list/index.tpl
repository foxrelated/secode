<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php 
	$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
  	              . 'application/modules/Seaocore/externals/styles/styles.css');
?>

<ul class="seaocore_item_day">
	<li>
		<?php echo $this->htmlLink($this->dayitem->getHref(), $this->itemPhoto($this->dayitem,'thumb.profile')) ?>
		<?php echo $this->htmlLink($this->dayitem->getHref(), $this->dayitem->getTitle(), array('title' => $this->dayitem->getTitle())) ?>
	</li>
</ul>