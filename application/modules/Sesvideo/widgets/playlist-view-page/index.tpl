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
<?php if(!$this->is_ajax){ 
if(isset($this->docActive)){
	$imageURL = $this->playlist->getPhotoUrl();
	if(strpos($this->playlist->getPhotoUrl(),'http') === false)
          	$imageURL = (!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://". $_SERVER['HTTP_HOST'].$this->playlist->getPhotoUrl();
  $this->doctype('XHTML1_RDFA');
  $this->headMeta()->setProperty('og:title', strip_tags($this->playlist->getTitle()));
  $this->headMeta()->setProperty('og:description', strip_tags($this->playlist->getDescription()));
  $this->headMeta()->setProperty('og:image',$imageURL);
  $this->headMeta()->setProperty('twitter:title', strip_tags($this->playlist->getTitle()));
  $this->headMeta()->setProperty('twitter:description', strip_tags($this->playlist->getDescription()));
}
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesvideo/externals/styles/styles.css'); ?>
<div class="sesvideo_item_view_wrapper clear">
<?php $playlist = $this->playlist; ?>
<div class="sesvideo_item_view_top sesbasic_clearfix sesbasic_bxs sesbm">
    <div class="sesvideo_item_view_artwork">
    	<?php echo $this->itemPhoto($playlist, 'thumb.profile'); ?>
    </div>
    <div class="sesvideo_item_view_info">
      <div class="sesvideo_item_view_title">
        <?php echo $playlist->getTitle() ?>
      </div>
      <?php if(!empty($this->informationPlaylist) && in_array('postedby',  $this->informationPlaylist)): ?>
      	<p class="sesvideo_item_view_stats sesbasic_text_light">
          <?php echo $this->translate('Created %s by ', $this->timestamp($this->playlist->creation_date)) ?>
          <?php echo $this->htmlLink($playlist->getOwner(), $playlist->getOwner()->getTitle()) ?>
      	</p>
       <?php endif; ?>   
        <div class="sesvideo_item_view_stats sesvideo_list_stats sesbasic_text_light sesbasic_clearfix"> 
        	<?php if(!empty($this->informationPlaylist) && in_array('viewCountPlaylist',  $this->informationPlaylist)): ?>
          	<span title="<?php echo $this->translate(array('%s view', '%s views', $this->playlist->view_count), $this->locale()->toNumber($this->playlist->view_count)) ?>"><i class="fa fa-eye"></i><?php echo $this->locale()->toNumber($this->playlist->view_count); ?></span>
        	<?php endif; ?>
          <?php if(!empty($this->informationPlaylist) && in_array('favouriteCountPlaylist', $this->informationPlaylist)): ?>
          	<span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $this->playlist->favourite_count), $this->locale()->toNumber($this->playlist->favourite_count)) ?>"><i class="fa fa-heart"></i><?php echo $this->locale()->toNumber($this->playlist->favourite_count);?></span>
      		<?php endif; ?>
      		<?php if(!empty($this->informationPlaylist) && in_array('likeCountPlaylist', $this->informationPlaylist)): ?>    
	          <span title="<?php echo $this->translate(array('%s like', '%s likes', $this->playlist->like_count), $this->locale()->toNumber($this->playlist->like_count)) ?>"><i class="fa fa-thumbs-up"></i><?php echo $this->locale()->toNumber($this->playlist->like_count); ?></span>  
      		<?php endif; ?>
        </div>
      <?php if(!empty($this->informationPlaylist) && in_array('descriptionPlaylist',  $this->informationPlaylist) && $playlist->description): ?>
        <div class="sesvideo_item_view_des">
          <?php echo (nl2br($playlist->description)); ?>
        </div>
      <?php endif; ?>
       <?php
          		$urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $this->playlist->getHref()); ?>
               <div class="sesvideo_list_btns sesvideo_item_view_options"> 
                   <?php if(!empty($this->informationPlaylist) && in_array('socialSharingPlaylist', $this->informationPlaylist)){ ?>
                    <a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $this->playlist->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesbasic_icon_btn sesbasic_icon_facebook_btn"><i class="fa fa-facebook"></i></a>
                    <a href="<?php echo 'http://twitthis.com/twit?url=' . $urlencode . '&title=' . $this->playlist->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesbasic_icon_btn sesbasic_icon_twitter_btn"><i class="fa fa-twitter"></i></a>
                    <a href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($this->playlist->getPhotoUrl('thumb.main'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $this->playlist->getPhotoUrl('thumb.main') ) : $this->playlist->getPhotoUrl('thumb.main'))); ?>&description=<?php echo $this->playlist->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesbasic_icon_btn sesbasic_icon_pintrest_btn"><i class="fa fa-pinterest"></i></a>
                    <?php } 
                    if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 ){
                          $this->playlisttype = 'sesvideo_playlist';
                          $getId = 'playlist_id';                                
                          $canComment =  true;
                          if(!empty($this->informationPlaylist) && in_array('likeButtonPlaylist', $this->informationPlaylist) && $canComment){
                        ?>
                      <!--Like Button-->
                      <?php $LikeStatus = Engine_Api::_()->sesvideo()->getLikeStatusVideo($this->playlist->$getId,$this->playlist->getType()); ?>
                        <a href="javascript:;" data-url="<?php echo $this->playlist->$getId ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesvideo_like_<?php echo $this->playlisttype; ?> <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $this->playlist->like_count; ?></span></a>
                        <?php } ?>
                         <?php if(!empty($this->informationPlaylist) && in_array('favouriteButtonPlaylist', $this->informationPlaylist) && isset($this->playlist->favourite_count)){ ?>
                        
                        <?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesvideo')->isFavourite(array('resource_type'=>$this->playlisttype,'resource_id'=>$this->playlist->$getId)); ?>
                        <a href="javascript:;" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesvideo_favourite_<?php echo $this->playlisttype; ?> <?php echo ($favStatus)  ? 'button_active' : '' ?>"  data-url="<?php echo $this->playlist->$getId ; ?>"><i class="fa fa-heart"></i><span><?php echo $this->playlist->favourite_count; ?></span></a>
                      <?php } ?>
                    <?php  } ?>
                    
                <?php $viewer = Engine_Api::_()->user()->getViewer(); ?>
                	<?php if($this->viewer_id): ?>
                    <?php if(!empty($this->informationPlaylist) && in_array('sharePlaylist', $this->informationPlaylist)): ?>
                      <a href="<?php echo $this->url(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesvideo_playlist', 'id' => $this->playlist->getIdentity(), 'format' => 'smoothbox'),'default',true) ?>" class="smoothbox sesbasic_icon_btn" title="<?php echo $this->translate("Share") ?>">
                      <i class="fa fa-share"></i>
                      </a>
                    <?php endif; ?>
                    
                  <?php if(!empty($this->informationPlaylist) && in_array('reportPlaylist',  $this->informationPlaylist)): ?>
                    <a href="<?php echo $this->url(array('module'=>'core', 'controller'=>'report', 'action'=>'create', 'route'=>'default', 'subject'=> $this->playlist->getGuid(), 'format' => 'smoothbox'),'default',true) ?>" class="smoothbox sesbasic_icon_btn" title="<?php echo $this->translate("Report") ?>">
                    <i class="fa fa-flag"></i>
                    </a>
          				<?php endif; ?>
          
          					<?php if($viewer->getIdentity() == $playlist->owner_id || $viewer->level_id = 1 ): ?>
                    <a href="<?php echo $this->url(array('action'=>'edit', 'playlist_id'=>$this->playlist->getIdentity(),'slug'=>$this->playlist->getSlug()),'sesvideo_playlist_view',true) ?>" class="sesbasic_icon_btn" title="<?php echo $this->translate("Edit Playlist") ?>">
                    <i class="fa fa-pencil"></i>
                    </a>
                       <a href="<?php echo $this->url(array('action'=>'delete', 'playlist_id'=>$this->playlist->getIdentity(),'slug'=>$this->playlist->getSlug(),  'format' => 'smoothbox'),'sesvideo_playlist_view',true) ?>" class="sesbasic_icon_btn smoothbox" title="<?php echo $this->translate("Delete Playlist") ?>">
                    <i class="fa fa-trash"></i>
                    </a>
          <?php endif; ?>
        <?php endif; ?>
        </div>
    </div>
  </div>
<?php } ?>
<?php include APPLICATION_PATH . '/application/modules/Sesvideo/views/scripts/_showVideoListGrid.tpl'; ?>
</div>