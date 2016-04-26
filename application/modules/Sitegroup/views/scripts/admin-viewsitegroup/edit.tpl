<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2 class="fleft"><?php echo $this->translate('Groups / Communities Plugin'); ?></h2>

<?php include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/manageExtensions.tpl'; ?>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/manageExtensions.tpl'; ?>

<div>
  <a href='<?php echo $this->url(array('module' => 'sitegroup', 'controller' => 'viewsitegroup'), 'admin_default', true) ?>' class="icon_sitegroup_admin_back buttonlink"><?php echo $this->translate('Back to Manage Groups') ?></a>
</div>
<br />
<div class="settings">
  <?php echo $this->form->render($this) ?>
</div>
<style type="text/css">
  #buttons-label{display:none;}
</style>