<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

if( !empty($this->isModsSupport) ):
	foreach( $this->isModsSupport as $modName ) {
		echo $this->translate('<div class="tip"><span>Note: Your website does not have the latest version of "%s". Please upgrade "%s" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Suggestions / Recommendations Plugin".</span></div>', ucfirst($modName), ucfirst($modName));
	}
endif;
?>
<h2><?php echo $this->translate('Suggestions / Recommendations Plugin')?></h2>
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
	<a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'admin-settings', 'action' => 'help-invite'), 'default', true) ?>" class="buttonlink" style="background-image:url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Suggestion/externals/images/admin/help.gif);padding-left:23px;"><?php echo $this->translate("Guidelines to configure the applications for Contact Importer Settings") ?></a>
  <div class='settings' style="margin-top:15px;">
	<?php  echo $this->form->render($this)  ?>
  </div>
</div>

<script type="text/javascript">
if (<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('suggestion.friend.invite.enable', 1);?> == 0) {
  $('suggestion_show_webmail-wrapper').style.display = 'none';		
  
}
  if ($('suggestion_friend_invite_enable-1')) {
  $('suggestion_friend_invite_enable-1').addEvent('click', function () {
  		$('suggestion_show_webmail-wrapper').style.display = 'block';		
  })
}

if ($('suggestion_friend_invite_enable-0')) {
  $('suggestion_friend_invite_enable-0').addEvent('click', function () {
  		$('suggestion_show_webmail-wrapper').style.display = 'none';		
  })
}
</script>