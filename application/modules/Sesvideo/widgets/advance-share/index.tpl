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
<?php $addThisCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('ses.addthis',0); ?>
<?php if($addThisCode && in_array('addThis',$this->allowAdvShareOptions)){ ?>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo $addThisCode; ?>" async></script>
<?php } ?>
 <button class="sesbasic_popup_slide_open sesbasic_bxs sesbasic_share_btn"><?php echo $this->translate("Share")?></button>
<!-- Slide in -->
<div id="sesbasic_popup_slide" class="well" style="display:none">
  <div class="sesbasic_popup sesbasic_bxs">
    <div class="sesbasic_popup_title">
       <?php echo $this->translate("Share This %s",ucfirst(str_replace(array('sesvideo_',''),'',$this->subject()->getType()))); ?>
      <span class="sesbasic_popup_slide_close sesbasic_text_light">
        <i class="fa fa-close"></i>
      </span>
    </div>
    <div class="sesbasic_popup_content">
      <div class="sesbasic_share_popup_content_row clear sesbasic_clearfix">
      	<div class="sesbasic_share_popup_buttons clear">
          <?php if(in_array('privateMessage',$this->allowAdvShareOptions)){ ?>
            <a href="javascript:void(0)" class="sesbasic_button" onClick="opensmoothboxurl('<?php echo $this->url(array('module'=> 'sesbasic', 'controller' => 'index', 'action' => 'message','item_id' => $this->subject()->getIdentity(), 'type'=>$this->subject()->getType()),'default',true); ?>')"> <?php echo $this->translate("Private Message"); ?></a>
            <?php } ?>
             <?php if(in_array('siteShare',$this->allowAdvShareOptions)){ ?>
            <a href="javascript:void(0)" class="sesbasic_button" onClick="opensmoothboxurl('<?php echo $this->url(array('module'=> 'sesvideo', 'controller' =>'index','action' => 'share','type' => $this->subject()->getType(),'id' => $this->subject()->getIdentity(),'format' => 'smoothbox'),'default',true); ?>')"> <?php echo $this->translate("Share on Site"); ?></a>
            <?php } ?>
             <?php if(in_array('quickShare',$this->allowAdvShareOptions)){ ?>
            <a href="javascript:void(0)" class="sesbasic_button" onClick="sessendQuickShare('<?php echo $this->url(array('module'=> 'sesvideo', 'controller' =>'index','action' => 'share','type' => $this->subject()->getType(),'id' => $this->subject()->getIdentity()),'default',true); ?>');return false;"> <?php echo $this->translate("Quick Share on Site"); ?></a>
          <?php } ?>
        </div>
      </div>
      <?php if($addThisCode && in_array('addThis',$this->allowAdvShareOptions)){ ?>
      	<div class="sesbasic_share_popup_content_row clear sesbasic_clearfix">
          <div class="sesbasic_share_popup_content_field clear">
            <!-- Go to www.addthis.com/dashboard to customize your tools -->
            <div class="addthis_sharing_toolbox"></div>
          </div>
      	</div>
    	<?php } ?>
      <div class="sesbasic_share_popup_content_row">
      	<div class="sesbasic_share_itme_preview sesbasic_clearfix">
        	<div class="sesbasic_share_itme_preview_img">
          	<img src="<?php echo $this->subject()->getPhotoUrl();?>" />
          </div>
          <div class="sesbasic_share_itme_preview_info">
          	<div class="sesbasic_share_itme_preview_title">
            	<a href="<?php echo $this->subject()->getHref();?>"><?php echo $this->subject()->title;?></a>
            </div>
            <div class="sesbasic_share_itme_preview_des">
             <?php if(strlen($this->subject()->description) > 200){ 
                  $description = mb_substr($this->subject()->description,0,200).'...';
                  echo nl2br(strip_tags($description));
                 }else{ ?>
              <?php  echo nl2br(strip_tags($this->subject()->description));?>
              <?php } ?>
            </div>	
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery-1.8.2.min.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.popupoverlay.js'); ?>
<script type="text/javascript">
jquery1_8_2SesObject(document).ready(function () {
	jquery1_8_2SesObject('#sesbasic_popup_slide').popup({
			focusdelay: 400,
			outline: true,
			vertical: 'top'
	});
});
</script>