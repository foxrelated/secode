<?php

/**
* SocialEngineSolutions
*
* @category   Application_Sesmusic
* @package    Sesmusic
* @copyright  Copyright 2015-2016 SocialEngineSolutions
* @license    http://www.socialenginesolutions.com/license/
* @version    $Id: view.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
* @author     SocialEngineSolutions
*/
?>
<?php $baseURL = $this->layout()->staticBaseUrl; ?>
<div class="sesbasic_view_stats_popup">
  <h3>Statics of <?php echo $this->item->title;  ?> </h3>
  <table>
    <tr>
      <?php if($this->item->photo_id): ?>
      <?php $img_path = Engine_Api::_()->storage()->get($this->item->photo_id, '')->getPhotoUrl();
      $path = $img_path; 
      ?>
      <?php else: ?>
      <?php $path = $this->baseUrl() . '/application/modules/Sesmusic/externals/images/nophoto_album_thumb_main.png'; ?>
      <?php endif; ?>
      <td colspan="2"><img src="<?php echo $path; ?>" style="height:75px; width:75px;"/></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Title') ?>:</td>
      <td><?php if(!is_null($this->item->title) && $this->item->title != '') {
        echo  $this->item->title ;
        } else { 
        echo "-";
        } ?>
      </td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Owner') ?>:</td>
      <td><?php echo  $this->item->getOwner(); ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Ratings') ?>:</td>
      <td>
        <?php if($this->item->rating): ?>
        <div class="sesbasic_text_light">
          <?php if( $this->item->rating > 0 ): ?>
          <?php for( $x=1; $x<= $this->item->rating; $x++ ): ?>
          <span class="sesbasic_rating_star_small fa fa-star"></span>
          <?php endfor; ?>
          <?php if((round($this->item->rating) - $this->item->rating) > 0): ?>
          <span class="sesbasic_rating_star_small fa fa-star-half-o"></span>
          <?php endif; ?>
          <?php endif; ?>
        </div>
        <?php else: ?>
          <?php for( $x=1; $x<= 5; $x++ ): ?>
            <span class="sesbasic_rating_star_small fa fa-star-o star-disabled"></span>
          <?php endfor; ?>
        <?php endif; ?>
      </td>
    </tr>
    <tr>
      <td><?php echo $this->translate('IP Address') ?>:</td>
      <td><?php echo  $this->item->ip_address ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Songs') ?>:</td>
      <td><?php echo  $this->item->song_count ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Featured') ?>:</td>
      <td><?php  if($this->item->featured == 1){ ?>
        <img src="<?php echo $baseURL . 'application/modules/Sesbasic/externals/images/icons/check.png'; ?>"/> <?php }else{ ?> 
        <img src="<?php echo $baseURL . 'application/modules/Sesbasic/externals/images/icons/error.png'; ?>" /> <?php } ?>
      </td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Sponsored') ?>:</td>
      <td><?php  if($this->item->sponsored == 1){ ?>
        <img src="<?php echo $baseURL . 'application/modules/Sesbasic/externals/images/icons/check.png'; ?>"/> <?php }else{ ?> 
        <img src="<?php echo $baseURL . 'application/modules/Sesbasic/externals/images/icons/error.png'; ?>" /> <?php } ?>
      </td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Hot') ?>:</td>
      <td><?php  if($this->item->hot == 1){ ?>
        <img src="<?php echo $baseURL . 'application/modules/Sesbasic/externals/images/icons/check.png'; ?>"/> <?php }else{ ?> 
        <img src="<?php echo $baseURL . 'application/modules/Sesbasic/externals/images/icons/error.png'; ?>" /> <?php } ?>
      </td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Latest') ?>:</td>
      <td><?php  if($this->item->upcoming == 1) { ?>
        <img src="<?php echo $baseURL . 'application/modules/Sesbasic/externals/images/icons/check.png'; ?>"/> <?php }else{ ?> 
        <img src="<?php echo $baseURL . 'application/modules/Sesbasic/externals/images/icons/error.png'; ?>" /> <?php } ?>
      </td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Comments') ?>:</td>
      <td><?php echo $this->item->comment_count ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Likes') ?>:</td>
      <td><?php echo $this->item->like_count ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Views') ?>:</td>
      <td><?php echo $this->locale()->toNumber($this->item->view_count) ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Date') ?>:</td>
      <td><?php echo $this->item->creation_date; ;?></td>
    </tr>
  </table>
  <br />
  <button onclick='javascript:parent.Smoothbox.close()'>
    <?php echo $this->translate("Close") ?>
  </button>
</div>