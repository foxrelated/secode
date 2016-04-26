<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: appconfig.tpl 2011-04-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if( count($this->navigation) ): ?>
	<div class='tabs'>
		<?php	echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
	</div>
<?php endif; ?>

<div class='clear'>
	<a href="<?php echo $this->url(array('module' => 'suggestion', 'controller' => 'global'), 'suggestion_global_global', true) ?>" class="buttonlink" style="background-image:url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Suggestion/externals/images/back.png);padding-left:23px;">Back to Global Settings</a>
  <div class='settings' style="margin-top:15px;">
		<h3><?php echo $this->translate("Guidelines to configure the applications for Contact Importer Settings") ?></h3>
		<p><?php echo $this->translate('Please click on the application names given below and follow the corresponding steps.') ?></p><br />
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="guideline_show('guideline_2');"><?php echo $this->translate("Yahoo Application");?></a>
					<div class='faq' style='display: none;' id='guideline_2'>
						<?php echo $this->translate('Getting Yahoo API Key :-');?>
												
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('yahoostep-1');"><?php echo $this->translate("Step 1") ?></a>
							<div id="yahoostep-1" style='display: none;'>
								<p>
								<?php echo $this->translate("Go here to register your application:");?> <a href="https://developer.apps.yahoo.com/dashboard/createKey.html" target="_blank" style="color:#5BA1CD;">https://developer.apps.yahoo.com/dashboard/createKey.html</a><br />
								
								<div style="font-weight:bold;"><?php echo $this->translate("About creating your application :-");?></div>
       		
								<?php echo $this->translate("a) Write your application name. You can make this the same as your site name.");?><br />
								<?php echo $this->translate("b) In the 'Kind of Application' field, choose : 'Web-based'.");?><br />
								<?php echo $this->translate("c) Write a short description about your application/website.");?><br />
								<?php echo $this->translate("d) In the 'Application URL' field, write the URL of your site (example : http://www.mysite.com).");?><br />
								
		            <div style="font-weight:bold;"><?php echo $this->translate("Security &amp; Privacy:-");?></div>
		            <?php echo $this->translate("a) In the 'Application Domain' field, write your site's domain (example : http://www.mysite.com).");?><br />
								<?php echo $this->translate("b) Choose the 'Access Scopes' field to : 'This app requires access to private user data.'. Selecting this option will give you a list of Yahoo services. Among them, choose 'Yahoo! Contacts' in 'Read' mode.");?><br />
								<?php echo $this->translate("c) Check on the 'Terms of Use' checkbox.");?><br />
								<?php echo $this->translate("d) Now click on the 'Get API Key' button. After clicking on this button you will be redirected to a page where it would ask you to verify your domain. So, verify your domain.");?><br />
								
								
								</p>
								<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/yahst1.gif" alt="Step 1" />
							</div>
						</div>
						
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('yahoostep-2');"><?php echo $this->translate("Step 2") ?></a>
							<div id="yahoostep-2" style='display: none;'>
								<p>
								   <?php echo $this->translate("a) Verify your domain.");?><br />
								   <?php echo $this->translate("b) After verifying your domain successfully, you will be redirected to a success page where you will get your 'API Key' and 'Shared Secret Key'. Copy them and paste these values in your site's Yahoo contact importer settings fields.");?><br />
								
								</p>
								<img src="https://lh4.googleusercontent.com/_nEoXA-sO-_M/TZxrEpPl3HI/AAAAAAAAABY/T7z5wR0Hh7c/yahst2.gif" alt="Step 2" />
							</div>
						</div>
						
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('yahoostep-3');"><?php echo $this->translate("Step 3") ?></a>
							<div id="yahoostep-3" style='display: none;'>
								<p><?php echo $this->translate("Copy below 'Consumer Key (API Key)' and 'Consumer Secret (Shared Secret Key)' and paste these values in your site's Yahoo contact importer settings fields.");?><br /></p>
								<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/yahst3.gif" alt="Step 3" />
							</div>
						</div>

					</div>
				</li>
				
			<li id="google-config">
					<a href="javascript:void(0);" onClick="guideline_show('guideline_3');"><?php echo $this->translate("Google Application");?></a>
					<div class='faq' style='display: none;' id='guideline_3'>
						<?php echo $this->translate('Getting Google Client ID:-');?>
												
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('googlestep-1');"><?php echo $this->translate("Step 1") ?></a>
							<div id="googlestep-1" style='display: none;'>
								<p>
								
								<?php echo $this->translate("To start using Google APIs console go here :");?> <a href="https://code.google.com/apis/console" target="_blank" style="color:#5BA1CD;">https://code.google.com/apis/console</a><br />
								
								
								 <?php echo $this->translate("Login to your google account and click on the 'Create project...' to select API access.");?><br />
								
								</p>
								<img src="https://lh4.googleusercontent.com/-BXrw5T8QqxM/Tx7LjkmjgaI/AAAAAAAAAI0/nmJ6lzqUk_Y/s912/step1.jpg" alt="Step 1" />
							</div>
						</div>
						
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('googlestep-2');"><?php echo $this->translate("Step 2") ?></a>
							<div id="googlestep-2" style='display: none;'>
								<p>
									 <?php echo $this->translate("Select 'API Access' tab from left menu and click on 'Create an OAuth 2.0 Client' button. After clicking on this button a popup will open where you will be asked to enter a 'Product name'. So enter your product name.");?><br />
								
								</p>
								<img src="https://lh5.googleusercontent.com/-2Qls_S7ZeaU/Tx7LjXpZGgI/AAAAAAAAAI4/Ra4S28cmdOQ/s912/step2.jpg" alt="Step 2" />
							</div>
						</div>
						
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('googlestep-3');"><?php echo $this->translate("Step 3") ?></a>
							<div id="googlestep-3" style='display: none;'>
								<p>
									
									<?php echo $this->translate("Enter your 'Product name'. This product name will be displayed to users while authenticating. You may also upload a logo within mentioned dimensions. Now click on the 'Next' button at bottom to enter 'Authorized Redirect URLs' and 'Authorized JavaScript Origins' of your site.");?><br />
								
								</p>
								<img src="https://lh6.googleusercontent.com/-e8gdSFWmikM/Tx7DElP9elI/AAAAAAAAAH8/weRVtEqp05w/s573/Step%2525203.jpg" alt="Step 3" />
							</div>
						</div>
						
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('googlestep-4');"><?php echo $this->translate("Step 4") ?></a>
							<div id="googlestep-4" style='display: none;'>
								<p>
									<?php echo $this->translate("Select the 'Web application' Application Type and enter the below mentioned 'Authorized Redirect URLs' and 'Authorized JavaScript Origins' and click on 'Create client ID' button to get your 'Client ID'");?><br />
									<?php echo  '<b>' . $this->translate("1) Authorized Redirec URLs") . ' => ' . ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->url(array(), 'friends_suggestions_viewall', true) . '</b>' ;?><br />
									 
									<?php echo '<b>' . $this->translate("2) Authorized JavaScript Origins") . ' => ' . ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST']. '</b>' ;?><br />
								
								</p>
								<img src="https://lh5.googleusercontent.com/--vclqbqdA68/Tx7DFbl162I/AAAAAAAAAII/b9_xh8GhrDM/s512/Step%2525204.jpg" alt="Step 4" />
							</div>
						</div>
						
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('googlestep-5');"><?php echo $this->translate("Step 5") ?></a>
							<div id="googlestep-5" style='display: none;'>
								<p>
								   <?php echo $this->translate("Copy below 'Client ID' and paste this value in your site's Gmail contact importer settings field.");?><br />
								</p>
								<img src="https://lh6.googleusercontent.com/-z0vE1EfIKq8/Tx7DEVrOdqI/AAAAAAAAAH4/52t3swk5m80/s743/Step%2525205.jpg" alt="Step 5" />
							</div>
						</div>

					</div>
				</li>
				
				<li>
					<a href="javascript:void(0);" onClick="guideline_show('guideline_4');"><?php echo $this->translate("Windows Live Application");?></a>
					<div class='faq' style='display: none;' id='guideline_4'>
						<?php echo $this->translate('Getting your Windows Live Application ID :-');?>
												
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('msnstep-1');"><?php echo $this->translate("Step 1") ?></a>
							<div id="msnstep-1" style='display: none;'>
								<p><?php echo $this->translate("To register your application, go here :");?> <a href="http://msdn.microsoft.com/en-us/library/cc287659.aspx" target="_blank" style="color:#5BA1CD;">http://msdn.microsoft.com/en-us/library/cc287659.aspx.</a><br /><br />
     			<?php echo $this->translate("Click on the 'Windows Live application management site' in the 'Registering Your Application' section.");?><br />
     </p>
								<img src="https://lh5.googleusercontent.com/_nEoXA-sO-_M/TZxrHAY0KxI/AAAAAAAAACA/_sCm06Mf8Vs/s720/msnst1.gif" alt="Step 1" />
							</div>
						</div>
						
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('msnstep-2');"><?php echo $this->translate("Step 2") ?></a>
							<div id="msnstep-2" style='display: none;'>
								<p>
									<?php echo $this->translate("Click on the 'Add an application' link.");?><br/>
								</p>
								<img src="https://lh6.googleusercontent.com/_nEoXA-sO-_M/TZxrG25O-tI/AAAAAAAAACI/GuXBM-M6Uy0/msnst2.gif" alt="Step 2" />
							</div>
						</div>
						
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('msnstep-3');"><?php echo $this->translate("Step 3") ?></a>
							<div id="msnstep-3" style='display: none;'>
								<p>
								   <?php echo $this->translate("a) Write your application name. You can make this the same as your site name.");?><br/>
								   <?php echo $this->translate("b) In the 'Application type' field, choose : 'Web application'.");?><br/>
								   <?php echo $this->translate("c) In the 'Domain' field, write your site's domain (example : www.mysite.com).");?><br/>
								  
								</p>
								<img src="https://lh3.googleusercontent.com/_nEoXA-sO-_M/TZxrGqxE8GI/AAAAAAAAAB8/qgJc3o1QZ3w/msnst3.gif" alt="Step 3" />
							</div>
						</div>
						
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('msnstep-4');"><?php echo $this->translate("Step 4") ?></a>
							<div id="msnstep-4" style='display: none;'>
								<p>
										<?php echo $this->translate("Click on the 'Essentials' link.");?><br/>
								</p>
								<img src="https://lh6.googleusercontent.com/_nEoXA-sO-_M/TZxrGdQUa5I/AAAAAAAAABw/7zhBSudu_xo/msnst4.gif" alt="Step 4" />
							</div>
						</div>
						
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('msnstep-5');"><?php echo $this->translate("Step 5") ?></a>
							<div id="msnstep-5" style='display: none;'>
								<p>
								
									<?php echo $this->translate("For verifying your domain, click on the 'Verify now' link.");?><br/>
								</p>
								<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/msnst5.gif" alt="Step 5" />
							</div>
						</div>
						
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('msnstep-6');"><?php echo $this->translate("Step 6") ?></a>
							<div id="msnstep-6" style='display: none;'>
								<p>
									<?php echo $this->translate("Verify your domain. Follow the instructions given on this page for verifying your domain.");?><br/>
								
								</p>
								<img src="https://lh3.googleusercontent.com/_nEoXA-sO-_M/TZxrF_HHayI/AAAAAAAAABo/t3jzzj_gZvk/msnst6.gif" alt="Step 6" />
							</div>
						</div>
						
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('msnstep-7');"><?php echo $this->translate("Step 7") ?></a>
							<div id="msnstep-7" style='display: none;'>
								<p>
									<?php echo $this->translate("After verifying your domain successfully, you will be redirected to the below success page where you will get your 'Client ID' and 'Secret Key'. Copy them and paste these values in your site's Windows Live contact importer settings fields.");?><br/>
								</p>
								<img src="https://lh6.googleusercontent.com/_nEoXA-sO-_M/TZxrFYGQMFI/AAAAAAAAABk/KgAdqm8qJdo/s800/msnst7.gif" alt="Step 7" />
							</div>
						</div>
						
					</div>
				</li>
				
				<li>
					<a href="javascript:void(0);" onClick="guideline_show('guideline_5');"><?php echo $this->translate("Facebook Application");?></a>
					<div class='faq' style='display: none;' id='guideline_5'>
					  <?php echo $this->translate('Getting Facebook API Key :-');?>
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('fbstep-1');"><?php echo $this->translate("Step 1");?></a>
							<div id="fbstep-1" style='display: none;'>
								<p>
                   <?php echo $this->translate("a) Go to this URL: ");?><a href="http://www.facebook.com/developers/#!/developers/apps.php" target="_blank" style="color:#5BA1CD;">http://www.facebook.com/developers/#!/developers/apps.php</a><br/>
                    <?php echo $this->translate("b) Click on the 'Set Up New App' button to start creating your application.");?><br />
                  <?php echo $this->translate("c) In the 'Create Application' section, enter the name of your application in the 'App Name' field and click on 'Create App' button to move to the next step.");?><br />
                </p>
								<img src="https://lh6.googleusercontent.com/_nEoXA-sO-_M/TZxqm-Qe8uI/AAAAAAAAABU/nQZiyDJ3TuU/fbst1.gif" alt="Step 1" />
							</div>
						</div>	

						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('fbstep-2');"><?php echo $this->translate("Step 2");?></a>
							<div id="fbstep-2" style='display: none;'>
								<p><?php echo $this->translate("Enter the 'Security Check' words in the text box and click on 'Submit' button.");?><br /></p>
								<img src="https://lh6.googleusercontent.com/_nEoXA-sO-_M/TZxrI3ISY4I/AAAAAAAAACo/JVTelFR3vzQ/fbst2.gif" alt="Step 2" />
							</div>
						</div>	

						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('fbstep-3');"><?php echo $this->translate("Step 3");?></a>
							<div id="fbstep-3" style='display: none;'>
								<p><?php echo $this->translate("Fill the details in the 'About' section and save them. We recommend you to upload an icon and logo for
your application for better identification of your site on Facebook.");?></p>
								<img src="https://lh6.googleusercontent.com/_nEoXA-sO-_M/TZxrIoOrF_I/AAAAAAAAACk/ftYmHQRl3o8/fbst3.gif" alt="Step 3" />
							</div>
						</div>	

						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('fbstep-4');"><?php echo $this->translate("Step 4");?></a>
							<div id="fbstep-4" style='display: none;'>
								<p>
                  <?php echo $this->translate("Fill the details of your website in the 'Web Site' section, and then click on 'Save Changes' to save them.");?><br />
                  <?php echo $this->translate("a) Enter your site's url in the 'Site Url' field.");?><br />
                  <?php echo $this->translate("b) Enter your site's domain in the 'Site Domain' field.");?><br />
                                </p>
								<img src="https://lh3.googleusercontent.com/_nEoXA-sO-_M/TamhfhojD-I/AAAAAAAAAC0/NazPWPFXPIM/fbst4.gif" alt="Step 4" />
							</div>
						</div>	
						
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('fbstep-5');"><?php echo $this->translate("Step 5");?></a>
							<div id="fbstep-5" style='display: none;'>
								<p>
<?php echo $this->translate("After providing all the details, you will be redirected to the below success page where you will get your 'App ID' and
'App Secret'. Copy them and paste these values in your site's 'Facebook App ID' and 'Facebook App Secret' field at (Admin => Settings => Facebook
integration) page.");?><br /></p>
								<img src="https://lh6.googleusercontent.com/_nEoXA-sO-_M/TZxrISjAb9I/AAAAAAAAACc/f0hRqpvMKFk/s576/fbst5.gif" alt="Step 5" />
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
	border-left:3px solid #ccc;
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
  window.addEvent('domready', function () { 
  
    <?php if (!empty($_GET['app']) && $_GET['app'] == 'google-config'): ?>
    guideline_show('guideline_3');
    guideline_show('googlestep-1');
   <?php endif;?>   
  });

 
  function guideline_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
</script>