<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>

<div class = "layout_middle">
<h3><?php echo $this->translate('Products Statistics')?></h3>
<br />
<?php if( count($this->paginator) ): ?>
<script type="text/javascript">
    var currentOrder = '<?php echo $this->filterValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->filterValues['direction'] ?>';
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
      <th style = "text-align: left;"><?php echo $this->translate("Product") ?></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'DESC');"><?php echo $this->translate("Featured") ?></a></th> 
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('rate_ave', 'DESC');"><?php echo $this->translate("Rate") ?></a></th>
      <th style = "text-align: right;"><?php echo $this->translate("Paid Fee") ?></th>
      <th style = "text-align: right;"><a href="javascript:void(0);" onclick="javascript:changeOrder('sold_qty', 'DESC');"><?php echo $this->translate("Unit Sold") ?></a></th>
      <th style = "text-align: right;"><a href="javascript:void(0);" onclick="javascript:changeOrder('total_amount', 'DESC');"><?php echo $this->translate("Income") ?></a></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td style = "text-align: left;">
        	<?php echo $item; ?>
        </td>
		<td>
         <?php if ($item->featured == 1) {
         			echo $this->translate('Yes');
         	    }
         	    else 
         	    { 
         	    	echo $this->translate('No');
         	    }
         ?> 
         </td>       
		<td>
         <?php echo $this->locale()->toNumber($item->rate_ave).$this->translate(' Stars'); ?> 
         </td>       
		<td style = "text-align: right;">
         <?php echo $this->currency($item->getTotalPaidFee()) ?>
         </td>       
		<td style = "text-align: right;">
         <?php echo $this->locale()->toNumber($item->sold_qty) ?> 
         </td>       
		<td style = "text-align: right;">
         <?php echo $this->currency($item->getAmount($item->sold_qty)) ?>
         </td>       
       
         <td>
          <?php 
          echo $this->htmlLink(array(
              'route' => 'socialstore_mystore_general',
              'action' => 'transaction-detail',
          	  'product_id' => $item->product_id,
            ), $this->translate('transaction history'), array(
            ))
           ?>
           </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<br />

			
			

<?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have no product yet.');?>
      </span>
    </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    //'query' => '',
    //'params' => $this->formValues,
  )); ?>

</div>
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
 </style>
