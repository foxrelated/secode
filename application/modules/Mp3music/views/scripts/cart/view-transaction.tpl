<div style="height:500px; width:400px;">
  <h3>
    <?php 
    if($this ->user_name == "null"):
        echo $this->translate('Transaction History');
    else:
        echo $this->translate('Transaction History of '); echo $this->user_name;
    endif;
    ?>
  </h3>

<table cellpadding="0" cellspacing="0" width="100%">
    <tr>
         <td style="border: 1px solid black;text-align:center" align="center"><?php echo $this->translate('Date');?>  </td>
         <td style="border: 1px solid black;text-align:center" align="center"><?php echo $this->translate('Seller');?>  </td>
         <td style="border: 1px solid black;text-align:center" align="center"><?php echo $this->translate('Buyer');?>  </td>
         <td style="border: 1px solid black;text-align:center" align="center"><?php echo $this->translate('Item ID');?>  </td>
         <td style="border: 1px solid black;text-align:center" align="center"><?php echo $this->translate('Item Type');?>  </td>
         <td style="border: 1px solid black;text-align:center" align="center"><?php echo $this->translate('Amount');?>  </td>
         <td style="border: 1px solid black;text-align:center" align="center"><?php echo $this->translate('Account Seller');?>  </td>
         <td style="border: 1px solid black;text-align:center" align="center"><?php echo $this->translate('Account Buyer');?>  </td>
         <td style="border: 1px solid black;text-align:center" align="center"><?php echo $this->translate('Type Tracking');?>  </td>
         <td style="border: 1px solid black;text-align:center" align="center"><?php echo $this->translate('Status');?>  </td>

    </tr>
 <?php foreach($this->history as $track):?>
    <tr style="border:1px solid">
        <td class="stat_number" style="border: 1px solid black;text-align:center" align="center" ><?php echo $track->pDate ?> </td>
        <td class="stat_number" style="border: 1px solid black;text-align:center" align="center" ><?php if($track->seller_user_name): echo $track->seller_user_name; else: echo "N/A"; endif;?> </td>
        <td class="stat_number" style="border: 1px solid black;text-align:center" align="center" ><?php if($track->buyer_user_name): echo $track->buyer_user_name; else: echo "N/A"; endif;?></td>
        <td class="stat_number" style="border: 1px solid black;text-align:center" align="center" ><?php echo $track->item_id ?> </td>
        <td class="stat_number" style="border: 1px solid black;text-align:center" align="center" ><?php if($track->item_type): echo $track->item_type; else: echo "N/A"; endif;?> </td>
        <td class="stat_number" style="border: 1px solid black;text-align:center" align="center" ><?php echo $track->amount ?></td>
        <td class="stat_number" style="border: 1px solid black;text-align:center" align="center" ><?php if($track->account_seller_email): echo $track->account_seller_email; else: echo "N/A"; endif; ?> </td>  
        <td class="stat_number" style="border: 1px solid black;text-align:center" align="center" ><?php if($track->account_buyer_email): echo $track->account_buyer_email; else: echo "N/A"; endif; ?> </td>          
        <td class="stat_number" style="border: 1px solid black;text-align:center" align="center" ><?php echo $this->translate($track->params) ?> </td>
        <td class="stat_number" style="border: 1px solid black;text-align:center" align="center" ><?php if ($track->transaction_status == 1): echo $this->translate('Succ'); else: echo $this->translate('Fail'); endif; ?> </td>
    </tr>
   
   <?php endforeach; ?>
</table>  
</div>
 