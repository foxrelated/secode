<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
window.addEvent('domready', function() {
	  if($('show_manual-wrapper')) 
    $('show_manual-wrapper').style.display = 'none';	
if ($('overwrite_headmeta_active-0')) {
  $('overwrite_headmeta_active-0').addEvent('click', function () {
  		$('show_manual-wrapper').style.display = 'block';		
  })
}

if ($('overwrite_headmeta_active-1')) {
  $('overwrite_headmeta_active-1').addEvent('click', function () {
  		$('show_manual-wrapper').style.display = 'none';		
  })
}
});
</script>
<h2><?php echo $this->translate('Advanced Facebook Integration / Likes, Social Plugins and Open Graph');?></h2>
<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>
<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_upgrade_messages.tpl'; ?>
<?php include APPLICATION_PATH . '/application/modules/Facebookse/views/scripts/Opengraph_message.tpl'; ?>

<div class='seaocore_settings_form'>
	<a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'admin-settings', 'action' => 'help-invite'), 'default', true) ?>"
class="buttonlink" style="background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Facebookse/externals/images/help.gif);padding-left:23px;"><?php echo
$this->translate("Guidelines to configure Facebook Application") ?></a>
  <div class='settings' style="margin-top:15px;">
    <?php echo $this->form->render($this) ?>
  </div>
</div>