<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: showTemplate.tpl 2012-6-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="global_form_popup">	
  <?php 
    switch ($this->template_id) {
			case "1" : $img_path = 'application/modules/Sitemailtemplates/externals/images/templates/template1.gif';
			break;
			case "2" : $img_path = 'application/modules/Sitemailtemplates/externals/images/templates/template2.gif';
			break;
			case "3" : $img_path = 'application/modules/Sitemailtemplates/externals/images/templates/template3.gif';
			break;
			case "4" : $img_path = 'application/modules/Sitemailtemplates/externals/images/templates/template4.gif';
			break;
			case "5" : $img_path = 'application/modules/Sitemailtemplates/externals/images/templates/template5.gif';
			break;
			case "6" : $img_path = 'application/modules/Sitemailtemplates/externals/images/templates/template6.gif';
			break;
			case "7" : $img_path = 'application/modules/Sitemailtemplates/externals/images/templates/template7.gif';
			break; 
			case "8" : $img_path = 'application/modules/Sitemailtemplates/externals/images/templates/template8.gif';
			break;
			case "9" : $img_path = 'application/modules/Sitemailtemplates/externals/images/templates/template9.gif';
			break;
			case "10" : $img_path = 'application/modules/Sitemailtemplates/externals/images/templates/template10.gif';
			break;
    }
  ?>
	<p>
	  <img src="<?php echo $this->layout()->staticBaseUrl.$img_path ?>" />
	</p>
	<br />
	<div class="tip" style="margin-bottom:0px;">
		<span>
			<?php echo $this->translate("This screenshot was taken by using the default settings of this template. This screenshot will not reflect your changes, if you have made any. You can customize the design and other aspects of this template like colors, etc by clicking on the ‘Edit’ link for this.") ?>	</span>
	</div>

</div>	
