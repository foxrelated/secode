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
<?php if(isset($this->identityForWidget) && !empty($this->identityForWidget)){
				$randonNumber = $this->identityForWidget;
      }else{
      	$randonNumber = $this->identity; 
      }
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
<?php if(!$this->is_ajax){ ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesvideo/externals/styles/styles.css'); ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/customscrollbar.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/customscrollbar.concat.min.js'); ?>
	<div id="scrollHeightDivSes_<?php echo $randonNumber; ?>" class="sesbasic_clearfix sesbasic_bxs clear">  
<?php } ?>
  <?php foreach( $this->paginatorCategory as $item): ?>
  	<?php if($this->view_channel_type == 1){ ?>
    <div class="sesvideo_browse_channel_listing clear sesbasic_clearfix">
    	<div class="sesvideo_browse_channel_listing_title clear sesbasic_clearfix">
      	<h3>
          <a href="<?php echo $item->getBrowseCategoryHref(); ?>?category_id=<?php echo $item->category_id ?>" title="<?php echo $item->category_name; ?>">
            <?php echo $item->category_name; ?>
            <?php if(isset($this->count_chanel)){ ?>
            <?php echo "(".$item->total_chanels_categories.")"; ?>
            <?php } ?>
         </a>
       </h3>
         <?php if(isset($this->seemore_text) && $this->seemore_text != ''){ ?>
          <span <?php echo $this->allignment_seeall == 'right' ?  'class="floatR"' : ''; ?> >
          	<a href="<?php echo $item->getBrowseCategoryHref(); ?>?category_id=<?php echo $item->category_id ?>" title="<?php echo $item->category_name; ?>">
            <?php echo str_replace('[category_name]',$item->category_name,$this->seemore_text); ?>
          </a>
         </span>
        <?php } ?>
      </div>
    <?php if(isset($this->resultArray['chanel_data'][$item->category_id])){ ?>
    	<div class="sesvideo_browse_channel_listing_thumbnails clear sesbasic_clearfix">
     <?php 
     			$counter = 1;
     			foreach($this->resultArray['chanel_data'][$item->category_id] as $itemChanel){ ?>
          <?php if($counter == 1)
          			$chanelData = $itemChanel;          			
            $urlThumb = $itemChanel->getPhotoUrl();
           ?>
      	<div class="<?php echo $counter == 1 ? 'thumbnail_active' : '' ?>" <?php if(empty($this->chanelThumbnailActive)) { ?> style="display:none;" <?php } ?>>
					<a href="<?php echo $itemChanel->getHref(); ?>" data-url="<?php echo $itemChanel->chanel_id ?>" class="slideshow_chanel_data">
          	<img src="<?php echo $urlThumb; ?>" alt="<?php echo $itemChanel->title ?>" class="thumb_icon item_photo_user  thumb_icon">
          </a>
        </div>
      	<?php 
        	$counter++;
        } ?>
      </div>      
      <?php } ?>
      <?php if(isset($chanelData) && $chanelData != ''){ ?>
      <div class="sesvideo_browse_channel_conatiner clear sesbasic_clearfix sesbm">
        <div class="sesvideo_browse_channel_item sesbasic_clearfix clear">
        <?php if(isset($this->chanelPhotoActive)){ ?>
          <div class="sesvideo_browse_channel_items_photo floatL">
          	<?php 
            	 $urlThumbChanel = $chanelData->getPhotoUrl();
            ?>
          	<a href="<?php echo $chanelData->getHref(); ?>">
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
                          $description = mb_substr($chanelData->description,0,$this->description_truncation).'...';
                         }else{ ?>
                  <?php $description = $chanelData->description; ?>
                    <?php } ?>
                    <?php echo $description; ?>
                </div>
              <?php } ?>
             	<?php if(isset($this->resultArray['videos'][$item->category_id])){ ?>
                <div class="sesvideo_list_channel_videos_listing clear sesbasic_clearfix">
                 <?php foreach($this->resultArray['videos'][$item->category_id] as $videoData){ ?>
                 <?php
                    $href = $videoData->getHref(array('type'=>'sesvideo_chanel','item_id'=>$chanelData->getIdentity()));
                    $imageURL = $videoData->getPhotoUrl();
                  ?>
                  <div class="sesvideo_listing_grid" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
                    <div class="sesvideo_grid_thumb sesvideo_thumb"> 
                      <a href="<?php echo $href; ?>" class="sesvideo_thumb_img">
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
      <?php for($i=2;$i<$counter;$i++){ ?>
      		<div class="sesvideo_browse_channel_item sesbasic_clearfix clear nodata" style="display:none;"></div>
      <?php } ?>
      	<?php if($counter>2){ ?>
        <div class="sesvideo_browse_channel_btns">
        	<a href="javascript:;" class="prevbtn seschanel_slideshow_prev"><i class="fa fa-angle-left sesbasic_text_light"></i></a>
          <a href="javascript:;" class="nxtbtn seschanel_slideshow_next"><i class="fa fa-angle-right sesbasic_text_light"></i></a>
        </div>
        <?php } ?>
      </div>
			<?php } ?>
    </div>   
    <?php } else{ ?>
      <div class="sesvideo_channel_list sesbasic_bxs sesbasic_clearfix sesbm">
        <div class="sesvideo_channel_list_top" style="height:<?php echo $this->height.'px' ?>">
          <a href="<?php echo $item->getHref(); ?>" class="sesvideo_channel_list_img"><img src="<?php echo $item->getPhotoUrl(); ?>" /></a>
        </div>
        <div class="sesvideo_channel_list_btm">
          <div class="sesvideo_channel_list_title"><a href="<?php echo $item->getHref(); ?>"><?php echo $item->getTitle(); ?></a></div>
          <div class="sesvideo_channel_list_stats sesbasic_text_light"><?php echo $this->translate(array('%s video', '%s videos', $item->total_videos), $this->locale()->toNumber($item->total_videos)); ?>  / <?php echo $this->translate(array('%s follow', '%s followers', $item->follow_videos), $this->locale()->toNumber($item->follow_videos)); ?>  </div>
          <div class="sesvideo_channel_list_des">
           <?php if(isset($this->descriptionActive)){ ?>
              <?php if(strlen($item->description) > $this->description_truncation){
                       $description = mb_substr($item->description,0,$this->description_truncation).'...';
                    }else{ ?>
                <?php $description = $item->description; ?>
                <?php } ?>
                <?php echo $description; ?>
            <?php } ?>
          </div> 
        </div>
      </div>
    <?php }
 		$chanelData = '';
 		endforeach;
     if($this->paginatorCategory->getTotalItemCount() == 0 ){  ?>
     <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has created a chanel yet.');?>
        <?php if ($this->can_create):?>
          <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action' => 'create','module'=>'sesvideo'), "sesvideo_chanel",true).'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
		<?php } 
    if($this->loadOptionData == 'pagging'){ ?>
 		 <?php echo $this->paginationControl($this->paginatorCategory, null, array("_pagging.tpl", "sesvideo"),array('identityWidget'=>$randonNumber)); ?>
 <?php } ?>
 <?php if(!$this->is_ajax){ ?>
  </div>
  	<?php if($this->loadOptionData != 'pagging'){ ?>
    <div class="sesbasic_view_more" id="view_more_<?php echo $randonNumber; ?>" onclick="viewMore_<?php echo $randonNumber; ?>();" > <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => "feed_viewmore_link_$randonNumber", 'class' => 'buttonlink icon_viewmore')); ?> </div>
  <div class="sesbasic_view_more_loading" id="loading_image_<?php echo $randonNumber; ?>" style="display: none;"> <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sesbasic/externals/images/loading.gif" /> </div>
  <?php } ?>
  <?php } ?>
  
