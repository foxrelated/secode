<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _addMessage.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<?php if ($this->depth == 3) : ?>
        <div id="message-wrapper" class="form-wrapper" style="display:none;">
            <div id="message-label" class="form-label">
                <label for="message" class="optional"  style="color: red" ><?php echo "This menu cannot be either sub menu or 3rd level menu."?></label>
            </div>
        </div>
<?php elseif ($this->depth == 2): ?>
        <div id="message-wrapper" class="form-wrapper" style="display:none;">
            <div id="message-label" class="form-label">
                <label for="message" class="optional" style="color: red" ><?php //echo "This menu can be a sub menu but cannot be 3rd level menu."?></label>
            </div>
        </div>
<?php else :?>
        <div id="message-wrapper" class="form-wrapper" style="display:none;">
            <div id="message-label" class="form-label">
                <label for="message" class="optional" style="color: red" ><?php echo ""?></label>
            </div>
        </div>
<?php endif;?>