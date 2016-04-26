<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: viewalbum.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
  include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/Adintegration.tpl';
?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitegroupalbum/externals/styles/style_sitegroupalbum.css');
include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<div class="sitegroup_viewgroups_head">
  <?php echo $this->htmlLink(array('route' => 'sitegroup_entry_view', 'group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($this->sitegroup->group_id)), $this->itemPhoto($this->sitegroup, 'thumb.icon', '', array('align' => 'left'))) ?>
  <h2><?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($this->sitegroup->group_id, $this->sitegroup->owner_id, $this->sitegroup->getSlug()), $this->sitegroup->getTitle()) ?>
    <?php echo $this->translate('&raquo; '); ?>
    <?php echo $this->htmlLink(array('route' => 'sitegroup_entry_view', 'group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($this->sitegroup->group_id), 'tab' => $this->tab_selected_id), $this->translate('Albums')) ?>
  </h2>
</div>
<!--RIGHT AD START HERE-->
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumview', 3) && $group_communityad_integration && Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup)): ?>
  <div class="layout_right" id="communityad_viewalbum">
    <?php echo $this->content()->renderWidget("sitegroup.group-ads", array('limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumview', 3), 'tab' => 'viewalbum', 'communityadid' => 'communityad_viewalbum', 'isajax' => 0)); ?>
  </div>
<?php endif; ?>
<!--RIGHT AD END HERE-->

<div class="layout_middle">
  <?php if (count($this->album) > 0) : ?>
    <div class="sitegroup_album_box">
      <ul class="thumbs">
        <?php foreach ($this->album as $albums): ?>
          <li style="height:200px;">
            <?php if ($albums->photo_id != 0): ?>
              <a href="<?php echo $this->url(array('action' => 'view', 'group_id' => $this->sitegroup->group_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug()), 'sitegroup_albumphoto_general') ?>" class="thumbs_photo" title="<?php echo $albums->title; ?>">
                <span style="background-image: url(<?php echo $albums->getPhotoUrl('thumb.normal'); ?>);"></span>
              </a>
            <?php else: ?>
              <a href="<?php echo $this->url(array('action' => 'view', 'group_id' => $this->sitegroup->group_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug()), 'sitegroup_albumphoto_general') ?>" class="thumbs_photo"  title="<?php echo $albums->title; ?>">
                <span><?php echo $this->itemPhoto($albums, 'thumb.normal') ?></span>
              </a>
            <?php endif; ?>
            <div class="sitegroup_profile_album_title">
              <a href="<?php echo $this->url(array('action' => 'view', 'group_id' => $this->sitegroup->group_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug()), 'sitegroup_albumphoto_general') ?>" title="<?php echo $albums->title; ?>"><?php echo $albums->title; ?></a>
            </div>
            <div class="sitegroup_profile_album_stat">
              <?php echo $this->translate(array('%s photo', '%s photos', $albums->count()), $this->locale()->toNumber($albums->count())) ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
</div>