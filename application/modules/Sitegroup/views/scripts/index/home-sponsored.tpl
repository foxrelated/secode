<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: homesponsored.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postedby', 1);?>
<?php if ($this->direction == 1) { ?>
  <?php foreach ($this->sitegroups as $sitegroup): ?>
    <div class="SlideItMoo_element seaocore_sponsored_carousel_items">
      <div class="seaocore_sponsored_carousel_items_thumb">
        <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id, $sitegroup->getSlug()), $this->itemPhoto($sitegroup, 'thumb.icon', $sitegroup->getTitle()), array('rel' => 'lightbox[galerie]', 'class' => "thumb_icon")) ?>
      </div>
      <div class="seaocore_sponsored_carousel_items_info">
        <div class="seaocore_sponsored_carousel_items_title">
          <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id, $sitegroup->getSlug()), Engine_Api::_()->sitegroup()->truncation($sitegroup->getTitle(), $this->titletruncation), array('title' => $sitegroup->getTitle())) ?>
        </div>
        <?php if($postedBy):?>
          <div class="seaocore_sponsored_carousel_items_stat seaocore_txt_light">
            <?php echo $this->translate('created by'); ?>
            <?php echo $this->htmlLink($sitegroup->getOwner()->getHref(), Engine_Api::_()->sitegroup()->truncation($sitegroup->getOwner()->getTitle(), 10), array('title' => $sitegroup->getOwner()->getTitle())) ?>
          </div>
        <?php endif;?>
      </div>
    </div>	 
  <?php endforeach; ?>
<?php } else { ?>
  <?php $count = $this->totalgroups; ?>
  <?php for ($i = $count; $i < $this->count; $i++): ?>
    <div class="SlideItMoo_element seaocore_sponsored_carousel_items">
      <div class="seaocore_sponsored_carousel_items_thumb">
        <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($this->sitegroups[$i]->group_id, $this->sitegroups[$i]->owner_id, $this->sitegroups[$i]->getSlug()), $this->itemPhoto($this->sitegroups[$i], 'thumb.icon', $this->sitegroups[$i]->getTitle()), array('rel' => 'lightbox[galerie]', 'class' => "thumb_icon")) ?>
      </div>
      <div class="seaocore_sponsored_carousel_items_info">
        <div class="seaocore_sponsored_carousel_items_title">
          <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($this->sitegroups[$i]->group_id, $this->sitegroups[$i]->owner_id, $this->sitegroups[$i]->getSlug()), Engine_Api::_()->sitegroup()->truncation($this->sitegroups[$i]->getTitle(), $this->titletruncation), array('title' => $this->sitegroups[$i]->getTitle())) ?>
        </div>      
        <?php if($postedBy):?>
          <div class="seaocore_sponsored_carousel_items_stat seaocore_txt_light">
            <?php echo $this->translate('created by'); ?>
            <?php echo $this->htmlLink($this->sitegroups[$i]->getOwner()->getHref(), Engine_Api::_()->sitegroup()->truncation($this->sitegroups[$i]->getOwner()->getTitle(), 10), array('title' => $this->sitegroups[$i]->getOwner()->getTitle())) ?>
          </div>
        <?php endif;?>
      </div>
    </div>	 
  <?php endfor; ?>
  <?php for ($i = 0; $i < $count; $i++): ?>
    <div class="SlideItMoo_element seaocore_sponsored_carousel_items">
      <div class="seaocore_sponsored_carousel_items_thumb">
        <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($this->sitegroups[$i]->group_id, $this->sitegroups[$i]->owner_id, $this->sitegroups[$i]->getSlug()), $this->itemPhoto($this->sitegroups[$i], 'thumb.icon', $this->sitegroups[$i]->getTitle()), array('rel' => 'lightbox[galerie]', 'class' => "thumb_icon")) ?>
      </div>
      <div class="seaocore_sponsored_carousel_items_info">
        <div class="seaocore_sponsored_carousel_items_title">
          <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($this->sitegroups[$i]->group_id, $this->sitegroups[$i]->owner_id, $this->sitegroups[$i]->getSlug()), Engine_Api::_()->sitegroup()->truncation($this->sitegroups[$i]->getTitle(), $this->titletruncation), array('title' => $this->sitegroups[$i]->getTitle())) ?>
        </div> 
        <?php if($postedBy):?>
          <div class="seaocore_sponsored_carousel_items_stat seaocore_txt_light">
            <?php echo $this->translate('created by'); ?>
            <?php echo $this->htmlLink($this->sitegroups[$i]->getOwner()->getHref(), Engine_Api::_()->sitegroup()->truncation($this->sitegroups[$i]->getOwner()->getTitle(), 10), array('title' => $this->sitegroups[$i]->getOwner()->getTitle())) ?>
          </div>
        <?php endif;?>
      </div>
    </div>
  <?php endfor; ?>
<?php } ?>