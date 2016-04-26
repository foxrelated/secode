<script type="text/javascript">
    window.addEvent('domready', function() {
        $$('.ynvideochannel_main_manage').getParent().addClass('active');
    });
</script>

<div class="ynvideochannel_count_videos">
    <i class="fa fa-bookmark"></i>
    <?php $totalItems = $this->paginator->getTotalItemCount();?>
    <?php echo $this -> translate(array("%s playlist", "%s playlists", $totalItems), $totalItems)?>
</div>
<?php
if ($totalItems > 0):?>
    <ul>
        <?php foreach ($this->paginator as $item):
            echo $this->partial('_playlist_item.tpl', array('item' => $item));
        endforeach;?>
    </ul>
    <?php
    echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues
    ));
else: ?>
    <div class="tip">
        <?php echo $this->translate('No playlists found.');?>
    </div>
<?php endif; ?>
