<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $id = $this->identity; ?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headScript()->appendFile($baseUrl . 'application/modules/Sitevideo/externals/scripts/core.js');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css');
$this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');

?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/scripts/core.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>

<div class='categories_manage sitevideo_home_featured' id='categories_manage' style="height: <?php echo $this->height; ?>px;" >
    <div id="featured_slideshow_wrapper_<?php echo $id; ?>" class="featured_slideshow_wrapper sitevideo_home_featured_wrapper">
        <div id="featured_slideshow_mask_<?php echo $id; ?>" class="featured_slideshow_mask" style="height: <?php echo $this->height; ?>px;">
            <div id="sitevideo_featured_channel_im_te_advanced_box_<?php echo $id; ?>" class="featured_slideshow_advanced_box">
                <?php $span = ""; ?>
                <?php foreach ($this->paginator as $video) : ?>
                    <?php
                    $tableOtherinfo = Engine_Api::_()->getDbtable('videootherinfo', 'sitevideo');
                    $vidoeOtherInfo = $tableOtherinfo->getOtherinfo($video->video_id);
                    $imageUrl = '';
                    $photoId = 0;
                    if ($video->photo_id) {
                        $photoId = $video->photo_id;
                    }
                    if ($photoId)
                        $imageUrl = $this->storage->get($photoId, 'thumb.main')->getPhotoUrl();
                    $span .="<span class='inactive'></span>";
                    ?>

                    <?php if ($vidoeOtherInfo && $vidoeOtherInfo->url): ?>
                        <?php
                        $url = $vidoeOtherInfo->url;
                        $target = "_blank";
                        ?>
                    <?php else: ?>
                        <?php
                        $url = $video->getHref();
                        $target = "_self";
                        ?>
                    <?php endif; ?>
                    <a href="<?php echo $url; ?>" target="<?php echo $target; ?>" >
                        <div class='featured_slidebox sitevideo_home_featured_slidebox' style="height: <?php echo $this->height; ?>px;">
                            <div class="sitevideo_home_featured_banner" <?php echo $imageUrl ? "style='background-image:url(" . $imageUrl . ")'" : "style='background-color:gray;'"; ?>>
                                <div class='featured_slidshow_content'>
                                    <h3></h3>
                                    <span class="sitevideo_home_featured_overlay"></span>
                                    <div class='channelInfo'>

                                        <?php if (in_array('title', $this->videoOption)): ?>
                                            <div class="sitevideo_home_featured_title">
                                                <h4>
                                                    <?php echo $this->string()->truncate($this->string()->stripTags($video->getTitle()), $this->titleTruncation); ?>
                                                </h4>
                                            </div>
                                        <?php endif; ?>
                                        <div class="sitevideo_home_featured_stats">
                                            <?php if ($this->showTagline1 && strlen(trim($vidoeOtherInfo->tagline1)) > 0) : ?>
                                                <div class="sitevideo_home_featured_tag1">
                                                    <?php echo $this->string()->truncate($this->string()->stripTags($vidoeOtherInfo->tagline1), $this->taglineTruncation) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($this->showTagline2 && strlen(trim($vidoeOtherInfo->tagline2)) > 0) : ?>
                                                <div class="sitevideo_home_featured_tag2">
                                                    <?php echo $this->string()->truncate($this->string()->stripTags($vidoeOtherInfo->tagline2), $this->taglineTruncation) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($this->showTaglineDesc && strlen(trim($vidoeOtherInfo->tagline_description)) > 0) : ?>
                                                <div class="sitevideo_home_featured_desc">
                                                    <?php echo $this->string()->truncate($this->string()->stripTags($vidoeOtherInfo->tagline_description), $this->descriptionTruncation) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (in_array('watchlater', $this->videoOption)): ?>
                                            <div class="sitevideo_home_featured_watch_later">
                                                <?php $this->shareLinks($video, $this->videoOption, true); ?>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                    <div class="sitevideo_home_featured_video_play sitevideo_thumb_viewer">
                                        <a href="<?php echo $url; ?>" target="<?php echo $target; ?>" >
                                            <span class="video_overlay"></span>
                                            <span class="play_icon"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="sitevideo_cat_slide_nav" id="handles8_<?php echo $id; ?>">
                <div>
                    <?php echo $span; ?>
                </div>
            </div>
        </div>
        <div class="featured_slideshow_option_bar sitevideo_home_featured_navigations" style="display: <?php echo $this->showNavigationButton ? 'block' : 'none'; ?>">
            <span id="sitevideo_featured_channel_prev8_<?php echo $id; ?>" class="featured_slideshow_controllers-prev featured_slideshow_controllers prev" title=<?php echo $this->translate("Previous") ?> ></span>
            <span id="sitevideo_featured_channel_next8_<?php echo $id; ?>" class="featured_slideshow_controllers-next featured_slideshow_controllers" title=<?php echo $this->translate("Next") ?> ></span>
        </div>
    </div>
