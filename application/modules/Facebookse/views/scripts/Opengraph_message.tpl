<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _upgrade_messages.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
function dismiss_opengraph(modName) {
	document.cookie= modName + "_opengraphdismiss" + "=" + 1;
	$('dismiss_opengraphmodules').style.display = 'none';
}
</script>

<?php 
	$moduleName = Zend_Controller_Front::getInstance()->getRequest()->getModuleName(); 
	if( !isset($_COOKIE[$moduleName . '_opengraphdismiss']) ):
?>
<div id="dismiss_opengraphmodules">
	<div class="seaocore-notice">
		<div class="seaocore-notice-icon">
			<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
		</div>
<div style="float:right;">
	<button onclick="dismiss_opengraph('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
</div>
		<div class="seaocore-notice-text ">
			<?php echo "The Open Graph protocol enables you to integrate your Web pages into the social graph. Including Open Graph tags on your Web page, makes your Site's page equivalent to a Facebook Page. To enable Open Graph protocol implementation settings for the various content types on your site, please go to the 'Open Graph Settings' section of this plugin."; ?>
		</div>	
	</div>
</div>
<?php endif; ?>