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
<?php if(isset($this->identityForWidget) && !empty($this->identityForWidget)){
				$randonNumber = $this->identityForWidget;
      }else{
      	$randonNumber = $this->identity; 
      }
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesalbum/externals/styles/styles.css'); ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/customscrollbar.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/customscrollbar.concat.min.js'); ?>
<?php if(!$this->is_ajax){ ?>
	<div id="scrollHeightDivSes_<?php echo $randonNumber; ?>" class="sesbasic_clearfix sesbasic_bxs clear sesalbum_categories_albums_listing_container">  
<?php } ?>
	<?php 
          $allowRatingAlbum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.album.rating',1);
					$allowShowPreviousRatingAlbum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.ratealbum.show',1);
          if($allowRatingAlbum == 0){
          	if($allowShowPreviousRatingAlbum == 0)
            	$ratingShowAlbum = false;
             else
             	$ratingShowAlbum = true;
          }else
          	$ratingShowAlbum = true;
  ?>
  <?php foreach( $this->paginatorCategory as $item): ?>
  	<div class="sesalbum_categories_albums_listing clear sesbasic_clearfix">
    	<div class="sesalbum_categories_albums_listing_title clear sesbasic_clearfix">
      	<a class="sesbasic_linkinherit" href="<?php echo $item->getBrowseCategoryHref(); ?>?category_id=<?php echo $item->category_id ?>" title="<?php echo $item->category_name; ?>"><?php echo $item->category_name; ?><?php if(isset($this->count_album) && $this->count_album == 1){ ?><?php echo "(".$item->total_album_categories.")"; ?><?php } ?></a>
       <?php if(isset($this->seemore_text) && $this->seemore_text != ''){ ?>
          <span <?php echo $this->allignment_seeall == 'right' ?  'class="floatR"' : ''; ?> >
          	<a href="<?php echo $item->getBrowseCategoryHref(); ?>?category_id=<?php echo $item->category_id ?>" title="<?php echo $item->category_name; ?>">
            <?php $seemoreTranslate = $this->translate($this->seemore_text); ?>
            <?php echo str_replace('[category_name]',$item->category_name,$seemoreTranslate); ?>
          </a>
         </span>
       <?php } ?>
      </div>
    <?php if($this->view_type == 1){ ?> 
      <?php if(isset($this->resultArray['album_data'][$item->category_id])){ ?>
        <div class="sesalbum_categories_albums_listing_thumbnails clear sesbasic_clearfix">
       <?php 
            $counter = 1;
            $itemAlbums = $this->resultArray['album_data'][$item->category_id];
            foreach($itemAlbums as $itemAlbum){ ?>
            <?php if($counter == 1)
                  $albumData = $itemAlbum;          			
             ?>
       
          <div class="<?php echo $counter == 1 ? 'thumbnail_active' : '' ?>" <?php if(empty($this->photoThumbnailActive)) { ?> style="display:none;" <?php } ?>>
            <a href="<?php echo $itemAlbum->getHref(); ?>" title="<?php echo $itemAlbum->getTitle(); ?>" data-url="<?php echo $itemAlbum->album_id ?>" class="slideshow_album_data">
              <img src="<?php echo $itemAlbum->getPhotoUrl('thumb.normalmain'); ?>" alt="<?php echo $itemAlbum->title ?>" class="thumb_icon item_photo_user  thumb_icon"></a>
          </div>
          <?php 
            $counter++;
          } ?>
        </div>      
        <?php } ?>
      <?php if(isset($albumData) && $albumData != ''){ ?>
      <div class="sesalbum_categories_albums_conatiner clear sesbasic_clearfix sesbm">
        <div class="sesalbum_categories_albums_item sesbasic_clearfix clear">
        <?php if(isset($this->albumPhotoActive)){ ?>
          <div class="sesalbum_categories_albums_items_photo floatL">
          	<a class="sesalbum_thumb_img" href="<?php echo $albumData->getHref(); ?>">
            	<span style="background-image:url(<?php echo $albumData->getPhotoUrl('thumb.normalmain'); ?>);"></span>
            </a>
							<?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive)){ ?>
                <span class="sesalbum_labels_container">
                  <?php if(isset($this->featuredLabelActive) && $albumData->is_featured == 1){ ?>
                    <span class="sesalbum_label_featured"><?php echo $this->translate("Featured"); ?></span>
                  <?php } ?>
                  <?php if(isset($this->sponsoredLabelActive)  && $albumData->is_sponsored == 1){ ?>
                    <span class="sesalbum_label_sponsored"><?php echo $this->translate("Sponsored"); ?></span>
                  <?php } ?>
                </span>
              <?php } ?>
          </div>
        <?php } ?>
          <div class="sesalbum_categories_albums_items_cont">
            <div class="sesalbum_categories_albums_items_title">
            <?php if(isset($this->titleActive)){ ?>
            	<?php 
              		if(strlen($albumData->title)>$this->title_truncation)
                  	$titleChanel = mb_substr($albumData->title,0,$this->title_truncation).'...';
                  else
              			$titleChanel = $albumData->title; 
              ?>
            	<a href="<?php echo $albumData->getHref(); ?>"><?php echo $titleChanel ?></a>
             <?php } ?>
            </div>
            <div class="sesalbum_categories_albums_item_stat sesalbum_list_stats sesbasic_clearfix sesbasic_text_light"> 
            	<?php if(isset($this->byActive)){ ?>
                <span>
                  <?php 
                  $owner = $albumData->getOwner();
                  echo $this->translate('Posted by %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle()));
                  ?>
                </span>
              <?php } ?>
              <?php if(isset($this->albumCountActive)){ ?>
                <span title="<?php echo $this->translate(array('%s photo', '%s photos', $albumData->total_photos), $this->locale()->toNumber($albumData->total_photos)); ?>">
                  <i class="fa fa-photo"></i> <?php echo $albumData->total_photos ; ?>
                </span>
              <?php } ?>
              <?php if(isset($this->likeActive)) { ?>
                <span title="<?php echo $this->translate(array('%s like', '%s likes', $albumData->like_count), $this->locale()->toNumber($albumData->like_count)); ?>">
                  <i class="fa fa-thumbs-up"></i>
                  <?php echo $albumData->like_count;?>
                </span>
              <?php } ?>
              <?php if(isset($this->commentActive)) { ?>
                <span title="<?php echo $this->translate(array('%s comment', '%s comments', $albumData->comment_count), $this->locale()->toNumber($albumData->comment_count)); ?>">
                  <i class="fa fa-comment"></i>
                  <?php echo $albumData->comment_count;?>
                </span>
             <?php } ?>
             <?php if(isset($this->favouriteActive)) { ?>
                <span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $albumData->favourite_count), $this->locale()->toNumber($albumData->favourite_count)); ?>">
                  <i class="fa fa-heart"></i>
                  <?php echo $albumData->favourite_count;?>
                </span>
             <?php } ?>
             <?php if(isset($this->viewActive)) { ?>
                <span title="<?php echo $this->translate(array('%s view', '%s views', $albumData->view_count), $this->locale()->toNumber($albumData->view_count)); ?>">
                  <i class="fa fa-eye"></i>
                  <?php echo $albumData->view_count;?>
                </span>
              <?php } ?>
              <?php if(isset($this->ratingActive) && $ratingShowAlbum) { ?>
               <?php
                  $user_rate = Engine_Api::_()->getDbtable('ratings', 'sesalbum')->getCountUserRate('album',$albumData->album_id);
                  $textuserRating = $user_rate == 1 ? 'user' : 'users'; 
                  $textRatingText = $albumData->rating == 1 ? 'rating' : 'ratings'; ?>
                <span title="<?php echo $albumData->rating.' '.$this->translate($textRatingText.' by').' '.$user_rate.' '.$this->translate($textuserRating); ?>">
                  <?php if( $albumData->rating > 0 ): ?>
                    <?php for( $x=1; $x<= $albumData->rating; $x++ ): ?>
                      <span class="sesbasic_rating_star_small fa fa-star"></span>
                    <?php endfor; ?>
                    <?php if( (round($albumData->rating) - $albumData->rating) > 0): ?>
                      <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                    <?php endif; ?>
                  <?php endif; ?> 
                </span>
             	<?php } ?>
            </div>
						<div class="sesalbum_categories_albums_item_cont_btm sesbasic_custom_scroll">
              <?php if(isset($this->descriptionActive)){ ?>
                <div class="sesalbum_list_des clear">
                <?php if(strlen($albumData->description) > $this->description_truncation){
                          $description = mb_substr($albumData->description,0,$this->description_truncation).'...';
                         }else{ ?>
                  <?php $description = $albumData->description; ?>
                    <?php } ?>
                    <?php echo $description; ?>
                </div>
              <?php } ?>
             	<?php if(isset($this->resultArray['photos'][$item->category_id])){ ?>
                <div class="sesalbum_categories_albums_photos clear sesbasic_clearfix">
                 <?php foreach($this->resultArray['photos'][$item->category_id] as $photoData){ ?>
                 <?php
                   	$imageURL = Engine_Api::_()->sesalbum()->getImageViewerHref($photoData);
                  ?>
                  <div class="sesalbum_thumb" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
                    <a href="<?php echo Engine_Api::_()->sesalbum()->getHrefPhoto($photoData->getIdentity(),$photoData->album_id); ?>" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $photoData->getPhotoUrl(); ?>','<?php echo $imageURL	; ?>')" class="sesalbum_thumb_img ses-image-viewer"> 
                    <span style="background-image:url(<?php echo $photoData->getPhotoUrl('thumb.normalmain'); ?>);"></span> </a> 
                  </div>
                 <?php } ?>
            		</div>
          		<?php } ?>
						</div>
          </div>
        </div>
      <?php for($i=2;$i<$counter;$i++){ ?>
      		<div class="sesalbum_categories_albums_item sesbasic_clearfix clear nodata" style="display:none;"></div>
      <?php } ?>
      	<?php if($counter>2){ ?>
        <div class="sesalbum_categories_albums_btns">
        	<a href="javascript:;" class="prevbtn seschanel_slideshow_prev"><i class="fa fa-angle-left sesbasic_text_light"></i></a>
          <a href="javascript:;" class="nxtbtn seschanel_slideshow_next"><i class="fa fa-angle-right sesbasic_text_light"></i></a>
        </div>
        <?php } ?>
      </div>
			<?php } ?>
    <?php }else{ ?>
    <?php if(isset($this->resultArray['album_data'][$item->category_id])){
          $changeClass = 0;
     ?>
    <?php	foreach($this->resultArray['album_data'][$item->category_id] as $itemAlbum){ 
      $href = $itemAlbum->getHref();
     $imageURL = $itemAlbum->getPhotoUrl('thumb.normalmain');
    ?>
   <div class="sesalbum_albumlist_column_<?php echo $changeClass == 0 ? 'big' : 'small'; ?> floatL">
    <div class="sesalbum_cat_album_list">
       <div class="sesalbum_thumb">
        <a href="<?php echo $href; ?>" class="sesalbum_thumb_img">
          <span class="sesalbum_animation" style="background-image:url(<?php echo $imageURL; ?>);"></span>
         <?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive)){ ?>
          <p class="sesalbum_labels">
          <?php if(isset($this->featuredLabelActive) && $itemAlbum->is_featured == 1){ ?>
            <span class="sesalbum_label_featured"><?php echo $this->translate("Featured"); ?></span>
          <?php } ?>
          <?php if(isset($this->sponsoredLabelActive) && $itemAlbum->is_sponsored == 1){ ?>
            <span class="sesalbum_label_sponsored"><?php echo $this->translate("Sponsored"); ?></span>
          <?php } ?>
          </p>
          <?php } ?>
          <div class="sesalbum_cat_album_list_info sesalbum_animation">
            <div>
              <div class="sesalbum_cat_album_list_content">
              <?php if(isset($this->titleActive)){ ?>
                <div class="sesalbum_cat_album_list_title">
                  <?php echo $itemAlbum->getTitle(); ?>
                </div>
                <?php } ?>
                <?php if(isset($this->byActive)){ ?>
                <div class="sesalbum_cat_album_list_stats">
                  <?php
                    $owner = $itemAlbum->getOwner();
                    echo $this->translate('Posted by %1$s', $owner->getTitle());
                  ?>
                </div>
                <?php } ?>
                <div class="sesalbum_cat_album_list_stats sesalbum_list_stats sesbasic_text_light">
                  <?php if(isset($this->likeActive) && isset($itemAlbum->like_count)) { ?>
                    <span title="<?php echo $this->translate(array('%s like', '%s likes', $itemAlbum->like_count), $this->locale()->toNumber($itemAlbum->like_count)); ?>"><i class="fa fa-thumbs-up"></i><?php echo $itemAlbum->like_count; ?></span>
                  <?php } ?>
                  <?php if(isset($this->commentActive) && isset($itemAlbum->comment_count)) { ?>
                    <span title="<?php echo $this->translate(array('%s comment', '%s comments', $itemAlbum->comment_count), $this->locale()->toNumber($itemAlbum->comment_count))?>"><i class="fa fa-comment"></i><?php echo $itemAlbum->comment_count;?></span>
                  <?php } ?>
                  <?php if(isset($this->viewActive) && isset($itemAlbum->view_count)) { ?>
                    <span title="<?php echo $this->translate(array('%s view', '%s views', $itemAlbum->view_count), $this->locale()->toNumber($itemAlbum->view_count))?>"><i class="fa fa-eye"></i><?php echo $itemAlbum->view_count; ?></span>
                  <?php } ?>
                   <?php  if(isset($this->favouriteActive) && isset($itemAlbum->favourite_count)) { ?>
                    <span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $itemAlbum->favourite_count), $this->locale()->toNumber($itemAlbum->favourite_count))?>"><i class="fa fa-heart"></i><?php echo $itemAlbum->favourite_count; ?></span>
                  <?php } ?>
                  <?php  if(isset($this->albumCountActive)) { ?>
                    <span title="<?php echo $this->translate(array('%s photo', '%s photos', $itemAlbum->count()), $this->locale()->toNumber($itemAlbum->count()))?>"><i class="fa fa-photo"></i><?php echo $itemAlbum->count(); ?></span>
                  <?php } ?>
                </div>
                <?php if(isset($this->ratingActive) && $ratingShowAlbum && isset($itemAlbum->rating) && $itemAlbum->rating > 0 ): ?>
                  <?php
                    $user_rate = Engine_Api::_()->getDbtable('ratings', 'sesalbum')->getCountUserRate('album',$itemAlbum->album_id);
                    $textuserRating = $user_rate == 1 ? 'user' : 'users'; 
                    $textRatingText = $itemAlbum->rating == 1 ? 'rating' : 'ratings'; ?>
                   <div class="sesalbum_grid_date sesbasic_text_light clear" title="<?php echo $itemAlbum->rating.' '.$this->translate($textRatingText.' by').' '.$user_rate.' '.$this->translate($textuserRating); ?>">
                    <?php for( $x=1; $x<=$itemAlbum->rating; $x++ ): ?>
                      <span class="sesbasic_rating_star_small fa fa-star"></span>
                    <?php endfor; ?>
                    <?php if( (round($itemAlbum->rating) - $itemAlbum->rating) > 0): ?>
                      <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                    <?php endif; ?>

                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </a>
    </div>
    </div>
