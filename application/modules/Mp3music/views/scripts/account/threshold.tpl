<?php $this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');   
       ?>
<div id="request">
<div class="table" style="padding:10px; width: 100%">
                
<div class="table_left" style="font-weight: bold;"><?php echo $this->translate("Amount"); ?></div>
  <div class="table_right" style="margin-bottom: 10px;">
 <input type="text" name="txtrequest_money" id="txtrequest_money"/>
    </div>
<div class="table_left" style="font-weight: bold;"><?php echo $this->translate("Reason"); ?></div>
<div class="table_right"><textarea  name="textarea_request" id="textarea_request" style="height:60px; width: 250px;"></textarea></div>
</div>
<div class="table_clear">
<button  name="submit"  onclick="requestMoney()" ><?php echo $this->translate('Request money'); ?></button>
</div>
</div>