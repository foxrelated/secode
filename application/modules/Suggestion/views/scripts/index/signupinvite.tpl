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
	$step_table = Engine_Api::_()->getDbtable('signup', 'user');
	$isDefaultInviteEnabled = $step_table->fetchRow($step_table->select()->where('class = ?', 'User_Plugin_Signup_Invite'))->enable;

?>


<?php if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')):?>
	<?php echo $this->form->render($this); ?>
<?php else:?>

<div class="global_form">
	<div style="float:none;">
		<div>
			<?php 
				$getSignupAction = Engine_Api::_()->suggestion()->getAction();
				$webmail_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggestion.show.webmail');
				$this->webmail_show = unserialize($webmail_show);
			?>
			<div id="invite_info">
				<h3>
					<?php if( empty($getSignupAction) ){ echo $this->translate("Find your Friends"); }else { echo $this->translate("Invite Your Friends"); } ?>
				</h3>
				<p class="suggestion_signup_des"><?php echo $this->translate("Use the tools here to invite your friends to join! If your friends decide to sign up, a friend request from you will be waiting for them when they first sign in."); ?></p>
			</div>

	<?php if (($this->user_id) || !empty($getSignupAction) ) { ?>
	<div id="id_show_networkcontacts" style="display:block;"  class="suggestion_inviter suggestion-signup-step">
		<div class="header">	
			<div class="title" id="header_title">	
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/webmail.png', '') ?>
				<?php echo $this->translate("Find People You Email"); ?>				
			</div>
			<div id="inviter_form1" class="suggestion_signup_invite"> <?php echo $this->form->render($this); ?> </div>
			<div class="webmail-options">
				<a href="javascript:void(0);" onclick="showhideinviter('id_show_networkcontacts', 'id_csvcontacts', 3)"><?php echo $this->translate("Upload Contact File"); ?></a><br>
				<div class="icons">
					<a href="javascript:void(0);" onclick="showhideinviter('id_show_networkcontacts', 'id_csvcontacts', 3)"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/addressbook.png', '',array('title' => 'Mac Address Book')) ?></a>
					<a href="javascript:void(0);" onclick="showhideinviter('id_show_networkcontacts', 'id_csvcontacts', 3)"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/linkedin16.png', '',array('title' => 'LinkedIn Address Book')) ?></a>
					<a href="javascript:void(0);" onclick="showhideinviter('id_show_networkcontacts', 'id_csvcontacts', 3)"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/thunderbird.png', '',array('title' => 'Thunderbird Address Book')) ?></a>
					<a href="javascript:void(0);" onclick="showhideinviter('id_show_networkcontacts', 'id_csvcontacts', 3)"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/outlook.png', '',array('title' => 'Microsoft Outlook Address Book')) ?></a>
					
					<a href="javascript:void(0);" onclick="showhideinviter('id_show_networkcontacts', 'id_csvcontacts', 3)"><?php echo $this->translate("more"); ?> <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/plus.png', '') ?></a>
           
				</div>
				<div class="help-link" style="margin:top 10px;" id="help_link1">
					<a href="javascript:void(0);" onclick="showhideinviter('id_csvcontacts', 'id_show_networkcontacts', 4)" ><?php echo $this->translate("Use your Web accounts"); ?></a>
         <br>
				<div class="icons">
					<a href="javascript:void(0);" onclick="showhideinviter('id_csvcontacts', 'id_show_networkcontacts', 4)"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/gmail16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhideinviter('id_csvcontacts', 'id_show_networkcontacts', 4)"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/yahoo16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhideinviter('id_csvcontacts', 'id_show_networkcontacts', 4)"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/windowslive16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/facebook16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/twitter16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/linkedin16.png', '') ?></a>
					
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/aol16.png', '') ?></a>
				</div>
				</div>
				<div class="help-link" style="margin:top 10px;" id="help_link2">
					<a href="javascript:void(0);" onclick="showhideinviter('id_show_networkcontacts', 'id_csvcontacts', 1)" ><?php if( empty($isDefaultInviteEnabled) ) { echo $this->translate("or Add Email Addresses Manually"); } ?></a>
				</div>
			</div>
		</div>
		<div class="sub-title" id="sub-title">
			<?php echo $this->translate("Search your Webmail accounts."); ?>			
		</div>
		<div class="webacc-logos" id="webacc-logos">
			 <!--FINDING FRIENDS FROM USER'S GOOGLE CONTACTS LIST.-->
			<?php if( in_array("gmail", $this->webmail_show) ) { ?>
				<div class="webacc-logos-img">
					<a href='javascript:void(0)' onclick="show_contacts_google (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/gmail.png', '') ?></a>
				</div>	
			<?php } ?>
			
			<!--FINDING FRIENDS FROM USER'S YAHOO CONTACTS LIST.-->
			<?php $yahoo_apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('yahoo.apikey');
						$yahoo_secret = Engine_Api::_()->getApi('settings', 'core')->getSetting('yahoo.secretkey');
			 if (!empty($yahoo_apikey) && !empty($yahoo_secret) && in_array("yahoo", $this->webmail_show) ) {?>
			 	<div class="webacc-logos-img">
					<a href='javascript:void(0)' onclick="show_contacts_yahoo (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/yahoomail.png', '') ?></a>
				</div>	
      <?php } ?>

			<!--FINDING FRIENDS FROM USER'S WINDOW LIVE CONTACTS LIST.-->
			<?php $windowlive_apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('windowlive.apikey');
						$windowlive_secret = Engine_Api::_()->getApi('settings', 'core')->getSetting('windowlive.secretkey');
			 if (!empty($windowlive_apikey) && !empty($windowlive_secret) && in_array("window_mail", $this->webmail_show) ) {?>
			 	<div class="webacc-logos-img">
					<a href='javascript:void(0)' onclick="show_contacts_windowlive (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/windowslive.png', '') ?></a>
				</div>	
			<?php } ?>
			
			
			<?php
		        $client_id = Engine_Api::_()->getApi('settings', 'core')->core_facebook_appid;
		        $client_secret = Engine_Api::_()->getApi('settings', 'core')->core_facebook_secret;

    			 if (!empty($client_id) && !empty($client_secret) && is_array($this->webmail_show) && in_array("facebook_mail", $this->webmail_show)) {
    			 ?>
    			  <div class="webacc-logos-img">  			
    					<a href='javascript:void(0)' onclick="show_contacts_Facebook(1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/facebook.png', '') ?></a>
    				</div>	
    				 
    			<?php } ?>
    			
    			<?php
	          $twittersettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.twitter');
		        if (!empty($twittersettings['key']) && !empty($twittersettings['secret']) && in_array("twitter_mail", $this->webmail_show) ) {
    			 ?>
    			  <div class="webacc-logos-img">  			
    					<a href='javascript:void(0)' onclick="show_contacts_Twitter(1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/twitter.png', '') ?></a>
    				</div>	
    				 
    			<?php } ?>
    			
    			<!--FINDING FRIENDS FROM USER'S LINKEDIN CONTACTS LIST.-->
				<?php $linkedin_apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.apikey');
							$linkedin_secret = Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.secretkey');
				 if (!empty($linkedin_apikey) && !empty($linkedin_secret) && in_array("linkedin_mail", $this->webmail_show) ) {?>
				 	<div class="webacc-logos-img">
						<a href='javascript:void(0)' onclick="show_contacts_linkedin (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/LinkedIn.png', '', array('align' => 'left')) ?></a>
					</div>	
	      <?php } ?>
	      
	      
    			
    			<!--FINDING FRIENDS FROM USER'S AOL CONTACTS LIST.-->
			<?php if( in_array("aol", $this->webmail_show) ) { ?>
				<div class="webacc-logos-img">
					<a href='javascript:void(0)' onclick="show_contacts_aol (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/aol.png', '') ?></a>
				</div>	
			<?php } ?>

	      
		</div>	
		<div class="sub-txt" id="sub-txt">
			<?php echo $this->translate("Click on one of the above services to choose from your Web Account."); ?>
			<br />
			<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/lock.gif', '', array('style'=>'float:left;margin-right:5px;')) ?>
			<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');?>
			<?php echo $this->translate("will not store your account information."); ?>			
		</div>
	</div>
	
	<div id="id_csvcontacts" style="display:none" class="suggestion_inviter suggestion-signup-step">
		<div class="header">	
			<div class="title">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/webmail.png', '') ?>
				<?php echo $this->translate("Find People You Email"); ?>				
			</div>	
			<div id="inviter_form2" class="suggestion_signup_invite"> <?php echo $this->form->render($this); ?> </div>
			<div class="webmail-options">
				<a href="javascript:void(0);" onclick="showhideinviter('id_csvcontacts', 'id_show_networkcontacts', 4)"><?php echo $this->translate("Use your Web accounts"); ?></a><br>
				<div class="icons">
					<a href="javascript:void(0);" onclick="showhideinviter('id_csvcontacts', 'id_show_networkcontacts', 4)"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/gmail16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhideinviter('id_csvcontacts', 'id_show_networkcontacts', 4)"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/yahoo16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhideinviter('id_csvcontacts', 'id_show_networkcontacts', 4)"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/windowslive16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/facebook16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/twitter16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/linkedin16.png', '') ?></a>
					
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/aol16.png', '') ?></a>
				</div>
				<div class="help-link" style="margin:top 10px;">
					<a href="javascript:void(0);" onclick="showhideinviter('id_csvcontacts', 'id_show_networkcontacts', 2)" ><?php if( empty($isDefaultInviteEnabled) ) { echo $this->translate("or Add Email Addresses Manually"); } ?></a>
				</div>
			</div>
		</div>
		<div class="sub-title" id="sub-title">
			<?php echo $this->translate("Search your contacts in your contact file."); ?>			
		</div>
		<div class="upload-contact-file">
			<div class="op-cat"><?php echo $this->translate("Contact file :"); ?></div>
			<div class="op-field">
				<iframe id='ajaxframe' name='ajaxframe' style='display: none;' src='javascript:void(0);' onchange="myform();"></iframe>
				<form method="post" action="<?php echo Zend_Controller_Front::getInstance()->getBaseUrl() . '/seaocore/usercontacts/uploads'?>" name="csvimport" id="csvimport" enctype="multipart/form-data" target="ajaxframe"> 

					<input name="Filedata"  class="inputbox" type="file"  id="Filedata"  size="23" value="" onchange="savefilepath();"><br />
					<span><?php echo $this->translate("Contact file must be of .csv or .txt format"); ?></span><br />
					<button style="margin-top:10px;" id="csvmasssubmit" name="csvmasssubmit" onClick="getcsvcontacts();return false;"><?php echo $this->translate("Find Friends"); ?></button>
					&nbsp;<?php echo $this->translate('or'); ?>&nbsp;
					<a name="skiplink" id="skiplink" type="button" href="javascript:void(0);" onclick="skipForm(); return false;"><?php echo $this->translate('skip'); ?></a>
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
		<ul class="form-notices" style="float:left;margin:0;"><li style="width:680px;"><?php echo $this->translate("Your invitation(s) will be sent on completion of your signup. If the persons you invited decide to join, they will automatically receive a friend request from you."); ?></li></ul>
	</div>		
	<div class="suggestion_inviter" style="display:none;" id="network_friends">
		<div id="show_contacts"> </div>
	</div>		

	<div id="id_csvformate_error_mess" style="display:none;">
		<ul class="form-errors"><li><ul class="errors"><li><?php echo $this->translate("Invalid file format."); ?></li></ul></li></ul>
  </div>
	<div class="suggestion_inviter" style="display:none;" id="network_friends">
		<div id="show_contacts"> </div>
	</div>		

	
	<div class="suggestion_inviter" style="display:none;" id="csv_friends">
		<div id="show_contacts_csv"> </div>
	</div>
		
		
			<div id="skipinviterlink">
				<br /><br>
				<form id="SignupForm" method="post" enctype="multipart/form-data">
					<input type="hidden"  name="skip" id="skip" value="">    
					<button onclick="skipForm(); return false;" type="button" id="skiplink" name="skiplink"><?php echo $this->translate("Skip This Step"); ?></button>
				</form>
			</div>
		</div>
	</div>		

	<?php
		include_once(APPLICATION_PATH ."/application/modules/Suggestion/views/scripts/_friendInviteContent.tpl");
	?>
	
		
<script type="text/javascript">
	window.addEvent('domready', function () { 
	 <?php if (!empty($client_id)) : ?>
	  fbappid= '<?php echo $client_id;?>';
	    
	  <?php endif;?>
	  
	   if (typeof FB == 'undefined' && typeof fbappid != 'undefined')  { 
    en4.seaocore.facebook.runFacebookSdk ();
  }
  
  
	
	});
	

	
</script>

<?php endif;?>