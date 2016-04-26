<div class="ynvideochannel_count_channels">
    <i class="fa fa-bookmark"></i>
    <?php $totalChannels = $this->paginator->getTotalItemCount();
        echo $totalChannels; ?>
    <?php echo $this->translate('Channels'); ?>
</div>
<?php
if ($totalChannels > 0):?>
    <ul class="ynvideochannel_manage_channel_items">
        <?php foreach ($this->paginator as $channel):
                echo $this->partial('_channel_manage_item.tpl', array('item' => $channel, 'manageType' => 'channel'));
        endforeach;?>
    </ul>
    <?php
        echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues
        ));
else: ?>
    <div class="tip">
        <?php echo $this->translate('No channels found.');?>
    </div>
<?php endif; ?>

<script type="text/javascript">
    window.addEvent('domready', function() {
        $$('.ynvideochannel_main_manage').getParent().addClass('active');
    });
</script>

