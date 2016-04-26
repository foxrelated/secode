<?php
$item = $this -> video;
$viewer = $this -> viewer();
?>

<?php if (($item->authorization()->isAllowed($viewer, 'edit')) || ($item->authorization()->isAllowed($viewer, 'delete'))):?>
<div class="ynvideochannel_video_options">
    <span class="ynvideochannel_video_options-btn"><i class="fa fa-cog" aria-hidden="true"></i></span>
    <ul class="ynvideochannel_video_options-block">
    <?php if ($item->authorization()->isAllowed($viewer, 'edit')):?>
        <li>
            <?php
            echo $this->htmlLink(array(
            'route' => 'ynvideochannel_video',
            'action' => 'edit',
            'video_id' => $item->getIdentity(),
            ), '<i class="fa fa-pencil-square-o"></i>'.$this->translate('Edit video'), array('class' => 'icon_ynvideochannel_edit'));
            ?>
        </li>
        <?php endif ?>
        <?php if ($item->authorization()->isAllowed($viewer, 'delete')):?>
        <li>
            <?php
            echo $this->htmlLink(array(
            'route' => 'ynvideochannel_video',
            'action' => 'delete',
            'video_id' => $item->getIdentity(),
            'format' => 'smoothbox'
            ), '<i class="fa fa-trash"></i>'.$this->translate('Delete video'), array('class' => 'smoothbox icon_ynvideochannel_delete'));
            ?>
        </li>
    <?php endif ?>
    </ul>
</div>
<?php endif ?>

<script type="text/javascript">
    var parent_active = $$('.ynvideochannel_videos_grid-item').length;
    $$('.ynvideochannel_video_options-btn').removeEvents('click').addEvent('click', function() {
        this.getParent('.ynvideochannel_video_options').toggleClass('explained');
        if(parent_active){
            this.getParents('.ynvideochannel_videos_grid-item').toggleClass('ynvideochannel-options-active');
        }
    });

    $$('.ynvideochannel_video_options-btn').addEvent('outerClick',function(){
        var popup = this.getParent('.ynvideochannel_video_options');
        var parent_popup = this.getParents('.ynvideochannel_videos_grid-item'); //Add class for hover out scope not hidden.
        if (popup.hasClass('explained')){
            popup.removeClass('explained');
        }
        if (parent_popup.hasClass('ynvideochannel-options-active')){
            parent_popup.removeClass('ynvideochannel-options-active');
        }
    })

</script>