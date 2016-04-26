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
<?php $addThisCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('ses.addthis',0); ?>
<?php if($addThisCode && in_array('addThis',$this->allowAdvShareOptions)){ ?>
<?php $this->headScript()->appendFile("//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo $addThisCode; ?>"); ?>
<?php } ?>
<?php
if(isset($this->docActive)){
	$imageURL = $this->video->getPhotoUrl();
	if(strpos($this->video->getPhotoUrl(),'http') === false)
          	$imageURL = (!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://". $_SERVER['HTTP_HOST'].$this->video->getPhotoUrl();
  $this->doctype('XHTML1_RDFA');
  $this->headMeta()->setProperty('og:title', strip_tags($this->video->getTitle()));
  $this->headMeta()->setProperty('og:description', strip_tags($this->video->getDescription()));
  $this->headMeta()->setProperty('og:image',$imageURL);
  $this->headMeta()->setProperty('twitter:title', strip_tags($this->video->getTitle()));
  $this->headMeta()->setProperty('twitter:description', strip_tags($this->video->getDescription()));
}
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesvideo/externals/styles/styles.css'); ?>
<div id="video_content" style="display:block">
<?php if($this->locked){ ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/customAlert/sweetalert.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/customAlert/sweetalert.js'); ?>
<script type="application/javascript">
 function promptPasswordCheck(){
	//var password = prompt("Enter the password ?");
	swal({   
			title: "<?php echo $this->translate('Enter Password For:'); ?>",   
			text: "<?php echo $this->video->getTitle(); ?>",   
			type: "input",   
			showCancelButton: true,   
			closeOnConfirm: false,   
			animation: "slide-from-top",   
			inputPlaceholder: "<?php echo $this->translate('Enter Password'); ?>"
		}, function(inputValue){   
			if (inputValue === false) {
				sesJqueryObject(document).ready(function(){
				document.getElementById('video_content').remove();
				document.getElementById('locked_content').show();
				sesJqueryObject('.layout_core_comments').remove();
			 });
			 return false;
			}
			if (inputValue === "") {    
			 swal.showInputError("<?php echo $this->translate('You need to write something!');  ?>");     
			 return false   
		}
			if(inputValue.toLowerCase() == '<?php echo strtolower($this->password); ?>'){
				sesJqueryObject(document).ready(function(){
					document.getElementById('locked_content').remove();
					document.getElementById('video_content').show();
					sesJqueryObject('.layout_core_comments').show();
					swal.close();
				})
			}else{
			 swal("Wrong Password", "You wrote: " + inputValue, "error");
			 sesJqueryObject(document).ready(function(){
				document.getElementById('video_content').remove();
				document.getElementById('locked_content').show();
				sesJqueryObject('.layout_core_comments').remove();
			 });
			}
			
	});
 }
 promptPasswordCheck();
</script>
<?php }else{ ?>
<script type="application/javascript">
 sesJqueryObject(document).ready(function(){
		document.getElementById('locked_content').remove();
		document.getElementById('video_content').show();
		sesJqueryObject('.layout_core_comments').show();
	 });
</script>
<?php } ?>
<?php if( !$this->video || $this->video->status != 1 ):
  echo $this->translate('The video you are looking for does not exist or has not been processed yet.');
  return; // Do no render the rest of the script in this mode
endif; ?>
<?php if ( $this->video->type == 3 && $this->video_extension == 'mp4' )
    $this->headScript()
         ->appendFile($this->layout()->staticBaseUrl . 'externals/html5media/html5media.min.js');
?>
<?php if( $this->video->type == 3 && $this->video_extension == 'flv' ):
    $this->headScript()
         ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js');
  ?>
  <script type='text/javascript'>
    en4.core.runonce.add(function() {
      flashembed("video_embed", {
        src: "<?php echo $this->layout()->staticBaseUrl ?>externals/flowplayer/flowplayer-3.1.5.swf",
        width: 480,
        height: 386,
        wmode: 'transparent'
      }, {
        config: {
          clip: {
            url: "<?php echo $this->video_location;?>",
            autoPlay: false,
            duration: "<?php echo $this->video->duration ?>",
            autoBuffering: true
          },
          plugins: {
            controls: {
              background: '#000000',
              bufferColor: '#333333',
              progressColor: '#444444',
              buttonColor: '#444444',
              buttonOverColor: '#666666'
            }
          },
          canvas: {
            backgroundColor:'#000000'
          }
        }
      });
    });
    
  </script>
<?php endif ?>
<?php if(($this->getAllowRating == 0 && $this->allowShowRating == 1 && $this->total_rating_average != 0 ) || ($this->getAllowRating == 1) && in_array('rateCount',$this->allowOptions) ){ ?>
<script type="text/javascript">
	 var tagAction = window.tagAction = function(tag,name){
			var url = "<?php echo $this->url(array('module' => 'sesvideo','action'=>'browse'), 'sesvideo_general', true) ?>?tag_id="+tag+'&tag_name='+name;
     window.location.href = url;
    }
  en4.core.runonce.add(function() {
    var pre_rate = "<?php echo $this->total_rating_average == '' ? 0 : $this->total_rating_average  ;?>";
		<?php if($this->viewer_id == 0){ ?>
			rated = 0;
		<?php }else if($this->allowShowRating == 1 && $this->allowRating == 0){?>
		var rated = 3;
		<?php }else if($this->allowRateAgain == 0 && $this->rated){ ?>
		var rated = 1;
		<?php }else if($this->canRate == 0 && $this->viewer_id != 0){?>
		var rated = 4;
		<?php }else if(!$this->allowMine){?>
		var rated = 2;
		<?php }else{ ?>
    var rated = '90';
		<?php } ?>
    var resource_id = <?php echo $this->video->video_id;?>;
    var total_votes = <?php echo $this->rating_count;?>;
    var viewer = <?php echo $this->viewer_id;?>;
    new_text = '';

    var rating_over = window.rating_over = function(rating) {
      if( rated == 1 ) {
        $('rating_text').innerHTML = "<?php echo $this->translate('you already rated');?>";
				return;
        //set_rating();
      }
			<?php if(!$this->canRate){ ?>
				else if(rated == 4){
						 $('rating_text').innerHTML = "<?php echo $this->translate('rating is not allowed for your member level');?>";
						 return;
				}
			<?php } ?>
			<?php if(!$this->allowMine){ ?>
				else if(rated == 2){
						 $('rating_text').innerHTML = "<?php echo $this->translate('rating on own video not allowed');?>";
						 return;
				}
			<?php } ?>
			<?php if($this->allowShowRating == 1){ ?>
				else if(rated == 3){
						 $('rating_text').innerHTML = "<?php echo $this->translate('rating is disabled');?>";
						 return;
				}
			<?php } ?>
			else if( viewer == 0 ) {
        $('rating_text').innerHTML = "<?php echo $this->translate('please login to rate');?>";
				return;
      } else {
        $('rating_text').innerHTML = "<?php echo $this->translate('click to rate');?>";
        for(var x=1; x<=5; x++) {
          if(x <= rating) {
            $('rate_'+x).set('class', 'fa fa-star');
          } else {
            $('rate_'+x).set('class', 'fa fa fa-star-o star-disable');
          }
        }
      }
    }
    
    var rating_out = window.rating_out = function() {
      if (new_text != ''){
        $('rating_text').innerHTML = new_text;
      }
      else{
        $('rating_text').innerHTML = " <?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";        
      }
      if (pre_rate != 0){
        set_rating();
      }
      else {
        for(var x=1; x<=5; x++) {	
          $('rate_'+x).set('class', 'fa fa fa-star-o star-disable');
        }
      }
    }

    var set_rating = window.set_rating = function() {
      var rating = pre_rate;
      if (new_text != ''){
        $('rating_text').innerHTML = new_text;
      }
      else{
        $('rating_text').innerHTML = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
      }
      for(var x=1; x<=parseInt(rating); x++) {
        $('rate_'+x).set('class', 'fa fa-star');
      }

      for(var x=parseInt(rating)+1; x<=5; x++) {
        $('rate_'+x).set('class', 'fa fa fa-star-o star-disable');
      }

      var remainder = Math.round(rating)-rating;
      if (remainder <= 0.5 && remainder !=0){
        var last = parseInt(rating)+1;
        $('rate_'+last).set('class', 'fa fa-star-half-o');
      }
    }

    var rate = window.rate = function(rating) {
      $('rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!');?>";
			<?php if($this->allowRateAgain == 0 && !$this->rated){ ?>
						 for(var x=1; x<=5; x++) {
								$('rate_'+x).set('onclick', '');
							}
					<?php } ?>
     
      (new Request.JSON({
        'format': 'json',
        'url' : '<?php echo $this->url(array('module' => 'sesvideo', 'controller' => 'index', 'action' => 'rate'), 'default', true) ?>',
        'data' : {
          'format' : 'json',
          'rating' : rating,
          'resource_id': resource_id,
					'resource_type':'<?php echo $this->rating_type; ?>'
        },
        'onSuccess' : function(responseJSON, responseText)
        {
					<?php if($this->allowRateAgain == 0 && !$this->rated){ ?>
							rated = 1;
					<?php } ?>
					total_votes = responseJSON[0].total;
					var rating_sum = responseJSON[0].rating_sum;
          pre_rate = rating_sum / total_votes;
          set_rating();
					if(responseJSON[0].total == 1)
						var textRating = en4.core.language.translate('rating');
					else
						var textRating = en4.core.language.translate('ratings');
          $('rating_text').innerHTML = responseJSON[0].total+" "+textRating;
          new_text = responseJSON[0].total+" "+textRating;
					showTooltip(10,10,'<i class="fa fa-star"></i><span>'+("Video Rated successfully")+'</span>', 'sesbasic_rated_notification');
        }
      })).send();
    }
    set_rating();
  });
</script>
<?php } ?>
<div class="sesvideo_video_view_container clear sesbasic_clearfix sesbasic_bxs">
  <?php if( $this->video->type == 3 ): ?>
    <div id="video_embed" class="sesvideo_view_embed clear sesbasic_clearfix">
      <?php if ($this->video_extension !== 'flv'): ?>
        <video id="video" controls preload="auto" width="480" height="386">
          <source type='video/mp4' src="<?php echo $this->video_location ?>">
        </video>
      <?php endif ?>
    </div>
  <?php else: ?>
    <div class="sesvideo_view_embed clear sesbasic_clearfix">
      <?php echo $this->videoEmbedded ?>
    </div>
  <?php endif; ?>
  <?php if(in_array('openVideoLightbox',$this->allowOptions)){ ?>
  	<a href="javascript:;" id="openVideoInLightbox" class="fa fa-expand sesvideo_view_openlightbox_link"><?php echo $this->translate("Open in Lightbox")?></a>
  <?php } ?>
  <h2 class="sesvideo_view_title sesbasic_clearfix clear">
    <?php echo $this->video->getTitle() ?>
  </h2>
  <div class="sesvideo_view_author">
    <div class="sesvideo_view_author_photo">  
      <?php echo $this->htmlLink($this->video->getParent(), $this->itemPhoto($this->video->getParent(), 'thumb.icon')); ?>
    </div>
    <div class="sesvideo_view_author_info">
      <div class="sesvideo_view_author_name sesbasic_text_light">
        <?php echo $this->translate('By') ?>
        <?php echo $this->htmlLink($this->video->getParent(), $this->video->getParent()->getTitle()) ?>
      </div>
      <div class="sesvideo_view_date sesbasic_text_light">
        <?php echo $this->translate('Posted') ?>
        <?php echo $this->timestamp($this->video->creation_date) ?>
      </div>
    </div>
  </div>
  <div class="sesvideo_view_statics">
    <?php if(($this->getAllowRating == 0 && $this->allowShowRating == 1 && $this->total_rating_average != 0 ) || ($this->getAllowRating == 1) && in_array('rateCount',$this->allowOptions) ){ ?>
      <div id="album_rating" class="sesbasic_rating_star sesvideo_view_rating" onMouseOut="rating_out();">
        <span id="rate_1" class="fa fa-star" <?php  if ($this->viewer_id && $this->ratedAgain && $this->allowMine &&  $this->allowRating):?>onclick="rate(1);"<?php  endif; ?> onMouseOver="rating_over(1);"></span>
        <span id="rate_2" class="fa fa-star" <?php if ( $this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating):?>onclick="rate(2);"<?php endif; ?> onMouseOver="rating_over(2);"></span>
        <span id="rate_3" class="fa fa-star" <?php if ( $this->viewer_id && $this->ratedAgain  && $this->allowMine && $this->allowRating):?>onclick="rate(3);"<?php endif; ?> onMouseOver="rating_over(3);"></span>
        <span id="rate_4" class="fa fa-star" <?php if ($this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating):?>onclick="rate(4);"<?php endif; ?> onMouseOver="rating_over(4);"></span>
        <span id="rate_5" class="fa fa-star" <?php if ($this->viewer_id && $this->ratedAgain  && $this->allowMine && $this->allowRating):?>onclick="rate(5);"<?php endif; ?> onMouseOver="rating_over(5);"></span>
        <span id="rating_text" class="sesbasic_rating_text"><?php echo $this->translate('click to rate');?></span>
      </div>
    <?php } ?>
    <div class="sesvideo_view_stats sesvideo_list_stats sesbasic_text_light">
    <?php if(in_array('likeCount',$this->allowOptions)){ ?>
      <span><i class="fa fa-thumbs-up"></i><?php echo $this->translate(array('%s like', '%s likes', $this->video->like_count), $this->locale()->toNumber($this->video->like_count)); ?></span>
      <?php } ?>
      <?php if(in_array('favouriteCount',$this->allowOptions)){ ?>
      <span><i class="fa fa-heart"></i><?php echo $this->translate(array('%s favourite', '%s favourites', $this->video->favourite_count), $this->locale()->toNumber($this->video->favourite_count)); ?></span>
      <?php } ?>
      <?php if(in_array('commentCount',$this->allowOptions)){ ?>
    <span><i class="fa fa-comment"></i><?php echo $this->translate(array('%s comment', '%s comments', $this->video->comment_count), $this->locale()->toNumber($this->video->comment_count))?></span>
    <?php } ?>
    <?php if(in_array('viewCount',$this->allowOptions)){ ?>
    <span><i class="fa fa-eye"></i><?php echo $this->translate(array('%s view', '%s views', $this->video->view_count), $this->locale()->toNumber($this->video->view_count)) ?></span>
    <?php } ?>
    </div>
  </div>
  <div class="sesvideo_view_meta sesbasic_text_light clear sesbasic_clearfix">
    <?php if( $this->category ): ?>
      <span><i class="fa fa-folder-open" title="<?php echo $this->translate('Category'); ?>"></i> 
      	<?php if($this->video->category_id){ ?>
      	<?php $category = Engine_Api::_()->getItem('sesvideo_category',$this->video->category_id); ?>
       <?php if($category){ ?>
          <a href="<?php echo $category->getHref(); ?>"><?php echo $category->category_name; ?></a>
          	<?php $subcategory = Engine_Api::_()->getItem('sesvideo_category',$this->video->subcat_id); ?>
             <?php if($subcategory){ ?>
                &nbsp;&raquo;&nbsp;<a href="<?php echo $subcategory->getHref(); ?>"><?php echo $subcategory->category_name; ?></a>
            <?php } ?>
            <?php $subsubcategory = Engine_Api::_()->getItem('sesvideo_category',$this->video->subsubcat_id); ?>
             <?php if($subsubcategory){ ?>
                &nbsp;&raquo;&nbsp;<a href="<?php echo $subsubcategory->getHref(); ?>"><?php echo $subsubcategory->category_name; ?></a>
            <?php } ?>
      	<?php }          
      	} ?>
      </span>
    <?php endif; ?>
    
    <?php if (count($this->videoTags )):?>
      <span>
        <i class="fa fa-tag"></i> 
        <?php foreach ($this->videoTags as $tag):
        			if(empty($tag->getTag()->text))
              	continue;
         ?>
          <a href='javascript:;' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>,"<?php echo $tag->getTag()->text; ?>");'>#<?php echo $tag->getTag()->text?></a>&nbsp;
        <?php endforeach; ?>
      </span>
    <?php endif; ?>
   	<?php if(!is_null($this->video->location) && $this->video->location != '' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo_enable_location', 1)){ ?>
      <span>
         <i class="fa fa-map-marker"></i> 
         <a href="javascript:;" onClick="openURLinSmoothBox('<?php echo $this->url(array("module"=> "sesvideo", "controller" => "index", "action" => "location",  "video_id" => $this->video->getIdentity(),'type'=>'video_location'),'default',true); ?>');return false;"><?php echo $this->video->location; ?></a>
      </span>
    <?php } ?>
  </div>
  <div class="sesvideo_view_desc clear">
    <?php echo nl2br($this->video->description);?>
  </div>
	<?php
    //custom field data
    $customMetaFields = Engine_Api::_()->sesvideo()->getCustomFieldMapData($this->video);
    if(count($customMetaFields)>0){
      echo '<div class="sesvideo_view_fields clear sesbasic_clearfix">';
      foreach($customMetaFields as $valueMeta){
        echo '<div class="clear"><span class="floatL sesvideo_view_field_ques"><b>'. $valueMeta['label']. '</b></span><span class="sesvideo_view_field_value">'. '     '.$valueMeta['value'].'</span></div>';
      }
      echo '</div>';
    }
	?>
    <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() && $this->video->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment')){ ?>
 		 <div class='sesvideo_view_options'>
    <!--Like Button-->
    <?php $LikeStatus = Engine_Api::_()->sesvideo()->getLikeStatusVideo($this->video->getidentity(),$this->video->getType()); ?>
    	<a href="javascript:;" title="<?php echo $this->translate('Like'); ?>" data-url="<?php echo $this->video->getIdentity() ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesvideo_like_sesvideo_video <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $this->video->like_count; ?></span></a>
    
    <?php if(in_array('favouriteButton',$this->allowOptions) && Engine_Api::_()->user()->getViewer()->getIdentity()){ ?>
    	<?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesvideo')->isFavourite(array('resource_type'=>'sesvideo_video','resource_id'=>$this->video->getIdentity())); ?>
      	<a href="javascript:;" title="<?php echo $this->translate('Favourite'); ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesvideo_favourite_sesvideo_video ?> <?php echo ($favStatus)  ? 'button_active' : '' ?>"  data-url="<?php echo $this->video->getIdentity(); ; ?>"><i class="fa fa-heart"></i><span><?php echo $this->video->favourite_count; ?></span></a>
    <?php } ?>
    <?php if(in_array('shareAdvance',$this->allowOptions)){ ?>
      <a href="javascript:;" title="<?php echo $this->translate('Share'); ?>" class="sesbasic_icon_btn initialism sesbasic_popup_slide_open btn btn-success">
      	<i class="fa fa-share"></i>
      </a>
		<?php } ?>
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('video.enable.watchlater', 1) && Engine_Api::_()->user()->getViewer()->getIdentity() && in_array('watchLater',$this->allowOptions)){
    $item = Engine_Api::_()->getDbTable('videos', 'sesvideo')->getWatchLaterStatus($this->video->video_id);
    ?>
      <a href="javascript:;" class="sesbasic_icon_btn sesvideo_watch_later <?php echo count($item)  ? 'selectedWatchlater' : '' ?>" title = "<?php echo count($item)  ? $this->translate('Remove from Watch Later') : $this->translate('Add to Watch Later') ?>" data-url="<?php echo $this->video->video_id ; ?>">
      	<i class="fa fa-clock-o"></i>
      </a>
    <?php } ?>
    <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() && in_array('addToPlaylist',$this->allowOptions)){ ?>
      <a href="javascript:;" onclick="opensmoothboxurl('<?php echo $this->url(array('action' => 'add','module'=>'sesvideo','controller'=>'playlist','video_id'=>$this->video->video_id),'default',true); ?>')" class="sesbasic_icon_btn sesvideo_add_playlist"  title="<?php echo  $this->translate('Add To Playlist'); ?>" data-url="<?php echo $this->video->video_id ; ?>"><i class="fa fa-plus"></i></a>
    <?php } ?>
    <?php if( Engine_Api::_()->user()->getViewer()->getIdentity() ): ?>
			<?php if(in_array('shareSimple',$this->allowOptions)){ ?>
      	<a href="<?php echo $this->url(array('module'=> 'sesvideo', 'controller' => 'index','action' => 'share','route' => 'default','type' => 'video','id' => $this->video->getIdentity(),'format' => 'smoothbox'),'default',true); ?>" class="sesbasic_icon_btn initialism btn btn-success smoothbox"  title="<?php echo  $this->translate('Share'); ?>"><i class="fa fa-share"></i></a>
      <?php  } ?>
      <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('video.enable.report',1) && in_array('reportVideo',$this->allowOptions)){ ?>
    		<a href="<?php echo $this->url(array('module'=> 'core','controller' => 'report','action' => 'create','route' => 'default','subject' => $this->video->getGuid(),'format' => 'smoothbox'),'default',true); ?>" class="sesbasic_icon_btn sesbasic_icon_report_btn smoothbox"  title="<?php echo  $this->translate('Report'); ?>" data-url="<?php echo $this->video->video_id ; ?>"><i class="fa fa-flag"></i></a>
      <?php } ?>
    <?php endif ?>
     
    <?php if( $this->can_embed && in_array('embedVideo',$this->allowOptions)): ?>
    	<a href="<?php echo $this->url(array('module'=> 'sesvideo','controller' => 'video','action' => 'embed','id' => $this->video->getIdentity(),'format' => 'smoothbox'),'default',true); ?>" class="sesbasic_icon_btn sesbasic_icon_embed_btn smoothbox"  title="<?php echo  $this->translate('Embed'); ?>"><i class="fa fa-code"></i></a>
    <?php endif;?>
     
    <?php if( $this->can_edit && in_array('editVideo',$this->allowOptions)): ?>
    	<a href="<?php echo $this->url(array('controller' => 'index','action' => 'edit','video_id' => $this->video->video_id),'sesvideo_general',true) ?>" class="sesbasic_icon_btn sesbasic_icon_edit_btn"  title="<?php echo  $this->translate('Edit'); ?>" data-url="<?php echo $this->video->video_id ; ?>"><i class="fa fa-pencil"></i></a>
    <?php endif;?>
    
    <?php if( $this->can_delete && $this->video->status != 2 && in_array('deleteVideo',$this->allowOptions)): ?>
    	<a href="<?php echo $this->url(array('controller' => 'index', 'action' => 'delete', 'video_id' => $this->video->video_id),'sesvideo_general',true); ?>" class="sesbasic_icon_btn sesbasic_icon_delete_btn smoothbox"  title="<?php echo  $this->translate('Delete'); ?>"><i class="fa fa-trash"></i></a>
    <?php endif;?>
  </div>  
 		<?php } ?>