<script type="text/javascript">
var params<?php echo $randonNumber; ?> = '<?php echo json_encode($this->params); ?>';
var identity<?php echo $randonNumber; ?>  = '<?php echo $randonNumber; ?>';
var page<?php echo $randonNumber; ?> = '<?php echo $this->page + 1; ?>';
var searchParams<?php echo $randonNumber; ?> ;
 <?php if(!$this->is_ajax){ ?>
sesJqueryObject(document).on('click','.seschanel_slideshow_prev',function(e){
		e.preventDefault();
		var activeClassIndex;
		var elem = sesJqueryObject(this).parent().parent().parent().find('.sesvideo_browse_channel_listing_thumbnails').children();
		var elemLength = elem.length;
		for(i=0;i<elemLength;i++){
			if(elem[i].hasClass('thumbnail_active')){
				 activeClassIndex = i;
				break;	
			}
		}
		if(activeClassIndex == 0){
			var changeIndex = elemLength-1;
		}else if((activeClassIndex+1) == elemLength){
			var changeIndex =activeClassIndex-1 ;	
		}else{
			var changeIndex = activeClassIndex-1; 	
		}
		sesJqueryObject(this).parent().parent().parent().find('.sesvideo_browse_channel_listing_thumbnails').children().eq(changeIndex).find('a').click();
});
sesJqueryObject(document).on('click','.seschanel_slideshow_next',function(e){
	e.preventDefault();
	var activeClassIndex;
	var elem = sesJqueryObject(this).parent().parent().parent().find('.sesvideo_browse_channel_listing_thumbnails').children();
	var elemLength = elem.length;
	for(i=0;i<elemLength;i++){
		if(elem[i].hasClass('thumbnail_active')){
			 activeClassIndex = i;
			break;	
		}
	}
	if((activeClassIndex+1) == elemLength){
		var changeIndex = 0;	
	}else if(activeClassIndex == 0){
		var changeIndex = activeClassIndex+1;
	}else{
		var changeIndex = activeClassIndex+1; 	
	}
	sesJqueryObject(this).parent().parent().parent().find('.sesvideo_browse_channel_listing_thumbnails').children().eq(changeIndex).find('a').click();
});
sesJqueryObject(document).on('click','.slideshow_chanel_data',function(e){
	e.preventDefault();
	var chanel_id = sesJqueryObject(this).attr('data-url');
	if(sesJqueryObject(this).parent().hasClass('thumbnail_active')){
			return false;
	}
	if(!chanel_id)
		return false;
	 
	 var elIndex = sesJqueryObject(this).parent().index();
	 var totalDiv = sesJqueryObject(this).parent().parent().find('div');
	 for(i=0;i<totalDiv.length;i++){
			 totalDiv[i].removeClass('thumbnail_active');
	 }
	 sesJqueryObject(this).parent().addClass('thumbnail_active');
	 var containerElem = sesJqueryObject(this).parent().parent().parent().find('.sesvideo_browse_channel_conatiner').children();
	 for(i=0;i<containerElem.length;i++){
	 	if(i != (containerElem.length-1))
			containerElem[i].hide();
	 }
	 sesJqueryObject(containerElem).get(elIndex).show();
	if(sesJqueryObject(containerElem).get(elIndex).hasClass('nodata')){
	 sesJqueryObject(containerElem).eq(elIndex).html('<div class="sesbasic_loading_cont_overlay"></div>');
	 new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + "sesvideo/chanel/chanel-data/chanel_id/"+chanel_id,
      'data': {
        format: 'html',
				params:'<?php echo json_encode($this->params); ?>',
				chanel_id : sesJqueryObject(this).attr('data-url'),
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        sesJqueryObject(containerElem).eq(elIndex).html(responseHTML);
				sesJqueryObject(containerElem).eq(elIndex).removeClass('nodata');
				jqueryObjectOfSes(".sesbasic_custom_scroll").mCustomScrollbar({
          theme:"minimal-dark"
        });
      }
    }).send();
	}
});
var valueTabData ;
// globally define available tab array
	var availableTabs_<?php echo $randonNumber; ?>;
	var requestTab_<?php echo $randonNumber; ?>;
  availableTabs_<?php echo $randonNumber; ?> = <?php echo json_encode($this->defaultOptions); ?>;
