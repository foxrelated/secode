<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: append-songs.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesmusic/externals/styles/styles.css'); ?>
<div class='sesmusic_playlist_popup'>
  <?php if( isset($this->success) ): ?>
  <div class="global_form_popup_message">
    <?php if( $this->success ): ?>
    <p><?php echo $this->message ?></p>
    <br />
    <?php elseif( !empty($this->error) ): ?>
    <pre style="text-align:left"><?php echo $this->error ?></pre>
    <?php else: ?>
    <p><?php echo $this->translate('There was an error processing your request.  Please try again later.') ?></p>
    <?php endif; ?>
  </div>
  <?php return; endif; ?>
  <?php echo $this->form->render($this) ?>
</div>
<script type="text/javascript">
  function updateTextFields() {
    if ($('playlist_id').value == 0) {
      $('title-wrapper').show();
      $('description-wrapper').show();
      $('mainphoto-wrapper').show();
    } else {
      $('title-wrapper').hide();
      $('description-wrapper').hide();
      $('mainphoto-wrapper').hide();
    }
    parent.Smoothbox.instance.doAutoResize();
  }
  en4.core.runonce.add(updateTextFields);
</script>