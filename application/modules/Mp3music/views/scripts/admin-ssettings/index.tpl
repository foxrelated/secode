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
.table {
    -moz-border-bottom-colors: none;
    -moz-border-image: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    background: none repeat scroll 0 0 #FAFBFC;
    border-color: #DFE4EE #DFE4EE -moz-use-text-color;
    border-style: solid solid none;
    border-width: 1px 1px medium;
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
<?php echo $this->translate("Global Settings") ?>
</div>
<form method="post" id="member_global_settings" action="" onsubmit="return checkValidate();">  
<div class="table">   
         <div class="table_left">
            *<?php echo $this->translate("Enable Test Mode") ?>
        </div>
        <div class="table_right">
            <?php echo $this->translate("Allow admin to test Music Selling by using development mode?") ?> 
            <div class="item_is_active_holder">
                <span class="js_item_active item_is_active"><input type="radio" name="val[is_test_mode]" value="1" <?php if($this->settings['is_test_mode'] == 1):?> checked <?php endif;?>/> <?php echo $this->translate("Yes") ?></span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_test_mode]" value="0" <?php if($this->settings['is_test_mode'] == 0):?> checked <?php endif;?>/> <?php echo $this->translate("No") ?></span>
            </div>
        </div>
  <div class="clear"></div>        
</div>
<div class="table">   
        <div class="table_left">
        <?php echo $this->translate("Policy for Upload Music") ?>
        </div>
        <div class="table_right">
            <div class=""> 
               <textarea name="val[upload_message]" id="upload_message" cols="100" rows="10"><?php if($this->settings['upload_message'] != ""): echo $this->settings['upload_message']; else: echo 'Only post songs in which they have the right to sell, etc.'; endif;  ?></textarea>
            </div>
        </div>
          <div class="clear"></div>        
</div>
<div class="table">   
        <div class="table_left">
        <?php echo $this->translate("Policy of Music Selling") ?>
        </div>
        <div class="table_right">
        <?php echo $this->translate("It can be support some html common tags.") ?>  <pre>&lt;<span class="tag">p</span>&gt;&lt;/<span class="tag">p</span>&gt;  <?php echo $this->translate(" to add new line.") ?> </pre>
            <div class=""> 
               <textarea name="val[policy_message]" id="policy_message" cols="100" rows="10"><?php if($this->settings['policy_message'] != ""): echo $this->settings['policy_message']; else: echo 'Under the first title of the Treaty of the European<p>Communities one finds the provisions dealing with the free movement of goods. In the years between the two world wars, and leading into the Great Depression, governments around the world had employed vigorous policies of national protectionism. The erection of tariffs and customs duties on imports and sometimes the export of goods was widely seen as contributing to a fall in trade and hence the stalling of economic growth and development. Economists had long said, since Adam Smith and David Ricardo that the Wealth of Nations could only be strengthened by the long term lowering and abolition of barriers and costs to international trade. The abolition of all such barriers is the function of the treaty provisions. According to Article 28 EC,</p><p>"28. Quantitative restrictions on imports and all measures having equivalent effect shall be prohibited between Member States."Article 29 EC states the same for exports. The first thing to note is that the prohibition is simply between member states of the European Community. One of the institutions primary duties is the management of trade policy to third parties - other countries such as the United States, or China. For instance, the controversial Common Agricultural Policy is regulated under Title II EC, Article 34(1) authorising "compulsory coordination of national market organisations" with common European organisation. The second thing to note is that Article 30 sets out the exceptions to the prohibition on free movement of goods.</p><p>"30. The provisions of Articles 28 and 29 shall not preclude prohibitions or restrictions on imports, exports or goods in transit justified on grounds of public morality, public policy or public security; the protection of health and life of humans, animals or plants; the protection of national treasures possessing artistic, historic or archaeological value; or the protection of industrial and commercial property. Such prohibitions or restrictions shall not, however, constitute a means of arbitrary discrimination or a disguised restriction on trade between Member States."</p><p>So governments of member states may still justify certain trade barriers when public morality, policy, security, health, culture or industrial and commercial property might be threatened by complete abolition. One recent example of this was that during the mad cow disease crisis in the United Kingdom, France erected a barrier to imports of British beef.[31]</p>'; endif;  ?></textarea>
            </div>
        </div>
          <div class="clear"></div>        
