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
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesvideo/externals/styles/styles.css'); ?>  
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesvideo/externals/styles/styles.css'); ?>
<?php if(isset($this->identityForWidget) && !empty($this->identityForWidget)){
				$randonNumber = $this->identityForWidget;
      }else{
      	$randonNumber = $this->identity; 
      }
?>

<?php if(!$this->is_ajax){ ?>
	<div id="scrollHeightDivSes_<?php echo $randonNumber;?>" class="sesbasic_bxs"> 
    <div id="tabbed-widget_<?php echo $randonNumber; ?>">
  <?php } ?>
    <?php foreach( $this->paginatorCategory as $item): ?>
  	<div class="sesvideo_categories_videos_listing clear sesbasic_clearfix">
    	<div class="sesvideo_catbase_list_head clear sesbasic_clearfix">
      	<a class="sesbasic_linkinherit" href="<?php echo $item->getBrowseVideoHref(); ?>?category_id=<?php echo $item->category_id ?>" title="<?php echo $item->category_name; ?>"><?php echo $item->category_name; ?><?php if(isset($this->count_video) && $this->count_video == 1){ ?><?php echo "(".$item->total_videos_categories.")"; ?><?php } ?></a>
       <?php if(isset($this->seemore_text) && $this->seemore_text != ''){ ?>
          <span <?php echo $this->allignment_seeall == 'right' ?  'class="floatR"' : ''; ?> >
          	<a href="<?php echo $item->getBrowseVideoHref(); ?>?category_id=<?php echo $item->category_id ?>" title="<?php echo $item->category_name; ?>">
            <?php $seemoreTranslate = $this->translate($this->seemore_text); ?>
            <?php echo str_replace('[category_name]',$item->category_name,$seemoreTranslate); ?>
          </a>
         </span>
       <?php } ?>
      </div>
       <?php //rating show code
        $allowRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.video.rating',1);
        $allowShowPreviousRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.ratevideo.show',1);
        if($allowRating == 0){
          if($allowShowPreviousRating == 0)
            $ratingShow = false;
           else
            $ratingShow = true;
        }else
          $ratingShow = true;
       ?>
    <?php if(isset($this->resultArray['video_data'][$item->category_id])){
          $changeClass = 0;
     ?>
    <?php	foreach($this->resultArray['video_data'][$item->category_id] as $item){
          $href = $item->getHref();
       		$imageURL = $item->getPhotoUrl('thumb.normalmain');
    ?>
   <div class="sesvideo_videolist_column_<?php echo $changeClass == 0 ? 'big' : 'small'; ?> floatL">
    <div class="sesvideo_cat_video_list">
       <div class="sesvideo_thumb">
        <a href="<?php echo $href; ?>" data-url = "sesvideo_chanel" class="sesvideo_thumb_img">
          <span class="sesvideo_animation" style="background-image:url(<?php echo $imageURL; ?>);"></span>
         <?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive) || isset($this->hotLabelActive)){ ?>
          <p class="sesvideo_labels">
          <?php if(isset($this->featuredLabelActive) && $item->is_featured == 1){ ?>
            <span class="sesvideo_label_featured"><?php echo $this->translate("Featured"); ?></span>
          <?php } ?>
          <?php if(isset($this->sponsoredLabelActive) && $item->is_sponsored == 1){ ?>
            <span class="sesvideo_label_sponsored"><?php echo $this->translate("Sponsored"); ?></span>
          <?php } ?>
          <?php if(isset($this->hotLabelActive) && $item->is_hot == 1){ ?>
            <span class="sesvideo_label_hot"><?php echo $this->translate("Hot"); ?></span>
          <?php } ?>
          </p>
          <?php } ?>
          <div class="sesvideo_cat_video_list_info sesvideo_animation">
            <div>
              <div class="sesvideo_cat_video_list_content">
              <?php if(isset($this->titleActive)){ ?>
                <div class="sesvideo_cat_video_list_title">
                  <?php echo $item->getTitle(); ?>
                </div>
                <?php } ?>
                <?php if(isset($this->byActive)){ ?>
                <div class="sesvideo_cat_video_list_stats">
                  <?php
                    $owner = $item->getOwner();
                    echo $this->translate('Posted by %1$s', $owner->getTitle());
                  ?>
                </div>
                <?php } ?>
                <div class="sesvideo_cat_video_list_stats sesvideo_list_stats sesbasic_text_light">
                  <?php if(isset($this->likeActive) && isset($item->like_count)) { ?>
                    <span title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>"><i class="fa fa-thumbs-up"></i><?php echo $item->like_count; ?></span>
                  <?php } ?>
                  <?php if(isset($this->commentActive) && isset($item->comment_count)) { ?>
                    <span title="<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count))?>"><i class="fa fa-comment"></i><?php echo $item->comment_count;?></span>
                  <?php } ?>
                  <?php if(isset($this->viewActive) && isset($item->view_count)) { ?>
                    <span title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count))?>"><i class="fa fa-eye"></i><?php echo $item->view_count; ?></span>
                  <?php } ?>
                   <?php  if(isset($this->favouriteActive) && isset($item->favourite_count)) { ?>
                    <span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count))?>"><i class="fa fa-heart"></i><?php echo $item->favourite_count; ?></span>
                  <?php } ?>
                  <?php if(isset($this->ratingActive) && $ratingShow && isset($item->rating) && $item->rating > 0 ): ?>
                   <span  title="<?php echo $this->translate(array('%s rating', '%s ratings', round($item->rating,1)), $this->locale()->toNumber(round($item->rating,1)))?>">
                     <i class="fa fa-star"></i><?php echo round($item->rating,1).'/5';?>
                    </span>
                <?php endif; ?>
                </div>
                <?php if(isset($this->watchnowActive)){ ?>
                	<div class="sesvideo_cat_video_list_button"><?php echo $this->translate('Watch now'); ?></div>
               	<?php } ?>
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
    </div>    
 <?php  
 		endforeach;
     if($this->paginatorCategory->getTotalItemCount() == 0 && !$this->is_ajax){  ?>
     <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has created an video yet.');?>
        <?php if ($this->can_create):?>
          <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action' => 'create','module'=>'sesvideo'), "sesvideo_general",true).'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
		<?php } 
    if($this->loadOptionData == 'pagging'){ ?>
 		 <?php echo $this->paginationControl($this->paginatorCategory, null, array("_pagging.tpl", "sesvideo"),array('identityWidget'=>$randonNumber)); ?>
 <?php } ?>
 
