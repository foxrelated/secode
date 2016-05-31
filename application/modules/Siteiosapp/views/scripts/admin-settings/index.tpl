<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    index.tpl 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
    <?php echo $this->translate('iOS Mobile Application - iPhone and iPad') ?>
</h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<?php include_once APPLICATION_PATH . '/application/modules/Siteapi/views/scripts/_web_view_message.tpl'; ?>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobileiosapp') || (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobileandroidapp') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobileapp'))): ?>
    <div class="seaocore_tip">
        <span>
            <?php echo 'You are still using old version of our App, which can be deleted now, please <a href="' . $this->url(array('action' => 'delete-existing-app'), 'admin_default', false) . '" class="smoothbox">click here</a> to know more.'; ?>
        </span>
    </div>
<?php endif; ?>

<div class="seaocore_settings_form">
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>