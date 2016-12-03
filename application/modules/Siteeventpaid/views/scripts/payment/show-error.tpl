<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: show-error.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (!Engine_Api::_()->seaocore()->checkModuleNameAndNavigation()): ?>
    <?php if (empty($this->hideNavigation)): ?>
        <?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/navigation_views.tpl'; ?>
    <?php endif; ?>
<?php endif; ?>

<ul class="form-errors">
    <li>
        <?php if (!empty($this->show)): ?>
            <?php echo $this->translate("There are currently no enabled payment gateways. Please contact the site admin to get this issue resolved."); ?>
        <?php else: ?>
            <?php echo $this->translate("There are currently no paid packages available of the site. Please upgrade your package."); ?>
        <?php endif; ?>
    </li>
</ul>