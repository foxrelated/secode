<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _hostlinks.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="form-wrapper">
    <div class="form-label"></div>
    <div class="form-element">
        <input type="checkbox" id="host_link" name="host_link" onclick="$('host_links').toggle();"  <?php if (isset($this->hostLinkInfo) && ((isset($this->hostLinkInfo['show_facebook'])) || isset($this->hostLinkInfo['show_twitter']) || isset($this->hostLinkInfo['show_website']))): ?> checked="checked" <?php endif; ?>>
        <label for="host_link" class="optional" for="host_link"><?php echo $this->translate('Include host\'s Facebook, Twitter and Website links'); ?></label>
        <div id="host_links" style="<?php if (isset($this->hostLinkInfo) && ((isset($this->hostLinkInfo['show_facebook'])) || isset($this->hostLinkInfo['show_twitter']) || isset($this->hostLinkInfo['show_website']))): ?> display:block; <?php else: ?> display:none;<?php endif; ?>margin-top: 10px;">
            <ul class="host-links">
                <li>
                    <input type="checkbox" id="hostlinks[show_facebook]" name="hostlinks[show_facebook]" <?php if (isset($this->hostLinkInfo['show_facebook']) && !empty($this->hostLinkInfo['show_facebook']['checked'])): ?> checked="checked" <?php endif; ?>>
                    <label for="hostlinks[show_facebook]">https://facebook.com/</label>
                    <input type="text" name="show_facebook" style="width: 150px;" value="<?php if (isset($this->hostLinkInfo['show_facebook'])): echo $this->hostLinkInfo['show_facebook']['text'];
endif;
?>" />
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/help.png" title="<?php echo $this->translate('Enter host\'s Facebook Profile or Page address to include a link to it from this event.'); ?>" >	
                </li>
                <li>
                    <input type="checkbox" id="hostlinks[show_twitter]" name="hostlinks[show_twitter]" <?php if (isset($this->hostLinkInfo['show_twitter']) && !empty($this->hostLinkInfo['show_twitter']['checked'])): ?> checked="checked" <?php endif; ?>>
                    <label for="hostlinks[show_twitter]">https://twitter.com/</label>
                    <input type="text" name="show_twitter"  style="width: 150px;" value="<?php if (isset($this->hostLinkInfo['show_twitter'])): echo $this->hostLinkInfo['show_twitter']['text'];
                           endif;
?>"/>
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/help.png" title="<?php echo $this->translate('Enter host\'s Twitter address to include a link to it from this event.') ?>" >	
                </li>
                <li>
                    <input type="checkbox" id="hostlinks[show_website]" name="hostlinks[show_website]" <?php if (isset($this->hostLinkInfo['show_website']) && !empty($this->hostLinkInfo['show_website']['checked'])): ?> checked="checked" <?php endif; ?>>
                    <label for="hostlinks[show_website]"><?php echo $this->translate('Website'); ?>:</label>
                    <input type="text" name="show_website" style="width: 150px;" value="<?php if (isset($this->hostLinkInfo['show_website'])): echo $this->hostLinkInfo['show_website']['text'];
                           endif;
?>" />
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/help.png" title="<?php echo $this->translate('Enter host\'s Website address to include a link to it from this event.') ?>" >	
                </li>
            </ul>
        </div>
    </div>
</div>