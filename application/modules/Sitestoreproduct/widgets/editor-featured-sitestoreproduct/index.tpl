<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php 
	$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
  	              . 'application/modules/Seaocore/externals/styles/styles.css');
?>

<ul class="seaocore_item_day">
	<li>
		<?php echo $this->htmlLink($this->editor->getHref(), $this->itemPhoto($this->user,'thumb.profile')) ?>
		<?php echo $this->htmlLink($this->editor->getHref(), $this->user->getTitle(), array('title' => $this->editor->details)) ?>
	</li>
</ul>
