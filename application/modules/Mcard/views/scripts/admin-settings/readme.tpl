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

<h2><?php echo $this->translate('Mcard') ?></h2>
<div class="tabs">
	<ul class="navigation">
    <li class="active">
       <a href="<?php echo $this->seaddonsBaseUrl() .'/admin/mcard/settings/readme'?>" ><?php echo $this->translate('Please go through these important points and proceed by clicking the button at the bottom of this page.') ?></a>
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
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("Q : Membership Card is not being displayed for some Member Level or Profile Type created by the Site Admin. What should I do?");?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php echo $this->translate('You will need to configure the Membership Card Settings for this Member Level or Profile Type. Go to "Member Level Settings" in the Admin Panel of this plugin and configure the settings of Membership Card for the appropriate Member Level and Profile Type combination. This will resolve your problem.');?>
			</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Q : The CSS of this plugin is not coming on my site. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_2'>
				<?php echo $this->translate("Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
			</div>
		</li>
	</ul>
</div>
<br />
<button onclick="form_submit();"><?php echo $this->translate('Proceed to enter License Key') ?> </button>
	
<script type="text/javascript" >

function form_submit() {
	var url='<?php echo $this->url(array('module' => 'mcard', 'controller' => 'settings', 'action' => 'index'), 'admin_default', true) ?>';
	window.location.href=url;
}

</script>