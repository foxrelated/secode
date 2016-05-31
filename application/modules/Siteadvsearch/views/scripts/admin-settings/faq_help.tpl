<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
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
     <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo "Can I know on what basis is searching results displayed in this plugin?";?></a>
     <div class='faq' style='display: none;' id='faq_1'>
      <?php echo "Search Results are shown on the basis of Content Title, Content Description & Keywords (Tags). If the search item is found in any of these then that content is shown as the searched result.";?>
     </div>
    </li>
     <li>
     <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo "I want to place Advanced Search Box On Members Home Page. How can I do so?";?></a>
     <div class='faq' style='display: none;' id='faq_2'>
      <?php echo "Please use our Advanced Search Box widget and place it wherever you want. You will be able to search any content of your site, same as you do on the Advanced Search Page.";?>
     </div>
    </li>
    <li>
     <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo "I want Advanced Search box to be enable in my Mini Menu. How can I do so?";?></a>
     <div class='faq' style='display: none;' id='faq_3'>
      <?php echo "You can place our Advanced Search - Mini Menu widget in place of Mini Menu widget. Also if you are having our <a href='http://www.socialengineaddons.com/socialengine-advanced-menus-plugin-interactive-attractive-navigation'>Advanced Menus Plugin - Interactive and Attractive Navigation</a>” then you will be able to enable Advanced Search box from the Advanced Menus - Mini Menu widget.";?>
     </div>
    </li>
    <li>
     <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo "I am confused which of my plugins are visible / invisible on Advanced Search Page. How can I manage these? ";?></a>
     <div class='faq' style='display: none;' id='faq_4'>
      <?php echo "Please go to “Manage Modules” section available in the admin panel of this plugin. Here you can manage all your Content Type (Plugins) and can enable / disable them to show in Search Tabs & Auto Suggest. ";?>
     </div>
    </li>
    <li>
     <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo "I have installed 3rd party plugin on my site, but I am unable to see it in the Advanced Search Tab. Why is it so?";?></a>
     <div class='faq' style='display: none;' id='faq_5'>
      <?php echo "You have not added that plugin in the content types from the admin panel. Please go to the ‘Manage Modules’ section available in the admin panel of this plugin, now click on the ‘Add a Content Type’ link. You will be redirected to the Add a New Content Type Page, use available dropdown to select the plugin you want to add in the Advanced Search Bar. you will be able to add your Content Module successfully.";?>
     </div>
    </li>
    <li>
     <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo "I have enables content types from Manage Modules tab in admin panel but still those content types are not visible in auto suggest. What might be the reason?";?></a>
     <div class='faq' style='display: none;' id='faq_6'>
      <?php echo "It is happening because you have not enabled those content types to “Add Link in Search Box”. Please enable it from the Manage Modules tab available in the admin panel of this plugin.";?>
     </div>
    </li>
    <li>
     <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo "I do not want my search results and auto suggests to display the results corresponding to all the Content Modules on my site instead,  I want it to be displayed corresponding to the specific modules ie. Events, Groups & Videos only. Is it possible with this plugin?";?></a>
     <div class='faq' style='display: none;' id='faq_7'>
      <?php echo "Yes, please follow the below guidelines to do so:<br />
1. Go to the ‘Global Settings’ section available in the admin panel of this plugin.<br />
2. Now select ‘Specific Module’ option for ‘Search Result’ setting.<br />
3. Go to the ‘Manage Modules’ section of this plugin and enable / disable content modules from ‘Show in search results’ column.<br /><br /> 

You can now see the search results to be displayed corresponding to the specific modules.
";?>
     </div>
    </li>
    <li>
     <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo "I want my users to see only 5 content in the auto suggest. Is it possible with this plugin?";?></a>
     <div class='faq' style='display: none;' id='faq_8'>
      <?php echo "Yes you can do so by entering the content limit for ‘Maximum results Limit’ setting available in the Global Settings section of its admin panel.";?>
     </div>
    </li>
     <li>
     <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo "I want search results to be displayed in the sequence of Events >> Groups >> Videos >> Music, etc. Can I customize this according to my will?";?></a>
     <div class='faq' style='display: none;' id='faq_9'>
      <?php echo "Yes to do so please go to the ‘Manage Modules’ section available in the admin panel of this plugin. Now, Drag and Drop the content types to reorder their sequence by assigning higher position to the content types which you want to appear in the search result first.";?>
     </div>
    </li>
    <li>
     <a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo "I want to change title of the Content Modules’ names shown in Search Tabs and on All Result Page. Is it possible to do so?";?></a>
     <div class='faq' style='display: none;' id='faq_10'>
      <?php echo "Yes please go to the ‘Manage Modules’ section available in the admin panel of this plugin. Now use ‘Edit’ link under the ‘Option’ column to edit the name of the desired module. Save your changes, you will be able to see this new name in Search Tabs and on All Result Page. [Note: You will be able to see the changes done by you only if you have enabled that content module for ‘Create Tab on Search Page’.]";?>
     </div>
    </li>
     <li>
     <a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo "I want to change text visible as placeholder in Advanced Search box from “Search For...” to “Search for Events, Groups & Videos”. How can I do so?";?></a>
     <div class='faq' style='display: none;' id='faq_11'>
      <?php echo "To do so please go to the ‘Layout Manager’ and click on edit phrases link, now search ‘SEARCH_BOX_MESSAGE’ from the given search box ”. Change the “Search for..” placeholder with phrase you desire. You can now see the phrase you have added in the Advanced Search Box.<br /><br /> 

If you are still unable to view the desired changes, please clear cache from Admin Panel >> ‘Settings’ >> ‘Performance and Cache’, and use flush cache option.";?>
     </div>
    </li>
    <li>
     <a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo "I want to add icon for all the Content Types. Is it possible?";?></a>
     <div class='faq' style='display: none;' id='faq_12'>
      <?php echo "Yes you can do so by enabling all the Content Types to “Show in auto suggest”. Now go to the ‘Manage Content Icons’ tab, you will find all the content types listed here. Please add desired icon using ‘Add Icon’ link available against each Content Type.";?>
     </div>
    </li>		
     <li>
     <a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo "My members tab do not seems to look good . How can I enhance it? What should I do?";?></a>
     <div class='faq' style='display: none;' id='faq_13'>
      <?php echo "You can purchase our <a href='http://www.socialengineaddons.com/socialengine-advanced-members-plugin-better-browse-search-user-reviews-ratings-location'>Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location</a> to make it look like our demo. By this plugin you can showcase your members in an attractive manner having better browsing and searching options with an awesome brand new look of members, which will enhance SocialEngine's core members plugin in all ways.";?>
     </div>
    </li>		
     <li>
     <a href="javascript:void(0);" onClick="faq_show('faq_14');"><?php echo " I want to customize search pages of Pages, Events, Groups, etc according to my will. How can I do so?";?></a>
     <div class='faq' style='display: none;' id='faq_14'>
      <?php echo "You can do it easily by going to Layout Editor in the admin panel and following the below instructions:<br />
1. Go to the Layout Editor.<br />
2. Now open the editing dropdown available in the left column, Now search for widgetized pages having.<br />
3. All the widgetized pages have been named with the “Plugin Name - SEAO / SE - Module Name”.<br />
4. Now customize your search pages by placing desired widgets here.";?>
     </div>
    </li>		
  </ul>
</div>