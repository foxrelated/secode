<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: module-edit.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Suggestions / Recommendations Plugin') ?></h2>
<?php if (count($this->navigation)): ?>
  <div class='tabs'>
  <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
</div>
<?php endif; ?>
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'suggestion', 'controller' => 'settings', 'action' => 'manage-module'), $this->translate("Back to Manage Modules for Suggestion"), array('class' => 'suggestion_icon_back buttonlink')) ?>
  <br style="clear:both;" /><br />
  <div class="seaocore_settings_form">
    <div class='settings'>
	<?php 
	  $this->form->setTitle($this->translate('Edit Module for Suggestions'));

	  $this->form->setDescription($this->translate('Use the form below to configure content from a module of your site to be displayed in various Suggestion widgets. For the chosen content module, enter the various database table related field names. In case of doubts regarding any field name, please contact the developer of that content module.')); 
	  $this->form->getDecorator('Description')->setOption('escape', false);

	  if( !empty($this->form->link) ) {
	    $suggFriendLink = $this->translate("Do you want to show 'Suggest to Friends' link to users?");
	    $this->form->link->setDescription($suggFriendLink);
	    $this->form->link->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
	  }
	?>
	<?php echo $this->form->render($this); ?>
  </div>
</div>	
<style type="text/css">
  .suggestion_icon_back{
	background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/back.png);
  }
</style>