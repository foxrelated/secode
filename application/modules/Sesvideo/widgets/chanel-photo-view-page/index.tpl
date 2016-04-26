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
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesalbum/externals/styles/styles.css'); ?>
<?php
if(!$this->is_ajax){
	$imageURL = $this->photo->getPhotoUrl();
	if(strpos($this->photo->getPhotoUrl(),'http') === false)
          	$imageURL = (!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://". $_SERVER['HTTP_HOST'].$this->photo->getPhotoUrl();
  $this->doctype('XHTML1_RDFA');
  $this->headMeta()->setProperty('og:title', $this->photo->getTitle());
  $this->headMeta()->setProperty('og:description', $this->photo->getDescription());
  $this->headMeta()->setProperty('og:image',$imageURL);
  $this->headMeta()->setProperty('twitter:title', $this->photo->getTitle());
  $this->headMeta()->setProperty('twitter:description', $this->photo->getDescription());
}
?>
<?php
  $this->headTranslate(array(
    'Save', 'Cancel', 'delete',
  ));
?>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<div class='sesalbum_view_photo sesbasic_bxs sesbasic_clearfix'>
  <div class='sesalbum_view_photo_container_wrapper sesbasic_clearfix'>
      <div class="sesalbum_view_photo_nav_btns">
        <?php
        $photoPreviousData = $this->previousPhoto;
        echo $this->htmlLink($photoPreviousData->getHref(), '<i class="fa fa-angle-left"></i>', array('id' => 'photo_prev','data-url'=>$photoPreviousData->chanelphoto_id, 'class' => 'sesalbum_view_photo_nav_prev_btn'));
        $photoNextData = $this->nextPhoto;
         ?>
        <?php echo $this->htmlLink($photoNextData->getHref(), '<i class="fa fa-angle-right"></i>', array('id' => 'photo_next','data-url'=>$photoNextData->chanelphoto_id, 'class' => 'sesalbum_view_photo_nav_nxt_btn')) ?>
      </div>
    <div class='sesalbum_view_photo_container' id='media_photo_div'>
      <?php  
         $imageViewerURL = Engine_Api::_()->sesvideo()->getImageViewerHref($this->photo);
         if($imageViewerURL != ''){  ?>
        <a href="<?php echo $this->photo->getHref(); ?>" title="Open image in image viewer" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $this->photo->getPhotoUrl(); ?>','<?php echo $imageViewerURL; ?>');return false;" class="sesalbum_view_photo_expend"><i class="fa fa-expand"></i></a>
      <?php } ?>
      <div id="media_photo_next">
      	<a id="photo_main_next" href="javascript:;">
        <?php echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
          'id' => 'media_photo',
          'onload'=>'doResizeForButton()'
        )); ?>
        </a>
      </div>
    </div>
    <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != ''){ 
    	$urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $this->photo->getHref());
    ?>
      <ul class="sesbasic_clearfix sesalbum_photo_view_btns">
        <li><a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $this->photo->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesalbum_facebook_button"><i class="fa fa-facebook"></i></a></li>
        <li><a href="<?php echo 'http://twitthis.com/twit?url=' . $urlencode . '&title=' . $this->photo->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesalbum_twitter_button"><i class="fa fa-twitter"></i></a></li>
        <li><a  href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($this->photo->getPhotoUrl('thumb.main'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] .$this->photo->getPhotoUrl() ) : $this->photo->getPhotoUrl('thumb.main')) . $this->photo->getPhotoUrl('thumb.main')); ?>&description=<?php echo $this->photo->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesalbum_pintrest_button"><i class="fa fa-pinterest"></i></a></li>
        <li><a title="<?php echo $this->translate('Share'); ?>" href="<?php echo $this->url(array("action" => "share", "type" => "sesvideo_chanelphoto", "photo_id" => $this->photo->getIdentity(),"format" => "smoothbox"), 'sesalbum_general', true); ?>" class="sesalbum_photo_view_share_button smoothbox"><i class="fa fa-share"></i></a></li>
        <li><a title="<?php echo $this->translate('Download'); ?>" href="<?php echo $this->url(array('module' => 'sesvideo', 'action' => 'download', 'photo_id' => $this->photo->chanelphoto_id,'file_id'=>$this->photo->file_id), 'sesvideo_chanel', true); ?>" class="sesalbum_photo_view_download_button"><i class="fa fa-download"></i></a></li>
       <?php if($this->canComment){ ?>
     	 <?php $LikeStatus = Engine_Api::_()->sesalbum()->getLikeStatusPhoto($this->photo->chanelphoto_id,'sesvideo_chanelphoto'); ?>
        <li><a href="javascript:void(0);" id="sesLikeUnlikeButton" class="sesalbum_view_like_button <?php echo $LikeStatus === true ? 'button_active' : '' ;  ?>"><i class="fa fa-thumbs-up"></i></a></li>
        <?php } ?>
      <?php if($this->canComment){ ?>
        <li><a title="<?php echo $this->translate('Comment'); ?>" href="javascript:void(0);" id="sescomment_button" class="sesalbum_view_comment_button"><i class="fa fa-comment"></i></a></ii>
      <?php } ?>
        <li class="sesalbum_photo_view_option_btn">
          <a title="<?php echo $this->translate('Options'); ?>" href="javascript:;" id="parent_container_option"><i id="fa-ellipsis-v" class="fa fa-ellipsis-v"></i></a>
        </li>  
      </ul>
    <?php } ?>
  </div>
  <div class="sesalbum_view_photo_count">
    <?php echo $this->translate('Photo %1$s of %2$s',
        $this->locale()->toNumber($this->photo->getPhotoIndex() + 1),
        $this->locale()->toNumber($this->chanel->count())) ?>
  </div>
	<div class="sesalbum_photo_view_bottom_right">
    <?php if(isset($this->status_slideshowPhoto)){ ?>
      <!-- Corresponding photo as per album id -->
      <div class="layout_sesalbum_photo_strip">
        <div class="sesalbum_photos_strip_slider sesbasic_clearfix clear">
          <a id="prevSlide" class="sesalbum_photos_strip_slider_btn btn-prev"><i class="fa fa-angle-left"></i></a>
          <div class="sesalbum_photos_strip_slider_content">
            <div id="sesalbum_corresponding_photo" style="width:257px;">
            <?php if(!$this->is_ajax){ ?>
              <img id="sesalbum_corresponding_photo_image" src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sesbasic/externals/images/loading.gif" alt="" style="margin-top:23px;" />
             <?php } ?>
            </div>
          </div>
          <a id="nextSlide" class="sesalbum_photos_strip_slider_btn btn-nxt"><i class="fa fa-angle-right"></i></a>
        </div>
      </div>
    <?php } ?>
    <?php if(isset($this->paginator_like) && isset($this->status_like) && $this->paginator_like->getTotalItemCount() >0){ ?>
      <!--People  Like photo code -->
      <div class="layout_sesalbum_people_like_photo">
        <h3><?php echo $this->translate("People Who Like This");?></h3>
        <ul id="like-status-<?php echo $this->identity; ?>" class="sesalbum_user_listing sesbasic_clearfix clear">
          <?php foreach( $this->paginator_like as $item ): ?>
            <li>
              <?php $user = Engine_Api::_()->getItem('user',$item->poster_id); ?>
              <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'),array('title'=>$user->getTitle())); ?>
            </li>
          <?php endforeach; ?>
          <?php if($this->paginator_like->getTotalItemCount() > $this->data_show_like){ ?>
            <li>
              <a href="javascript:;" onclick="getLikeData('<?php echo $this->chanelphoto_id; ?>')" class="sesalbum_user_listing_more">
                <?php echo '+';echo $this->paginator_like->getTotalItemCount() - $this->data_show_like ; ?>
              </a>
            </li>
          <?php } ?>
        </ul>
      </div>
    <?php } ?>
  </div>
	<div class="sesalbum_photo_view_bottom_middle sesbasic_clearfix">
    <?php if( $this->photo->getTitle() ): ?>
      <div class="sesalbum_view_photo_title">
        <?php echo $this->photo->getTitle(); ?>
      </div>
    <?php endif; ?>
    <div class="sesalbum_view_photo_middle_box clear sesbasic_clearfix">
      <div class="sesalbum_view_photo_owner_info sesbasic_clearfix">
        <div class="sesalbum_view_photo_owner_photo">
          <?php $albumOwnerDetails = Engine_Api::_()->user()->getUser($this->chanel->owner_id); ?>
          <?php echo $this->htmlLink($albumOwnerDetails->getHref(), $this->itemPhoto($albumOwnerDetails, 'thumb.icon')); ?>  
        </div>
        <div class="sesalbum_view_photo_owner_details">
          <span class="sesalbum_view_photo_owner_name sesbasic_text_light">
            <?php echo $this->translate("by"); ?><?php echo $this->htmlLink($albumOwnerDetails->getHref(), $albumOwnerDetails->getTitle()); ?>
          </span>
          <span class="sesbasic_text_light sesalbum_view_photo_date">
            <?php echo $this->translate('in %1$s', $this->htmlLink( $this->chanel->getHref(), $this->chanel->getTitle())); ?>
            on <?php echo date('F j',strtotime($this->photo->creation_date)); ?>
          </span>
        </div>
    	</div>
    </div>
    <div class="sesalbum_view_photo_info_left">
      <?php if( $this->photo->getDescription() ): ?>
        <div class="sesalbum_view_photo_des">
          <b><?php echo $this->translate("Description"); ?></b>
          <?php echo nl2br($this->photo->getDescription()) ?>
        </div>
      <?php endif; ?>
      <?php if(!is_null($this->photo->location) && $this->photo->location != '' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo_enable_location', 1)){ ?>
        <div class="sesalbum_view_photo_location"><i class="fa fa-map-marker sesbasic_text_light"></i>
        <?php echo $this->htmlLink(Array("module"=> "sesalbum", "controller" => "photo", "action" => "location", "route" => "sesalbum_extended", "type" => "location","chanel_id" =>$this->photo->chanel_id, "photo_id" => $this->photo->getIdentity()), $this->photo->location, array("class" => "smoothboxOpen")); ?>
        </div>
      <?php } ?>
    </div>
    <!-- comment code-->
    <div class="sesalbum_photo_view_bottom_comments layout_core_comments">
      <?php echo $this->action("list", "comment", "core", array("type" => "sesvideo_chanelphoto", "id" => $this->photo->getIdentity())); ?> 
    </div>
  </div>
