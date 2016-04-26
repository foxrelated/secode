<?php $viewer = $this -> viewer ?>
<?php $channel = $this->channel ?>
<div class="ynvideochannel_channels_detail">
<?php $cover_url = ($channel->getCoverUrl('thumb.main')) ? $channel->getCoverUrl('thumb.main') : 'application/modules/Ynvideochannel/externals/images/nophoto_channel_cover.png'; ?>
<div><img src="<?php echo $cover_url?>"/></div>
<?php $photo_url = ($channel->getPhotoUrl('thumb.normal')) ? $channel->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_channel_thumb_normal.png'; ?>
<div><img width="200px" src="<?php echo $photo_url?>"/></div>
<div><?php if($viewer->getIdentity() != $channel->owner_id):
    echo $this->partial('_subscribe_channel.tpl', 'ynvideochannel', array('item' => $channel, 'user_id' => $viewer->getIdentity()));
    endif;?>
</div>
    <div><a href="<?php echo $channel -> getHref()?>"><?php echo $channel -> getTitle()?></a></div>
    <?php if ($channel->category_id)
    echo $this->partial('_category_breadcrumb.tpl', 'ynvideochannel', array('item' => $channel));
    ?>
    <?php echo $this -> translate("%1s by %2s", $this->timestamp(strtotime($channel->creation_date)), $channel -> getOwner());?>
    <div id="ynvideochannel_subscriber_count_<?php echo $channel -> channel_id ?>"><?php echo $this -> translate(array("%s subscriber", "%s subscribers", $channel -> subscriber_count), $channel -> subscriber_count)?></div>
    <div><?php echo $this -> translate(array("%s video", "%s videos", $channel -> video_count), $channel -> video_count)?></div>
    <div><?php echo $this -> translate(array("%s like", "%s likes", $channel -> like_count), $channel -> like_count)?></div>
    <div><?php echo $this -> translate(array("%s comment", "%s comments", $channel -> comment_count), $channel -> comment_count)?></div>
    <div class="ynvideochannel_addthis">
        <div class="addthis_sharing_toolbox"></div>
    </div>
    <div>
        <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
        <?php echo $this->htmlLink(array(
        'module'=>'activity',
        'controller'=>'index',
        'action'=>'share',
        'route'=>'default',
        'type'=>'ynvideochannel_channel',
        'id' => $channel -> getIdentity(),
        'format' => 'smoothbox'
        ), '<i class="fa fa-share-alt"></i>'.$this->translate("Share"), array('class' => 'ynvideochannel_share_button smoothbox')); ?>
        <?php $isLiked = $channel->likes()->isLike($this->viewer()) ? 1 : 0; ?>
        <a id="ynvideochannel_like_button" class="ynvideochannel_like_button" href="javascript:void(0);" onclick="onlike('<?php echo $channel->getType() ?>', '<?php echo $channel -> getIdentity() ?>', <?php echo $isLiked ?>);">
            <?php if( $isLiked ): ?>
            <?php echo '<i class="fa fa-thumbs-up"></i>'.$this -> translate("Liked");?>
            <?php else: ?>
            <?php echo '<i class="fa fa-thumbs-up"></i>'.$this -> translate("Like");?>
            <?php endif; ?>
        </a>
        <?php endif ?>
    </div>
    <div class="ynvideochannel_button_more">
        <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
        <a href="javascript:void(0)" class="ynvideochannel_button_more_btn"><i class="fa fa-angle-down"></i></a>
        <ul class="ynvideochannel_button_more_explain">
            <li>
                <i class="fa fa-envelope"></i>
                <?php echo $this->htmlLink(
                array(
                'route'=>'ynvideochannel_general',
                'action' => 'send-to-friends',
                'id' => $channel -> getIdentity(),
                'type' => 'channel'
                ),
                $this->translate('Send to Friends'),
                array(
                'class' => 'smoothbox'
                )
                )?>
            </li>
            <?php
            $url = $this->url(array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $channel -> getGuid()
            ),'default', true);
            ?>
            <li>
                <i class="fa fa-bolt"></i><a href="javascript:void(0)" onclick="openPopup('<?php echo $url?>')"><?php echo $this->translate('Report') ?></a>
            </li>

            <li class="ynvideochannel_block " style="display:none">
                <form id="ynvideochannel_form_return_url" onsubmit="return false;">
                    <span id="global_content_simple">
                        <label class="ynvideochannel_popup_label"><?php echo $this->translate("URL")?></label>
                        <input style="max-width: 100%" type="text" id="ynvideochannel_return_url" class="ynvideochannel_return_url"/>
                        <br/>
                        <div class="ynvideochannel_center" style="padding-top: 10px">
                            <a href="javascript:void(0)" onclick="closeSmoothbox()" class="ynvideochannel_bold_link">
                                <button><?php echo $this->translate('Close')?></button>
                            </a>
                        </div>
                    </span>
                </form>
            </li>
        </ul>
        <?php endif ?>
    </div>

    <div><?php echo $this->partial('_channel_options.tpl', 'ynvideochannel', array('channel' => $channel)); ?></div>
    <ul class="ynvideochannel_videos" id="video_list_container">
    </ul>
