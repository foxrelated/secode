<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: manage-videos.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php if( $this->countItem > 0 ): ?>
  <?php foreach( $this->paginator as $item ):
  			if(isset($this->getVideoItem) && $this->getVideoItem == 'getVideoItem'){
         $item = Engine_Api::_()->getItem('video', $item->resource_id);
        }else if(isset($this->getVideoWatch))
        	$item = Engine_Api::_()->getItem('video', $item->video_id);
  ?>
   <li id="videoId-<?php echo $item->video_id; ?>" class="sesvideo_channel_create_videoslist sesbasic_bxs">
      	<div class="sesvideo_grid_thumb sesvideo_thumb">
         <?php
            $imageURL = $item->getPhotoUrl();
            $class = isset($this->editChanel) ?  'selected-manage-video' : 'add-video-manage';
          ?>
					<a href="javascript:;" data-url="<?php  echo $item->video_id;?>" class="sesvideo_thumb_nolightbox <?php echo $class; ?>"> 
              <span style="background-image:url(<?php echo $imageURL; ?>);"></span> 
          </a>
        <?php if( $item->duration ){ ?>
        <span class="sesvideo_length">
          <?php
            if( $item->duration >= 3600 ) {
              $duration = gmdate("H:i:s", $item->duration);
            } else {
              $duration = gmdate("i:s", $item->duration);
            }
            echo $duration;
          ?>
        </span>
        <?php }else{ ?> 
        	<span class="sesvideo_length"></span>
        <?php } 
        if(isset($this->editChanel)):  ?>
        <span class="delete_selected_video"><a href="javascript:;" class="delete-selected"><i class='fa fa-close'></i></a></a></span>
        <?php endif; ?>
        </div>
    </li>
<?php endforeach; ?>
<?php if(!(isset($this->editChanel))){ ?>
<?php echo $this->paginationControl($this->paginator, null, array("_pagging.tpl", "sesvideo"),array('identityWidget'=>$this->identityWidget)); ?>
<?php } ?>
<?php endif;

	if(!(isset($this->editChanel)) && $this->paginator->getTotalItemCount() == 0 ){ ?>
			<div class="tip">
        <span>
          <?php echo $this->translate("There are currently no %s",$this->typeSearch);?>
        </span>
			</div>	
<?php } 
if(!$this->is_ajax && !(isset($this->editChanel))){
 ?>
<script type="application/javascript">
function paggingNumberaddChanel(pageNum){
	 sesJqueryObject('.sesbasic_loading_cont_overlay').css('display','block');   
    en4.core.request.send(new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + "sesvideo/chanel/manage-videos",
      'data': {
        format: 'html',
        page: pageNum,
				data:sesJqueryObject('#manage_videos').val(),
				is_ajax:true,
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				sesJqueryObject('.sesbasic_loading_cont_overlay').css('display','none');
        document.getElementById('manage_videos_data').innerHTML =  responseHTML;
				disableSelectedVideos();
      }
    }));
    return false;
}
</script>
<?php }die; ?>