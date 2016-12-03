<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
    <?php echo 'Advanced Events Plugin'; ?>
</h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<?php if (count($this->navigationGeneral)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
    </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>

<script type="text/javascript">
    if ($('siteeventticketcoupon_isprivate-1')) {

        $('siteeventticketcoupon_isprivate-1').addEvent('click', function () {
            $('siteeventticketcoupon_coupon_show_menu-wrapper').setStyle('display', ($(this).get('value') == '1' ? 'none' : 'block'));
            $('siteeventticketcoupon_order-wrapper').setStyle('display', ($(this).get('value') == '1' ? 'none' : 'block'));
            $('siteeventticketcoupon_truncation_limit-wrapper').setStyle('display', ($(this).get('value') == '1' ? 'none' : 'block'));
        });

        $('siteeventticketcoupon_isprivate-0').addEvent('click', function () {
            $('siteeventticketcoupon_coupon_show_menu-wrapper').setStyle('display', ($(this).get('value') == '0' ? 'block' : 'none'));
            $('siteeventticketcoupon_order-wrapper').setStyle('display', ($(this).get('value') == '0' ? 'block' : 'none'));
            $('siteeventticketcoupon_truncation_limit-wrapper').setStyle('display', ($(this).get('value') == '0' ? 'block' : 'none'));
        });

        window.addEvent('domready', function () {
            $('siteeventticketcoupon_coupon_show_menu-wrapper').setStyle('display', ($('siteeventticketcoupon_isprivate-0').checked ? 'block' : 'none'));
            $('siteeventticketcoupon_order-wrapper').setStyle('display', ($('siteeventticketcoupon_isprivate-0').checked ? 'block' : 'none'));
            $('siteeventticketcoupon_truncation_limit-wrapper').setStyle('display', ($('siteeventticketcoupon_isprivate-0').checked ? 'block' : 'none'));
        });
    }
</script>