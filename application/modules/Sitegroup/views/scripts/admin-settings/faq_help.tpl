<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
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

<?php if(!empty($this->faq)) : ?>
	<p><?php echo $this->translate("Browse the different FAQ sections of this plugin by clicking on the corresponding tabs below.") ?><p>
	<br />
	<?php $action = 'faq' ?>
<?php else : ?>
	<?php $action = 'readme' ?>
<?php endif; ?>

<div class='tabs seaocore_faq_tabs'>
		<ul class="navigation">
		  <li class="<?php if($this->faq_type == 'general') { echo "active"; } ?>">
		 	<?php echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitegroup','controller' => 'settings','action' => $action, 'faq_type' => 'general'), $this->translate('General'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'package') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitegroup','controller' => 'settings','action' => $action, 'faq_type' => 'package'), $this->translate('Packages'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'layout') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitegroup','controller' => 'settings','action' => $action, 'faq_type' => 'layout'), $this->translate('Group Layout'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'claim') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitegroup','controller' => 'settings','action' => $action, 'faq_type' => 'claim'), $this->translate('Claims'), array())
		  ?>
			<li class="<?php if($this->faq_type == 'category') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitegroup','controller' => 'settings','action' => $action, 'faq_type' => 'category'), $this->translate('Categories'), array())
		  ?>
			<li class="<?php if($this->faq_type == 'profile') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitegroup','controller' => 'settings','action' => $action, 'faq_type' => 'profile'), $this->translate('Profile Fields'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'insights') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitegroup','controller' => 'settings','action' => $action, 'faq_type' => 'insights'), $this->translate('Insights'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'import') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitegroup','controller' => 'settings','action' => $action, 'faq_type' => 'import'), $this->translate('Import'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'language') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitegroup','controller' => 'settings','action' => $action, 'faq_type' => 'language'), $this->translate('Language'), array())
		  ?>
			</li>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.isActivate',0)): ?>
			<li class="<?php if($this->faq_type == 'activity') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitegroup','controller' => 'settings','action' => 'activity-feed'), $this->translate('Activity Feed'), array())
		  ?>
			</li>
      <?php endif; ?>
			<li class="<?php if($this->faq_type == 'customize') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitegroup','controller' => 'settings','action' => $action, 'faq_type' => 'customize'), $this->translate('Customize'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'integration') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitegroup','controller' => 'settings','action' => $action, 'faq_type' => 'integration'), $this->translate('Plugin Integration'), array())
		  ?>
			</li>
		</ul>
	</div>

