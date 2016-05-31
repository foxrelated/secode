<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    types.tpl 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
    <?php echo $this->translate('iOS Mobile Application - iPhone and iPad') ?>
</h2>

<script type="text/javascript">
    var pushnotificationTypeSettings = function (type) {
        window.location.href = en4.core.baseUrl + 'admin/siteiosapp/push-notification/types/type/' + type;
    }
</script>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<?php if ($this->countNotification): ?>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
<?php else: ?>
    <div class="tip">
        <span>
            No enable any Push Notification.
        </span>
    </div>
<?php endif; ?>
