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
<?php include APPLICATION_PATH .  '/application/modules/Sesvideo/views/scripts/dismiss_message.tpl';?>
<div class='clear sesbasic_admin_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<div class="sesbasic_waiting_msg_box" style="display:none;">
	<div class="sesbasic_waiting_msg_box_cont">
    <?php echo $this->translate("Please wait.. It might take some time to activate plugin."); ?>
    <i></i>
  </div>
</div>
<?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo.pluginactivated',0)){ 
 $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/sesJquery.js');?>
	<script type="application/javascript">
  	sesJqueryObject('.global_form').submit(function(e){
			sesJqueryObject('.sesbasic_waiting_msg_box').show();
		});
  </script>
<?php }else{ ?>
<script type="application/javascript">
  function confirmChangeLandingPage(value){
	if(value == 1 && !confirm('Are you sure want to set the default Welcome page of this plugin as the Landing page of your website. Your old landing page will not be recoverable after changing it using this setting.')){
		document.getElementById('sevideoset_landingpage-0').checked = true;
	}
}
  window.addEvent('domready', function() {
      rating_video("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('video.video.rating', 1); ?>");
      checkChange("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('video.enable.chanel', 0); ?>");
			//rating_chanel("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('video.chanel.rating', 1); ?>");
  });
  
  
	function rating_video(value){
		if(value == 1){
      if(document.getElementById('video_ratevideo_own-wrapper'))
      	document.getElementById('video_ratevideo_own-wrapper').style.display = 'block';
      if(document.getElementById('video_ratevideo_again-wrapper'))
        document.getElementById('video_ratevideo_again-wrapper').style.display = 'block';
      if(document.getElementById('video_ratevideo_show-wrapper'))
        document.getElementById('video_ratevideo_show-wrapper').style.display = 'none';	
		} else{
      if(document.getElementById('video_ratevideo_show-wrapper'))
        document.getElementById('video_ratevideo_show-wrapper').style.display = 'block';
      if(document.getElementById('video_ratevideo_own-wrapper'))
        document.getElementById('video_ratevideo_own-wrapper').style.display = 'none';
      if(document.getElementById('video_ratevideo_again-wrapper'))
        document.getElementById('video_ratevideo_again-wrapper').style.display = 'none';
		}
	} 
  
  if(document.getElementById('video_video_rating-wrapper')) {
    if(document.getElementById('video_video_rating').value == 1) {
      document.getElementById('video_ratevideo_own-wrapper').style.display = 'block';		
      document.getElementById('video_ratevideo_again-wrapper').style.display = 'block';
      document.getElementById('video_ratevideo_show-wrapper').style.display = 'none';
    } 
  } else{
      document.getElementById('video_ratevideo_show-wrapper').style.display = 'block';
      document.getElementById('video_ratevideo_own-wrapper').style.display = 'none';
      document.getElementById('video_ratevideo_again-wrapper').style.display = 'none';
	}
  
	function rating_chanel(value){
		if(value == 1){
			document.getElementById('video_chanel_rating-wrapper').style.display = 'block';		
			document.getElementById('video_ratechanel_own-wrapper').style.display = 'block';		
			document.getElementById('video_ratechanel_again-wrapper').style.display = 'block';
			document.getElementById('video_ratechanel_show-wrapper').style.display = 'none';	
		} else{
			document.getElementById('video_chanel_rating-wrapper').style.display = 'block';
			document.getElementById('video_ratechanel_show-wrapper').style.display = 'block';
			document.getElementById('video_ratechanel_own-wrapper').style.display = 'none';
			document.getElementById('video_ratechanel_again-wrapper').style.display = 'none';
		}
	} 
  
	/*if(document.getElementById('video_chanel_rating-wrapper').value == 1){
		document.getElementById('video_ratechanel_own-wrapper').style.display = 'block';		
			document.getElementById('video_ratechanel_again-wrapper').style.display = 'block';
			document.getElementById('video_ratechanel_show-wrapper').style.display = 'none';
	} else{
		document.getElementById('video_ratechanel_show-wrapper').style.display = 'block';
			document.getElementById('video_ratechanel_own-wrapper').style.display = 'none';
			document.getElementById('video_ratechanel_again-wrapper').style.display = 'none';
	}*/
	
	
	function checkChange(value){
		if(value == 1){
			document.getElementById('video_enable_chaneloption-wrapper').style.display = 'block';
		document.getElementById('video_chanels_manifest-wrapper').style.display = 'block';	
		document.getElementById('video_chanel_manifest-wrapper').style.display = 'block';	
		document.getElementById('videochanel_category_enable-wrapper').style.display = 'block';	
		document.getElementById('video_enable_chaneloption-wrapper').style.display = 'block';	
		document.getElementById('video_enable_subscription-wrapper').style.display = 'block';
		} else{
			document.getElementById('video_enable_chaneloption-wrapper').style.display = 'none';
			document.getElementById('video_chanels_manifest-wrapper').style.display = 'none';	
			document.getElementById('video_chanel_manifest-wrapper').style.display = 'none';	
			document.getElementById('videochanel_category_enable-wrapper').style.display = 'none';	
			document.getElementById('video_enable_chaneloption-wrapper').style.display = 'none';	
			document.getElementById('video_enable_subscription-wrapper').style.display = 'none';
		}
	} 
	if(document.getElementById('video_enable_chanel').value == 1){
		document.getElementById('video_enable_chaneloption-wrapper').style.display = 'block';
		document.getElementById('video_chanels_manifest-wrapper').style.display = 'block';	
		document.getElementById('video_chanel_manifest-wrapper').style.display = 'block';	
		document.getElementById('videochanel_category_enable-wrapper').style.display = 'block';	
		document.getElementById('video_enable_chaneloption-wrapper').style.display = 'block';	
		document.getElementById('video_enable_subscription-wrapper').style.display = 'block';
		if(document.getElementById('video_chanel_rating').value == 1){
			document.getElementById('video_chanel_rating-wrapper').style.display = 'block';		
			document.getElementById('video_ratechanel_own-wrapper').style.display = 'block';		
			document.getElementById('video_ratechanel_again-wrapper').style.display = 'block';
			document.getElementById('video_ratechanel_show-wrapper').style.display = 'none';	
		} else{
			document.getElementById('video_chanel_rating-wrapper').style.display = 'block';
			document.getElementById('video_ratechanel_show-wrapper').style.display = 'block';
			document.getElementById('video_ratechanel_own-wrapper').style.display = 'none';
			document.getElementById('video_ratechanel_again-wrapper').style.display = 'none';
		}
	} else{
		document.getElementById('video_enable_chaneloption-wrapper').style.display = 'none';
		document.getElementById('video_chanels_manifest-wrapper').style.display = 'none';	
		document.getElementById('video_chanel_manifest-wrapper').style.display = 'none';	
		document.getElementById('videochanel_category_enable-wrapper').style.display = 'none';	
		document.getElementById('video_enable_chaneloption-wrapper').style.display = 'none';	
		document.getElementById('video_enable_subscription-wrapper').style.display = 'none';
		document.getElementById('video_chanel_rating-wrapper').style.display = 'none';		
		document.getElementById('video_ratechanel_own-wrapper').style.display = 'none';		
		document.getElementById('video_ratechanel_again-wrapper').style.display = 'none';
		document.getElementById('video_ratechanel_show-wrapper').style.display = 'none';
	}
</script>
<script>
  function rating_artist(value) {
    if (value == 1) {
      //document.getElementById('sesvideo_rateartist_own-wrapper').style.display = 'block';
      document.getElementById('sesvideo_rateartist_again-wrapper').style.display = 'block';
      document.getElementById('sesvideo_rateartist_show-wrapper').style.display = 'none';
    } else {
      document.getElementById('sesvideo_rateartist_show-wrapper').style.display = 'block';
      //document.getElementById('sesvideo_rateartist_own-wrapper').style.display = 'none';
      document.getElementById('sesvideo_rateartist_again-wrapper').style.display = 'none';
    }
  }

  if (document.querySelector('[name="sesvideo_artist_rating"]').value == 0) {
    //document.getElementById('sesvideo_rateartist_own-wrapper').style.display = 'none';
    document.getElementById('sesvideo_rateartist_again-wrapper').style.display = 'none';
    document.getElementById('sesvideo_rateartist_show-wrapper').style.display = 'block';
  } else {
    document.getElementById('sesvideo_rateartist_show-wrapper').style.display = 'none';
  }
</script>
<?php  } ?>