</div>
<script type="text/javascript">
var maxHeight = <?php echo $this->maxHeight; ?>;
function doResizeForButton(){
	<?php if(Engine_Api::_()->user()->getViewer()->getIdentity() == '0'){ ?>
			return false;
	<?php } ?>
	var topPositionOfParentDiv =  sesJqueryObject(".sesalbum_photo_view_option_btn").offset().top + 35;
	topPositionOfParentDiv = topPositionOfParentDiv+'px';
	var leftPositionOfParentDiv =  sesJqueryObject(".sesalbum_photo_view_option_btn").offset().left - 115;
	leftPositionOfParentDiv = leftPositionOfParentDiv+'px';
	sesJqueryObject('.sesalbum_option_box').css('top',topPositionOfParentDiv);
	sesJqueryObject('.sesalbum_option_box').css('left',leftPositionOfParentDiv);
}
 var width = sesJqueryObject('.sesalbum_view_photo_container_wrapper').width();
  sesJqueryObject('#media_photo').css('max-width',width+'px');
	sesJqueryObject('#media_photo').css('max-height',maxHeight+'px');
<?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != ''){ ?>
window.addEvent('load',function(){
	doResizeForButton();
});
var optionDataForButton;
optionDataForButton = '<div class="sesalbum_option_box"><?php if ($this->viewer()->getIdentity()):?><?php if( $this->canEdit ): ?><?php echo $this->htmlLink(Array("module"=> "sesvideo", "controller" => "chanel", "action" => "location", "route" => "sesvideo_chanel","chanel_id" =>$this->photo->chanel_id, "photo_id" => $this->photo->getIdentity(),type=>"photo"), $this->translate("Edit Location"), array("class" => "smoothboxOpen fa fa-map-marker")); ?><?php echo $this->htmlLink(Array("module"=> "sesvideo", "controller" => "chanel", "action" => "edit-photo", "route" => "sesvideo_chanel","chanel_id" =>$this->photo->chanel_id, "photo_id" => $this->photo->getIdentity()), $this->translate("Edit"), array("class" => "smoothboxOpen fa fa-pencil")) ?><?php endif; ?><?php if( $this->canDelete ): ?><?php echo $this->htmlLink(Array("module"=> "sesvideo", "controller" => "chanel", "action" => "delete-photo", "route" => "sesvideo_chanel","chanel_id" =>$this->photo->chanel_id, "photo_id" => $this->photo->getIdentity()), $this->translate("Delete"), array("class" => "smoothboxOpen fa fa-trash")) ?><?php endif; ?><?php if( !$this->message_view ):?>  <?php echo $this->htmlLink($this->url(array("action" => "share", "type" => "sesvideo_chanelphoto", "photo_id" => $this->photo->getIdentity(),"format" => "smoothbox"), "sesalbum_general"	, true), $this->translate("Share"), array("class" => "smoothboxOpen fa fa-share")); ?><?php echo $this->htmlLink(Array("module"=> "core", "controller" => "report", "action" => "create", "route" => "default", "subject" => $this->photo->getGuid()), $this->translate("Report"), array("class" => "smoothboxOpen  fa fa-flag")); ?><?php endif; ?><?php endif; ?></div>';
sesJqueryObject(optionDataForButton).appendTo('body');
<?php if(!$this->is_ajax){ ?>
	sesJqueryObject(document).click(function(event){
		if(event.target.id == 'parent_container_option' || event.target.id == 'fa-ellipsis-v'){
			if(sesJqueryObject('#parent_container_option').hasClass('active')){
				sesJqueryObject('#parent_container_option').removeClass('active');
				sesJqueryObject('.sesalbum_option_box').hide();	
			}else{
				sesJqueryObject('#parent_container_option').addClass('active');
				sesJqueryObject('.sesalbum_option_box').show();	
		  }
		}else{
			sesJqueryObject('#parent_container_option').removeClass('active');
			sesJqueryObject('.sesalbum_option_box').hide();	
		}
	});
	// on window resize work
	sesJqueryObject(window).resize(function(){
			doResizeForButton();
	});
<?php } ?>
  //Set Width On Image
<?php } ?>
<?php if(!$this->is_ajax){ ?>
	/*change next previous button click event*/
		sesJqueryObject(document).on('click','#photo_prev',function(){
			changeNextPrevious(this);	
			return false;
		});
		sesJqueryObject(document).on('click','#photo_next',function(){
			changeNextPrevious(this);	
			return false;
		});
	 function changeNextPrevious(thisObject){
			history.pushState(null, null, sesJqueryObject(thisObject).attr('href'));
			var height = sesJqueryObject('#media_photo_div').height();
			var width = sesJqueryObject('#media_photo_div').width();
			sesJqueryObject('#media_photo_div').html('<div class="clear sesbasic_loading_container"></div>');
			sesJqueryObject('.sesbasic_loading_container').css('height',height) ;
			sesJqueryObject('.sesbasic_loading_container').css('width',width) ;
			var correspondingImageData = sesJqueryObject('#sesalbum_corresponding_photo').html();
			var photo_id = sesJqueryObject(thisObject).attr('data-url');
			(new Request.HTML({
      method: 'post',
      'url':en4.core.baseUrl + 'widget/index/mod/sesvideo/name/chanel-photo-view-page/',
      'data': {
        format: 'html',
				 photo_id : photo_id,
				params :'<?php echo json_encode($this->params); ?>', 
				is_ajax : 1,
				maxHeight:maxHeight
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				if(sesJqueryObject('.sesalbum_option_box').length >0)
					sesJqueryObject('.sesalbum_option_box').remove();
				<?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != ''){ ?>
					sesJqueryObject(optionDataForButton).appendTo('body');
				<?php } ?>
					sesJqueryObject('.layout_sesvideo_chanel_photo_view_page').html(responseHTML);
					var width = sesJqueryObject('.sesalbum_view_photo_container_wrapper').width();
					sesJqueryObject('#media_photo').css('max-width',width+'px');
					sesJqueryObject('#media_photo').css('max-height',maxHeight+'px');
					sesJqueryObject('#sesalbum_corresponding_photo').html(correspondingImageData);
					sesJqueryObject('#sesalbum_corresponding_photo > a').each(function(index){
					sesJqueryObject(this).removeClass('slideuptovisible');
					if(sesJqueryObject(this).attr('data-url') == photo_id)
						sesJqueryObject(this).addClass('active');
					else
						sesJqueryObject(this).removeClass('active');
						countSlide++;
					});
					sesJqueryObject('#sesalbum_corresponding_photo > a').eq(3).addClass('slideuptovisible');
					sesJqueryObject('#sesalbum_corresponding_photo').css('width',(countSlide*64)+'px');
      }
    })).send();
    return false;
	}