</div>
<div id="sesvideo_image_video_url" data-src="<?php echo $this->video->getPhotoUrl(); ?>" style="display:none"></div>
<!-- Slide in -->
<div id="sesbasic_popup_slide" class="well">
  <div class="sesbasic_popup sesbasic_bxs">
    <div class="sesbasic_popup_title">
       <?php echo $this->translate("Share This Video"); ?>
      <span class="sesbasic_popup_slide_close sesbasic_text_light">
        <i class="fa fa-close"></i>
      </span>
    </div>
    <div class="sesbasic_popup_content">
      <div class="sesbasic_share_popup_content_row clear sesbasic_clearfix">
      	<div class="sesbasic_share_popup_buttons clear">
          <?php if(in_array('privateMessage',$this->allowAdvShareOptions)){ ?>
            <a href="javascript:void(0)" class="sesbasic_button" onClick="opensmoothboxurl('<?php echo $this->url(array('module'=> 'sesbasic', 'controller' => 'index', 'action' => 'message','item_id' => $this->video->getIdentity(), 'type'=>'sesvideo_video'),'default',true); ?>')"> <?php echo $this->translate("Private Message"); ?></a>
            <?php } ?>
             <?php if(in_array('siteShare',$this->allowAdvShareOptions)){ ?>
            <a href="javascript:void(0)" class="sesbasic_button" onClick="opensmoothboxurl('<?php echo $this->url(array('module'=> 'sesvideo', 'controller' =>'index','action' => 'share','type' => 'video','id' => $this->video->getIdentity(),'format' => 'smoothbox'),'default',true); ?>')"> <?php echo $this->translate("Share on Site"); ?></a>
            <?php } ?>
             <?php if(in_array('quickShare',$this->allowAdvShareOptions)){ ?>
            <a href="javascript:void(0)" class="sesbasic_button" onClick="sessendQuickShare('<?php echo $this->url(array('module'=> 'sesvideo', 'controller' =>'index','action' => 'share','type' => 'video','id' => $this->video->getIdentity()),'default',true); ?>');return false;"> <?php echo $this->translate("Quick Share on Site"); ?></a>
          <?php } ?>
      	</div>
      </div>
      <?php if($addThisCode && in_array('addThis',$this->allowAdvShareOptions)){ ?>
      	<div class="sesbasic_share_popup_content_row clear sesbasic_clearfix">
          <div class="sesbasic_share_popup_content_field clear">
            <!-- Go to www.addthis.com/dashboard to customize your tools -->
            <div class="addthis_sharing_toolbox"></div>
          </div>
       	</div>
     <?php  } ?>
		 <?php if( $this->can_embed && Engine_Api::_()->getApi('settings', 'core')->getSetting('video.embeds', 1) && in_array('embed',$this->allowAdvShareOptions)): ?>   
      <div class="sesbasic_share_popup_content_row clear sesbasic_clearfix">
        <div class="sesbasic_share_popup_content_label">
          <?php echo $this->translate("Embed"); ?>
        </div>
        <div class="sesbasic_share_popup_content_field clear">
          <textarea id="embed_testare_select_ses" style="height:67px"><?php echo $this->video->getEmbedCode(); ?></textarea>
        </div>
      </div>
    	<?php endif ?>
      <div class="sesbasic_share_popup_content_row">
      	<div class="sesbasic_share_itme_preview sesbasic_clearfix">
        	<div class="sesbasic_share_itme_preview_img">
          	<img src="<?php echo $this->video->getPhotoUrl();?>" />
          </div>
          <div class="sesbasic_share_itme_preview_info">
          	<div class="sesbasic_share_itme_preview_title">
            	<a href="<?php echo $this->video->getHref();?>"><?php echo $this->video->title;?></a>
            </div>
            <div class="sesbasic_share_itme_preview_des">
             <?php if(strlen($this->video->description) > 200){ 
                  $description = mb_substr($this->video->description,0,200).'...';
                  echo nl2br(strip_tags($description));
                 }else{ ?>
              <?php  echo nl2br(strip_tags($this->video->description));?>
              <?php } ?>
            </div>	
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- comment / like and artist code -->
<div class="sesvideo_view_bottom sesbasic_clearfix">
	<div class="sesvideo_view_bottom_right sesbasic_bxs">
    <?php if(in_array('peopleLike',$this->allowOptions)){  ?>
      <div class="layout_sesvideo_people_like_video">
        <?php echo $this->content()->renderWidget('sesvideo.people-like-item',array('limit_data'=>$this->limitLike,'removeDecorator'=>'yes')); ?>
      </div>
    <?php } ?>
    <?php if(in_array('favourite',$this->allowOptions)){  ?>
      <div class="layout_sesvideo_people_favourite_video">
        <?php echo $this->content()->renderWidget('sesvideo.people-favourite-item',array('limit_data'=>$this->limitFavourite,'removeDecorator'=>'yes')); ?>
      </div>
    <?php } ?>
    <?php $artists = json_decode($this->video->artists,true); ?>
    <?php if(in_array('artist',$this->allowOptions) && count($artists) && $artists != ''){  ?>
      <div class="layout_sesvideo_video_artist">
        <h3><?php echo $this->translate('Artist In This Video'); ?></h3>
        <ul class="sesbasic_sidebar_block sesbasic_user_grid_list sesbasic_clearfix">
          <?php foreach( $artists as $item ): ?>
            <li>
              <?php $artistItem = Engine_Api::_()->getItem('sesvideo_artist',$item) ?>
              <?php if(!$artistItem) continue; ?>
               <?php echo $this->htmlLink($artistItem->getHref(), $this->itemPhoto($artistItem, 'thumb.icon'),array('title'=>$artistItem->getTitle())); ?>
            </li>
          <?php endforeach; ?>
        </ul>
    	</div>
  	<?php } ?>
  </div>
  <div class="sesvideo_view_bottom_left">
    <?php if(in_array('comment',$this->allowOptions)){ ?>
    	<?php echo $this->action("list", "comment", "core", array("type" => $this->video->getType(), "id" => $this->video->getIdentity())); ?>
    <?php } ?>
  </div>


</div>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery-1.8.2.min.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.popupoverlay.js'); ?>
<script type="text/javascript">
jquery1_8_2SesObject(document).ready(function () {
		jquery1_8_2SesObject('#embed_testare_select_ses').toggle(function() {
		jquery1_8_2SesObject(this).select();
	}, function() {
	});
    jquery1_8_2SesObject('#sesbasic_popup_slide').popup({
			focusdelay: 400,
			outline: true,
			vertical: 'top'
    });
});
</script>
</div>
<div id="locked_content" style="display:none">
<h1><?php echo $this->translate('Locked Video'); ?></h1>
<div><?php echo $this->translate('Seems you enter wrong password'); ?> <a href="javascript:;" onClick="window.location.reload();"><?php echo $this->translate('click here'); ?></a> <?php echo $this->translate('to enter password again.'); ?></div>
</div>