<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: readme.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<h2><?php echo $this->translate('Listings / Catalog Showcase Plugin') ?></h2>

<div class="tabs">
	<ul class="navigation">
		<li class="active">
			<a href="<?php echo $this->baseUrl() .'/admin/list/settings/readme'?>" ><?php echo $this->translate('Please go through these important points and proceed by clicking the button at the bottom of this page.') ?></a>
    </li>
	</ul>
</div>		

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
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("Q : I want to use this plugin for listings of cars. How can I change the word: 'listings' to 'cars' in this plugin?");?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php echo $this->translate("You can easily use this plugin for creation of any type of directories. You can change the word 'listing' to your record type from the 'Layout' > 'Language Manager' section in the Admin Panel.");?>
				</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Q : How should I start with creating listings on my site?");?></a>
			<div class='faq' style='display: none;' id='faq_2'>
				<?php echo $this->translate("After plugin installation, start by configuring the Global Settings and Member Level Settings for your plugin. Then go to the Listing Questions section to create custom fields if required for the listings on your site. Then create the categories and sub-categories for the listings on your site. You can now start creating the listings on your site.");?>
				</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("Q : The widths of the page columns are not coming fine on the Listings Home page. What might be the reason?");?></a>
			<div class='faq' style='display: none;' id='faq_3'>
				<?php echo $this->translate("This is happening because none or very few listings have been created and viewed on your site, and thus the widgets on the Listings Home page are currently empty. Once listings are rated and liked on your site, and more activity happens, these widgets will get populated and the Listings Home page will look good.");?>
				</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("Q : The Categories and Sub-categories widget is not showing all the categories and sub-categories. What can be the reason?");?></a>
			<div class='faq' style='display: none;' id='faq_4'>
				<?php echo $this->translate("This widget only shows those categories and sub-categories which have atleast one listing in them. However, the Catgories and Sub-categories sidebar widget shows all the categories and sub-categories irrespective of the number of listings in them.");?>
				</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("Q : Is there any need of Google Maps JavaScript API keys?");?></a>
			<div class='faq' style='display: none;' id='faq_10'>
				<?php echo $this->translate('Ans: No, there is no need of Google Maps JavaScript API keys. This is because we are using Google Maps Javascript API V3 in this plugin and this version no longer needs API keys. For more details, you can visit this link: %s', '<a href="http://code.google.com/apis/maps/documentation/javascript/basics.html" target="_blank">http://code.google.com/apis/maps/documentation/javascript/basics.html</a>');?>
				</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("Q : The CSS of this plugin is not coming on my site. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_5'>
				<?php echo $this->translate("Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
				</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("Q : I am not able to find the Videos feature in this plugin. What can be the reason?");?></a>
			<div class='faq' style='display: none;' id='faq_6'>
				<?php echo $this->translate("The Videos feature is dependent on the Videos Plugin of SocialEngine and requires that to be installed.");?>
				</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("Q : I am not able to find the Suggest to Friends feature, Suggestions popup and the Listings Recommendations widget in this plugin. What can be the reason?");?></a>
			<div class='faq' style='display: none;' id='faq_7'>
				<?php echo $this->translate("These features are dependent on the <a href='http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin' target='_blank'>Suggestions / Recommendations / People you may know & Inviter</a> plugin and requires that to be installed.");?>
				</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("Q : I want to improve the Likes functionality of this plugin. How can that be done?");?></a>
			<div class='faq' style='display: none;' id='faq_8'>
				<?php echo $this->translate("You may install the <a href='http://www.socialengineaddons.com/socialengine-likes-plugin-and-widgets' target='_blank'>Likes Plugin and Widgets</a> on your site to incorporate its integration with this plugin.");?>
				</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("Q : How can I re-arrange the sequence of the custom fields for listings?");?></a>
			<div class='faq' style='display: none;' id='faq_9'>
				<?php echo $this->translate("You may drag-and-drop the fields in the Listing Questions section to their desired position in sequence.");?>
				</div>
		</li>

	</ul>
</div>
<br />
<button onclick="form_submit();"><?php echo $this->translate('Proceed to enter License Key') ?> </button>
	
<script type="text/javascript" >
	function form_submit() {
		
		var url='<?php echo $this->url(array('module' => 'list', 'controller' => 'settings'), 'admin_default', true) ?>';
		window.location.href=url;
	}
</script>