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
    vertical-align: middle;
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
    <?php echo $this->translate("Manage Finance Accounts") ?>      
</div>
<div class="table">
<?php if (count($this->accounts)>0):?>
     <table>
        <thead>
            <td style="text-align:center" align="center">User ID</td>
            <td style="text-align:center" align="center">User Account</td>
            <td style="text-align:center" align="center">Payment Account</td>
            <td style="text-align:center" align="center">Total Amount(<?php echo $this->currency;?>)</td>
        </thead>
        <tbody>
            <?php foreach ($this->accounts as $acc):
            	$user = Engine_Api::_() -> getItem('user', $acc->user_id);?>
             <tr>
                <td class="stat_number" style="color: red;text-align:center" align="center" ><?php echo $acc->user_id; ?> </td>
                <td class="stat_number" style="color: red;text-align:center" align="center" >
                <?php echo $this->htmlLink(
                           $this->url(array('id'=>$user->user_id,'username'=>$user->getTitle()), 'mp3music_cart_viewtransaction'),
                            $user->getTitle(),
                            array('class'=>'smoothbox') );   ?>  
                            </td>
               
                <td class="stat_number" style="color: red;text-align:center" align="center" > <?php echo $acc->account_username; ?> </td>
                <td class="stat_number" style="color: red;text-align:center" align="center" > <?php echo $acc->total_amount; ?> </td>
            </tr>
           
           <?php endforeach; ?>
            
        </tbody>
        <tfoot>
            <td colspan="7">&nbsp;</td>
        </tfoot>
    </table>   
<?php echo  $this->paginationControl($this->accounts); ?>
<?php else: ?>
   <?php echo $this->translate("There are no finance accounts.") ?>                 
<?php endif; ?>
</div>