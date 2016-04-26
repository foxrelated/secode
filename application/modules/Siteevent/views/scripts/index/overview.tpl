<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: overview.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
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
    <?php echo $this->form->render($this); ?>

    <script type="text/javascript">
        var catdiv1 = $('overview-label');
        var catdiv2 = $('save-label');
        var catarea1 = catdiv1.parentNode;
        catarea1.removeChild(catdiv1);
        var catarea2 = catdiv2.parentNode;
        catarea2.removeChild(catdiv2);
    </script>
</div>
</div>