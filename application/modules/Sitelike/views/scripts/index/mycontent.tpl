<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: mycontent.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/infotooltip.tpl'; ?>
<?php $this->urlAction = 'mycontent';

//THIS FILE USE FOR SUGGESTION LINK SHOW ON THE "MY CONTENT OR MY LIKES" TAB.
include_once APPLICATION_PATH . '/application/modules/Sitelike/views/scripts/content_friend_mylike.tpl'; ?>
