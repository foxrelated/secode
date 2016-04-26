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
<?php if($this->message != ''):   ?>
<div class = "message"><?php echo $this->message;?>  </div>
<?php endif; ?>   
<div class="table_header">
    <?php echo $this->translate("Paypal API  Information") ?>      
</div>
<form action="" method="post">
<div class="table">   
        <div class="table_left">
            *<?php echo $this->translate("Email Account") ?>      
        </div>
        <div class="table_right">
             <div class="item_is_active_holder"> 
               <input type="text" value="<?php echo @$this->paypal['admin_account'];?>" name="val[admin_account]" id="admin_account" size="60"/> 
            </div>
        </div>
        <div class="clear"></div>        
</div>
 <div class="table_bottom">
        <input type="submit" value="Save Changes" class="button" name ="save_api_paypal" id="save_api_paypal" onclick=""/>
        
    </div>
 </form>