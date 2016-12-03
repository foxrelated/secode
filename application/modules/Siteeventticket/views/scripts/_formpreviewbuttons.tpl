<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formpreviewbuttons.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
echo '
  <div id="submit-wrapper" class="form-wrapper"><div id="submit-label" class="form-label">&nbsp;</div>
	<button name="submit" id="submit" type="submit">'.$this->translate("Save Settings").'</button>&nbsp;&nbsp;
	<button name="default" id="default" type="submit">'.$this->translate("Reset to Default").'</button></div></div>
'
?>