<?php if($this->loadOptionData == 'auto_load'){ ?>
		window.addEvent('load', function() {
		 sesJqueryObject(window).scroll( function() {
			  var heightOfContentDiv_<?php echo $randonNumber; ?> = sesJqueryObject('#scrollHeightDivSes_<?php echo $randonNumber; ?>').offset().top;
        var fromtop_<?php echo $randonNumber; ?> = sesJqueryObject(this).scrollTop();
        if(fromtop_<?php echo $randonNumber; ?> > heightOfContentDiv_<?php echo $randonNumber; ?> - 100 && sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').css('display') == 'block' ){
						document.getElementById('feed_viewmore_link_<?php echo $randonNumber; ?>').click();
				}
     });
	});
<?php } ?>
<?php if($this->loadOptionData == 'pagging'){ ?>
function paggingNumber<?php echo $randonNumber; ?>(pageNum){
	 sesJqueryObject('.sesbasic_loading_cont_overlay').css('display','block');
   (new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + "widget/index/mod/sesvideo/name/<?php echo $this->widgetName; ?>",
      'data': {
        format: 'html',
        page: pageNum,    
				params :params<?php echo $randonNumber; ?>, 
				is_ajax : 1,
				searchParams:searchParams<?php echo $randonNumber; ?> ,
				identity : identity<?php echo $randonNumber; ?> ,
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				if($('loadingimgsesvideo-wrapper'))
						sesJqueryObject('#loadingimgsesvideo-wrapper').hide();
				sesJqueryObject('.sesbasic_loading_cont_overlay').css('display','none');
        document.getElementById('scrollHeightDivSes_<?php echo $randonNumber; ?>').innerHTML =  responseHTML;
				jqueryObjectOfSes(".sesbasic_custom_scroll").mCustomScrollbar({
          theme:"minimal-dark"
        });
      }
    })).send();
    return false;
}
<?php } ?>
<?php } ?>
</script>
<script type="text/javascript">
var defaultOpenTab ;
  viewMoreHide_<?php echo $randonNumber; ?>();
  function viewMoreHide_<?php echo $randonNumber; ?>() {
    if ($('view_more_<?php echo $randonNumber; ?>'))
      $('view_more_<?php echo $randonNumber; ?>').style.display = "<?php echo ($this->paginatorCategory->count() == 0 ? 'none' : ($this->paginatorCategory->count() == $this->paginatorCategory->getCurrentPageNumber() ? 'none' : '' )) ?>";
  }
  function viewMore_<?php echo $randonNumber; ?> (){
    document.getElementById('view_more_<?php echo $randonNumber; ?>').style.display = 'none';
    document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = '';    
    en4.core.request.send(new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + "widget/index/mod/sesvideo/name/<?php echo $this->widgetName; ?>/",
      'data': {
        format: 'html',
        page: page<?php echo $randonNumber; ?>,    
				params :params<?php echo $randonNumber; ?>, 
				is_ajax : 1,
				searchParams:searchParams<?php echo $randonNumber; ?> ,
				identity : identity<?php echo $randonNumber; ?> ,
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				if($('loadingimgsesvideo-wrapper'))
						sesJqueryObject('#loadingimgsesvideo-wrapper').hide();
        document.getElementById('scrollHeightDivSes_<?php echo $randonNumber; ?>').innerHTML = document.getElementById('scrollHeightDivSes_<?php echo $randonNumber; ?>').innerHTML + responseHTML;
				document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = 'none';
				jqueryObjectOfSes(".sesbasic_custom_scroll").mCustomScrollbar({
          theme:"minimal-dark"
        });
      }
    }));
    return false;
  }
</script>