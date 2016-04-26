<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: post.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
  include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/Adintegration.tpl';
?>

<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitegroupdiscussion/externals/styles/style_sitegroupdiscussion.css')
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/payment_navigation_views.tpl'; ?>

<div class="sitegroup_viewgroups_head">
  <?php echo $this->htmlLink($this->sitegroup->getHref(), $this->itemPhoto($this->sitegroup, 'thumb.icon', '', array('align' => 'left'))) ?>
  <h2>
    <?php echo $this->sitegroup->__toString() ?>
    <?php echo $this->translate('&raquo; '); ?>
    <?php echo $this->htmlLink(array('route' => 'sitegroup_entry_view', 'group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($this->sitegroup->group_id), 'tab' => $this->tab_selected_id), $this->translate('Discussions')) ?>
    <?php echo $this->translate('&raquo; '); ?>
    <?php echo $this->topic->getTitle() ?>
  </h2>
</div>
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.addiscussionreply', 3) && $group_communityad_integration && Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup)): ?>
  <div class="layout_right" id="communityad_post">
    <?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.addiscussionreply', 3),"loaded_by_ajax"=>1,'widgetId'=>'group_post'))?>
  </div>
<?php endif; ?>
<div class="layout_middle">
  <?php if ($this->message)
    echo $this->message ?>
  <?php if ($this->form)
    echo $this->form->render($this) ?>
</div>