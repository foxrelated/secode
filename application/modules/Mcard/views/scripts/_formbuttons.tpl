<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteslideshow
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formbuttons.tpl 6590 2010-08-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
  echo '
    <div class="form-wrapper">
    	<div class="form-element">
    		<button name="submit_temp" id="submit_temp" type="submit" onclick="javascript:mainFormSubmit();" style="float:left;margin-right:5px;">'.$this->translate("Save Settings").'</button>
    		<button name="preview" id="preview" type="button" onclick="javascript:showPreview();" style="float:left;">'.$this->translate("Preview").'</button>
  		</div>
  	</div>'
?>
