<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: post.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css'); ?>

<?php
include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/Adintegration.tpl';
?>

<div class="siteevent_view_top">
    <?php echo $this->htmlLink($this->siteevent->getHref(), $this->itemPhoto($this->siteevent, 'thumb.icon', '', array('align' => 'left'))) ?>
    <h2>	
        <?php echo $this->siteevent->__toString() ?>	
        <?php echo $this->translate('&raquo; '); ?>
        <?php echo $this->htmlLink($this->siteevent->getHref(array('tab' => $this->tab_selected_id)), $this->translate('Discussions')) ?>
        <?php echo $this->translate('&raquo; '); ?>
        <?php echo $this->topic->__toString() ?>
    </h2>
</div>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.addiscussionreply', 3) && $event_communityad_integration): ?>
    <div class="layout_right" id="communityad_post">
			<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.addiscussionreply', 3),"loaded_by_ajax"=>0,'widgetId'=>'event_addiscussionreply'));?>
    </div>
<?php endif; ?>

<div class="layout_middle">
    <?php if ($this->message) echo $this->message ?>
    <?php if ($this->form) echo $this->form->render($this) ?>
</div>