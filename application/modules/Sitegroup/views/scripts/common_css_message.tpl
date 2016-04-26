<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitebusiness
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: common_style_css.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $moduleName = 'sitegroup';
	if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness')) {
		$message = $this->translate('You also have the "Directory / Pages Plugin" and "Directory / Businesses Plugin" installed on your website. If you want a common CSS loaded on your website for the "Directory / Pages Plugin" and "Groups / Communities Plugin" and "Directory / Businesses Plugin", then please enable the "Common CSS" field from the Global Settings of this plugin. See the "Common CSS" field for benefits of enabling it.');
	}
	elseif(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')) { 
		$message = $this->translate('You also have the "Directory / Pages Plugin" installed on your website. If you want a common CSS loaded on your website for the "Directory / Pages Plugin" and "Groups / Communities Plugin", then please enable the "Common CSS" field from the Global Settings of this plugin. See the "Common CSS" field for benefits of enabling it.');
  }
	elseif(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness')) { 
		$message = $this->translate('You also have the "Directory / Businesses Plugin" installed on your website. If you want a common CSS loaded on your website for the "Directory / Businesses Plugin" and "Groups / Communities Plugin", then please enable the "Common CSS" field from the Global Settings of this plugin. See the "Common CSS" field for benefits of enabling it.');
  } ?>
<?php if(!empty($message)) : ?>
	<?php if( !isset($_COOKIE[$moduleName . '_dismiss_group'])): ?>
		<div id="dismiss_modules_group">
			<div class="seaocore-notice">
				<div class="seaocore-notice-icon">
					<img src="./application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
				</div>
				<div style="float:right;">
					<button onclick="dismiss1('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
				</div>
				<div class="seaocore-notice-text ">
					<?php echo $message; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
<?php endif; ?>