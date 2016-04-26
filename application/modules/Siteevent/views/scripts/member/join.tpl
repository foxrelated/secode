<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: join.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if($this->isEventFull && $this->waitlist_id): ?>
    <div class='global_form_popup'>
        <div class="tip">
            <span>
                <?php echo $this->translate("Please increase the capacity value to approve this waitlist request.") ?>
            </span>
            <div class="buttons mtop10">
              <button type="button" name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
            </div>        
        </div>
    </div>    
<?php else: ?>
    <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
<?php endif; ?>
