  <div class="headline">
  <h2>
    <?php echo $this->translate('GroupBuy');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>
<h3>
<?php echo $this->translate('Transaction Listing');?>
</h3>
<table class='admin_table'>
    <thead>
    <tr>
        <th style="text-align:center"><?php echo $this->translate('Date');?></th>
        <th style="text-align:center"><?php echo $this->translate('Payment To');?>  </th>
        <th style="text-align:center"><?php echo $this->translate('Payment From');?>  </th> 
        <th style="text-align:center"><?php echo $this->translate('Item ID');?>  </th>
        <th style="text-align:center"><?php echo $this->translate('Quantity');?>  </th>
        <th style="text-align:center"><?php echo $this->translate('Amount');?>  </th>
        <th style="text-align:center"><?php echo $this->translate('Commission Fee');?>  </th>
        <th style="text-align:center"><?php echo $this->translate('Type Tracking');?>  </th>
        <th style="text-align:center"><?php echo $this->translate('Status');?>  </th>
    </tr>
  </thead>
    <tbody>
    <?php foreach($this->history as $track):?>
    <tr>
    <td style="text-align:center"><?php echo $this->locale()->toDateTime($track->pDate); ?> </td>
    <td style="text-align:center">
    <?php $seller = Engine_Api::_()->getItem('user',$track->user_seller); ?>
    <?php echo $seller; ?>
   </td>
    <td style="text-align:center">
    <?php $buyer = Engine_Api::_()->getItem('user',$track->user_buyer); ?>
    <?php echo $buyer ?>  
    </td>
    <td style="text-align:center"><?php echo $track->item_id ?> </td>
    <td style="text-align:center"><?php echo $track->number ?> </td>
    <td style="text-align:center"><?php echo $this->currencyadvgroup($track->amount,$track['currency']) ?></td>        
    <td style="text-align:center"><?php echo $this->currencyadvgroup($track->commission_fee,$track['currency']) ?></td>        
    <td style="text-align:center"><?php echo $this->translate($track->params) ?> </td>
    <td style="text-align:center"><?php if ($track->transaction_status == 1): echo $this->translate('Successfully');  else:  echo $this->translate('Fail'); endif; ?> </td>
  </tr>
                               
  <?php endforeach; ?>
   </tbody>
  </table>  
<?php echo  $this->paginationControl($this->history); ?>  
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
table.admin_table tbody tr:nth-child(2n) {
    background-color: #F8F8F8;
}
 </style>
