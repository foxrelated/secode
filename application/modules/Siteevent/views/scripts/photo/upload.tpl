<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/Adintegration.tpl';
?>

<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent_dashboard.css');
?>

<?php if ($this->can_edit): ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>
<?php else: ?>
    <div class="siteevent_view_top">
        <?php echo $this->htmlLink($this->siteevent->getHref(), $this->itemPhoto($this->siteevent, 'thumb.icon', '', array('align' => 'left'))) ?>
        <h2>	
            <?php echo $this->siteevent->__toString() ?>	
            <?php echo $this->translate('&raquo; '); ?>
            <?php echo $this->htmlLink($this->siteevent->getHref(array('tab' => $this->tab_id)), $this->translate('Photos')) ?>
        </h2>
    </div>
    <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.adalbumcreate', 3) && $event_communityad_integration): ?>
        <div class="layout_right" id="communityad_albumcreate">
					<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.adalbumcreate', 3),"loaded_by_ajax"=>0,'widgetId'=>'event_albumcreate'))?>
        </div>
        <div class="layout_middle">
        <?php endif; ?>
    <?php endif; ?>

    <div class="siteevent_dashboard_content">
        <?php if ($this->can_edit): ?>
            <?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
        <?php endif; ?>
        <?php echo $this->form->render($this) ?>
        <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.adalbumcreate', 3) && $event_communityad_integration): ?>
        </div>
    <?php endif; ?>
</div>
<?php if ($this->can_edit): ?>
    </div>
<?php endif; ?>