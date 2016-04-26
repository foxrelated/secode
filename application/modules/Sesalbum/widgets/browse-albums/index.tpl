<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesalbum/externals/styles/styles.css'); ?> 
<?php if(isset($this->identityForWidget) && !empty($this->identityForWidget)){
				$randonNumber = $this->identityForWidget;
      }else{
      	$randonNumber = $this->identity; 
       }?>
 <div class="sesalbum_search_result sesbasic_clearfix sesbm" id="<?php echo !$this->is_ajax ? 'paginator_count_sesalbum' : 'paginator_count_ajax_sesalbum' ?>"><span id="total_item_count_sesalbum" style="display:inline-block;"><?php echo $this->paginator->getTotalItemCount(); ?></span> <?php echo $this->translate("albums found."); ?></div>
 <?php if(!$this->is_ajax){ ?>
  <div id="scrollHeightDivSes_<?php echo $randonNumber; ?>">
    <ul class="sesalbum_listings sesalbum_browse_album_listings sesbasic_bxs sesbasic_clearfix" id="tabbed-widget_<?php echo $randonNumber; ?>">
 <?php }
 				 $allowRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.album.rating',1);
					$allowShowPreviousRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.ratealbum.show',1);
          if($allowRating == 0){
          	if($allowShowPreviousRating == 0)
            	$ratingShow = false;
             else
             	$ratingShow = true;
          }else
          	$ratingShow = true;
      	 foreach( $this->paginator as $album ): ?>
      <?php if($this->view_type == 1){ ?>
        <li id="thumbs-photo-<?php echo $album->photo_id ?>" class="sesalbum_list_grid_thumb sesalbum_list_grid sesa-i-<?php echo (isset($this->insideOutside) && $this->insideOutside == 'outside') ? 'outside' : 'inside'; ?> sesa-i-<?php echo (isset($this->fixHover) && $this->fixHover == 'fix') ? 'fix' : 'over'; ?> sesbm" style="width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">  
          <a class="sesalbum_list_grid_img" href="<?php echo Engine_Api::_()->sesalbum()->getHref($album->getIdentity(),$album->album_id); ?>" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;">
            <span class="main_image_container" style="background-image: url(<?php echo $album->getPhotoUrl('thumb.normalmain'); ?>);"></span>
            <div class="ses_image_container" style="display:none;">
              <?php $image = Engine_Api::_()->sesalbum()->getAlbumPhoto($album->getIdentity(),$album->photo_id); 
                foreach($image as $key=>$valuePhoto){?>
                 <div class="child_image_container"><?php echo Engine_Api::_()->sesalbum()->photoUrlGet($valuePhoto->photo_id,'thumb.normalmain');  ?></div>
              <?php  }  ?>  
             	<div class="child_image_container"><?php echo $album->getPhotoUrl('thumb.normalmain'); ?></div>          
            </div>
          </a>
          <?php  if(isset($this->socialSharing) || isset($this->likeButton) || isset($this->favouriteButton)){  ?>
            <span class="sesalbum_list_grid_btns">
             <?php if(isset($this->socialSharing)){ 
              //album viewpage link for sharing
                $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $album->getHref());
             ?>
              <a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $album->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesalbum_icon_btn sesalbum_icon_facebook_btn"><i class="fa fa-facebook"></i></a>
              <a href="<?php echo 'http://twitthis.com/twit?url=' . $urlencode . '&title=' . $album->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesalbum_icon_btn sesalbum_icon_twitter_btn"><i class="fa fa-twitter"></i></a>
              <a href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($album->getPhotoUrl('thumb.main'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : $album->getPhotoUrl('thumb.main')) . $album->getPhotoUrl('thumb.main')); ?>&description=<?php echo $album->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesalbum_icon_btn sesalbum_icon_pintrest_btn"><i class="fa fa-pinterest"></i></a>
              <?php }
              $canComment = $album->authorization()->isAllowed($this->viewer, 'comment');
              if(Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && isset($this->likeButton) && $canComment){  ?>
                <!--Album Like Button-->
                <?php $albumLikeStatus = Engine_Api::_()->sesalbum()->getLikeStatus($album->album_id); ?>
                <a href="javascript:;" data-src='<?php echo $album->album_id; ?>' class="sesalbum_icon_btn sesalbum_icon_like_btn sesalbum_albumlike <?php echo ($albumLikeStatus) ? 'button_active' : '' ; ?>">
                  <i class="fa fa-thumbs-up"></i>
                  <span><?php echo $album->like_count; ?></span>
                </a>
              <?php } 
              if(Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && isset($this->favouriteButton)){
                    $albumFavStatus = Engine_Api::_()->getDbtable('favourites', 'sesalbum')->isFavourite(array('resource_type'=>'album','resource_id'=>$album->album_id)); ?>
                <a href="javascript:;" data-src='<?php echo $album->album_id; ?>' class="sesalbum_icon_btn sesalbum_icon_fav_btn sesalbum_albumFav <?php echo ($albumFavStatus) ? 'button_active' : '' ; ?>">
                  <i class="fa fa-heart"></i>
                  <span><?php echo $album->favourite_count; ?></span>
                </a>
            <?php } ?>
          	</span>
         	<?php } ?>
          <?php if(isset($this->featured) || isset($this->sponsored)){ ?>
          	<span class="sesalbum_labels_container">
              <?php if(isset($this->featured) && $album->is_featured == 1){ ?>
                <span class="sesalbum_label_featured"><?php echo $this->translate("Featured"); ?></span>
              <?php } ?>
            <?php if(isset($this->sponsored)  && $album->is_sponsored == 1){ ?>
            	<span class="sesalbum_label_sponsored"><?php echo $this->translate("Sponsored"); ?></span>
            <?php } ?>
          </span>
         <?php } ?>
         <?php if(isset($this->like) || isset($this->comment) || isset($this->view) || isset($this->title) || isset($this->rating) || isset($this->photoCount) || isset($this->favouriteCount) || isset($this->downloadCount)  || isset($this->by)){ ?>
              <p class="sesalbum_list_grid_info sesbasic_clearfix<?php if(!isset($this->photoCount)) { ?> nophotoscount<?php } ?>">
              <?php if(isset($this->title)) { ?>
                <span class="sesalbum_list_grid_title">
                  <?php echo $this->htmlLink($album, $this->string()->truncate($album->getTitle(), $this->title_truncation),array('title'=>$album->getTitle())) ; ?>
                </span>
              <?php } ?>
              <span class="sesalbum_list_grid_stats">
                <?php if(isset($this->by)) { ?>
                  <span class="sesalbum_list_grid_owner">
                    <?php echo $this->translate('By');?>
                   <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
                  </span>
                <?php }?>
               	<?php if(isset($this->rating) && $ratingShow) { ?>
                <?php
                          $user_rate = Engine_Api::_()->getDbtable('ratings', 'sesalbum')->getCountUserRate('album',$album->album_id);
                          $textuserRating = $user_rate == 1 ? 'user' : 'users'; 
                          $textRatingText = $album->rating == 1 ? 'rating' : 'ratings'; ?>
                       <span class="sesalbum_list_grid_rating" title="<?php echo $album->rating.' '.$this->translate($textRatingText.' by').' '.$user_rate.' '.$this->translate($textuserRating); ?>">
                    <?php if( $album->rating > 0 ): ?>
                      <?php for( $x=1; $x<= $album->rating; $x++ ): ?>
                      	<span class="sesbasic_rating_star_small fa fa-star"></span>
                      <?php endfor; ?>
                      <?php if( (round($album->rating) - $album->rating) > 0): ?>
                        <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                      <?php endif; ?>
                    <?php endif; ?> 
                  </span>
                <?php } ?>
              </span>
              <span class="sesalbum_list_grid_stats sesbasic_text_light">
                <?php if(isset($this->like)) { ?>
                  <span class="sesalbum_list_grid_likes" title="<?php echo $this->translate(array('%s like', '%s likes', $album->like_count), $this->locale()->toNumber($album->like_count))?>">
                    <i class="fa fa-thumbs-up"></i>
                    <?php echo $album->like_count;?>
                  </span>
                <?php } ?>
                <?php if(isset($this->comment)) { ?>
                  <span class="sesalbum_list_grid_comment" title="<?php echo $this->translate(array('%s comment', '%s comments', $album->comment_count), $this->locale()->toNumber($album->comment_count))?>">
                    <i class="fa fa-comment"></i>
                    <?php echo $album->comment_count;?>
                  </span>
               <?php } ?>
               <?php if(isset($this->view)) { ?>
                  <span class="sesalbum_list_grid_views" title="<?php echo $this->translate(array('%s view', '%s views', $album->view_count), $this->locale()->toNumber($album->view_count))?>">
                    <i class="fa fa-eye"></i>
                    <?php echo $album->view_count;?>
                  </span>
               <?php } ?>
               <?php if(isset($this->favouriteCount)) { ?>
                  <span class="sesalbum_list_grid_fav" title="<?php echo $this->translate(array('%s favourite', '%s favourites', $album->favourite_count), $this->locale()->toNumber($album->favourite_count))?>">
                    <i class="fa fa-heart"></i> 
                    <?php echo $album->favourite_count;?>            
                  </span>
                <?php } ?>
                <?php if(isset($this->downloadCount)) { ?>
                <span class="sesalbum_list_grid_download" title="<?php echo $this->translate(array('%s download', '%s downloads', $album->download_count), $this->locale()->toNumber($album->download_count))?>">
                  <i class="fa fa-download"></i> 
                  <?php echo $album->download_count;?>            
                </span>
              <?php } ?>
                 <?php if(isset($this->photoCount)) { ?>
               	<span class="sesalbum_list_grid_count" title="<?php echo $this->translate(array('%s photo', '%s photos', $album->count()), $this->locale()->toNumber($album->count()))?>" >
                  <i class="fa fa-photo"></i> 
                  <?php echo $album->count();?>                
               	</span>
                <?php } ?>
                </span>
              </p>
         <?php } ?>
         		<?php if(isset($this->photoCount)) { ?>
              <p class="sesalbum_list_grid_count">
                <?php echo $this->translate(array('%s <span>photo</span>', '%s <span>photos</span>', $album->count()),$this->locale()->toNumber($album->count())); ?>
              </p>
              <?php } ?>
            </li>
      <?php }else{ ?>
      		<?php $image = Engine_Api::_()->sesalbum()->getAlbumPhoto($album->getIdentity(),$album->photo_id,3);  ?>
          <?php if(count($image) == 0) {
          	$heightDiv = (str_replace('px','',$this->height)) + 100;
          }else{
          	$heightDiv = $this->height;
          } ?>
          <?php $imageURL = Engine_Api::_()->sesalbum()->getImageViewerHref($album); ?>
          <li class="sesalbum_list_thumbnail_view" style="width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
            <div class="sesalbum_album_thumbnail_view_main_img" style="height:<?php echo is_numeric($heightDiv) ? $heightDiv.'px' : $heightDiv ?>;">
              <a class="ses-image-viewer" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $album->getPhotoUrl(); ?>','<?php echo $imageURL	; ?>')" href="<?php echo Engine_Api::_()->sesalbum()->getHrefPhoto($album->photo_id,$album->album_id); ?>"><span class="sesalbum_album_thumbnail_view_thumb" style="background-image:url(<?php echo $album->getPhotoUrl('thumb.normalmain'); ?>);"></span></a>
            </div>
						<?php 
            		if(count($image) != 0) { ?>
                <div class="sesalbum_album_thumbnail_view_thumbs thumbs<?php echo count($image); ?> clear sesbasic_clearfix">
             		<?php	foreach($image as $key=>$valuePhoto){ ?>
                <div>
                	<?php $imageURL = Engine_Api::_()->sesalbum()->getImageViewerHref($valuePhoto); ?>
                  <a onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $valuePhoto->getPhotoUrl(); ?>','<?php echo $imageURL	; ?>')" href="<?php echo Engine_Api::_()->sesalbum()->getHrefPhoto($valuePhoto->getIdentity(),$valuePhoto->album_id); ?>" class="ses-image-viewer"><span class="sesalbum_album_thumbnail_view_thumb" style="background-image:url(<?php echo Engine_Api::_()->sesalbum()->photoUrlGet($valuePhoto->photo_id,'thumb.icon');  ?>);"></span></a>
                </div>
            <?php } ?>
            		</div>
            <?php	} ?>
            <div class="sesalbum_album_thumbnail_view_btm clear sesbasic_clearfix">
              <div class="sesalbum_album_thumbnail_owner_photo">
              	<?php $user = Engine_Api::_()->getItem('user',$album->owner_id) ?>
                <a href="<?php echo $user->getHref();; ?>">
                  <?php echo $this->itemPhoto($user, 'thumb.profile'); ?>
                 </a>          
              </div>
              <div class="sesalbum_album_thumbnail_album_info">
                <p class="sesalbum_album_thumbnail_album_name">
                  <?php if(isset($this->title)) { ?>
                   <?php echo $this->htmlLink($album, $this->string()->truncate($album->getTitle(), $this->title_truncation),array('title'=>$album->getTitle())) ; ?>
                  <?php } ?>
                </p>
                <p class="sesalbum_album_thumbnail_album_by sesbasic_clearfix sesbasic_text_light"> 
                  <?php if(isset($this->by)) { ?>
                    <span>
                      <?php echo $this->translate('By');?>
                       <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
                    </span>
                  <?php }?>
                  <?php if(isset($this->rating) && $ratingShow) { ?>
                  <?php
                          $user_rate = Engine_Api::_()->getDbtable('ratings', 'sesalbum')->getCountUserRate('album',$album->album_id);
                          $textuserRating = $user_rate == 1 ? 'user' : 'users'; 
                          $textRatingText = $album->rating == 1 ? 'rating' : 'ratings'; ?>
                   <span class="floatR" title="<?php echo $album->rating.' '.$this->translate($textRatingText.' by').' '.$user_rate.' '.$this->translate($textuserRating); ?>" >
                    <?php if( $album->rating > 0 ): ?>
                    <?php for( $x=1; $x<= $album->rating; $x++ ): ?>
                      <span class="sesbasic_rating_star_small fa fa-star"></span>
                    <?php endfor; ?>
                    <?php if( (round($album->rating) - $album->rating) > 0): ?>
                      <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                    <?php endif; ?>
                  <?php endif; ?> 
                    </span>
                  <?php } ?>
                </p>
                <p class="sesalbum_album_thumbnail_album_stats sesalbum_list_stats sesbasic_clearfix sesbasic_text_light">
                	<?php if(isset($this->like)) { ?>
                  <span title="<?php echo $this->translate(array('%s like', '%s likes', $album->like_count), $this->locale()->toNumber($album->like_count))?>">
                    <i class="fa fa-thumbs-up"></i>
                    <?php echo $album->like_count;?>
                  </span>
                <?php } ?>
                <?php if(isset($this->comment)) { ?>
                  <span  title="<?php echo $this->translate(array('%s comment', '%s comments', $album->comment_count), $this->locale()->toNumber($album->comment_count))?>">
                    <i class="fa fa-comment"></i>
                    <?php echo $album->comment_count;?>
                  </span>
               <?php } ?>
               <?php if(isset($this->view)) { ?>
                  <span  title="<?php echo $this->translate(array('%s view', '%s views', $album->view_count), $this->locale()->toNumber($album->view_count))?>">
                    <i class="fa fa-eye"></i>
                    <?php echo $album->view_count;?>
                  </span>
               <?php } ?>
               <?php if(isset($this->favouriteCount)) { ?>
                  <span  title="<?php echo $this->translate(array('%s favourite', '%s favourites', $album->favourite_count), $this->locale()->toNumber($album->favourite_count))?>">
                    <i class="fa fa-heart"></i> 
                    <?php echo $album->favourite_count;?>            
                  </span>
                <?php } ?>
                <?php if(isset($this->downloadCount)) { ?>
                <span class="sesalbum_list_grid_download" title="<?php echo $this->translate(array('%s download', '%s downloads', $album->download_count), $this->locale()->toNumber($album->download_count))?>">
                  <i class="fa fa-download"></i> 
                  <?php echo $album->download_count;?>            
                </span>
              <?php } ?>
                 <?php if(isset($this->photoCount)) { ?>
               	<span  title="<?php echo $this->translate(array('%s photo', '%s photos', $album->count()), $this->locale()->toNumber($album->count()))?>" >
                  <i class="fa fa-photo"></i> 
                  <?php echo $album->count();?>                
               	</span>
                <?php } ?>
                </p>
            <?php  if(isset($this->socialSharing) || isset($this->likeButton) || isset($this->favouriteButton)){  ?>
            	<p class="sesalbum_album_list_btns sesalbum_album_thumbnail_album_btns">
             	<?php if(isset($this->socialSharing)){ 
              //album viewpage link for sharing
                $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $album->getHref());
             ?>
                <a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $album->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesalbum_icon_btn sesalbum_icon_facebook_btn"><i class="fa fa-facebook"></i></a>
                <a href="<?php echo 'http://twitthis.com/twit?url=' . $urlencode . '&title=' . $album->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesalbum_icon_btn sesalbum_icon_twitter_btn"><i class="fa fa-twitter"></i></a>
                <a href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($album->getPhotoUrl('thumb.main'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : $album->getPhotoUrl('thumb.main')) . $album->getPhotoUrl('thumb.main')); ?>&description=<?php echo $album->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesalbum_icon_btn sesalbum_icon_pintrest_btn"><i class="fa fa-pinterest"></i></a>
              <?php }
              $canComment = $album->authorization()->isAllowed($this->viewer, 'comment');
                if(Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && isset($this->likeButton) && $canComment){  ?>
                  <!--Album Like Button-->
                  <?php $albumLikeStatus = Engine_Api::_()->sesalbum()->getLikeStatus($album->album_id); ?>
                  <a href="javascript:;" data-src='<?php echo $album->album_id; ?>' class="sesalbum_icon_btn sesalbum_icon_like_btn sesalbum_albumlike <?php echo ($albumLikeStatus) ? 'button_active' : '' ; ?>">
                    <i class="fa fa-thumbs-up"></i>
                    <span><?php echo $album->like_count; ?></span>
                  </a>
              	<?php } 
                 if(Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && isset($this->favouriteButton)){
                        $albumFavStatus = Engine_Api::_()->getDbtable('favourites', 'sesalbum')->isFavourite(array('resource_type'=>'album','resource_id'=>$album->album_id)); ?>
                    <a href="javascript:;" data-src='<?php echo $album->album_id; ?>' class="sesalbum_icon_btn sesalbum_icon_fav_btn sesalbum_albumFav <?php echo ($albumFavStatus) ? 'button_active' : '' ; ?>">
                      <i class="fa fa-heart"></i>
                      <span><?php echo $album->favourite_count; ?></span>
                    </a>
                <?php } ?>
              </span>
            <?php } ?>
                </p>
              </div>
            </div>
          </li>      
      <?php } ?>
      <?php endforeach;
      if($this->load_content == 'pagging'){ ?>
 		 		<?php echo $this->paginationControl($this->paginator, null, array("_pagging.tpl", "sesalbum"),array('identityWidget'=>$randonNumber)); ?>
			<?php } ?>
       <?php  if(  $this->paginator->getTotalItemCount() == 0){  ?>
            <div class="tip">
              <span>
                <?php echo $this->translate("There are currently no albums.");?>
                 <?php if( $this->canCreate ): ?>
                  <?php echo $this->translate('Be the first to %1$screate%2$s one!', 
                     '<a href="'.$this->url(array('action' => 'create','controller'=>'index'),'sesalbum_general',true).'">', '</a>'); 
                  ?>
                  <?php endif; ?>
              </span>
            </div>    
    			<?php } ?>
