<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: signupinvite.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
	$session = new Zend_Session_Namespace();
	$session->friendInvite = 1;
?>
<div class="global_form">
	<div style="float:none;">
		<div>
			<?php 
				$getSignupAction = Engine_Api::_()->suggestion()->getAction();
				$webmail_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggestion.show.webmail');
				$this->webmail_show = unserialize($webmail_show);
			?>

			<h3>
				<?php if( empty($getSignupAction) ){ echo $this->translate("Find your Friends"); }else { echo $this->translate("Invite your friends"); } ?>
			</h3>
			<p><?php echo $this->translate("You can use any of the tools on this page to find and connect with more friends."); ?></p>

	<?php if (($this->user_id) || !empty($getSignupAction) ) { ?>
	<div id="id_show_networkcontacts" style="display:block"  class="suggestion_inviter">
		<div class="header">	
			<div class="title">	
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/webmail.png', '') ?>
				<?php echo $this->translate("Find People You Emailasdsadasdas"); ?>				
			</div>
			<div class="webmail-options">
				<a href="javascript:void(0);" onclick="showhide('id_show_networkcontacts', 'id_csvcontacts')"><?php echo $this->translate("Upload Contact File"); ?></a><br>
				<div class="icons">
					<a href="javascript:void(0);" onclick="showhide('id_show_networkcontacts', 'id_csvcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/addressbook.png', '',array('title' => 'Mac Address Book')) ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_show_networkcontacts', 'id_csvcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/linkedin16.png', '',array('title' => 'LinkedIn Address Book')) ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_show_networkcontacts', 'id_csvcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/thunderbird.png', '',array('title' => 'Thunderbird Address Book')) ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_show_networkcontacts', 'id_csvcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/outlook.png', '',array('title' => 'Microsoft Outlook Address Book')) ?></a>
					
					<a href="javascript:void(0);" onclick="showhide('id_show_networkcontacts', 'id_csvcontacts')"><?php echo $this->translate("more"); ?> <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/plus.png', '') ?></a>
           
				</div>
				<div class="help-link" style="margin:top 10px;">
					<a href="javascript:void(0);" onclick="signupInvite()" ><?php echo $this->translate("or Add Email Addresses Manually"); ?></a>
				</div>
			</div>
		</div>
		<div class="sub-title">
			<?php echo $this->translate("Search your Webmail account."); ?>			
		</div>
		<div class="webacc-logos">
			 <!--FINDING FRIENDS FROM USER'S GOOGLE CONTACTS LIST.-->
			<?php if( in_array("gmail", $this->webmail_show) ) { ?>
				<a href='javascript:void(0)' onclick="show_contacts_google (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/gmail.png', '') ?></a>
			<?php } ?>
			
			<!--FINDING FRIENDS FROM USER'S YAHOO CONTACTS LIST.-->
			<?php $yahoo_apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('yahoo.apikey');
						$yahoo_secret = Engine_Api::_()->getApi('settings', 'core')->getSetting('yahoo.secretkey');
			 if (!empty($yahoo_apikey) && !empty($yahoo_secret) && in_array("yahoo", $this->webmail_show) ) {?>
				<a href='javascript:void(0)' onclick="show_contacts_yahoo (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/yahoomail.png', '') ?></a>
      <?php } ?>

			<!--FINDING FRIENDS FROM USER'S WINDOW LIVE CONTACTS LIST.-->
			<?php $windowlive_apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('windowlive.apikey');
						$windowlive_secret = Engine_Api::_()->getApi('settings', 'core')->getSetting('windowlive.secretkey');
			 if (!empty($windowlive_apikey) && !empty($windowlive_secret) && in_array("window_mail", $this->webmail_show) ) {?>
				<a href='javascript:void(0)' onclick="show_contacts_windowlive (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/windowslive.png', '') ?></a>
			<?php } ?>
			<!--FINDING FRIENDS FROM USER'S AOL CONTACTS LIST.-->
			<?php if( in_array("aol", $this->webmail_show) ) { ?>
				<a href='javascript:void(0)' onclick="show_contacts_aol (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/aol.png', '') ?></a>
			<?php } ?>
		</div>	
		<div class="sub-txt">
			<?php echo $this->translate("Click on one of the above services to search your email account."); ?>
			<br />
			<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/lock.gif', '', array('style'=>'float:left;margin-right:5px;')) ?>
			<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');?>
			<?php echo $this->translate("will not store your account information."); ?>			
		</div>
	</div>
	
	<div id="id_csvcontacts" style="display:none" class="suggestion_inviter">
		<div class="header">	
			<div class="title">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/webmail.png', '') ?>
				<?php echo $this->translate("Find People You Email"); ?>				
			</div>	
			<div class="webmail-options">
				<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->translate("Use your webmail contacts"); ?></a><br>
				<div class="icons">
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/gmail16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/yahoo16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/windowslive16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/aol16.png', '') ?></a>
				</div>
				<div class="help-link" style="margin:top 10px;">
					<a href="javascript:void(0);" onclick="signupInvite()" ><?php echo $this->translate("or Add Email Addresses Manually"); ?></a>
				</div>
			</div>
		</div>
		<div class="sub-title">
			<?php echo $this->translate("Search your contacts in your contact file."); ?>			
		</div>
		<div class="upload-contact-file">
			<div class="op-cat"><?php echo $this->translate("Contact file :"); ?></div>
			<div class="op-field">
				<iframe id='ajaxframe' name='ajaxframe' style='display: none;' src='javascript:void(0);' onchange="myform();"></iframe>
				<form method="post" action="<?php echo $this->layout()->staticBaseUrl . 'seaocore/usercontacts/uploads'?>" name="csvimport" id="csvimport" enctype="multipart/form-data" target="ajaxframe"> 

					<input name="Filedata"  class="inputbox" type="file"  id="Filedata"  size="23" value="" onchange="savefilepath();"><br />
					<span><?php echo $this->translate("Contact file must be of .csv or .txt format"); ?></span><br />
					<button style="margin-top:10px;" id="csvmasssubmit" name="csvmasssubmit" onClick="getcsvcontacts();return false;"><?php echo $this->translate("Find Friends"); ?></button>
				</form>
			</div>	
		</div>
		<div class="help-link">
     	<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/support.png', '', array('style'=>'float:left;margin-right:2px;')) ?>
			<a href="javascript:void(0);" onclick="show_services();"><?php echo $this->translate("Supported Services"); ?></a><br>
			<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/help.png', '', array('style'=>'float:left;margin-right:2px;')) ?>
			<a href="javascript:void(0);" onclick="show_createfile();"><?php echo $this->translate("How to create a contact file"); ?></a>
		</div>	
	</div>
<?php } ?>

   <div id="id_success_frequ" style="display:none;">
		<ul class="form-notices" style="float:left;margin:0;"><li  style="width:350px;"><?php echo $this->translate("Your friend request(s) have been successfully sent!"); ?></li></ul>
  </div>
	<div id="id_nonsite_success_mess" style="display:none;">
		<ul class="form-notices" style="float:left;margin:0;"><li style="width:680px;"><?php echo $this->translate("Your invitation(s) were sent successfully. If the persons you invited decide to join, they will automatically receive a friend request from you."); ?></li></ul>
	</div>		
    <br />
	<div class="suggestion_inviter" style="display:none;" id="network_friends">
		<div id="show_contacts"> </div>
	</div>		

	<div id="id_csvformate_error_mess" style="display:none;">
		<ul class="form-errors"><li><ul class="errors"><li><?php echo $this->translate("Invalid file format."); ?></li></ul></li></ul>
  </div>
   <br />
	<div class="suggestion_inviter" style="display:none;" id="network_friends">
		<div id="show_contacts"> </div>
	</div>		

	
	<div class="suggestion_inviter" style="display:none;" id="csv_friends">
		<div id="show_contacts_csv"> </div>
	</div>
		
		
			<form id="SignupForm" method="post" enctype="multipart/form-data">
				<input type="hidden"  name="skip" id="skip" value="">    
				<button onclick="skipForm(); return false;" type="button" id="skiplink" name="skiplink">skip this step</button>
			</form>
		</div>
	</div>		

	<?php
		include_once(APPLICATION_PATH ."/application/modules/Suggestion/views/scripts/_friendInviteContent.tpl");
	?>