<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: gateway.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')): ?>
    <?php if (!Engine_Api::_()->seaocore()->checkModuleNameAndNavigation()): ?>
        <?php if (empty($this->hideNavigation)): ?>
            <?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/navigation_views.tpl'; ?>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

<?php if ($this->status == 'pending'): // Check for pending status ?>
    <?php echo $this->translate("Your Event is pending payment. You will receive an email when the payment completes."); ?>
<?php else: ?>

    <form method="get" action="<?php echo $this->escape($this->url(array(), "siteevent_process_payment", true)) ?>"
          class="global_form" enctype="application/x-www-form-urlencoded">
        <div>
            <div>
                <h3>
                    <?php echo $this->translate("Order your Event") ?>
                </h3>
                <p class="form-description">
                    <?php echo $this->translate("You have created an Event that requires payment. You will be taken to a secure checkout area where you can pay for your Event.") ?>
                </p>
                <p style="font-weight: bold; padding-top: 15px; padding-bottom: 15px;max-width:none;">
                    <?php if ($this->package->recurrence): ?>
                        <?php echo $this->translate("Your Event requires payment:") ?>
                    <?php else: ?>
                        <?php echo $this->translate('Please pay a one-time fee to continue:') ?>
                    <?php endif; ?>
                    <?php echo $this->package->getPackageDescription() . "." ?>
                </p>
                <div class="form-elements">
                    <div id="buttons-wrapper" class="form-wrapper">
                        <?php
                        foreach ($this->gateways as $gatewayInfo):
                            $gateway = $gatewayInfo['gateway'];
                            $first = (!isset($first) ? true : false );
                            ?>
                            <?php if (!$first): ?>
                                <?php echo $this->translate('or') ?>
                            <?php endif; ?>
                            <?php if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')): ?>
                                <button type="submit" name="execute" onclick="$('gateway_id').set('value', '<?php echo $gateway->gateway_id ?>')">
                                <?php else: ?>
                                    <button type="submit" name="execute" onclick="$('#gateway_id').val('<?php echo $gateway->gateway_id ?>')">
                                    <?php endif; ?>
                                    <?php echo $this->translate('Pay with %1$s', $this->translate($gateway->title)) ?>
                                </button>
                            <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="gateway_id" id="gateway_id" value="" />
    </form>
<?php endif; ?>