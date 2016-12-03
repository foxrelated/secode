<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-main-photo.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
$viewerId = Engine_Api::_()->user()->getViewer()->getIdentity() ;
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/scripts/core.js'); ?>
<div class="seaocore_profile_cover_head_section_inner" id="seaocore_profile_cover_head_section_inner">

  <div class="seaocore_profile_coverinfo_status">
    <?php if (!empty($this->showContent) && in_array('title', $this->showContent)): ?>
      <?php if (empty($this->cover_photo_preview)): ?>
        <h2><?php echo $this->subject()->getTitle(); ?></h2>
      <?php else: ?>
        <h2><?php
          $getShortType = ucfirst($this->subject()->getShortType());
          echo $this->translate("%s Title", $getShortType)
          ?></h2>
      <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($this->showContent) && in_array('owner', $this->showContent)): ?>
    <div class="siteevent_listings_stats">
      <div class="o_hidden f_small">
      <?php if (!empty($this->cover_photo_preview)): ?>
        <div class="mtop5"><?php echo $this->translate("Owner Name"); ?></div>
      <?php else: ?>
        <div class="mtop5"><?php echo $this->translate('By %1$s', $this->subject->getOwner()->__toString()); ?></div>
      <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
		
    <div class="seaocore_profile_cover_info">
      <?php
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1) && !empty($this->showContent) && in_array('CategoryLink', $this->showContent) && $this->subject->category_id) :
        $categoryName = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getCategoryName($this->subject->category_id);
        ?>
        <div class="seao_listings_stats">
          <div class="o_hidden"><i class="seao_icon_strip seao_icon seao_icon_category" title="<?php echo $this->translate('Category') ?>"></i>
            <a href="<?php echo $this->url(array('category_id' => $this->subject->category_id, 'categoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $this->subject->category_id)->getCategorySlug()), 'sitevideo_general_category', true) ?>">
              <span><?php echo $categoryName; ?></span>
            </a> 
          </div>
        </div>
      <?php endif; ?>
      
      <?php
      if (!empty($this->showContent) && in_array('creationDate', $this->showContent)) {
        echo '<div class="seao_listings_stats"><div class="o_hidden"><i class="seao_icon_strip seao_icon seao_icon_time" title="' . $this->translate("Creation Date") . '"></i>' . $this->timestamp($this->subject->creation_date) . '</div></div>';
      }
      ?>
  
      <?php
      if (!empty($this->showContent) && in_array('updateDate', $this->showContent)) {
        echo '<div class="seao_listings_stats"><div class="o_hidden"><i class="seao_icon_strip seao_icon seao_icon_edit" title="' . $this->translate('Update Date') . '"></i>' . $this->translate('Updated about %1$s', $this->timestamp($this->subject->modified_date)) . '</div></div>';
      }
      ?>
  
      <?php if (!empty($this->showContent) && in_array('description', $this->showContent)): ?>
  
        <div class="mtop10">
          <?php
          echo Engine_Api::_()->seaocore()->seaocoreTruncateText($this->subject->description, 50);
          if (Engine_String::strlen($this->subject->description) > 50):
            ?>
            <span class="sitealbum_profile_desc_link" title="<?php echo $this->subject->description ?>"> <?php echo $this->translate('See more') ?> </span>
          
        <?php endif; ?></div>
      <?php endif; ?>
  
      <?php if (!empty($this->showContent) && in_array('rating', $this->showContent)): ?>
        <?php echo $this->content()->renderWidget("sitevideo.user-ratings"); ?>
      <?php endif; ?>
    </div>
  </div>

  <?php if (($this->profile_like_button == 1) || (in_array('optionsButton', $this->showContent) || in_array('viewCount', $this->showContent) || in_array('likeCount', $this->showContent) || in_array('commentCount', $this->showContent) || in_array('totalVideos', $this->showContent))): ?>
    <?php if(!$this->contentFullWidth):?>
    <div class="seaocore_profile_coverinfo_buttons">
      <?php if (!empty($this->showContent) && isset($this->subject()->videos_count) && is_array($this->showContent) && in_array('totalVideos', $this->showContent)): ?>
        <div class="seaocore_profile_coverinfo_statistics">
          <span><?php echo $this->subject()->videos_count; ?></span>  
          <div><?php if(empty($this->subject()->videos_count)){ echo $this->translate("Photos");  } else { echo ($this->subject()->videos_count > 1) ? $this->translate("Videos") : $this->translate("Video"); } ?></div>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($this->showContent) && in_array('subscribe', $this->showContent)): ?>
        <div class="seaocore_profile_coverinfo_statistics">
          <span><?php echo $this->subject()->subscribe_count; ?></span>  
          <div><?php if(empty($this->subject()->subscribe_count)){ echo $this->translate("Subscribers");  } else { echo ($this->subject()->subscribe_count > 1) ? $this->translate("Subscribers") : $this->translate("Subscriber"); } ?></div>
        </div>
      <?php endif; ?>

      <?php if (!empty($this->showContent) && in_array('likeCount', $this->showContent)): ?>
        <div class="seaocore_profile_coverinfo_statistics">
          <span><?php echo $this->subject()->like_count; ?></span>  
          <div><?php if(empty($this->subject()->like_count)){ echo $this->translate("Likes");  } else { echo ($this->subject()->like_count > 1) ? $this->translate("Likes") : $this->translate("Like"); } ?></div>
        </div>
      <?php endif; ?>

      <?php if (!empty($this->showContent) && in_array('commentCount', $this->showContent)): ?>
        <div class="seaocore_profile_coverinfo_statistics">
          <span><?php echo $this->subject()->comment_count; ?></span>  
          <div> <?php if(empty($this->subject()->comment_count)){ echo $this->translate("Comments");  } else { echo ($this->subject()->comment_count > 1) ? $this->translate("Comments") : $this->translate("Comment"); } ?></div>
        </div>
      <?php endif; ?>

      <?php if ($this->profile_like_button == 1) : ?>
        <div>
          <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike')): ?>
            <?php echo $this->content()->renderWidget("sitelike.commoncover-like-button"); ?>
          <?php else: ?>
            <?php echo $this->content()->renderWidget("seaocore.like-button"); ?>
          <?php endif; ?>
        </div>	
      <?php endif; ?>
      <?php if ($this->viewer()->getIdentity() && !empty($this->showContent) && in_array('subscribe', $this->showContent)): ?>        
        <div>
            <?php $this->shareLinks($this->subject(), array('subscribe'),true);?>
        </div>	
      <?php endif; ?>
      <?php 
        if (is_array($this->showContent) && in_array('shareOptions', $this->showContent)) {
            $this->subject = $this->subject();
            include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_shareCoverPhotoButtons.tpl';
        }
       ?> 
     <?php 
        if (is_array($this->showContent) && in_array('uploadVideos', $this->showContent) && $viewerId && ($viewerId==$this->subject()->owner_id)) {?>
        <div> 
           <?php echo $this->content()->renderWidget("sitevideo.upload-video-sitevideo", array('upload_button' => 0, 'upload_button_title' => $this->translate("Upload Videos"))); ?>
        </div>
         <?php }
       ?>
      <?php if (!empty($this->showContent) && in_array('optionsButton', $this->showContent)): ?>
        <?php $this->navigationProfile = $coreMenus->getNavigation("channel_profile"); ?>
        <?php if (count($this->navigationProfile) > 0): ?>
          <div class="seaocore_button seaocore_profile_option_btn prelative">
            <a href="javascript:void(0);" onclick="showPulDownOptions();"><i class="icon_cog"></i><i class="icon_down"></i></a>
            <ul class="seaocore_profile_options_pulldown" id="sitecontent_cover_settings_options_pulldown" style="display:none;right:0;">
              <li>
                <?php echo $this->navigation()->menu()->setContainer($this->navigationProfile)->setPartial(array('_navIcons.tpl', 'sitevideo'))->render(); ?>
              </li>
            </ul>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <?php else:?>
     <div class="seaocore_profile_coverinfo_buttons">
      <?php if (!empty($this->showContent) && isset($this->subject()->videos_count) && is_array($this->showContent) && in_array('totalVideos', $this->showContent)): ?>
        <div class="seaocore_profile_coverinfo_statistics">
          <div><span><?php echo $this->subject()->videos_count; ?></span> <?php if(empty($this->subject()->photos_count)){ echo $this->translate("Videos");  } else { echo ($this->subject()->photos_count > 1) ? $this->translate("Videos") : $this->translate("Video"); } ?></div>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($this->showContent) && in_array('subscribe', $this->showContent)): ?>
        <div class="seaocore_profile_coverinfo_statistics">
          <div><span><?php echo $this->subject()->subscribe_count; ?></span> <?php if(empty($this->subject()->subscribe_count)){ echo $this->translate("Subscribers");  } else { echo ($this->subject()->subscribe_count > 1) ? $this->translate("Subscribers") : $this->translate("Subscriber"); } ?></div>
        </div>
      <?php endif; ?>

      <?php if (!empty($this->showContent) && in_array('likeCount', $this->showContent)): ?>
        <div class="seaocore_profile_coverinfo_statistics">
          <div><span><?php echo $this->subject()->like_count; ?></span> <?php if(empty($this->subject()->like_count)){ echo $this->translate("Likes");  } else { echo ($this->subject()->like_count > 1) ? $this->translate("Likes") : $this->translate("Like"); } ?></div>
        </div>
      <?php endif; ?>

      <?php if (!empty($this->showContent) && in_array('commentCount', $this->showContent)): ?>
        <div class="seaocore_profile_coverinfo_statistics"> 
          <div><span><?php echo $this->subject()->comment_count; ?></span> <?php if(empty($this->subject()->comment_count)){ echo $this->translate("Comments");  } else { echo ($this->subject()->comment_count > 1) ? $this->translate("Comments") : $this->translate("Comment"); } ?></div>
        </div>
      <?php endif; ?>

			<br /> <br />
      <?php if ($this->profile_like_button == 1) : ?>
        <div>
          <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike')): ?>
            <?php echo $this->content()->renderWidget("sitelike.commoncover-like-button"); ?>
          <?php else: ?>
            <?php echo $this->content()->renderWidget("seaocore.like-button"); ?>
          <?php endif; ?>
        </div>	
      <?php endif; ?>
        <?php if ($this->viewer()->getIdentity() && !empty($this->showContent) && in_array('subscribe', $this->showContent)): ?>        
        <div>
            <?php $this->shareLinks($this->subject(), array('subscribe'),true);?>
        </div>	
      <?php endif; ?>
      <?php 
        if (is_array($this->showContent) && in_array('shareOptions', $this->showContent)) {
            $this->subject = $this->subject();
            include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_shareCoverPhotoButtons.tpl';
        }
       ?>  
      <?php 
        if (is_array($this->showContent) && in_array('uploadVideos', $this->showContent)  && $viewerId && ($viewerId==$this->subject()->owner_id)) {?>
       <div> 
          <?php echo $this->content()->renderWidget("sitevideo.upload-video-sitevideo", array('upload_button' => 0, 'upload_button_title' => $this->translate("Upload Videos"))); ?>
       </div>
         <?php }
       ?>                
      <?php if (!empty($this->showContent) && in_array('optionsButton', $this->showContent)): ?>
        <?php $this->navigationProfile = $coreMenus->getNavigation("channel_profile"); ?>
        <?php if (count($this->navigationProfile) > 0): ?>
          <div class="seaocore_button seaocore_profile_option_btn prelative">
            <a href="javascript:void(0);" onclick="showPulDownOptions();"><i class="icon_cog"></i><i class="icon_down"></i></a>
            <ul class="seaocore_profile_options_pulldown" id="sitecontent_cover_settings_options_pulldown" style="display:none;right:0;">
              <li>
                <?php echo $this->navigation()->menu()->setContainer($this->navigationProfile)->setPartial(array('_navIcons.tpl', 'sitevideo'))->render(); ?>
              </li>
            </ul>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
	<?php endif; ?>
  <?php $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse'); ?>
  <?php if ($fbmodule && !empty($fbmodule->enabled) && ($this->profile_like_button == 2)) : ?>
    <div class="seaocore_profile_cover_fb_like_button"> 
      <?php echo $this->content()->renderWidget("Facebookse.facebookse-commonlike", array('subject' => $this->subject()->getGuid())); ?>
    </div>	
  <?php endif; ?>
</div>

<style>
  .seaocore_profile_coverinfo_status,.seaocore_profile_coverinfo_status h2, .seaocore_profile_coverinfo_status a, .seaocore_profile_coverinfo_status div, .seaocore_profile_coverinfo_statistics, .seaocore_profile_coverinfo_statistics div{
    color:<?php echo $this->fontcolor; ?> !important;
  }
</style>
