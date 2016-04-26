<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: readme.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Suggestions / Recommendations Plugin') ?></h2>
<div class="tabs">
	<ul class="navigation">
		<li class="active">
			<a href="<?php echo Zend_Controller_Front::getInstance()->getBaseUrl() .'/admin/suggestion/global/readme'?>" ><?php echo $this->translate('Please go through these important points and proceed by clicking the button at the bottom of this page.') ?></a>
	
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
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("Q. I am getting the error : 'failed to open stream: HTTP request failed!' while trying to upload csv/text file content; what should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php 	$link_phpinfo = "<a href='" . Zend_Controller_Front::getInstance()->getBaseUrl() . "/admin/system/php' 	style='color:#5BA1CD;'>http://" . 			$_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . "/admin/system/php</a>";
					echo $this->translate("Ans : You should ask your server administrator to check your server's php.ini PHP configuration file for 'allow_url_fopen' to be 'on' and 'user_agent' to have some values listed in it.It should be listed here. %s This should resolve the issue.",$link_phpinfo);?>
			</div>
		</li>	
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("Q.The CSS of this plugin is not coming on my site. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_3'>
				<?php echo $this->translate("Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
			</div>
		</li>

		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("Q: I have a 3rd party content module installed on my website. How can I integrate it with this plugin such that suggestions of its content are displayed ? ");?></a>
			<div class='faq' style='display: none;' id='faq_4'>
				<?php echo $this->translate("You can configure such an integration for any 3rd party content plugin from the Manage Modules section of this plugin. ");?>
			</div>
		</li>
	</ul>
</div>
<br />
<button onclick="form_submit();"><?php echo $this->translate('Proceed to enter License Key') ?> </button>
	
<script type="text/javascript" >

function form_submit() {
	
	var url='<?php echo $this->url(array('module' => 'suggestion', 'controller' => 'global', 'action' => 'global'), 'admin_default', true) ?>';
	window.location.href=url;
}

</script>