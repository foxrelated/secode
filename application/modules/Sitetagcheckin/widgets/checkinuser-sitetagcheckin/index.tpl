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
<?php
	$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitetagcheckin/externals/styles/style_sitetagcheckin.css');
?>
<ul class="seaocore_sidebar_list">
	<li>
		<b><?php echo $this->translate(array("%s $this->checkedin_heading", "%s $this->checkedin_heading", $this->check_in_user_count), $this->locale()->toNumber($this->check_in_user_count)); ?></b>
	</li> 
  <?php foreach ($this->results as $value): ?>
    <li>
      <?php if ($this->checkedin_user_photo): ?>
        <?php echo $this->htmlLink($value->getHref(), $this->itemPhoto($value, 'thumb.icon', $value->getTitle()), array('class' => 'item_photo', 'target' => '_parent', 'title' => $value->getTitle())); ?>
      <?php endif; ?>
      <div class="seaocore_sidebar_list_info">
        <?php if ($this->checkedin_user_name): ?>
          <div class="seaocore_sidebar_list_title">
            <?php $title1 = $value->getTitle(); ?>
            <?php $truncatetitle = Engine_String::strlen($title1) > 20 ? Engine_String::substr($title1, 0, 20) . '..' : $title1 ?>
            <?php echo $this->htmlLink($value->getHref(), $truncatetitle, array('title' => $value->getTitle(), 'target' => '_parent')); ?>
          </div>	
        <?php endif; ?>
        <?php if ($this->checkedin_user_checkedtime): ?>
          <div class="seaocore_sidebar_list_details">
            <?php echo $this->timestamp($value->location_modified_date) ?>
          </div>
        <?php endif; ?>
    </li>
  <?php endforeach; ?>
  <?php if ($this->check_in_user_count > 1): ?>
    <li>
      <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitetagcheckin', 'controller' => 'checkin', 'action' => 'see-all-checkin-user', 'resource_type' => $this->resource_type, 'resource_id' => $this->resource_id, 'call_status' => 'public', 'checkedin_status' => $this->checkedin_users, 'checkedin_item_count' => 5, 'checkedin_see_all_heading' => $this->checkedin_see_all_heading), $this->translate("See All &raquo;"), array('class' => 'smoothbox more_link')) ?> 
    </li>	
  <?php endif; ?>
</ul>	