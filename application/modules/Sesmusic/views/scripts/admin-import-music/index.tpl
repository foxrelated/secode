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
<?php include APPLICATION_PATH .  '/application/modules/Sesmusic/views/scripts/dismiss_message.tpl';?>

<script type="text/javascript">

  function importMusicPlaylists() {

    $('loading_image').style.display = '';
    $('musicplaylist_import').style.display = 'none';
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'admin/sesmusic/import-music',
      method: 'get',
      data: {
        'is_ajax': 1,
        'format': 'json',
      },
      onSuccess: function(responseJSON) {
        if (responseJSON.error_code) {
          $('loading_image').style.display = 'none';
          $('musicplaylist_message').innerHTML = "<span>Some error might have occurred during the import process. Please refresh the page and click on “Start Importing Music” again to complete the import process.</span>";
        } else {
          $('loading_image').style.display = 'none';
          $('musicplaylist_message').style.display = 'none';
          $('musicplaylist_message1').innerHTML = "<span>" + '<?php echo $this->string()->escapeJavascript($this->translate("Playlists from SE Music have been successfully imported.")) ?>' + "</span>";
        }
      }
    }));
  }
</script>
<div class='settings'>
  <form class="global_form">
    <div>
      <h3><?php echo $this->translate('Import SE Music into this Plugin');?></h3>
      <p class="description">
        <?php echo $this->translate('Here, you can import playlists from SE Music plugin into this plugin. All the playlists in the SE Music plugin will become Music Albums in this plugin and users can edit their Music Albums and Songs to add main photos and Covers photos to them.'); ?>
      </p>
      <div class="clear sesmusic_import_msg sesmusic_import_loading" id="loading_image" style="display: none;">
        <span><?php echo $this->translate("Importing ...") ?></span>
      </div>
      <div id="musicplaylist_message" class="clear sesmusic_import_msg sesmusic_import_error"></div>
      <div id="musicplaylist_message1" class="clear sesmusic_import_msg sesmusic_import_success"></div>
      <?php if(count($this->playlistresults) > 0): ?>
        <div id="musicplaylist_import">
          <button class="sesmusic_import_button" type="button" name="sesmusic_import" onclick='importMusicPlaylists();'>
            <?php echo $this->translate('Start Importing Music');?>
          </button>
        </div>
      <?php else: ?>
        <div class="tip">
          <span>
            <?php echo $this->translate('There are no playlists in SE Music plugin to be imported into this plugin.') ?>
          </span>
        </div>
      <?php endif; ?>
    </div>
  </form>
</div>