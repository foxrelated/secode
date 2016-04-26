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
<?php $album = $this->album; ?>

<?php if($this->information && in_array('addFavouriteButton', $this->information)): ?>
  <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesmusic/externals/scripts/favourites.js'); ?>
<?php endif; ?>

<?php if($this->showRating): ?>
  <script type="text/javascript">
    
    en4.core.runonce.add(function() {
    
      var pre_rate = '<?php echo $this->album->rating;?>';
      <?php if($this->viewer_id == 0){ ?>
      rated = 0;
      <?php } elseif($this->allowShowRating == 1 && $this->allowRating == 0) { ?>
      var rated = 3;
      <?php } elseif($this->allowRateAgain == 0 && $this->rated) { ?>
      var rated = 1;
      <?php } elseif($this->canRate == 0 && $this->viewer_id != 0) { ?>
      var rated = 4;
      <?php } elseif(!$this->allowMine) { ?>
      var rated = 2;
      <?php } else { ?>
      var rated = '90';
      <?php } ?>
    
      var resource_id = '<?php echo $this->album->album_id;?>';
      var total_votes = '<?php echo $this->rating_count;?>';
      var viewer = '<?php echo $this->viewer_id;?>';
      new_text = '';

      var rating_over = window.rating_over = function(rating) {
        if( rated == 1 ) {
          $('rating_text').innerHTML = "<?php echo $this->translate('You already rated.');?>";
          return;
          //set_rating();
        }
        <?php if(!$this->canRate) { ?>
          else if(rated == 4){
               $('rating_text').innerHTML = "<?php echo $this->translate('You are not allowed to rate.');?>";
               return;
          }
        <?php } ?>
        <?php if(!$this->allowMine) { ?>
          else if(rated == 2){
               $('rating_text').innerHTML = "<?php echo $this->translate('Rating on own album is not allowed.');?>";
               return;
          }
        <?php } ?>
        <?php if($this->allowShowRating == 1) { ?>
          else if(rated == 3){
               $('rating_text').innerHTML = "<?php echo $this->translate('You are not allowed to rate.');?>";
               return;
          }
        <?php } ?>
        else if( viewer == 0 ) {
          $('rating_text').innerHTML = "<?php echo $this->translate('Please login to rate.');?>";
          return;
        } else {
          $('rating_text').innerHTML = "<?php echo $this->translate('Click to rate.');?>";
          for(var x=1; x<=5; x++) {
            if(x <= rating) {
              $('rate_'+x).set('class', 'fa fa-star');
            } else {
              $('rate_'+x).set('class', 'fa fa-star-o star-disable');
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
            $('rate_'+x).set('class', 'fa fa-star-o star-disable');
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
          $('rate_'+x).set('class', 'fa fa-star-o star-disable');
        }

        var remainder = Math.round(rating)-rating;
        if (remainder <= 0.5 && remainder !=0){
          var last = parseInt(rating)+1;
          $('rate_'+last).set('class', 'fa fa fa-star-half-o');
        }
      }

      var rate = window.rate = function(rating) {
        $('rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating.');?>";
        <?php if($this->allowRateAgain == 0 && !$this->rated){ ?>
               for(var x=1; x<=5; x++) {
                  $('rate_'+x).set('onclick', '');
                }
            <?php } ?>

        (new Request.JSON({
          'format': 'json',
          'url' : '<?php echo $this->url(array('module' => 'sesmusic', 'controller' => 'album', 'action' => 'rate'), 'default', true) ?>',
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
            if(responseJSON[0].total == 0 || responseJSON[0].total > 1) {
            $('rating_text').innerHTML = responseJSON[0].total+" ratings";
            new_text = responseJSON[0].total+" ratings";
            } else {
              $('rating_text').innerHTML = responseJSON[0].total+" rating";
              new_text = responseJSON[0].total+" rating";
            }
          }
        })).send();
      }
      set_rating();
    });
  </script>
<?php endif; ?>

<?php if($this->albumCover): ?>
  <?php if($album->album_cover): ?>
    <?php $storage = Engine_Api::_()->storage()->get($album->album_cover, '')->getPhotoUrl();
    $photoUrl = $storage;
    ?>
  <?php else: ?>
    <?php if($this->albumCoverPhoto): ?>
    <?php $photoUrl = $this->baseUrl() . '/' . $this->albumCoverPhoto; ?>
  <?php else: ?>
    <?php $photoUrl = $this->baseUrl() . '/application/modules/Sesmusic/externals/images/banner/cover.jpg'; ?>
  <?php endif; ?>
  <?php endif; ?>
  <div class="sesmusic_cover" style="background-image:url(<?php echo $photoUrl ?>); height:<?php echo $this->height; ?>px;">
    <div class="sesmusic_cover_inner">
      <?php if($this->information && in_array('photo', $this->information)): ?>
        <div class="sesmusic_cover_music_artwork" style="height:<?php echo $this->mainPhotoHeight; ?>px;width:<?php echo $this->mainPhotowidth; ?>px;">
          <?php if($album->photo_id):   ?>
            <?php $img_path = Engine_Api::_()->storage()->get($album->photo_id, '')->getPhotoUrl();
            $path = $img_path; 
          elseif(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.albumdefaultphoto')):
           $defaultPhoto = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.albumdefaultphoto');
           $path = $this->baseUrl() . '/' . $defaultPhoto  ?>
          <span style="background-image:url(<?php echo $path ?>)"></span>
          <?php else: 
            $path = $this->baseUrl() . '/application/modules/Sesmusic/externals/images/nophoto_album_thumb_main.png';  ?>          
          <?php endif; ?>
          <span style="background-image:url(<?php echo $path ?>)"></span>
          <?php if($album->featured || $album->sponsored || $album->hot ): ?>
            <div class="sesmusic_item_info_label">
              <?php if(in_array('hot', $this->information)): ?>
                <span class="sesmusic_label_hot"><?php echo $this->translate("HOT"); ?></span>
              <?php endif; ?>
              <?php if(in_array('featured', $this->information)): ?>
                <span class="sesmusic_label_featured"><?php echo $this->translate("FEATURED"); ?></span>
              <?php endif; ?>
              <?php if(in_array('sponsored', $this->information)): ?>
               <span class="sesmusic_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      <div class="sesmusic_cover_content">
        <div class="sesmusic_cover_title">
          <?php echo $album->getTitle() ?>
        </div>
        <p class="sesmusic_item_view_stats">
          <?php if($this->information && in_array('postedBy', $this->information)): ?>
            <?php echo $this->translate('By %s', $this->htmlLink($album->getOwner(), $album->getOwner()->getTitle())) ?>
          <?php endif; ?>      
          <?php if($this->information && in_array('creationDate', $this->information)): ?>
            &nbsp;|&nbsp;
            <?php echo $this->translate('Created on %s', $album->creation_date); ?>
          <?php endif; ?>
        </p>
        <?php if(!empty($this->information) && in_array('category', $this->information) && $album->category_id) :?>
          <p class="sesmusic_item_view_stats">
            <?php $catName = $this->categoriesTable->getColumnName(array('column_name' => 'category_name', 'category_id' => $album->category_id)); ?>
            <a href="<?php echo $this->url(array('action' => 'browse'), 'sesmusic_general', true).'?category_id='.urlencode($album->category_id) ; ?>"><?php echo $catName; ?></a>
            <?php if($album->subcat_id): ?>
            <?php $subcatName = $this->categoriesTable->getColumnName(array('column_name' => 'category_name', 'category_id' => $album->subcat_id)); ?>
            &nbsp;&raquo;
            <a href="<?php echo $this->url(array('action' => 'browse'), 'sesmusic_general', true).'?category_id='.urlencode($album->category_id) . '&subcat_id='.urlencode($album->subcat_id) ?>"><?php echo $subcatName; ?></a>
            <?php endif; ?>
            <?php if($album->subsubcat_id): ?>
            <?php $subsubcatName = $this->categoriesTable->getColumnName(array('column_name' => 'category_name', 'category_id' => $album->subsubcat_id)); ?>
            &nbsp;&raquo;
            <a href="<?php echo $this->url(array('action' => 'browse'), 'sesmusic_general', true).'?category_id='.urlencode($album->category_id) . '&subcat_id='.urlencode($album->subcat_id) .'&subsubcat_id='.urlencode($album->subsubcat_id) ?>"><?php echo $subsubcatName; ?></a>
            <?php endif; ?>
          </p>
        <?php endif; ?>
        <p class="sesmusic_item_view_stats">
         <?php 
            $information = '';
            if(!empty($this->information)): 
            if ($this->showRating && in_array('ratingCount', $this->information))
              $information .= $this->translate(array('%s rating', '%s ratings', $album->rating), $this->locale()->toNumber($album->rating)) . ' | ';

            if (in_array('likeCount', $this->information))
             $information .= $this->translate(array('%s like', '%s likes', $album->like_count), $this->locale()->toNumber($album->like_count)) . ' | '; 

            if (in_array('commentCount', $this->information))
              $information .= $this->translate(array('%s comment', '%s comments', $album->comment_count), $this->locale()->toNumber($album->comment_count)) . ' | ';

            if (in_array('viewCount', $this->information))
             $information .= $this->translate(array('%s view', '%s views', $album->view_count), $this->locale()->toNumber($album->view_count)) . ' | ';

            if (in_array('favouriteCount', $this->information))
              $information .= $this->translate(array('%s favorite', '%s favorites', $album->favourite_count), $this->locale()->toNumber($album->favourite_count)) . ' | ';
              
            if (in_array('songCount', $this->information))
              $information .= $this->translate(array('%s song', '%s songs', $album->song_count), $this->locale()->toNumber($album->song_count)) . ' | ';

            $information = trim($information);
            $information = rtrim($information, '|');
          ?>
          <?php echo $information; ?>
          <?php endif; ?>

        </p>
        <?php if(!empty($this->information) && in_array('description', $this->information)): ?>
        <p class="sesmusic_item_view_des">
          <?php echo $this->viewMore($album->description); ?>
        </p>
        <?php endif; ?>

        <?php if($this->showRating == 1 && !empty($this->information) && in_array('ratingStars', $this->information)):  ?>
          <div id="album_rating" class="sesbasic_rating_star sesmusic_cover_rating" onmouseout="rating_out();">
            <span id="rate_1" class="fa fa-star" <?php  if ($this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating ):?>onclick="rate(1);"<?php  endif; ?> onmouseover="rating_over(1);"></span>
            <span id="rate_2" class="fa fa-star" <?php if ( $this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating):?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
            <span id="rate_3" class="fa fa-star" <?php if ( $this->viewer_id && $this->ratedAgain  && $this->allowMine && $this->allowRating):?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
            <span id="rate_4" class="fa fa-star" <?php if ($this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating):?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
            <span id="rate_5" class="fa fa-star" <?php if ($this->viewer_id && $this->ratedAgain  && $this->allowMine && $this->allowRating):?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
            <span id="rating_text" class="sesbasic_rating_text"><?php echo $this->translate('click to rate');?></span>
          </div>
        <?php endif; ?>
      </div>
      <?php //Album Like Users ?>
      <div class="sesmusic_cover_content">
        <?php //foreach( $this->results as $item ): ?>
            <?php //$user = Engine_Api::_()->getItem('user', $item['poster_id']); ?>
            <?php //echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('title' => $user->getTitle(), 'target' => '_parent')); ?>
        <?php //endforeach; ?>
      </div>
      <div class="sesmusic_cover_options sesmusic_options_buttons">
        <?php //if ($this->canCreate && $this->information && in_array('uploadButton', $this->information))    
        //echo $this->htmlLink(array('route' => 'sesmusic_general', 'action' => 'create'), $this->translate(''), array('class'=>'fa fa-upload', 'title'=> $this->translate('Upload Songs'))) ?>

        <?php if ($album->isEditable() && $this->information && in_array('editButton', $this->information))
        echo $this->htmlLink($album->getHref(array('route' => 'sesmusic_album_specific', 'action' => 'edit')), $this->translate(''), array('class'=>'fa fa-pencil', 'title' => $this->translate('Edit Music Album'))); ?>
        <?php if($album->isDeletable() && $this->information && in_array('deleteButton', $this->information))
        echo $this->htmlLink(array('route' => 'sesmusic_general', 'module' => 'sesmusic', 'controller' => 'index', 'action' => 'delete', 'album_id' => $album->getIdentity(), 'format' => 'smoothbox'), $this->translate(''), array('class' => 'smoothbox fa fa-trash', 'title' => $this->translate('Delete Music Album'))); ?>
        <?php if($this->canAddPlaylist && $this->viewer_id  && $this->information && in_array('addplaylist', $this->information)): ?>
        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'song', 'action' => 'append-songs', 'album_id' => $album->album_id), $this->translate(''), array('class' => 'smoothbox fa fa-plus', 'title' => $this->translate('Add to Playlist'))); ?>
        <?php endif; ?>
        <?php if(!empty($this->viewer_id) && !empty($this->albumlink) && in_array('share', $this->albumlink) && $this->information && in_array('share', $this->information)): ?>
        <?php echo $this->htmlLink(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_album', 'id' => $this->album->getIdentity(), 'format' => 'smoothbox'), $this->translate(""), array('class' => 'smoothbox fa fa-share', 'title' => $this->translate("Share"))); ?>
        <?php endif; ?>
        <?php if(!empty($this->viewer_id) && !empty($this->albumlink) && in_array('report', $this->albumlink) && $this->information && in_array('report', $this->information)): ?>
        <?php echo $this->htmlLink(array('module'=>'core', 'controller'=>'report', 'action'=>'create', 'route'=>'default', 'subject'=> $this->album->getGuid(), 'format' => 'smoothbox'), $this->translate(""), array('class' => 'smoothbox fa fa-flag', 'title' => $this->translate("Report"))); ?>
        <?php endif; ?>
        <?php if (!empty($this->viewer_id) && $this->information && in_array('downloadButton', $this->information)): ?>
        <?php //echo $this->htmlLink($album->getHref(array('route' => 'sesmusic_album_specific', 'action' => 'download-zip')), $this->translate(''), array('class'=>'fa fa-download', 'title' => $this->translate('Download All Songs'))); ?>
        <?php endif; ?>
        <?php if ($this->canAddFavourite && !empty($this->viewer_id) && $this->information && in_array('addFavouriteButton', $this->information)): ?>
        <a class="fa fa-heart sesmusic_favourite" id="sesmusic_album_unfavourite_<?php echo $this->album->getIdentity(); ?>" style ='display:<?php echo $this->isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $this->album->getIdentity(); ?>', 'sesmusic_album');" title="<?php echo $this->translate("Remove from Favorite") ?>"></a>
        <a class="fa fa-heart" id="sesmusic_album_favourite_<?php echo $this->album->getIdentity(); ?>" style ='display:<?php echo $this->isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $this->album->getIdentity(); ?>', 'sesmusic_album');" title="<?php echo $this->translate("Add to Favorite") ?>"></a>
        <input type="hidden" id="sesmusic_album_favouritehidden_<?php echo $this->album->getIdentity(); ?>" value='<?php echo $this->isFavourite ? $this->isFavourite : 0; ?>' />
        <?php endif; ?>
      </div>
    </div>
  </div>
