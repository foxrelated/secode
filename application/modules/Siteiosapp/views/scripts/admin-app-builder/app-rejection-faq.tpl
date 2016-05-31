<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    app-rejection-faq.tpl 2015-10-01 00:00:00Z SocialEngineAddOns $
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
<div class="admin_seaocore_files_wrapper" style="width: 800px;">
    <ul class="admin_seaocore_files seaocore_faq">
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('There should be proper content in `Terms of Services` and `Privacy` sections.'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('iOS says that:'); ?>
                    <br />
                    a. Apps should have all included URLs fully functional when you submit it for review, such as support and privacy policy URLs. <br />
                    b. Apps that include account registration or access a user’s existing account must include a privacy policy or they will be rejected. <br />
                    So, `Terms of Services` and `Privacy` section should have proper content in it, to make your App submission a success.
                </div>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('Profile fields, which are coming in signup forms should not be required.'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    Except the First Name and Last Name, none of the fields should be required because “Apps that require users to share personal information, such as email address and date of birth, in order to function will be rejected”.
                </div>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('Playing Music in Background should be disabled.'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    In iOS words “Multitasking Apps may only use background services for their intended purposes: VoIP, audio playback, location, task completion, local notifications, etc.” else the App will be rejected.
                </div>
            </div>
        </li>        
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('Your website should not have any offensive content.'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    Which states that site content should not support nudity or pronography.
                </div>
            </div>
        </li>
    </ul>
</div>
<div style="float: right; padding-top: 5px;">
    <button onclick="parent.Smoothbox.close();">Close</button>
</div>