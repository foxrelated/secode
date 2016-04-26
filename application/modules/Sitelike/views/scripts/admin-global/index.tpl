<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

if( !empty($this->isModsSupport) ):
	foreach( $this->isModsSupport as $modName ) {
		echo "<div class='tip'><span>" . $this->translate("Note: You do not have the latest version of the '%s'. Please upgrade it to the latest version to enable its integration with Likes Plugin and Widgets.", ucfirst($modName)) . "</span></div>";
	}
endif;
?>
<h2>
  <?php echo $this->translate('Likes Plugin & Widgets') ?>
</h2>
<?php if( count($this->navigation) ): ?>
<div class='tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>
<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_upgrade_messages.tpl'; ?>
<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php
      //RENDEDR THE FORM		
      echo $this->form->render($this);
    ?>
  </div>
</div>
<script type="text/javascript">

// window.addEvent('domready', function() { 
// var e4 = $('like_profile_show-1');
// $('like_setting_show-wrapper').setStyle('display', (e4.checked ?'block':'none'));
// var e5 = $('like_profile_show-0');
// $('like_setting_show-wrapper').setStyle('display', (e5.checked?'none':'block'));
// 
// 
// $$('input[type=radio]:([name=like_profile_show])').addEvent('click', function(e){
//     $(this).getParent('.form-wrapper').getAllNext(':([id^=like_setting_show-element])').setStyle('display', ($(this).get('value')>0?'none':'none'));
// });
// $('like_profile_show-1').addEvent('click', function(){    
// 		$('like_setting_show-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
// });
// $('like_profile_show-0').addEvent('click', function(){
//     $('like_setting_show-wrapper').setStyle('display', ($(this).get('value')>0?'none':'none'));
// });  
// 
// });
</script>