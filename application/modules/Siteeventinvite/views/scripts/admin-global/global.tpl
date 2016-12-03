<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventinvite
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: global.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate('Advanced Events Plugin');?>
</h2>
<?php if (count($this->navigationEvent)): ?>
	<div class='seaocore_admin_tabs'>
		<?php echo $this->navigation()->menu()->setContainer($this->navigationEvent)->render() ?>
	</div>
<?php endif; ?>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'admin-settings', 'action' => 'help-invite'), 'default', true) ?>" class="buttonlink" style="background-image:url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Siteeventinvite/externals/images/admin/help.gif);"><?php echo $this->translate("Guidelines to configure the applications for Contact Importer Settings") ?></a>
  <br /><br />
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>

<script type="text/javascript">
if (<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('eventinvite.friend.invite.enable', 1);?> == 0) {
  $('eventinvite_show_webmail-wrapper').style.display = 'none';		
  
}
  if ($('eventinvite_friend_invite_enable-1')) {
  $('eventinvite_friend_invite_enable-1').addEvent('click', function () {
  		$('eventinvite_show_webmail-wrapper').style.display = 'block';		
  })
}
if ($('eventinvite_friend_invite_enable-0')) {
  $('eventinvite_friend_invite_enable-0').addEvent('click', function () {
  		$('eventinvite_show_webmail-wrapper').style.display = 'none';		
  })
}
</script>