<?php if(!$this->is_ajax){ ?>
    </ul>
  </div>  
 	<?php if($this->load_content != 'pagging'){ ?>
  	<div class="sesbasic_view_more" id="view_more_<?php echo $randonNumber; ?>" onclick="viewMore_<?php echo $randonNumber; ?>();" > 
   		<?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => "feed_viewmore_link_$randonNumber", 'class' => 'buttonlink icon_viewmore')); ?> 
   	</div>
  	<div class="sesbasic_view_more_loading" id="loading_image_<?php echo $randonNumber; ?>" style="display: none;">
    	<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sesbasic/externals/images/loading.gif' />
    </div>
  <?php } ?>
<?php } ?>
 <?php if($this->load_content == 'auto_load'){ ?>
<script type="text/javascript">
		window.addEvent('load', function() {
		 sesJqueryObject(window).scroll( function() {
			  var heightOfContentDiv_<?php echo $randonNumber; ?> = sesJqueryObject('#scrollHeightDivSes_<?php echo $randonNumber; ?>').offset().top;
        var fromtop_<?php echo $randonNumber; ?> = sesJqueryObject(this).scrollTop();
        if(fromtop_<?php echo $randonNumber; ?> > heightOfContentDiv_<?php echo $randonNumber; ?> - 100 && sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').css('display') == 'block' ){
						document.getElementById('feed_viewmore_link_<?php echo $randonNumber; ?>').click();
				}
     });
	});
</script>
<?php } ?>
<script type="text/javascript">
var params<?php echo $randonNumber; ?> = '<?php echo json_encode($this->params); ?>';
var identity<?php echo $randonNumber; ?>  = '<?php echo $randonNumber; ?>';
var searchParams<?php echo $randonNumber; ?>;
function paggingNumber<?php echo $randonNumber; ?>(pageNum){
	 sesJqueryObject ('.overlay_<?php echo $randonNumber ?>').css('display','block');
    (new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + "widget/index/mod/sesalbum/name/browse-albums",
      'data': {
        format: 'html',
        page: pageNum,    
				params :params<?php echo $randonNumber; ?>, 
				is_ajax : 1,
				searchParams : searchParams<?php echo $randonNumber; ?>,
				identity : identity<?php echo $randonNumber; ?>,
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
			 if($('loadingimgsesalbum-wrapper'))
				sesJqueryObject('#loadingimgsesalbum-wrapper').hide();
				sesJqueryObject ('.overlay_<?php echo $randonNumber ?>').css('display','none');
        document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML =  responseHTML;
				sesJqueryObject('#paginator_count_sesalbum').find('#total_item_count_sesalbum').html(sesJqueryObject('#paginator_count_ajax_sesalbum').find('#total_item_count_sesalbum').html());
				sesJqueryObject('#paginator_count_ajax_sesalbum').remove();
      }
    })).send();
    return false;
}
  var page<?php echo $randonNumber; ?> = '<?php echo $this->page + 1; ?>';
  viewMoreHide_<?php echo $randonNumber; ?>();
  function viewMoreHide_<?php echo $randonNumber; ?>() {
    if ($('view_more_<?php echo $randonNumber; ?>'))
      $('view_more_<?php echo $randonNumber; ?>').style.display = "<?php echo ($this->paginator->count() == 0 ? 'none' : ($this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' )) ?>";
  }
  function viewMore_<?php echo $randonNumber; ?> (){
    document.getElementById('view_more_<?php echo $randonNumber; ?>').style.display = 'none';
    document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = '';    
    (new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'widget/index/mod/sesalbum/name/browse-albums/index/',
      'data': {
        format: 'html',
        page: page<?php echo $randonNumber; ?>,    
				params :params<?php echo $randonNumber; ?>, 
				is_ajax : 1,
				searchParams : searchParams<?php echo $randonNumber; ?>,
				identity : identity<?php echo $randonNumber; ?>,
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				if($('loadingimgsesalbum-wrapper'))
					sesJqueryObject('#loadingimgsesalbum-wrapper').hide();
        document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML = document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML + responseHTML;
				sesJqueryObject('#paginator_count_sesalbum').find('#total_item_count_sesalbum').html(sesJqueryObject('#paginator_count_ajax_sesalbum').find('#total_item_count_sesalbum'));
				sesJqueryObject('#paginator_count_ajax_sesalbum').remove();
				//document.getElementById('view_more_<?php echo $randonNumber; ?>').style.display = 'block';
				document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = 'none';
      }
    })).send();
    return false;
	};
</script>
