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
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/customscrollbar.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/customscrollbar.concat.min.js'); ?>
<?php if(isset($this->identityForWidget) && !empty($this->identityForWidget)){
				$randonNumber = $this->identityForWidget;
      }else{
      	$randonNumber = $this->identity; 
      }
?>
<?php if(!$this->is_ajax){ ?>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<?php if(isset($this->category->thumbnail) && !empty($this->category->thumbnail)){ ?>
  <div class="sesvideo_category_cover sesbasic_bxs sesbm">
    <div class="sesvideo_category_cover_inner" style="background-image:url(<?php echo  Engine_Api::_()->storage()->get($this->category->thumbnail)->getPhotoUrl('thumb.thumb'); ?>);">
      <div class="sesvideo_category_cover_content">
        <div class="sesvideo_category_cover_breadcrumb">
          <!--breadcrumb -->
          <a href="<?php echo $this->url(array('action' => 'browse'), "sesvideo_category"); ?>"><?php echo $this->translate("Categories"); ?></a>&nbsp;&raquo;
          <?php if(isset($this->breadcrumb['category'][0]->category_id)){ ?>
             <?php if($this->breadcrumb['subcategory']) { ?>
              <a href="<?php echo $this->breadcrumb['category'][0]->getHref(); ?>"><?php echo $this->translate($this->breadcrumb['category'][0]->category_name) ?></a>
             <?php }else{ ?>
               <?php echo $this->translate($this->breadcrumb['category'][0]->category_name) ?>
             <?php } ?>
             <?php if($this->breadcrumb['subcategory']) echo "&nbsp;&raquo"; ?>
          <?php } ?>
          <?php if(isset($this->breadcrumb['subcategory'][0]->category_id)){ ?>
            <?php if($this->breadcrumb['subSubcategory']) { ?>
              <a href="<?php echo $this->breadcrumb['subcategory'][0]->getHref(); ?>"><?php echo $this->translate($this->breadcrumb['subcategory'][0]->category_name) ?></a>
            <?php }else{ ?>
              <?php echo $this->translate($this->breadcrumb['subcategory'][0]->category_name) ?>
            <?php } ?>
            <?php if($this->breadcrumb['subSubcategory']) echo "&nbsp;&raquo"; ?>
          <?php } ?>
          <?php if(isset($this->breadcrumb['subSubcategory'][0]->category_id)){ ?>
            <?php echo $this->translate($this->breadcrumb['subSubcategory'][0]->category_name) ?>
          <?php } ?>
        </div>
        <div class="sesvideo_category_cover_blocks">
          <div class="sesvideo_category_cover_block_img">
            <span style="background-image:url(<?php echo  Engine_Api::_()->storage()->get($this->category->thumbnail)->getPhotoUrl('thumb.thumb'); ?>);"></span>
          </div>
          <div class="sesvideo_category_cover_block_info">
            <?php if(isset($this->category->title) && !empty($this->category->title)): ?>
              <div class="sesvideo_category_cover_title"> 
                <?php echo $this->category->title; ?>
              </div>
            <?php endif; ?>
            <?php if(isset($this->category->description) && !empty($this->category->description)): ?>
              <div class="sesvideo_category_cover_des clear sesbasic_custom_scroll">
                <?php echo $this->category->description;?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>  
<?php } else { ?>
  <div class="sesvide_breadcrumb clear sesbasic_clearfix">
    <!--breadcrumb -->
    <a href="<?php echo $this->url(array('action' => 'browse'), "sesvideo_category"); ?>"><?php echo $this->translate("Categories"); ?></a>&nbsp;&raquo;
    <?php if(isset($this->breadcrumb['category'][0]->category_id)){ ?>
       <?php if($this->breadcrumb['subcategory']) { ?>
        <a href="<?php echo $this->breadcrumb['category'][0]->getHref(); ?>"><?php echo $this->translate($this->breadcrumb['category'][0]->category_name) ?></a>
       <?php }else{ ?>
         <?php echo $this->translate($this->breadcrumb['category'][0]->category_name) ?>
       <?php } ?>
       <?php if($this->breadcrumb['subcategory']) echo "&nbsp;&raquo"; ?>
    <?php } ?>
    <?php if(isset($this->breadcrumb['subcategory'][0]->category_id)){ ?>
      <?php if($this->breadcrumb['subSubcategory']) { ?>
        <a href="<?php echo $this->breadcrumb['subcategory'][0]->getHref(); ?>"><?php echo $this->translate($this->breadcrumb['subcategory'][0]->category_name) ?></a>
      <?php }else{ ?>
        <?php echo $this->translate($this->breadcrumb['subcategory'][0]->category_name) ?>
      <?php } ?>
      <?php if($this->breadcrumb['subSubcategory']) echo "&nbsp;&raquo"; ?>
    <?php } ?>
    <?php if(isset($this->breadcrumb['subSubcategory'][0]->category_id)){ ?>
      <?php echo $this->translate($this->breadcrumb['subSubcategory'][0]->category_name) ?>
    <?php } ?>
  </div>
  <div class="sesvideo_browse_cat_top sesbm">
    <?php if(isset($this->category->title) && !empty($this->category->title)): ?>
      <div class="sesvideo_catview_title"> 
        <?php echo $this->category->title; ?>
      </div>
    <?php endif; ?>
    <?php if(isset($this->category->description) && !empty($this->category->description)): ?>
      <div class="sesvideo_catview_des">
        <?php echo $this->category->description;?>
      </div>
    <?php endif; ?>
  </div>
<?php } ?>
<!-- category subcategory -->
<?php if($this->show_subcat == 1 && count($this->innerCatData)>0){ ?>
  <div class="sesbasic_clearfix">
    <ul class="sesvideo_category_grid_listing sesbasic_clearfix clear sesbasic_bxs">	
      <?php foreach( $this->innerCatData as $item ):  ?>
        <li class="sesvideo_category_grid sesbm <?php echo $this->mouse_over_title ? 'sesvideo_category_grid_hover' : ''; ?>" style="height:<?php echo is_numeric($this->heightSubcat) ? $this->heightSubcat.'px' : $this->heightSubcat ?>;width:<?php echo is_numeric($this->widthSubcat) ? $this->widthSubcat.'px' : $this->widthSubcat ?>;">
          <a href="<?php echo $item->getHref(); ?>">
            <div class="sesvideo_category_grid_img">
              <?php if($item->thumbnail != '' && !is_null($item->thumbnail) && intval($item->thumbnail)){ ?>
                <span class="sesvideo_animation" style="background-image:url(<?php echo  Engine_Api::_()->storage()->get($item->thumbnail)->getPhotoUrl('thumb.thumb'); ?>);"></span>
              <?php } ?>
            </div>
            <div class="sesvideo_category_grid_overlay sesvideo_animation"></div>
            <div class="sesvideo_category_grid_info">
              <div>
                <div class="sesvideo_category_grid_title">
                  <?php if(isset($this->iconSubcatActive) && $item->cat_icon != '' && !is_null($item->cat_icon) && intval($item->cat_icon)){ ?>
                    <img src="<?php echo  Engine_Api::_()->storage()->get($item->cat_icon)->getPhotoUrl('thumb.icon'); ?>" />
                  <?php } ?>
                  <?php if(isset($this->titleSubcatActive)){ ?>
                  <span><?php echo $this->translate($item->category_name); ?></span>
                  <?php } ?>
                  <?php if(isset($this->countVideoSubcatActive)){ ?>
                    <span class="sesvideo_category_grid_stats sesvideo_animation"><?php echo $this->translate(array('%s video', '%s videos', $item->total_videos_categories), $this->locale()->toNumber($item->total_videos_categories))?></span>
                  <?php } ?>
                </div>
              </div>
            </div>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
   </div>
<?php } ?> 
<div class="sesvideo_subcat_list_head clear sesbasic_clearfix">
	<?php echo $this->translate($this->textVideo);?>
</div>
<div id="scrollHeightDivSes_<?php echo $randonNumber; ?>" class="sesbasic_clearfix sesbasic_bxs clear">    
   <ul class="sesvideo_cat_video_listing sesbasic_clearfix clear" id="tabbed-widget_<?php echo $randonNumber; ?>">
<?php } ?>
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
    <?php $totalCount = $this->paginator->getCurrentItemCount(); 
    			$allowedLimit = 5;
          $counter =1;
          $break = false;
          $type = 1;
          $close = false;
    ?>
    <?php foreach($this->paginator as $key=>$video){  ?>
    	<?php
        $href = $video->getHref();
        $imageURL = $video->getPhotoUrl();
      ?>
      <?php if(($this->paginator->getCurrentPageNumber() == 1 || $this->loadOptionData == 'pagging') && !$break && $totalCount >= $allowedLimit ){ ?>
			<?php if(($counter-1)%5 == 0 ){ ?>
        <div class="sesbasic_clearfix sesbasic_bxs clear">
      		<div class="sesvideo_videolist_row clear sesbasic_clearfix">
      <?php } ?>
						<?php if($type == 1){ ?>
               <?php if(!$close){  ?><div class="sesvideo_videolist_column_small floatL"> <?php } ?>
                    <div class="sesvideo_cat_video_list">
                  <div class="sesvideo_thumb">
                    <a href="<?php echo $href; ?>" data-url = "<?php echo $video->getType() ?>" class="sesvideo_thumb_img">
                      <span class="sesvideo_animation" style="background-image:url(<?php echo $imageURL; ?>);"></span>
                     <?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive) || isset($this->hotLabelActive)){ ?>
                      <p class="sesvideo_labels">
                      <?php if(isset($this->featuredLabelActive) && $video->is_featured == 1){ ?>
                        <span class="sesvideo_label_featured"><?php echo $this->translate('FEATURED'); ?></span>
                      <?php } ?>
                      <?php if(isset($this->sponsoredLabelActive) && $video->is_sponsored == 1){ ?>
                        <span class="sesvideo_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
                      <?php } ?>
                       <?php if(isset($this->hotLabelActive) && $video->is_hot == 1){ ?>
                        <span class="sesvideo_label_hot"><?php echo $this->translate("HOT"); ?></span>
                      <?php } ?>
                      </p>
                      <?php } ?>
                      <div class="sesvideo_cat_video_list_info sesvideo_animation">
                        <div>
                          <div class="sesvideo_cat_video_list_content">
                          <?php if(isset($this->titleActive)){ ?>
                            <div class="sesvideo_cat_video_list_title">
                              <?php echo $video->getTitle(); ?>
                            </div>
                            <?php } ?>
                            <?php if(isset($this->byActive)){ ?>
                            <div class="sesvideo_cat_video_list_stats">
                              <?php
                                $owner = $video->getOwner();
                                echo $this->translate('Posted by %1$s', $owner->getTitle());
                              ?>
                            </div>
                            <?php } ?>
                            <div class="sesvideo_cat_video_list_stats sesvideo_list_stats sesbasic_text_light">
                              <?php if(isset($this->likeActive) && isset($video->like_count)) { ?>
                                <span title="<?php echo $this->translate(array('%s like', '%s likes', $video->like_count), $this->locale()->toNumber($video->like_count)); ?>"><i class="fa fa-thumbs-up"></i><?php echo $video->like_count; ?></span>
                              <?php } ?>
                              <?php if(isset($this->commentActive) && isset($video->comment_count)) { ?>
                                <span title="<?php echo $this->translate(array('%s comment', '%s comments', $video->comment_count), $this->locale()->toNumber($video->comment_count))?>"><i class="fa fa-comment"></i><?php echo $video->comment_count;?></span>
                              <?php } ?>
                              <?php if(isset($this->viewActive) && isset($video->view_count)) { ?>
                                <span title="<?php echo $this->translate(array('%s view', '%s views', $video->view_count), $this->locale()->toNumber($video->view_count))?>"><i class="fa fa-eye"></i><?php echo $video->view_count; ?></span>
                              <?php } ?>
                              <?php if(isset($this->favouriteActive) && isset($video->favourite_count)) { ?>
                                <span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $video->favourite_count), $this->locale()->toNumber($video->favourite_count))?>"><i class="fa fa-heart"></i><?php echo $video->favourite_count; ?></span>
                              <?php } ?>
                              <?php if(isset($this->ratingActive) && $ratingShow && isset($video->rating) && $video->rating > 0 ): ?>
                              <span  title="<?php echo $this->translate(array('%s rating', '%s ratings', round($video->rating,1)), $this->locale()->toNumber(round($video->rating,1)))?>">
                               <i class="fa fa-star"></i><?php echo round($video->rating,1).'/5';?>
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
               <?php if($close){ $close = false;  ?></div> <?php }else{ $close = true;  }   ?>
            <?php } ?>
						<?php if($type == 2){ ?>
             <div class="sesvideo_videolist_column_big floatL">
             		<div class="sesvideo_cat_video_list">
                   <div class="sesvideo_thumb">
                    <a href="<?php echo $href; ?>" data-url = "<?php echo $video->getType() ?>" class="sesvideo_thumb_img">
                      <span class="sesvideo_animation" style="background-image:url(<?php echo $imageURL; ?>);"></span>
                     <?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive) || isset($this->hotLabelActive)){ ?>
                      <p class="sesvideo_labels">
                      <?php if(isset($this->featuredLabelActive) && $video->is_featured == 1){ ?>
                        <span class="sesvideo_label_featured"><?php echo $this->translate('FEATURED'); ?></span>
                      <?php } ?>
                      <?php if(isset($this->sponsoredLabelActive) && $video->is_sponsored == 1){ ?>
                        <span class="sesvideo_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
                      <?php } ?>
                      <?php if(isset($this->hotLabelActive) && $video->is_hot == 1){ ?>
                        <span class="sesvideo_label_hot"><?php echo $this->translate("HOT"); ?></span>
                      <?php } ?>
                      </p>
                      <?php } ?>
                      <div class="sesvideo_cat_video_list_info sesvideo_animation">
                        <div>
                          <div class="sesvideo_cat_video_list_content">
                          <?php if(isset($this->titleActive)){ ?>
                            <div class="sesvideo_cat_video_list_title">
                              <?php echo $video->getTitle(); ?>
                            </div>
                            <?php } ?>
                            <?php if(isset($this->byActive)){ ?>
                            <div class="sesvideo_cat_video_list_stats">
                              <?php
                                $owner = $video->getOwner();
                                echo $this->translate('Posted by %1$s', $owner->getTitle());
                              ?>
                            </div>
                            <?php } ?>
                            <div class="sesvideo_cat_video_list_stats sesvideo_list_stats sesbasic_text_light">
                              <?php if(isset($this->likeActive) && isset($video->like_count)) { ?>
                                <span title="<?php echo $this->translate(array('%s like', '%s likes', $video->like_count), $this->locale()->toNumber($video->like_count)); ?>"><i class="fa fa-thumbs-up"></i><?php echo $video->like_count; ?></span>
                              <?php } ?>
                              <?php if(isset($this->commentActive) && isset($video->comment_count)) { ?>
                                <span title="<?php echo $this->translate(array('%s comment', '%s comments', $video->comment_count), $this->locale()->toNumber($video->comment_count))?>"><i class="fa fa-comment"></i><?php echo $video->comment_count;?></span>
                              <?php } ?>
                              <?php if(isset($this->viewActive) && isset($video->view_count)) { ?>
                                <span title="<?php echo $this->translate(array('%s view', '%s views', $video->view_count), $this->locale()->toNumber($video->view_count))?>"><i class="fa fa-eye"></i><?php echo $video->view_count; ?></span>
                              <?php } ?>
                              <?php if(isset($this->favouriteActive) && isset($video->favourite_count)) { ?>
                                <span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $video->favourite_count), $this->locale()->toNumber($video->favourite_count))?>"><i class="fa fa-heart"></i><?php echo $video->favourite_count; ?></span>
                              <?php } ?>
                              <?php if(isset($this->ratingActive) && $ratingShow && isset($video->rating) && $video->rating > 0 ): ?>
                              <span  title="<?php echo $this->translate(array('%s rating', '%s ratings', round($video->rating,1)), $this->locale()->toNumber(round($video->rating,1)))?>">
                               <i class="fa fa-star"></i><?php echo round($video->rating,1).'/5';?>
                              </span>
                            <?php endif; ?>
                            </div>
                            <div class="sesvideo_cat_video_list_button"><?php echo $this->translate('Watch now'); ?></div>
                          </div>
                        </div>
                      </div>
                    </a>
                </div>
                </div>
            </div>
            <?php } ?>
     <?php if(($counter)%5 == 0){ ?>
           </div>
         </div>
		<?php } ?>
      <?php
      	if($counter == 2 || $counter == 9 || $counter == 10) $type = 2;
       	else $type = 1;
        ?>
      <?php if($counter%5 == 0){
              $allowedLimit = $allowedLimit + 5;
            }
             if($counter%15 == 0){
              $break = true;
              }
      ?>
      <?php }else{ ?>
   					   <li class="sesvideo_cat_video_list" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
                <div class="sesvideo_thumb">
                    <a href="<?php echo $href; ?>" data-url = "<?php echo $video->getType() ?>" class="sesvideo_thumb_img">
                      <span class="sesvideo_animation" style="background-image:url(<?php echo $imageURL; ?>);"></span>
                     <?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive) || isset($this->hotLabelActive)){ ?>
                      <p class="sesvideo_labels">
                      <?php if(isset($this->featuredLabelActive) && $video->is_featured == 1){ ?>
                        <span class="sesvideo_label_featured"><?php echo $this->translate('FEATURED'); ?></span>
                      <?php } ?>
                      <?php if(isset($this->sponsoredLabelActive) && $video->is_sponsored == 1){ ?>
                        <span class="sesvideo_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
                      <?php } ?>
                      <?php if(isset($this->hotLabelActive) && $video->is_hot == 1){ ?>
                        <span class="sesvideo_label_hot"><?php echo $this->translate("HOT"); ?></span>
                      <?php } ?>
                      </p>
                      <?php } ?>
                      <div class="sesvideo_cat_video_list_info sesvideo_animation">
                        <div>
                          <div class="sesvideo_cat_video_list_content">
                          <?php if(isset($this->titleActive)){ ?>
                            <div class="sesvideo_cat_video_list_title">
                              <?php echo $video->getTitle(); ?>
                            </div>
                            <?php } ?>
                            <?php if(isset($this->byActive)){ ?>
                            <div class="sesvideo_cat_video_list_stats">
                              <?php
                                $owner = $video->getOwner();
                                echo $this->translate('Posted by %1$s', $owner->getTitle());
                              ?>
                            </div>
                            <?php } ?>
                            <div class="sesvideo_cat_video_list_stats sesvideo_list_stats sesbasic_text_light">
                              <?php if(isset($this->likeActive) && isset($video->like_count)) { ?>
                                <span title="<?php echo $this->translate(array('%s like', '%s likes', $video->like_count), $this->locale()->toNumber($video->like_count)); ?>"><i class="fa fa-thumbs-up"></i><?php echo $video->like_count; ?></span>
                              <?php } ?>
                              <?php if(isset($this->commentActive) && isset($video->comment_count)) { ?>
                                <span title="<?php echo $this->translate(array('%s comment', '%s comments', $video->comment_count), $this->locale()->toNumber($video->comment_count))?>"><i class="fa fa-comment"></i><?php echo $video->comment_count;?></span>
                              <?php } ?>
                              <?php if(isset($this->viewActive) && isset($video->view_count)) { ?>
                                <span title="<?php echo $this->translate(array('%s view', '%s views', $video->view_count), $this->locale()->toNumber($video->view_count))?>"><i class="fa fa-eye"></i><?php echo $video->view_count; ?></span>
                              <?php } ?>
                              <?php if(isset($this->favouriteActive) && isset($video->favourite_count)) { ?>
                                <span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $video->favourite_count), $this->locale()->toNumber($video->favourite_count))?>"><i class="fa fa-heart"></i><?php echo $video->favourite_count; ?></span>
                              <?php } ?>
                              <?php if(isset($this->ratingActive) && $ratingShow && isset($video->rating) && $video->rating > 0 ): ?>
                              <span  title="<?php echo $this->translate(array('%s rating', '%s ratings', round($video->rating,1)), $this->locale()->toNumber(round($video->rating,1)))?>">
                               <i class="fa fa-star"></i><?php echo round($video->rating,1).'/5';?>
                              </span>
                            <?php endif; ?>
                            </div>
                            <div class="sesvideo_cat_video_list_button"><?php echo $this->translate('Watch now'); ?></div>
                          </div>
                        </div>
                      </div>
                    </a>
                </div>
      </li>
    	  <?php }
   		 $counter ++;
    } ?>   
    <?php  if(  count($this->paginator) == 0){  ?>
      <div class="tip">
        <span>
        	<?php echo $this->translate("No video in this  category."); ?>
          <?php if (!$this->can_edit):?>
                    <?php echo $this->translate('Be the first to %1$spost%2$s one in this category!', '<a href="'.$this->url(array('action' => 'create'), "sesvideo_general").'">', '</a>'); ?>
          <?php endif; ?>
        </span>
      </div>
    <?php } ?>    
    <?php
          if($this->loadOptionData == 'pagging'){ ?>
 		 <?php echo $this->paginationControl($this->paginator, null, array("_pagging.tpl", "sesvideo"),array('identityWidget'=>$randonNumber)); ?>
 <?php } ?>
<?php if(!$this->is_ajax){ ?> 
 </ul>
 </div>
 <?php if($this->loadOptionData != 'pagging'){ ?>
  <div class="sesbasic_view_more" id="view_more_<?php echo $randonNumber; ?>" onclick="viewMore_<?php echo $randonNumber; ?>();" > <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => "feed_viewmore_link_$randonNumber", 'class' => 'buttonlink icon_viewmore')); ?> </div>
  <div class="sesbasic_view_more_loading" id="loading_image_<?php echo $randonNumber; ?>" style="display: none;"> <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sesbasic/externals/images/loading.gif" /> </div>
  <?php } ?>
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
				params :<?php echo json_encode($this->params); ?>, 
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
      $('view_more_<?php echo $randonNumber; ?>').style.display = "<?php echo ($this->paginator->count() == 0 ? 'none' : ($this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' )) ?>";
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
				params :<?php echo json_encode($this->params); ?>, 
				is_ajax : 1,
				identity : '<?php echo $randonNumber; ?>',
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML = document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML + responseHTML;
				document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = 'none';
				dynamicWidth();
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