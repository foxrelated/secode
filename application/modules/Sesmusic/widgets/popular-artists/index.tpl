<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesmusic/externals/scripts/favourites.js'); ?>
<?php if(count($this->results) > 0) :?>
  <ul class="sesmusic_side_block sesmusic_browse_listing">
    <?php foreach( $this->results as $item ): ?>
    <?php if($this->viewType == 'listview'): ?>
      <li class="sesmusic_sidebar_list">
        <?php if($item->artist_photo): ?>
          <?php $img_path = Engine_Api::_()->storage()->get($item->artist_photo, '')->getPhotoUrl();
          $path = $img_path; 
          ?>
          <a href="<?php echo $this->escape($this->url(array('module'=>'sesmusic', 'controller'=>'artist', 'action'=>'view', 'artist_id' => $item->artist_id), 'default' , true)); ?>" class="sesmusic_sidebar_list_thumb">
            <img class="thumb_icon" src="<?php echo $path ?>">
          </a>
        <?php endif; ?> 
        <div class="sesmusic_sidebar_list_info">
          <div class="sesmusic_sidebar_list_title">
            <?php echo $this->htmlLink(array('module'=>'sesmusic', 'controller'=>'artist', 'action'=>'view', 'route'=>'default', 'artist_id' => $item->artist_id), $item->name); ?>
          </div>
          <?php //if(!empty($this->information) && in_array('ratingCount', $this->information)): ?>
          <div class="sesmusic_sidebar_list_stats sesbasic_text_light" title="<?php echo $this->translate(array('%s rating', '%s ratings', $item->rating), $this->locale()->toNumber($item->rating)); ?>">
            <?php if( $item->rating > 0 ): ?>
            <?php for( $x=1; $x<= $item->rating; $x++ ): ?>
            <span class="sesbasic_rating_star_small fa fa-star"></span>
            <?php endfor; ?>
            <?php if( (round($item->rating) - $item->rating) > 0): ?>
            <span class="sesbasic_rating_star_small fa fa-star-half"></span>
            <?php endif; ?>
            <?php endif; ?>
          </div>
          <?php //endif; ?>

          <div class="sesmusic_sidebar_list_stats sesmusic_list_stats sesbasic_text_light">
            <span title="<?php echo $this->translate(array('%s favorite', '%s favorites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count)); ?>">
              <i class="fa fa-heart"></i>
              <?php echo $item->favourite_count; ?>
            </span>
          </div>
        </div>
      </li>
    <?php elseif($this->viewType == 'gridview'): ?>
      <li class="sesmusic_item_grid" style="width:<?php echo $this->width ?>px;">
        <div class="sesmusic_item_artwork" style="height:<?php echo $this->height ?>px;">
          <?php if($item->artist_photo): ?>
          <?php $img_path = Engine_Api::_()->storage()->get($item->artist_photo, '')->getPhotoUrl();
          $path = $img_path; 
          ?>
          <img src="<?php echo $path ?>">
          <?php endif; ?>  
          <div class="hover_box">
            <div class="hover_box_options">
              <?php if($this->viewer_id): ?>
              <?php //if($this->songlink && in_array('favourite', $this->songlink)): ?>
              <?php $isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_artist", 'resource_id' => $item->artist_id)); ?>
              <a title='<?php echo $this->translate("Remove from Favorite") ?>' class="favorite-white favorite" id="sesmusic_artist_unfavourite_<?php echo $item->artist_id; ?>" style ='display:<?php echo $isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $item->artist_id; ?>', 'sesmusic_artist');"><i class="fa fa-heart sesmusic_favourite"></i></a>
              <a title='<?php echo $this->translate("Add to Favorite") ?>' class="favorite-white favorite" id="sesmusic_artist_favourite_<?php echo $item->artist_id; ?>" style ='display:<?php echo $isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $item->artist_id; ?>', 'sesmusic_artist');"><i class="fa fa-heart"></i></a>
              <input type="hidden" id="sesmusic_artist_favouritehidden_<?php echo $item->artist_id; ?>" value='<?php echo $isFavourite ? $isFavourite : 0; ?>' />
              <?php //endif; ?>
              <?php endif; ?>
            </div>
            <a href="<?php echo $this->escape($this->url(array('module'=>'sesmusic', 'controller'=>'artist', 'action'=>'view', 'artist_id' => $item->artist_id), 'default' , true)); ?>" class="transparentbg"></a>
          </div>
        </div>
        <div class="sesmusic_browse_artist_info">
          <?php //if(!empty($this->information) && in_array('title', $this->information)): ?>
            <div class="sesmusic_browse_artist_title">
              <?php echo $this->htmlLink(array('module'=>'sesmusic', 'controller'=>'artist', 'action'=>'view', 'route'=>'default', 'artist_id' => $item->artist_id), $item->name); ?>
              <?php //echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
            </div>
          <?php //endif; ?>
          <div class="sesmusic_browse_artist_stats sesbasic_text_light">
            <?php //if (!empty($this->information) && in_array('commentCount', $this->information)) :?>
              <span class="floatL">
                <?php echo $this->translate(array('%s favorite', '%s favorites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count)) ?>
              </span>
            <?php //endif; ?>
            <?php //if (!empty($this->information) && in_array('ratingCount', $this->information)) : ?>
              <span class="floatR">
                <?php if( $item->rating > 0 ): ?>
                <?php for( $x=1; $x<= $item->rating; $x++ ): ?>
                <span class="sesbasic_rating_star_small fa fa-star"></span>
                <?php endfor; ?>
                <?php if( (round($item->rating) - $item->rating) > 0): ?>
                <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                <?php endif; ?>
                <?php endif; ?>
              </span>
            <?php //endif; ?>
          </div>
        </div>
      </li>
    <?php endif; ?>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>