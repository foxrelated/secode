<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: location.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesvideo/externals/styles/styles.css'); ?>
<div class="sesvideo_edit_location_popup">
  <?php echo $this->form->render($this) ?>
</div>
<script type="text/javascript">
$('lat-wrapper').style.display = 'none';
$('lng-wrapper').style.display = 'none';
sesJqueryObject('#mapcanvas-label').attr('id','map-canvas-list');
sesJqueryObject('#map-canvas-list').css('height','200px');
sesJqueryObject('#ses_location-label').attr('id','ses_location_data_list');
sesJqueryObject('#ses_location_data_list').html('<?php echo $this->video->location; ?>');
sesJqueryObject('#ses_location-wrapper').css('display','none');
<?php if($this->type == 'video_location'){ ?>
	sesJqueryObject('#location-wrapper').hide();
	sesJqueryObject('#execute').hide();
	sesJqueryObject('#or_content').hide();
	sesJqueryObject('#location-form').find('div').find('div').find('h3').hide();
	sesJqueryObject('#cancel').replaceWith('<button name="cancel" id="cancel" type="button" href="javascript:void(0);" onclick="parent.Smoothbox.close();">'+en4.core.language.translate('Close')+'</button>');
<?php } ?>
initializeSesVideoMapList();
 sesJqueryObject( window ).load(function() {
	editSetMarkerOnMapListVideo();
	});
</script>