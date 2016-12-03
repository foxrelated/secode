<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _navigationAdmin.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>
<?php if (count($this->navigationStore)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigationStore)->render()
  ?>
  </div>
<?php endif; ?>

<div class='tabs'>
  <ul class="navigation">
    <li class="active">
    <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestorereview','controller'=>'settings','action'=>'index'), $this->translate('Stores'), array())
    ?>
    </li>
    <li>
    <?php
      echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestoreproduct','controller'=>'review','action'=>'index'), $this->translate('Products'), array())
    ?>
    </li>			
  </ul>
</div>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>