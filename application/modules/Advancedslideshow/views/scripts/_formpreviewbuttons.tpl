<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formpreviewbuttons.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
echo '
		<div id="submit-wrapper" class="form-wrapper"><div id="submit-label" class="form-label">&nbsp;</div>
	<button name="submit" id="submit" type="submit">'.$this->translate("Save Settings").'</button>&nbsp;&nbsp;
	<button name="preview" id="preview" type="button" onclick="javascript:showPreview();">'.$this->translate("Preview").'</button>&nbsp;&nbsp;
	<button name="default" id="default" type="submit">'.$this->translate("Reset to Default").'</button></div></div>
'
?>