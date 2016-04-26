<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: likesetings.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate('Likes Plugin & Widgets') ?>
</h2>
<?php if( count($this->navigation) ): ?>
<div class='tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>
<?php   $newStyleButtonUpdate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelike.button.likeupdatefile', 1);
 if (empty($newStyleButtonUpdate)): ?>
<div class="tip">
  <span>
    <?php echo $this->translate('Note: If you want to minimize the size of Likes Plugin CSS, then please give write permission (chmod 777) to the file "/application/modules/Sitelike/externals/styles/likesettings.css".');?>
  </span>
</div>
<?php endif; ?>
<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php
      //RENDEDR THE FORM		
      echo $this->form->render($this);
    ?>
  </div>
</div>
<?php if($this->error_message == 1): ?> 
	<?php if($this->message == 0): ?>
		<script type="text/javascript">
			var url = en4.core.baseUrl + 'admin/sitelike/settings/popupcssfile';
			Smoothbox.open(url);
		</script>
	<?php else: ?>
		<script type="text/javascript">
			var url = en4.core.baseUrl + 'admin/sitelike/settings/defaultcsspopup';
			Smoothbox.open(url);
		</script>
	<?php endif; ?>
<?php endif; ?>