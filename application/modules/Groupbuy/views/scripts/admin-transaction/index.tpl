 <h2><?php echo $this->translate("Group Buy Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
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
</style>   
<h2>
   <?php echo $this->translate("Transaction Tracking") ?>
</h2>
<?php if($this->message):?>
 <div class="tip">
    <span>
      <?php echo $this->translate($this->message) ?>
    </span>
  </div>
<?php endif;?>
<div class='admin_search'>   
<?php  echo $this->form->render($this); ?>
</div>
<br /> 
<?php if(count($this->transtracking) > 0): ?>
<?php if($this->transtracking): ?>
    <table class="admin_table">
        <thead>
        <tr>
            <th style='width: 1%;'><?php echo $this->translate("Date") ?></th>
            <!--<td style="text-align:center;font-weight: bold;" align="center"><?php echo $this->translate("Seller") ?> </td>
            <td style="text-align:center;font-weight: bold;" align="center"><?php echo $this->translate("Buyer") ?></td> -->
            <th style='width: 1%;'><?php echo $this->translate("Item ID") ?></th>
             <th style='width: 1%;'><?php echo $this->translate('Type Tracking');?>  </th>
            <th style='width: 1%;'><?php echo $this->translate("Quantity") ?></th>
            <th style='width: 1%;'><?php echo $this->translate("Amount") ?></th>
            <th style='width: 1%;'><?php echo $this->translate("Payment To") ?></th>
            <th style='width: 1%;'><?php echo $this->translate("Payment From") ?></th>
            <th style='width: 1%;'><?php echo $this->translate("Status") ?></th>
         </tr>   
        </thead>
        <tbody>
            <?php foreach ($this->transtracking as $track):?>
            <tr>
                <td><?php echo $this->locale()->toDateTime($track['pDate']);?></td>
                <!--<td class="stat_number" style="text-align:center" align="center" > <?php if($track['seller_user_name']): echo $track['seller_user_name']; else: echo "N/A"; endif;?> </td>
                <td class="stat_number" style="text-align:center" align="center" > <?php if($track['buyer_user_name']): echo $track['buyer_user_name']; else: echo "N/A"; endif;?> </td> -->
                <td> <?php echo $track['item_id'];?></td>
                 <td><?php echo $track->params ?> </td> 
                 <td> <?php echo $track['number'];?> </td>
                <td> <?php echo $this->currencyadvgroup($track['amount'],$track['currency']);?> </td>
                <td> 
                <?php $seller = Engine_Api::_()->getItem('user',$track['user_seller']); ?>
                <?php echo $seller ?>
                </td>
                <td>
                <?php $buyer = Engine_Api::_()->getItem('user',$track['user_buyer']); ?>
                <?php echo $buyer ?>  
                </td>
                <td><?php if($track['transaction_status'] == 1): echo $this->translate('Successfully');  else:  echo $this->translate('Fail');  endif;?> </td>
            </tr>
           <?php endforeach;?> 
        </tbody>
    </table> 
  <?php  echo $this->paginationControl($this->transtracking, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->filterValues,
    ));     ?>  
   <div class="clear"></div>
   <?php endif; ?>
<?php else: ?>
 <div class="tip">
    <span>
      <?php echo $this->translate("No transaction found with that criteria.") ?>
    </span>
  </div>
<?php endif; ?>