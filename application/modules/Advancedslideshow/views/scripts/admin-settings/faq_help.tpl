<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
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
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("On which all pages of my site can I show slideshows ?");?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php echo $this->translate("You can show slideshows on all the pages of your site, irrespective of whether a page is widgetized or non-widgetized. To show Slideshows on a page, you must place Slideshow Widget on it.");?>
				<br /><br /><?php echo $this->translate("a) If the page is a Widgetized Page, then from the 'Manage Slideshows' section, create a new slideshow for that page. Widget for this slideshow will get placed automatically. You can later go to the Layout Editor and adjust the vertical position of the Slideshow Widget.");?>
				<br /><br /><?php echo $this->translate("b) If the page is a Non-widgetized Page, then from the 'Manage Slideshows' section, create a new Slideshow for Non-Widgetized Page. Then go to 'Manage Slideshows' page to view the code for this Slideshow. Copy the code of the slideshow and paste it at the desired location on the non-widgetized page to display slideshow over there. Please be sure that you paste the code at the right place. After pasting the code in your template page, if the page layout becomes disorganized, then contact your theme developer to assist you.");?>
			</div>
		</li>	
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Where can I check the various slideshow types to see which one will be best for my site ?");?></a>
			<div class='faq' style='display: none;' id='faq_2'>
				<?php echo $this->translate("You may check out the Demo section in this Admin Panel to see which slideshow type will be best for your site. There is also a Preview button in the ‘Edit Slideshow’ section by clicking on ‘edit’ option against the slideshow using which you can try out various settings to see which setting will be suitable for your site.");?>
			</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("How should I go about creating my slideshow and enabling it on my site ?");?></a>
			<div class='faq' style='display: none;' id='faq_10'>
				<?php echo $this->translate("You may follow the below procedure:");?>
				<br /><?php echo $this->translate("a) Based on the widget position of your slideshow, see from the below question #4 that what dimensions will be suitable for your slideshow.");?>
				<br /><?php echo $this->translate("b) From the Demo section, see that which of the slideshow type do you find suitable for your slideshow.");?>
				<br /><?php echo $this->translate("c) Create your slideshow after clicking on “Create New Slideshow” link in Manage Slideshows section.    ");?>
				<br /><?php echo $this->translate("d) Create the slides for your slideshow using the “Manage” option against your slideshow from the Manage Slideshows section.");?>
				<br /><?php echo $this->translate("e) You may now preview your slideshow by clicking on the Preview button against your slideshow from the Manage Slideshows section and tweak the settings according to your needs.");?>
				<br /><?php echo $this->translate("f) If you have created the slideshow for a widgetized page, then you may now adjust its position vertically from Layout Editor. If you have created the slideshow for a non-widgetized page, then you may now put its code in the appropriate template file.");?>
				<br /><?php echo $this->translate("g) You are done! Let your users enjoy the slideshow.");?>
			</div>
		</li>	

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("What should be the dimensions of my slideshow ?");?></a>
			<div class='faq' style='display: none;' id='faq_3'>
				<?php echo $this->translate("The slideshows can be placed at any widget or block position on your site. Below are the suitable slideshow dimensions for the various widget positions:");?>
				
				<br /><?php echo $this->translate("a) Full width :");?>
				<br />&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->translate("Width : 938 px (maximum)"); ?>
				<br />&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->translate("Height : 275 px (height can be flexible)"); ?>

				<br /><?php echo $this->translate("b) Middle column :");?>
				<br />&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->translate("Width : 517 px (maximum)"); ?>
				<br />&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->translate("Height : 250 px (height can be flexible)"); ?>

				<br /><?php echo $this->translate("c) Left column :");?>    
				<br />&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->translate("Width : 174 px (maximum)"); ?>
				<br />&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->translate("Height : 180 px (height can be flexible)"); ?>

				<br /><?php echo $this->translate("d) Right column :");?>
				<br />&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->translate("Width : 174 px (maximum)"); ?>
				<br />&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->translate("Height : 180 px (height can be flexible)"); ?>

				<br /><?php echo $this->translate("e) Extended Right / Extended Left column :");?>
				<br />&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->translate("Width : 728 px (maximum)"); ?>
				<br />&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->translate("Height : 275 px (height can be flexible)"); ?>
			</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("I have messed up the settings for my slideshow. How can I get them back to original ?");?></a>
			<div class='faq' style='display: none;' id='faq_4'>
				<?php echo $this->translate('You may click on the "Reset to Default" button in the "Edit Slideshow" section by using the edit option against your slideshow from Manage Slideshows section to get the slideshow back to default settings. Please note that the default settings are based on the slideshow widget position.');?>
			</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo $this->translate("How can I rearrange the photos in my Slideshow ?");?></a>
			<div class='faq' style='display: none;' id='faq_11'>
				<?php echo $this->translate('By default, your slideshows always show their most recently uploaded slides first. To change the order of the slides, go to ‘Manage Slides’ section by clicking on manage link against the desired slideshow from the ‘Manage Slideshows’ section. Now, adjust the slides by dragging them and click on “Save Order” button to save the order of your slides.');?>
			</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("I want my slides to be displayed to users of only certain Member Level and / or Network. How can this be done ?");?></a>
			<div class='faq' style='display: none;' id='faq_5'>
				<?php echo $this->translate("Enable the “Member Level Specific Slides” and / or “Network Specific Slides” fields using the ‘Edit’ option against the desired slideshow from 'Manage Slideshows' section. Now click on ‘Manage’ option against the same slideshow to edit the slides and select their respective 'Member Levels' and 'Networks'.") ?>
			</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("I have placed a slideshow widget on a widgetized page from the ‘Layout Editor’ section, but it is not getting displayed on the page. What would be the reason ?");?></a>
			<div class='faq' style='display: none;' id='faq_6'>
				<?php echo $this->translate("Though it is recommended that you do not manually place a slideshow widget on a widgetized page from Layout Editor, if you have done so, then for slideshow to be displayed, you have to first create a slideshow for that page and widget position. Then, you can adjust the slideshow’s position vertically by dragging its widget in Layout Editor.");?>
			</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("I want to temporarily disable a slideshow / slide on a page. How can this be done ?");?></a>
			<div class='faq' style='display: none;' id='faq_7'>
				<?php echo $this->translate("You can disable a slideshow as well as a slide by disabling its status using “Status” column in the list of slideshows and slides. You can also remove the respective slideshow widget from 'Layout Editor' section. But the initial method is more preferable.");?>
			</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_8'>
				<?php echo $this->translate("Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
			</div>
		</li>
  </ul>
</div>