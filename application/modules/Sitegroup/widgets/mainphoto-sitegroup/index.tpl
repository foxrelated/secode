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

<?php if (!empty($this->sitegroup->sponsored)): ?>
  <?php $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.image', 1);
  if (!empty($sponsored)) { ?>
    <div class="sitegroup_profile_sponsorfeatured" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.color', '#fc0505'); ?>;'>
      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/star-img.png', '') ?>
      <?php echo $this->translate('SPONSORED'); ?>
      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/star-img.png', '') ?>
    </div>
  <?php } ?>
<?php endif; ?>
<div class='sitegroup_photo <?php if ($this->can_edit) : ?>sitegroup_photo_edit_wrapper<?php endif; ?>'>
  <?php if (!empty($this->can_edit)) : ?>
    <a href="<?php echo $this->url(array('action' => 'profile-picture', 'group_id' => $this->sitegroup->group_id), 'sitegroup_dashboard', true) ?>" class="sitegroup_photo_edit">  	  
      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/edit_pencil.png', '') ?>
      <?php echo $this->translate('Change Picture'); ?>
    </a>
  <?php endif; ?>

  <?php echo $this->itemPhoto($this->sitegroup, 'thumb.profile', '', array('align' => 'left')); ?>
</div>
<?php if (!empty($this->sitegroup->featured)): ?>
  <?php $feature = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.feature.image', 1);
  if (!empty($feature)) { ?>
    <div class="sitegroup_profile_sponsorfeatured"  style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.featured.color', '#0cf523'); ?>;'>
      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/star-img.png', '') ?>
      <?php echo $this->translate('FEATURED'); ?>
    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/star-img.png', '') ?>
    </div>
  <?php } ?>
<?php endif; ?>