<?php if(!$this->is_ajax){ ?>
  </div>
	</div>
   <?php if($this->loadOptionData != 'pagging') { ?>
  <div class="sesbasic_view_more" id="view_more_<?php echo $randonNumber; ?>" onclick="viewMore_<?php echo $randonNumber; ?>();" > <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => "feed_viewmore_link_$randonNumber", 'class' => 'buttonlink icon_viewmore')); ?> </div>
  <div class="sesbasic_view_more_loading" id="loading_image_<?php echo $randonNumber; ?>" style="display: none;"> <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sesbasic/externals/images/loading.gif" /> </div>
   <?php  } ?>
<?php } ?>
<script type="text/javascript">
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
    en4.core.request.send(new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + "widget/index/mod/sesvideo/name/<?php echo $this->widgetName; ?>/openTab/" + openTab_<?php echo $randonNumber; ?>,
      'data': {
        format: 'html',
        page: <?php echo $this->page + 1; ?>,    
				params :'<?php echo json_encode($this->params); ?>', 
				is_ajax : 1,
				identity : '<?php echo $randonNumber; ?>',
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML = document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML + responseHTML;
				dynamicWidth();
				document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = 'none';
				
      }
    }));
    return false;
  }
	<?php if(!$this->is_ajax){ ?>
function dynamicWidth(){
	var objectClass = sesJqueryObject('.sesvideo_cat_video_list_info');
	for(i=0;i<objectClass.length;i++){
			sesJqueryObject(objectClass[i]).find('div').find('.sesvideo_cat_video_list_content').find('.sesvideo_cat_video_list_title').width(sesJqueryObject(objectClass[i]).width());
	}
}
dynamicWidth();
<?php } ?>
</script>
<?php if(!$this->is_ajax){ ?>
<script type="application/javascript">
function paggingNumber<?php echo $randonNumber; ?>(pageNum){
	 sesJqueryObject('.sesbasic_loading_cont_overlay').css('display','block');
	 var openTab_<?php echo $randonNumber; ?> = '<?php echo $this->defaultOpenTab; ?>';
    en4.core.request.send(new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + "widget/index/mod/sesvideo/name/<?php echo $this->widgetName; ?>/openTab/" + openTab_<?php echo $randonNumber; ?>,
      'data': {
        format: 'html',
        page: pageNum,    
				params :'<?php echo json_encode($this->params); ?>', 
				is_ajax : 1,
				identity : '<?php echo $randonNumber; ?>',
				type:'<?php echo $this->view_type; ?>'
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				sesJqueryObject('.sesbasic_loading_cont_overlay').css('display','none');
        document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML =  responseHTML;
				dynamicWidth();
      }
    }));
    return false;
}
</script>
<?php } ?>