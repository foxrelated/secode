<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Advanced Facebook Integration / Likes, Social Plugins and Open Graph');?></h2>
<?php if( count($this->navigation) ): ?>
<div class='tabs facebookse_tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>


<div class='clear'>
	<a href="<?php echo $this->url(array('module' => 'facebookse', 'controller' => 'settings'), 'admin_default', true) ?>" class="buttonlink"
style="background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Facebookse/externals/images/back.png);padding-left:23px;">
    <?php echo $this->translate("Back to Global Settings") ?></a>
  <div class='settings' style="margin-top:15px;">
		<h3><?php echo $this->translate("Guidelines to configure Facebook Application") ?></h3>
		<p><?php echo $this->translate('Please follow the steps given below.') ?></p><br />

		<div class="admin_files_wrapper" style="width:930px;">
			<ul class="admin_files guidelines_faq" style="max-height:none;">
				<li>
					<div class='faq' style='display: block;' id='guideline_1'>
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('fbstep-1');"><?php echo $this->translate("Step 1");?></a>
							<div id="fbstep-1" style='display: none;'>
								<p>
                   <?php echo $this->translate("Go to this URL for steps to configure basic Facebook Integration on your SocialEngine website : ");?><a href="http://www.google.com/url?q=http%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DlyfHc6jMvZc%26list%3DUUlUy2ac9Xb-Bw95e1EyKlEQ%26index%3D3%26feature%3Dplcp" target="_blank" style="color:#5BA1CD;">http://www.google.com/url?q=http%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DlyfHc6jMvZc%26list%3DUUlUy2ac9Xb-Bw95e1EyKlEQ%26index%3D3%26feature%3Dplcp</a><br/>
                    
                </p>
							</div>
						</div>	

						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('fbstep-2');"><?php echo $this->translate("Step 2");?></a>
							<div id="fbstep-2" style='display: none;'>
								<p><?php echo $this->translate("1) Go to this URL: ");?><a href="https://developers.facebook.com/apps" target="_blank" style="color:#5BA1CD;">https://developers.facebook.com/apps </a><?php echo $this->translate("and click on 'Edit App' button to configure your application created using above step.");?> <br />
								<?php echo $this->translate("2) Fill the basic settings about your website in the 'Basic' section and then click on 'Save Changes' to save them.");?> <br />
								
								<?php echo $this->translate("a) Enter your site's domain ('" . $_SERVER['HTTP_HOST'] . "') in the 'App Domain' field in the 'Basic Info' section.");?> <br />
								
								<?php echo $this->translate("b) Enter your site's url ('" .( _ENGINE_SSL ? 'https://' : 'http://' )
                    . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . "/') in the 'Site URL' field in the 'Website' section.");?> <br />
                    
                 <?php echo $this->translate("c) Enter your site's canvas url ('http://" . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . "') in the 'Canvas URL' field in the 'App on facebook' section.");?> <br />
                 
                 <?php echo $this->translate("d) If your site runs on https, then enter your site's secure canvas url  ('https://" . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . "/') in the 'Secure Canvas URL' field in the 'App on facebook' section.");?> <br />   
   
								
								</p>
								<img src="https://lh3.googleusercontent.com/-j6cPDFeoJ6w/T31CITgweGI/AAAAAAAAAJc/FXQiYGwOpUQ/s640/2.jpg" alt="Step 2" />
							</div>
						</div>	

						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('fbstep-3');"><?php echo $this->translate("Step 3");?></a>
							<div id="fbstep-3" style='display: none;'>
								<p><?php echo $this->translate("Go to the 'Advanced' section from the left panel of the page and make sure that the field 'Enhanced Auth Dialog' is enabled and 'Deprecate offline access' is disabled.");?></p>
								<img src="https://lh6.googleusercontent.com/_nEoXA-sO-_M/TZxrIoOrF_I/AAAAAAAAACk/ftYmHQRl3o8/fbst3.gif" alt="Step 3" />
							</div>
						</div>	

						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('fbstep-4');"><?php echo $this->translate("Step 4");?></a>
							<div id="fbstep-4" style='display: none;'>
								<p>
                  <?php echo $this->translate("Now, go to the 'Auth Dialog' section from the left panel of the page and enable 'Authenticated Referrals' from the 'Configure how Facebook refers users to your ap' section and then click on 'Save Changes' to save changes. Upload a logo for your site from the 'Logo' field in the 'Customize' section.");?><br />
                </p>
								<img src="https://lh6.googleusercontent.com/-ra3S7_SUZWU/T31CJW2XYCI/AAAAAAAAAJg/Ovi6IH-lhxA/s700/3.jpg" alt="Step 4" />
							</div>
						</div>	
					</div>
				</li>
			</ul>
		</div>
	</div>
</div>	


<style type="text/css">
.settings .form-wrapper {
	width:650px;
}   
.settings .form-element{
	max-width:400px;
}
.form-sub-heading{
	clear:both;
}
/*css for help*/
.guidelines_faq li > div{
	clear:both;
	padding:5px 5px 5px 10px;
	margin:5px;
	font-family:arial;
	line:height:18px;
}
.guidelines_faq li > a{font-weight:bold;}
.guidelines_faq li div b{font-weight:bold;}
.guidelines_faq li div p{clear:both;margin:5px 0;}
.code{ background-color:#F9F6D5;padding:10px;color:#000;font-family: "Trebuchet MS",tahoma,verdana,arial,serif;}
.steps{border-bottom-width:1px;padding:5px;clear:both;overflow: auto;}
.steps > a{font-weight:bold;margin-bottom:5px;float:left;}
.steps > a + div{ background-color:#F9F6D5;padding:10px;color:#000;clear:both;}
</style>
<script type="text/javascript">
  function guideline_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
</script>