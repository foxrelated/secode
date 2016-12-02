<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: add.tpl 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  function setModuleName(module_name){
    window.location.href="<?php echo $this->url(array('module' => 'sitetagcheckin', 'controller' => 'content', 'action' => 'add'), 'admin_default', true) ?>/module_name/"+module_name;
  }
</script>

<h2><?php echo $this->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin') ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>

<?php //echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitetagcheckin', 'controller' => 'manage', 'action' => 'index'), $this->translate("Back to Manage Modules for Check-ins"), array('class' => 'sitetagcheckin_icon_back buttonlink')) ?>

<!--<br style="clear:both;" /><br />-->
<div class="seaocore_settings_form">
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<style type="text/css">
  .sitetagcheckin_icon_back{
    background-image: url(./application/modules/Sitetagcheckin/externals/images/back.png);
  }
</style>