 <h2><?php echo $this->translate("Mp3 Music Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<style type="text/css">
.table {
padding-bottom: 10px;
}
.table_bottom {
    border-top: medium none;
}
.table_clear, .table_bottom {
    background: none repeat scroll 0 0 #DFE4EE;
    border: 1px solid #DFE4EE;
    line-height: 32px;
    padding: 4px;
    position: relative;
    text-align: right;
}
.table_left {
    float: left;
    padding: 5px;
    position: relative;
    text-align: right;
    width: 25%;
    font-weight: bold;
}
.table_right {
    background: none repeat scroll 0 0 #FFFFFF;
    margin-left: 26%;
    padding: 5px;
}
div.message {
    background: none repeat scroll 0 0 #FEFBD9;
    border: 1px solid #EEE9B5;
    color: #6B6B6B;
    font-size: 10pt;
    font-weight: bold;
    margin: 4px;
    padding: 4px;
    position: relative;
}
.table_header {
    background: none repeat scroll 0 0 #495A77;
    color: #FFFFFF;
    font-size: 11pt;
    font-weight: bold;
    padding: 5px;
}
table {
   -moz-border-bottom-colors: none;
    -moz-border-image: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    background: none repeat scroll 0 0 #EFEFEF;
    border-color: #CCCCCC -moz-use-text-color #CCCCCC #CCCCCC;
    border-style: solid none solid solid;
    border-width: 1px medium 1px 1px;
    width: 100%;
}
.item_is_active_holder {
    height: 30px;
    position: relative;
}
.item_is_active {
    background: none repeat scroll 0 0 #E3F6E5;
    border: 1px solid #B4E3B9;
    cursor: default;
    left: 0;
}
.item_is_active, .item_is_not_active {
    display: block;
    padding: 4px 8px 4px 4px;
    position: absolute;
    width: 50px;
}
.item_is_not_active {
    background: none repeat scroll 0 0 #F6E3E3;
    border: 1px solid #E3B4B4;
    cursor: default;
    left: 0;
    margin-left: 64px;
}
.button {
    background: none repeat scroll 0 0 #FFFFFF;
    border: 1px solid #B2B2B2;
    color: #2D2E30;
    cursor: pointer;
    font-size: 9pt;
    margin: 0;
    padding: 4px;
    font-weight: bold;
    vertical-align: middle;
}
.button:hover
{
    border: 1px solid #495A77;
}
td {
    border-right: 1px solid #CCCCCC;
    padding: 6px 4px;
    vertical-align: top;
}
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {  
 display: table;
}
</style>   
<div class="table_header">
  <?php echo $this->translate("Number Details") ?>
</div>

<div class="filter" style="margin:10px;">
<span>
<form action="" method="post">
     <?php echo $this->translate("From") ?> <input type="text" value="<?php echo $this->fromDate;?>" name="fromDate" id="fromDate"/> <?php echo $this->translate("To") ?> <input type="text" value="<?php echo $this->toDate;?>" name="toDate" id="toDate"/>
     <?php echo $this->translate("Date Format (YYYY-MM-DD)") ?>
    <input type="submit" value="Filter " class="button" name="filter_stat" style=""/>
    
</form>
</span>
</div>
<div class="table">
    <table>
        <thead>
            <td style="text-align:center" align="center"><?php echo $this->translate("Date") ?></td>
            <td style="text-align:center" align="center"><?php echo $this->translate("Sold Songs") ?></td>
            <td style="text-align:center" align="center"><?php echo $this->translate("Sold Albums") ?></td>
            <td style="text-align:center" align="center"><?php echo $this->translate("Successful Transactions") ?></td>
            <td style="text-align:center" align="center"><?php echo $this->translate("Fail Transactions") ?></td>
            <td style="text-align:center" align="center"><?php echo $this->translate("Total Amount") ?></td>
            
        </thead>
        <tbody>
            
            <?php foreach($this->histories  as $his): ?>
            <tr style="border:1px solid">
                <td class="stat_number" style=" text-align:center" align="center" ><?php echo $his['pDate'];?> </td>
                <td class="stat_number" style=" text-align:center" align="center" ><?php echo $his['selling_sold_songs'];?> </td>
                <td class="stat_number" style=" text-align:center" align="center" ><?php echo $his['selling_sold_albums'];?> </td>
                <td class="stat_number" style=" text-align:center" align="center" ><?php echo $his['selling_transaction_succ'];?>  </td>
                <td class="stat_number" style=" text-align:center" align="center" ><?php echo $his['selling_transaction_fail'];?> </td>
                <td class="stat_number" style=" text-align:center" align="center" ><?php echo $his['selling_total_amount'];?>  </td>
            </tr>
           <?php endforeach;?>
           <tr style="border:1px solid">
                <td class="stat_number" style="text-align:center" align="center" >Total </td>
                <td class="stat_number" style="text-align:center" align="center" ><?php echo $this->totalHistories['sold_song'];?> </td>
                <td class="stat_number" style="text-align:center" align="center" > <?php echo $this->totalHistories['sold_album'];?>  </td>
                <td class="stat_number" style="text-align:center" align="center" > <?php echo $this->totalHistories['transaction_succ'];?> </td>
                <td class="stat_number" style="text-align:center" align="center" > <?php echo $this->totalHistories['transaction_fail'];?> </td>
                <td class="stat_number" style="text-align:center" align="center" > <?php echo $this->totalHistories['total_amount'];?> </td>
            </tr> 
        </tbody>
    </table>   
   <div class="clear"></div>
   <div class="p_4"></div>
</div>

<div class="table_header">
   <?php echo $this->translate("Transaction Tracking") ?>
</div>
<div class="filter" style="margin:10px;">
<span>
<form action="" method="get">
     <?php echo $this->translate("From") ?> <input type="text" value="<?php echo $this->fromDateTracking;?>" name="fromDateTracking" id="fromDateTracking"/> <?php echo $this->translate("To") ?> <input type="text" value="<?php echo $this->toDateTracking;?>" name="toDateTracking" id="toDateTracking"/>
    <?php echo $this->translate("Date Format (YYYY-MM-DD)") ?> 
    <input type="submit" value="Filter " class="button" name="filter_tracking" style=""/>
</form>
</span>
</div>

<div class="table">
    <table>
        <thead>
            <td style="text-align:center" align="center"><?php echo $this->translate("Date") ?></td>
            <td style="text-align:center" align="center"><?php echo $this->translate("Seller") ?> </td>
            <td style="text-align:center" align="center"><?php echo $this->translate("Buyer") ?></td>
            <td style="text-align:center" align="center"><?php echo $this->translate("Item ID") ?></td>
            <td style="text-align:center" align="center"><?php echo $this->translate("Item Type") ?></td>
            <td style="text-align:center" align="center"><?php echo $this->translate("Amount") ?></td>
            <td style="text-align:center" align="center"><?php echo $this->translate("Account Seller") ?></td>
            <td style="text-align:center" align="center"><?php echo $this->translate("Account Buyer") ?></td>
            <td style="text-align:center" align="center"><?php echo $this->translate("Status") ?></td>
            
        </thead>
        <tbody>
            <?php foreach ($this->transtracking as $track):?>
            <tr style="border:1px solid">
                <td class="stat_number" style="text-align:center" align="center" ><?php echo $track['pDate'];?></td>
                <td class="stat_number" style="text-align:center" align="center" > <?php if($track['seller_user_name']): echo $track['seller_user_name']; else: echo "N/A"; endif;?> </td>
                <td class="stat_number" style="text-align:center" align="center" > <?php if($track['buyer_user_name']): echo $track['buyer_user_name']; else: echo "N/A"; endif;?> </td>
                <td class="stat_number" style="text-align:center" align="center" > <?php echo $track['item_id'];?></td>
                <td class="stat_number" style="text-align:center" align="center" > <?php if($track['item_type']): echo $track['item_type']; else: echo "N/A"; endif;?></td>
                <td class="stat_number" style="text-align:center" align="center" > <?php echo $track['amount'];?> </td>
                <td class="stat_number" style="text-align:center" align="center" > <?php if($track['account_seller_email']): echo $track['account_seller_email'];else: echo "N/A";  endif;?> </td>
                <td class="stat_number" style="text-align:center" align="center" > <?php if($track['account_buyer_email']): echo $track['account_buyer_email'];else: echo "N/A"; endif;?> </td>
                <td class="stat_number" style="text-align:center" align="center" ><?php if($track['transaction_status'] == 1): ?>Succ <?php else: ?> Fail <?php endif;?> </td>
            </tr>
           <?php endforeach;?> 
        </tbody>
    </table> 
  <?php  echo $this->paginationControl($this->transtracking, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>  
   <div class="clear"></div>
</div>
