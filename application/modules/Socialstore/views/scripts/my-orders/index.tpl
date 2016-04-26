<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<div class='admin_search'>   
<?php  echo $this->form->render($this); ?>
</div>
<br />
<?php if( count($this->paginator) ): ?>
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
<div class="layout_middle">
<table class='admin_table'>
  <thead>
    <tr>
      <th ><?php echo $this->translate("Order ID") ?></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('payment_status', 'DESC');"><?php echo $this->translate("Status") ?></a></th> 
      <th><?php echo $this->translate("Currency") ?></th>
      <th style = "text-align: right;"><a href="javascript:void(0);" onclick="javascript:changeOrder('quantity', 'DESC');"><?php echo $this->translate("Quantity") ?></a></th>
      <th style = "text-align: right;"><a href="javascript:void(0);" onclick="javascript:changeOrder('sub_amount', 'DESC');"><?php echo $this->translate("Sub Amount") ?></a></th>
      <th style = "text-align: right;"><a href="javascript:void(0);" onclick="javascript:changeOrder('shipping_amount', 'DESC');"><?php echo $this->translate("Shipping Fee") ?></a></th>
      <th style = "text-align: right;"><a href="javascript:void(0);" onclick="javascript:changeOrder('handling_amount', 'DESC');"><?php echo $this->translate("Handling Fee") ?></a></th>
      <th style = "text-align: right;"><a href="javascript:void(0);" onclick="javascript:changeOrder('total_amount', 'DESC');"><?php echo $this->translate("Total Amount") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Order Date") ?></a></th>
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
         <?php echo $this->translate($item->payment_status);?> 
         </td>       
		<td>
         <?php echo Engine_Api::_()->getApi('core','Socialstore')->getDefaultCurrency()?> 
         </td>       
		<td style = "text-align: right;">
         <?php echo $item->quantity;?> 
         </td>       
		<td style = "text-align: right;">
         <?php echo $this->currency($item->getSubAmount());?> 
         </td>       
		<td style = "text-align: right;">
         <?php echo $this->currency($item->getShippingAmount());?> 
         </td>       
		<td style = "text-align: right;">
         <?php echo $this->currency($item->getHandlingAmount());?> 
         </td>       
		<td style = "text-align: right;">
         <?php echo $this->currency($item->getTotalAmount());?> 
         </td>       
       
        <td>
        <?php date_default_timezone_set($this->viewer->timezone);
		echo date('Y-m-d H:i:s',strtotime($item->creation_date)); ?></td>
         
         <td>
          <?php 
          echo $this->htmlLink(array(
              'route' => 'socialstore_extended',
              'controller' => 'my-orders',
              'action' => 'order-detail',
          	  'order_id' => $item->order_id,
            ), $this->translate('view details'), array(
            ))
           ?>
           </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
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
      <?php echo $this->translate("There are no orders yet.") ?>
    </span>
  </div>
<?php endif; ?>
 <style type="text/css">
 table.admin_table thead tr th {
    background-color: #E9F4FA;
    border-bottom: 1px solid #AAAAAA;
    font-weight: bold;
    padding: 7px 10px;
    white-space: nowrap;
    text-align: center;
}
table.admin_table tbody tr td {
    border-bottom: 1px solid #EEEEEE;
    font-size: 0.9em;
    padding: 7px 10px;
    vertical-align: top;
    white-space: normal;
    text-align: center;
}
#global_page_socialstore-my-orders-index form#filter_form {
	height: 35px;
	width: 200px;
}
#global_page_socialstore-my-orders-index form#filter_form div {
	height: 20px;
	float: left;
	margin-left: 5px;
}
#global_page_socialstore-my-orders-index form#filter_form div div.buttons button#search {
	margin-top: 5px;
}
 </style>
