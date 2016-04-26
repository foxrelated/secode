<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-checkin-content.tpl 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $i = 1; ?>


<?php if ($this->checkin_count > 1): ?>
  <div id="prev"><a class="disabled" onclick="showpreimage(0,'2');"><?php echo $this->translate("Prev") ?></a></div><div id="next"><a onclick="showpreimage(1,'2');"><?php echo $this->translate("Next") ?></a></div>

<?php endif; ?>

<div id="checkin_content" style="display:block;">
  <?php foreach ($this->checkin_rows_withlocation as $key => $checkinlocation) : ?>


    <?php $checkin_item = Engine_Api::_()->getItem($checkinlocation['resource_type'], $checkinlocation['resource_id']); ?>

    <?php if ($i == 1) : ?>
      <div id="show_content_detail_<?php echo $i; ?>" style="display:block;"> 
        <?php echo $this->htmlLink($checkin_item->getHref(), $this->itemPhoto($checkin_item, 'thumb.normal', '', array('style' => 'max-height:100px;'))); ?>
        <?php echo $this->htmlLink($checkin_item->getHref(), $checkin_item->getTitle()); ?>
        <?php echo ucfirst($checkin_item->getShortType()); ?>
        <?php echo Engine_Api::_()->seaocore()->getCategory($checkin_item->getType(), $checkin_item); ?>
        <?php echo $this->timestamp($checkinlocation['modified_date']) ?> 
      </div>
    <?php else : ?>
      <div id="show_content_detail_<?php echo $i; ?>" style="display:none;"> 
        <?php echo $this->htmlLink($checkin_item->getHref(), $this->itemPhoto($checkin_item, 'thumb.normal', '', array('style' => 'max-height:100px;'))); ?>
        <?php echo $this->htmlLink($checkin_item->getHref(), $checkin_item->getTitle()); ?>
        <?php echo ucfirst($checkin_item->getShortType()); ?>
        <?php echo Engine_Api::_()->seaocore()->getCategory($checkin_item->getType(), $checkin_item); ?>
        <?php echo $this->timestamp($checkinlocation['creation_date']) ?>  
      </div>
    <?php endif; ?> 

    <?php $i++; ?>
  <?php endforeach; ?>
</div>