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
<?php
if($this->deal->user_id != $this->viewer_id):?>
<div class="tip" style="clear: inherit;">
      <span>
<?php echo $this->translate('You can not view this page !');?>
 </span>
           <div style="clear: both;"></div>
    </div>
<?php return; endif; ?>
  <h3>
    <?php 
        echo $this->translate('Statistics of '); ?>
        <a href="<?php echo $this->deal->getHref()?>"><?php echo $this->deal->title ?></a>
  </h3>
  <br/>
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
<?php if(count($this->statistics) > 0): ?>   
  <table class='admin_table' style = "width: 100%;">
    <thead>
  <tr>
    <th style="text-align:center"><?php echo $this->translate('Trans. ID');?>  </th>
    <th style="text-align:center; "><?php echo $this->translate('Date');?>  </th>
    <!-- <th style="text-align:center;"><?php echo $this->translate('Name of Deal');?>  </th>-->
  <!--    <th style="text-align:center"><?php echo $this->translate('Price');?>  </th> -->
    <!--  <th style="text-align:center"><?php echo $this->translate('Quantity');?>  </th> -->
    <!--  <th style="text-align:center"><?php echo $this->translate("Total Amount") ?></th> -->
        <th style="text-align:center"><?php echo $this->translate('Buyer');?>  </th>
   <!--   <th style="text-align:center"><?php echo $this->translate('Buyer Info');?>  </th> -->
    <th style="text-align:center"><?php echo $this->translate('Type Tracking');?>  </th>
    <th style="text-align:center"><?php echo $this->translate('Status');?>  </th>
    <th style="text-align:center"><?php echo $this->translate('Coupon Code');?>  </th>
    <th style="text-align:center"><?php echo $this->translate('Coupon Status');?>  </th>
</tr>
  </thead>
    <tbody>
<?php foreach($this->statistics as $track):
		if ($track->code == "") {
			continue;
		}?>
 <tr>
    <td style="text-align:center"><?php 
        echo $track->tranid; ?> </td>
    <td style="text-align:center"><?php 
        echo $this->locale()->toDateTime($track->pDate); ?> </td>
    
   <!--   <td style="text-align:center;"><?php echo $this->deal->title ?> </td>-->
   <!-- <td style="text-align:center"><?php 
    if($this->deal->price > 0):
        echo number_format($this->deal->price,2);
    else:
         echo $track['amount'];
    endif;
    ?> </td> -->
    <!-- <td style="text-align:center"> <?php echo $track->number;?> </td>  -->
    <!-- <td style="text-align:center"> <?php echo $track['amount'];?> </td> -->
    <td style="text-align:left">
         <?php $buyer = Engine_Api::_()->getItem('user',$track->user_buyer); ?>
         <?php echo $buyer; ?> 
         </td>
        <!--   <td style="text-align:center"><?php 
         if ($track->address != "") :
         echo $this->translate('Address: ').$track->address.'<br />';
         echo $this->translate('Email: ').$track->email.'<br />';
         echo $this->translate('Phone: ').$track->phone.'<br />';
         else :
         echo "N/A"; endif;
         ?> </td> --> 
    <td style="text-align:center"><?php echo $this->translate($track->params);?> </td>  
    <td style="text-align:center"><?php if ($track->transaction_status == 1): echo $this->translate('Successfully');  else:  echo $this->translate('Fail'); endif; ?> </td>
 	<?php if ($track->code_status == 'Used') :?>
 	<td style="text-align:center; text-decoration:line-through;"><?php echo $track->code;?> </td>
 	<td style="text-align:center">
 			<a class='smoothbox' href='<?php echo $this->url(array('action' => 'editcoupon', 'id' => $track->coupon_id, 'status' => 'Unused'),'groupbuy_general',true);?>'>
                <?php echo $this->translate("Unused") ?>
              </a>
              |
              <a class='smoothbox' href='<?php echo $this->url(array('action' => 'editcoupon', 'id' => $track->coupon_id, 'status' => 'Expired'),'groupbuy_general',true);?>'>
                <?php echo $this->translate("Expired") ?>
              </a>
    </td>
 	<?php elseif ($track->code_status == 'Expired') :?>  
 	<td style="text-align:center; color: #FF0000; text-decoration:line-through;"><?php echo $track->code;?> </td>
 	<td style="text-align:center">
 			 <a class='smoothbox' href='<?php echo $this->url(array('action' => 'editcoupon', 'id' => $track->coupon_id, 'status' => 'Used'),'groupbuy_general',true);?>'>
                <?php echo $this->translate("Used") ?>
              </a>
              |
              <a class='smoothbox' href='<?php echo $this->url(array('action' => 'editcoupon', 'id' => $track->coupon_id, 'status' => 'Unused'),'groupbuy_general',true);?>'>
                <?php echo $this->translate("Unused") ?>
              </a>
    </td>
 	<?php else :?>  
    <td style="text-align:center"><?php echo $track->code;?> </td>
 	<td style="text-align:center">
 			 <a class='smoothbox' href='<?php echo $this->url(array('action' => 'editcoupon', 'id' => $track->coupon_id, 'status' => 'Used'),'groupbuy_general',true);?>'>
                <?php echo $this->translate("Used") ?>
              </a>
              |
              <a class='smoothbox' href='<?php echo $this->url(array('action' => 'editcoupon', 'id' => $track->coupon_id, 'status' => 'Expired'),'groupbuy_general',true);?>'>
                <?php echo $this->translate("Expired") ?>
              </a>
    </td>
 	<?php endif;?>

</tr>
                           
 <?php endforeach; ?>
     </tbody>
</table>  
<?php  echo $this->paginationControl($this->statistics, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->values,
    ));     ?>  
    <div class="clear"></div>
<?php else: ?>
 <div class="tip">
    <span>
      <?php echo $this->translate("No statistic found with that criteria.") ?>
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
}
table.admin_table tbody tr td {
    border-bottom: 1px solid #EEEEEE;
    font-size: 0.9em;
    padding: 7px 10px;
    vertical-align: top;
    white-space: normal;
}
 </style>
 <style type="text/css">
#global_page_groupbuy-index-statistic div.admin_search div.form-elements {
    height: 35px;
}
#global_page_groupbuy-index-statistic div.admin_search div.form-elements div.form-wrapper {
	float: left;
	margin-left: 15px;
    clear: none;
    margin-top: 0px;
}
#global_page_groupbuy-index-statistic .global_form_box .form-wrapper + .form-wrapper {
    margin-top: 0px;
}
#global_page_groupbuy-index-statistic div.buttons #minh {
   float: left;
    margin-left: 30px;
    margin-top: 7px;
}
.admin_search {
	margin-top: 10px;
	margin-bottom: 10px;
}
</style>