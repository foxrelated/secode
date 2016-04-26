<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: unset-waitlist-flag.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form">
    <div>
        <div>
            <?php if(!Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>
                <h3><?php echo $this->translate('Allow Join Events ?'); ?></h3>
                <p>
                    <?php echo $this->translate('Do you want to allow site members to Join this event despite some members has been already added under waitlist for this event ?'); ?>
                </p>            
            <?php else: ?>
                <h3><?php echo $this->translate('Allow Tickets Booking ?'); ?></h3>
                <p>
                    <?php echo $this->translate('Do you want to allow site members to book tickets for this event despite some members has been already joined under waitlist for this event ?'); ?>
                </p>            
            <?php endif; ?>
            <br />
            <p>
                <input type="hidden" name="confirm" value="<?php echo $this->occurrence_id ?>"/>
                <button type='submit'><?php echo $this->translate('Allow'); ?></button>
                <?php echo $this->translate('or'); ?> <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
            </p>
        </div>
    </div>
</form>

<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>