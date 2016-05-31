<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if( !empty($this->isModsSupport) ):
	foreach( $this->isModsSupport as $modName ) {
		echo $this->translate('<div class="tip"><span>Note: You do not have the latest version of the "%s". Please upgrade it to the latest version to enable its integration with Advanced Search Plugin.</span></div>', ucfirst($modName));
	}
endif;
?>

<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetheme')):?>
  <?php $getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitetheme');?>
  <?php $isModSupport = Engine_Api::_()->siteadvsearch()->checkVersion($getModVersion->version, '4.8.6');?>
  <?php if ($isModSupport < 0):?>
    <?php echo $this->translate('<div class="tip"><span>Note: You do not have the latest version of the "%s". Please upgrade it to the latest version to enable its integration with Advanced Search Plugin.</span></div>', ucfirst('Shopping Hub - a Social Commerce Theme'));?>
  <?php endif;?>
<?php endif;?>

<h2><?php echo "Advanced Search Plugin"; ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='clear seaocore_settings_form'>
	 <div class='settings'>
		  <?php echo $this->form->render($this); ?>
	 </div>
</div>
