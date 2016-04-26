<?php

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
      <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("I have a 3rd party content module installed on my website. How can I integrate it with this plugin such that members can check-into its content ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_1'>
        <?php echo $this->translate("Ans: You can configure such an integration for any 3rd party content plugin from the Manage Modules section of this plugin."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("I have added a 3rd party content module, but I am not able to add locations to the photos belonging to it. What should I do?"); ?></a>
      <div class='faq' style='display: none;' id='faq_2'>
        <?php echo $this->translate('Ans: If you have added any 3rd party content module, then you can place "Photos: Auto-suggest Add Location" widget on the view page of the photos belonging to this content from "Layout Editor".  If the view page of such photos is not widgetized, then you can add locations by opening these photos in the lightbox by using our "<a href="http://www.socialengineaddons.com/socialengine-advanced-photo-albums-plugin" target="_blank">Advanced Photo Albums Plugin</a>". You may purchase this plugin <a href="http://www.socialengineaddons.com/socialengine-advanced-photo-albums-plugin" target="_blank">over here</a>.'); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate('When I am adding locations to the photos belonging to any 3rd party content module, the location markers for them are not shown in the map on my profile. Why is this happening?'); ?></a>
      <div class='faq' style='display: none;' id='faq_3'>
        <?php echo $this->translate("Ans: To show location markers for the locations associated with the photos belonging to any 3rd party content modules in the map on your profile, you have to manually do the below mentioned changes:"); ?>
				<p> 
					<?php echo $this->translate("a) Open the file: 'application/modules/Sitetagcheckin/Api/Core.php'."); ?><br />
					<?php echo $this->translate("b) Go to the line around line number 722 containing: <b style='font-weight:bold;'>return array('album', 'album_photo', 'group_photo', 'event_photo')'</b> ."); ?><br />
					<?php echo $this->translate("c) Add an entry in this line, separated by comma (,), for the desired module / plugin, similar to the existing entry for Event: <b style='font-weight:bold;'>'event_photo'</b> ."); ?><br />
				</p>	
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("How can I check-in via the status update box, add smileys, tag friends and add privacy to my status updates while checking into various contents?"); ?></a>
      <div class='faq' style='display: none;' id='faq_4'>
        <?php echo $this->translate('Ans:You can check-in via the status update box, add smileys, tag friends and add privacy to your status updates while checking into various contents by using our “<a href="http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin" target="_blank">Advanced Activity Feeds / Wall Plugin</a>”. You may purchase this plugin <a href="http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin" target="_blank">over here</a>.'); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_5'>
        <?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("After installing this plugin, users on my site can now enter locations from their ‘Edit My Profile’ page and ‘Edit My Location’ page. Which location will be associated with users’ Profiles to be searched in the ‘Members Location & Proximity Based Search’?"); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
        <?php echo $this->translate("The location entered from the ‘Edit My Location’ page will always be associated with the profiles of members on your site whereas the location entered from the ‘Edit My Profile’ page will be associated, if they have entered their location in the “Location” type field mapped by you, from the ‘Profile Type - Location Field Mapping’ field in the ‘Global Settings’ section of this plugin. Both these locations will be synced with each other.<br />If you change a mapping, then the new location entered in the newly mapped “Location” type field will be synced when a user edits their location from any of the above mentioned 2 pages."); ?>
      </div>
    </li>
	</ul>
</div>
