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
  <div class="sesalbum_category_cover sesbasic_bxs sesbm">
    <div class="sesalbum_category_cover_inner" style="background-image:url(<?php echo  Engine_Api::_()->storage()->get($this->category->thumbnail)->getPhotoUrl('thumb.thumb'); ?>);">
      <div class="sesalbum_category_cover_content">
        <div class="sesalbum_category_cover_breadcrumb">
          <!--breadcrumb -->
          <a href="<?php echo $this->url(array('action' => 'browse'), "sesalbum_category"); ?>"><?php echo $this->translate("Categories"); ?></a>&nbsp;&raquo;
          <?php if(isset($this->breadcrumb['category'][0]->category_id)){ ?>
             <?php if($this->breadcrumb['subcategory']) { ?>
              <a href="<?php echo $this->breadcrumb['category'][0]->getHref(); ?>"><?php echo $this->breadcrumb['category'][0]->category_name ?></a>
             <?php }else{ ?>
               <?php echo $this->breadcrumb['category'][0]->category_name ?>
             <?php } ?>
             <?php if($this->breadcrumb['subcategory']) echo "&nbsp;&raquo"; ?>
          <?php } ?>
          <?php if(isset($this->breadcrumb['subcategory'][0]->category_id)){ ?>
            <?php if($this->breadcrumb['subSubcategory']) { ?>
              <a href="<?php echo $this->breadcrumb['subcategory'][0]->getHref(); ?>"><?php echo $this->breadcrumb['subcategory'][0]->category_name ?></a>
            <?php }else{ ?>
              <?php echo $this->breadcrumb['subcategory'][0]->category_name ?>
            <?php } ?>
            <?php if($this->breadcrumb['subSubcategory']) echo "&nbsp;&raquo"; ?>
          <?php } ?>
          <?php if(isset($this->breadcrumb['subSubcategory'][0]->category_id)){ ?>
            <?php echo $this->breadcrumb['subSubcategory'][0]->category_name ?>
          <?php } ?>
        </div>
        <div class="sesalbum_category_cover_blocks">
          <div class="sesalbum_category_cover_block_img">
            <span style="background-image:url(<?php echo  Engine_Api::_()->storage()->get($this->category->thumbnail)->getPhotoUrl(''); ?>);"></span>
          </div>
          <div class="sesalbum_category_cover_block_info">
            <?php if(isset($this->category->title) && !empty($this->category->title)): ?>
              <div class="sesalbum_category_cover_title"> 
                <?php echo $this->category->title; ?>
              </div>
            <?php endif; ?>
            <?php if(isset($this->category->description) && !empty($this->category->description)): ?>
              <div class="sesalbum_category_cover_des clear sesbasic_custom_scroll">
                <?php echo nl2br($this->category->description);?>
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
    <a href="<?php echo $this->url(array('action' => 'browse'), "sesalbum_category"); ?>"><?php echo $this->translate("Categories"); ?></a>&nbsp;&raquo;
    <?php if(isset($this->breadcrumb['category'][0]->category_id)){ ?>
       <?php if($this->breadcrumb['subcategory']) { ?>
        <a href="<?php echo $this->breadcrumb['category'][0]->getHref(); ?>"><?php echo $this->breadcrumb['category'][0]->category_name ?></a>
       <?php }else{ ?>
         <?php echo $this->breadcrumb['category'][0]->category_name ?>
       <?php } ?>
       <?php if($this->breadcrumb['subcategory']) echo "&nbsp;&raquo"; ?>
    <?php } ?>
    <?php if(isset($this->breadcrumb['subcategory'][0]->category_id)){ ?>
      <?php if($this->breadcrumb['subSubcategory']) { ?>
        <a href="<?php echo $this->breadcrumb['subcategory'][0]->getHref(); ?>"><?php echo $this->breadcrumb['subcategory'][0]->category_name ?></a>
      <?php }else{ ?>
        <?php echo $this->breadcrumb['subcategory'][0]->category_name ?>
      <?php } ?>
      <?php if($this->breadcrumb['subSubcategory']) echo "&nbsp;&raquo"; ?>
    <?php } ?>
    <?php if(isset($this->breadcrumb['subSubcategory'][0]->category_id)){ ?>
      <?php echo $this->breadcrumb['subSubcategory'][0]->category_name ?>
    <?php } ?>
  </div>
  <div class="sesalbum_browse_cat_top sesbm">
    <?php if(isset($this->category->title) && !empty($this->category->title)): ?>
      <div class="sesalbum_catview_title"> 
        <?php echo $this->category->title; ?>
      </div>
    <?php endif; ?>
    <?php if(isset($this->category->description) && !empty($this->category->description)): ?>
      <div class="sesalbum_catview_des">
        <?php echo nl2br($this->category->description);?>
      </div>
    <?php endif; ?>
  </div>
