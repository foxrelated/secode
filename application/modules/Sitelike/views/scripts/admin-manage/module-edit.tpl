<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: admodule-create.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Likes Plugin & Widgets') ?></h2>
<?php if( count($this->navigation) ): ?>
<div class='tabs'>
  <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
</div>
<?php endif; ?>
	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitelike', 'controller' => 'manage', 'action' => 'index'), $this->translate("Back to Manage Modules for Like"), array('class'=>'sitelike_icon_back buttonlink')) ?>
<br style="clear:both;" /><br />
<div class="seaocore_settings_form">
	<div class='settings'>
	<?php		echo $this->form->render($this); ?>
	</div>
</div>	
<style type="text/css">
.sitelike_icon_back{
	background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitelike/externals/images/back.png);
}
</style>