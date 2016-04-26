<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
?>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<?php $randonNumber = $this->identity; ?>
<?php 
	$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesvideo/externals/styles/styles.css');
?>
<style>
#sesvideo_heighted_view_<?php echo $randonNumber; ?> .sesvideo_listing_in_grid{
	height:<?php echo str_replace(array('px','%'),'',$this->height).'px'; ?>;
}
#sesvideo_heighted_view_<?php echo $randonNumber; ?> .sesvideo_listing_in_grid:first-child{
	height:<?php echo str_replace(array('px','%'),'',$this->heightmain).'px'; ?>;
}
</style>
<div class="sesvideo_heighted_view sesbasic_clearfix sesbasic_bxs" id="sesvideo_heighted_view_<?php echo $randonNumber; ?>">
  <?php 
  if($this->categoryItem == 'chanels'){
    $allowRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.chanel.rating',1);
      $allowShowPreviousRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.ratechanel.show',1);
      if($allowRating == 0){
        if($allowShowPreviousRating == 0)
          $ratingChanelShow = false;
         else
          $ratingChanelShow = true;
      }else
        $ratingChanelShow = true;
      }else if($this->categoryItem == 'videos'){
      $allowRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.video.rating',1);
      $allowShowPreviousRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.ratevideo.show',1);
      if($allowRating == 0){
        if($allowShowPreviousRating == 0)
          $ratingChanelShow = false;
         else
          $ratingChanelShow = true;
      }else
        $ratingChanelShow = true;
      }else 
        $ratingChanelShow = false;
    foreach( $this->paginator as $item ){
    $photoURL = $item->getPhotoUrl('thumb.main'); ?>
      <div class="sesvideo_listing_in_grid">
        <div class="sesvideo_listing_in_grid_thumb sesvideo_thumb sesvideo_play_btn_wrap">
        <?php 
        $photoURL=$item->getPhotoUrl(); ?>
        <a class="<?php echo $item->getType() == 'video' ? 'sesvideo_thumb_img' : 'sesvideo_thumb_nolightbox' ?>" href="<?php echo $item->getHref(); ?>"> 
          <span style="background-image: url(<?php echo $photoURL; ?>);"></span> 
        </a>
          <?php  if($item->getType() == 'video') { ?>
          <a href="<?php echo $item->getHref()?>" data-url = "<?php echo $item->getType() ?>" class="sesbasic_animation sesvideo_play_btn fa fa-play-circle sesvideo_thumb_img">
            <span style="background-image:url(<?php echo $item->getPhotoUrl(); ?>);display:none;"></span>
          </a>  
          <?php } ?>
          <?php if(isset($this->featured) || isset($this->sponsored) || isset($this->hot)){ ?>
            <p class="sesvideo_labels sesbasic_animation">
            <?php if(isset($this->featured) && $item->is_featured == 1){ ?>
              <span class="sesvideo_label_featured"><?php echo $this->translate('FEATURED'); ?></span>
            <?php } ?>
            <?php if(isset($this->sponsored)  && $item->is_sponsored == 1){ ?>
              <span class="sesvideo_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
            <?php } ?>
           <?php if(isset($this->hot)  && $item->is_hot == 1){ ?>
              <span class="sesvideo_label_hot"><?php echo $this->translate("HOT"); ?></span>
            <?php } ?>
            </p>
           <?php } ?>
           <?php if(isset($this->duration) && isset($item->duration) && $item->duration ): ?>
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
           <?php endif ?>
           <?php if(isset($this->watchlater) && (isset($item->watchlater_id)) && Engine_Api::_()->user()->getViewer()->getIdentity() != '0'){ ?>
              <?php $watchLaterId = $item->watchlater_id; ?>
              <a href="javascript:;" class="sesvideo_watch_later_btn sesbasic_animation sesvideo_watch_later <?php echo !is_null($watchLaterId)  ? 'selectedWatchlater' : '' ?>" title = "<?php echo !is_null($watchLaterId)  ? $this->translate('Remove from Watch Later') : $this->translate('Add to Watch Later') ?>" data-url="<?php echo $item->video_id ; ?>"></a>
          <?php } ?>
          <?php 
            if(isset($this->socialSharing) || isset($this->likeButton) || isset($this->favouriteButton)){
            //basic viewpage link for sharing
            $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $item->getHref()); ?>
            <span class="sesvideo_thumb_btns">
              <?php if(isset($this->socialSharing)){ ?>
                <a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $item->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesbasic_icon_btn sesbasic_icon_facebook_btn"><i class="fa fa-facebook"></i></a>
                <a href="<?php echo 'http://twitthis.com/twit?url=' . $urlencode . '&title=' . $item->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesbasic_icon_btn sesbasic_icon_twitter_btn"><i class="fa fa-twitter"></i></a>
                <a href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($item->getPhotoUrl('thumb.main'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : $item->getPhotoUrl('thumb.main')) . $item->getPhotoUrl('thumb.main')); ?>&description=<?php echo $item->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesbasic_icon_btn sesbasic_icon_pintrest_btn"><i class="fa fa-pinterest"></i></a>
                <?php } 
                  if(isset($item->chanel_id)){
                    $itemtype = 'sesvideo_chanel';
                    $getId = 'chanel_id';
                  }else if($item->video_id){
                    $itemtype = 'sesvideo_video';
                    $getId = 'video_id';
                  }else{
                    $itemtype = 'sesvideo_playlist';
                    $getId = 'playlist_id';
                  }
                  $canComment =  $item->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
                  if(isset($this->likeButton) && Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && $canComment){
                ?>
                <?php $LikeStatus = Engine_Api::_()->sesvideo()->getLikeStatusVideo($item->$getId,$item->getType()); ?>
                 <a href="javascript:;" data-url="<?php echo $item->$getId ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesvideo_like_<?php echo $itemtype; ?><?php echo ($LikeStatus) ? ' button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $item->like_count; ?></span></a>
                  <?php }
                  if(Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && isset($this->favouriteButton)){
                  $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesvideo')->isFavourite(array('resource_type'=>$itemtype,'resource_id'=>$item->$getId)); ?>
                    <a href="javascript:;" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesvideo_favourite_<?php echo $itemtype; ?><?php echo ($favStatus)  ? ' button_active' : '' ?>"  data-url="<?php echo $item->$getId ; ?>"><i class="fa fa-heart"></i><span><?php echo $item->favourite_count; ?></span></a>
                  <?php } ?>
            </span>
          <?php } ?>
          </div>
            <?php if(isset($this->like) || isset($this->comment) || isset($this->view) || isset($this->title) || isset($this->rating) || isset($this->favouriteCount) || isset($this->downloadCount)  || isset($this->by) || isset($this->videoCount)){ ?>
              <div class="sesvideo_listing_in_grid_info clear sesbasic_animation">
                <?php if(isset($this->title)) { ?>
                  <div class="sesvideo_listing_in_grid_title">
                    <?php echo $this->htmlLink($item, $this->htmlLink($item, $this->string()->truncate($item->getTitle(), $this->title_truncation), array('title'=>$item->getTitle()))) ?>
                  </div>
                <?php } ?>
                <?php if(isset($this->by)) { ?>
                  <div class="sesvideo_listing_in_grid_date sesvideo_list_stats sesbasic_text_light floatL">
                    <span>
                      <i class="fa fa-user"></i>
                      <?php echo $this->translate('By');?>
                      <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
                    </span>
                    </div>
                <?php }?>
              
                <div class="sesvideo_listing_in_grid_date sesvideo_list_stats sesbasic_text_light clear floatL">
                  <?php if(isset($this->like)) { ?>
                    <span class="sesbasic_list_grid_likes" title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count))?>">
                      <i class="fa fa-thumbs-up"></i>
                      <?php echo $item->like_count;?>
                    </span>
                  <?php } ?>
                <?php if(isset($this->comment)) { ?>
                  <span class="sesbasic_list_grid_comment" title="<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count))?>">
                    <i class="fa fa-comment"></i>
                    <?php echo $item->comment_count;?>
                  </span>
               <?php } ?>
               <?php if(isset($this->view)) { ?>
                <span class="sesbasic_list_grid_views" title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count))?>">
                  <i class="fa fa-eye"></i>
                  <?php echo $item->view_count;?>
                </span>
               <?php } ?>
               <?php if(isset($this->favouriteCount)) { ?>
                  <span class="sesbasic_list_grid_fav" title="<?php echo $this->translate(array('%s favourite', '%s favourites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count))?>">
                    <i class="fa fa-heart"></i> 
                    <?php echo $item->favourite_count;?>            
                  </span>
                <?php } ?>
                <?php if(isset($this->videoCount)) { ?>
               <?php if($item->getType() == 'sesvideo_chanel'){ 
                $videoCount = $item->total_videos;
                }else{ 
                $videoCount = $item->countVideos();
                 } ?>
              <span class="sesbasic_list_grid_download" title="<?php echo $this->translate(array('%s video', '%s videos', $videoCount), $this->locale()->toNumber($videoCount))?>">
                <i class="fa fa-video-camera"></i> 
                <?php echo $videoCount;?>            
              </span>
            <?php } ?>
              <?php if(isset($this->rating) && $ratingChanelShow && $item->rating > 0) { ?>
                    <span  title="<?php echo $this->translate(array('%s rating', '%s ratings', round($item->rating,1)), $this->locale()->toNumber(round($item->rating,1)))?>">
               <i class="fa fa-star"></i><?php echo round($item->rating,1).'/5';?>
              </span>
                <?php } ?>
            </div>
          </div>         
        <?php } ?>
      </div>      
  <?php
  } ?>
</div>
