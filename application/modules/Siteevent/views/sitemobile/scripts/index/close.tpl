<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: publish.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class='global_form_popup'>
    <?php if ($this->success): ?>
        <script type="text/javascript">
            parent.$('list-item-<?php echo $this->event_id ?>').destroy();
            setTimeout(function() {
                parent.Smoothbox.close();
            }, 1000);
        </script>
        <div class="global_form_popup_message">
            <?php if (!$this->siteevent->closed): ?>  
                <?php echo $this->translate('Your event has been re-published.'); ?> 
            <?php else: ?>  
                <?php echo $this->translate('Your event has been cancelled.'); ?>
            <?php endif; ?>  
        </div>
    <?php else: ?>
        <form method="POST" action="<?php echo $this->url() ?>">
            <div>
                <?php if (!$this->siteevent->closed): ?>    
                    <h3><?php echo $this->translate("Cancel event?"); ?></h3>
                    <p>
                        <?php echo $this->translate("Are you sure that you want to cancel this event?"); ?>
                    </p>
                <?php else: ?>
                    <h3><?php echo $this->translate("Re-publish event?"); ?></h3>
                    <p>
                        <?php echo $this->translate("Are you sure that you want to re-publish this event that was cancelled earlier?"); ?>
                    </p>          
                <?php endif; ?>
                <p>&nbsp;
                </p>
                <p>
                    <input type="hidden" name="event_id" value="<?php echo $this->event_id ?>"/>
                    <input type="hidden" value="" name="email" />
                    <input type="checkbox" checked="checked" value="1" id="email" name="email" onclick="$('reason').toggle()" />
                    <?php echo $this->translate("Send an email alerts to all guests."); ?>
                    <br/><br/>
                    <textarea name="reason" id="reason" rows="4" cols="50" placeholder="<?php echo $this->translate("Write the email body..."); ?>"></textarea>
                    <br />
                    <br />
                    <button type='submit' data-theme='b'><?php echo empty($this->siteevent->closed) ? $this->translate('Submit') : $this->translate('Re-publish'); ?></button>
                    <?php echo $this->translate(' or ') ?> 
                    <a href="#" data-rel="back" data-role="button">
                        <?php echo $this->translate('Close') ?>
                    </a>
                </p>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
            TB_close();
    </script>
<?php endif; ?>