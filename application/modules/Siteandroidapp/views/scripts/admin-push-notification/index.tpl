<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    index.tpl 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
    <?php echo $this->translate('Android Mobile Application') ?>
</h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<?php
$this->form->setDescription($this->translate('Below, you can choose to enable / disable Push Notifications for the various actions that occur on your social network. Push notifications are one of the best ways of increasing engagement in your app, and getting users to use your app actively.<br /><br />Note: To enable, manage, and disable individual push notifications, please refer to the <a href="%1$s"> Push Notification Type Settings page</a>.', $this->url(array('module' => 'siteandroidapp', 'controller' => 'push-notification', 'action' => 'types'), 'admin_default')));
$this->form->getDecorator('Description')->setOption('escape', false);
?>
<div class='settings sitemobileapp_notifications_form'>
    <?php echo $this->form->render($this); ?>
</div>
