<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: favourite-artists.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesmusic/externals/scripts/favourites.js'); ?>

<?php if( 0 == count($this->paginator) ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are currently no artists.') ?>
    </span>
  </div>
<?php else: ?>
  <ul class="sesmusic_browse_listing clear">
    <?php foreach ($this->paginator as $artist): ?>
      <?php $artist = Engine_Api::_()->getItem('sesmusic_artist', $artist->resource_id) ?>
      <li id="music_playlist_item_<?php echo $artist->getIdentity() ?>" class="sesmusic_item_grid">
        <div class="sesmusic_item_artwork">
          <?php if($artist->artist_photo): ?>
          <?php $img_path = Engine_Api::_()->storage()->get($artist->artist_photo, '')->getPhotoUrl();
          $path = $img_path; 
          ?>
          <img src="<?php echo $path ?>">
          <?php else: ?>
           <img src="<?php //echo $path ?>">
          <?php endif; ?>
          <div class="hover_box">           
            <div class="hover_box_options">
              <?php if($this->viewer_id): ?>
              <?php if($this->artistlink && in_array('favourite', $this->artistlink)): ?>
                <?php $isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_artist", 'resource_id' => $artist->getIdentity())); ?>
                <a title='<?php echo $this->translate("Remove from Favorites") ?>' class="favorite-white favorite" id="sesmusic_artist_unfavourite_<?php echo $artist->getIdentity(); ?>" style ='display:<?php echo $isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $artist->getIdentity(); ?>', 'sesmusic_artist');"><i class="fa fa-heart sesmusic_favourite"></i></a>
                <a title='<?php echo $this->translate("Add to Favorite") ?>' class="favorite-white favorite" id="sesmusic_artist_favourite_<?php echo $artist->getIdentity(); ?>" style ='display:<?php echo $isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $artist->getIdentity(); ?>', 'sesmusic_artist');"><i class="fa fa-heart"></i></a>
                <input type="hidden" id="sesmusic_artist_favouritehidden_<?php echo $artist->getIdentity(); ?>" value='<?php echo $isFavourite ? $isFavourite : 0; ?>' />
                <?php endif; ?>
              <?php endif; ?>
            </div>
            <a title="<?php echo $artist->getTitle(); ?>" class="transparentbg" href="<?php echo $this->escape($this->url(array('module'=>'sesmusic', 'controller'=>'artist', 'action'=>'view', 'artist_id' => $artist->artist_id), 'default' , true)); ?>"></a>
          </div>
        </div>
        <div class="sesmusic_browse_artist_info">
          <div class="sesmusic_browse_artist_title">
            <?php echo $this->htmlLink(array('module'=>'sesmusic', 'controller'=>'artist', 'action'=>'view', 'route'=>'default', 'artist_id' => $artist->artist_id), $artist->name); ?>
          </div>
          <div class="sesmusic_browse_artist_stats sesbasic_text_light">
             <?php echo $this->translate(array('%s rating', '%s ratings', $artist->rating), $this->locale()->toNumber($artist->rating)) ?>
            &nbsp;|&nbsp;
            <?php echo $this->translate(array('%s favorite', '%s favorites', $artist->favourite_count), $this->locale()->toNumber($artist->favourite_count)) ?>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php echo $this->paginationControl($this->paginator, null, null, array('pageAsQuery' => true, 'query' => $this->formValues)); ?>
<?php endif; ?>
<script type="text/javascript">
  $$('.core_main_sesmusic').getParent().addClass('active');
</script>
