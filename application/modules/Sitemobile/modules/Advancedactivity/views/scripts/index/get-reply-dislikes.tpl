<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-reply-dislikes.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (Engine_Api::_()->seaocore()->checkEnabledNestedComment('advancedactivity')):?>
    <?php 
      include APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/get-reply-dislikes.tpl';
    ?> 
<?php endif;?>