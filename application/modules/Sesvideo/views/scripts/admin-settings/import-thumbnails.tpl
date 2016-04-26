<?php

/**
* SocialEngineSolutions
*
* @category   Application_Sesvideo
* @package    Sesvideo
* @copyright  Copyright 2015-2016 SocialEngineSolutions
* @license    http://www.socialenginesolutions.com/license/
* @version    $Id: index.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
* @author     SocialEngineSolutions
*/
?>
<?php include APPLICATION_PATH .  '/application/modules/Sesvideo/views/scripts/dismiss_message.tpl';?>

<script type="text/javascript">

  function importVideoThumbnails() {

    $('loading_image').style.display = '';
    $('video_import').style.display = 'none';
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'admin/sesvideo/settings/import-thumbnails',
      method: 'get',
      data: {
        'is_ajax': 1,
        'format': 'json',
      },
      onSuccess: function(responseJSON) {
        if (responseJSON.error_code) {
          $('loading_image').style.display = 'none';
          $('video_message').innerHTML = "<span>Some error might have occurred during the import process. Please refresh the page and click on 'Start Importing' again to complete the import process.</span>";
        } else {
          $('loading_image').style.display = 'none';
          $('video_message').style.display = 'none';
          $('video_message1').innerHTML = "<span>" + '<?php echo $this->string()->escapeJavascript($this->translate("Video thumbnails have been imported successfully.")) ?>' + "</span>";
        }
      }
    }));
  }
</script>
<div class='settings'>
  <form class="global_form">
    <div>
      <h3><?php echo $this->translate('Import Thumbnails');?></h3>
      <p class="description">
        <?php echo $this->translate('Here, import thumbnails from existing videos in SocialEngine’s Video Sharing plugin. When videos were uploaded in SocialEngine’s Video Sharing plugin, then the size of thumbnails created were smaller, and in this plugin we have used bigger images, so we suggest you to import all those thumbnails in this plugin for better visibility of thumbnails.'); ?>
      </p>
      <div class="clear sesvideo_import_msg sesvideo_import_loading" id="loading_image" style="display: none;">
        <span><?php echo $this->translate("Importing ...") ?></span>
      </div>
      <div id="video_message" class="clear sesvideo_import_msg sesvideo_import_error"></div>
      <div id="video_message1" class="clear sesvideo_import_msg sesvideo_import_success"></div>
      <?php if(count($this->results) > 0): ?>
        <div id="video_import">
          <button class="sesvideo_import_button" type="button" name="sesvideo_import" onclick='importVideoThumbnails();'>
            <?php echo $this->translate('Start Importing');?>
          </button>
        </div>
      <?php else: ?>
        <div class="tip">
          <span>
            <?php echo $this->translate('There are no video.') ?>
          </span>
        </div>
      <?php endif; ?>
    </div>
  </form>
</div>