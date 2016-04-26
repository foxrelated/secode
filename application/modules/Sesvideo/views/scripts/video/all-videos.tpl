<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: all-videos.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php $randonNumber = 'sesvideolightbox_123'; ?>
<?php if(!$this->is_ajax){ ?>
<div class="ses_ml_overlay"></div>
<div class="ses_ml_photos_panel_wrapper sesbasic_clearfix sesbasic_bxs" id="ses_ml_photos_panel_wrapper">
  <div class="ses_ml_photos_panel_header">
      <span class="photoscount"><?php echo $this->translate("View All Videos")."(".$this->allVideos->getTotalItemCount().")" ; ?></span>
      <a href="javascript:;" id="close-all-videos" class="photospanel_closebtn">
        <i class="fa fa-close" id="a_btn_btn"></i>
      </a>
  </div>
  <div class="ses_ml_photos_panel_content" style="height:200px;">
      <div id="all-video-container">
      <div class="ses_media_lightbox_all_photo" id="ses_media_lightbox_all_video_id">
<?php } ?>
       <?php foreach($this->allVideos as $valueVideo){ 
       		  if (!$valueVideo instanceof Sesvideo_Model_Video)
            	$valueVideo = Engine_Api::_()->getItem('video',$valueVideo->video_id);
       ?>
          <?php $imageURL = $valueVideo->getHref($this->customParamsArray); ?>
          <a id="video-lightbox-id-<?php echo $valueVideo->video_id; ?>" href="<?php echo $valueVideo->getHref($this->customParamsArray); ?>" class="ses-video-viewer ses_ml_photos_panel_photo_thumb sesvideo_thumb_img">
            <span style="background-image:url(<?php echo $valueVideo->getPhotoUrl(); ?>);"></span>
         </a>
       <?php 
       	} ?>
<?php if(!$this->is_ajax){ ?>
       </div>
      </div>
  </div>
</div>
<?php } ?>
<script type="application/javascript">
viewMoreHide_<?php echo $randonNumber; ?>();
  function viewMoreHide_<?php echo $randonNumber; ?>() {
    canPaginateAllVideo = "<?php echo ($this->allVideos->count() == 0 ? '0' : ($this->allVideos->count() == $this->allVideos->getCurrentPageNumber() ? '0' : '1' ))  ?>";
  }
function <?php echo $randonNumber; ?> (){
    (new Request.HTML({
      method: 'post',
      'url': requestVideosesVideoURL,
      'data': {
        format: 'html',
        page: <?php echo $this->page + 1; ?>,    
				is_ajax : 1,
				identity : '<?php echo $randonNumber; ?>',
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('ses_media_lightbox_all_video_id').innerHTML = document.getElementById('ses_media_lightbox_all_video_id').innerHTML + responseHTML;
				sesJqueryObject('#all-video-container').slimscroll({
					 height: 'auto',
					 alwaysVisible :true,
					 color :'#ffffff',
					 railOpacity :'0.5',
					 disableFadeOut :true,					 
					});
      }
    })).send();
    return false;
  }
</script>