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
<h2><?php echo $this->translate('Birthday Plugin') ?></h2>
<div class="tabs">
	<ul class="navigation">
		<li class="active">
       <a href="<?php echo $this->baseUrl() .'/admin/birthday/settings/readme'?>" ><?php echo $this->translate('Please go through these important points and proceed by clicking the button at the bottom of this page.') ?></a>
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
			<a href="javascript:void(0);" onClick="faq_show('faq_0');"><?php echo $this->translate("Q. When I unzipped the plugin's zipped file, there were 2 modules in it. Do I need to install both?");?></a>
			<div class='faq' style='display: none;' id='faq_0'>
				<?php echo $this->translate("Yes, you will need to install both the modules for this plugin.");	?>
			</div>
		</li>	
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("Q.What all formats can I choose from for the Birthdays Widget layout?");?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php echo $this->translate("You can choose your Birthdays Widget to be one of these types: 'Names only', 'Profile pictures only', 'Profile pictures and names', 'Calendar'. While the Calendar format display makes the widget to be always visible, and shows birthdays in the previous, current and next years, the other formats make the widget to be visible only if there are birthdays in the current date, and show birthdays in the current date with a link to the birthdays listing page. All formats have good-looking tooltips allowing you to easily wish or message the persons having their birthdays.");	?>
			</div>
		</li>	
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Q.Where can users find the link for the Birthdays Listing Page?");?></a>
			<div class='faq' style='display: none;' id='faq_2'>
				<?php echo $this->translate("You, the Admin, can enable the link for the Birthdays Listing Page on the Member Homepage in the left-side menu. Additionally, the Birthdays widgets (other than the Calendar format widget) contain the link for that page. Also, if a particular date has more than 2 birthdays, then the tooltip for it in the Calendar format Birthdays Widget also has a link for that page.");	?>
			</div>
		</li>	
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("Q.Can I set the maximum number of entries to be shown in the Birthdays Widget?");?></a>
			<div class='faq' style='display: none;' id='faq_3'>
				<?php echo $this->translate("Yes, you can set the maximum number of entries for the Birthdays Widget (other than the Calendar format) in the Global Settings.");	?>
			</div>
		</li>	
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("Q.Does this plugin allow my site to send automatic emails?");?></a>
			<div class='faq' style='display: none;' id='faq_4'>
				<?php echo $this->translate("Yes, this plugin enables you to send Automatic Birthday Wish and Automatic Birthday Reminder emails. You can enable personalized automatic birthday wish emails from your site to your members. Additionally, you can also enable your users to receive automatic email reminders for upcoming birthdays of their friends.");	?>
			</div>
		</li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("Q.Can I configure the wishes and reminders emails?");?></a>
			<div class='faq' style='display: none;' id='faq_5'>
				<?php echo $this->translate("Yes, you can completely configure the content and templates of the wishes and reminders emails from the 'Email Settings' and 'Mail Templates' pages. You can also include a birthday wish image in the Automatic Birthday Wish email.");	?>
			</div>
		</li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("Q.The CSS of this plugin is not coming on my site. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_6'>
				<?php echo $this->translate("Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");	?>
			</div>
		</li>	
	</ul>
</div>
<br />
<button onclick="form_submit();"><?php echo $this->translate('Proceed to enter License Key') ?> </button>
	
<script type="text/javascript" >

function form_submit() {
	
	var url='<?php echo $this->url(array('module' => 'birthday', 'controller' => 'settings'), 'admin_default', true) ?>';
	window.location.href=url;
}

</script>
