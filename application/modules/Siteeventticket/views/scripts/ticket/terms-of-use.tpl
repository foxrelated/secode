<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: term-of-use.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="siteevent_dashboard_content">
    <?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
    <?php if (!empty($this->success)): ?>
        <ul class="form-notices" >
            <li>
                <?php echo $this->translate($this->success); ?>
            </li>
        </ul>
    <?php endif; ?>
    <div class="siteevent_editstyle">
        <?php echo $this->form->render($this); ?>
    </div>
</div>
</div>
