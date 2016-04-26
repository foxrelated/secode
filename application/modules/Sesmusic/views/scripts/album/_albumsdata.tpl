<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: _albumsdata.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>

<?php if($this->canAddFavourite): ?>
  <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesmusic/externals/scripts/favourites.js'); ?> 
<?php endif; ?>

<script type="text/javascript">
  function showPopUp(url) {
    Smoothbox.open(url);
    parent.Smoothbox.close;
  }
</script>

<?php if( 0 == count($this->paginator) ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You have not mark any music album as favourite.') ?>
    </span>
  </div>
<?php else: ?>

  <ul class="sesmusic_browse_listing clear">
    <?php foreach ($this->paginator as $album): ?>
      <?php $album = Engine_Api::_()->getItem('sesmusic_album', $album->resource_id); ?>
      <?php if($album): ?>
      <li id="music_playlist_item_<?php echo $album->getIdentity() ?>" class="sesmusic_item_grid">
        <div class="sesmusic_item_artwork">
          <?php echo $this->htmlLink($album, $this->itemPhoto($album, 'thumb.main') ) ?>
          <a href="<?php echo $album->getHref(); ?>" class="transparentbg"></a>          
          <div class="sesmusic_item_info">
            <div class="sesmusic_item_info_title">
              <?php echo $this->htmlLink($album->getHref(), $album->getTitle()) ?>
            </div>
            <div class="sesmusic_item_info_owner">
              <?php echo $this->translate('by %s', $this->htmlLink($album->getOwner(), $album->getOwner()->getTitle())); ?>
            </div>
            
            <div class="sesmusic_item_info_stats">
              <?php if($album->comment_count): ?>
                <span>
                  <?php echo $this->translate(array($album->comment_count), $this->locale()->toNumber($album->comment_count)) ?>
                  <i class="fa fa-comment"></i>
                </span>
              <?php endif; ?>
              
              <?php if($album->like_count): ?>
                <span>
                  <?php echo $this->translate(array($album->like_count), $this->locale()->toNumber($album->like_count)) ?>
                  <i class="fa fa-thumbs-up"></i>
                </span>
              <?php endif; ?>
              
              <?php if($album->view_count): ?>
                <span>
                  <?php echo $this->translate(array($album->view_count), $this->locale()->toNumber($album->view_count)) ?>
                  <i class="fa fa-eye"></i>
                </span>
              <?php endif; ?>
            </div>
            <?php if ($this->showRating) : ?>
              <div class="sesmusic_item_info_rating">
                <?php if( $album->rating > 0 ): ?>
                <?php for( $x=1; $x<= $album->rating; $x++ ): ?>
                <span class="sesbasic_rating_star_small fa fa-star"></span>
                <?php endfor; ?>
                <?php if( (round($album->rating) - $album->rating) > 0): ?>
                <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                <?php endif; ?>
                <?php endif; ?>
              </div>
            <?php endif; ?>
            <div class="sesmusic_item_info_label">
              <?php if($album->hot): ?>
                <span class="sesmusic_label_hot"><?php echo $this->translate('HOT'); ?></span>
              <?php endif; ?>
              <?php if($album->featured): ?>
              <span class="sesmusic_label_featured"><?php echo $this->translate('FEATURED'); ?></span>
              <?php endif; ?>
              <?php if($album->sponsored): ?>
              <span class="sesmusic_label_sponsored"><?php echo $this->translate('SPONSORED'); ?></span>
              <?php endif; ?>
            </div>
          </div>
          <div class="hover_box">
            <a title="<?php echo $album->getTitle(); ?>" href="<?php echo $album->getHref(); ?>" class="sesmusic_grid_link"></a>
            <div class="hover_box_options">
              <?php if($this->viewer_id): ?>
              <?php if($this->canAddFavourite): ?>
                <?php $isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_album", 'resource_id' => $album->album_id)); ?>
                <a title='<?php echo $this->translate("Remove from Favorite") ?>' class="favorite-white favorite" id="sesmusic_album_unfavourite_<?php echo $album->album_id; ?>" style ='display:<?php echo $isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $album->album_id; ?>', 'sesmusic_album');"><i class="fa fa-heart sesmusic_favourite"></i></a>
                <a title='<?php echo $this->translate("Add to Favorite") ?>' class="favorite-white favorite" id="sesmusic_album_favourite_<?php echo $album->album_id; ?>" style ='display:<?php echo $isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $album->album_id; ?>', 'sesmusic_album');"><i class="fa fa-heart"></i></a>
                <input type ="hidden" id = "sesmusic_album_favouritehidden_<?php echo $album->album_id; ?>" value = '<?php echo $isFavourite ? $isFavourite : 0; ?>' />
                <?php endif; ?>

                <?php if($this->canAddPlaylist): ?>
                <a class="add-white" title='<?php echo $this->translate("Add to Playlist") ?>' href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('module' =>'sesmusic', 'controller' => 'song', 'action'=>'append - songs','album_id' => $album->album_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><i class="fa fa-plus"></i></a>
                <?php endif; ?>
              <?php endif; ?>
              <?php if($this->albumlink && in_array('share', $this->albumlink)): ?>
              <a class="share-white" title='<?php echo $this->translate("Share") ?>' href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_album', 'id' => $album->album_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><i class="fa fa-share"></i></a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>
  <?php echo $this->paginationControl($this->paginator, null, null, array('pageAsQuery' => true, 'query' => $this->formValues)); ?>
<?php endif; ?>
<script type="text/javascript">
  $$('.core_main_sesmusic').getParent().addClass('active');
</script>