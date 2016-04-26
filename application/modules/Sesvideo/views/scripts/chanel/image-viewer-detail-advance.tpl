<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: image-viewer-detail-advance.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<?php 
        	$taggedUserMap = false;
     if($this->toArray){
        if(!empty($this->previousPhoto))
        	$previousURL = Engine_Api::_()->sesvideo()->getImageViewerHref($this->previousPhoto[0],$this->extraParamsPrevious);
          if(!empty($previousURL))
          $previousImageURL = $this->previousPhoto->getPhotoUrl();
       }else{
        if(!empty($this->previousPhoto))
        	$previousURL = Engine_Api::_()->sesvideo()->getImageViewerHref($this->previousPhoto,$this->extraParamsPrevious);
          if(!empty($previousURL))
          $previousImageURL = $this->previousPhoto->getPhotoUrl();
       }
        if(isset($previousURL)){
        if(isset($this->imagePrivateURL))
        	$previousImageURL = $this->imagePrivateURL;
      ?>
<a class="pswp__button pswp__button--arrow--left" style="display:block" href="<?php echo $this->previousPhoto->getHref(); ?>" title="<?php echo $this->translate('Previous'); ?>" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $previousImageURL; ?>','<?php echo $previousURL; ?>');return false;" id="nav-btn-prev"></a>
<?php }else{ ?>
			<a class="pswp__button pswp__button--arrow--left" style="display:block" href="javascript:;" title="<?php echo $this->translate('Previous'); ?>" id="first-element-btn"></a>
<?php } 
          if($this->toArray){
          if(!empty($this->nextPhoto))
	          $nextURL = Engine_Api::_()->sesvideo()->getImageViewerHref($this->nextPhoto[0],$this->extraParamsNext);
            if(!empty($nextURL))
            $nextImageURL  = $this->nextPhoto->getPhotoUrl();
          
         }else{
          if(!empty($this->nextPhoto))
	          $nextURL = Engine_Api::_()->sesvideo()->getImageViewerHref($this->nextPhoto,$this->extraParamsNext);
            if(!empty($nextURL))
            $nextImageURL  = $this->nextPhoto->getPhotoUrl();
         }
        if(!empty($nextURL)){
        if(isset($this->imagePrivateURL))
        	$nextImageURL = $this->imagePrivateURL;
       ?>
<a class="pswp__button pswp__button--arrow--right" style="display:block" href="<?php echo $this->nextPhoto->getHref(); ?>" title="<?php echo $this->translate('Next'); ?>" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $nextImageURL; ?>','<?php echo $nextURL; ?>');return false;" id="nav-btn-next"></a>
<?php }else{ ?>
		<a class="pswp__button pswp__button--arrow--right" style="display:block" href="javascript:;" title="<?php echo $this->translate('Next'); ?>"  id="last-element-btn"></a>
<?php } ?>
<div class="ses_pswp_information" id="ses_pswp_information">
  <div id="heightOfImageViewerContent">
    <div id="flexcroll" >
      <div class="ses_pswp_info" id="ses_pswp_info">
        <div class="ses_pswp_information_top sesbasic_clearfix">
          <?php $albumUserDetails = Engine_Api::_()->user()->getUser($this->photo->owner_id); ?>
          <div class="ses_pswp_author_photo"> <?php echo $this->htmlLink($albumUserDetails->getHref(), $this->itemPhoto($albumUserDetails, 'thumb.icon')); ?> </div>
          <div class="ses_pswp_author_info"> <span class="ses_pswp_author_name"> <?php echo $this->htmlLink($albumUserDetails->getHref(), $albumUserDetails->getTitle()); ?> </span> <span class="ses_pswp_item_posted_date sesbasic_text_light"> <?php echo date('F j',strtotime($this->photo->creation_date)); ?> </span> </div>
        </div>
        <div class="ses_pswp_item_title" id="ses_title_get"> <?php echo $this->photo->getTitle(); ?></div>
        <div class="ses_pswp_item_description" id="ses_title_description"><?php echo nl2br($this->photo->getDescription()) ?></div>
      <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo_enable_location', 1)){ ?>
        <div class="ses_pswp_location"> <span class="sesbasic_text_light" id="seslocationIn">
          <?php if($this->photo->location != '') echo $this->translate("In"); ?>
          </span> 
          <span>
            <a id="ses_location_data" href="<?php echo $this->url(array("module"=> "sesalbum", "controller" => "photo", "action" => "location", "type" => "location","chanel_id" =>$this->photo->chanel_id, "photo_id" => $this->photo->getIdentity()),'default',true); ?>" onclick="openURLinSmoothBox(this.href);return false;"><?php echo $this->photo->location; ?></a>
         </span>
        </div>
        <?php } ?>
        <?php if($this->canEdit){ ?>
        <div class="ses_pswp_item_edit_link"> <a id="editDetailsLink" href="javascript:void(0)" class="sesalbum_button"> <i class="fa fa-pencil sesbasic_text_light"></i> <?php echo $this->translate('Edit Details'); ?> </a> </div>
        <?php } ?>
      </div>
      <?php if($this->canEdit){ ?>
      <div class="ses_pswp_item_edit_form" id="editDetailsForm" style="display:none;">
        <form id="changePhotoDetails">
          <input  name="title" id="titleSes" type="text" placeholder="<?php echo $this->translate('Title'); ?>" />
          <textarea id="descriptionSes" name="description" value="" placeholder="<?php echo $this->translate('Description'); ?>"></textarea>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo_enable_location', 1)) { ?>
          <input id="locationSes" name="location"  type="text" placeholder="<?php echo $this->translate('Location'); ?>">
          <input type="hidden" id="latSes" name="lat" value="" />
          <input type="hidden" id="lngSes" name="lng" value="" />
       <?php } ?>
          <input type="hidden" id="photo_id_ses" name="photo_id" value="<?php echo $this->photo->chanelphoto_id; ?>" />
          <input type="hidden" id="chanel_id_ses" name="chanel_id" value="<?php echo $this->photo->chanel_id; ?>" />
       <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo_enable_location', 1)) { ?>
          <div id="map-canvas" style="height:200px; margin-bottom:10px;"></div>
       <?php } ?>
          <button id="saveDetailsChanelSes"><?php echo $this->translate('Save Changes'); ?></button>
          <button id="cancelDetailsSes"><?php echo $this->translate('Cancel'); ?></button>
        </form>
      </div>
      <?php } ?>
      <div class="ses_pswp_comments clear"><?php echo $this->action("list", "comment", "core", array("type" => "sesvideo_chanelphoto", "id" => $this->photo->getIdentity())); ?></div>
    </div>
  </div>
