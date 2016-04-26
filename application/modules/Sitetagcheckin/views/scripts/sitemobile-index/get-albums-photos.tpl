<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
	$i=0;
?>

<?php if (empty($this->is_ajax)) : ?>
  <div id='location_photo_image_recent' class="seaocore_photo_strips">
  <?php endif; ?>		
  <?php $a = "javascript:void(0);"?>
  <?php if($this->paginator->getTotalItemCount() > 0):?>
		<?php foreach ($this->paginator as $photo):?>
		
				<div class='thumb_photo'> 
				<?php echo  $this->htmlLink($a,
					$this->itemPhoto($photo, 'thumb.normal', $photo->getTitle()),  array('class'=>'sea_add_tooltip_autosuggest', 'rel'=>$photo->getType().'_'.$photo->getIdentity())
				)  ?>
				<div id='hide_<?php echo $photo->getIdentity(); ?>' class="photo_hide">
					<a href="javascript:void(0);" title="<?php echo $this->translate('Skip this photo'); ?>" onclick="skipAlbumPhoto(<?php echo $photo->photo_id; ?>);" ></a>
				</div>
			</div>
    
			<?php $i++; ?>

		<?php endforeach; ?>
  <?php else:?>
  	<div class="tip" style="margin:0px;">
  		<span style="margin:0px;">
    		<?php echo $this->translate("You have no more photos to add to your map.");?>
    	</span>
    </div>		
  <?php endif;?>
  <?php if (empty($this->is_ajax)) : ?>
  </div> 
<?php endif; ?>


<script type="text/javascript">

  var submit_topageprofile = true;
  function skipAlbumPhoto(photo_id)
  {	
    submit_topageprofile = false;
   
    sm4.core.request.send({     
      method : 'post',
      dataType: "html",
      'url' : sm4.core.baseUrl + 'sitetagcheckin/index/get-albums-photos',
      'data' : {
        format : 'html',
        //'subject' : '<?php //echo $this->subject()->getGuid() ?>',
        isajax : 1,
        itemCountPerPage:'<?php echo $this->limit ?>',
        skip_photo_id : photo_id
      },
      onSuccess : function(responseHTML, textStatus, xhr) {
        $('#location_photo_image_recent').html(responseHTML);
      }
    });
	
    return false;

  }

</script>