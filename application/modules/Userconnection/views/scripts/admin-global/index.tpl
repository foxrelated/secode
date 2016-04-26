<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Userconnection Plugin')?></h2>
<div class='tabs'>
  <?php
    // Render the menu
    echo $this->navigation()
      ->menu()
      ->setContainer($this->navigation)
      ->render();
  ?>
</div>
<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_upgrade_messages.tpl'; ?>
<div class='seaocore_settings_form'>
  <div class='settings'>
		<?php  echo $this->form->render($this)  ?>
  </div>
</div>
  
<script type="text/javascript">

$$('input[type=radio]:([name=message])').addEvent('click', function(e){
    $(this).getParent('.form-wrapper').getAllNext(':([id^=show_msg-element])').setStyle('display', ($(this).get('value')>0?'none':'none'));
});
$('userconnection_message-5').addEvent('click', function(){
    $('show_msg-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
    $('show_msg-label').setStyle('display', ($(this).get('value')>0?'block':'none'));
});
$('userconnection_message-6').addEvent('click', function(){
    $('show_msg-wrapper').setStyle('display', ($(this).get('value')>0?'none':'block'));
    $('show_msg-label').setStyle('display', ($(this).get('value')>0?'none':'block'));
});  

window.addEvent('domready', function() { 
var e4 = $('userconnection_message-5');
$('show_msg-wrapper').setStyle('display', (e4.checked ?'block':'none'));
$('show_msg-label').setStyle('display', (e4.checked ?'block':'none'));
var e5 = $('userconnection_message-6');
$('show_msg-wrapper').setStyle('display', (e5.checked?'none':'block'));
$('show_msg-label').setStyle('display', (e5.checked?'none':'block'));
});
</script>