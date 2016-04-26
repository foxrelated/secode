<?php if($this->exist):?>
<div>
<ul class="form-errors">
    <li>
        <ul class="errors"><li><?php echo $this->translate('You have already shared this channel.')?></li></ul>
    </li>
</ul>
</div>
<?php endif?>
<?php if($this->inValid):?>
<div>
    <ul class="form-errors">
        <li>
            <ul class="errors"><li><?php echo $this->translate('Please provide a valid URL for your channel.')?></li></ul>
        </li>
    </ul>
</div>
<?php endif?>
<div>
    <label><?php echo $this -> translate("Channel URL")?></label>
    <input id="channel_url" name = "channel_url" value="<?php echo $this -> url?>">
    <span class="buttonlink" onclick="getChannel()"><?php echo $this -> translate("Get Channel")?></span>
</div>
<div>
    <label><?php echo $this -> translate("Keywords")?></label>
    <input name = "keyword" id = "keyword" value="<?php echo $this -> keyword?>">
    <span class="buttonlink" onclick="findChannel()"><?php echo $this -> translate("Find Channels")?></span>
</div>

<script type="text/javascript">
    var getChannel = function()
    {
        var channel_url = $('channel_url').value;
        window.location = "<?php echo $this -> url(array('action' => 'get-channel'), 'ynvideochannel_general', true)?>" + "?channel_url=" + channel_url;
    }
    var findChannel = function()
    {
        var keyword = $('keyword').value;
        window.location = "<?php echo $this -> url(array('action' => 'find-channel'), 'ynvideochannel_general', true)?>" + "?keyword=" + keyword;
    }
</script>