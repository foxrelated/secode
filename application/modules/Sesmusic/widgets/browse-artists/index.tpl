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

<script type="text/javascript">
  function loadMore() {  
    if ($('load_more'))
      $('load_more').style.display = "<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>";

    if(document.getElementById('load_more'))
      document.getElementById('load_more').style.display = 'none';
    
    if(document.getElementById('underloading_image'))
     document.getElementById('underloading_image').style.display = '';

    en4.core.request.send(new Request.HTML({
      method: 'post',              
      'url': en4.core.baseUrl + 'widget/index/mod/sesmusic/name/browse-artists',
      'data': {
        format: 'html',
        page: "<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>",
        viewmore: 1,
        params: '<?php echo json_encode($this->all_params); ?>',
        
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('results_data').innerHTML = document.getElementById('results_data').innerHTML + responseHTML;
        
        if(document.getElementById('load_more'))
          document.getElementById('load_more').destroy();
        
        if(document.getElementById('underloading_image'))
         document.getElementById('underloading_image').destroy();
       
        if(document.getElementById('loadmore_list'))
         document.getElementById('loadmore_list').destroy();
      }
    }));
    return false;
  }
</script>

<?php if(count($this->paginator) > 0): ?>
  <?php if (empty($this->viewmore)): ?>
     <h4><?php echo $this->translate(array('%s artist found.', '%s artists found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())); ?></h4>
    <ul class="sesmusic_browse_listing clear" id= "results_data">
  <?php endif; ?>
  <?php foreach ($this->paginator as $artist): ?>
  <li id="music_playlist_item_<?php echo $artist->getIdentity() ?>" class="sesmusic_item_grid" style="width:<?php echo $this->width ?>px;">
    <div class="sesmusic_item_artwork" style="height:<?php echo $this->height ?>px;">
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
        <a class="transparentbg" href="<?php echo $this->escape($this->url(array('module'=>'sesmusic', 'controller'=>'artist', 'action'=>'view', 'artist_id' => $artist->artist_id), 'default' , true)); ?>"></a>
      </div>
    </div>
    <div class="sesmusic_browse_artist_info">
      <div class="sesmusic_browse_artist_title">
        <?php echo $this->htmlLink(array('module'=>'sesmusic', 'controller'=>'artist', 'action'=>'view', 'route'=>'default', 'artist_id' => $artist->artist_id), $artist->name); ?>
      </div>
      <div class="sesmusic_browse_artist_stats sesbasic_text_light">
        <?php if($this->showArtistRating && !empty($this->information) && in_array('showrating', $this->information)): ?>
        <?php echo $this->translate(array('%s rating', '%s ratings', $artist->rating), $this->locale()->toNumber($artist->rating)) ?>
        <?php endif; ?>
        <?php if($this->showArtistRating && !empty($this->information) &&  in_array('showfavourite', $this->information) && in_array('showrating', $this->information)): ?>
        &nbsp;|&nbsp;
        <?php endif; ?>
        <?php if(!empty($this->information) && in_array('showfavourite', $this->information)): ?>
        <?php echo $this->translate(array('%s favorite', '%s favorites', $artist->favourite_count), $this->locale()->toNumber($artist->favourite_count)) ?>
        <?php endif; ?>
      </div>
    </div>
  </li>
  <?php endforeach; ?>

  <?php //if($this->paginationType == 1): ?>
    <?php if (!empty($this->paginator) && $this->paginator->count() > 1): ?>
      <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
        <div class="clear" id="loadmore_list"></div>
        <div class="sesbasic_view_more" id="load_more" onclick="loadMore();" style="display: block;">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => 'feed_viewmore_link', 'class' => 'buttonlink icon_viewmore')); ?>
        </div>
        <div class="sesbasic_view_more_loading" id="underloading_image" style="display: none;">
          <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="Loading" />
          <?php echo $this->translate("Loading ...") ?>
        </div>
      <?php endif; ?>
     <?php endif; ?>
  <?php //else: ?>
    <?php //echo $this->paginationControl($this->paginator, null, null, array('pageAsQuery' => true, 'query' => $this->formValues)); ?>
  <?php //endif; ?>
  
<?php if (empty($this->viewmore)): ?>
</ul>
<?php endif; ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are currently no artists.') ?>
    </span>
  </div>
<?php endif; ?>

<?php if (empty($this->viewmore)): ?>
  <script type="text/javascript">
    $$('.core_main_sesmusic').getParent().addClass('active');
  </script>
<?php endif; ?>

<?php if($this->paginationType == 1): ?>
  <script type="text/javascript">    
     //Take refrences from: http://mootools-users.660466.n2.nabble.com/Fixing-an-element-on-page-scroll-td1100601.html
    //Take refrences from: http://davidwalsh.name/mootools-scrollspy-load
    en4.core.runonce.add(function() {
    
      var paginatorCount = '<?php echo $this->paginator->count(); ?>';
      var paginatorCurrentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';
      function ScrollLoader() { 
        var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
        if($('loadmore_list')) {
          if (scrollTop > 40)
            loadMore();
        }
      }
      window.addEvent('scroll', function() { 
        ScrollLoader(); 
      });
    });    
  </script>
<?php endif; ?>
