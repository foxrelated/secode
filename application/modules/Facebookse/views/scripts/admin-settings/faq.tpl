<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Advanced Facebook Integration / Likes, Social Plugins and Open Graph');?></h2>
<?php if( count($this->navigation) ): ?>
	<div class='seaocore_admin_tabs'>
		<?php	echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
	</div>
<?php endif; ?>

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
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("Q Is this plugin dependent on SocialEngine's in-built Facebook Integration?");?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php echo $this->translate("Enabling of SocialEngine's in-built Facebook Integration is not required for this plugin to work. The only feature of this plugin which is dependent on SocialEngine's Facebook Integration to be enabled is the 'Facebook Friends on community' page. This plugin has been developed such that it will not conflict with other Facebook integration script on your site.") ;?>
			</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Q Where do I enter the Facebook Application details for the integration?");?></a>
			<div class='faq' style='display: none;' id='faq_2'>
				<?php echo $this->translate("For this plugin to work, you should have created an application on Facebook from the Application Developers (<a href='http://www.facebook.com/developers/apps.php' target='_blank'>http://www.facebook.com/developers/apps.php</a>) page. Fill the required information in the About and Web Site sections of this application's settings. After that, go to the 'Settings' > 'Facebook Integration' page of your site's Admin Panel and fill the details. Note that this plugin does not depend on what you choose for the 'Integrate Features' field.");?>
			</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("Q How do I go about setting up this plugin on my site?");?></a>
			<div class='faq' style='display: none;' id='faq_3'>
			  <?php $ietext = "Follow the below steps to set up this plugin on your site:<br />1) Enter the Facebook Application details for your site as explained in the above question.<br />2) Configure the Global Settings for this plugin from the Global Settings section.<br />3) Choose the settings for the Facebook Like Buttons on your site for the various content types, homepage, and user profiles from the 'FB Like Button Settings' section. Note that upon plugin installation, these buttons are by default enabled for these content types.<br />4) Choose whether you want to integrate the Facebook Like Buttons on your site with the Likes system of SocialEngine from the 'Likes Integration' section. This integration feature is dependent on the <a href=' http://www.socialengineaddons.com/socialengine-likes-plugin-and-widgets' target='_blank' > Likes Plugin and Widgets</a> and requires it to be installed on your site.<br />5) Choose the settings for the various Facebook Social Plugins from the 'FB Social Plugins Settings' section.<br />6) Place the widgets for the Social Plugins at the desired locations from the 'Layout' > 'Layout Editor' section in the Admin Panel. Note that at plugin installation, these widgets are already placed at some places.<br />7) Configure the settings for the Open Graph Protocol on your site, and enable it for the desired content types and site homepage.<br />";?>
				<?php echo $this->translate($ietext);?>
			</div>
		</li>

    <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_14');"><?php echo $this->translate("Q IMPORTANT: In Internet Explorer, some Facebook components such as Social Plugins, Like Buttons are not showing up, and Open Graph tags are not working. What should I do?");?></a>
			<div class='faq' style='display: none;' id='faq_14'>
			  <?php $ietext = ' <br /><pre> FIND (around line 15):<div class="code">&lt;html xmlns="http://www.w3.org/1999/xhtml"</div><br /> REPLACE this with: <div class="code">&lt;html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"</div></pre>';?>
				<?php echo $this->translate("You need to apply a change to a template file as described below:<br />OPEN template file: application/modules/Core/layouts/scripts/default.tpl <br /> %s <br />NOTE: This change will have to be re-applied every time that you upgrade your SocialEngine core.", $ietext);?>
			</div>
		</li>

     <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_15');"><?php echo $this->translate("Q I have done the template level change mentioned in the above point, but Open Graph is still not working on my site. The Open Graph tags are not coming on my site's content web page?");?></a>
			<div class='faq' style='display: none;' id='faq_15'>
				<?php echo $this->translate("Firstly, ensure that you have configured the Open Graph Setting for this content type from the Open Graph Settings section, and saved the form. Then, ensure that you have followed and filled the 'HeadMeta View Helper modification' field in the Global Settings and have saved the Global Settings. The Open Graph must now work on your site.");?>
			</div>
		</li>

     <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_16');"><?php echo $this->translate("Q I have another plugin installed on my site for connecting to Facebook, which uses old Facebook libraries. Will it conflict with this plugin?");?></a>
			<div class='faq' style='display: none;' id='faq_16'>
				<?php echo $this->translate("Our plugin uses the latest SDKs, API libraries and techniques released by Facebook. The older javascript library has been deprecated by Facebook, and is not compatible with the latest library. Facebook strongly recommends that all sites using its old library move to the new one. Most of the features of this plugin will work in the presence of old library. However, only the below mentioned 2 functionalities will not work because of the other plugin using old Facebook libraries:<br />1. Likes Integration<br />2. Facebook Likes Statistics");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("Q How can this plugin integrate with my SocialEngine community's Likes system?");?></a>
			<div class='faq' style='display: none;' id='faq_4'>
				<?php echo $this->translate("You can integrate the Facebook Like Buttons on your site with the Likes system of your site, such that, if a user clicks on the Facebook Like Button for an item, the item will also be Liked on your site. This integration requires the <a href='http://www.socialengineaddons.com/socialengine-likes-plugin-and-widgets' target='_blank'>Likes Plugin and Widgets</a> to be installed on your site. Note that the vice-versa will not happen, i.e., if the user clicks the Like button of the site, then the item will not get liked on Facebook, as that is not in our control. If the user clicks the Facebook Like Button to unlike an item, then that item will not get unliked on site.");?>
			</div>
		</li>

     <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_17');"><?php echo $this->translate("Q The Likes Integration and Facebook Likes Statistics are not working on my site. What might be the reason?");?></a>
			<div class='faq' style='display: none;' id='faq_17'>
				<?php echo $this->translate("This might be happening because of another plugin on your site which would be using old Facebook libraries. Our plugin uses the latest SDKs, API libraries and techniques released by Facebook. The older javascript library has been deprecated by Facebook, and is not compatible with the latest library. Facebook strongly recommends that all sites using its old library move to the new one.");?>
			</div>
		</li>
		
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("Q What is Open Graph Protocol?");?></a>
			<div class='faq' style='display: none;' id='faq_5'>
				<?php echo $this->translate("The Open Graph protocol enables you to integrate your Web pages into the social graph. It is currently designed for Web pages representing profiles of real-world things. Including Open Graph tags on your Web page, makes your Site's page equivalent to a Facebook Page. This means when a Facebook user clicks a Like button on a page of your site, a connection is made between your page and the user. Your page will appear in the 'Likes and Interests' section of the user's Facebook profile, and you have the ability to publish updates to the user on Facebook. Your page will show up in same places that Facebook pages show up around Facebook (e.g. FB search, user feeds, profiles, etc), and you can target ads to Facebook people who Like your Site's content.");?>
			</div>
		</li>
		
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("Q I want to know more about Open Graph protocol. Where can I find the information?");?></a>
			<div class='faq' style='display: none;' id='faq_6'>
				<?php echo $this->translate("You can find more information about Open Graph protocol over here: <a href='http://developers.facebook.com/docs/opengraph' target='_blank'> http://developers.facebook.com/docs/opengraph</a> .");?>
			</div>
		</li>
		
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("Q Corresponding Facebook Pages are not being created for some of the content types. What might be the reason?");?></a>
			<div class='faq' style='display: none;' id='faq_7'>
				<?php echo $this->translate("This must be happening because of your Open Graph Settings. For certain Meta Object Types (for example 'article'), we have found that Facebook does not create corresponding admin manageable Facebook Pages, and thus, it is out of our control.");?>
			</div>
		</li>
		
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("Q I want to provide more great Facebook related features like Facebook News Feed, Friends Invite, etc to my community's users. How can I do that?");?></a>
			<div class='faq' style='display: none;' id='faq_8'>
				<?php echo $this->translate("You may check out and purchase our <a href='http://www.socialengineaddons.com/socialengine-facebook-news-feed-friends-invite-and-more' target='_blank'>Facebook News Feed, Friends, Invite and more</a> plugin for attractive features, and great experience for your users.");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("Q I also want the Facebook Feed Publishing feature on my site. How can I get that?");?></a>
			<div class='faq' style='display: none;' id='faq_9'>
				<?php echo $this->translate("You may purchase our <a href='http://www.socialengineaddons.com/socialengine-facebook-feed-stories-publisher' target='_blank' >Facebook Feed Stories Publisher</a> plugin for this feature.");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("Q What should be the dimensions of the Social Plugins for the various widget positions?");?></a>
			<div class='faq' style='display: none;' id='faq_10'>
				<?php echo $this->translate("The Social Plugins can be placed at any widget position on your site. Below are the suitable dimensions for the various widget positions:<br />a) Full width :<br />Width : 938 px (maximum)<br />Height : 275 px (height can be flexible)<br />b) Middle column :<br />Width : 517 px (maximum)<br />Height : 250 px (height can be flexible)<br />c) Left column :<br />Width : 174 px (maximum)<br />Height : 180 px (height can be flexible)<br />d) Right column :<br />Width : 174 px (maximum)<br />Height : 180 px (height can be flexible)<br />e) Extended Right / Extended Left column :<br />Width : 728 px (maximum)<br />Height : 275 px (height can be flexible)<br />");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo $this->translate("Q How can I place a Facebook Like Button at a page of my choice?");?></a>
			<div class='faq' style='display: none;' id='faq_11'>
				<?php echo $this->translate("This plugin already allows you to put Facebook Like Buttons on the pages of popular content types from the 9 SocialEngine plugins and Documents. To put Like Buttons on other pages, you may use the Facebook Like Button Configurator to generate the code, and place that code in a suitable template file. Note that the button code depends on the URL of the page.");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo $this->translate("Q I want to see more detailed analytics for the Facebook Integration on my site. Where can I see them?");?></a>
			<div class='faq' style='display: none;' id='faq_12'>
				<?php echo $this->translate("Facebook provides you more detailed analytics for the integration with your site over here: <a href='http://www.facebook.com/insights/' target='_blank'> http://www.facebook.com/insights/ </a> . Note that insights are only available for sites with 30 or more users connected to Facebook.");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo $this->translate("Q The CSS of this plugin is not coming on my site. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_13'>
				<?php echo $this->translate("Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_18');"><?php echo $this->translate("Q I want to remove the site's Like Button and only want to show the Facebook Like Button for a content type. What should I do?");?></a>
			<div class='faq' style='display: none;' id='faq_18'>
				<?php echo $this->translate("If you want to remove the site's Like Button for a content type then you can do it in the following ways:<br /><br />1) If the main content page is a widgetized page then you can simply go to your site's admin panel at: 'Admin' => 'Layout' => 'Layout Editor'. Then select that widgetized page from the drop-down 'Editing' link and remove the widget for Like Button from here.<br /> <br />2) If the main content page from where you want to remove the site's Like Button is not a widgetized page then you can remove it by going in your site's admin panel at: 'Admin' => 'Plugins' => 'Likes Plugin & Widgets'. Here you will see the settings for site's Like Button for various content types. Choose the setting to remove the Like Button from content type of your choice.");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_19');"><?php echo $this->translate("Q I have changed the 'Meta Title' and 'Meta Type' for a content and now when I like it using Facebook Like button, the old 'Meta Title' and 'Meta Type' is shown at facebook side for the content. What could be the reason?");?></a>
			<div class='faq' style='display: none;' id='faq_19'>
				<?php echo $this->translate("Facebook maintains a cache of your content, thus saving the old values and the changes made by you will reflect at Facebook side after 24 hours, when the cache gets cleared. Also, the value of 'og:title' gets fixed at facebook side when your content has got 50 likes and 'og:type' is fixed once your content has got 10.000 likes. These properties are fixed to avoid maintain the trust of users who have already liked your content.");?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_20');"><?php echo $this->translate("Q I have enabled the 'Open Graph' feature at my site but when I like a content Facebook Like button is picking up wrong info of the content. What should I do?");?></a>
			<div class='faq' style='display: none;' id='faq_20'>
				<?php echo $this->translate("Please make sure that you are liking the content which either belongs to SocialEngine Core modules or SocialEngineAddOns Modules. Now, if you still have a problem with the content, please make sure that you have enabled the 'Open Graph' feature for this type of content on your site. To enable open graph for a content, please go to : 'Admin => Plugins => Advanced Facebook Integration => Open Graph Settings' section. <br />
Now, if the problem still occurs, please check that you have not made any changes within 24 hours as Facebook maintains a cache of your content and clears it in every 24 hours. So, your changes will be reflected after 24 hours at Facebook side.<br /> <br />If you want to instantly clear the cache for your content, then go to the link: <a href=\"https://developers.facebook.com/tools/debug\" target='_blank'>https://developers.facebook.com/tools/debug</a> and put the URL of your site content here to clear the cache.");?>
			</div>
		</li>
		
	</ul>
</div>