<?php } ?>
 <?php if(isset($this->status_slideshowPhoto) && !$this->is_ajax){ ?>
sesJqueryObject(document).on('click','.sesalbum_corresponding_image_album',function(e){
		e.preventDefault();
		if(!sesJqueryObject(this).hasClass('active'))
			changeNextPrevious(this);
});
var countSlide = 0;
function getCorrespondingImg(){
	(new Request.HTML({
      method: 'post',
      'url':en4.core.baseUrl + 'sesvideo/index/corresponding-image/chanel_id/<?php echo $this->chanel->chanel_id; ?>',
      'data': {
        format: 'html',
				is_ajax : 1,
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				if(responseHTML){
					sesJqueryObject('#sesalbum_corresponding_photo').html(responseHTML);
					sesJqueryObject('#sesalbum_corresponding_photo > a').each(function(index){
						sesJqueryObject(this).removeClass('slideuptovisible');
						if(sesJqueryObject(this).attr('data-url') == "<?php echo $this->photo->chanelphoto_id; ?>"){
							sesJqueryObject(this).addClass('active');	
						}
						countSlide++;	
					});
					sesJqueryObject('#sesalbum_corresponding_photo > a').eq(3).addClass('slideuptovisible');
					sesJqueryObject('#sesalbum_corresponding_photo').css('width',(countSlide*64)+'px');
				}
      }
    })).send();	
}
<?php } ?>
<?php if(!$this->is_ajax && isset($this->status_slideshowPhoto)){ ?>
sesJqueryObject(document).on('mouseover','#prevSlide',function(e){
	var indexCurrent = 	sesJqueryObject('#sesalbum_corresponding_photo > a.slideuptovisible').index();
	if(indexCurrent<4 || indexCurrent == '-1')
		sesJqueryObject('#prevSlide').css('cursor','not-allowed');
	else
		sesJqueryObject('#prevSlide').css('cursor','pointer');
});
sesJqueryObject(document).on('mouseover','#nextSlide',function(e){
	var indexCurrent = 	sesJqueryObject('#sesalbum_corresponding_photo > a.slideuptovisible').index();
	if(indexCurrent == (countSlide-1) || indexCurrent == '-1')
		sesJqueryObject('#nextSlide').css('cursor','not-allowed');
	else
		sesJqueryObject('#nextSlide').css('cursor','pointer');
});
sesJqueryObject(document).on('click','#nextSlide',function(){
	var indexCurrent = 	sesJqueryObject('#sesalbum_corresponding_photo > a.slideuptovisible').index();
	if((countSlide-1) == indexCurrent || indexCurrent == '-1'){
		// last slide is visible
	}else{
		var slideLeft = (countSlide-1) - indexCurrent;
		var leftAttr = sesJqueryObject('#sesalbum_corresponding_photo').css('left').replace('px','');
		leftAttr = leftAttr.replace('-','');		
		if(slideLeft>3){
			leftAttr = parseInt(leftAttr,10);
			sesJqueryObject('#sesalbum_corresponding_photo').css('left','-'+(leftAttr+(64*4))+'px');
			sesJqueryObject('#sesalbum_corresponding_photo > a').eq(indexCurrent).removeClass('slideuptovisible');
			sesJqueryObject('#sesalbum_corresponding_photo > a').eq((indexCurrent+4)).addClass('slideuptovisible');
		}else{
			leftAttr = parseInt(64*slideLeft,10)+parseInt(leftAttr,10);
			sesJqueryObject('#sesalbum_corresponding_photo').css('left','-'+leftAttr+'px');
			sesJqueryObject('#sesalbum_corresponding_photo > a').eq(indexCurrent).removeClass('slideuptovisible');
			sesJqueryObject('#sesalbum_corresponding_photo > a').eq((indexCurrent+slideLeft)).addClass('slideuptovisible');
		}
	}
});
sesJqueryObject(document).on('click','#prevSlide',function(){
	var indexCurrent = 	sesJqueryObject('#sesalbum_corresponding_photo > a.slideuptovisible').index();
	var leftAttr = sesJqueryObject('#sesalbum_corresponding_photo').css('left').replace('px','');
	leftAttr = leftAttr.replace('-','');
	leftAttr = parseInt(leftAttr,10);
 if(leftAttr == 0 || countSlide < 4 || indexCurrent == '-1'){
	 //first slide
 }else{
	var type = indexCurrent - 3;
	if(typeof type == 'number' && type > 3 ){
		sesJqueryObject('#sesalbum_corresponding_photo').css('left','-'+(leftAttr-(64*4))+'px');
		sesJqueryObject('#sesalbum_corresponding_photo > a').eq(indexCurrent).removeClass('slideuptovisible');
		sesJqueryObject('#sesalbum_corresponding_photo > a').eq((indexCurrent-4)).addClass('slideuptovisible');
	}else{
		var slideLeft = (countSlide-1)-((countSlide-1) - indexCurrent)
		leftAttr = parseInt(leftAttr,10) -  parseInt(64*type,10);
		if(countSlide-1 > 3 || countSlide-1 == 3 || indexCurrent-type < 4)
			var selectedindex = 3;
		else
			var selectedindex = indexCurrent-type;
		sesJqueryObject('#sesalbum_corresponding_photo').css('left',leftAttr+'px');
		sesJqueryObject('#sesalbum_corresponding_photo > a').eq(indexCurrent).removeClass('slideuptovisible');
		sesJqueryObject('#sesalbum_corresponding_photo > a').eq(selectedindex).addClass('slideuptovisible');
	}
 }
	return false;
});
<?php } ?>
 <?php if(isset($this->status_slideshowPhoto) && !$this->is_ajax){ ?>
sesJqueryObject(document).ready(function(){
	getCorrespondingImg();	
});
<?php } ?>
function getTagData(value){
	if(value){
		url = en4.core.staticBaseUrl+'albums/index/tag-photo/photo_id/'+value;
		openURLinSmoothBox(url);	
		return;
	}
}
<?php if($this->viewer->getIdentity() !=0){ ?>
	sesJqueryObject(document).on('keyup', function (e) {
		if(sesJqueryObject('#'+e.target.id).prop('tagName') == 'INPUT' || sesJqueryObject('#'+e.target.id).prop('tagName') == 'TEXTAREA')
				return true;
		if(sesJqueryObject('#ses_media_lightbox_container_video').css('display') == 'none'){
			// like code
			if (e.keyCode === 76) {
				if(sesJqueryObject('#sesLikeUnlikeButton').length > 0)
				 sesJqueryObject('#sesLikeUnlikeButton').trigger('click');
			}
			// favourite code
			if (e.keyCode === 70) {
				if(sesJqueryObject('.sesalbum_photoFav').length > 0)
					sesJqueryObject('.sesalbum_photoFav').trigger('click');
			}
			// open photo in lightbox code
			if(e.keyCode === 77){
				if(sesJqueryObject('.sesalbum_view_photo_expend').length>0)	
					sesJqueryObject('.sesalbum_view_photo_expend').trigger('click');
			}
		}
	});
<?php } ?>
function getLikeData(value){
	if(value){
		url = en4.core.staticBaseUrl+'albums/index/like-photo/photo_id/'+value;
		openURLinSmoothBox(url);	
		return;
	}
}
</script>