<?php } ?>
<!-- category subcategory -->
<?php if($this->show_subcat == 1 && count($this->innerCatData)>0){ ?>
  <div class="sesbasic_clearfix">
    <ul class="sesalbum_category_grid_listing sesbasic_clearfix clear sesbasic_bxs">	
      <?php foreach( $this->innerCatData as $item ):  ?>
        <li class="sesalbum_category_grid sesbm" style="height:<?php echo is_numeric($this->heightSubcat) ? $this->heightSubcat.'px' : $this->heightSubcat ?>;width:<?php echo is_numeric($this->widthSubcat) ? $this->widthSubcat.'px' : $this->widthSubcat ?>;">
          <a href="<?php echo $item->getHref(); ?>">
            <div class="sesalbum_category_grid_img">
              <?php if($item->thumbnail != '' && !is_null($item->thumbnail) && intval($item->thumbnail)){ ?>
                <span class="sesalbum_animation" style="background-image:url(<?php echo  Engine_Api::_()->storage()->get($item->thumbnail)->getPhotoUrl('thumb.thumb'); ?>);"></span>
              <?php } ?>
            </div>
            <div class="sesalbum_category_grid_overlay sesalbum_animation"></div>
            <div class="sesalbum_category_grid_info">
              <div>
                <div class="sesalbum_category_grid_details">
                  <?php if(isset($this->iconSubcatActive) && $item->cat_icon != '' && !is_null($item->cat_icon) && intval($item->cat_icon)){ ?>
                    <img src="<?php echo  Engine_Api::_()->storage()->get($item->cat_icon)->getPhotoUrl('thumb.icon'); ?>" />
                  <?php } ?>
                  <?php if(isset($this->titleSubcatActive)){ ?>
                  <span><?php echo $item->category_name; ?></span>
                  <?php } ?>
                  <?php if(isset($this->countAlbumsSubcatActive)){ ?>
                    <span class="sesalbum_category_grid_stats"><?php echo $this->translate(array('%s album', '%s albums', $item->total_albums_categories), $this->locale()->toNumber($item->total_albums_categories))?></span>
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
<div id="scrollHeightDivSes_<?php echo $randonNumber; ?>" class="sesbasic_clearfix sesbasic_bxs clear">    
   <ul class="sesalbum_cat_album_listing sesbasic_clearfix clear" id="tabbed-widget_<?php echo $randonNumber; ?>">
<?php } ?>
    <?php //rating show code
         $allowRatingAlbum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.album.rating',1);
					$allowShowPreviousRatingAlbum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.ratealbum.show',1);
          if($allowRatingAlbum == 0){
          	if($allowShowPreviousRatingAlbum == 0)
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
    <?php foreach($this->paginator as $key=>$item){  ?>
    	 <?php
          $href = $item->getHref();
          $imageURL = $item->getPhotoUrl('thumb.normalmain');
      ?>
      <?php if(($this->paginator->getCurrentPageNumber() == 1 || $this->loadOptionData == 'pagging') && !$break && $totalCount >= $allowedLimit ){ ?>
			<?php if(($counter-1)%5 == 0 ){ ?>
        <li class="sesbasic_clearfix sesbasic_bxs clear">
      		<div class="sesalbum_albumlist_row clear sesbasic_clearfix">
      <?php } ?>
						<?php if($type == 1){ ?>
               <?php if(!$close){  ?><div class="sesalbum_albumlist_column_small floatL"> <?php } ?>
                    <div class="sesalbum_cat_album_list">
                     <div class="sesalbum_thumb">
                    <a href="<?php echo $href; ?>" class="sesalbum_thumb_img">
                      <span class="sesalbum_animation" style="background-image:url(<?php echo $imageURL; ?>);"></span>
                     <?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive)){ ?>
                      <p class="sesalbum_labels">
                      <?php if(isset($this->featuredLabelActive) && $item->is_featured == 1){ ?>
                        <span class="sesalbum_label_featured"><?php echo $this->translate("Featured"); ?></span>
                      <?php } ?>
                      <?php if(isset($this->sponsoredLabelActive) && $item->is_sponsored == 1){ ?>
                        <span class="sesalbum_label_sponsored"><?php echo $this->translate("Sponsored"); ?></span>
                      <?php } ?>
                      </p>
                      <?php } ?>
                      <div class="sesalbum_cat_album_list_info sesalbum_animation">
                        <div>
                          <div class="sesalbum_cat_album_list_content">
                          <?php if(isset($this->titleActive)){ ?>
                            <div class="sesalbum_cat_album_list_title">
                              <?php echo $item->getTitle(); ?>
                            </div>
                            <?php } ?>
                            <?php if(isset($this->byActive)){ ?>
                            <div class="sesalbum_cat_album_list_stats">
                              <?php
                                $owner = $item->getOwner();
                                echo $this->translate('Posted by %1$s', $owner->getTitle());
                              ?>
                            </div>
                            <?php } ?>
                            <div class="sesalbum_cat_album_list_stats sesalbum_list_stats sesbasic_text_light">
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
                              <?php  if(isset($this->photoActive)) { ?>
                                <span title="<?php echo $this->translate(array('%s photo', '%s photos', $item->count()), $this->locale()->toNumber($item->count()))?>"><i class="fa fa-photo"></i><?php echo $item->count(); ?></span>
                              <?php } ?>
                            </div>
                            <?php if(isset($this->ratingActive) && $ratingShow && isset($item->rating) && $item->rating > 0 ): ?>
                            <?php
                                $user_rate = Engine_Api::_()->getDbtable('ratings', 'sesalbum')->getCountUserRate('album',$item->album_id);
                                $textuserRating = $user_rate == 1 ? 'user' : 'users'; 
                                $textRatingText = $item->rating == 1 ? 'rating' : 'ratings'; ?>
                              <div class="sesalbum_grid_date sesbasic_text_light clear" title="<?php echo $item->rating.' '.$this->translate($textRatingText.' by').' '.$user_rate.' '.$this->translate($textuserRating); ?>">
                                <?php for( $x=1; $x<=$item->rating; $x++ ): ?>
                                  <span class="sesbasic_rating_star_small fa fa-star"></span>
                                <?php endfor; ?>
                                <?php if( (round($item->rating) - $item->rating) > 0): ?>
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
               <?php if($close){ $close = false;  ?></div> <?php }else{ $close = true;  }   ?>
            <?php } ?>
						<?php if($type == 2){ ?>
             <div class="sesalbum_albumlist_column_big floatL">
             		<div class="sesalbum_cat_album_list">
                   <div class="sesalbum_thumb">
                    <a href="<?php echo $href; ?>" class="sesalbum_thumb_img">
                      <span class="sesalbum_animation" style="background-image:url(<?php echo $imageURL; ?>);"></span>
                     <?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive)){ ?>
                      <p class="sesalbum_labels">
                      <?php if(isset($this->featuredLabelActive) && $item->is_featured == 1){ ?>
                        <span class="sesalbum_label_featured"><?php echo $this->translate("Featured"); ?></span>
                      <?php } ?>
                      <?php if(isset($this->sponsoredLabelActive) && $item->is_sponsored == 1){ ?>
                        <span class="sesalbum_label_sponsored"><?php echo $this->translate("Sponsored"); ?></span>
                      <?php } ?>
                      </p>
                      <?php } ?>
                      <div class="sesalbum_cat_album_list_info sesalbum_animation">
                        <div>
                          <div class="sesalbum_cat_album_list_content">
                          <?php if(isset($this->titleActive)){ ?>
                            <div class="sesalbum_cat_album_list_title">
                              <?php echo $item->getTitle(); ?>
                            </div>
                            <?php } ?>
                            <?php if(isset($this->byActive)){ ?>
                            <div class="sesalbum_cat_album_list_stats">
                              <?php
                                $owner = $item->getOwner();
                                echo $this->translate('Posted by %1$s', $owner->getTitle());
                              ?>
                            </div>
                            <?php } ?>
                            <div class="sesalbum_cat_album_list_stats sesalbum_list_stats sesbasic_text_light">
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
                              <?php  if(isset($this->photoActive)) { ?>
                                <span title="<?php echo $this->translate(array('%s photo', '%s photos', $item->count()), $this->locale()->toNumber($item->count()))?>"><i class="fa fa-photo"></i><?php echo $item->count(); ?></span>
                              <?php } ?>
                            </div>
                            <?php if(isset($this->ratingActive) && $ratingShow && isset($item->rating) && $item->rating > 0 ): ?>
                              <?php
                                $user_rate = Engine_Api::_()->getDbtable('ratings', 'sesalbum')->getCountUserRate('album',$item->album_id);
                                $textuserRating = $user_rate == 1 ? 'user' : 'users'; 
                                $textRatingText = $item->rating == 1 ? 'rating' : 'ratings'; ?>
                              <div class="sesalbum_grid_date sesbasic_text_light clear" title="<?php echo $item->rating.' '.$this->translate($textRatingText.' by').' '.$user_rate.' '.$this->translate($textuserRating); ?>">
                                <?php for( $x=1; $x<=$item->rating; $x++ ): ?>
                                  <span class="sesbasic_rating_star_small fa fa-star"></span>
                                <?php endfor; ?>
                                <?php if( (round($item->rating) - $item->rating) > 0): ?>
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
            <?php } ?>
     <?php if(($counter)%5 == 0){ ?>
           </div>
         </li>
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
   					   <li class="sesalbum_cat_album_list" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
                <div class="sesalbum_thumb">
                    <a href="<?php echo $href; ?>" class="sesalbum_thumb_img">
                      <span class="sesalbum_animation" style="background-image:url(<?php echo $imageURL; ?>);"></span>
                     <?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive)){ ?>
                      <p class="sesalbum_labels">
                      <?php if(isset($this->featuredLabelActive) && $item->is_featured == 1){ ?>
                        <span class="sesalbum_label_featured"><?php echo $this->translate("Featured"); ?></span>
                      <?php } ?>
                      <?php if(isset($this->sponsoredLabelActive) && $item->is_sponsored == 1){ ?>
                        <span class="sesalbum_label_sponsored"><?php echo $this->translate("Sponsored"); ?></span>
                      <?php } ?>
                      </p>
                      <?php } ?>
                      <div class="sesalbum_cat_album_list_info sesalbum_animation">
                        <div>
                          <div class="sesalbum_cat_album_list_content">
                          <?php if(isset($this->titleActive)){ ?>
                            <div class="sesalbum_cat_album_list_title">
                              <?php echo $item->getTitle(); ?>
                            </div>
                            <?php } ?>
                            <?php if(isset($this->byActive)){ ?>
                            <div class="sesalbum_cat_album_list_stats">
                              <?php
                                $owner = $item->getOwner();
                                echo $this->translate('Posted by %1$s', $owner->getTitle());
                              ?>
                            </div>
                            <?php } ?>
                            <div class="sesalbum_cat_album_list_stats sesalbum_list_stats sesbasic_text_light">
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
                              <?php  if(isset($this->photoActive)) { ?>
                                <span title="<?php echo $this->translate(array('%s photo', '%s photos', $item->count()), $this->locale()->toNumber($item->count()))?>"><i class="fa fa-photo"></i><?php echo $item->count(); ?></span>
                              <?php } ?>
                            </div>
                            <?php if(isset($this->ratingActive) && $ratingShow && isset($item->rating) && $item->rating > 0 ): ?>
                               <?php
                                $user_rate = Engine_Api::_()->getDbtable('ratings', 'sesalbum')->getCountUserRate('album',$item->album_id);
                                $textuserRating = $user_rate == 1 ? 'user' : 'users'; 
                                $textRatingText = $item->rating == 1 ? 'rating' : 'ratings'; ?>
                              <div class="sesalbum_grid_date sesbasic_text_light clear" title="<?php echo $item->rating.' '.$this->translate($textRatingText.' by').' '.$user_rate.' '.$this->translate($textuserRating); ?>">
                                <?php for( $x=1; $x<=$item->rating; $x++ ): ?>
                                  <span class="sesbasic_rating_star_small fa fa-star"></span>
                                <?php endfor; ?>
                                <?php if( (round($item->rating) - $item->rating) > 0): ?>
                                  <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                                <?php endif; ?>
                              </div>
                            <?php endif; ?>
                          </div>
                        </div>

                      </div>
                    </a>
                </div>

      </li>
    	  <?php }
   		 $counter ++;
    } ?>   
    <?php  if(  $totalCount == 0){  ?>
      <div class="tip">
        <span>
        	<?php echo $this->translate("No albums in this  category."); ?>
          <?php if (!$this->can_edit):?>
                    <?php echo $this->translate('Be the first to %1$spost%2$s one in this category!', '<a href="'.$this->url(array('action' => 'create'), "sesalbum_general").'">', '</a>'); ?>
          <?php endif; ?>
        </span>
      </div>
    <?php } ?>    
    <?php
          if($this->loadOptionData == 'pagging'){ ?>
 		 <?php echo $this->paginationControl($this->paginator, null, array("_pagging.tpl", "sesalbum"),array('identityWidget'=>$randonNumber)); ?>
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
	 jqueryObjectOfSes('.overlay_<?php echo $randonNumber ?>').css('display','block');
	 var openTab_<?php echo $randonNumber; ?> = '<?php echo $this->defaultOpenTab; ?>';
    (new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + "widget/index/mod/sesalbum/name/<?php echo $this->widgetName; ?>/openTab/" + openTab_<?php echo $randonNumber; ?>,
      'data': {
        format: 'html',
        page: pageNum,    
				params :'<?php echo json_encode($this->params); ?>', 
				is_ajax : 1,
				identity : '<?php echo $randonNumber; ?>',
				type:'<?php echo $this->view_type; ?>'
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				jqueryObjectOfSes('.overlay_<?php echo $randonNumber ?>').css('display','none');
        document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML =  responseHTML;
				dynamicWidth();
      }
    })).send();
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
		 jqueryObjectOfSes(window).scroll( function() {
			  var heightOfContentDiv_<?php echo $randonNumber; ?> = jqueryObjectOfSes('#scrollHeightDivSes_<?php echo $randonNumber; ?>').offset().top;
        var fromtop_<?php echo $randonNumber; ?> = jqueryObjectOfSes(this).scrollTop();
        if(fromtop_<?php echo $randonNumber; ?> > heightOfContentDiv_<?php echo $randonNumber; ?> - 100 && jqueryObjectOfSes('#view_more_<?php echo $randonNumber; ?>').css('display') == 'block' ){
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
        document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML = document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML + responseHTML;
				document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = 'none';
				dynamicWidth();
      }
    })).send();
    return false;
  }
<?php if(!$this->is_ajax){ ?>
function dynamicWidth(){
	var objectClass = jqueryObjectOfSes('.sesalbum_cat_album_list_info');
	for(i=0;i<objectClass.length;i++){
			jqueryObjectOfSes(objectClass[i]).find('div').find('.sesalbum_cat_album_list_content').find('.sesalbum_cat_album_list_title').width(jqueryObjectOfSes(objectClass[i]).width());
	}
}
dynamicWidth();
<?php } ?>
</script>
