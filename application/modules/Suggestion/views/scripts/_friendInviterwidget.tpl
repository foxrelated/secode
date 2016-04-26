<?php 
	$getSignupAction = Engine_Api::_()->suggestion()->getAction();
	$webmail_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggestion.show.webmail');
	$this->webmail_show = unserialize($webmail_show);
?>
<div class="adv_activity_welcome">
	<div class="adv_activity_welcome_num">
	</div>
	<div class="adv_activity_welcome_cont">
		<div class="adv_activity_welcome_cont_title">
			<?php echo $this->translate("Add Your Contacts as Friends Here"); ?>
		</div>
		<div class="adv_activity_welcome_cont_des">	
	
	
	<?php if (($this->user_id)) { ?>
	<div id="id_show_networkcontacts" style="display:block"  class="suggestion_inviter">
		<div class="aac_welcome_webmail_options">
			<div class="help-link">
				<a href="javascript:void(0);" onclick="showhide('id_show_networkcontacts', 'id_csvcontacts')"><?php echo $this->translate("Upload Contact File"); ?></a><br />
				<div class="icons">
					<a href="javascript:void(0);" onclick="showhide('id_show_networkcontacts', 'id_csvcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/addressbook.png', '',array('title' => 'Mac Address Book')) ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_show_networkcontacts', 'id_csvcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/linkedin16.png', '',array('title' => 'LinkedIn Address Book')) ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_show_networkcontacts', 'id_csvcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/thunderbird.png', '',array('title' => 'Thunderbird Address Book')) ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_show_networkcontacts', 'id_csvcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/outlook.png', '',array('title' => 'Microsoft Outlook Address Book')) ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_show_networkcontacts', 'id_csvcontacts')"><?php echo $this->translate("more"); ?> <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/plus.png', '') ?></a>
				</div>
			</div>
			<div class="help-link">
				<a href="<?php echo Zend_Controller_Front::getInstance()->getBaseUrl() . '/invite';?>" ><?php echo $this->translate("or Add Email Addresses Manually"); ?></a>
			</div>
		</div>
		<div class="aac_welcome_invite_cont">
			<div class="sub-title">
				<?php echo $this->translate("Choose from your Web Accounts."); ?>			
			</div>
			<div class="aaf-webacc-logos">
				 <!--FINDING FRIENDS FROM USER'S GOOGLE CONTACTS LIST.-->
				<?php if( in_array("gmail", $this->webmail_show) ) { ?>
					<div class="aaf-webacc-logos-img">
						<a href='javascript:void(0)' onclick="show_contacts_google (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/gmail.png', '', array('align' => 'left')) ?></a>
					</div>	
				<?php } ?>
				
				<!--FINDING FRIENDS FROM USER'S YAHOO CONTACTS LIST.-->
				<?php $yahoo_apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('yahoo.apikey');
							$yahoo_secret = Engine_Api::_()->getApi('settings', 'core')->getSetting('yahoo.secretkey');
				 if (!empty($yahoo_apikey) && !empty($yahoo_secret) && in_array("yahoo", $this->webmail_show) ) {?>
				 	<div class="aaf-webacc-logos-img">
						<a href='javascript:void(0)' onclick="show_contacts_yahoo (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/yahoomail.png', '', array('align' => 'left')) ?></a>
					</div>	
	      <?php } ?>
	
				<!--FINDING FRIENDS FROM USER'S WINDOW LIVE CONTACTS LIST.-->
				<?php $windowlive_apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('windowlive.apikey');
							$windowlive_secret = Engine_Api::_()->getApi('settings', 'core')->getSetting('windowlive.secretkey');
				 if (!empty($windowlive_apikey) && !empty($windowlive_secret) && in_array("window_mail", $this->webmail_show) ) {?>
				 	<div class="aaf-webacc-logos-img">
						<a href='javascript:void(0)' onclick="show_contacts_windowlive (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/windowslive.png', '', array('align' => 'left')) ?></a>
					</div>	
				<?php } ?>
				
	
				<?php
			        $client_id = Engine_Api::_()->getApi('settings', 'core')->core_facebook_appid;
			        $client_secret = Engine_Api::_()->getApi('settings', 'core')->core_facebook_secret;
	
	    			 if (!empty($client_id) && !empty($client_secret) && in_array("facebook_mail", $this->webmail_show)) {
	    			 ?>
	    			 	<div class="aaf-webacc-logos-img">   			
	    					<a href='javascript:void(0)' onclick="show_contacts_Facebook(1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/facebook.png', '', array('align' => 'left')) ?></a>
	    				</div>	
	    				 
	    			<?php } ?>
	    			
	    			<?php
	          $twittersettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.twitter');
		        if (!empty($twittersettings['key']) && !empty($twittersettings['secret']) && in_array("twitter_mail", $this->webmail_show) ) {
    			 ?>
    			  <div class="aaf-webacc-logos-img">  			
    					<a href='javascript:void(0)' onclick="show_contacts_Twitter(1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/twitter.png', '') ?></a>
    				</div>	
    				 
    			<?php } ?>
	    			
	    			
	    			<!--FINDING FRIENDS FROM USER'S LINKEDIN CONTACTS LIST.-->
				<?php $linkedin_apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.apikey');
							$linkedin_secret = Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.secretkey');
				 if (!empty($linkedin_apikey) && !empty($linkedin_secret) && in_array("linkedin_mail", $this->webmail_show) ) {?>
				 	<div class="aaf-webacc-logos-img">
						<a href='javascript:void(0)' onclick="show_contacts_linkedin (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/LinkedIn.png', '', array('align' => 'left')) ?></a>
					</div>	
	      <?php } ?>
	      
	     
    			<!--FINDING FRIENDS FROM USER'S AOL CONTACTS LIST.-->
			<?php if( in_array("aol", $this->webmail_show) ) { ?>
				<div class="aaf-webacc-logos-img">
					<a href='javascript:void(0)' onclick="show_contacts_aol (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/aol.png', '') ?></a>
				</div>	
			<?php } ?>

	
	
			</div>	
		</div>	
		<div class="sub-txt">
			<?php echo $this->translate("Click on one of the above services to choose from your Web Account."); ?>
			<br />
			<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/lock.gif', '', array('style'=>'float:left;margin-right:5px;')) ?>
			<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');?>
			<?php echo $this->translate("will not store your account information."); ?>			
		</div>
	</div>
	
	<div id="id_csvcontacts" style="display:none" class="suggestion_inviter">
		<div class="aac_welcome_webmail_options">
			<div class="help-link fleft">
				<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->translate("Use your web accounts"); ?></a><br />
				<div class="icons">
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/gmail16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/yahoo16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/windowslive16.png', '') ?></a>
				<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/facebook16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/twitter16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/linkedin16.png', '') ?></a>
					
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/aol16.png', '') ?></a>
				</div>
			</div>	
			<div class="help-link fright">
				<a href="<?php echo Zend_Controller_Front::getInstance()->getBaseUrl() . '/invite';?>" ><?php echo $this->translate("or Add Email Addresses Manually"); ?></a>
			</div>
			<div class="help-link help-link-right">
	     	<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/support.png', '', array('class'=>'fleft')) ?>
				<a href="javascript:void(0);" onclick="show_services();"><?php echo $this->translate("Supported Services"); ?></a><br />
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/help.png', '', array('class'=>'fleft')) ?>
				<a href="javascript:void(0);" onclick="show_createfile();"><?php echo $this->translate("How to create a contact file"); ?></a>
			</div>
		</div>
		<div class="aac_welcome_invite_cont">
			<div class="sub-title">
				<?php echo $this->translate("Search your contacts in your contact file."); ?>			
			</div>
			<div class="upload-contact-file">
				<div class="op-cat"><?php echo $this->translate("Contact file :"); ?></div>
				<div class="op-field clr">
					<iframe id='ajaxframe' name='ajaxframe' style='display: none;' src='javascript:void(0);' onchange="myform();"></iframe>
					<form method="post" action="<?php echo Zend_Controller_Front::getInstance()->getBaseUrl() . '/seaocore/usercontacts/uploads'?>" name="csvimport" id="csvimport" enctype="multipart/form-data" target="ajaxframe">
	
						<input name="Filedata"  class="inputbox" type="file"  id="Filedata"  size="18" value="" onchange="savefilepath();" /><br />
						<input type="hidden"  value="" id="file_upload" />
						<span><?php echo $this->translate("Contact file must be of .csv or .txt format"); ?></span><br />
						<ul class="form-notices" id="uccess_fileupload_parent_sugg" style="float:left;margin:0; display:none;"><li  id="success_fileupload"></li></ul>
						<button style="margin-top:10px;" id="csvmasssubmit" name="csvmasssubmit" onClick="getcsvcontacts();return false;"><?php echo $this->translate("Find Friends"); ?></button>
					</form>
				</div>	
			</div>
		</div>	
	</div>
<?php } ?>

   <div id="id_success_frequ" style="display:none;">
		<ul class="form-notices" style="float:left;margin:0;"><li  style="width:350px;"><?php echo $this->translate("Your friend request(s) have been successfully sent!"); ?></li></ul>
  </div>
	<div id="id_nonsite_success_mess" style="display:none;">
		<ul class="form-notices" style="float:left;margin:0;"><li style="width:680px;"><?php echo $this->translate("Your invitation(s) were sent successfully. If the persons you invited decide to join, they will automatically receive a friend request from you."); ?></li></ul>
	</div>		

	<div class="suggestion_inviter" style="display:none;" id="network_friends">
		<div id="show_contacts"> </div>
	</div>		

	<div id="id_csvformate_error_mess" style="display:none;">
		<ul class="form-errors clr"><li><ul class="errors"><li><?php echo $this->translate("Invalid file format."); ?></li></ul></li></ul>
  </div>

	<div class="suggestion_inviter" style="display:none;" id="network_friends">
		<div id="show_contacts"> </div>
	</div>		

	
	<div class="suggestion_inviter" style="display:none;" id="csv_friends">
		<div id="show_contacts_csv"> </div>
	</div>
	
	</div>
</div>
</div>
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