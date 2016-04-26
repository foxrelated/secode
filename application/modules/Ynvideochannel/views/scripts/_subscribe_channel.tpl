<?php $item = $this -> item ?>
<div class="ynvideochannel_subscribe_button" onclick="subscribeChannel(this, '<?php echo $item -> getIdentity() ?>');">
    <?php if ($item->isSubscribed($this->user_id)): ?>
        <?php echo $this->translate('Unsubscribe') ?>
    <?php else: ?>
        <?php echo $this->translate('Subscribe') ?>
    <?php endif; ?>
</div>

<script type="text/javascript">
    function subscribeChannel(ele, channel_id) {
        var url = '<?php echo $this->url(array("action" => "subscribe", "channel_id" => ""),"ynvideochannel_channel", true);?>';
        var request = new Request.JSON({
            url : url,
            data : {
                id: channel_id
            },
            onSuccess: function(responseJSON) {
                if (responseJSON.result) {
                    if (responseJSON.subscribed == 1) {
                        var html = '<?php echo $this->translate('Unsubscribe') ?>';
                        ele.innerHTML = html;
                    } else {
                        var html = '<?php echo $this->translate('Subscribe') ?>';
                        ele.innerHTML = html;
                    }
                    var subscriber_count = document.getElementById("ynvideochannel_subscriber_count_" + channel_id );
                    subscriber_count.innerHTML = responseJSON.count;
                }
            }
        });
        request.send();
    }
</script>