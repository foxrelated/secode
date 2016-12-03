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

<?php if ($this->details || $this->request->credit) :?>
<div class="generic_layout_container layout_right" id="store-cart-checkout-container">
  <?php echo $this->render('request/_summary_item.tpl'); ?>
</div>
<?php endif;?>

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
  <?php echo $this->render('request/_browse_list.tpl'); ?>
</div>