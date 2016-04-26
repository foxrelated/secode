<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: meta-detail.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php echo $this->partial('application/modules/Siteevent/views/sitemobile/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
<div class="siteevent_dashboard_content">
    <?php echo $this->form->render($this); ?>
</div>