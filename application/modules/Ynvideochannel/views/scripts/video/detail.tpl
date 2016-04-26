<?php $video = $this -> video;
$videoId = $video->getIdentity(); ?>
<script type="text/javascript">
</script>

<?php echo $this -> video -> getVideoIframe(620, 348);?>
<div class="ynvideochannel_detail_title">
    <?php echo htmlspecialchars($video->getTitle()) ?>
</div>
<div class="ynvideochannel_detail_block_info">
    <div class="ynvideochannel_detail_categories_ratings_owner clearfix">
        <div class="ynvideochannel_detail_owner">
            <?php
            $poster = $video->getOwner();
            echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon'), array('class' => 'ynvideochannel_img_owner clearfix'))
            ?>
            <?php if ($video->category_id):
            echo $this->partial('_category_breadcrumb.tpl', 'ynvideochannel', array('item' => $video));
            endif; ?>
                <span class="ynvideochannel_detail_owner_info ynvideochannel_detail_owner_username">
                        <?php echo $this->translate('Posted by') ?>
                    <?php
                            $poster = $video->getOwner();
                            if ($poster) {
                                echo $this->htmlLink($poster, $poster->getTitle());
                            }
                        ?>
                </span>
                <span class="ynvideochannel_detail_owner_info">
                    <?php echo '&nbsp;.&nbsp;'.$this->timestamp($video->creation_date) ?>
                </span>
            <div id="video_rating" class="ynvideochannel_detail_ratings ynvideochannel_detail_owner_info" onmouseout="rating_out();">
                <i id="rate_1" class="fa fa-star" <?php if (!$this->rated && $this->viewer->getIdentity()): ?>onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></i>
                <i id="rate_2" class="fa fa-star" <?php if (!$this->rated && $this->viewer->getIdentity()): ?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></i>
                <i id="rate_3" class="fa fa-star" <?php if (!$this->rated && $this->viewer->getIdentity()): ?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></i>
                <i id="rate_4" class="fa fa-star" <?php if (!$this->rated && $this->viewer->getIdentity()): ?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></i>
                <i id="rate_5" class="fa fa-star" <?php if (!$this->rated && $this->viewer->getIdentity()): ?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></i>
                <span id="rating_text" class="ynvideochannel_rating_text"></span>
            </div>
        </div>
    </div>
    <div class="ynvideochannel_detail_button_count clearfix">
        <div class="ynvideochannel_detail_count">
            <div class="ynvideochannel_detail_count_items">
                <div class="ynvideochannel_detail_count_item">
                    <?php echo $this->translate(array('<span>%s</span> favorite', '<span>%s</span> favorites', $video->favorite_count), $this->locale()->toNumber($video->favorite_count)) ?>
                </div>

                <div class="ynvideochannel_detail_count_item">
                    <?php echo $this->translate(array('<span>%s</span> view', '<span>%s</span> views', $video->view_count), $this->locale()->toNumber($video->view_count)) ?>
                </div>

                <div class="ynvideochannel_detail_count_item">
                    <?php echo $this->translate(array('<span>%s</span> like', '<span>%s</span> likes', $video->like_count), $this->locale()->toNumber($video->like_count)) ?>
                </div>

                <div class="ynvideochannel_detail_count_item">
                    <?php echo $this->translate(array('<span>%s</span> comment', '<span>%s</span> comments', $video->comment_count), $this->locale()->toNumber($video->comment_count)) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="ynvideochannel_detail_block_info_2">
        <div class="ynvideochannel_detail_block_info_2_header clearfix">
            <div class="ynvideochannel_addthis">
                <div class="addthis_sharing_toolbox"></div>
            </div>
            <div class="ynvideochannel_detail_block_button">
                <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
                <?php echo $this->htmlLink(array(
                'module'=>'activity',
                'controller'=>'index',
                'action'=>'share',
                'route'=>'default',
                'type'=>'ynvideochannel_video',
                'id' => $videoId,
                'format' => 'smoothbox'
                ), '<i class="fa fa-share-alt"></i>'.$this->translate("Share"), array('class' => 'ynvideochannel_share_button smoothbox')); ?>
                <?php $isLiked = $video->likes()->isLike($this->viewer()) ? 1 : 0; ?>
                <a id="ynvideochannel_like_button" class="ynvideochannel_like_button" href="javascript:void(0);" onclick="onlike('<?php echo $video->getType() ?>', '<?php echo $videoId ?>', <?php echo $isLiked ?>);">
                    <?php if( $isLiked ): ?>
                    <?php echo '<i class="fa fa-thumbs-up"></i>'.$this -> translate("Liked");?>
                    <?php else: ?>
                    <?php echo '<i class="fa fa-thumbs-up"></i>'.$this -> translate("Like");?>
                    <?php endif; ?>
                </a>
                <?php echo $this->partial('_add_to_menu.tpl','ynvideochannel', array('video' => $video)); ?>
                <?php echo $this->partial('_video_options.tpl', 'ynvideochannel', array('video' => $video)); ?>
            </div>
        </div>
    </div>

    <div class="ynvideochannel_button_more">
        <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
        <a href="javascript:void(0)" class="ynvideochannel_button_more_btn"><i class="fa fa-angle-down"></i></a>
        <ul class="ynvideochannel_button_more_explain">
            <?php if(!$session -> mobile):?>
            <li>
                <i class="fa fa-link"></i><a href="javascript:void(0)" onclick="viewURL()"><?php echo $this->translate('URL') ?></a>
            </li>
            <?php endif;?>
            <li>
                <?php
                $url = $this->url(array(
                'module' => 'ynvideochannel',
                'controller' => 'video',
                'action' => 'embed',
                'id' => $videoId
                ),'default', true);
                ?>
                <i class="fa fa-code"></i><a href="javascript:void(0)" onclick="openPopup('<?php echo $url?>')"><?php echo $this->translate('HTML Code') ?></a>
            </li>
            <li>
                <i class="fa fa-envelope"></i>
                <?php echo $this->htmlLink(
                array(
                'route'=>'ynvideochannel_general',
                'action' => 'send-to-friends',
                'id' => $videoId,
                'type' => 'video'
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
            'subject' => $video->getGuid()
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
    <?php if (count($this->videoTags)): ?>
    <div class="ynvideochannel_detail_tags">
        <span>
            <i class="fa fa-tag"></i>&nbsp;<?php echo $this->translate('Tags:') ?>
        </span>
        <?php foreach ($this->videoTags as $index => $tag): ?>
        <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>
            <?php echo $tag->getTag()->text ?>
        </a>
        <?php if ($index < count($this->videoTags) - 1) : ?>
        ,&nbsp;
        <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php if($video->description):?>
    <div class="ynvideochannel_detail_description_video_title">
        <?php echo $this->translate('Video Description') ?>
    </div>
    <?php $description = $video->description;
    $description = str_replace( "\r\n", "<br />", $description);
    $description = str_replace( "\n", "<br />", $description);
    echo $description; ?>
    <?php endif;?>
    <?php $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($this -> video); ?>
    <?php if($this -> fieldValueLoop($this -> video, $fieldStructure)):?>
    <div class="ynvideochannel-profile-fields">
        <div class="ynvideochannel-overview-title">
            <span class="ynvideochannel-overview-title-content"><?php echo $this->translate('Video Specifications');?></span>
        </div>
        <div class="ynvideochannel-overview-content">
            <?php echo $this -> fieldValueLoop($this -> video, $fieldStructure); ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_()->getApi('settings', 'core') -> getSetting('ynvideochannel.addthis.pubid', 'younet');?>" async="async"></script>
    <script type="text/javascript">
        en4.core.runonce.add(function() {
            var pre_rate = <?php echo $video->rating; ?>;
            var rated = '<?php echo $video->isRated(Engine_Api::_()->user()->getViewer()->getIdentity())?>';
            var video_id = <?php echo $video -> getIdentity(); ?>;
            var total_votes = <?php echo $video->rating_count?>;
            var viewer = <?php echo Engine_Api::_()->user()->getViewer()->getIdentity() ?>;
            var rating_over = window.rating_over = function(rating) {
                    if( rated == 1 ) {
                        $('rating_text').innerHTML = "<?php echo $this->translate('you already rated'); ?>";
                        set_rating();
                    } else if( viewer == 0 ) {
                        $('rating_text').innerHTML = "<?php echo $this->translate('please login to rate'); ?>";
                    } else {
                        $('rating_text').innerHTML = "<?php echo $this->translate('click to rate'); ?>";
                        for(var x=1; x<=5; x++) {
                        if(x <= rating) {
                        $('rate_'+x).set('class', 'fa fa-star');
                        } else {
                            $('rate_'+x).set('class', 'fa fa-star-o');
                        }
                    }
                }
            }
            var rating_out = window.rating_out = function() {
                $('rating_text').innerHTML = "";
                if (pre_rate != 0){
                set_rating();
                }
                else {
                    for(var x=1; x<=5; x++) {
                    $('rate_'+x).set('class', 'fa fa-star-o');
                    }
                }
            }
            var set_rating = window.set_rating = function() {
                var rating = pre_rate;
                for(var x=1; x<=parseInt(rating); x++) {
                $('rate_'+x).set('class', 'fa fa-star');
            }

                for(var x=parseInt(rating)+1; x<=5; x++) {
                $('rate_'+x).set('class', 'fa fa-star-o');
            }

                var remainder = Math.round(rating)-rating;
                if (remainder <= 0.5 && remainder !=0){
                var last = parseInt(rating)+1;
                $('rate_'+last).set('class', 'fa fa-star-half-o');
            }
            }
            var rate = window.rate = function(rating) {
                $('rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!'); ?>";
                for(var x=1; x<=5; x++) {
                $('rate_'+x).set('onclick', '');
            }
                (new Request.JSON({
                'format': 'json',
                'url' : '<?php echo $this->url(array('action' => 'rate', 'video_id' => $video -> getIdentity()), 'ynvideochannel_video', true) ?>',
                'data' : {
                'format' : 'json',
                'rating' : rating,
            },
                'onRequest' : function(){
            },
                'onSuccess' : function(responseJSON, responseText)
            {
                rated = 1;
                pre_rate = responseJSON.rating;
                set_rating();
            }
            })).send();

            }
            var tagAction = window.tagAction = function(tag){
                window.location = "<?php echo $this -> url(array('action' => 'browse-videos'), 'ynvideochannel_general', true);?>" + "?tag=" + tag;
            }
            set_rating();
            });

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
                        var html = '<a id="ynvideochannel_like_button" class="ynvideochannel_like_button" href="javascript:void(0);" onclick="unlike(\'<?php echo $video->getType()?>\', \'<?php echo $videoId ?>\')"><i class="fa fa-thumbs-up"></i><?php echo $this -> translate('Liked'); ?></a>';
                        $("ynvideochannel_like_button").outerHTML = html;
                    }
                },
                onComplete: function(responseJSON, responseText) {
                }
            }).send();
        }
    </script>
    <script type="text/javascript">
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
                        var html = '<a id="ynvideochannel_like_button" class="ynvideochannel_like_button" href="javascript:void(0);" onclick="like(\'<?php echo $video->getType()?>\', \'<?php echo $videoId ?>\')"><i class="fa fa-thumbs-up"></i><?php echo $this -> translate('Like'); ?></a>';
                        $("ynvideochannel_like_button").outerHTML = html;
                    }
                }
            }).send();
        }
        function viewURL()
        {
            $('ynvideochannel_return_url').value = document.URL;
            if(window.innerWidth <= 408)
            {
                Smoothbox.open($('ynvideochannel_form_return_url'), {autoResize : true, width: 300});
            }
            else
            {
                Smoothbox.open($('ynvideochannel_form_return_url'));
            }
            var elements = document.getElements('video');
            elements.each(function(e)
            {
                e.style.display = 'none';
            });
        }

        function closeSmoothbox()
        {
            var block = Smoothbox.instance;
            block.close();
            var elements = document.getElements('video');
            elements.each(function(e)
            {
                e.style.display = 'block';
            });
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