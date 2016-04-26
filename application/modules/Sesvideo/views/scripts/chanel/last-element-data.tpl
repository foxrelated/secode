<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: last-element-data.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<div class="ses_ml_more_popup_container sesbasic_clearfix">
  	<div class="ses_ml_more_popup_lb">
    	<div class="ses_ml_more_popup_bh">
      	<i class="fa fa-file-image-o"></i>
      	<span><?php echo $this->translate("Popular Chanels"); ?></span>
      </div>
      <div class="ses_ml_more_popup_bc sesbasic_clearfix">
      <?php  $chanelItems = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->getChanels(array('limit_data'=>6,'order'=>'view_count','chanelphoto'=>true)); 
        	foreach($chanelItems as $chanelItem){ ?>
          <?php $imageURL = Engine_Api::_()->sesvideo()->getImageViewerHref($chanelItem); ?>
      	<div class="ses_ml_more_popup_a_list sesbasic_clearfix">
        	<a class="ses-image-viewer" href="<?php echo $chanelItem->getHref(); ?>" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo Engine_Api::_()->getItem('sesvideo_chanelphoto',$chanelItem->chanelphoto_id )->getPhotoUrl(); ?>','<?php echo $imageURL	; ?>','change')" href="<?php echo Engine_Api::_()->sesvideo()->getHrefPhoto($chanelItem->chanelphoto_id,$chanelItem->chanel_id); ?>">
        		<span class="ses_ml_more_popup_a_list_img" style="background-image:url(<?php echo $chanelItem->getPhotoUrl('thumb.normalmain'); ?>);"></span>
            <span class="ses_ml_more_popup_a_list_title">
            	  <?php echo $this->string()->truncate($chanelItem->title, 30) ; ?>
              <span class="ses_ml_more_popup_a_list_owner"><?php echo $this->translate('by').' '.$chanelItem->getOwner()->getTitle(); ?></span>
             </span>
          </a>
        </div>
         <?php } ?>
      </div>
    </div>
    <div class="ses_ml_more_popup_rb">
    	<div class="ses_ml_more_popup_bh">
      	<i class="fa fa-picture-o"></i>
        <span><?php echo $this->translate("Popular Chanel Photos"); ?></span>
      </div>
      <div class="ses_ml_more_popup_bc sesbasic_clearfix">
      	<?php  $photoItems = Engine_Api::_()->getDbTable('chanelphotos', 'sesvideo')->getPhotoSelect(array('limit_data'=>9,'order'=>'view_count DESC')); 
        			 foreach($photoItems as $photoItem){ ?>
          <?php $imageURL = Engine_Api::_()->sesvideo()->getImageViewerHref($photoItem); ?>
						<a onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $photoItem->getPhotoUrl(); ?>','<?php echo $imageURL	; ?>','change')" href="<?php echo Engine_Api::_()->sesvideo()->getHrefPhoto($photoItem->getIdentity(),$photoItem->chanel_id); ?>" class="ses_ml_more_popup_photo_list ses-image-viewer"><span style="background-image:url(<?php echo $photoItem->getPhotoUrl('thumb.normalmain'); ?>);"></span></a>
        <?php } ?>
      </div>
    </div>
  </div>