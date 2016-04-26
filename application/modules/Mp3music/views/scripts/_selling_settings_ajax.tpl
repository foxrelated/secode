<div class="table">   
         <div class="table_left">
            *<?php echo $this->translate("Method Payment") ?>     
        </div>
        <div class="table_right">
        <?php echo $this->translate("Select the method to pay for songs?") ?>
            <div class=""> 
                <input type="radio" <?php if ($this->settings['method_payment'] == 1): ?>checked <?php endif;?> name="val[method_payment]" id="val[method_payment]" value="1"/><?php echo $this->translate("The amount comes from users' purchase will add to social finance account.") ?>    <br/>
<!--                changed date 4_3_2011-->
<!--                <input type="radio" {if $settings.method_payment eq 2 }checked {/if} name="val[method_payment]" id="val[method_payment]" value="2"/>The second is the amount will direct pay to users after subtracting commission.<br/>
                <input type="radio" {if $settings.method_payment eq 3 }checked {/if} name="val[method_payment]" id="val[method_payment]" value="3"/>User can select the method to pay.<br/>-->
            </div>
        </div>
  <div class="clear"></div>        
</div>
<div class="table">   
         <div class="table_left">
            *<?php echo $this->translate("Music Seller") ?>      
        </div>
        <div class="table_right">
        <?php echo $this->translate("Allow users to sell songs?") ?>
            <div class="item_is_active_holder"> 
            
                <span class="js_item_active item_is_active"><input type="radio" name="val[can_sell_song]" value="1" <?php if ($this->settings['can_sell_song'] == 1):?> checked <?php endif;?>/> <?php echo $this->translate("Yes"); ?></span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="val[can_sell_song]" value="0" <?php if ($this->settings['can_sell_song'] == 0): ?>checked <?php  endif;?>/> <?php echo $this->translate("No"); ?></span>
            </div>
        </div>
  <div class="clear"></div>        
</div>

<div class="table">   
         <div class="table_left">
             *<?php echo $this->translate("Music Buyer") ?>       
        </div>
        <div class="table_right">
        <?php echo $this->translate("Allow users to buy songs?"); ?>
        <div class="item_is_active_holder"> 
                <span class="js_item_active item_is_active"><input type="radio" name="val[can_buy_song]" value="1" <?php if ($this->settings['can_buy_song'] == 1):?> checked <?php endif;?>/> <?php echo $this->translate("Yes"); ?></span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="val[can_buy_song]" value="0" <?php if ($this->settings['can_buy_song'] == 0): ?>checked <?php  endif;?>/> <?php echo $this->translate("No"); ?></span>
            </div>
        </div>
  <div class="clear"></div>        
</div>
<div class="table">   
        <div class="table_left">
            *<?php echo $this->translate("Maximum of Payout") ?>
        </div>
        <div class="table_right">
           <?php echo $this->translate("Maximum of Payout") ?>  
            <div class="item_is_active_holder"> 
               <input type="text" value="<?php echo $this->settings['max_payout'];?>" name="val[max_payout]" id="max_payout"/> <?php echo $this->currency;?>
            </div>
        </div>
          <div class="clear"></div>        
</div>
<div class="table">   
        <div class="table_left">
            *<?php echo $this->translate("Minimum  of Payout") ?>
        </div>
        <div class="table_right">
           <?php echo $this->translate("Minimum  of Payout") ?> 
            <div class="item_is_active_holder"> 
               <input type="text" value="<?php echo $this->settings['min_payout'];?>" name="val[min_payout]" id="min_payout"/> <?php echo $this->currency;?>
            </div>
        </div>
          <div class="clear"></div>        
</div>
<div class="table">   
        <div class="table_left">
            *<?php echo $this->translate("Minimum price of album") ?>
        </div>
        <div class="table_right">
          <?php echo $this->translate("Minimum price of album") ?>    
            <div class="item_is_active_holder"> 
               <input type="text" value="<?php echo $this->settings['min_price_album'];?>" name="val[min_price_album]" id="min_price_album"/>  <?php echo $this->currency;?>  
            </div>
        </div>
          <div class="clear"></div>        
</div>
<div class="table">   
        <div class="table_left">
            *<?php echo $this->translate("Minimum price of song") ?>
        </div>
        <div class="table_right">
          <?php echo $this->translate("Minimum price of song") ?>    
            <div class="item_is_active_holder"> 
               <input type="text" value="<?php echo $this->settings['min_price_song'];?>" name="val[min_price_song]" id="min_price_song"/>  <?php echo $this->currency;?>  
            </div>
        </div>
          <div class="clear"></div>        
</div>

<div class="table">   
        <div class="table_left">
            *<?php echo $this->translate("Commission Fee") ?>
        </div>
        <div class="table_right">
          <?php echo $this->translate("Commission Fee") ?>  
            <div class="item_is_active_holder"> 
               <input type="text" value="<?php echo $this->settings['comission_fee'] ; ?>" name="val[comission_fee]" id="comission_fee"/>%
            </div>
        </div>
          <div class="clear"></div>        
</div>
<div class="table">   
         <div class="table_left">
         *<?php echo $this->translate("Who pays the fee?") ?>
        </div>
        <div class="table_right">
        <?php echo $this->translate("Select person who pay the fee for paypal?") ?>     
            <div class=""> 
               <!-- <input type="radio" {if $settings.who_payment eq 1 }checked {/if} name="val[who_payment]" id="val[who_payment]" value="1"/>The PRIMARY RECEIVERS will pay this fee.<br/>-->
                <!--<input type="radio" {if $settings.who_payment eq 2 }checked {/if} name="val[who_payment]" id="val[who_payment]" value="2"/>The SENDER will pay this fee.<br/>-->
                <input type="radio" checked name="val[who_payment]" id="val[who_payment]" value="3"/>All RECEIVERS will pay this fee.<br/>
            </div>
        </div>
  <div class="clear"></div>        
</div>