<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _composeSitestoreproductVideo.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php  if (Engine_Api::_()->core()->hasSubject() && in_array($this->subject()->getType(),array('sitestoreproduct_product','sitestoreproductevent_event'))):?>
<style type="text/css">
  /*
ACTIVITY FEED COMPOSER  VIDEO
These styles are used for the attachment composer above the
main feed.
*/
#compose-video-activator,
#compose-video-menu span
{
 display: none !important;
}
</style>
 <?php
 $subject=$this->subject();
 if(in_array($subject->getType(),array('sitestoreproductevent_event'))):
    $subject = Engine_Api::_()->getItem('sitestoreproduct_product', $subject->product_id);
 endif;
 if (!Engine_Api::_()->sitestoreproduct()->isManageAdmin($subject,'svcreate') ):
    return;
 endif; ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl.'application/modules/Sitestoreproduct/externals/scripts/composer_video.js') ?>

<?php
  $allowed = 0;
  $user = Engine_Api::_()->user()->getViewer();
  $youtubeEnabled = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
  $allowed_upload = 1;
  $ffmpeg_path = (bool) Engine_Api::_()->getApi('settings', 'core')->sitestoreproduct_video_ffmpeg_path;
  if($allowed_upload && $ffmpeg_path) $allowed = 1;
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    var type = 'wall';
    if (composeInstance.options.type) type = composeInstance.options.type;
    composeInstance.addPlugin(new Composer.Plugin.Sitestoreproduct({
      title : '<?php echo $this->translate('Add Video') ?>',
      lang : {
        'Add Video' : '<?php echo $this->string()->escapeJavascript($this->translate('Add Video')) ?>',
        'Select File' : '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
        'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
        'Attach' : '<?php echo $this->string()->escapeJavascript($this->translate('Attach')) ?>',
        'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
        'Choose Source': '<?php echo $this->string()->escapeJavascript($this->translate('Choose Source')) ?>',
        'My Computer': '<?php echo $this->string()->escapeJavascript($this->translate('My Computer')) ?>',
        'YouTube': '<?php echo $this->string()->escapeJavascript($this->translate('YouTube')) ?>',
        'Vimeo': '<?php echo $this->string()->escapeJavascript($this->translate('Vimeo')) ?>',
        'To upload a video from your computer, please use our full uploader.': '<?php echo addslashes($this->translate('To upload a video from your computer, please use our <a href="%1$s">full uploader</a>.', $this->url(array('action' => 'create','product_id'=>$subject->getIdentity(), 'type'=>3), 'sitestoreproduct_video_general'))) ?>'
      },
      allowed : <?php echo $allowed;?>,
      youtubeEnabled: <?php echo (int) $youtubeEnabled?>,
      type : type,
      requestOptions : {
        'url' : en4.core.baseUrl + 'sitestoreproduct/video/compose-upload/format/json/c_type/'+type
      }
    }));
  });
</script>
<?php endif; ?>
