<?php
$item = $this -> channel;
$viewer = $this -> viewer();
?>
<div class="ynvideochannel_channel_options">
    <span class="ynvideochannel_channel_options-btn"><i class="fa fa-cog" aria-hidden="true"></i></span>
    <ul class="ynvideochannel_channel_options-block">
        <?php if ($item->authorization()->isAllowed($viewer, 'edit')):?>
            <?php if(Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynvideochannel_video', null, 'create')->checkRequire()):?>
            <li>
                <a onclick="autoUpdateChannel(this, '<?php echo $item -> getIdentity() ?>');">
                    <?php if ($item->isAutoUpdate()): ?>
                        <i class="fa fa-ban"></i><?php echo $this->translate('Stop auto update') ?>
                    <?php else: ?>
                        <i class="fa fa-play-circle"></i><?php echo $this->translate('Auto update') ?>
                    <?php endif; ?>
                </a>
            </li>

            <li>
                <?php
                    echo $this->htmlLink(array(
                    'route' => 'ynvideochannel_channel',
                    'action' => 'add-more-videos',
                    'channel_id' => $item->getIdentity(),
                    'format' => 'smoothbox'
                    ), '<i class="fa fa-trash"></i>'.$this->translate('Add more videos'), array('class' => 'smoothbox'));
                ?>
            </li>
            <?php endif;?>
            <li>
                <?php
                    echo $this->htmlLink(array(
                    'route' => 'ynvideochannel_channel',
                    'action' => 'edit',
                    'channel_id' => $item->getIdentity(),
                    ), '<i class="fa fa-pencil-square-o"></i>'.$this->translate('Edit channel'), array('class' => 'icon_ynvideochannel_edit'));
                   ?>
            </li>
            <?php endif;?>
            <?php if ($item->authorization()->isAllowed($viewer, 'delete')):?>
            <li>
                <?php
                    echo $this->htmlLink(array(
                    'route' => 'ynvideochannel_channel',
                    'action' => 'delete',
                    'channel_id' => $item->getIdentity(),
                    'format' => 'smoothbox'
                    ), '<i class="fa fa-trash"></i>'.$this->translate('Delete channel'), array('class' => 'smoothbox icon_ynvideochannel_delete'));
                  ?>
            </li>
        <?php endif;?>
    </ul>
</div>

<script type="text/javascript">
    function autoUpdateChannel(ele, channel_id) {
        var url = '<?php echo $this->url(array("action" => "auto-update", "channel_id" => ""),"ynvideochannel_channel", true);?>';
        var request = new Request.JSON({
            url : url,
            data : {
                id: channel_id
            },
            onSuccess: function(responseJSON) {
                if (responseJSON.result) {
                    if (responseJSON.autoUpdated == 1) {
                        var html = '<i class="fa fa-ban"></i>' + ' ' + '<?php echo $this->translate('Stop auto update') ?>';
                        ele.innerHTML = html;
                    } else {
                        var html = '<i class="fa fa-play-circle"></i>' + ' ' + '<?php echo $this->translate('Auto update') ?>';
                        ele.innerHTML = html;
                    }
                    var messgae =  responseJSON.message;
                    alert(messgae);
                }
            }
        });
        request.send();
    }

    $$('.ynvideochannel_channel_options-btn').removeEvents('click').addEvent('click', function() {
        this.getParent('.ynvideochannel_channel_options').toggleClass('explained');

        //z-index hight => overlay dropdown
        $$('.ms-nav-next').toggle(); 
    });

    $$('.ynvideochannel_channel_options-btn').addEvent('outerClick',function(){
        var popup = this.getParent('.ynvideochannel_channel_options');
        if (popup.hasClass('explained')){
            popup.removeClass('explained');

            //z-index hight => overlay dropdown
            $$('.ms-nav-next').setStyle('display','block'); 
        }
    })
</script>
















