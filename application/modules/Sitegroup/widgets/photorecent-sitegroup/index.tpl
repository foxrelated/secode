<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
  <?php
  $k=0;
  ?>
<?php if (empty($this->is_ajax)) : ?>
  <div id='photo_image_recent' class="sitepge_photos_list" <?php if ($this->currenttabid): ?>style="display:none"<?php else: ?>style="display:block"<?php endif; ?>>
<?php endif; ?>	
  <?php foreach ($this->paginator as $photo): ?>
    <div class="thumb_photo"> 
      <?php //if (!$this->showLightBox): ?>
<!--        <a href="javascript:void(0)" onclick='ShowPhotoGroup("<?php //echo $photo->getHref() ?>")' title="<?php //echo $photo->title; ?>" style="background-image:url(<?php //echo $photo->getPhotoUrl('thumb.normal'); ?>);" class="thumb_img">			
        </a>-->
      <?php //else: ?>
        <a href="<?php echo $photo->getHref() ?>"  <?php if(SEA_SITEGROUPALBUM_LIGHTBOX) :?> onclick="openSeaocoreLightBox('<?php echo $photo->getHref() . '/type/strip_creation_date' . '/count/'. $this->row_count. '/offset/' . $k. '/group_id/' . $this->sitegroup_subject->group_id; ?>');return false;" <?php endif;?> style="background-image:url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);" class="thumb_img">
        </a>
      <?php //endif; ?>
      <?php if ($this->can_edit): ?>
        <div id='hide_<?php echo $photo->photo_id; ?>' class="photo_hide">
          <a href="javascript:void(0);" title="<?php echo $this->translate('Hide this photo'); ?>" onclick="hidephoto(<?php echo $photo->photo_id; ?>, <?php echo $this->sitegroup_subject->group_id; ?>);" ></a>
        </div>
      <?php endif; ?>
    </div>
    <?php $k++;?>
  <?php endforeach; ?>    
  <?php if ($this->count > 0 && $this->can_edit): ?>
    <?php if (count($this->paginator) < $this->limit): ?>
      <div class="thumb_photo">
        <a href='javascript:void(0);' title="<?php echo $this->translate('Reset Photo Strip'); ?>" onclick='opensmoothbox("<?php echo $this->url(array('action' => 'unhide-photo', 'group_id' => $this->sitegroup_subject->group_id), 'sitegroup_dashboard', true) ?>");return false;'>
          <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitegroup/externals/images/photoreset.png' />
        </a>	    	
      </div>
      <?php for ($i=1; $i <$this->limit - count($this->paginator); $i++ ): ?>       
        <div class="thumb_photo"> </div>    
      <?php endfor; ?>      
    <?php endif; ?>
  <?php endif; ?>
<?php if (empty($this->is_ajax)) : ?>
  </div> 
<?php endif; ?>

<script type="text/javascript">
  var submit_togroupprofile = true;
  function hidephoto( photo_id, group_id) 
  {	
    submit_togroupprofile = false;
   
    en4.core.request.send(new Request.HTML({     
      method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitegroup/name/photorecent-sitegroup', 
      'data' : {
        format : 'html',
        'subject' : 'sitegroup_group_' + group_id,
        isajax : 1,
        hide_photo_id : photo_id
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('photo_image_recent').innerHTML = responseHTML;
      }
    }));
	
    return false;
  }

  function ShowPhotoGroup(groupurl) {
    if (submit_togroupprofile) {
      window.location = groupurl;
    }
    else {
      submit_togroupprofile = true;
    }
  }

  function opensmoothbox(url) {
    Smoothbox.open(url);
  }

</script>