<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: enable-disable.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
    <div>
        <?php if(empty($this->siteeventticketcoupon->status)):?>
            <h3><?php echo $this->translate('Enable Coupon'); ?></h3>
        <?php else:?>
            <h3><?php echo $this->translate('Disable Coupon'); ?></h3>
        <?php endif;?>
            
        <?php if(empty($this->siteeventticketcoupon->status)):?>
            <p>
                <?php echo $this->translate('Are you sure you want to Enable this Coupon?'); ?>
            </p>
        <?php else:?>
            <p>
                <?php echo $this->translate('Are you sure you want to Disable this Coupon?'); ?>
            </p>
        <?php endif;?>
            
        <br />
        <p>
            <input type="hidden" name="confirm" value="<?php echo $this->coupon_id ?>"/>
                <?php if(empty($this->siteeventticketcoupon->status)):?>
                    <button type='submit'><?php echo $this->translate('Enable'); ?></button>
                <?php else:?>
                    <button type='submit'><?php echo $this->translate('Disable'); ?></button>
                <?php endif;?>
                <?php echo $this->translate(' or '); ?>
                <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
        </p>
    </div>
</form>

<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>