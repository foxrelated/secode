<?php
    $totalVideo = $this->paginator->getTotalItemCount();
?>
<div class="ynultimatevideo_count_videos">
    <i class="fa fa-bookmark"></i>
    <?php echo $totalVideo; ?>
    <?php echo $this->translate('Videos'); ?>
</div>
<?php if ($totalVideo > 0) : ?>
    <div class="ynultimatevideo_list_my_watch_later">
        <ul class="ynultimatevideo_list_most_items clearfix">
            <?php foreach ($this->paginator as $video) : ?>    
                <li class="ynultimatevideo_list_most_item">
                    <?php
                    echo $this->partial('_video-manage-item.tpl', array('video' => $video, 'watched' => $video->watched, 'manageType' => 'playlist'))
                    ?>
                </li>
            <?php endforeach; ?>

            <?php if ($this->paginator->getCurrentItemCount() < $totalVideo) : ?>
                <li class="ynultimatevideo_pages">
                    <?php echo $this->paginationControl($this->paginator, null, null, array('query' => $this->params));?>
                </li>
            <?php endif; ?>   
        </ul>
    </div>
<?php else: ?>
    <div class="tip">
        <span>
            <?php
                if (array_key_exists('search', $this->params)) {
                    echo $this->translate('No videos found.');
                } else {
                    echo $this->translate('You do not have any videos in your watch-later list.');
                }
            ?>
        </span>
    </div>
<?php endif; ?>


<script type="text/javascript">
    window.addEvent('domready', function() {
        $$('.ynultimatevideo_main_manage').getParent().addClass('active');
    });

    function ynultimatevideoAddNewPlaylist(ele, guid) {
        var nextEle = ele.getNext();
        if(nextEle.hasClass("ynultimatevideo_active_add_playlist")) {
            //click to close
            nextEle.removeClass("ynultimatevideo_active_add_playlist");
            nextEle.setStyle("display", "none");
        } else {
            //click to open
            nextEle.addClass("ynultimatevideo_active_add_playlist");
            nextEle.setStyle("display", "block");
        }
        $$('.play_list_span').each(function(el){
            if(el === nextEle){
                //do not empty the current box
            } else {
                el.empty();
                el.setStyle("display", "none");
                el.removeClass("ynultimatevideo_active_add_playlist");
            }
        });
        var data = guid;
        var url = '<?php echo $this->url(array('action' => 'get-playlist-form'), 'ynultimatevideo_playlist', true);?>';
        var request = new Request.HTML({
            url : url,
            data : {
                subject: data,
            },
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                var spanEle = nextEle;
                spanEle.innerHTML = responseHTML;
                eval(responseJavaScript);

                var popup = spanEle.getParent('.ynultimatevideo-action-pop-up');
                var layout_parent = popup.getParent('.layout_middle');
                if (!layout_parent) layout_parent = popup.getParent('#global_content');
                var y_position = popup.getPosition(layout_parent).y;
                var p_height = layout_parent.getHeight();
                var c_height = popup.getHeight();
                if(p_height - y_position < (c_height + 21)) {
                    layout_parent.addClass('popup-padding-bottom');
                    var margin_bottom = parseInt(layout_parent.getStyle('padding-bottom').replace( /\D+/g, ''));
                    layout_parent.setStyle('padding-bottom', (margin_bottom + c_height + 21 + y_position - p_height)+'px');
                }
            }
        });
        request.send();
    }

    function ynultimatevideoAddToPlaylist(ele, playlistId, guild) {
        var checked = ele.get('checked');
        var data = guild;
        var url = '<?php echo $this->url(array('action' => 'add-to-playlist'), 'ynultimatevideo_playlist', true);?>';
        var request = new Request.JSON({
            url : url,
            data : {
                subject: data,
                playlist_id: playlistId,
                checked: checked,
            },
            onSuccess: function(responseJSON) {
                if (!responseJSON.status) {
                    ele.set('checked', !checked);
                }
                var div = ele.getParent('.ynultimatevideo-action-pop-up');
                var notices = div.getElement('.add-to-playlist-notices');
                var notice = new Element('div', {
                    'class' : 'add-to-playlist-notice',
                    text : responseJSON.message
                });
                notices.adopt(notice);
                notice.fade('in');
                (function() {
                    notice.fade('out').get('tween').chain(function() {
                        notice.destroy();
                    });
                }).delay(2000, notice);
            }
        });
        request.send();
    }

    function ynultimatevideoAddToWatchLater(ele, video_id) {
        var url = '<?php echo $this->url(array('action' => 'add-to'), 'ynultimatevideo_watch_later', true);?>';
        var request = new Request.JSON({
            url : url,
            data : {
                video_id: video_id
            },
            onSuccess: function(responseJSON) {
                if (responseJSON.result) {
                    if (responseJSON.added == 1) {
                        var html = '<i class="fa fa-ban"></i>' + ' ' + '<?php echo $this->translate('Unwatched') ?>';
                        ele.innerHTML = html;
                    } else {
                        var html = '<i class="fa fa-play-circle"></i>' + ' ' + '<?php echo $this->translate('Watch Later') ?>';
                        ele.innerHTML = html;
                    }
                }
                var div = ele.getParent('.ynultimatevideo-action-pop-up');
                var notices = div.getElement('.add-to-playlist-notices');
                var notice = new Element('div', {
                    'class' : 'add-to-playlist-notice',
                    text : responseJSON.message
                });
                notices.adopt(notice);
                notice.fade('in');
                (function() {
                    notice.fade('out').get('tween').chain(function() {
                        notice.destroy();
                    });
                }).delay(2000, notice);
            }
        });
        request.send();
    }

    function ynultimatevideoAddToFavorite(ele, video_id) {
        var url = '<?php echo $this->url(array('action' => 'add-to'), 'ynultimatevideo_favorite', true);?>';
        var request = new Request.JSON({
            url : url,
            data : {
                video_id: video_id
            },
            onSuccess: function(responseJSON) {
                if (responseJSON.result) {
                    if (responseJSON.added == 1) {
                        ele.addClass('added');
                    } else {
                        ele.removeClass('added');
                    }
                }
                var div = ele.getParent('.ynultimatevideo-action-pop-up');
                var notices = div.getElement('.add-to-playlist-notices');
                var notice = new Element('div', {
                    'class' : 'add-to-playlist-notice',
                    text : responseJSON.message
                });
                notices.adopt(notice);
                notice.fade('in');
                (function() {
                    notice.fade('out').get('tween').chain(function() {
                        notice.destroy();
                    });
                }).delay(2000, notice);
            }
        });
        request.send();
    }
</script>