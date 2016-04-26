<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageurl
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2011-11-15 9:40:21Z SocialEngineAddOns $
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
<div class="admin_sitepage_files_wrapper">
	<ul class="admin_sitepage_files sitepage_faq">
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("Q. A Page on my website has been assigned a short URL. However the short URL is not coming for that Page. What could be the reason?");?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php echo $this->translate("Ans: The number of Likes on that Page would not have exceeded the Likes Limit for Active Short URL configured by you in Global Settings. Short URLs for Pages are activated when their Likes exceed the value set by you for Likes limit.")?>
			</div>
		</li>	
    <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Q. There is a Page on my website which has the number of Likes greater than the Likes Limit for Active Short URL configured in Global Settings. However, short URL is still not activated for this Page. What could be the reason?");?></a>
			<div class='faq' style='display: none;' id='faq_2'>
				<?php echo $this->translate("Ans: The short URL set for this Page might have been added to the Banned URLs, and would have thus been blocked. In this case, this Page would be appearing in the “Pages with Banned URLs” section. Edit the short URL for this Page from that section.");?>
			</div>
		</li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("Q. There are already Pages created on my website. What will happen to their URLs?");?></a>
			<div class='faq' style='display: none;' id='faq_3'>
				<?php echo $this->translate("Ans: This extension will automatically pick up the URLs of those Pages for their Short URLs. Check the “Pages with Banned URLs” section of this plugin’s administration to see the Pages which will have conflicting URLs and edit their URLs.");?>
			</div>
		</li>		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("Q. What type of short URLs should I ban for Pages from the Banned URLs section?");?></a>
			<div class='faq' style='display: none;' id='faq_4'>
				<?php echo $this->translate("Ans: You should ban all standard URLs, i.e., URLs from other plugins, etc. If a non-banned URL from a plugin gets assigned to a Page, then that corresponding plugin’s webpage will not be accessible, rather, the Page will open at that URL. The list in the Banned URLs section comes pre-configured with some banned URLs. You can also ban URLs containing offensive terms.");?>
			</div>
		</li>	
    <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("Q: Is there a way to simply let the Directory / Pages Short Page URL Extension work all the time and not be limited to a minimum of 5 likes?");?></a>
			<div class='faq' style='display: none;' id='faq_5'>
				<?php echo $this->translate('Ans: The reason of making this a requirement to have minimum 5 likes for Directory / Pages Short Page URL Extension to work:<br />
				Suppose your site has URL shorten working for 0 likes also, then in that case if there exists a page created by a user with URL:<br />
				http://yoursitename.com/files<br />
				and one of the plugins installed on your site is already using this URL.<br />

				Then in this situation when there is an ajax based call for the URL of the plugin then also page URL will be opened as it will not be listed in the "Banned URLs".
				So to avoid this situation we fixed the minimum likes requirement to 5 for URL to be shortened.
				By doing this URL will be listed in the "Banned URLs" and will not be shortened until and unless you do not change it by clicking on "edit" in the "Admin Panel" of this plugin from the tab "Pages with Banned URLs".');?>
			</div>
		</li>	
    <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("Q. The CSS of this plugin is not coming on my site. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_6'>
				<?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
			</div>
		</li>	
	</ul>
</div>