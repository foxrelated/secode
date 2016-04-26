<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: settings_css.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$sitelikeButtonLikeUpdatefile = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelike.button.likeupdatefile', 1);
//     if (empty($sitelikeButtonLikeUpdatefile)) {
//      
// 			$this->headLink()
//         ->prependStylesheet(Zend_Controller_Front::getInstance()->getBaseUrl() . '/sitelike/index/likesettingcss');
// 		}	else {
// 			$this->headLink()
// 			->appendStylesheet($this->layout()->staticBaseUrl
//                    . 'application/modules/Sitelike/externals/styles/likesettings.css');
// 		}
?>
