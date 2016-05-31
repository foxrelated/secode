<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    faq_help.tpl 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
    function faq_show(id) {
        if ($(id)) {
            if ($(id).style.display == 'block') {
                $(id).style.display = 'none';
            } else {
                $(id).style.display = 'block';
            }
        }
    }
<?php if ($this->faq_id): ?>
        window.addEvent('domready', function () {
            faq_show('<?php echo $this->faq_id; ?>');
        });
<?php endif; ?>
</script>

<?php $i = 1; ?>
<?php
$GlobalTabUrl = $this->url(array('controller' => 'settings', 'action' => 'index'), 'admin_default', false);
$pushNotificationTabUrl = $this->url(array('controller' => 'push-notification', 'action' => 'index'), 'admin_default', false);
?>
<div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files seaocore_faq">
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('What should i do to configure push notifications with my android app?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('To configure push notification to your android app follow the below steps:'); ?>
                    <br />
                    <?php echo $this->translate('Step 1: Generate <a href="https://developers.google.com/cloud-messaging/" target="_blank">Google API Key</a> for your android app and enter it in <a href="' . $GlobalTabUrl . '">Global Settings</a> form.'); ?>
                    <br />
                    <?php echo $this->translate('Step 2: Send "device_uuid" and "registration_id" to your android device with login and signup api call.'); ?><br />
                    <?php echo $this->translate('Step 3: Congratulation !! push notification has been configured for your android app. You may also enable / disable these notifications from <a href="' . $pushNotificationTabUrl . '">Push Notification Settings</a>.'); ?>
                </div>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('I am not getting any notification. What might be the reason?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate("In this case you should follow the below steps:"); ?><br />
                    <?php echo $this->translate('Step 1: Check whether the respective notification is enable or not from <a href="' . $pushNotificationTabUrl . '">Push Notification Settings</a> page.'); ?><br />
                    <?php echo $this->translate('Step 2: It might be that your generated "Google API Key" is not valid.'); ?><br />
                    <?php echo $this->translate('Step 3: It might be that you have not sent "device_uuid" or "registration_id" with login or signup api call.'); ?><br />
                    <?php echo $this->translate('Step 4: It might be that your sent "device_uuid" and "registration_id" is not valid.'); ?>
                </div>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('Should i send any parameters, while user login or signup?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('Yes, you need to send "device_uuid" and "registration_id" with login or signup api call.'); ?>
                </div>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('Should i send any parameters, while user logout?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('Yes, you need to send "device_uuid" with logout api call.'); ?>
                </div>
            </div>
        </li>

    </ul>
</div>