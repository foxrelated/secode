<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  function faq_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
</script>

<div class="admin_seaocore_files_wrapper">
	<ul class="admin_seaocore_files seaocore_faq">	
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("I do not want to enable the settings of this plugin (rich emails) for some messages. What should I do?"); ?></a>
      <div class='faq' style='display: none;' id='faq_1'>
        <?php echo $this->translate("Ans: You can disable the settings of this plugin for particular messages by un-selecting the field: “Activate Above Email Template for this Message” for the message from ‘Mail Templates’ section of this plugin."); ?>
      </div>
    </li>

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("I have plugin(s) from SocialEngineAddOns in which I had configured email template settings. However, I am not able to view theme settings for the email templates from these plugins. What might be the reason?"); ?></a>
      <div class='faq' style='display: none;' id='faq_2'>
        <?php echo $this->translate("Ans: In our plugins which had email template settings, like: “<a href='http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-contact-page-owners-extension'>Directory / Pages - Contact Page Owners Extension</a>”, “<a href='http://www.socialengineaddons.com/businessextensions/socialengine-directory-businesses-contact-business-owners-extension'>Directory / Businesses - Contact Business Owners Extension</a>” and “<a href='http://www.socialengineaddons.com/socialengine-birthdays-plugin-listing-wishes-reminder-emails-widgets '>Birthdays Plugin-Listing, Wishes, Reminder Emails & Widgets</a>”, you would not be seeing these settings any more. You are not able to see the theme settings for the email templates from these plugins because we have moved these settings to this “Email Templates Plugin”. To configure the respective settings for these plugins, go to the “Global Settings” of this plugin."); ?>
      </div>
    </li>
	</ul>
</div>