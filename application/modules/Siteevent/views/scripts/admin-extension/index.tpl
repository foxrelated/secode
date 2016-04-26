<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Advanced Events Plugin'); ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate('Extensions for Advanced Events Plugin'); ?></h3>
<?php echo $this->translate(''); ?>
<div class='tabs'>
  <ul class="navigation">
    <li>
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteevent', 'controller' => 'extension', 'action' => 'upgrade'), $this->translate('Extension Upgrade'), array()) ?>
    </li>
    <li >
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteevent', 'controller' => 'extension', 'action' => 'information'), $this->translate('Extension Information'), array()) ?>
    </li>
  </ul>
</div>
<div class='clear siteevent_settings_form'>
  <div class='settings'>
    <?php //echo $this->form->render($this) ?>
  </div>
</div>

<?php echo $this->content()->renderWidget('siteevent.extension-show') ?>