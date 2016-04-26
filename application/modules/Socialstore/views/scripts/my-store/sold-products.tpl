<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>
<div class="layout_middle">
<h3><?php echo $this->translate('List Sold Products of Store')?></h3>
<br />
 <?php //echo $this->count." ".$this->translate('order(s)');   ?>
<?php if( count($this->paginator) ): ?>
<div class='admin_search'>   
<?php  echo $this->form->render($this); 
$viewer = Engine_Api::_()->user()->getViewer();
?>
</div>
<br />
<script type="text/javascript">
    var currentOrder = '<?php echo $this->formValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
    var changeOrder = function(order, default_direction){
      // Just change direction
      if( order == currentOrder ) {
        $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
      } else {
        $('order').value = order;
        $('direction').value = default_direction;
      }
      $('filter_form').submit();
    }
  </script>
<table class='admin_table'>
  <thead>
    <tr>
      <th><?php echo $this->translate("Order ID") ?></th>
      <th><?php echo $this->translate("Product") ?></th>
      <th><?php echo $this->translate('Attributes')?></th>
      <th><?php echo $this->translate("Buyer") ?></th>
      <th style = "text-align: right;"><a href="javascript:void(0);" onclick="javascript:changeOrder('quantity', 'DESC');"><?php echo $this->translate("Quantity") ?></a></th> 
      <th><?php echo $this->translate("Shipping") ?></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('order_date', 'DESC');"><?php echo $this->translate("Order Date") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('delivery_status', 'DESC');"><?php echo $this->translate("Delivery Status") ?></a></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td>
        	<?php echo $item->order_id ?>
        </td>
        
         <td>
         	<?php 
         	$product = Engine_Api::_()->getItem('social_product', $item->product_id);
         	echo $this->htmlLink($product->getHref(), $product->title) ?>
         </td>
         <td>
				<?php echo Engine_Api::_()->getApi('attribute', 'socialstore')->getAttributes($item->options) ?>	
		</td>
		<td>
		<?php if ($item->buyer_id != 0) :?>
         	<a href="<?php echo $this->user($item->buyer_id)->getHref() ?>">
         		<?php echo $this->user($item->buyer_id)->getTitle() ?>
        	 </a>
        	  <?php elseif ($item->guest_id != 0) :?>
        	 <?php echo $this->translate('Guest');?>
        	 <?php endif;?>
         </td>
		<td style = "text-align: right;">
         <?php echo $item->quantity;?> 
         </td>       
       <td>
          <?php 
          echo $this->htmlLink(array(
              'route' => 'socialstore_mystore_general',
              'action' => 'view-shipping-info',
          	  'sh_add' => $item->shippingaddress_id,
            ), $this->translate('view info'), array(
            	'class' => ' smoothbox ',
            ))
           ?>
           </td>
        <td>
        <?php date_default_timezone_set($viewer->timezone);
		echo date('Y-m-d H:i:s',strtotime($item->order_date)); ?>
		</td>
         
         <td>
         	<?php echo $item->delivery_status;?>
         </td>
         
         <td>
          <?php 
          if ($item->delivery_status != 'delivered') { 
          echo $this->htmlLink(array(
              'route' => 'socialstore_mystore_general',
              'action' => 'change-delivery-status',
          	  'orderitem_id' => $item->orderitem_id,
          	  'owner_id' => $item->owner_id,
            ), $this->translate('Delivered'), array(
            	'class' => ' smoothbox ',
            ));
          }
          else {
          	echo $this->translate('N/A');
          }
           ?>
           </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br />
<div>
   <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
</div>

<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no sold products yet.") ?>
    </span>
  </div>
<?php endif; ?>
</div>
<style type="text/css">
 table.admin_table thead tr th {
    background-color: #E9F4FA;
    border-bottom: 1px solid #AAAAAA;
    font-weight: bold;
    padding: 7px 10px;
    white-space: nowrap;
}
table.admin_table tbody tr td {
    border-bottom: 1px solid #EEEEEE;
    font-size: 0.9em;
    padding: 7px 10px;
    vertical-align: top;
    white-space: normal;
}
#global_page_socialstore-my-store-sold-products form#filter_form {
	height: 35px;

}
#global_page_socialstore-my-store-sold-products form#filter_form div {
	height: 20px;
	float: left;
	margin-left: 15px;
}
#global_page_socialstore-my-store-sold-products form#filter_form div div.buttons button#productsearch {
	margin-top: 6px;
}
 </style>
