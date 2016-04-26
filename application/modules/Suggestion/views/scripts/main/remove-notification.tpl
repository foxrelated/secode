<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: remove-notification.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
if (!empty($this->responseWithTip)) {
  echo '<div class="tip"><span>' . $this->translate("Suggestion has been removed successfully.") . '</span></div>';
//  echo '<div class="seaocore_tip">' . $this->translate("Suggestion has been removed successfully.") . '</div>';
} else {
  echo '<div>' . $this->translate("Suggestion has been removed successfully.") . '</div>';
}
?>