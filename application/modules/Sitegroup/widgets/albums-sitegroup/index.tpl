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
<?php
$i = 0;
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<div class="sitegroupalbum_sidebar">
  <div class="sitegroupalbum_sidebar_header">
    <?php if (count($this->paginator) > 1) : ?>		
      <span> <?php echo $this->translate('2 of ') ?>  
        <?php echo $this->htmlLink($this->sitegroup->getHref(array('tab'=>$this->tab_id)),$this->translate(array('%s album', '%s albums', $this->albumcount), $this->locale()->toNumber($this->albumcount))) ?>
      </span>
    <?php else : ?>
     <span><?php echo $this->htmlLink($this->sitegroup->getHref(array('tab'=>$this->tab_id)),$this->translate(array('%s album', '%s albums', $this->albumcount), $this->locale()->toNumber($this->albumcount))) ?></span>
    <?php endif; ?>
    <span><?php echo $this->htmlLink($this->sitegroup->getHref(array('tab'=>$this->tab_id)),$this->translate('See All')) ?></span>
  </div>
  <?php if (count($this->paginator) > 0) : ?>
    <ul>
      <?php foreach ($this->paginator as $albums): ?>
        <li> 
          <?php if ($albums->photo_id != 0): ?>
            <a href="<?php echo $this->url(array('action' => 'view', /* 'owner_id' => $image->user_id, */'group_id' => $this->sitegroup->group_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug(), 'tab' => $this->tab_id), 'sitegroup_albumphoto_general') ?>" title="<?php echo $albums->title; ?>">
              <?php echo $this->itemPhoto($albums, 'thumb.icon'); ?>
            </a>
          <?php else: ?>
            <a href="<?php echo $this->url(array('action' => 'view', /* 'owner_id' => $image->user_id, */'group_id' => $this->sitegroup->group_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug(), 'tab' => $this->tab_id), 'sitegroup_albumphoto_general') ?>"  title="<?php echo $albums->title; ?>" >
              <?php echo $this->itemPhoto($albums, 'thumb.icon'); ?>
            </a>
          <?php endif; ?>
          <div class="sitegroupalbum_info">
            <div class="sitegroupalbum_title">
              <?php
              $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.truncation.limit', 13);
              $tmpBody = strip_tags($albums->getTitle());
              $item_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
              ?>	  
              <a href="<?php echo $this->url(array('action' => 'view', /* 'owner_id' => $image->user_id, */'group_id' => $this->sitegroup->group_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug(), 'tab' => $this->tab_id), 'sitegroup_albumphoto_general') ?>" title="<?php echo $albums->title; ?>">	        
                <?php echo $item_title; ?></a>
            </div>
            <div class="sitegroupalbum_details">
              <?php echo $this->timestamp($albums->creation_date) ?>
            </div>	
          </div>
        </li>      
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
  <?php if (count($this->paginators) > 0) : ?>
    <div class="sitegroupalbum_sidebar_header">
      <span><?php echo $this->locale()->toNumber(count($this->paginators)); ?> <?php echo $this->translate('of'); ?> <a href="<?php echo $this->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($this->sitegroup->group_id), 'tab' => $this->tab_id), 'sitegroup_entry_view', true); ?>"><?php echo $this->totalphotosothers ?> <?php echo $this->translate('photos by others'); ?></a></span>
      <span><a href="<?php echo $this->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($this->sitegroup->group_id), 'tab' => $this->tab_id), 'sitegroup_entry_view', true); ?>"><?php echo $this->translate('See All'); ?></a></span>
    </div>
    <ul class="sitegroupalbum_sidebar_thumbs">
      <?php foreach ($this->paginators as $photo): ?>
        <li>
          <?php //if (!$this->showLightBox): ?>
<!--            <a href="javascript:void(0)" onclick='ShowPhotoGroup("<?php //echo $photo->getHref() ?>")' title="<?php //echo $photo->title; ?>"  class="thumbs_photo">		
              <span style="background-image: url(<?php //echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>	
            </a>-->
       
            <a href="<?php echo $photo->getHref() ?>" <?php if(SEA_SITEGROUPALBUM_LIGHTBOX) :?> onclick="openSeaocoreLightBox('<?php echo $photo->getHref() . '/type/creation_date' .  '/count/'. $this->totalphotosothers. '/offset/' . $i. '/owner_id/' . $this->viewer_id;  ?>');return false;" <?php endif;?> title="<?php echo $photo->title; ?>" class="thumbs_photo">  
              <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
            </a>
          <?php //endif; ?>
        </li> 
        <?php $i++;?>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
<?php //if ($this->showLightBox): ?>
  <?php
  	//include APPLICATION_PATH . '/application/modules/Sitegroupalbum/views/scripts/_lightboxImageAlbum.tpl';
  ?>
<?php //endif; ?>