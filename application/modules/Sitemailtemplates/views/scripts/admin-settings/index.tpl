<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate("Email Templates Plugin") ?></h2>

<?php $this->tinyMCESEAO()->addJS();?>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<?php 
if( !empty($this->isModsSupport) ):
	foreach( $this->isModsSupport as $modName ) {
		echo $this->translate('<div class="tip"><span>Note: Your website does not have the latest version of "%s". Please upgrade "%s" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Email Templates Plugin".</span></div>', ucfirst($modName), ucfirst($modName));
	}
endif;
?>

<?php if (!empty($this->messageSent)): ?>
  <ul class="form-notices" >
    <li>
      <?php echo $this->successMessge; ?>
    </li>
  </ul>
<?php endif; ?>

<div class='clear  seaocore_settings_form'>
	<div class='settings'>
		<?php echo $this->form->render($this); ?>
	</div>
</div> 


<script type="text/javascript">
  
  var textfalg = '<?php echo $this->textFlag;?>';
  if(textfalg == 0) {
		addTinyMCE('sitemailtemplates_footer1');
  }

	function addTinyMCE(element_id) {
		 <?php echo $this->tinyMCESEAO()->render(array('element_id' => 'element_id',
      'language' => $this->language,
      'directionality' => $this->directionality,
      'upload_url' => $this->upload_url)); ?>
	}
</script>

<style type="text/css">
.defaultSkin iframe {
	height:300px !important;
	width: 650px !important;
}
</style>