</div>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_()->getApi('settings', 'core') -> getSetting('ynvideochannel.addthis.pubid', 'younet');?>" async="async"></script>
<script type="text/javascript">
    var url = '<?php echo $this->url(array('action'=>'ajax-get-videos', 'channel_id' => $this -> channel -> getIdentity()), 'ynvideochannel_channel')?>'
    function ajaxGetVideos(pageId)
    {
        var request = new Request.HTML({
            url : url,
            data : {
                page : pageId,
            },
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript)
            {
                responseHTML = responseHTML.replace(/<\/?span[^>]*>/g,"");
                document.getElementById("video_list_container").innerHTML = responseHTML;
            }
        });
        request.send();
    }
    window.addEvent('domready', function(){
        ajaxGetVideos(1);
    });
    var openPage = function(pageId){
        ajaxGetVideos(pageId);
    }
    function onlike(itemType, itemId, isLiked) {
        if (isLiked) {
            unlike(itemType, itemId);
        } else {
            like(itemType, itemId);
        }
    }
    function like(itemType, itemId)
    {
        new Request.JSON({
            url: en4.core.baseUrl + 'core/comment/like',
            method: 'post',
            data : {
                format: 'json',
                type : itemType,
                id : itemId,
                comment_id : 0
            },
            onSuccess: function(responseJSON, responseText) {
                if (responseJSON.status == true)
                {
                    var html = '<a id="ynvideochannel_like_button" class="ynvideochannel_like_button" href="javascript:void(0);" onclick="unlike(\'<?php echo $channel->getType()?>\', \'<?php echo $channel -> getIdentity() ?>\')"><i class="fa fa-thumbs-up"></i><?php echo $this -> translate('Liked'); ?></a>';
                    $("ynvideochannel_like_button").outerHTML = html;
                }
            },
            onComplete: function(responseJSON, responseText) {
            }
        }).send();
    }

    function unlike(itemType, itemId)
    {
        new Request.JSON({
            url: en4.core.baseUrl + 'core/comment/unlike',
            method: 'post',
            data : {
                format: 'json',
                type : itemType,
                id : itemId,
                comment_id : 0
            },
            onSuccess: function(responseJSON, responseText) {
                if (responseJSON.status == true)
                {
                    var html = '<a id="ynvideochannel_like_button" class="ynvideochannel_like_button" href="javascript:void(0);" onclick="like(\'<?php echo $channel -> getType()?>\', \'<?php echo $channel -> getIdentity() ?>\')"><i class="fa fa-thumbs-up"></i><?php echo $this -> translate('Like'); ?></a>';
                    $("ynvideochannel_like_button").outerHTML = html;
                }
            }
        }).send();
    }

    function openPopup(url)
    {
        if(window.innerWidth <= 480)
        {
            Smoothbox.open(url, {autoResize : true, width: 300});
        }
        else
        {
            Smoothbox.open(url);
        }
    }
 </script>