</div>

<script type="text/javascript">
    en4.core.runonce.add(function () {


        if (document.getElementsByClassName == undefined) {
            document.getElementsByClassName = function (className)
            {
                var hasClassName = new RegExp("(?:^|\\s)" + className + "(?:$|\\s)");
                var allElements = document.getElementsByTagName("*");
                var results = [];

                var element;
                for (var i = 0; (element = allElements[i]) != null; i++) {
                    var elementClass = element.className;
                    if (elementClass && elementClass.indexOf(className) != -1 && hasClassName.test(elementClass))
                        results.push(element);
                }

                return results;
            }
        }
        SlideShow = function ()
        {
            this.width = 0;
            this.slideElements = [];
            this.noOfSlideShow = 0;
            this.id = 0;
            this.handles8_more = '';
            this.handles8 = '';
            this.interval = 0;
            this.set = function (arg)
            {
                this.noOfSlideShow = arg.noOfSlideShow;
                this.id = arg.id;
                this.interval = arg.interval;
                if (arg.fullWidth == 1) {
                    this.width = window.getWidth();
                }
                else {
                    this.width = $('global_content').getElement("#featured_slideshow_wrapper_" + this.id).clientWidth;
                }
                $('global_content').getElement("#featured_slideshow_mask_" + this.id).style.width = (this.width) + "px";
                this.slideElements = document.getElementsByClassName('featured_slidebox');
                for (var i = 0; i < this.slideElements.length; i++)
                    this.slideElements[i].style.width = (this.width) + "px";
                this.handles8_more = $$('#handles8_more_' + this.id + ' span');
                this.handles8 = $$('#handles8_' + this.id + ' span');
            }
            this.walk = function ()
            {
                var uid = this.id;
                var noOfSlideShow = this.noOfSlideShow;
                var handles8 = this.handles8;
                var nS8 = new noobSlide({
                    box: $('sitevideo_featured_channel_im_te_advanced_box_' + this.id),
                    items: $$('#sitevideo_featured_channel_im_te_advanced_box_' + this.id + ' h3'),
                    size: (this.width),
                    handles: this.handles8,
                    addButtons: {previous: $('sitevideo_featured_channel_prev8_' + this.id), next: $('sitevideo_featured_channel_next8_' + this.id)},
                    interval: this.interval,
                    fxOptions: {
                        duration: 500,
                        transition: '',
                        wait: false,
                    },
                    autoPlay: true,
                    mode: 'horizontal',
                    onWalk: function (currentItem, currentHandle) {
                        $$(this.handles, handles8).removeClass('active');
                        $$(currentHandle, handles8[this.currentIndex]).addClass('active');
                        if ((this.currentIndex + 1) == (this.items.length))
                            $('sitevideo_featured_channel_next8_' + uid).hide();
                        else
                            $('sitevideo_featured_channel_next8_' + uid).show();

                        if (this.currentIndex > 0)
                            $('sitevideo_featured_channel_prev8_' + uid).show();
                        else
                            $('sitevideo_featured_channel_prev8_' + uid).hide();
                    }
                });
                //more handle buttons
                nS8.addHandleButtons(this.handles8_more);
                //walk to item 3 witouth fx
                nS8.walk(0, false, true);
            }
        }

        var slideshow = new SlideShow();
        slideshow.set({
            id: '<?php echo $id; ?>',
            noOfSlideShow: <?php echo $this->totalCount; ?>,
            interval: <?php echo $this->delay; ?>,
            fullWidth:<?php echo $this->fullWidth; ?>
        });
        slideshow.walk();
    });
<?php if ($this->fullWidth) : ?>
        en4.core.runonce.add(function () {
            if ($$('.layout_main')) {
                var globalContentWidth = $('global_content').getWidth();
                $$('.layout_main').setStyles({
                    'width': globalContentWidth,
                    'margin': '0 auto'
                });
            }
            $('global_content').setStyles({
                'width': '100%',
                'margin-top': '-16px'
            });
        });
<?php endif; ?>

</script>
<script type="text/javascript">
    en4.core.runonce.add(function () { 
        en4.sitevideolightboxview.attachClickEvent(Array('sitevideo_thumb_viewer'));
    });
</script>