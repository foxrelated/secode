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
<script type="text/javascript">

  window.addEvent('domready', function() {
    checkUpload("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.uploadoption', 'myComputer'); ?>");
  });
  
  function showPopUp() {
    Smoothbox.open('<?php echo $this->escape($this->url(array('module' =>'sesmusic', 'controller' => 'admin-settings', 'action'=>'showpopup', 'format' => 'smoothbox'), 'default' , true)); ?>');
    parent.Smoothbox.close;
  }
  
  function checkUpload(value) {
    if (value == 'both' || value == 'soundCloud') {
      if ($('sesmusic_scclientid-wrapper'))
        $('sesmusic_scclientid-wrapper').style.display = 'block';
      if ($('sesmusic_scclientscreatid-wrapper'))
        $('sesmusic_scclientscreatid-wrapper').style.display = 'block';
    } else {
      if ($('sesmusic_scclientid-wrapper'))
        $('sesmusic_scclientid-wrapper').style.display = 'none';
      if ($('sesmusic_scclientscreatid-wrapper'))
        $('sesmusic_scclientscreatid-wrapper').style.display = 'none';
    }
  }
</script>
<?php include APPLICATION_PATH .  '/application/modules/Sesmusic/views/scripts/dismiss_message.tpl';?>

<div class="sesbasic-form">
  <div>
    <?php if( count($this->subNavigation) ): ?>
      <div class='sesbasic-admin-sub-tabs'>
        <?php
        echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
        ?>
      </div>
    <?php endif; ?>
    <div class='sesbasic-form-cont'>
      <div class='settings sesbasic_admin_form'>
        <?php echo $this->form->render($this); ?>
      </div>
    </div>
  </div>
</div>