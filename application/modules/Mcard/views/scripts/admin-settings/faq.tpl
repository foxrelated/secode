<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if( count($this->navigation) ): ?>
	<div class='tabs'>
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
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("Q : Membership Card is not being displayed for some Member Level or Profile Type created by the Site Admin. What should I do?");?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php echo 'You will need to configure the Membership Card Settings for this Member Level or Profile Type. Go to "Member Level Settings" in the Admin Panel of this plugin and configure the settings of Membership Card for the appropriate Member Level and Profile Type combination. This will resolve your problem.'	?>
			</div>
		</li>	
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Q : The CSS of this plugin is not coming on my site. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_2'>
				<?php echo "Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."	?>
			</div>
		</li>	
	</ul>
</div>