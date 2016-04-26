<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: widget-settings.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<h2><?php echo $this->translate('Listings / Catalog Showcase Plugin'); ?></h2>

<?php if( count($this->navigation) ): ?>
	<div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
	</div>
<?php endif; ?>

<h3><?php echo $this->translate('Widget Settings'); ?></h3>
<?php echo $this->translate('Configure the settings for the various widgets available with this plugin.'); ?><br /><br />

<div class='tabs'>
	<ul class="navigation">
		<li class="active">
			<?php echo $this->htmlLink(array('module' => 'list', 'controller' => 'settings','action' => 'widget-settings'), $this->translate('General Settings'), array());?>
		</li>
		<li>
			<?php echo $this->htmlLink(array('module' => 'list', 'controller' => 'items', 'action' => 'manage'), $this->translate('Listing of the Day'), array()); ?>
		</li>
	</ul>
</div>

<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>