</div>          
          <?php 
          $changeClass++;
          }
          $changeClass = 0;
           ?>
      <?php } ?>
    <?php } ?>
    </div>    
 <?php 
 		$albumData = '';
 		endforeach;
     if($this->paginatorCategory->getTotalItemCount() == 0 && !$this->is_ajax){  ?>
     <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has created an album yet.');?>
        <?php if ($this->can_create):?>
          <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action' => 'create','module'=>'sesalbum'), "sesalbum_general",true).'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
		<?php } 
    if($this->loadOptionData == 'pagging'){ ?>
 		 <?php echo $this->paginationControl($this->paginatorCategory, null, array("_pagging.tpl", "sesalbum"),array('identityWidget'=>$randonNumber)); ?>
 <?php } ?>
 <?php if(!$this->is_ajax){ ?>
  </div>
  	<?php if($this->loadOptionData != 'pagging'){ ?>
    <div class="sesbasic_view_more" id="view_more_<?php echo $randonNumber; ?>" onclick="viewMore_<?php echo $randonNumber; ?>();" > <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => "feed_viewmore_link_$randonNumber", 'class' => 'buttonlink icon_viewmore')); ?> </div>
  <div class="sesbasic_view_more_loading" id="loading_image_<?php echo $randonNumber; ?>" style="display: none;"> <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sesbasic/externals/images/loading.gif" /> </div>
  <?php } ?>
  <?php } ?>
 <?php if(!$this->is_ajax){ ?>
<script type="text/javascript">
<?php if($this->view_type == 0){ ?>
function dynamicWidth(){
	var objectClass = jqueryObjectOfSes('.sesalbum_cat_album_list_info');
	for(i=0;i<objectClass.length;i++){
			jqueryObjectOfSes(objectClass[i]).find('div').find('.sesalbum_cat_album_list_content').find('.sesalbum_cat_album_list_title').width(jqueryObjectOfSes(objectClass[i]).width());
	}
}
dynamicWidth();
<?php } ?>
sesJqueryObject (document).on('click','.seschanel_slideshow_prev',function(e){
		e.preventDefault();
		var activeClassIndex;
		var elem = sesJqueryObject (this).parent().parent().parent().find('.sesalbum_categories_albums_listing_thumbnails').children();
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
		sesJqueryObject (this).parent().parent().parent().find('.sesalbum_categories_albums_listing_thumbnails').children().eq(changeIndex).find('a').click();
});
sesJqueryObject (document).on('click','.seschanel_slideshow_next',function(e){
	e.preventDefault();
	var activeClassIndex;
	var elem = sesJqueryObject (this).parent().parent().parent().find('.sesalbum_categories_albums_listing_thumbnails').children();
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
	sesJqueryObject (this).parent().parent().parent().find('.sesalbum_categories_albums_listing_thumbnails').children().eq(changeIndex).find('a').click();
});
sesJqueryObject (document).on('click','.slideshow_album_data',function(e){
	e.preventDefault();
	var album_id = sesJqueryObject (this).attr('data-url');
	if(sesJqueryObject (this).parent().hasClass('thumbnail_active')){
			return false;
	}
	if(!album_id)
		return false;
	 var elIndex = sesJqueryObject (this).parent().index();
	 var totalDiv = sesJqueryObject (this).parent().parent().find('div');
	 for(i=0;i<totalDiv.length;i++){
			 totalDiv[i].removeClass('thumbnail_active');
	 }
	 sesJqueryObject (this).parent().addClass('thumbnail_active');
	 var containerElem = sesJqueryObject (this).parent().parent().parent().find('.sesalbum_categories_albums_conatiner').children();
	 for(i=0;i<containerElem.length;i++){
	 	if(i != (containerElem.length-1))
			containerElem[i].hide();
	 }
	sesJqueryObject (containerElem).get(elIndex).show();
	if(sesJqueryObject (containerElem).get(elIndex).hasClass('nodata')){
	 sesJqueryObject (containerElem).eq(elIndex).html('<div class="sesbasic_loading_cont_overlay"></div>');
	 new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + "sesalbum/category/album-data/album_id/"+album_id,
      'data': {
        format: 'html',
				params:'<?php echo json_encode($this->params); ?>',
				album_id : sesJqueryObject (this).attr('data-url'),
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        sesJqueryObject (containerElem).eq(elIndex).html(responseHTML);
				sesJqueryObject (containerElem).eq(elIndex).removeClass('nodata');
				jqueryObjectOfSes (".sesbasic_custom_scroll").mCustomScrollbar({
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
		 sesJqueryObject (window).scroll( function() {
			  var heightOfContentDiv_<?php echo $randonNumber; ?> = sesJqueryObject ('#scrollHeightDivSes_<?php echo $randonNumber; ?>').offset().top;
        var fromtop_<?php echo $randonNumber; ?> = sesJqueryObject (this).scrollTop();
        if(fromtop_<?php echo $randonNumber; ?> > heightOfContentDiv_<?php echo $randonNumber; ?> - 100 && sesJqueryObject ('#view_more_<?php echo $randonNumber; ?>').css('display') == 'block' ){
						document.getElementById('feed_viewmore_link_<?php echo $randonNumber; ?>').click();
				}
     });
	});
<?php } ?>
function paggingNumber<?php echo $randonNumber; ?>(pageNum){
	 sesJqueryObject ('.overlay_<?php echo $randonNumber ?>').css('display','block');
    (new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + "widget/index/mod/sesalbum/name/<?php echo $this->widgetName; ?>",
      'data': {
        format: 'html',
        page: pageNum,    
				params :'<?php echo json_encode($this->params); ?>', 
				is_ajax : 1,
				identity : '<?php echo $randonNumber; ?>',
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				sesJqueryObject ('.overlay_<?php echo $randonNumber ?>').css('display','none');
        document.getElementById('scrollHeightDivSes_<?php echo $randonNumber; ?>').innerHTML =  responseHTML;
			<?php if($this->view_type == 1){ ?>
				jqueryObjectOfSes (".sesbasic_custom_scroll").mCustomScrollbar({
          theme:"minimal-dark"
        });
				<?php }else{ ?>
				dynamicWidth();
				<?php } ?>
      }
    })).send();
    return false;
}
</script>
<?php } ?>
<script type="text/javascript">
var defaultOpenTab ;
  viewMoreHide_<?php echo $randonNumber; ?>();
  function viewMoreHide_<?php echo $randonNumber; ?>() {
    if ($('view_more_<?php echo $randonNumber; ?>'))
      $('view_more_<?php echo $randonNumber; ?>').style.display = "<?php echo ($this->paginatorCategory->count() == 0 ? 'none' : ($this->paginatorCategory->count() == $this->paginatorCategory->getCurrentPageNumber() ? 'none' : '' )) ?>";
  }
  function viewMore_<?php echo $randonNumber; ?> (){
    var openTab_<?php echo $randonNumber; ?> = '<?php echo $this->defaultOpenTab; ?>';
    document.getElementById('view_more_<?php echo $randonNumber; ?>').style.display = 'none';
    document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = '';    
    (new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + "widget/index/mod/sesalbum/name/<?php echo $this->widgetName; ?>/openTab/" + openTab_<?php echo $randonNumber; ?>,
      'data': {
        format: 'html',
        page: <?php echo $this->page + 1; ?>,    
				params :'<?php echo json_encode($this->params); ?>', 
				is_ajax : 1,
				identity : '<?php echo $randonNumber; ?>',
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('scrollHeightDivSes_<?php echo $randonNumber; ?>').innerHTML = document.getElementById('scrollHeightDivSes_<?php echo $randonNumber; ?>').innerHTML + responseHTML;
				document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = 'none';
				<?php if($this->view_type == 1){ ?>
				jqueryObjectOfSes (".sesbasic_custom_scroll").mCustomScrollbar({
          theme:"minimal-dark"
        });
				<?php }else{ ?>
				dynamicWidth();
				<?php } ?>
      }
    })).send();
    return false;
  }
</script>