</div>
</div>
<div class="pswp__top-bar" style="display:none" id="imageViewerId"> 
	<a title="Close (Esc)" class="pswp__button pswp__button--close"></a> 
  <a title="Toggle fullscreen" onclick="toogle()" href="javascript:;" class="pswp__button"></a><a title="Show Info" id="pswp__button--info-show" class="pswp__button pswp__button--info pswp__button--info-show"></a> 
  <a title="Show All Photos" id="show-all-photo-container" class="pswp__button pswp__button--show-photos"></a>
  <a title="Zoom in/out" id="pswp__button--zoom" class="pswp__button pswp__button--zoom"></a>
  <div class="pswp__top-bar-action">
    <div class="pswp__top-bar-albumname"><?php echo $this->translate('In %1$s', $this->htmlLink( $this->chanel->getHref(), $this->string()->truncate($this->chanel->title,Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.title.truncate',35)))); ?> </div>
    <div class="pswp__top-bar-share">
      <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.add.share',1) == 1){ ?>
      <a href="javascript:;" class='smoothbox' onclick="openURLinSmoothBox('<?php echo $this->url(array("action" => "share", "type" => "sesvideo_chanelphoto", "photo_id" => $this->photo->getIdentity(),"format" => "smoothbox"), 'sesalbum_general', true); ?>')"><?php echo $this->translate('Share'); ?></a>
      <?php } ?>
    </div>
    <div class="pswp__top-bar-more"> <a href="javascript:;" class="optionOpenImageViewer" id="overlay-model-class" class="">Options <i class="fa fa-angle-down" id="overlay-model-class-down"></i></a>
      <div class="pswp__top-bar-more-tooltip" style="display:none">
        <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 && $this->canDelete){ ?>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.add.delete',1) == 1){ ?>
        <a href="javascript:;" class='smoothbox' onclick="openURLinSmoothBox('<?php echo $this->url(array('reset' => false, 'action' => 'delete-photo', 'format' => 'smoothbox')); ?>')"><?php echo $this->translate('Delete'); ?></a>
        <?php } ?>
        <?php } ?>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.add.report',1) == 1 && Engine_Api::_()->user()->getViewer()->getIdentity() != 0){ ?>
        <a href="javascript:;" class='smoothbox' onclick="openURLinSmoothBox('<?php echo $this->url(array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'),'default',true); ?>')"><?php echo $this->translate('Report'); ?></a>
        <?php } ?>
        <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.add.profilepic',1) == 1){ ?>
        <a href="javascript:;" class='smoothbox' onclick="openURLinSmoothBox('<?php echo $this->url(array('route' => 'default', 'controller' => 'index', 'action' => 'external-photo','module'=>'sesbasic', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'),'default',true); ?>')"><?php echo $this->translate('Make Profile Photo'); ?></a>
        <?php } ?>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.add.download',1) == 1 && isset($this->canDownload) && $this->canDownload == 1){ ?>
        <a class="ses-album-photo-download" href="<?php echo $this->url(array('module' => 'sesalbum', 'action' => 'download', 'photo_id' => $this->photo->chanelphoto_id,'type'=>'sesvideo_chanelphoto'), 'sesalbum_general', true); ?>"><?php echo $this->translate('Download'); ?></a>
        <?php } ?>
        <a href="javascript:;" onclick="slideShow()"><?php echo $this->translate("Slideshow"); ?></a> </div>
    </div>
    <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0){ ?>
    <div class="pswp__top-bar-msg pswp__top-bar-btns">      
     <?php if ($this->photo->authorization()->isAllowed($this->viewer, 'comment')){ ?>
      <?php $LikeStatus = Engine_Api::_()->sesalbum()->getLikeStatusPhoto($this->photo->chanelphoto_id,'sesvideo_chanelphoto'); ?>
      <a href="javascript:void(0);" id="sesLightboxLikeUnlikeButton" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn<?php echo $LikeStatus === true ? ' button_active' : '' ;  ?>"><i class="fa fa-thumbs-up"></i><span id="like_unlike_count"><?php echo $this->photo->like_count; ?></span></a>
      <?php } ?>
      <a class="sesbasic_icon_btn sesalbum_icon_msg_btn smoothbox" href="<?php echo $this->url(array('module'=> 'sesalbum', 'controller' => 'index', 'action' => 'message','photo_id' => $this->photo->getIdentity(),'type'=>'sesvideo_chanelphoto' ,'format' => 'smoothbox'),'sesalbum_extended',true); ?>" onclick="openURLinSmoothBox(this.href);return false;"><i class="fa fa-envelope"></i></a> 
    </div>
    <?php } ?>
  </div>
 <div id="media_photo_next_ses" style="display:inline;">
 					<?php if(isset($this->imagePrivateURL)){
          				$imageUrl = $this->imagePrivateURL;
                  $className = 'ses-private-image';
                 }else{
                 	$imageUrl = $this->photo->getPhotoUrl();
                  $className = '';
                  }
          ?>
          <?php echo $this->htmlImage($imageUrl, $this->photo->getTitle(), array(
                'id' => 'gallery-img',
                'class'=>$className
              )); ?>
        </div>
   <div id="sesalbum_photo_id_data_src" data-src="<?php echo $this->photo->chanelphoto_id; ?>" style="display:none;"></div>
  <div class="pswp__preloader">
    <div class="pswp__preloader__icn">
      <div class="pswp__preloader__cut">
        <div class="pswp__preloader__donut"></div>
      </div>
    </div>
  </div>
</div>
<div id="content-from-element" style="display:none;">
<div class="ses_ml_overlay"></div>
<div class="ses_ml_more_popup sesbasic_bxs sesbasic_clearfix">
	<div class="ses_ml_more_popup_header">
  	<span><?php echo $this->translate("You've finished Photos from posts") ?></span>
    <a href="javascript:;" class="morepopup_bkbtn"><i id="morepopup_bkbtn_btn" class="fa fa-repeat"></i></a>
    <a href="javascript:;" class="morepopup_closebtn" id="morepopup_closebtn"><i id="morepopup_closebtn_btn" class="fa fa-close"></i></a>
  </div>
<div id="content_last_element_lightbox"></div>
</div>