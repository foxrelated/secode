<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventinvite
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
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
      <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("I am getting the error : 'failed to open stream: HTTP request failed!' while trying to upload csv/text file content; what should I do ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_1'>
        <?php $link_phpinfo = "<a href='" . $this->baseUrl() . "/admin/system/php' 	style='color:#5BA1CD;'>http://" . $_SERVER['HTTP_HOST'] . $this->baseUrl() . "/admin/system/php</a>";
        echo $this->translate("Ans: You should ask your server administrator to check your server's php.ini PHP configuration file for 'allow_url_fopen' to be 'on' and 'user_agent' to have some values listed in it. It should be listed here: %s. This should resolve the issue.", $link_phpinfo); ?>
      </div>
    </li>	

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Windows Live contact importer is not working on my site, what should I do ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_2'>
        <?php $link_phpinfo = "<a href='" . $this->baseUrl() . "/admin/system/php' 	style='color:#5BA1CD;'>http://" . $_SERVER['HTTP_HOST'] . $this->baseUrl() . "/admin/system/php</a>";
        echo $this->translate("Ans: If, Windows Live contact importer is not working on your site then there may be chance that PHP Mhash package is not installed on your server. Please ask your server administrator to install this. After installing mhash, it should also be listed here: %s. This should resolve the issue.", $link_phpinfo); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate('Is "Invite Guests" feature dependent on packages / member levels?'); ?></a>
      <div class='faq' style='display: none;' id='faq_3'>
        <?php echo $this->translate("Ans: Yes, if packages are enabled on site for events, you (admin) can choose if this app extension should be available to events of a package. If packages are disabled, then access to this feature can be based on Member Level via the Member Level Settings."); ?></a>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate('Who can use the "Invite and Promote" feature to invite their contacts to a Event?'); ?></a>
      <div class='faq' style='display: none;' id='faq_4'>
        <?php echo $this->translate('Ans: Event Admins have access to the "Invite and Promote" feature from the "Marketing" and "Apps" sections of their Event Dashboard.'); ?></a>
      </div>
    </li>
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("Would the invitees who are already members of this site also get event invitations from their friends if sent to them?"); ?></a>
      <div class='faq' style='display: none;' id='faq_5'>
        <?php echo $this->translate('Ans: Yes, the invitees who are already members of this site would get a suggestion for the Event. This feature is dependent on the %1$sSuggestions / Recommendations / People you may know & Inviter%2$s plugin and require that to be installed. In case, this plugin is not installed, a suggestion notification will be sent.<br />Additionally, in both cases an email will also be sent to the invitees who are not the members of the site. Users will also be able to preview these suggestions and email templates before sending the invitations to their friends.', '<a href="http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin" target="_blank">', '</a>'); ?></a>
      </div>
    </li>
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("I had not selected to 'Enable Inviter Module for Default Package' in 'Global Settings' section before activating this Plugin. But after activation, I am not able to view this option. Does this mean that this module is permanently disabled for Default Package?"); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
        <?php echo $this->translate("Ans: No, it does not mean so. You can still enable this module for Default Package from the 'Manage Packages' section of 'Events Plugin' by editing the Default Package and selecting the 'Invite Guests' checkbox in the 'Modules / Apps' field."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("I want to use this plugin for directory of car invitations. How can I change the word: 'event invitations' to 'car invitations' in this plugin?"); ?></a>
      <div class='faq' style='display: none;' id='faq_7'>
        <?php echo $this->translate("Ans: You can easily use this plugin for creation of any type of directories. You can change the word 'event invitations' to your directory type from the 'Layout' > 'Language Manager' section in the Admin Panel."); ?><br />
        <?php echo $this->translate("If you also want to change the URLs of this plugin, then follow the below steps:") ?><br /><br />
        <?php echo $this->translate('1) Open this file: "application/modules/Siteeventinvite/settings/manifest.php".') ?><br /><br />
        <?php echo $this->translate("2) Change the word 'eventinvites' to your directory type in the route part of all the routes defined in this file. For example, if you want to change 'eventinvites' to 'carinvites', the change should be like:") ?><br /><br />
        <div class="code">
          <?php echo "'route' => 'eventinvites/invitefriends/:user_id/:siteevent_id/'" ?>
        </div><br />
        <?php echo $this->translate("The above code should be replaced with the below code.") ?><br /><br />
        <div class="code">
          <?php echo "'route' => 'carinvites/invitefriends/:user_id/:siteevent_id/'" ?>
        </div><br />
        <?php echo $this->translate("Please note that in the above example, the change is 'eventinvites' => 'carinvites'. But you can also change it to anything else according to your specifications. This type of change should be made in all the routes of this file.") ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_8'>
        <?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homeevent and then check the event which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?></a>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("I want to enhance the Events on my site and provide more features to my users. How can I do it?"); ?></a>
      <div class='faq' style='display: none;' id='faq_9'>
        <?php echo $this->translate('Ans: There are various apps / extensions available for the "Events Plugin" which can enhance the Events on your site, by adding valuable functionalities to them. To view the list of available extensions, please visit: %s.', '<a href="http://www.socialengineaddons.com/catalog/directory-events-extensions" target="_blank">http://www.socialengineaddons.com/catalog/directory-events-extensions</a>'); ?></a>
      </div>
    </li>
  </ul>
</div>