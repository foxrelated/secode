<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2010-10-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate("Membership Cards Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>
<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_upgrade_messages.tpl'; ?>
<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
