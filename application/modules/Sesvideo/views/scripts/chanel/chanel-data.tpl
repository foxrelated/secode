<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: chanel-data.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
	<?php //rating show code
            $allowRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.chanel.rating',1);
            $allowShowPreviousRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.ratechanel.show',1);
            if($allowRating == 0){
              if($allowShowPreviousRating == 0)
                $ratingChanelShow = false;
               else
                $ratingChanelShow = true;
            }else
              $ratingChanelShow = true;
       ?>  
  	 <?php if(empty($this->resultArray['chanel_data'][0])){ die($this->translate('No channel found with this id.'));}
      $chanelData = $this->resultArray['chanel_data'][0];?>
      <div class="sesvideo_browse_channel_item sesbasic_clearfix clear">
        <?php if(isset($this->chanelPhotoActive)){ ?>
          <div class="sesvideo_browse_channel_items_photo floatL">
          	<?php 
            	 $urlThumbChanel = $chanelData->getPhotoUrl();
            ?>
          	<a class="sesvideo_thumb_img" data-url = "<?php echo $chanelData->getType() ?>" href="<?php echo $chanelData->getHref(); ?>">
            	<span style="background-image:url(<?php echo $urlThumbChanel; ?>);"></span>
            </a>
           <?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive) || isset($this->hotLabelActive)){ ?>
              <p class="sesvideo_labels">
              <?php if(isset($this->featuredLabelActive) && $chanelData->is_featured == 1){ ?>
                <span class="sesvideo_label_featured"><?php echo $this->translate('FEATURED'); ?></span>
              <?php } ?>
              <?php if(isset($this->sponsoredLabelActive) && $chanelData->is_sponsored == 1){ ?>
                <span class="sesvideo_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
              <?php } ?>
              <?php if(isset($this->hotLabelActive) && $chanelData->is_hot == 1){ ?>
                <span class="sesvideo_label_hot"><?php echo $this->translate("HOT"); ?></span>
              <?php } ?>
              </p>
             <?php } ?>
          </div>
        <?php } ?>
          <div class="sesvideo_browse_channel_items_cont">
            <div class="sesvideo_browse_channel_items_title">
            <?php if(isset($this->titleActive)){ ?>
            	<?php 
              		if(strlen($chanelData->title)>$this->title_truncation)
                  	$titleChanel = mb_substr($chanelData->title,0,$this->title_truncation).'...';
                  else
              			$titleChanel = $chanelData->title; 
              ?>
            	<a href="<?php echo $chanelData->getHref(); ?>"><?php echo $titleChanel ?></a>
             <?php } ?>
             <?php if(isset($this->verifiedActive) && $chanelData->is_verified){ ?>
              	<i class="sesvideo_verified fa fa-check-square" title="<?php echo $this->translate('Verified') ?>"></i>
              <?php  } ?>
              <span class="sesvideo_browse_channel_items_btns floatR">
                 <?php if(isset($this->socialSharingActive)){ ?>
                 <?php $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $chanelData->getHref()); ?>
                  <a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $chanelData->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesbasic_icon_btn sesbasic_icon_facebook_btn"><i class="fa fa-facebook"></i></a>
                  <a href="<?php echo 'http://twitthis.com/twit?url=' . $urlencode . '&title=' . $chanelData->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesbasic_icon_btn sesbasic_icon_twitter_btn"><i class="fa fa-twitter"></i></a>
                  <a href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($chanelData->getPhotoUrl(),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'].$chanelData->getPhotoUrl() ) : $chanelData->getPhotoUrl())); ?>&description=<?php echo $chanelData->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesbasic_icon_btn sesbasic_icon_pintrest_btn"><i class="fa fa-pinterest"></i></a>
                  <?php } ?>
              <?php if(isset($chanelData->follow) && $chanelData->follow == 1 && isset($this->followButtonActive) && Engine_Api::_()->user()->getViewer()->getIdentity() != '0' && Engine_Api::_()->getApi('settings', 'core')->getSetting('video.enable.subscription',1)){ ?>
              <?php  $followbutton =  Engine_Api::_()->getDbtable('chanelfollows', 'sesvideo')->checkFollow(Engine_Api::_()->user()->getViewer()->getIdentity(),$chanelData->chanel_id); ?>
              <a href="javascript:;" data-url="<?php echo $chanelData->chanel_id ; ?>" class="sesbasic_icon_btn sesvideo_chanel_follow sesbasic_icon_btn_count sesbasic_icon_follow_btn <?php echo ($followbutton)  ? 'button_active' : '' ?>"> <i class="fa fa-check"></i><span><?php echo $chanelData->follow_count; ?></span></a>
              <?php } ?>
            <?php
              $canComment =  $chanelData->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
              if(isset($this->likeButtonActive) && Engine_Api::_()->user()->getViewer()->getIdentity() != 0 && $canComment){
            ?>
                <!--Like Button-->
                <?php $LikeStatus = Engine_Api::_()->sesvideo()->getLikeStatusVideo($chanelData->chanel_id,$chanelData->getType()); ?>
               <a href="javascript:;" data-url="<?php echo $chanelData->chanel_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesvideo_like_sesvideo_chanel <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $chanelData->like_count; ?></span></a>
              <?php } ?>
              <?php if(isset($this->favouriteButtonActive) && isset($chanelData->favourite_count) && Engine_Api::_()->user()->getViewer()->getIdentity() != '0'){ ?>            	
              <?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesvideo')->isFavourite(array('resource_type'=>'sesvideo_chanel','resource_id'=>$chanelData->chanel_id)); ?>
             <a href="javascript:;" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesvideo_favourite_sesvideo_chanel <?php echo ($favStatus)  ? 'button_active' : '' ?>"  data-url="<?php echo $chanelData->chanel_id ; ?>"><i class="fa fa-heart"></i><span><?php echo $chanelData->favourite_count; ?></span></a>
            <?php } ?>
    		 </span>
        	</div>
            <div class="sesvideo_browse_channel_item_stat sesbasic_text_light sesvideo_list_stats sesbasic_clearfix"> 
            	<?php if(isset($this->byActive)){ ?>
              <span>
              	<?php 
              	$owner = $chanelData->getOwner();
           		  echo $this->translate('Posted by %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle()));
                ?>
              </span>
              <?php } ?>
              <?php if(isset($this->videoCountActive)){ ?>
            	<span title="<?php echo $this->translate(array('%s video', '%s videos', $chanelData->total_videos), $this->locale()->toNumber($chanelData->total_videos)); ?>">
              	<i class="fa fa-video-camera"></i> <?php echo $chanelData->total_videos ; ?>
              </span>
              <?php } ?>
              <?php if(isset($this->likeActive) && isset($chanelData->like_count)) { ?>
                <span title="<?php echo $this->translate(array('%s like', '%s likes', $chanelData->like_count), $this->locale()->toNumber($chanelData->like_count)); ?>"><i class="fa fa-thumbs-up"></i><?php echo $chanelData->like_count; ?></span>
              <?php } ?>
              <?php if(isset($this->photoActive) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesalbum')) { ?>
              <?php $photos = $chanelData->count(); ?>
                <span title="<?php echo $this->translate(array('%s photo', '%s photos', $photos), $this->locale()->toNumber($photos)); ?>"><i class="fa fa-photo"></i><?php echo $photos; ?></span>
              <?php } ?>
              <?php if(isset($this->commentActive) && isset($chanelData->comment_count)) { ?>
                <span title="<?php echo $this->translate(array('%s comment', '%s comments', $chanelData->comment_count), $this->locale()->toNumber($chanelData->comment_count))?>"><i class="fa fa-comment"></i><?php echo $chanelData->comment_count;?></span>
              <?php } ?>
              <?php if(isset($this->favouriteActive)){ ?>
            	<span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $chanelData->favourite_count), $this->locale()->toNumber($chanelData->favourite_count)); ?>">
              	<i class="fa fa-heart"></i> <?php echo $chanelData->favourite_count ; ?>
              </span>
              <?php } ?>
              <?php if(isset($chanelData->follow) && $chanelData->follow == 1 && isset($this->followActive) && Engine_Api::_()->getApi('settings', 'core')->getSetting('video.enable.subscription',1)){ ?>
              <span title="<?php echo $this->translate(array('%s follow', '%s follows', $chanelData->follow_videos), $this->locale()->toNumber($chanelData->follow_videos)); ?>">
              	<i class="fa fa-users"></i> <?php echo $chanelData->follow_videos ; ?>
              </span>
              <?php } ?>
               <?php if(isset($this->ratingActive) && $ratingChanelShow && isset($chanelData->rating) && $chanelData->rating > 0 ): ?>
              <span  title="<?php echo $this->translate(array('%s rating', '%s ratings', round($chanelData->rating,1)), $this->locale()->toNumber(round($chanelData->rating,1)))?>">
               <i class="fa fa-star"></i><?php echo round($chanelData->rating,1).'/5';?>
              </span>
            <?php endif; ?>
            </div>
						<div class="sesvideo_browse_channel_item_cont_btm sesbasic_custom_scroll">
              <?php if(isset($this->descriptionActive)){ ?>
                <div class="sesvideo_list_des clear">
                <?php if(strlen($chanelData->description) > $this->description_truncation){
                          $description = mb_substr(strip_tags($chanelData->description),0,$this->description_truncation).'...';
                         }else{ ?>
                  <?php $description = $chanelData->description; ?>
                    <?php } ?>
                    <?php echo $description; ?>
                </div>
              <?php } ?>
             	<?php if(isset($this->resultArray['videos'])){ ?>
                <div class="sesvideo_list_channel_videos_listing clear sesbasic_clearfix">
                 <?php foreach($this->resultArray['videos'] as $videoData){ ?>
                 <?php
                    $href = $videoData->getHref(array('type'=>'sesvideo_chanel','item_id'=>$chanelData->getIdentity()));
                    $imageURL = $videoData->getPhotoUrl();
                  ?>
                  <div class="sesvideo_listing_grid" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
                    <div class="sesvideo_grid_thumb sesvideo_thumb"> 
                      <a href="<?php echo $href; ?>"  class="sesvideo_thumb_img"> 
                      <span style="background-image:url(<?php echo $imageURL; ?>);"></span> </a> 
                      <?php if(isset($this->durationActive)){ ?>
                      <?php
                        if( $videoData->duration >= 3600 ) {
                          $duration = gmdate("H:i:s", $videoData->duration);
                        } else {
                          $duration = gmdate("i:s", $videoData->duration);
                        }
                      ?>
                        <span class="sesvideo_length"><?php echo $duration; ?></span> 
                      <?php } ?>
                  <?php if(isset($this->watchLaterActive)){ ?>
                  	<?php if(isset($videoData->watchlater_id) && Engine_Api::_()->user()->getViewer()->getIdentity() != '0'){ ?>
                      <a href="javascript:;" class="sesvideo_watch_later_btn sesvideo_watch_later <?php echo !is_null($videoData->watchlater_id)  ? 'selectedWatchlater' : '' ?>" title = "<?php echo !is_null($videoData->watchlater_id)  ? $this->translate('Remove from Watch Later') : $this->translate('Add to Watch Later') ?>" data-url="<?php echo $videoData->video_id ; ?>"></a>
                    <?php } ?>
                  <?php } ?>
                     </div>
                  </div>
                 <?php } ?>
            		</div>
          		<?php } ?>
						</div>
          </div>
        </div>