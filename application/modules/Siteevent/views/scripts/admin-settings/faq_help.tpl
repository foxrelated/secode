<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
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

<?php if(!empty($this->faq)) : ?>
	<p><?php echo $this->translate("Browse the different FAQ sections of this plugin by clicking on the corresponding tabs below.") ?><p>
	<br />
	<?php $action = 'faq' ?>
<?php else : ?>
	<?php $action = 'readme' ?>
<?php endif; ?>

<div class='tabs'>
	<ul class="navigation">
		<li class="<?php if($this->faq_type == 'general') { echo "active"; } ?>">
			<?php echo $this->htmlLink(array('route'=>'admin_default','module' => 'siteevent','controller' => 'settings','action' => $action, 'faq_type' => 'general'), $this->translate('General'), array())
		?>
		</li>
			
		<li class="<?php if($this->faq_type == 'eventfeatures') { echo "active"; } ?>">
			<?php echo $this->htmlLink(array('route'=>'admin_default','module' => 'siteevent','controller' => 'settings','action' => $action, 'faq_type' => 'eventfeatures'), $this->translate('Event Features'), array())
		?>
		</li>	
    <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat')): ?>
    <li class="<?php if($this->faq_type == 'repeatingevents') { echo "active"; } ?>">
			<?php echo $this->htmlLink(array('route'=>'admin_default','module' => 'siteevent','controller' => 'settings','action' => $action, 'faq_type' => 'repeatingevents'), $this->translate('Repeating Event'), array())
		?>
		</li>
    <?php endif;?>
        <li class="<?php if($this->faq_type == 'host') { echo "active"; } ?>">
			<?php echo $this->htmlLink(array('route'=>'admin_default','module' => 'siteevent','controller' => 'settings','action' => $action, 'faq_type' => 'host'), $this->translate('Host'), array())
		?>
		</li>
		<li class="<?php if($this->faq_type == 'editors') { echo "active"; } ?>">
			<?php echo $this->htmlLink(array('route'=>'admin_default','module' => 'siteevent','controller' => 'settings','action' => $action, 'faq_type' => 'editors'), $this->translate('Editors'), array())
		?>
		</li>

        
        <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')): ?>
          <li class="<?php if($this->faq_type == 'document') { echo "active"; } ?>">
                <?php echo $this->htmlLink(array('route'=>'admin_default','module' => 'siteevent','controller' => 'settings','action' => $action, 'faq_type' => 'document'), $this->translate('Documents'), array())
            ?>
            </li>
        <?php endif; ?>
        		

  <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventadmincontact')): ?>
           <li class="<?php if($this->faq_type == 'admincontact') { echo "active"; } ?>">
                <?php echo $this->htmlLink(array('route'=>'admin_default','module' => 'siteevent','controller' => 'settings','action' => $action, 'faq_type' => 'admincontact'), $this->translate('Contact Event Owners'), array())
            ?>
            </li>	
        <?php endif; ?>
     <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite')): ?>
           <li class="<?php if($this->faq_type == 'inviter') { echo "active"; } ?>">
                <?php echo $this->htmlLink(array('route'=>'admin_default','module' => 'siteevent','controller' => 'settings','action' => $action, 'faq_type' => 'inviter'), $this->translate('Inviter'), array())
            ?>
            </li>	
        <?php endif; ?> 
             <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventemail')): ?>
           <li class="<?php if($this->faq_type == 'email') { echo "active"; } ?>">
                <?php echo $this->htmlLink(array('route'=>'admin_default','module' => 'siteevent','controller' => 'settings','action' => $action, 'faq_type' => 'email'), $this->translate('Event Reminders'), array())
            ?>
            </li>	
        <?php endif; ?>  
            
        <?php if (Engine_Api::_()->hasModuleBootstrap('siteeventticket')): ?>
           <li class="<?php if($this->faq_type == 'tickets') { echo "active"; } ?>">
                <?php echo $this->htmlLink(array('route'=>'admin_default','module' => 'siteevent','controller' => 'settings','action' => $action, 'faq_type' => 'tickets'), $this->translate('Tickets'), array())
            ?>
           </li>	
        <?php endif; ?>              
            
	</ul>
</div>

