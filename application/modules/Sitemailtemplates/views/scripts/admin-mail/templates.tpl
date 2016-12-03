<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: templates.tpl 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->tinyMCESEAO()->addJS();?>

<h2><?php echo $this->translate("Email Templates Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='clear  seaocore_settings_form'>
	<div class='settings'>
		<?php echo $this->form->render($this); ?>
	</div>
</div>

<script type="text/javascript">
  var mailTemplateLanguage = '<?php echo $this->language_url ?>';
  
  var setEmailLanguage = function(language) {
    
    var url = '<?php echo $this->url(array('textFlag' => 0,'signature_editor' => 0,'language' => null, 'template' => null)) ?>';
    window.location.href = url + '/language/' + language;
  }

  var fetchEmailTemplate = function(template_id) {
    var url = '<?php echo $this->url(array('textFlag' => 0,'signature_editor' => 0,'language' => null, 'template' => null)) ?>';
    window.location.href = url + '/language/' + mailTemplateLanguage + '/template/' + template_id;
  }

  var textfalg = '<?php echo $this->textFlag;?>';
  if(textfalg == 0) {
		addTinyMCE('body');
  }

  var textFlagofSig = '<?php echo $this->textFlagofSig;?>';
  if(textFlagofSig == 0) {
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
