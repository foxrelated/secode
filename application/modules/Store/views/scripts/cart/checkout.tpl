<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  4/25/12 6:23 PM mt.uulu $
 * @author     Mirlan
 */
?>

<?php if ($this->justHtml): ?>
  <?php
  echo $this->render('cart/_browse_item.tpl');
  return;
  ?>
<?php endif; ?>

<script type="text/javascript">
  var cart = <?php echo $this->cart->getIdentity(); ?>;
  var checkout = function (credit) {


 	var url = '<?php echo $this->url(array('action'=>'select-items', 'product_id'=> $this->product->getIdentity()),'store_cart',true);?>';
 	var creditUrl = '<?php echo $this->url(array('action'=>'make-request', 'product_id'=> $this->product->getIdentity(), 'credit' => true),'store_cart',true);?>';  


  	if (credit) {
  		Smoothbox.open(creditUrl);
  	}
  	else {
  		Smoothbox.open(url);
  	}
  };
</script>

<?php if ($this->details) :?>
<div class="generic_layout_container layout_right" id="store-cart-checkout-container">
  <?php echo $this->render('cart/_checkout_item.tpl'); ?>
</div>
<?php endif;?>

<script type="text/javascript">
  product_manager.widget_url = "<?php echo $this->url(array('controller'=>'cart'), 'store_extended', true);?>";
  product_manager.widget_element = '.he-items';
  store_cart.prices_url = "<?php echo $this->url(array('controller' => 'cart', 'action' => 'price'), 'store_extended', true);?>";
</script>

<?php if ($this->details): ?>
  <div class="shipping-details">
    <span class="float_left"><?php echo $this->translate('Shipping Details'); ?>&nbsp;</span>
    <?php if (isset($this->details['zip'])): ?>
      <span class="float_left">
        <?php
        echo $this->details['first_name'] . ' ' . $this->details['last_name'] . "<br />" .
          $this->details['address_line_1'] . (($this->details['address_line_2']) ? $this->translate(' or ') . $this->details['address_line_2'] : '') . "<br />" .
          $this->details['city'] . ', ' . $this->region . ' ' . $this->details['zip'] . ', ' . $this->country . "<br />" .
          $this->details['phone'] . (($this->details['phone_extension']) ? $this->translate(' or ') . $this->details['phone_extension'] : '');
        ?>
      </span>
    <?php endif; ?>
    <?php
    echo $this->htmlLink(array(
        'route' => 'store_extended',
        'controller' => 'cart',
        'action' => 'details'),
      '<i class="hei hei-pencil-square-o"></i>',
      array(
        'class' => 'smoothbox float_right',
        'title' => $this->translate("Edit")
      ));
    ?>
  </div>
<?php endif; ?>

<div class="generic_layout_container layout_middle">
  <?php echo $this->render('cart/_browse_item.tpl'); ?>
</div>