<?php switch($this->faq_type) : ?>
<?php case 'general': ?>
  <div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files seaocore_faq">	
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("How should I start with creating events on my site?");?></a>
				<div class='faq' style='display: none;' id='faq_1'>
					<?php echo $this->translate("After plugin installation, follow the steps below:");?>
					<ul>
						  <li>
							  <?php echo $this->translate("Start by configuring the Global Settings for your plugin.");?>
							</li>
								<li>
									<?php echo $this->translate("Then, configure Member Level Settings for events.");?>
								</li>
								<li>
									<?php echo $this->translate("Then create the categories, sub-categories and 3rd level categories.");?>
								</li>
								<li>
									<?php echo $this->translate("Then go to the Profile Fields section to create custom fields if required for any events on your site and configure mapping between event categories and profile types such that custom profile fields can be based on categories, sub-categories and 3rd level categories for your events.");?>
								</li>
								<li>
									<?php echo $this->translate("Configure the reviews and ratings settings for your site from the Reviews & Ratings section.");?>
									<br />
									<?php echo $this->translate('1) Go to the Review Settings sub-section and configure the settings here.');?>
									<br />
									<?php echo $this->translate('2) Then go to the Review Profile Fields sub-section to create custom review profile fields if required and configure the mapping between event categories and review profile types such that custom profile fields can be based on categories, sub-categories and 3rd level categories for your event.');?>
									<br />
									<?php echo $this->translate('3) Now, go to the Rating Parameters sub-section and create rating parameters for different categories.');?>
									<br />
								</li>
                 <li>
									<?php echo $this->translate('Choose Editors and Super Editor from the Manage Editors section and add Editor badges to assign them to the Editors.');?>
                </li>
                <li>
									<?php echo $this->translate('Then, go to the Video Settings section to configure various settings for videos.');?>
                </li>
                <li>
									<?php echo $this->translate('Now, configure "Email Settings" for automatic event reminder emails.');?>
                </li>
                <li>
									<?php echo $this->translate('Customize various widgetized pages from the Layout Editor section.');?>
                </li>
              <br />
              <?php echo $this->translate("You can now start creating the events on your site.");?>
						</li>
					</ul>
				</div>
			</li>
			<li>
          <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("The widths of the page columns are not coming fine on the Events Home page. What might be the reason?");?></a>
				<div class='faq' style='display: none;' id='faq_2'>
					<?php echo $this->translate('This is happening because none or very few events have been created and viewed on your site, and thus the widgets on the Events Home page are currently empty. Once events are viewed and liked on your site, and more activity happens, these widgets will get populated and the Events Home page will look good.<br /> If still the width of the pages are not coming fine, then edit the width of the page column by using the "Column Width" widget available in the SociaEngineAddOns-Core block in the Available Blocks section of the Layout Editor.');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("How can I change the labels of Featured and Sponsored markers for the Events?");?></a>
				<div class='faq' style='display: none;' id='faq_3'>
					<?php echo $this->translate('You can change the labels of Featured and New markers for the Events by replacing the "featured-label.png", "new-label.png" images respectively at the path "application/modules/Siteevent/externals/images/".<br /> To change the label for Sponsored marker, you can select a different color from "Manage Event Types" section in the admin panel of this plugin.');?>
				</div>
			</li>

			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate('How can I mark events as Hot instead of New on my site?');?></a>
				<div class='faq' style='display: none;' id='faq_4'>
					<?php echo $this->translate('To mark events as Hot instead of New, you can change the label of New marker for the Events by replacing the "new-label.png" image at the path "application/modules/Siteevent/externals/images/".');?>
				</div>
			</li>

			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate('There is a setting to choose between “Official SocialEngine Videos Plugin” and “Advanced Events - Inbuilt Videos”. What is the difference between these two?');?></a>
				<div class='faq' style='display: none;' id='faq_5'>
					<?php echo $this->translate('If you enable "Official SocialEngine Videos Plugin", then video settings will be inherited from the "Videos" plugin and videos uploaded in events will be displayed on "Video Browse Page" and "Events Profile" pages.<br /> If you enable "Advanced Events - Inbuilt Videos", then Videos uploaded in the events will only be displayed on Event Profile pages and will have their own widgetized Events - Video View page.');?>
				</div>
			</li>

			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("I want emails sent out from this plugin to be attractive. How can I do this?");?></a>
				<div class='faq' style='display: none;' id='faq_6'>
					<?php echo $this->translate('You can send attractive emails to your users via rich, branded, professional and impact-ful emails by using our "%1$s". To see details, please %2$s.', '<a target="blank" href="http://www.socialengineaddons.com/socialengine-email-templates-plugin">Email Templates Plugin</a>', '<a target="blank" href="http://www.socialengineaddons.com/socialengine-email-templates-plugin">visit here</a>');?>
				</div>
			</li>      
      
        <li>
          <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("I want to enhance the Events on my site and provide more features to my users. How can I do it?");?></a>
          <div class='faq' style='display: none;' id='faq_7'>
            <?php echo $this->translate('There are various extensions available for the "Advanced Events Plugin" which can enhance the Events on your site, by adding valuable functionalities to them. To view the list of available extensions, please %1$svisit here%2$s.', "<a href='http://www.socialengineaddons.com/catalog/advanced-events-extensions' target='_blank'>", "</a>");?>
          </div>
        </li>

			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("I have integrated Advanced Events plugin with a content module. Where can I see the events associated with that respective module?");?></a>
				<div class='faq' style='display: none;' id='faq_10'>
					<?php echo $this->translate('If you have integrated a content module from “Manage Modules” section of this plugin, then you can see the events associated with that module by placing the “Content Type: Profile Events” widget on that content’s profile page. ');?>
				</div>
			</li>

			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo $this->translate("In the import section at admin side, I can not see any button or link to import events of Stores on my site into Advanced Events. Why is it so?");?></a>
				<div class='faq' style='display: none;' id='faq_11'>
					<?php echo $this->translate("There are some required conditions which needs to be fulfilled before starting to import events of Stores into Advanced Events. Once all the conditions are fulfilled, the button for 'Start Import' will automatically appear.");?>
				</div>
			</li>

      <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo $this->translate("How can I make my users to edit categories of their Events?");?></a>
				<div class='faq' style='display: none;' id='faq_12'>
					<?php echo $this->translate("Please go to the ‘Miscellaneous settings’ under 'Global Settings' section of this plugin and select the value 'Yes' for the 'Edit Event Category' field there. This way event leaders will be able to edit the category of their events from the 'Edit Info' section available in the Event Dashboard.");?>
				</div>
			</li>
      
      <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo $this->translate('Facebook “Like”  and “Share” option are not visible in my Social Share widget at left side, what can be reason?');?></a>
				<div class='faq' style='display: none;' id='faq_13'>
					<?php echo $this->translate('Facebook "Like" and "Share" options are dependent on our “%1$sAdvanced Facebook Integration / Likes, Social Plugins and Open Graph%2$s”. So, you can use these features in Social Share widget only after installing and enabling that plugin on your website. To see details, Please %3$svisit here%4$s.', '<a href="http://www.socialengineaddons.com/socialengine-advanced-facebook-integration-likes-social-plugins-and-open-graph" target="_blank">', '</a>', '<a href="http://www.socialengineaddons.com/socialengine-advanced-facebook-integration-likes-social-plugins-and-open-graph" target="_blank">', '</a>');?>
				</div>
			</li>      

      <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_53');"><?php echo $this->translate('The height and width of the widgets are not coming fine on the Events Home page. What might be the reason?');?></a>
				<div class='faq' style='display: none;' id='faq_53'>
					<?php echo $this->translate('To do so, follow these steps below:');?>
					<ul>
						  <li>
								<?php echo $this->translate('Go to the Layout Editor.');?>
							</li> 
						  <li>
								<?php echo $this->translate('Now go to the desired widgetized page from the editing dropdown menu.');?>
							</li> 
						  <li>
								<?php echo $this->translate('Click on the widget in which the height and width are not coming fine.');?>
							</li> 
						  <li>
								<?php echo $this->translate('Now configure the settings according to you in selected widget.');?>
							</li>  
							<br />
							<?php echo $this->translate('It should now seem fine. If still the height and width of the pages are not coming fine, you can %1$scontact us%2$s.', '<a href="http://www.socialengineaddons.com/contact" target="_blank">', '</a>');?>
					</ul>
				</div>
			</li>      

      <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_54');"><?php echo $this->translate("How can I see Events according to my location in various widgets on Events Home, Browse Events, etc pages?");?></a>
				<div class='faq' style='display: none;' id='faq_54'>
					<?php echo $this->translate("If you want to see events based on your location, then simply choose ‘Yes’ option for “Do you want to display events based on user’s current location?” setting. You can also choose the miles within which events will be displayed.");?>
				</div>
			</li> 

      <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_56');"><?php echo $this->translate("When can I Review and Rate an event?");?></a>
				<div class='faq' style='display: none;' id='faq_56'>
					<?php echo $this->translate("Ratings and reviews can be given only after an event ends. In case of multiple occurrences, they can be given once first occurrence of an event ends. <br /> <b>Note:</b> Only guests will be allowed to review and rate an event.");?>
				</div>
			</li> 
      
        <li>
          <a href="javascript:void(0);" onClick="faq_show('faq_73');"><?php echo $this->translate("I want only ratings to be enabled for the events on my site. Is it possible with this plugin?");?></a>
          <div class='faq' style='display: none;' id='faq_73'>
            <?php echo $this->translate("Yes, Please go to Admin Panel >>  Reviews & Ratings, now enable the ‘Allow Only User Ratings’ settings. Now you will only be able to rate events on your site.");?>
          </div>
        </li>      

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_77');"><?php echo $this->translate("I am unable to write reviews for the events I am attending? What might be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_77'>
						<?php echo $this->translate("It is happening because ratings and reviews can be given only after an event ends. In case of multiple occurrences, they can be given once first occurrence of an event ends.");?>
					</div>
			  </li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_76');"><?php echo $this->translate("I do not want my site members to set ‘View Privacy’ for Diaries. How can I do so?");?></a>
					<div class='faq' style='display: none;' id='faq_76'>
						<?php echo $this->translate("Please go to the Admin Panel >> Member Level Settings, and select ‘Default Level’ for ‘Member Level’ setting. Now uncheck all the options available for the ‘Diaries View Privacy’. Site member will now not be able to set ‘View Privacy’ for Diaries. Also, the diaries made by the site members will be visible to everyone.");?>
					</div>
			  </li>
        
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_78');"><?php echo $this->translate("I do not want to display the views and counts for the diaries on the Browse Diaries page. How can I do this?");?></a>
					<div class='faq' style='display: none;' id='faq_78'>
						<?php echo $this->translate("Go to the Layout Editor >> Advanced Events-Browse Diaries Page >> Browse Diaries widget. You can uncheck the desired statistics available for the ‘<b>Choose the statistics that you want to be displayed for the Diaries in this block</b>’ setting in the Edit popup of this widget.");?>
					</div>
			  </li>        
        
        <li>
          <a href="javascript:void(0);" onClick="faq_show('faq_70');"><?php echo $this->translate("I am not able to see all my Events under the ‘Select Content’ option while creating an Ad. What might be the reason?");?></a>
          <div class='faq' style='display: none;' id='faq_70'>
            <?php echo $this->translate("It is happening because when you create an Ad for an Event, not all the Events gets listed under the ‘Select Content’ option, only the Events fulfilling the below mentioned criteria are available for Ad creation.");?>
            <ul>
                <li>
                  <?php echo $this->translate("Should be Approved");?>
                </li>
                  <li>
                    <?php echo $this->translate("Should be Published");?>
                  </li>
                  <li>
                    <?php echo $this->translate("Should be Searchable");?>
                  </li>
                  <li>
                    <?php echo $this->translate("Currently Running or Upcoming");?>
                  </li>
              </li>
            </ul>
          </div>
        </li>        
      
        <li>
          <a href="javascript:void(0);" onClick="faq_show('faq_69');"><?php echo $this->translate("I want only the group members to create events for their groups? Is it possible with this plugin?");?></a>
          <div class='faq' style='display: none;' id='faq_69'>
            <?php echo $this->translate("To do so, Please go to the ‘Edit Info’ section available on the Group’s Dashboard, you may set the “Event Creation Privacy” to “Group members Only” here.");?>
          </div>
        </li>   
        
        

      <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_14');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?");?></a>
				<div class='faq' style='display: none;' id='faq_14'>
					<?php echo $this->translate("Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
				</div>
			</li>      
   
		</ul>
	</div>
	<?php break; ?>
<?php case 'eventfeatures': ?>
<div class="admin_seaocore_files_wrapper">
	<ul class="admin_seaocore_files seaocore_faq">	
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_15');"><?php echo $this->translate("How can I disable the Price, Location, Overview and Host features for events?");?></a>
					<div class='faq' style='display: none;' id='faq_15'>
						<?php echo $this->translate('You can disable Price, Location, Overview, Where to Buy and many other features for events using the respective fields available in the "Miscellaneous settings" under "Global Settings" section of this plugin.');?>
					</div>
				</li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_16');"><?php echo $this->translate("In how many ways can I enable reviews on my site?");?></a>
					<div class='faq' style='display: none;' id='faq_16'>
						<?php echo $this->translate("You can either disable reviews for an event or choose to enable 'editor reviews', 'user reviews' or 'both editor and user reviews' by using the 'Allow Reviews' field in Review section under Reviews and Ratings settings.");?>
					</div>
				</li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_17');"><?php echo $this->translate("How can I rearrange the items in the search box shown on the ‘Events Home’ and ‘Browse Events’ pages?");?></a>
					<div class='faq' style='display: none;' id='faq_17'>
						<?php echo $this->translate("Yes, you can rearrange the items in the search box from the 'Search Form Settings' section in the admin panel of this plugin. There, you can drag-and-drop the search fields vertically and save the sequence as per your requirement.");?>
					</div>
				</li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_18');"><?php echo $this->translate("I do not want to show some fields in the search box to the users. How can I do this?");?></a>
					<div class='faq' style='display: none;' id='faq_18'>
						<?php echo $this->translate("Go to the 'Search Form Settings' section in the admin panel of this plugin and click on the Hide / Display icon for such fields. Fields which have been made hidden here would not be shown in the search box shown on the ‘Events Home’ and ‘Browse Events’ pages. You can also display them later if you want.");?>
					</div>
			  </li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_19');"><?php echo $this->translate("Can an Event have multiple administrators? How will this work?");?></a>
					<div class='faq' style='display: none;' id='faq_19'>
						<?php echo $this->translate('Yes, Events on your site can have multiple leaders (administrators) having equal authority on the event. Multiple leaders for events can be enabled from Miscellaneous settings under Global Settings section using the field: "Allow Multiple Event Leaders". If enabled, then every Event will be able to have multiple leaders who will be able to manage that Event. Event Leaders will have the authority to add other users as leader of their Event from the Event Dashboard.');?>
					</div>
			  </li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_20');"><?php echo $this->translate("I have multiple networks on my site and I want users to be shown only those events belonging to their network. How can this be done?");?></a>
					<div class='faq' style='display: none;' id='faq_20'>
						<?php echo $this->translate('You can do this by saving "Yes" for the "Browse by Networks" field in Global Settings.');?>
					</div>
			  </li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_21');"><?php echo $this->translate("I want users to see particular location in the location field in various widget placed on Events Home page, Browse Events page, etc. Does this plugin support this functionality?");?></a>
					<div class='faq' style='display: none;' id='faq_21'>
						<?php echo $this->translate('Yes, you can do this by saving the location (for example: Canada) for the “Default Location for Searching Events” field available in the Global settings section of this plugin.');?>
					</div>
			  </li>
        
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_61');"><?php echo $this->translate("I want to save default values for ‘Location’ and ‘Within Miles’ fields on my site? Is it possible with this plugin?");?></a>
					<div class='faq' style='display: none;' id='faq_61'>
						<?php echo $this->translate("Yes, go to the Global Settings section of this plugin and set your desired values of ‘Location’ & ‘Within Miles for: ‘Default Location for Searching Events’ and ‘Default Value for Miles / Kilometers’ settings. You will now be able to see these location settings for every search form widget on your site.");?>
					</div>
			  </li>        

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_23');"><?php echo $this->translate("I have saved location for “Default Location for Searching Events” field but still some of my user are seeing different location in various search widgets. What might be the reason for this?");?></a>
					<div class='faq' style='display: none;' id='faq_23'>
						<?php echo $this->translate("This is happening because the default location saved by you is visible to users until they share their location. After sharing the location, user’s current location get saved in browser cookies and is reflected in all the search widgets.");?>
					</div>
			  </li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_24');"><?php echo $this->translate("I want to display my events as Most Attended / Most Glamorous Events. Is there any way to do this?");?></a>
					<div class='faq' style='display: none;' id='faq_24'>
						<?php echo $this->translate("Yes, you can do so by placing our Special Events widget. Place this widget at desired location and select your events from autosuggest and appropriate settings for the widget.");?>
					</div>
			  </li>
        
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_62');"><?php echo $this->translate("I want to show timezone with the event date so that if someone in India posts an online event in their time zone, then person in the US get to know that the time is in the India time zone and not the US time zone ?");?></a>
					<div class='faq' style='display: none;' id='faq_62'>
						<?php echo $this->translate("In this plugin the event date is displayed according to the time zone that has been selected by the viewer from Settings >> General >> TimeZone. If the user has not selected any time zone then the default time set by the Admin (from the Admin Panel, Settings >> Locale Settings >> Default Timezone) is applied to its user profile.");?>
					</div>
			  </li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_63');"><?php echo $this->translate("I want to display event time in 12 hour format with am & pm for the events on my site. How can I do so?");?></a>
					<div class='faq' style='display: none;' id='faq_63'>
						<?php echo $this->translate("This setting is dependent on ‘Time Zone' and the 'Locale' setting, you can set your time zone accordingly from Settings >> General >> TimeZone to display events according to your time zone on your website.");?>
					</div>
			  </li>
        


				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_64');"><?php echo $this->translate("I want my members to see their Upcoming Events as soon as they log-in to the website without navigating through certain pages and without using various filters. Is there any way to do this?");?></a>
					<div class='faq' style='display: none;' id='faq_64'>
						<?php echo $this->translate("Yes, To do so, you can use ‘Upcoming Events’ widget and place this widget at the Member’s Home Page. There are 3 filtering criterias available in this widget, in your case you may set the filter to ‘current member's upcoming events.");?>
					</div>
			  </li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_65');"><?php echo $this->translate("I am not able to add leaders from ‘Manage Leaders’ section available on Event’s Dashboard. What might be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_65'>
						<?php echo $this->translate("It is happening because you will be able to add only those members as leader who are guests of that event. Once they join that event, you will be able to add those members as leader from the autosuggest of Manage Leaders section available on Event’s Dashboard.");?>
					</div>
			  </li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_66');"><?php echo $this->translate("I want the Event Creators on my site to be redirected to the Events Dashboard after the creation of the Event. How can I do so?");?></a>
					<div class='faq' style='display: none;' id='faq_66'>
						<?php echo $this->translate("To do so, go to the Global Settings >> Miscellaneous Settings from the admin section of this plugin. Now, choose desired option available for ‘Redirection after Event Creation’ settings. Your event creators will now be able to redirect to this page after event creation.");?>
					</div>
			  </li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_67');"><?php echo $this->translate("I can only see 2 Featured Events for ‘Popular Events Slideshow’ widget having ‘Featured Only’ popularity criteria, while I have 6 Featured Events on my site? Why is it happening so?");?></a>
					<div class='faq' style='display: none;' id='faq_67'>
						<?php echo $this->translate("Please check the ‘Count’ for ‘Popular Events Slideshow’ widget. You might have entered lesser number here, increase the count value to ‘6’ and save your changes. There are other settings also which may affect the events visibility like: Category, Event type, Popularity / Sorting Criteria, etc. Please make sure you have chosen right options for these settings.");?>
					</div>
			  </li>
        
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_79');"><?php echo $this->translate("I want sponsored / featured label to be visible for the events in ‘AJAX based Events Carousel’ widget. How can I do so?");?></a>
					<div class='faq' style='display: none;' id='faq_79'>
						<?php echo $this->translate("To do so, Please go to the Layout Editor >> Browse Events Page >> Ajax Based Events Carousel widget. Now select the ‘Featured Label’ and ‘Sponsored Label’ option available amongst the various statistics multi check boxes. You will now be able to see the sponsored / featured label for the events in ‘AJAX based Events Carousel’ widget.");?>
					</div>
			  </li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_80');"><?php echo $this->translate("I do not want ‘Show this event on browse page and in various blocks’ option to be available to my users while creating an event. How can I do so?");?></a>
					<div class='faq' style='display: none;' id='faq_80'>
						<?php echo $this->translate("Please go to the Global Settings >> Miscellaneous Settings. You may choose various options mentioned for “Event Creation Fields” setting here, which you want to be available to the users at the time of event creation.");?>
					</div>
			  </li>        
        
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_74');"><?php echo $this->translate("I do not want my site members to create online events for my site? How can I do so?");?></a>
					<div class='faq' style='display: none;' id='faq_74'>
						<?php echo $this->translate("To do so, Please go to the Global Settings >> Miscellaneous Settings, available in the admin section of this plugin, now disable the “Allow Online Events” setting from here. Site members will now not be able to create online events for your site.");?>
					</div>
			  </li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_75');"><?php echo $this->translate("While creating an Event, the Profile Fields belonging to different category are shown to me? What might be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_75'>
						<?php echo $this->translate("It is happening because you might have mapped wrong Profile Field to this category. Please go to the ‘Category-Event Profile Mapping’ section available in the admin panel of this plugin and map the associated profile field created by you for this category using, Add button.");?>
					</div>
			  </li>        
        
        

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_25');"><?php echo $this->translate('I am only seeing Join Button in the “Advanced Events - Event Profile Page Options” menu and not other buttons like Leave Button, Accept Event Invite, etc. Why is it so?');?></a>
					<div class='faq' style='display: none;' id='faq_25'>
						<?php echo $this->translate("You cannot leave an event before joining it, hence Leave button will be shown only after you have joined an event. If an event is request only type, then Request Invite option will be visible instead of Join Button. If the Owner or guests of an event which is request only type, sends you invite, then Accept Invite and Reject Invite options will be shown.");?>
					</div>
			  </li>
        
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_71');"><?php echo $this->translate("I have bought an event kit but I do not understand from where should I disable the AOL invite service?");?></a>
					<div class='faq' style='display: none;' id='faq_71'>
						<?php echo $this->translate("Go to the Admin Panel >> Invite and Promote section of this plugin. Now uncheck the desired inviter services available for the ‘Web Account Services’ setting. In your case uncheck AOL invite service, you will now not be able to see AOL service on the invite guests page of this plugin.");?>
					</div>
			  </li>  
        
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_72');"><?php echo $this->translate("I used the 'Promote Event' link to promote one of my repeating event, but now I am not able to see multiple dates available for this event in the created badge? What might be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_72'>
						<?php echo $this->translate("A Badge created from the ‘Promote Event’ link will have Start Date and End Date of the ongoing / upcoming event occurrence. In case of repeating event, If user wants to see which all dates are available for this event, he can see this from the ‘Event Occurrences’ widget placed on the Events Profile Page.");?>
					</div>
			  </li>        

			</ul>
		</div>
	<?php break; ?>
  <?php case 'repeatingevents': ?>
    <div class="admin_seaocore_files_wrapper">
        <ul class="admin_seaocore_files seaocore_faq">	
 
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_26');"><?php echo $this->translate('What is a repeating event?');?></a>
					<div class='faq' style='display: none;' id='faq_26'>
						<?php echo $this->translate("A repeating event is an event that repeat over a period of time like daily, weekly, monthly, or on specific dates. Recurring / repeating events will save time and effort that event owners spent in creating multiple separate events and it also let’s them easily change all occurrences of their events in the series at once.");?>
					</div>
			  </li>   
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_27');"><?php echo $this->translate("How to create a daily event happening on 1st February 2014 to 4th February 2014 from 2:00 p.m. - 5:00 p.m.?");?></a>
				<div class='faq' style='display: none;' id='faq_27'>
 
					<?php echo $this->translate("To create such an event, follow the steps below:");?>
					<ul>
						  <li>
							  <?php echo $this->translate("Click on Create New Event link and open event creation form.");?>
							</li>
								<li>
									<?php echo $this->translate("Now, start by filling the basic details for your event.");?>
								</li>
								<li>
									<?php echo $this->translate("Set the Start and End date / time of the first occurrence / instance of your event. Here, choose 1st Feb, 2014 - 2 p.m. in Start Time field and 1st Feb, 2014 - 5 p.m. in End Time field.");?>
								</li>
								<li>
									<?php echo $this->translate('Now, select “Daily” from "Event Repeats" field.');?>
								</li>
								<li>
									<?php echo $this->translate("A pop up will appear, select the recurring pattern from “Repeat every” field. Here, choose “1” day.");?>
									</li>
									<li><?php echo $this->translate('Now, select the duration till which you want to repeat the daily event cycle from “End this event” field. Here, choose 4th Feb, 2014.');?>
									</li>
									<li><?php echo $this->translate('After filling all the necessary details for the event, click on “Create” button.');?>
									</li>
              <br />
              <?php echo $this->translate("You have successfully created a daily event by following the above steps.");?>
              <br />
              <li><?php echo $this->translate('This event will have following occurrences:');?></li>
              <?php echo $this->translate("a) Feb 1, 2014 2:00 p.m. - 5:00 p.m.");?><br />
              <?php echo $this->translate("b) Feb 2, 2014 2:00 p.m. - 5:00 p.m.");?><br />
              <?php echo $this->translate("c) Feb 3, 2014 2:00 p.m. - 5:00 p.m.");?><br />
              <?php echo $this->translate("d) Feb 4, 2014 2:00 p.m. - 5:00 p.m.");?>
					</ul>
				</div>
			</li> 
    <li>
        
					<a href="javascript:void(0);" onClick="faq_show('faq_28');"><?php echo $this->translate("I want to create a weekly event occurring on Monday, Tuesday, Friday of every week from 1st February 2014 to 17th February 2014 at 2:00 p.m. - 5:00 p.m.. How can I do this?");?></a>
					<div class='faq' style='display: none;' id='faq_28'>
						<?php echo $this->translate('To create such an event, follow the steps below:');?>
            <ul>
                <li>
                    <?php echo $this->translate('Click on Create New Event link and open event creation form.');?>
                </li>
                <li>
                    <?php echo $this->translate('Now, start by filling the basic details for your event.');?>
                </li>
                <li>
                    <?php echo $this->translate('Set the Start and End date / time of the first occurrence / instance of your event. Here, choose 1st Feb, 2014 - 2 p.m. in Start Time field and 1st Feb, 2014 - 5 p.m. in End Time field.');?>
                </li>
                <li>
                    <?php echo $this->translate('Now, Select “Weekly” from "Event Repeats" field.');?>
                </li>
                <li>
                    <?php echo $this->translate('A pop up will appear, select the recurring pattern from “Repeat every” field and check desired days from checkboxes. Here, choose “1” week and check “Mon”, “Tue” and “Fri” options in the checkboxes.');?>
                </li>
                 <li>
                    <?php echo $this->translate('Now, select the duration till which you want to repeat the weekly event cycle from “End this event” field. Here, choose 17th Feb, 2014.');?>
                </li>
                                 <li>
                    <?php echo $this->translate('After filling all the necessary details for the event, click on “Create” button.');?>
                           
                </li><br />
                <?php echo $this->translate('You have successfully created a weekly event by following the above steps.');?><br />
                <li><?php echo $this->translate('This event will have following occurrences:');?></li>
                    <?php echo $this->translate('a) Feb 3, 2014 2:00 p.m. - 5:00 p.m.');?><br />
                   <?php echo $this->translate('b) Feb 4, 2014 2:00 p.m. - 5:00 p.m.');?><br />
                    <?php echo $this->translate('c) Feb 7, 2014 2:00 p.m. - 5:00 p.m.');?><br />
                    <?php echo $this->translate('d) Feb 10, 2014 2:00 p.m. - 5:00 p.m.');?><br />
                    <?php echo $this->translate('e) Feb 11, 2014 2:00 p.m. - 5:00 p.m.');?><br />
                    <?php echo $this->translate('f) Feb 14, 2014 2:00 p.m. - 5:00 p.m.');?><br />
                     <?php echo $this->translate('g) Feb 17, 2014 2:00 p.m. - 5:00 p.m.');?>
                
            </ul>
					</div>
				</li>  
        
        <li>
					<a href="javascript:void(0);" onClick="faq_show('faq_29');"><?php echo $this->translate("How to create a monthly event occurring on fourth Saturday after every 2 months from 1st February 2014 to 1st August 2014 at 2:00 p.m. - 5:00 p.m.?");?></a>
					<div class='faq' style='display: none;' id='faq_29'>
						<?php echo $this->translate('To create such an event, follow the steps below:');?>
            <ul>
                <li><?php echo $this->translate('Click on Create New Event link and open event creation form.');?></li>
                <li><?php echo $this->translate('Now, start by filling the basic details for your event.');?></li>
                <li><?php echo $this->translate('Set the Start and End date / time of the first occurrence / instance of your event. Here, choose 1st Feb, 2014 - 2 p.m. in Start Time field and 1st Feb, 2014 - 5 p.m. in End Time field.');?></li>
                <li><?php echo $this->translate('Now, Select “Monthly” from "Event Repeats" field.');?></li>
                <li><?php echo $this->translate('A pop up will appear, select the recurring pattern from “Repeat event on” field and desired months from “Repeat event in every” field. Here, choose Fourth Saturday from the dropdowns in “Repeat event on” field and 1 month from “Repeat event in every” field.');?></li>
                <li><?php echo $this->translate('Now, select the duration till which you want to repeat the weekly event cycle from “End this event on” field. Here, choose 1st Aug, 2014.');?></li>
                <li><?php echo $this->translate('After filling all the necessary details for the event, click on “Create” button.');?></li>
                <br />
                <?php echo $this->translate('You have successfully created a weekly event by following the above steps.');?>
                <br />
                <li><?php echo $this->translate('This event will have following occurrences:');?></li>
                  <?php echo $this->translate('a) Feb 22, 2014 2:00 p.m. - 5:00 p.m.');?><br />
                   <?php echo $this->translate('b) Apr 26, 2014 2:00 p.m. - 5:00 p.m.');?><br />
                  <?php echo $this->translate('c) June 28, 2014 2:00 p.m. - 5:00 p.m.');?>
             </ul>
            </div>
          </li>
          
                  <li>
					<a href="javascript:void(0);" onClick="faq_show('faq_30');"><?php echo $this->translate("How to create an event occurring on 2nd day of month after every 3 months from 1st February 2014 to 1st September 2014 at 2:00 p.m. - 5:00 p.m.?");?></a>
					<div class='faq' style='display: none;' id='faq_30'>
						<?php echo $this->translate('To create such an event, follow the steps below:');?>
            <ul>
                <li><?php echo $this->translate('Click on Create New Event link and open event creation form.');?></li>
                <li><?php echo $this->translate('Now, start by filling the basic details for your event.');?></li>
                <li><?php echo $this->translate('Set the Start and End date / time of the first occurrence / instance of your event. Here, choose 1st Feb, 2014 - 2 p.m. in Start Time field and 1st Feb, 2014 - 5 p.m. in End Time field.');?></li>
                <li><?php echo $this->translate('Now, Select “Monthly” from "Event Repeats" field.');?></li>
                <li><?php echo $this->translate('A pop up will appear, click on “repeat event on a date of month” link available in this popup.');?></li>
                <li><?php echo $this->translate('Now select desired value for “Repeat on every:” field and desired months from “Repeat event in every” field. Here, choose “1 day of the month” from the dropdowns in “Repeat on every” field and 3 month from “Repeat event in every” field.');?></li>
                
                <li><?php echo $this->translate('Now, select the duration till which you want to repeat the weekly event cycle from “End this event on” field. Here, choose 1st Sep, 2014.');?></li>
                <li><?php echo $this->translate('After filling all the necessary details for the event, click on “Create” button.');?></li>
                <br />
                <?php echo $this->translate('You have successfully created a monthly event by following the above steps.');?>
                <br />
                <li><?php echo $this->translate('This event will have following occurrences:');?></li>
                   <?php echo $this->translate('a) Feb 2, 2014 2:00 p.m. - 5:00 p.m.');?><br />
                   <?php echo $this->translate('b) May 2, 2014 2:00 p.m. - 5:00 p.m.');?><br />
                   <?php echo $this->translate('c) Aug 2, 2014 2:00 p.m. - 5:00 p.m.');?>
             </ul>
            </div>
          </li>
          
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_31');"><?php echo $this->translate("I want my event to repeat on my own specific dates. How can I do this?");?></a>
					<div class='faq' style='display: none;' id='faq_31'>
						<?php echo $this->translate('To do so, select the option Other (be specific) from the “Event Repeats” dropdown and add your desired custom dates and time for your event while creating your repeating event.');?>
					</div>
				</li>          
          
        </ul>   
		</div>
	<?php break; ?>
  <?php case 'host':?>
    <div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files seaocore_faq">	
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_32');"><?php echo $this->translate("I want to add a non site entity as Host of my event. Can I do this? If Yes, then how?");?></a>
					<div class='faq' style='display: none;' id='faq_32'>
						<?php echo $this->translate('Yes, you can make a non-site entity as Host of your event. To do so, follow the steps below:');?>
            <ul>
              <li>
                  <?php echo $this->translate('Go to the “Host” field on Create Event Page.');?>
              </li>
                            <li>
                  <?php echo $this->translate('Click on “Change” and choose “Other Individual or Organisation” from the dropdown.');?>
              </li>
              <li>
                  <?php echo $this->translate('Now click on “Add new” link to add new host and fill the required details for the Host you want to add. You can add Hosts’ Photo, Description, Social Pages links.');?>
              </li>
                            <li>
                  <?php echo $this->translate('After filling all the necessary details for the event, click on “Create” button.');?>
              </li>
              <br />
              <?php echo $this->translate("Note: Once you add an host, a host profile will be automatically made. And the next you only need to start typing the name of this host for the “Other Individual or Organisation” option in the dropdown.");?>
              
              
            </ul>
            
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_33');"><?php echo $this->translate("Who all can I make Host of my event?");?></a>
					<div class='faq' style='display: none;' id='faq_33'>
						<?php echo $this->translate('You can make following entities as Host of your event:');?><br />

                    <?php echo $this->translate('a) Site Member');?><br />
   
                    <?php echo $this->translate('b) Other Individual or Organisation');?><br />
     
                    <?php echo $this->translate('c) Store %1$sDependent on Stores / Marketplace - Ecommerce Plugin%2$s', '<a target="_blank" href="http://www.socialengineaddons.com/socialengine-stores-marketplace-ecommerce-plugin">', '</a>');?><br />
 
                    <?php echo $this->translate('d) Page %1$sDependent on Directory / Pages Plugin%2$s', '<a target="_blank" href="http://www.socialengineaddons.com/socialengine-directory-pages-plugin">', '</a>');?><br />
                    <?php echo $this->translate('e) Group %1$sDependent on Group / Community Plugin%2$s', '<a target="_blank" href="http://www.socialengineaddons.com/socialengine-groups-communities-plugin">', '</a>');?><br />
                    <?php echo $this->translate('f) Business %1$sDependent on Dependent on Directory / Businesses Plugin%2$s', '<a target="_blank" href="http://www.socialengineaddons.com/socialengine-groups-communities-plugin">', '</a>');?>
                    
                
					</div>
				</li>
        				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_34');"><?php echo $this->translate("I have added a “Member” as Host of my event. Now, I am not able to add “Host Description” and “Social Sites Links” for my host . What might be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_34'>
						<?php echo $this->translate('You can add “Host Description” and “Social Sites Links” for “Other Individual or Organisation” as hosts. As “Member” represents a member already registered member on site, the Information added by him in its “About Me” field will be showcase for “Host Description”. Guests can also visit host’s profile info tab for his personal details.');?>
					</div>
				</li>
    </ul>
    </div>
  <?php break; ?>
	<?php case 'editors': ?>
  <div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files seaocore_faq">	
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_35');"><?php echo $this->translate("What are Editor Reviews and how they can be useful for my site?");?></a>
					<div class='faq' style='display: none;' id='faq_35'>
						<?php echo $this->translate('Editor reviews are helpful in displaying accurate, trusted and unbiased reviews. This will bring more user engagement to your site, as editor reviews provide reviews from expert people (editors) on the events of their interest.<br /> You can choose Editors from “Manage Editors” section of this plugin.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_36');"><?php echo $this->translate("I can choose various Editors for my site, but only one Super Editor. Why is this so? What is the difference between an Editor and a Super Editor?");?></a>
					<div class='faq' style='display: none;' id='faq_36'>
						<?php echo $this->translate('You can choose any number of Editors for your site who all will be allowed to write editor reviews for various events. There can be only one Super Editor as, if any Editor deletes the respective member profile, then all the editor reviews written by that Editor will be automatically assigned to the Super Editor.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_37');"><?php echo $this->translate("I am removing a member as Editor from my site. What will happen to the editor reviews written by him?");?></a>
					<div class='faq' style='display: none;' id='faq_37'>
						<?php echo $this->translate('If you remove a member as Editor from your site, then you would be able to assign all Editor reviews written by that editor to any other editor on your site.
You can remove an editor by using "Remove" option from "Manage Editors" section of this plugin.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_38');"><?php echo $this->translate("How can I select a Featured Editor on my site?");?></a>
					<div class='faq' style='display: none;' id='faq_38'>
						<?php echo $this->translate('You can select a Featured Editor on your site by using the "Use the auto-suggest field to select Featured Editor." field available in the edit settings of "Featured Editor" widget from the Layout editor by placing the widget on any widgetized page.<br /> You can place this widget multiple times on different pages with different featured editor chosen for each placement.');?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>
<?php case 'document': ?>    
    <div class="admin_seaocore_files_wrapper">
        <ul class="admin_seaocore_files seaocore_faq">
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_39');"><?php echo $this->translate("How do I get my Scribd API details ?");?></a>
                <?php if($this->show):?>
                    <div class='faq' style='display: block;' id='faq_39'>
                <?php else:?>
                    <div class='faq' style='display: none;' id='faq_39'>
                <?php endif;?>
                    <ul>
                        <?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_getScribdKeys.tpl';?>
                    </ul>
                </div>
            </li>	
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_40');"><?php echo $this->translate("How can I change the maximum limit for the document file size ?");?></a>
                <div class='faq' style='display: none;' id='faq_40'>
                    <?php echo $this->translate('Go to the “Documents Settings” available in the “Documents” section of this plugin and change the value of "Maximum file size" field.');?>
                </div>
            </li>	
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_52');"><?php echo $this->translate("Can I create custom fields for Event Documents on my site?");?></a>
                <div class='faq' style='display: none;' id='faq_52'>
                    <?php echo $this->translate('Yes, you can do so from the "Document Questions" section.');?></a>
                </div>
            </li>
                        <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_41');"><?php echo $this->translate("I am not able to find the Suggest to Friends feature for Event Documents. What can be the reason?");?></a>
                <div class='faq' style='display: none;' id='faq_41'>
                    <?php echo $this->translate('The suggestions features are dependent on the %1$sSuggestions / Recommendations / People you may know & Inviter plugin%2$s and require that to be installed.', '<a href="http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin" target="_blank">', '</a>');?>
                </div>
            </li>
            
                                   <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_42');"><?php echo $this->translate("I am uploading documents on my website. Format conversion for most of them is being completed successfully. However, format for some documents are not getting converted. Why would this be happening?");?></a>
                <div class='faq' style='display: none;' id='faq_42'>
                    <?php echo $this->translate('The documents which are not getting converted might be password protected or copyrighted. So, please check such documents by uploading them at scribd (https://www.scribd.com/) to see if they are getting converted there. Scribd does not allow password protected or copyrighted documents to get converted.');?>
                </div>
            </li>
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_111');"><?php echo $this->translate('Some of my website users are facing "We\'re sorry!" or "Mysqli prepare error: MySQL server has gone away" error on "Document View" page. What might be the problem?'); ?></a>
                <div class='faq' style='display: none;' id='faq_111'>
                    <?php echo $this->translate('It might be happening because the document users are uploading is larger in size. So we recommend you to increase the value of "max_allowed_packet" in your database configuration and check the issue. You can also ask to your service provider for increasing the value of "max_allowed_packet".'); ?></a>
                </div>
            </li>             
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_43');"><?php echo $this->translate("I have selected the HTML5 Reader as default viewer for the documents on my site, but some documents are still being shown in Flash Reader. Why is it so?
"); ?></a>
                <div class='faq' style='display: none;' id='faq_43'>
                  <?php echo $this->translate('This is happening because secure documents use access-management technology which is available in Flash only. Therefore, secure documents will always be viewed in Flash reader, even if you choose HTML5 reader as default.'); ?></a>
                </div>
            </li>
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_44');"><?php echo $this->translate("My website is running under SSL (with https). Will this plugin work fine in this case?"); ?></a>
                <div class='faq' style='display: none;' id='faq_44'>
                  <?php echo $this->translate('All pages of this plugin will display fine on your website running under SSL (with https). The main Document view page will give an SSL warning in the browser because of some components of the document viewer which are rendered over http and not https, but the page will display fine. The Scribd document viewer currently does not support https completely.'); ?>
                </div>
            </li>
        </ul>
    </div>    
<?php break; ?>    
	
  
  
  <?php case 'admincontact':?>

   <div class="admin_seaocore_files_wrapper">
	<ul class="admin_seaocore_files seaocore_faq">	
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_45');"><?php echo $this->translate("Why emails are queued for sending ?");?></a>
			<div class='faq' style='display: none;' id='faq_45'>
				<?php echo $this->translate("Mail queueing permits the emails to be sent out over time, preventing your mail server from being overloaded by outgoing emails. We utilize mail queueing for large email blasts to help prevent negative performance impacts on your site.")?>
			</div>
		</li>	
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_46');"><?php echo $this->translate("I am not receiving messages sent from 'Message Event Owners and Leaders' section of this plugin even when I have created some Events. What is the reason ?");?></a>
			<div class='faq' style='display: none;' id='faq_46'>
				<?php echo $this->translate("You are not receiving messages sent from ''Message Event Owners and Leaders' section of this plugin because messages can be sent to other users only, you can not send messages to yourself.");?>
			</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_47');"><?php echo $this->translate("From where should I edit the header and footer of the email body ?");?></a>
			<div class='faq' style='display: none;' id='faq_47'>
				<?php echo $this->translate('To edit the header and footer of the email body, go to Admin panel -> Main Navigation Menu bar -> Settings -> Mail Templates -> Choose message(Header ( Members ) and Footer ( Members )) -> Message Body. You can also send attractive emails to your users via rich, branded, professional and impact-ful emails by using our "%1$sEmail Templates Plugin%2$s". To see details, please %3$svisit here%4$s.', '<a href="http://www.socialengineaddons.com/socialengine-email-templates-plugin" target="_blank">', '</a>', '<a href="http://www.socialengineaddons.com/socialengine-email-templates-plugin" target="_blank">', '</a>');?>
			</div>
		</li>	
    
    
	</ul>
</div>
	<?php break; ?>
  	<?php case 'inviter': ?>
  <div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files seaocore_faq">	
        <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_48');"><?php echo $this->translate("Windows Live contact importer is not working on my site, what should I do ?");?></a>
        <div class='faq' style='display: none;' id='faq_48'>
				<?php echo $this->translate('If, Windows Live contact importer is not working on your site then there may be chance that PHP Mhash package is not installed on your server. Please ask your server administrator to install this. After installing mhash, it should also be listed here:%1$shttp://demo.socialengineaddons.com/admin/system/php%2$s. This should resolve the issue.', '<a href="http://demo.socialengineaddons.com/admin/system/php" target="_blank">', '</a>');?>
			</div>
        </li>
        
        		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_49');"><?php echo $this->translate('Who can use the "Invite Guests" feature to invite their contacts to an Event?');?></a>
			<div class='faq' style='display: none;' id='faq_49'>
				<?php echo $this->translate('Owners, Leaders, Guests of an event have access to the "Invite Guests" feature but, Owners and leaders will be able to invite any site user to their event whereas Guests will be able to invite their friends only.');?>
			</div>
		</li>
    
    <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_50');"><?php echo $this->translate("Would the invitees who are already members of this site also get event invitations from their friends if sent to them?");?></a>
			<div class='faq' style='display: none;' id='faq_50'>
				<?php echo $this->translate('Yes, the invitees who are already members of this site would get a suggestion for the Event. This feature is dependent on the %1$sSuggestions / Recommendations / People you may know & Inviter plugin%2$s and require that to be installed. In case, this plugin is not installed, a suggestion notification will be sent. <br />Additionally, in both cases an email will also be sent to the invitees who are not the members of the site. Users will also be able to preview these suggestions and email templates before sending the invitations to their friends.', '<a href="http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin" target="_blank">', '</a>');?>
			</div>
		</li>
    
        <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_51');"><?php echo $this->translate("I want to enhance the Events on my site and provide more features to my users. How can I do it?");?></a>
			<div class='faq' style='display: none;' id='faq_51'>
				<?php echo $this->translate('There are various extensions available for the "Advanced Events Plugin" which can enhance the Events on your site, by adding valuable functionalities to them. To view the list of available extensions, please %1$svisit here%2$s.', "<a href='http://www.socialengineaddons.com/catalog/advanced-events-extensions' target='_blank'>", "</a>");?>
			</div>
		</li>

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_68');"><?php echo $this->translate("I am unable to see the Promote Event Link on the ‘Content Cover Photo and Information’ widget under Settings link? What might be the reason?");?></a>
      <div class='faq' style='display: none;' id='faq_68'>
        <?php echo $this->translate("It is happening because you have not enabled “Allow to Promote Event” setting available in the ‘<b>Invite and Promote</b>’ section, from the admin panel of this plugin. Please select ‘Yes’ for this setting, you will now be able to see the ‘Promote Event’ link.");?>
      </div>
    </li>    
    

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_81');"><?php echo $this->translate("I want to invite all members of a group in it’s particular Group Event? How can I do so?");?></a>
					<div class='faq' style='display: none;' id='faq_81'>
						<?php echo $this->translate("You can use our ‘‘Invite Guests’ option available on the content cover photo under settings section. By this you will be redirected to the invite guest page where you will find the option ‘Invite members belonging to this group’. Using this you will be able to invite all members of a group in it’s particular Group Event.");?>
					</div>
			  </li>    
    
    </ul>
  </div>
  <?php break; ?>
  <?php case 'email': ?>
    <div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files seaocore_faq">	
       
        <li>
          <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("Does this plugin allow my site to send automatic email reminders?");?></a>
          <div class='faq' style='display: none;' id='faq_8'>
            <?php echo $this->translate('Yes, this plugin enables you to send Automatic Event Reminders and Automatic emails after an event is ended. You can also choose the durations of this reminders from the “Reminder Emails” section of this plugin');?>
          </div>
        </li>
      
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("Can I configure Invite and Reminder emails?");?></a>
				<div class='faq' style='display: none;' id='faq_9'>
					<?php echo $this->translate('Yes, you can completely configure the content and templates of invite and reminder emails from “Settings” >> “Mail Templates” section available in the admin panel.');?>
				</div>
			</li></ul>
  </div>
  <?php break; ?>

<?php case 'tickets': ?>
    <div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files seaocore_faq">	
       
        <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("How can I start setting up my ticket extension after installation?");?></a>
				<div class='faq' style='display: none;' id='faq_1'>
					<?php echo $this->translate("After installing this extension, you can see a new tab 'Tickets & Packages' at admin panel under 'Advanced Event' plugin. From here you can start configuring your tickets related settings like Ticket Formats, Payment Settings, Packages Settings, etc. And after done the required changes at admin panel, you can start creating tickets for your events from your event dashboard.");?>
				</div>
			</li>
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("After installing this extension I am unable to see the 'Join Event' Button for the events on my site. What might be the reason?");?></a>
                <div class='faq' style='display: none;' id='faq_2'>
                    <?php echo $this->translate("It is happening because 'Join Event' Button has been replaced with ‘Book Now’ button. And, now site members need to book tickets in order to join the events instead of simply click on 'Join Event' button.");?>
                </div>
            </li>    
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_22');"><?php echo $this->translate("After installation of this extension what will happen to the events, which are already created / running on my site ?");?></a>
                <div class='faq' style='display: none;' id='faq_22'>
                    <?php echo $this->translate('We have created a script which will migrate all your ongoing and upcoming RSVP events to ticket based events along with creation of free order for already joined members for those events. After installation of this extension, you will see a tab "Migrate RSVP Events" at admin panel of this extension from here you can run this script. Once script completed successfully, you can see that a free ticket has been created for all your ongoing and upcoming events on your web site and free orders has been also created for already joined members (Attending Members Only).');?>
                </div>
            </li>               
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("I want to create Free Tickets for my events. Is it possible with this plugin ?");?></a>
                <div class='faq' style='display: none;' id='faq_3'>
                    <?php echo $this->translate("Yes, you can create Free tickets for your events by entering their price as 0 while creating a ticket from 'Tickets' section available on the event dashboard.");?>
                </div>
            </li>                
                        
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("I am unable to create paid tickets for my events. What might be the reason?");?></a>
                <div class='faq' style='display: none;' id='faq_6'>
                    <?php echo $this->translate('It seems that you have not enabled payment methods for your event tickets. Please enable payment methods for your event tickets, you can then create paid tickets for your event.');?>
                </div>
            </li> 
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("How will I be able to create tickets for my repeating event? Do I need to create separate tickets for each occurrence?");?></a>
                <div class='faq' style='display: none;' id='faq_5'>
                    <?php echo $this->translate("No, You don't need to create tickets separately. You only need to create tickets for your event and in case your event is repetitive then the same tickets with same quantity & price will be created automatically for all event occurrences.");?>
                </div>
            </li>            
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("I want to create tickets for my event but do not want it to be available to the site members now, instead I want it to publish later. Can I do this with this extension?");?></a>
                <div class='faq' style='display: none;' id='faq_4'>
                    <?php echo $this->translate('Yes sure, you can create your tickets as ‘Hidden’ and can change their status to ‘Open’ as when you want to. You can do it from ticket create / edit pop up.');?>
                </div>
            </li>             
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("What are the payment flows available with this extension and how it works ?");?></a>
                <div class='faq' style='display: none;' id='faq_7'>
                    <?php echo $this->translate('There are two payment flows available with this extension: Direct Payment to Sellers and Payment to Website / Site Admin');?>
                    
                    <ul>
                        <li>
                            <?php echo $this->translate("Direct Payment to Sellers: Here the payment made by the buyers goes to the seller’s account directly and the commission will be paid by seller to Site Admin in case any commissions has been defined.");?>
                        </li>
                        <li>
                            <?php echo $this->translate("Payment to Website / Site Admin: The payment made by buyers goes to the Site Admin’s account directly. Further, seller requests to site admin for the payment after admin commission deduction in case commissions applied. Admin can set threshold amount for seller payment request.");?>
                        </li>                  
                        <br />
                        <?php echo $this->translate("*Threshold Amount: Minimum amount after which seller can request for the payment to site admin.");?>
                    </ul>                    
                </div>
            </li>   
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_17');"><?php echo $this->translate("What is ‘Pay At Event’? How does it work?");?></a>
                <div class='faq' style='display: none;' id='faq_17'>
                    <?php echo $this->translate("‘Pay At Event’ is a payment method which can be enabled for the site members for buying tickets. It allows site members to pay for event ticket at the event location just before event starts. Or you can say it almost work as 'Cash On Delivery'.");?>
                </div>
            </li>                  
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_23');"><?php echo $this->translate("I can see Commissions tab in the admin panel of this plugin. Can you tell me how commissions will get calculated on my site?");?></a>
                <div class='faq' style='display: none;' id='faq_23'>
                    <?php echo $this->translate("Commissions gets calculated by monitoring event's package and the commission's defined for that package. Various packages can be created with varying commission amount.");?>
                </div>
            </li>            
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_20');"><?php echo $this->translate("I do not want Commission for events, just want to apply fees on creating events. Is it possible ?");?></a>
                <div class='faq' style='display: none;' id='faq_20'>
                    <?php echo $this->translate('You can created paid packages for events, by this you will be paid whenever an event get posted on your site. You can create non commission based packages with paid subscription fees. These packages can be renewed after their expiry-limit.');?>
                </div>
            </li>                 
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("Can I customize event ticket's format for my site? If yes, how can I do so?");?></a>
                <div class='faq' style='display: none;' id='faq_9'>
                    <?php echo $this->translate('Yes you can do so by following the steps mentioned below:');?>
                    <br/>
                    <?php echo $this->translate("Please go to ‘Tickets and Packages’ >> ‘Tickets Formats’ section available in the admin panel of this extension. From here you can select the 'Advertisement image', which will get printed on the event ticket and also by using the tinymce editor: You can create your own ticket format or can drag and drop the available ticket placeholders position and customize them according to your requirement.");?>
                    <br/>
                    <?php echo $this->translate("You can also see the 'Ticket PDF Preview' before saving your changes.");?>
                </div>
            </li>        
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("Can I print my website’s logo on event tickets of my site?");?></a>
                <div class='faq' style='display: none;' id='faq_10'>
                    <?php echo $this->translate('Yes, please follow the below steps to do so:');?>
                    <ul>
                        <li><?php echo $this->translate("Go to the 'Layout' >> 'File & Media Manager' from the admin panel of your site.");?></li>
                        <li><?php echo $this->translate("Upload the logo which you want to upload on the ticket.");?></li>
                        <li><?php echo $this->translate("Go to the 'Layout' >> 'Layout Editor’ >> ‘Site Header’, from the admin panel of your site.");?></li>
                        <li><?php echo $this->translate("Now configure the ‘Site Logo’ widget using ‘edit’ link, here you can select the image / logo you want to print on the ticket (uploaded by you in the 2nd step) from here.");?></li>                     
                        <br/>
                        <?php echo $this->translate("Site members will now be able to see your website logo on the event tickets.");?>
                    </ul>
                </div>
            </li>    
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo $this->translate("I want to publish Advertisement on my ticket PDF. Is it possible with this extension?");?></a>
                <div class='faq' style='display: none;' id='faq_13'>
                    <?php echo $this->translate('Yes, please follow the below steps to do so:');?>
                    <ul>
                        <li><?php echo $this->translate("Go to the 'Layout' >> 'File & Media Manager' from the Admin panel of your site.");?></li>
                        <li><?php echo $this->translate("Upload the image / advertisement you want to upload on the ticket.");?></li>
                        <li><?php echo $this->translate("Now, go to ‘Tickets and Packages’ >> ‘Tickets Formats’ section available in the admin panel of this extension.");?></li>
                        <li><?php echo $this->translate("Here, you can see the dropdown available along with the ‘Select Image for advertisement’ setting. Now select the image you want to print on the ticket (uploaded by you in the 2nd step) from here.");?></li>
                    </ul>
                    <br/>
                    <?php echo $this->translate("Your site members will now be able to see the advertisement on the ticket. [Note: It can prove to be a source of monetization for you by promoting featured/sponsored events or by publishing 3rd party ads.]");?>
                </div>
            </li>        
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_14');"><?php echo $this->translate("I want my tickets to be printed in French language. Is it possible with this extension?");?></a>
                <div class='faq' style='display: none;' id='faq_14'>
                    <?php echo $this->translate('Yes, you can do so by following the below steps:');?>
                    <ul>
                        <li><?php echo $this->translate("Go to the 'Layout' >> 'Language Manager' from the Admin panel of your site.");?></li>
                        <li><?php echo $this->translate("Now, click on 'Edit Phrases' link and search for the phrase you want to change for your ticket.");?></li>
                        <li><?php echo $this->translate("Now add your phrase here which you want to replace with the French text and click on 'Save Changes'.");?></li>                        
                    </ul>
                    <br/>
                    <?php echo $this->translate("You will now be able to see all your added text in French on the user side.");?>
                </div>
            </li>    
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_19');"><?php echo $this->translate("Can I change the text 'Event' coming everywhere in this plugin with Dance Classes, Concerts, Cooking Classes, Car Washing etc? In order to use this plugin for providing training classes, Party Passes, door to door services, etc for the same?");?></a>
                <div class='faq' style='display: none;' id='faq_19'>
                    <?php echo $this->translate('Yes sure, you can do it easily by following the below steps:');?>
                    <ul>
                        <li><?php echo $this->translate("Please go to the 'Global Settings' section available in the admin panel of this plugin.");?></li>
                        <li><?php echo $this->translate("Now configure the ‘Singular Event Title’ and ‘Plural Event Title’ and set service and services or anything you want to, for these settings.");?></li>
                    </ul>
                    <br/>
                    <?php echo $this->translate("You will now see these terms replacing the event and events on your website.");?>
                </div>
            </li>                  
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_15');"><?php echo $this->translate("I can see two blocks coming on my event tickets. What will get displayed here? It is coming blank now.");?></a>
                <div class='faq' style='display: none;' id='faq_15'>
                    <?php echo $this->translate('In your event tickets there are two rectangular boxes:');?>
                    <ul>
                        <li><?php echo $this->translate("Left Box: It will display the Terms & Conditions text mentioned by you for the event ticket and in case Terms & Condition has been not defined then, Event Overview details will be shown here.");?></li>
                        <li><?php echo $this->translate("Right Box: It will display the 'Advertisement Image' for your ticket which can be configured from ‘Tickets and Packages’ >> ‘Tickets Formats’ section available in the admin panel of this extension. If no image is configured then, it will be shown blank as it is coming now.");?></li>
                    </ul>
                </div>
            </li> 
      
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_21');"><?php echo $this->translate("I am unable to see Ticket PDF Preview button under ticket formats section, as written in the description of this plugin. What might be the reason?");?><br/><center><?php echo $this->translate("or");?></center><br/><?php echo $this->translate("I have downloaded dompdf files but, I am still not able to send ticket PDF’s through mail.  What might be the reason?");?><br/><center><?php echo $this->translate("or");?></center><br/><?php echo $this->translate("How can I download dompdf files on my website?");?></a>
                <div class='faq' style='display: none;' id='faq_21'>
                    <?php $url = $this->url(array('module' => 'siteeventticket', 'controller' => 'dompdf'), 'admin_default', true);?>
                    <?php echo $this->translate("Firstly, Please %s. Now choose one from the available methods, to upload dompdf files on your website. However, if you have downloaded dompdf files earlier and you're still unable to send ticket PDF’s through mail then re-install dompdf file by going on to the above link. After installation of these files you will be able to see ‘Ticket PDF Preview’ button under ticket formats section.", "<a href='$url', target='_blank'>click here</a>");?>
                </div>
            </li>    
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_22');"><?php echo $this->translate("I am unable to view 'Ticket PDF Preview' for my ticket, from 'Ticket Formats' section, available in the admin panel of this plugin. What might be the reason?");?></a>
                <div class='faq' style='display: none;' id='faq_22'>
                    <?php echo $this->translate("It might be possible that the site logo you are using on your website is larger in size, so you can try by using smaller size logo.");?>
                </div>
            </li>             
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo $this->translate("I want to send Event tickets to buyers via email, I have also installed Dompdf Libraries and Email Templates Plugin, still I am unable to do the same. What might be the reason?");?></a>
                <div class='faq' style='display: none;' id='faq_11'>
                    <?php echo $this->translate("It might be possible that you are having 'Email Queue' enabled for your website. Please go to 'Settings' >> 'Mail Settings' , here please select 'No, always send emails immediately' option for 'Email Queue' setting.");?>
                </div>
            </li>   
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_18');"><?php echo $this->translate("I am unable to view my event on Browse Events Page though it was visible before 2 days. What might be the reason?");?></a>
                <div class='faq' style='display: none;' id='faq_18'>
                    <?php echo $this->translate('It seems that your event package is expired, please check your package details from ‘Package’ section on event dashboard. If yes then please pay for your package if it is a paid package or upgrade your package in case of free package.');?>
                </div>
            </li>            
            
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_16');"><?php echo $this->translate("I am unable to delete tickets of my event. What might be the reason?");?></a>
                <div class='faq' style='display: none;' id='faq_16'>
                    <?php echo $this->translate('It is happening because some of the event tickets have been purchased by your event members. But if you still want to delete it then:');?>
                    <ul>
                        <li><?php echo $this->translate("You can make the ticket status as ‘Hidden’ instead of deleting but in this case tickets will remain with the members who have already purchased the tickets earlier.");?></li>
                        <li><?php echo $this->translate("You can cancel the event and make new event, in this case all the event members will receive the event cancellation notifications. [Recommendation: You need to communicate with your tickets buyers and manage refund accordingly.]");?></li>
                    </ul>
                </div>
            </li>                 

    </ul>
  </div>
  <?php break; ?>
               
                
<?php endswitch; ?>