<?php else: ?>
  <div class="sesmusic_item_view_top">
    <div class="sesmusic_item_view_artwork">
      <?php echo $this->itemPhoto($album, 'thumb.profile'); ?>
      <div class="sesmusic_item_info_label">
        <span class="sesmusic_label_hot">HOT</span>
        <span class="sesmusic_label_featured">FEATURED</span>
        <span class="sesmusic_label_sponsored">SPONSORED</span>
      </div>
    </div>
    <div class="sesmusic_item_view_info">
      <div class="sesmusic_item_view_title">
        <?php echo $album->getTitle() ?>
      </div>
      <p class="sesmusic_item_view_stats sesbasic_text_light">
        <?php if($this->information && in_array('postedBy', $this->information)): ?>
          <?php echo $this->translate('By %s', $this->htmlLink($album->getOwner(), $album->getOwner()->getTitle())) ?>
        <?php endif; ?>      
        <?php if($this->information && in_array('creationDate', $this->information)): ?>
          &nbsp;|&nbsp;
          <?php echo $this->translate('Created on %s', $album->creation_date); ?>
        <?php endif; ?>
      </p>
      <?php if(!empty($this->information) && in_array('category', $this->information) && $album->category_id) :?>
      <p class="sesmusic_item_view_stats">
        <?php $catName = $this->categoriesTable->getColumnName(array('column_name' => 'category_name', 'category_id' => $album->category_id)); ?>
        <a href="<?php echo $this->url(array('action' => 'browse'), 'sesmusic_general', true).'?category_id='.urlencode($album->category_id) ; ?>"><?php echo $catName; ?></a>
        <?php if($album->subcat_id): ?>
        <?php $subcatName = $this->categoriesTable->getColumnName(array('column_name' => 'category_name', 'category_id' => $album->subcat_id)); ?>
        &nbsp;&raquo;
        <a href="<?php echo $this->url(array('action' => 'browse'), 'sesmusic_general', true).'?category_id='.urlencode($album->category_id) . '&subcat_id='.urlencode($album->subcat_id) ?>"><?php echo $subcatName; ?></a>
        <?php endif; ?>
        <?php if($album->subsubcat_id): ?>
        <?php $subsubcatName = $this->categoriesTable->getColumnName(array('column_name' => 'category_name', 'category_id' => $album->subsubcat_id)); ?>
        &nbsp;&raquo;
        <a href="<?php echo $this->url(array('action' => 'browse'), 'sesmusic_general', true).'?category_id='.urlencode($album->category_id) . '&subcat_id='.urlencode($album->subcat_id) .'&subsubcat_id='.urlencode($album->subsubcat_id) ?>"><?php echo $subsubcatName; ?></a>
        <?php endif; ?>
      </p>
      <?php endif; ?>
      <p class="sesmusic_item_view_stats sesbasic_text_light">
       <?php 
          $information = '';
          if(!empty($this->information)):
          if ($this->showRating && in_array('ratingCount', $this->information))
            $information .= $this->translate(array('%s rating', '%s ratings', $album->rating), $this->locale()->toNumber($album->rating)) . ' | ';

          if (in_array('likeCount', $this->information))
           $information .= $this->translate(array('%s like', '%s likes', $album->like_count), $this->locale()->toNumber($album->like_count)) . ' | '; 

          if (in_array('commentCount', $this->information))
            $information .= $this->translate(array('%s comment', '%s comments', $album->comment_count), $this->locale()->toNumber($album->comment_count)) . ' | ';

          if (in_array('viewCount', $this->information))
           $information .= $this->translate(array('%s view', '%s views', $album->view_count), $this->locale()->toNumber($album->view_count)) . ' | ';

          if (in_array('favouriteCount', $this->information))
            $information .= $this->translate(array('%s favorite', '%s favorites', $album->favourite_count), $this->locale()->toNumber($album->favourite_count)) . ' | ';
            
          if (in_array('songCount', $this->information))
            $information .= $this->translate(array('%s song', '%s songs', $album->song_count), $this->locale()->toNumber($album->song_count)) . ' | ';
            
          $information = trim($information);
          $information = rtrim($information, '|');
        ?>
        <?php echo $information; ?>
        <?php endif; ?>

      </p>
      <?php if(!empty($this->information) && in_array('description', $this->information)): ?>
      <p class="sesmusic_item_view_des">
        <?php echo $this->viewMore($album->description); ?>
      </p>
      <?php endif; ?>

      <?php if($this->showRating == 1 && !empty($this->information) && in_array('ratingStars', $this->information)):  ?>
        <div id="album_rating" class="sesbasic_rating_star" onmouseout="rating_out();">
          <span id="rate_1" class="fa fa-star" <?php  if ($this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating ):?>onclick="rate(1);"<?php  endif; ?> onmouseover="rating_over(1);"></span>
          <span id="rate_2" class="fa fa-star" <?php if ( $this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating):?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
          <span id="rate_3" class="fa fa-star" <?php if ( $this->viewer_id && $this->ratedAgain  && $this->allowMine && $this->allowRating):?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
          <span id="rate_4" class="fa fa-star" <?php if ($this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating):?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
          <span id="rate_5" class="fa fa-star" <?php if ($this->viewer_id && $this->ratedAgain  && $this->allowMine && $this->allowRating):?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
          <span id="rating_text" class="sesbasic_rating_text"><?php echo $this->translate('click to rate');?></span>
        </div>
      <?php endif; ?>

      <div class="sesmusic_options_buttons sesmusic_item_view_options">
        <?php if ($this->canCreate && $this->information && in_array('uploadButton', $this->information))    
        //echo $this->htmlLink(array('route' => 'sesmusic_general', 'action' => 'create'), $this->translate('Upload Songs'), array('class'=>'fa fa-upload' )) ?>

        <?php if ($album->isEditable() && $this->information && in_array('editButton', $this->information))
        echo $this->htmlLink($album->getHref(array('route' => 'sesmusic_album_specific', 'action' => 'edit')), $this->translate('Edit Music Album'), array('class'=>'fa fa-pencil' )); ?>

        <?php if($album->isDeletable() && $this->information && in_array('deleteButton', $this->information))
        echo $this->htmlLink(array('route' => 'sesmusic_general', 'module' => 'sesmusic', 'controller' => 'index', 'action' => 'delete', 'album_id' => $album->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete Music Album'), array('class' => 'smoothbox fa fa-trash')); ?>

        <?php if($this->canAddPlaylist && $this->viewer_id  && $this->information && in_array('addplaylist', $this->information)): ?>
        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'song', 'action' => 'append-songs', 'album_id' => $album->album_id), $this->translate('Add all songs in to  my Playlist'), array('class' => 'smoothbox fa fa-plus')); ?>
        <?php endif; ?>

        <?php if(!empty($this->albumlink) && in_array('share', $this->albumlink) && $this->information && in_array('share', $this->information)): ?>
        <?php echo $this->htmlLink(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_album', 'id' => $this->album->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox fa fa-share')); ?>
        <?php endif; ?>

        <?php if(!empty($this->albumlink) && in_array('report', $this->albumlink) && $this->information && in_array('report', $this->information)): ?>
        <?php echo $this->htmlLink(array('module'=>'core', 'controller'=>'report', 'action'=>'create', 'route'=>'default', 'subject'=> $this->album->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox fa fa-flag')); ?>
        <?php endif; ?>

        <?php if (!empty($this->viewer_id) && $this->information && in_array('downloadButton', $this->information)): ?>
        <?php //echo $this->htmlLink($album->getHref(array('route' => 'sesmusic_album_specific', 'action' => 'download-zip')), $this->translate('Download All Songs'), array('class'=>'fa fa-download' )); ?>
        <?php endif; ?>

        <?php if ($this->canAddFavourite && !empty($this->viewer_id) && $this->information && in_array('addFavouriteButton', $this->information)): ?>

        <a class="fa fa-heart sesmusic_favourite" id="sesmusic_album_unfavourite_<?php echo $this->album->getIdentity(); ?>" style ='display:<?php echo $this->isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $this->album->getIdentity(); ?>', 'sesmusic_album');"><?php echo $this->translate("Remove from Favorite") ?></a>
        <a class="fa fa-heart" id="sesmusic_album_favourite_<?php echo $this->album->getIdentity(); ?>" style ='display:<?php echo $this->isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $this->album->getIdentity(); ?>', 'sesmusic_album');"><?php echo $this->translate("Add to Favorite") ?></a>
        <input type="hidden" id="sesmusic_album_favouritehidden_<?php echo $this->album->getIdentity(); ?>" value='<?php echo $this->isFavourite ? $this->isFavourite : 0; ?>' />
        <?php endif; ?>
      </div>
    </div>
  </div>
<?php endif; ?>
