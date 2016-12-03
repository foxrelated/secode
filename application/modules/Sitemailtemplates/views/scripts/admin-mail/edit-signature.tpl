<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-signature.tpl 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class='global_form_popup'>
	<?php echo $this->form->render($this); ?>
</div>

<script type="text/javascript" >

	function saveSignature()
	{
		var confirm_mail =  confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to save email signature for all the Templates in this language?")) ?>');
    if(confirm_mail == false) {
     return;
    }
    $('signature_form').submit();
	}

</script>