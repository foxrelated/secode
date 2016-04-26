<div style="height:500px; width:600px;">  
  <h3>
    <?php 
    if($this ->user_name == "null"):
        echo $this->translate('Transaction History');
    else:
        echo $this->translate('Transaction History of '); echo $this->user_name;
    endif;
    ?>
  </h3>
     <table class='admin_table' style="border-collapse: collapse;">
    <thead>
    <tr>
         <th style="text-align:center" align="center"><?php echo $this->translate('Date');?>  </th>
         <th style=" text-align:center" align="center"><?php echo $this->translate('Item ID');?>  </th>
         <th style=" text-align:center" align="center"><?php echo $this->translate('Type Tracking');?>  </th>
         <th style=" text-align:center" align="center"><?php echo $this->translate("Amount") ?></th>
         <th style=" text-align:center" align="center"><?php echo $this->translate('Commission Fee');?>  </th>
         <th style=" text-align:center" align="center"><?php echo $this->translate('Payment To');?>  </th>
         <th style=" text-align:center" align="center"><?php echo $this->translate('Payment From');?>  </th>
         <th style=" text-align:center" align="center"><?php echo $this->translate('Status');?>  </th>

    </tr>
    </thead>
    <tbody>
         <?php foreach($this->history as $track):?>
            <tr>
                <td  style="text-align:center" align="center" ><?php echo $this->locale()->toDateTime($track->pDate); ?> </td>
                <td style="text-align:center" align="center" ><?php echo $track->item_id ?> </td>
                <td  style="text-align:center" align="center" ><?php echo $track->params ?> </td>
                <td  style="text-align:center" align="center" > <?php echo $this->currencyadvgroup($track['amount'],$track['currency']);?> </td>
                <td  style="text-align:center" align="center" > <?php echo $this->currencyadvgroup($track['commission_fee'],$track['currency']);?> </td>
                <td  style="text-align:center" align="center" ><?php if($track->account_seller_email): echo $track->account_seller_email; else: echo "N/A"; endif; ?> </td>  
                <td  style="text-align:center" align="center" ><?php if($track->account_buyer_email): echo $track->account_buyer_email; else: echo "N/A"; endif; ?> </td>          
                <td  style="text-align:center" align="center" ><?php if ($track->transaction_status == 1): echo $this->translate('Successfully');  else:  echo $this->translate('Fail'); endif; ?> </td>
            </tr>
       <?php endforeach; ?>
         </tbody>
  </table>
  </div>
 <style type="text/css">
 table.admin_table thead tr th {
    background-color: #E9F4FA;
    border-bottom: 1px solid #AAAAAA;
    font-weight: bold;
    padding: 7px 10px;
    white-space: nowrap;
    font-size: 0.8em; 
    font-family: tahoma, arial, verdana, sans-serif;
}
table.admin_table tbody tr td {
    border-bottom: 1px solid #EEEEEE;
    font-size: 0.8em; 
    font-family: tahoma, arial, verdana, sans-serif;
    padding: 7px 10px;
    vertical-align: top;
    white-space: normal;
}
table.admin_table tbody tr:nth-child(2n) {
    background-color: #F8F8F8;
}
 </style>
