<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
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
<?php
//   $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
//   $description = sprintf(Zend_Registry::get('Zend_Translate')->_('We have moved these "Advanced Lightbox Display Settings" in "SocialEngineAddOns Core Plugin". You can change the desired settings by visiting "%1sPhotos Lightbox Viewer%2s" section in "SocialEngineAddOns Core Plugin".'),
//   "<a href='" . $view->baseUrl() . "/admin/socialengineaddon/settings/lightbox"."' target='_blank'>", "</a>");
?>
<div class="admin_sitegroup_files_wrapper">
  <ul class="admin_sitegroup_files sitegroup_faq">
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("I want Albums to be available to only certain groups on my site. How can this be done?"); ?></a>
      <div class='faq' style='display: none;' id='faq_1'>
        <?php echo $this->translate("Ans: You can enable packages for groups on your site, and make Photos available to only certain packages. If you have not enabled packages, then from Member Level Settings, you can make Photos to be available for groups of only certain member levels."); ?>
      </div>
    </li>	
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Is it possible for a member (other than Group owner or Group admin) to upload photo to a Group album without being the owner of the Group?"); ?></a>
      <div class='faq' style='display: none;' id='faq_2'>
        <?php echo $this->translate('Ans: Yes, it is possible but such a member can upload photos only to the Default Group Album which was created during the Group creation. Also while creating a Group, at "Photo Creation Privacy" field, Group owner can decide that who can upload photos to the default group album. Group owner/admin can also edit this privacy setting by editing the Group details after its creation.<br />Default privacy is set to "All Registered Members" which means that all the registered members of the site can upload photos to the Group\'s default album.'); ?>
      </div>
    </li>	
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("What kinds of displays are available for Group Albums and Photos?"); ?></a>
      <div class='faq' style='display: none;' id='faq_3'>
        <?php echo $this->translate("Ans: There are 2 types of Album displays available. Both of them are AJAX based displays. The first is a simple AJAX based display, and the second one is an advanced lightbox display. The Advanced Lightbox Display for group album photos can be enabled from General Settings of Group Albums Extension."); ?>
      </div>
    </li>	
    <li style='display: none;'>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate('I have enabled the "Advanced Lightbox Display" from Global Settings. But the group album photos in the activity feed on the Group Profile are still not being displayed in Advanced Lightbox. What can be the solution ?'); ?></a>
      <div class='faq' style='display: none;' id='faq_4'>
      	<?php //echo $description;?>
        </div>
    </li>	
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("I had not selected to 'Enable Albums Module for Default Package' in 'General Settings' section before activating this Plugin. But after activation, I am not able to view this option. Does this mean that this module is permanently disabled for Default Package?"); ?></a>
      <div class='faq' style='display: none;' id='faq_5'>
        <?php echo $this->translate("Ans: No, it does not mean so. You can still enable this module for Default Package from the 'Manage Packages' section of 'Groups Plugin' by editing the Default Package and selecting the 'Photos' checkbox in the 'Modules / Apps' field."); ?>
      </div>
    </li>	
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("I want to use this plugin for directory of car albums. How can I change the word: 'group albums' to 'car albums' in this plugin?"); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
        <?php echo $this->translate("Ans: You can easily use this plugin for creation of any type of directories. You can change the word 'group albums' to your directory type from the 'Layout' > 'Language Manager' section in the Admin Panel."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_7'>
        <?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homegroup and then check the group which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?>
      </div>
    </li>	
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("I am not able to find the Suggest to Friends feature for Group Albums. What can be the reason?"); ?></a>
      <div class='faq' style='display: none;' id='faq_8'>
        <?php echo $this->translate('Ans: The suggestions features are dependent on the %1$sSuggestions / Recommendations / People you may know & Inviter%2$s plugin and require that to be installed.', '<a href="http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin" target="_blank">', '</a>'); ?>
      </div>
    </li>	
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("I want to enhance the Groups on my site and provide more features to my users. How can I do it?"); ?></a>
      <div class='faq' style='display: none;' id='faq_9'>
        <?php echo $this->translate('Ans: There are various apps / extensions available for the "Groups Plugin" which can enhance the Groups on your site, by adding valuable functionalities to them. To view the list of available extensions, please visit: %s.', '<a href="http://www.socialengineaddons.com/catalog/directory-groups-extensions" target="_blank">http://www.socialengineaddons.com/catalog/directory-groups-extensions</a>'); ?>
      </div>
    </li>	
  </ul>
</div>