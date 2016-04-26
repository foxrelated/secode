<h2><?php echo $this->translate("Store Plugin") ?></h2>

<!-- admin menu -->
<?php echo $this->content()->renderWidget('socialstore.admin-main-menu') ?>
<div class='admin_search'>   
<?php  echo $this->form->render($this); ?>
</div>

<br />

<div>
<?php if( count($this->paginator) ): 
$viewer = Engine_Api::_()->user()->getViewer();
?>
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
<table class='admin_table'>
  <thead>
    <tr>
      <th style = "text-align: left;"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'DESC');"><?php echo $this->translate("Product") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'DESC');"><?php echo $this->translate("Featured") ?></a></th> 
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('rate_ave', 'DESC');"><?php echo $this->translate("Rate") ?></a></th>
      <th style = "text-align: right;"><?php echo $this->translate("Paid Fee") ?></th>
      <th style = "text-align: right;"><a href="javascript:void(0);" onclick="javascript:changeOrder('sold_qty', 'DESC');"><?php echo $this->translate("Unit Sold") ?></a></th>
      <th style = "text-align: right;"><a href="javascript:void(0);" onclick="javascript:changeOrder('total_amount', 'DESC');"><?php echo $this->translate("Income") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('approved_date', 'DESC');"><?php echo $this->translate("Approve Date") ?></a></th>
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
         <?php echo $this->locale()->toNumber($item->rate_ave)." ".$this->translate('Stars'); ?> 
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
        <?php if($item->isApproved()){ 
                  date_default_timezone_set($viewer->timezone);
				  echo date('Y-m-d H:i:s',strtotime($item->approved_date)); 
			  }
			  else {
			  	//echo $this->translate('-');
			  }
		?>
		</td>
         
         <td>
          <?php 
          echo $this->htmlLink(array(
          'module' => 'socialstore',
       	  'controller' => 'manage-store',
          'action' => 'transaction-history',
      	  'product_id' => $item->product_id,
       	  'route' => 'admin_default',
          'reset' => true,
           ), $this->translate('transaction history'), array(
            ))
           ?>
           </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<br />

			
			

<?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('This store does not have any product yet.');?>
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
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {  
 display: table;
  height: 65px;
}
.profile_fields {
    margin-top: 10px;
    overflow: hidden;
}
.profile_fields h4 {
    border-bottom: 1px solid #EAEAEA;
    font-weight: bold;
    margin-bottom: 10px;
    padding: 0.5em 0;
}
.profile_fields h4 > span {
    background-color: #FFFFFF;
    display: inline-block;
    margin-top: -1px;
    padding-right: 6px;
    position: absolute;
    color: #717171;
    font-weight: bold;
}
.profile_fields > ul {
    padding: 10px;
    list-style-type: none;
}
.profile_fields > ul > li {
    overflow: hidden;
    margin-top: 3px;
}

.profile_fields > ul > li > span {
    display: block;
    float: left;
    margin-right: 15px;
    overflow: hidden;
    width: 275px;
}

.profile_fields > ul > li > span + span {
    display: block;
    float: left;
    min-width: 0;
    overflow: hidden;
    width: auto;
}

</style>