</div>
<div class="table">   
        <div class="table_left">
        <?php echo $this->translate("Policy for request money from user") ?> 
        </div>
        <div class="table_right">
           <?php echo $this->translate("It can be support some html common tags.") ?> <pre>&lt;<span class="tag">p</span>&gt;&lt;/<span class="tag">p</span>&gt; <?php echo $this->translate(" to add new line.") ?></pre>
            <div class=""> 
               <textarea name="val[policy_message_request]" id="policy_message_request" cols="100" rows="8"><?php if($this->settings['policy_message_request'] != ""): echo $this->settings['policy_message_request']; else: echo 'Since the goal of the Treaty of Rome was to create a common market, and the Single European Act to create an internal market, it was crucial to ensure that the efforts of government could not be distorted by corporations abusing their market power. Hence under the treaties are provisions to ensure that free competition prevails, rather than cartels and monopolies sharing out markets and fixing prices. Competition law in the European Union is largely similar and inspired by United States antitrust.\r\n[edit] Collusion and cartels'; endif;  ?></textarea>
            </div>
        </div>
          <div class="clear"></div>        
</div>
 <div class="clear" style="clear: both;"></div>   
    <div class="table_bottom">
        <input type="submit" value="Save Changes" class="button" name ="save_change_global_setings" id="save_change_group_setting" onclick=""/>
    </div>
    
</form>
 <div class="clear"></div>   
<div class="table_header">
 <?php echo $this->translate("User Group Settings") ?>      
</div>
</br>
<p><?php echo $this->translate("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.") ?> </p>
</br> 
<form method="post" id="member_level_settings" action="" onsubmit="return checkValidate();">
     <div class="table">
        <div class="table_left">
           <?php echo $this->translate("Group Member") ?>
        </div>
        <div class="table_right">
            <select id="select_group_member" name="val[select_group_member]" onchange="loadGroupSetting(this.value)">
            <?php foreach($this->group_members as $gr): 
            if($gr['level_id'] != 5):
            ?>
                <option value="<?php echo $gr['level_id'];?>" <?php if($this->default_view_group == $gr['level_id']):?>selected<?php endif;?> > <?php echo $gr['title'] ?></option>                
            <?php endif; endforeach; ?>
            </select>
            <span id="loading"></span>
        </div>
        <div class="clear"></div>
        <div id="div_settings"></div>     
    </div>
    <div class="table_bottom">
       <input type="submit" value="Save Changes" class="button" name ="save_change_group_setting" id="save_change_group_setting" onclick=""/>
        
    </div>
    
</form>
<script type="text/javascript">
        function loadGroupSetting(value) 
        {
            
            document.getElementById('loading').innerHTML = '<img src="./application/modules/Mp3music/externals/images/ajax-loader.gif"/>';
            document.getElementById('div_settings').innerHTML = '';
            var makeRequest = new Request(
            {
                url: en4.core.baseUrl +  "mp3music/cart/load-settings-cart/user_group_id/"+value,
                onComplete: function (respone){
                     document.getElementById('div_settings').innerHTML =  respone; 
                     document.getElementById('loading').innerHTML = '';
                }
            }
            )
            makeRequest.send();
        }
     loadGroupSetting(<?php echo $this->default_view_group;?>);
     function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
     }

    function checkValidate()
    {
        var comission_fee =  document.getElementById('comission_fee').value;
        var max_payout = document.getElementById('max_payout').value;
        var min_payout = document.getElementById('min_payout').value;
        var min_price_song = document.getElementById('min_price_song').value;
        if (!isNumber(comission_fee) || comission_fee <0) {
          alert('The commission fee number is invalid');
          return false;
        }
        if (!isNumber(max_payout) || (max_payout <= 0 && max_payout!=-1)) {
          alert('The maximum payout  is invalid');
          return false;
        }
        if (!isNumber(min_payout) || min_payout <= 0) {
          alert('The minimum payout is invalid');
          return false;
        }
        if (!isNumber(min_price_song) || min_price_song < 0) {
          alert('The minimum price of song is invalid');
          return false;
        }
        return true;
    } 
</script>

