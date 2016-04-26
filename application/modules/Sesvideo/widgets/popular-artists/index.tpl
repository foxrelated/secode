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
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesvideo/externals/styles/styles.css');?>
<?php if(count($this->results) > 0): ?>
  <ul class="sesbasic_sidebar_block sesvideo_browse_listing sesbasic_clearfix sesbasic_bxs">
    <?php foreach( $this->results as $item ): ?>
    <?php if($this->viewType == 'listview'): ?>
      <li class="sesvideo_sidebar_list">
          <?php $img_path = $item->getPhotoUrl();
          $path = $img_path; 
          ?>
          <a href="<?php echo $item->getHref(); ?>" class="sesvideo_sidebar_list_thumb">
            <img class="thumb_icon" src="<?php echo $path ?>">
          </a>
        <div class="sesvideo_sidebar_list_info">
         <?php if(!empty($this->information) && in_array('title', $this->information)): ?>
          <div class="sesvideo_sidebar_list_title">
            <?php echo $this->htmlLink($item->getHref(), $item->name); ?>
          </div>
         <?php endif; ?>
          <div class="sesvideo_sidebar_list_stats sesvideo_list_stats sesbasic_clearfix sesbasic_text_light">
           <?php if(!empty($this->information) && in_array('favouriteCount', $this->information)): ?>
            <span title="<?php echo $this->translate(array('%s favorite', '%s favorites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count)); ?>">
              <i class="fa fa-heart"></i>
              <?php echo $item->favourite_count; ?>
            </span>
            <?php endif; ?>
            <?php if(!empty($this->information) && in_array('ratingCount', $this->information)): ?>
            <span  title="<?php echo $this->translate(array('%s rating', '%s ratings', round($item->rating,1)), $this->locale()->toNumber(round($item->rating,1)))?>">
            <i class="fa fa-star"></i><?php echo round($item->rating,1).'/5';?>
            </span>
            <?php endif; ?>
          </div>
        </div>
      </li>
    <?php elseif($this->viewType == 'gridview'): ?>
      <li class="sesvideo_artist_list" style="height:<?php echo $this->height ?>px;width:<?php echo $this->width ?>px;">
        <div class="sesvideo_artist_list_photo">
          <?php $img_path = $item->getPhotoUrl();
          $path = $img_path; 
          ?>
          <img src="<?php echo $path ?>">
        </div>
        <a href="<?php echo $item->getHref(); ?>" class="sesvideo_artist_list_overlay"></a>
        <div class="sesvideo_browse_artist_info">
          <?php if(!empty($this->information) && in_array('title', $this->information)): ?>
            <div class="sesvideo_browse_artist_title">
              <?php echo $this->htmlLink($item->getHref(), $item->name); ?>
            </div>
          <?php endif; ?>
          <div class="sesvideo_browse_artist_stats sesvideo_list_stats">
            <?php if (!empty($this->information) && in_array('favouriteCount', $this->information)) :?>
              <span title="<?php echo $this->translate(array('%s favorite', '%s favorites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count)) ?>">
              	<i class="fa fa-heart"></i>
                <?php echo $this->locale()->toNumber($item->favourite_count);?>
              </span>
            <?php endif; ?>
            <?php if(!empty($this->information) && in_array('ratingCount', $this->information)): ?>
            <span  title="<?php echo $this->translate(array('%s rating', '%s ratings', round($item->rating,1)), $this->locale()->toNumber(round($item->rating,1)))?>">
               <i class="fa fa-star"></i><?php echo round($item->rating,1).'/5';?>
              </span>
            <?php endif; ?>
          </div>
        </div>
      </li>
    <?php endif; ?>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>