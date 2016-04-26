<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class='seaocore_settings_form'>
	<div class='settings'>
    <?php 
    if( !empty($this->supportingModules)) :
			foreach( $this->supportingModules as $modName ) {
				echo "<div class='tip'><span>" . $this->translate("You do not have the latest version of the '%s'. Please upgrade it to the latest version to enable its integration with Groups / Communities Plugin.", ucfirst($modName)) . "</span></div>";
			}
    endif;
    
    ?>
  </div>
</div>

<?php $coreSettings = Engine_Api::_()->getApi('settings', 'core'); ?>
<script type="text/javascript">
//   var display_msg=0;
  window.addEvent('domready', function() {
		showInviteOption('<?php echo $coreSettings->getSetting('groupmember.invite.option', 1) ?>');
		showApprovalOption('<?php echo $coreSettings->getSetting('groupmember.member.approval.option', 1) ?>');
    showAnnouncements('<?php echo $coreSettings->getSetting('sitegroupmember.tinymceditor', 1) ?>');
    notificationEmail('none');
  });

  function showInviteOption(option) {
    if($('groupmember_invite_automatically-wrapper')) {
      if(option == 1) {
        $('groupmember_invite_automatically-wrapper').style.display='none';
      } else{
        $('groupmember_invite_automatically-wrapper').style.display='block';
      }
    }
  }
  
  function showApprovalOption(option) {
    if($('groupmember_member_approval_automatically-wrapper')) {
      if(option == 1) {
        $('groupmember_member_approval_automatically-wrapper').style.display='none';
      } else{
        $('groupmember_member_approval_automatically-wrapper').style.display='block';
      }
    }
  }

 function showAnnouncements(option) {
    if($('sitegroupmember_tinymceditor-wrapper')) {
      if(option == 1) {
        $('sitegroupmember_tinymceditor-wrapper').style.display='block';
      } else{
        $('sitegroupmember_tinymceditor-wrapper').style.display='none';
      }
    }
 }
 
  function showGroupSettings(option) {
		if($('sitegroupmember_group_settings').checked == true) {
			notificationEmail('block');
		} else {
			notificationEmail('none');
		}
 }
  function notificationEmail(display) {
		if($('sitegroupmember_settings-wrapper')) {
			$('sitegroupmember_settings-wrapper').style.display=display;
		}
		
		if($('sitegroupmember_settingsforlayout-wrapper')) {
			$('sitegroupmember_settingsforlayout-wrapper').style.display=display;
		}

  }
</script>
<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/pluginLink.tpl'; ?>
<h2 class="fleft"><?php echo $this->translate('Groups / Communities Plugin'); ?></h2>

<?php if (count($this->navigationGroup)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigationGroup)->render()
  ?>
  </div>
<?php endif; ?>

<h2><?php //echo $this->translate('Groups / Communities - Group Members Extension'); ?></h2>
<?php if( count($this->navigation) ): ?>
<div class='tabs'>
  <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
</div>
<?php endif; ?>

<?php if(!$this->hasLanguageDirectoryPermissions):?>
<div class="seaocore_tip">
  <span>
    <?php echo "Please log in over FTP and set CHMOD 0777 (recursive) on the application/languages/ directory for  change the pharse groups and group." ?>
  </span>
</div>
<?php endif; ?>

<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>