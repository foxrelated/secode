<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: readme.tpl 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Likes Plugin & Widgets') ?></h2>
<div class="tabs">
	<ul class="navigation">
		<li class="active">
    	<a href="<?php echo $this->baseUrl() .'/admin/sitelike/global/readme'?>" ><?php echo $this->translate('Please go through these important points and proceed by clicking the button at the bottom of this page.') ?></a>
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
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php echo $this->translate("Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
			</div>
		</li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("On my site's Member Profile/Group/Event page, the like button is not coming in the correct position, what should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_2'>
				<?php echo $this->translate("On these pages, the Like Button is a widget. Please go to Admin Page > Layout > Layout Editor, and select the corresponding page. You may change the widget position over here.");?>
			</div>
		</li>
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate( "I have a 3rd party content module installed on my website. How can I integrate it with this plugin such
that Likes of its content are shown in the various widgets, user profiles, etc? ") ; ?></a>
      <div class='faq' style='display: none;' id='faq_3'>
        <?php echo $this->translate("You can configure such an integration for any 3rd party content plugin that extends SocialEngine’s Likes system from the Manage Modules section of this plugin.")
?>
      </div>
    </li>


<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate( "I want to show the Most Likes Items of a content type in a widget. How can I do this?") ; ?></a>
      <div class='faq' style='display: none;' id='faq_4'>
        <?php echo $this->translate("You can do this by first placing the “Most Liked Items (selected content)” widget at the desired location from the Layout Editor, and then choosing the content
type after clicking on the “edit” link for the widget.")
?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate( "I am not able to like my profile. What could be the reason?") ; ?></a>
      <div class='faq' style='display: none;' id='faq_5'>
        <?php echo $this->translate("You can not do this because no member is allowed to like his own profile. A member can only like other members profile on your site.")
?>
      </div>
    </li>
	</ul>
</div>
<br>
<button onclick="form_submit();"><?php echo $this->translate('Proceed to enter License Key') ?> </button>
	
<script type="text/javascript" >

	function form_submit() {
		
		var url='<?php echo $this->url(array('module' => 'sitelike', 'controller' => 'global', 'action' => 'index'), 'admin_default', true) ?>';
		window.location.href=url;
	}

</script>