<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/pluginLink.tpl'; ?>
<h2 class="fleft"><?php echo $this->translate('Groups / Communities Plugin'); ?></h2>

<?php if (count($this->navigationGroup)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigationGroup)->render()
  ?>
  </div>
<?php endif; ?>

<h2><?php //echo $this->translate('Groups - Albums Extension') ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitegroupalbum/views/scripts/admin-settings/faq_help.tpl'; ?>