<?php switch($this->faq_type) : ?>
<?php case 'general': ?>
	<div class="admin_seaocore_files_wrapper">
		<ul class="admin_seaocore_files seaocore_faq">
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("How do I go about setting up this plugin on my site? What are the steps?");?></a>
				<div class='faq' style='display: none;' id='faq_1'>
					<?php echo $this->translate("Ans: Below are the steps you should follow to configure the plugin on your site according to your requirements:");?>
					<ul>
						<li>
							<?php echo $this->translate("Start by configuring the Global Settings for this plugin on your site. Some important settings here are:");?>
							<ul>
								<li>
									<?php echo $this->translate("Should Packages be enabled for directory items / groups?");?>
								</li>
								<li>
									<?php echo $this->translate("Should groups be able to have multiple admins / owners?");?>
								</li>
								<li>
									<?php echo $this->translate("Should group admins / owners be able to alter the block positions(layout) / add new available blocks on the directory item / group profile?");?>
								</li>
								<li>
									<?php echo $this->translate("Should Price and Location fields be enabled for directory items / groups?");?>
								</li>
								<li>
									<?php echo $this->translate("Should maps integration be enabled?");?>
								</li>
							</ul>
							<?php echo $this->translate("and many more.");?>
						</li>
						<li>
							<?php echo $this->translate('If you have enabled packages for directory items / groups on your site, then create packages from the "Manage Packages" section. Users will have to select a package before creating a group. You can choose settings for packages like free/paid, duration, auto-approve, featured, sponsored, features available, apps accessible, etc.');?>
						</li>
						<li>
							<?php echo $this->translate('If you have disabled packages, then settings for groups like auto-approve, featured, sponsored, features available, apps accessible, etc will depend on Member Level Settings. You may configure them from the "Member Level Settings" section.');?>
						</li>
						<li>
							<?php echo $this->translate('If you have configured paid group packages on your site, then configure payment related settings on your site from the "Billing" > "Settings" and "Billing" > "Gateways" sections.');?>
						</li>
						<li>
							<?php echo $this->translate('Configure the categories and sub-categories for groups on your site from the "Categories" section.');?>
						</li>
						<li>
							<?php echo $this->translate('Create and configure profile types and profile questions (custom fields) for groups on your site from the Profile Fields section.');?>
						</li>
						<li>
							<?php echo $this->translate('Configure the Categories to Group Profile mapping from the "Category - Group Profile Mapping" section. This feature enables you to have different profile information questions for groups based on their categories.');?>
						</li>
						<li>
							<?php echo $this->translate('Choose the type of layout for Groups on your site from the "Group Layout" section. This plugin provides 2 types of layouts: tabbed and non-tabbed.');?>
						</li>
						<li>
							<?php echo $this->translate('Configure other settings for directory items / groups on your site like: Member Level Settings, Widget Settings, Insights Settings, Ad Settings, etc.');?>
						</li>
						<li>
							<?php echo $this->translate('Place the various widgets available with this plugin at the desired locations from the "Layout" > "Layout Editor" section.');?>
						</li>
						<li>
							<?php echo $this->translate('Install and set up the desired extensions / apps for this plugin. There are various extensions available for this plugin which extend and enhance its functionalities. To see the available extensions, please visit: %s.', '<a href="http://www.socialengineaddons.com/catalog/directory-groups-extensions" target="_blank">http://www.socialengineaddons.com/catalog/directory-groups-extensions</a>');?>
						</li>
					</ul>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("Can a directory item / group have multiple administrators? How will this work?");?></a>
				<div class='faq' style='display: none;' id='faq_5'>
					<?php echo $this->translate('Ans: Yes, directory items / groups on your site can have multiple administrators having equal authority on the group. Multiple admins for groups can be enabled from Global Settings using the field: "Group Admins". If enabled, then every Group will be able to have multiple administrators who will be able to manage that Group. Group Admins will have the authority to add other users as administrators of their Group from the Group Dashboard.');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("Can the owners of directory items / groups select somes specific administrators of their groups and highlight them in some way to users?");?></a>
				<div class='faq' style='display: none;' id='faq_6'>
					<?php echo $this->translate('Ans. Yes, Group owners can do so by making some admins as "Featured" from the Group Dashboard. These Featured Group admins will then be displayed in a block at Group Profile.');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("What is Proximity Search?");?></a>
				<div class='faq' style='display: none;' id='faq_7'>
					<?php echo $this->translate("Ans: Proximity Search enables users to find directory items / groups within a certain distance from a location.");?>
				</div>
			</li>

			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_34');"><?php echo $this->translate('How can I disable the Price Field from the "Create New Group" section?');?></a>
				<div class='faq' style='display: none;' id='faq_34'>
					<?php echo $this->translate('Ans: Go to the "Global Settings" section of this plugin and set the "Price Field" field to "No" and save the settings. This will disable the Price field at the "Create New Group" form.');?>
				</div>
			</li>

			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_60');"><?php echo $this->translate('What is the difference between sponsored and featured groups?');?></a>
				<div class='faq' style='display: none;' id='faq_60'>
					<?php echo $this->translate('Ans: "Sponsored" and "Featured" are just some kind of special or can say preferred categories which are in some way highlighted through dedicated widgets on the site.<br />Now, if we talk about the difference between the two, then there is no difference as such on the level of functionality between the two. They just work like labels for a particular group so that user can view the Group with that label.<br /><br />For ex- there is a "Featured Groups Slideshow widget" and a "Sponsored Groups Carousel widget" in this plugin.<br />So, one difference in terms of display of Featured and Sponsored Groups is that Featured Groups are displayed in Slideshow and Sponsored Groups are displayed in Carousel in the respective widgets.<br /><br />But there is no such criteria only on which a group can be made sponsored or featured. Site admin can make any group sponsored or featured considering the value of that Group and also users can select packages accordingly while creating group to 
make their group sponsored or featured.');?>
				</div>
			</li>
			
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("How can groups be made sponsored? Where are sponsored groups displayed? Can I have users to pay for Sponsored Groups?");?></a>
				<div class='faq' style='display: none;' id='faq_8'>
					<?php echo $this->translate("Ans: There are 3 ways in which groups can be made sponsored:");?>
							<br />
							<?php echo $this->translate('1) If you have packages enabled on your site, then you can choose for groups of a package to be automatically made sponsored.');?>
							<br />
							<?php echo $this->translate('2) If packages are disabled, then you can choose from Member Level Settings that groups created by certain member levels be automatically made sponsored.');?>
							<br />
							<?php echo $this->translate('3) From the Manage Groups section, you can make / remove groups as sponsored.');?>
							<br />
					<?php echo $this->translate('Sponsored Groups are shown in the "Sponsored Groups Carousel" widget. Also, at various places like in listing of groups on Groups Home and in Browse Groups, sponsored groups are specially marked. The setting: "Default ordering on Browse Groups" in Global Settings also enable you to show sponsored groups at the top in the listing on browse groups. Also, on the main profile of sponsored groups, you can configure a label to be shown above the profile picture. Users can be made to pay to get their groups sponsored by making auto-sponsored packages as paid packages.'); ?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("How can groups be made featured? Where are featured groups displayed? Can I have users to pay for Featured Groups?");?></a>
				<div class='faq' style='display: none;' id='faq_9'>
					<?php echo $this->translate("Ans: There are 3 ways in which groups can be made featured:");?>
							<br />
							<?php echo $this->translate('1) If you have packages enabled on your site, then you can choose for groups of a package to be automatically made featured.');?>
							<br />
							<?php echo $this->translate('2) If packages are disabled, then you can choose from Member Level Settings that groups created by certain member levels be automatically made featured.');?>
							<br />
							<?php echo $this->translate('3) From the Manage Groups section, you can make / remove groups as featured.');?>
							<br />
					<?php echo $this->translate('Featured Groups are shown in the "Featured Groups Slideshow" widget. Also, at various places like in listing of groups on Groups Home and in Browse Groups, featured groups are specially marked. The setting: "Default ordering on Browse Groups" in Global Settings also enable you to show featured groups at the top in the listing on browse groups. Also, on the main profile of featured groups, you can configure a label to be shown below the profile picture. Users can be made to pay to get their groups featured by making auto-featured packages as paid packages.'); ?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("I have multiple networks on my site and I want users to be shown only those groups belonging to their network. How can this be done?");?></a>
				<div class='faq' style='display: none;' id='faq_10'>
					<?php echo $this->translate('Ans: You can do this by saving "Yes" for the "Browse by Networks" field in Global Settings.');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_47');"><?php echo $this->translate("I have closed a Group. But still I am able to view this. How can I disable the display of closed groups to users ?");?></a>
				<div class='faq' style='display: none;' id='faq_47'>
					<?php echo $this->translate("Ans: To do so, please go to the 'Global Settings' section of this plugin and find the field 'Open / Closed status in Search'. Setting this field to 'No' will disable the display of Closed Groups to users on the Browse Groups and in the search options and results.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_22');"><?php echo $this->translate("The widths of the webgroup columns are not coming fine on Groups Home. What might be the reason?");?></a>
				<div class='faq' style='display: none;' id='faq_22'>
					<?php echo $this->translate("Ans: This is happening because none or very few directory items / groups have been created and viewed on your site, and thus the widgets on Groups Home are currently empty. Once groups are rated and liked on your site, and more activity happens, these widgets will get populated and Groups Home will look good.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_35');"><?php echo $this->translate('Is there any need of Google Maps JavaScript API keys?');?></a>
				<div class='faq' style='display: none;' id='faq_35'>
					<?php echo $this->translate('Ans: No, there is no need of Google Maps JavaScript API keys. This is because we are using Google Maps Javascript API V3 in this plugin and this version no longer needs API keys. For more details, you can visit this link: %s', '<a href="http://code.google.com/apis/maps/documentation/javascript/basics.html" target="_blank">http://code.google.com/apis/maps/documentation/javascript/basics.html</a>');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_58');"><?php echo $this->translate('Almost all the Groups of my site belongs to the location which come under a particular area or city. Is there any way so that users can clearly view the nearby areas and locations in the map without manually zooming it?');?></a>
				<div class='faq' style='display: none;' id='faq_58'>
					<?php echo $this->translate("Ans: Yes, you can set the center location for the Groups in the field 'Center Location for Map at Groups Home and Browse Groups' under the Global Settings section of this plugin. Below this option, you can enter the default zoom level for the map as per your requirement so that users do not have to manually do that.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_48');"><?php echo $this->translate("How can I rearrange the items in the search box shown on the ‘Groups Home’ and ‘Browse Groups’ groups?");?></a>
				<div class='faq' style='display: none;' id='faq_48'>
					<?php echo $this->translate("Ans: Yes, you can rearrange the items in the search box from the 'Search Form Settings' section in the admin panel of this plugin. There, you can drag-and-drop the search fields vertically and save the sequence as per your requirement.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_49');"><?php echo $this->translate("I do not want to show some fields in the search box to the users. How can I do this?");?></a>
				<div class='faq' style='display: none;' id='faq_49'>
					<?php echo $this->translate("Ans: Go to the 'Search Form Settings' section in the admin panel of this plugin and click on the Hide / Display icon for such fields. Fields which have been made hidden here would not be shown in the search box shown on the ‘Groups Home’ and ‘Browse Groups’ groups. You can also display them later if you want.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_57');"><?php echo $this->translate("I want my users to be able to link their Groups to other related Groups. Can it be done ?");?></a>
				<div class='faq' style='display: none;' id='faq_57'>
					<?php echo $this->translate("Ans: Yes, you can link your Group to the other related groups with this plugin. To do so, please go the 'Global Settings' section of this plugin and select the option 'Yes' for the field 'Linking Groups' there. Doing so will make a 'Link to your Group' link appear on Groups and then users can select any of their Groups which they want to be linked to that Group.<br /><br />Once a Group is linked to their Group, that Group will appear in a widget titled 'Linked groups' on their Group's Profile.<br /> Please make sure that 'Group Profile Linked Groups' widget is placed on the Profile Group Layout.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_28');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?");?></a>
				<div class='faq' style='display: none;' id='faq_28'>
					<?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homegroup and then check the group which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
				</div>
			</li>
		    <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_100');"><?php echo $this->translate("I have changed the word: “Group” to some other word (say Directory, Group, etc), but I am seeing my changed word in place of “Group” at various places in the admin panel also. Why is this happening?");?></a>
				<div class='faq' style='display: none;' id='faq_100'>
					<?php echo $this->translate("Ans: SocialEngine uses same language variables for both the admin panel and user sides. Therefore, when you change the word: “Group” to some other word, then this change is visible in both user and administration sides.");?>
				</div>
			</li>
		</ul>
	</div>
	<?php break; ?>

	<?php case 'package': ?>
	<div class="admin_seaocore_files_wrapper">
		<ul class="admin_seaocore_files seaocore_faq">
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("What are Group Packages? How are Packages and Member Level Settings related?");?></a>
				<div class='faq' style='display: none;' id='faq_2'>
					<?php echo $this->translate('Ans: Both packages and member level settings enable you to configure settings for groups belonging to them / created by members belonging to them, like auto-approve, featured, sponsored, features available, apps accessible, etc. Packages can be enabled from "Global Settings" and created from "Manage Packages" section. If packages are disabled, then settings configured in Member Level Settings apply. If packages are enabled, then before creating a group on your site, users will have to choose a package for it. Packages in this system are very flexible and create many settings as mentioned below, to suit your needs:');?>
					<ul>
						<li>
							<?php echo $this->translate('Paid / Free package. Paid packages enable you to monetize your site!');?>
						</li>
						<li>
							<?php echo $this->translate('Package cost');?>
						</li>
						<li>
							<?php echo $this->translate('Lifetime Duration for groups of this package (forever, or fixed duration)');?>
						</li>
						<li>
							<?php echo $this->translate('Auto-Approve groups of this package');?>
						</li>
						<li>
							<?php echo $this->translate('Make groups of package sponsored');?>
						</li>
						<li>
							<?php echo $this->translate('Make groups of package featured');?>
						</li>
						<li>
							<?php echo $this->translate('Which of the following features should be available to groups of this package:');?>
							<ul>
								<li>
									<?php echo $this->translate('Tell a friend');?>
								</li>
								<li>
									<?php echo $this->translate('Print');?>
								</li>
								<li>
									<?php echo $this->translate('Rich Overview');?>
								</li>
								<li>
									<?php echo $this->translate('Location Map');?>
								</li>
								<li>
									<?php echo $this->translate('Insights');?>
								</li>
								<li>
									<?php echo $this->translate('Contact Details');?>
								</li>
<!--								<li>
									<?php echo $this->translate('Save to Foursquare Button');?>
								</li>-->
                <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouptwitter')) :?>
                  <li>
                    <?php echo $this->translate('Display Twitter Updates');?>
                  </li>
                <?php endif;?>
								<li>
									<?php echo $this->translate('Send an Update');?>
								</li>
							</ul>
							<?php echo $this->translate('and more'); ?>
						</li>
						<li>
							<?php echo $this->translate('Modules / App Extensions that should be available to groups of this package.'); ?>
						</li>
						<li>
							<?php echo $this->translate('Show all, none or restricted profile information for groups of this package. Restricted profile information will enable you to choose the profile fields that should be available to groups of this package.'); ?>
						</li>
					</ul>
				</div>
			</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("I have created a new package but no Apps are available for the groups of this package. What can be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_3'>
						<?php echo $this->translate("Ans. To enable the various Apps for the groups of a package, you would have to edit this package from 'Manage Packages' section and select the modules in the 'Modules / Apps' field which you want to be available for the groups of that package.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("Can the Group owner change the package of a Group after its creation?");?></a>
					<div class='faq' style='display: none;' id='faq_4'>
						<?php echo $this->translate("Ans: Yes, Group owners can change the package of their Groups from the 'Packages' section at Group Dashboard. All the available packages with their features are listed there. Once the package for a group is changed, all the settings of the group will be applied according to the new package, including apps available, features available, price, etc.<br />Please note that if you(admin) have not selected the 'Auto-Approve' field for the new package in 'Manage Packages' section, then the Group will get dis-approved. Also, if the new package is a paid package then the Group will get dis-approved even if it has been made auto-approved by you(admin) and group owner will have to first make the payment to make it approved again.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_59');"><?php echo $this->translate("Why am I not able to delete a Group Package?");?></a>
					<div class='faq' style='display: none;' id='faq_59'>
						<?php echo $this->translate("Ans: You can not delete a Group Package once you have created it. This is because the consistency of the already created groups in that package would get affected in that case. But if you wish, you can disable a group package from Manage Packages section and hence it would not be displayed in the list of packages during the initial step of the group creation and also while upgrading the package of a group at Group's Dashboard.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_60');"><?php echo $this->translate("Is it possible to let users create free trial groups on my site valid only for a limited period of time?");?></a>
					<div class='faq' style='display: none;' id='faq_60'>
						<?php echo $this->translate('Ans: Yes, it is possible to do so by doing the following things:<br />1) You can disable the existing free package from the "Manage Packages" section at the admin panel of this plugin.<br />2) Now, create a new free package from there and you can set the specifications including the "Duration" and the features you want to give users in that package and then do not select the checkbox for field \'Show in "Other available Packages" List\' so that users will not be able to upgrade their group\'s package and use this package for more than the no. of days you have set there.<br />3) Also, in future when you do not want users to create free Groups anymore at your site, you can disable all the FREE packages from "Manage Packages" section.');?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'layout': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo $this->translate("Can I enable the owners of directory items / groups on my site to customize the position of blocks / layout of their groups, and add new blocks to it?");?></a>
					<div class='faq' style='display: none;' id='faq_11'>
						<?php echo $this->translate('Ans: Yes, you can do so by enabling the "Edit Group Layout" field in Global Settings. If enabled, group admins will be able to alter the block positions / add new available blocks on the directory item / group profile. Group admins will also be able to add HTML blocks on their directory item / group profile.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo $this->translate("Can I change the layout types for profile of directory items / groups on my site? What options do I have?");?></a>
					<div class='faq' style='display: none;' id='faq_12'>
						<?php echo $this->translate('Ans: Yes, you can choose from amongst 2 attractive AJAX based layouts for Groups on your site, from the "Group Layout" section. The 2 layouts are: Tabbed Layout and Non-tabbed Layout. The 2 layouts differ primarily in the way the AJAX based widgets like Info, Updates, Overview, and modules based widgets like Photos, Videos, Reviews, etc are displayed. The tabbed layout has main widgets as ajax based tabs in a horizontal row and in the middle column of the group. The non-tabbed layout has main widgets as ajax based links in a vertical order and in the left column of the group, below the group profile picture.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_45');"><?php echo $this->translate("I do not want some tabs to be hidden in the 'More' at the Group Profile. I want users to view them directly on the Group profile. What should I do ?");?></a>
					<div class='faq' style='display: none;' id='faq_45'>
						<?php echo $this->translate("Ans: If you want some specific tabs to be shown directly and not in the 'More' tab, then you can do the following things:<br /><br />1) Go to the Global Settings section of this plugin, and set the value of 'Tabs / Links' field like that so as to occupy the whole space in the navigation bar for tabs at Group profile. This will display that much of tabs out of the 'More' tab on the Group Profile.<br /><br />2) Now, if user 'Edit Group Layout' is disabled from Global Settings section then, you can go to the 'Layout' > 'Layout Editor' section at the admin side and edit the Group profile layout and place those widgets a bit above vertically in the tab container there.<br /> Otherwise individual Group Admins will be able to set the ordering of tabs on the Group Profile from their Group Dashboard's 'Edit Layout' section.<br /><br />It will let those tabs appear in priority and out of the 'More' tab if there are too many tabs.");?>
					</div>
				</li>
				<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_43');"><?php echo $this->translate("The left column menu is not shown in the tabs of Groups Plugin Extensions on Group profile. But this is not the case with 'Info' and 'Updates' tabs. Why is it so ?");?></a>
				<div class='faq' style='display: none;' id='faq_43'>
					<?php echo $this->translate("Ans: In that case Community Ads would have been installed at your site and is being integrated with Groups Plugin.<br /><br />When Ads are shown in the tabs of groups extensions at Group profile, we have to hide the left side menu column. This is because of the lack of width in that case. So, to display the Ads in such a way that there is no UI issue, we are hiding the left side menu in case of extension tabs.<br /><br /> While at Info and Update tabs, there is sufficient width to display Ads at the same Group and we do not need to hide left side menu in that case.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_44');"><?php echo $this->translate("I have messed up with the layout of Group Profile and want to set it to the default layout again. What should I do ?");?></a>
				<div class='faq' style='display: none;' id='faq_44'>
					<?php echo $this->translate("Ans: Yes, you can reset the layout of your Groups to default layout from the 'Group Layout' > 'Group Profile Layout Type' section at the admin side of this plugin. You can just choose one out of the two layouts there and save it. It will set the layout of all the Groups at your site to the default layout.");?>
				</div>
			</li>
      <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_80');"><?php echo $this->translate('How can I configure Groups Main Navigation Menu bar? Suppose my site is an Album based site and I want users to view "Albums" tab above other tabs in the Groups Main Navigation Menu. How can I do this?');?></a>
				<div class='faq' style='display: none;' id='faq_80'>
					<?php echo $this->translate('Ans: You can do so by following the steps mentioned below:<br /><br />
						1) Go to the Global Settings section of this plugin, and set the value of "Tabs in Groups navigation bar" as the number of tabs you want to be displayed before the "More" drop-down link including more link.<br /><br />
						2)Now, to configure the tabs, go to the "Layout" > "Menu Editor" section at the admin side. There on selecting "Group Main Navigation Menu" from "Editing" drop-down you can add item or edit the items in the navigation bar. To re-order the tabs drag and drop the tabs to their desired position.');?>
				</div>
			</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'claim': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo $this->translate("Can users claim directory items / groups on my site, if they are the rightful owner? How does this work?");?></a>
					<div class='faq' style='display: none;' id='faq_13'>
						<?php echo $this->translate('Ans: The claiming of groups feature can be enabled from the "Claim a Group Listing" field in Global Settings. If enabled, users will be able to file claims for directory items / groups. You can customize the position of the "Claim a Group" link from Global Settings. Claims filed by users can be managed from the "Manage Claims" section. From "Member Level Settings", you can choose if users of a member level should be able to claim groups. Whenever someone makes a claim for a group, that claim comes to you(admin) for review and approval.<br />You(admin) can also assign certain users as "Claimable Group Creators" from the "Manage Claims" section. Though using the "Claim a Group" link a user can make a claim for any group, the groups created by "Claimable Group Creators" get the "Claim this Group" link on the group profile itself. This would be useful in cases like if you have certain members whose job is to create only those 
groups on your site which could later be easily claimed by their 
rightful owners.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_14');"><?php echo $this->translate("How can I modify the Terms of Service for claiming a group?");?></a>
					<div class='faq' style='display: none;' id='faq_14'>
						<?php echo $this->translate('Ans: You can modify them from the Language variables of the plugin using the Language Manager. Go to the Layout > Language Manager and search for the two variables "GROUP_TERMS_CLAIM_1" and "GROUP_TERMS_CLAIM_2" and edit them according to your specifications.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_15');"><?php echo $this->translate("If I approve a claim request for a Group, what changes take place?");?></a>
					<div class='faq' style='display: none;' id='faq_15'>
						<?php echo $this->translate("Ans: If you approve a claim request by a user for a Group, it means that you are assigning that Group to the claimer and the claimer will now be the new owner of that group. All ownership rights for this group will then be transferred to the new owner. In this case, both the new and old owners will be recieving an email concerning the change in the ownership of the Group.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_16');"><?php echo $this->translate("Can I change the owner of a Group and assign it to someone else even if I have not recieved a claim request for that Group?");?></a>
					<div class='faq' style='display: none;' id='faq_16'>
						<?php echo $this->translate("Ans. Yes, you can change the owner of a group from the 'change owner' option in the 'Mange Groups' section. Both the new and old owner of the group will be recieving an email concerning the change in the ownership of the group.");?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'category': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_23');"><?php echo $this->translate("The Categories and Sub-categories widget is not showing all the categories and sub-categories. What can be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_23'>
						<?php echo $this->translate("Ans: This widget only shows those categories and sub-categories which have atleast one group in them. However, the Categories and Sub-categories sidebar widget shows all the categories and sub-categories irrespective of the number of groups in them.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_24');"><?php echo $this->translate("There is no option to delete a category or sub-category. If I want to delete them, how can I do so ?");?></a>
					<div class='faq' style='display: none;' id='faq_24'>
						<?php echo $this->translate("Ans. Go to the 'Categories' section and click on the category or sub-category name which want to delete. The category name would then appear in a text box for editing. Now, you can either edit category name or delete the whole text to delete the category.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_40');"><?php echo $this->translate("How can I make my users to edit categories of their Groups ?");?></a>
					<div class='faq' style='display: none;' id='faq_40'>
						<?php echo $this->translate("Ans: Please go to the 'Global Settings' section of this plugin and select the value 'Yes' for the 'Edit Group Category' field there. This way group admins will be able to edit the category of their Groups even after the group creation in the 'Edit Info' section of the Group Dashboard.");?>
					</div>
				</li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_46');"><?php echo $this->translate("I am not able to see sub-categories below some categories in the widget on the 'Groups Home' group. Why is it so ?");?></a>
					<div class='faq' style='display: none;' id='faq_46'>
						<?php echo $this->translate("Ans: This is because you would have not selected any subcategory for those Groups created in those categories. And as we have told you in the 1st FAQ of this section also that this widget only shows those categories and sub-categories which have atleast one group in them.");?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'profile': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_17');"><?php echo $this->translate("Can I add custom profile fields for directory items / groups? Can these fields be related to the categories of the groups?");?></a>
					<div class='faq' style='display: none;' id='faq_17'>
						<?php echo $this->translate('Ans: Yes, you can create custom profile fields for groups from the "Profile Fields" section. Profile Fields can be created by first creating profile types and then creating profile questions for them. You can also associate mappings between categories and group profile types from the "Category-Group Profile Mapping" section, such that profile fields are available to groups based on their categories. If you have not created such mappings between categories and profile types, then group owners will have to select a profile type before filling profile information.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_39');"><?php echo $this->translate("I have added some Profile fields for Groups, but these are not shown on the Group creation form. Why is it so ?");?></a>
					<div class='faq' style='display: none;' id='faq_39'>
						<?php echo $this->translate('Ans: The profile questions which are created by the site admin in different profile types for the Groups on the site does not appear at the time of the creation of the Group in the create form.<br /><br />These profile information related questions appear after the creation of the Group at the Group Dashboard in the "Profile Info" tab.<br /><br />The user can select the profile type there and then fill the profile info in various fields. In case the category of that group had already mapped with a profile type by you (site admin), then the user can directly fill the information in the corresponding fields of the profile type.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_18');"><?php echo $this->translate("I have created a new Profile Type for Groups and associated it with a category. But while editing the Profile Info of a Group in that category from the Group Dashboard, all the fields of that profile type did not appear. What can be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_18'>
						<?php echo $this->translate("Ans. Availability of Profile fields to groups also depends on their package; if packages are disabled, then it depends on the member level settings for the group owner. Now, you would have unchecked some profile fields in the 'Profile Information' field of the package your group belongs to. And if packages are disabled, then you would have unchecked some profile fields in the 'Profile Creation' field of 'Member Level Settings' for the member level of the owner of this Group. So, Please check these settings.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_19');"><?php echo $this->translate("How can I re-arrange the sequence of the custom profile fields for groups?");?></a>
					<div class='faq' style='display: none;' id='faq_19'>
						<?php echo $this->translate('Ans: You may drag-and-drop the fields in the "Profile Fields" section to their desired position in sequence.');?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'insights': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_20');"><?php echo $this->translate("Can directory item / group owners see the performance of their group listing?");?></a>
					<div class='faq' style='display: none;' id='faq_20'>
						<?php echo $this->translate('Ans: Yes, group owners can see both graphical as well as statistical reports of their groups. Graphical statistics are shown in the "Insights" section of the Group Dashboard and tabular statistics are shown in the "Reports" section. Insights can also be made available to groups based on their package. Insights for groups show graphical statistics and other metrics such as views, likes, comments, active users, etc over different durations and time summaries. Using this, group admins will also be getting periodic, auto-generated emails containing Group insights. Settings for these can be configured from the "Insights Email Settings" and "Insights Graph Settings" sections in the admin panel.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_25');"><?php echo $this->translate("For the Graphical Insights of the Groups on my site, the dates shown do not match with the actual dates of the activities. For example, it shows up yesterday's or tomorrow's date in place of today's date. How should this be corrected ?");?></a>
					<div class='faq' style='display: none;' id='faq_25'>
						<?php echo $this->translate('Ans. This is happening because of the Locale Settings of your site. Please go to the "Settings" > "Locale Settings" section in the Admin Panel and set the \'Default Timezone\' according to your location.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_26');"><?php echo $this->translate("I am not able to view comments for the Groups in the insights displayed at Group Profile, graph, reports etc. What can be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_26'>
						<?php echo $this->translate("Ans: Comments are shown at all these places only if Info widget is rendered on Group Profile as comments can be posted from there. You might have removed the Info widget from Group Profile. Please check it from the 'Layout' > 'Layout Editor' section.");?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>
	<?php case 'import': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_50');"><?php echo $this->translate("I want to import many Groups altogether from a CSV file. Is that possible with this plugin ?");?></a>
					<div class='faq' style='display: none;' id='faq_50'>
						<?php echo $this->translate("Ans: Yes, you can import many groups at the same time from a CSV file. To do so, please go to the 'Import' section at the admin side of this plugin and read all the instructions given there under the section 'Import Groups from a CSV file' and then click on 'Import a file' link.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_51');"><?php echo $this->translate("I want to convert all the Listings of my site into Groups. How can it be done ?");?></a>
					<div class='faq' style='display: none;' id='faq_51'>
						<?php echo $this->translate("Ans: Yes, you can convert your Listings into Groups but there are some required conditions for that. Please go to the 'Import' section at the admin side of this plugin and fulfil all the required conditions given under the section 'Import Listings into Groups'. Then, you can import your Listings into Groups there.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_52');"><?php echo $this->translate("In the import section at admin side, I can not see any button or link to import Listings at my site into Groups. Why is it so ?");?></a>
					<div class='faq' style='display: none;' id='faq_52'>
						<?php echo $this->translate("Ans: There are some required conditions which are required to be fulfilled before starting to import Listings into Groups. when the condition becomes true or is fulfilled, it appears in green otherwise red. So, once all the conditions are green or fulfilled, the button for 'Import listings' will automatically appear there.");?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'language': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_21');"><?php echo $this->translate("I want to use this plugin for directory of cars. How can I change the word: 'groups' to 'cars' in this plugin?");?></a>
					<div class='faq' style='display: none;' id='faq_21'>
						<?php echo $this->translate("Ans: You can easily use this plugin for creation of any type of directories. You can change the word 'groups' to your directory type from the 'Layout' > 'Language Manager' section in the Admin Panel.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_42');"><?php echo $this->translate("I want to change the text 'groupitems' to 'caritems' in the URLs of this plugin. How can I do so ?");?></a>
					<div class='faq' style='display: none;' id='faq_42'>
						<?php echo $this->translate('Ans: To do so, please go to the Global Settings section of this plugin. Now, search for the fields:<br />1) "Groups URL alternate text for \'groupitems\'" <br /> 2) "Groups URL alternate text for \'groupitem\'" <br /><br />Now, enter the text you want to display in place of \'groupitems\' and \'groupitem\' in the respective text boxes there.');?>
						</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_29');"><?php echo $this->translate("There are multiple languages on my site. How should this plugin be used for non-English languages?");?></a>
					<div class='faq' style='display: none;' id='faq_29'>
						<?php echo $this->translate("Ans : This plugin only comes with English language by default. For other languages, you need to copy the 'sitegroup.csv' language file from the directory: '/application/languages/en/' of your site, to the directory '/application/languages/LANGUAGE_PACK_DIRECTORY/'. Then, go to the section 'Layout' > 'Language Manager' in the Admin Panel and edit phrases for the desired language.");?>
						</div>
				</li>
			</ul>
		</div>
	<?php break; ?>
	<?php case 'customize': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_56');"><?php echo $this->translate("I want to remove some information from the 'Basic Information' section in the Info tab at the Group profile. How can I do so?");?></a>
					<div class='faq' style='display: none;' id='faq_56'>
						<?php echo $this->translate("Ans: You can do so by commenting the code corresponding to the sections you do not want to display in the following template file:<br />'application/modules/Sitegroup/widgets/info-sitegroup/index.tpl'");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_57');"><?php echo $this->translate("I want to customize the main menu for Groups / Communities. From where can I do this ?");?></a>
					<div class='faq' style='display: none;' id='faq_57'>
						<?php echo $this->translate("Ans: Yes, you can do so from the admin panel at: 'Layout' > 'Menu Editor' section. Open the 'Group Main Navigation Menu' to edit and then you can edit all the items of this menu there.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_41');"><?php echo $this->translate("I want to change the 'Groups' text in the mini-navigation bar for Groups / Communities. How can I do so ?");?></a>
					<div class='faq' style='display: none;' id='faq_41'>
						<?php echo $this->translate('Ans: To do this, follow the steps below:<br /><br />1) Please open the file with path: <br />"application/modules/Sitegroup/views/scripts/payment_navigation_views.tpl"<br /><br />2) Now, change the word "Groups" to anything you want at line no. 15 (approx) in this file.<br /><br />It will change the text "Groups" to as desired in the mini navigation bar for Groups / Communities.');?>
						</div>
				</li>
				<li>
				  <a href="javascript:void(0);" onClick="faq_show('faq_33');"><?php echo $this->translate("How can I improve search engine optimization (SEO) for the main directory item / group profile?");?></a>
				  <div class='faq' style='display: none;' id='faq_33'>
					  <?php echo $this->translate("Ans: A quick and easy way to improve SEO for group profile is to manually do the template level changes as detailed below (Please note that these changes will need to be re-done when you upgrade SocialEngine on your site.):");?>
					  <br /><br />
					  <?php echo $this->translate('1) 2 easy changes in the template file : "/application/modules/Core/widgets/container-tabs/index.tpl":'); ?>
					  <br /><br />
					  <?php echo $this->translate('i) Open the template file and find around line 50:'); ?>
					  <br /><br />
					  <div class="code">
						  <?php echo '&lt;li class="&lt;?php echo $class ?&gt;"&gt;&lt;a href="javascript:void(0);" onclick="tabContainerSwitch($(this), \'&lt;?php echo $tab[\'containerClass\'] ?&gt;\');"&gt;&lt;?php echo $this->translate($tab[\'title\']) ?&gt;&lt;?php if( !empty($tab[\'childCount\']) ): ?&gt;&lt;span&gt;(&lt;?php echo $tab[\'childCount\'] ?&gt;)&lt;/span&gt;&lt;?php endif; ?&gt;&lt;/a&gt;&lt;/li&gt;'; ?>
					  </div>
					  <br />
					  <?php echo $this->translate('Replace the above code with the below code:') ?>
					  <br /><br />
					  <div class="code">
						  <?php echo '&lt;li class="&lt;?php echo $class ?&gt;"&gt;&lt;a href="&lt;?php echo  Zend_Controller_Front::getInstance()->getRequest()->getRequestUri()."/tab/".$tab[\'id\']?&gt;" onclick="tabContainerSwitch($(this), \'&lt;?php echo $tab[\'containerClass\'] ?&gt;\'); return false;"&gt;&lt;?php echo $this->translate($tab[\'title\']) ?&gt;&lt;?php if( !empty($tab[\'childCount\']) ): ?&gt;&lt;span&gt;(&lt;?php echo $tab[\'childCount\'] ?&gt;)&lt;/span&gt;&lt;?php endif; ?&gt;&lt;/a&gt;&lt;/li&gt;'; ?>
					  </div>
					  <br />
					  <?php echo $this->translate('2) 1 easy addition in the CSS file : "application/modules/Core/externals/style/main.css":'); ?>
					  <br /><br />
					  <?php echo $this->translate('Open this CSS file and add the below code at the end of this file:'); ?>
					  <div class="code">
					  <?php echo $this->translate('.tab_pulldown_contents > ul > li >a, .tab_pulldown_contents > ul > li >a:hover{<br />
		  color: $theme_tabs_font_color;<br />
		  text-decoration:none;<br />
	  }'); ?>
					  </div>
					  <br /><br />
					  <?php echo $this->translate('NOTE: Whenever you will upgrade Socialengine Core at your site, these changes will be overwritten and you will have to do them again in the respective files as mentioned above.'); ?>
				  </div>
				</li>
				<li>

				  <a href="javascript:void(0);" onClick="faq_show('faq_70');"><?php echo $this->translate("Can I change the Featured label for the Groups created on my site? If yes then how?");?></a>
				  <div class='faq' style='display: none;' id='faq_70'>
				    <?php echo $this->translate('Ans: Yes, you can change the look of the Featured label for the Groups on your site. You can customize Featured label for your site by replacing the image in the file at the path "application/modules/Sitegroup/externals/images/featured-label.png".');?>
				  </div>
				</li>
				<li>
				  <a href="javascript:void(0);" onClick="faq_show('faq_71');"><?php echo $this->translate("Can I customize the icon for Featured Groups on my site? If yes then how?");?></a>
				  <div class='faq' style='display: none;' id='faq_71'>
				    <?php echo $this->translate('Ans: Yes, you can customize the icons for Featured Groups on your site by replacing the image in the file at the path "application/modules/Sitegroup/externals/images/sitegroup_goldmedal1.gif".');?>
				  </div>
				</li>


			</ul>
		</div>
	<?php break; ?>

	<?php case 'integration': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_53');"><?php echo $this->translate("I have installed the Groups Discussions extension plugin on my site. But I can not find it in the 'Plugins' dropdown at the admin panel. Why should I do ?");?></a>
					<div class='faq' style='display: none;' id='faq_53'>
						<?php echo $this->translate("Ans: Discussion for Groups / Communities is a FREE extension plugin. Hence, it does not have admin section of its own and gets automatically enabled once you install it.<br />Its settings are controlled from the admin section of Groups / Communities Plugin only.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_54');"><?php echo $this->translate("I do not find disscussion section in my groups. Where can I enable it ?");?></a>
					<div class='faq' style='display: none;' id='faq_54'>
						<?php echo $this->translate("Ans: To enable disscussions for Groups, please go to the 'Manage Packages' section of this plugin (if packages are enabled from Global Settings) and edit the packages one by one for which you want to enable Discussions for Groups and select the checkbox corresponding to the 'Discussions' in the 'Modules / Apps' field.<br /><br />If Packages are disabled from Global Settings section of this plugin, then 'Discussions' depend on the Comments privacy. If comments are enabled for that member level then discussions are also otherwise not.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_27');"><?php echo $this->translate("I have installed the Community Ads plugin and set the 'Advertise your Group Widget' and 'Sample Ad Widget' options as 'Yes' in the 'Ad Settings' section. But I am not able to view these widgets on Group Profile. What can be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_27'>
						<?php echo $this->translate('Ans: These widgets appear in the Info tab on Group Profile and will only be visible to Group admins. This widget is not shown to the Group admins if an Ad corresponding to that Group has been created. So, Please check this.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_55');"><?php echo $this->translate("I have installed the Community Ads plugin and then trying to create an Ad corresponding to a Group from the Group profile, then I am redirected to a Group saying that there are no Ad packages available although I am having packages in my Ads Plugin. What can be the reason ?");?></a>
					<div class='faq' style='display: none;' id='faq_55'>
						<?php echo $this->translate("Ans: This happens when you have not enabled 'Group' for any of the Ad Packages available on your site. You can do so by going to the 'Manage Packages' section of Community Ads Plugin and then editing the Package which you want to be available while creating the Ads corresponding to the Groups. Now, for the field 'Content Advertised in this Package', select the 'Group' option too in the multi-select box there.<br />In this way, that package would then be available when you will try to create an Ad corresponding to any Group from the Group Profile.");?>
					</div>
				</li>
				<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_30');"><?php echo $this->translate("I am not able to find the Suggest to Friends feature and the Groups Recommendations widget in this plugin. What can be the reason?");?></a>
				<div class='faq' style='display: none;' id='faq_30'>
					<?php echo $this->translate('These features are dependent on the %1$sSuggestions / Recommendations / People you may know & Inviter%2$s plugin and require that to be installed.', '<a href="http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin" target="_blank">', '</a>');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_31');"><?php echo $this->translate("I want to improve the Likes functionality of this plugin. How can that be done?");?></a>
				<div class='faq' style='display: none;' id='faq_31'>
					<?php echo $this->translate('Ans: You may install the %1$sLikes Plugin and Widgets%2$s on your site to incorporate its integration with this plugin.', '<a href="http://www.socialengineaddons.com/socialengine-likes-plugin-and-widgets" target="_blank">', '</a>');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_32');"><?php echo $this->translate("I want to enhance the Groups on my site and provide more features to my users. How can I do it?");?></a>
				<div class='faq' style='display: none;' id='faq_32'>
					<?php echo $this->translate('Ans: There are various apps / extensions available for the "Groups / Communities Plugin" which can enhance the Groups on your site, by adding valuable functionalities to them. To view the list of available extensions, please visit: %s.', '<a href="http://www.socialengineaddons.com/catalog/directory-groups-extensions" target="_blank">http://www.socialengineaddons.com/catalog/directory-groups-extensions</a>');?>
				</div>
			</li>
			</ul>
		</div>
	<?php break; ?>
<?php endswitch; ?>
