<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1); ?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>

<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css');
?>

<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . "application/modules/Siteevent/externals/scripts/_class.noobSlide.packed.js");
?>

<?php if ($this->is_ajax_load): ?>

    <?php
    $ratingValue = $this->ratingType;
    $ratingShow = 'small-star';
    if ($this->ratingType == 'rating_editor') {
        $ratingType = 'editor';
    } elseif ($this->ratingType == 'rating_avg') {
        $ratingType = 'overall';
    } else {
        $ratingType = 'user';
    }
    ?>

    <script type="text/javascript">
        en4.core.runonce.add(function() {
            if (document.getElementsByClassName == undefined) {
                document.getElementsByClassName = function(className)
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

            var width = $("featured_slideshow_wrapper<?php echo $this->identity ?>").clientWidth;
            $("featured_slideshow_mask<?php echo $this->identity ?>").style.width = (width - 10) + "px";
            var divElements = $("featured_slideshow_mask<?php echo $this->identity ?>").getElements('.featured_slidebox');
            for (var i = 0; i < divElements.length; i++)
                divElements[i].style.width = (width - 10) + "px";

            var handles8_more = $$('.handles8_more span');
            var num_of_slidehsow = "<?php echo $this->num_of_slideshow; ?>";
            var nS8 = new noobSlide({
                box: $('siteevent_featured_<?php echo $this->identity ?>_im_te_advanced_box'),
                items: $$('#siteevent_featured_<?php echo $this->identity ?>_im_te_advanced_box h3'),
                size: (width - 10),
                handles: $$('#handles8 span'),
                addButtons: {previous: $('siteevent_featured_<?php echo $this->identity ?>_prev8'), stop: $('siteevent_featured_<?php echo $this->identity ?>_stop8'), play: $('siteevent_featured_<?php echo $this->identity ?>_play8'), next: $('siteevent_featured_<?php echo $this->identity ?>_next8')},
                interval: 5000,
                fxOptions: {
                    duration: 500,
                    transition: '',
                    wait: false
                },
                autoPlay: true,
                mode: 'horizontal',
                onWalk: function(currentItem, currentHandle) {

                    // Finding the current number of index.
                    var current_index = this.items[this.currentIndex].innerHTML;
                    var current_start_title_index = current_index.indexOf(">");
                    var current_last_title_index = current_index.indexOf("</span>");
                    // This variable containe "Index number" and "Title" and we are finding index.
                    var current_title = current_index.slice(current_start_title_index + 1, current_last_title_index);
                    // Find out the current index id.
                    var current_index = current_title.indexOf("_");
                    // "current_index" is the current index.
                    current_index = current_title.substr(0, current_index);

                    // Find out the caption title.
                    var current_caption_title = current_title.indexOf("_caption_title:") + 15;
                    var current_caption_link = current_title.indexOf("_caption_link:");
                    // "current_caption_title" is the caption title.
                    current_caption_title = current_title.slice(current_caption_title, current_caption_link);
                    var caption_title = current_caption_title;
                    // "current_caption_link" is the caption title.
                    current_caption_link = current_title.slice(current_caption_link + 14);

                    var caption_title_lenght = current_caption_title.length;
                    if (caption_title_lenght > 30)
                    {
                        current_caption_title = current_caption_title.substr(0, 30) + '..';
                    }

                    if (current_caption_title != null && current_caption_link != null)
                    {
                        $('siteevent_featured_<?php echo $this->identity ?>_caption').innerHTML = current_caption_link;
                    }
                    else {
                        $('siteevent_featured_<?php echo $this->identity ?>_caption').innerHTML = '';
                    }
                    $('siteevent_featured_<?php echo $this->identity ?>_current_numbering').innerHTML = current_index + '/' + "<?php echo $this->num_of_slideshow; ?>";
                }
            });

            //more handle buttons
            nS8.addHandleButtons(handles8_more);
            //walk to item 3 witouth fx
            nS8.walk(0, false, true);
        });
    </script>

    <div class="featured_slideshow_wrapper" id="featured_slideshow_wrapper<?php echo $this->identity ?>">
        <div class="featured_slideshow_mask" id="featured_slideshow_mask<?php echo $this->identity ?>" style="height:<?php echo $this->blockHeight; ?>px;">
            <div id="siteevent_featured_<?php echo $this->identity ?>_im_te_advanced_box" class="featured_slideshow_advanced_box">

                <?php $image_count = 1; ?>
                <?php foreach ($this->show_slideshow_object as $type => $item): ?>
                    <div class='featured_slidebox' style="height:<?php echo $this->blockHeight; ?>px;">
                        <div class='featured_slidshow_img'> 
                            <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $item->featured): ?>
                                <i class="siteevent_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
                            <?php endif; ?>
                            <?php if (!empty($this->statistics) && in_array('newLabel', $this->statistics) && $item->newlabel): ?>
                                <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                            <?php endif; ?>
                            <a href="<?php echo $item->getHref(array('showEventType' => $this->showEventType)) ?>">
                                <?php
                                $url = $item->getPhotoUrl('thumb.profile');

                                if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/nophoto_event_thumb_normal.png';
                                endif;
                                ?>
                                <span style="background-image: url(<?php echo $url; ?>);"></span>
                            </a>
                            <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($item->sponsored)): ?>
                                <div class="siteevent_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505'); ?>">
                                    <?php echo $this->translate('SPONSORED'); ?>                 
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class='featured_slidshow_content'>
                            <?php
                            $tmpBody = strip_tags($item->title);
                            $title = ( Engine_String::strlen($tmpBody) > $this->title_truncation ? Engine_String::substr($tmpBody, 0, $this->title_truncation) . '..' : $tmpBody );
                            ?>
                            <h5 class="o_hidden"> <?php echo $this->htmlLink($item->getHref(array('showEventType' => $this->showEventType)), $title, array('title' => $item->getTitle())) ?></h5>
                            <h3 style='display:none'><span><?php echo $image_count++ . '_caption_title:' . $item->title . '_caption_link:' . $this->htmlLink($item->getHref(array('showEventType' => $this->showEventType)), $this->translate("View Event &raquo;"), array('class' => 'featured_slideshow_view_link', 'title' => $item->getTitle())) . '</span>' ?></h3>

                            <?php if (!empty($this->statistics)) : ?>
                                <?php echo $this->eventInfo($item, $this->statistics, array('ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation, 'showEventType' => $this->showEventType)); ?>
                            <?php endif; ?>

                            <span class="featured_slidshow_info">
                                    <?php if (!empty($item->body) && $this->truncationDescription > 0) : ?>
                                    <p class="clr">
                                        <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($item->body, $this->truncationDescription) ?>
                                        <?php if (Engine_String::strlen($item->body) > $this->truncationDescription): ?>
                                            <?php echo $this->htmlLink($item->getHref(array('showEventType' => $this->showEventType)), $this->translate('More &raquo;')) ?>
                                    <?php endif; ?>
                                    </p>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="featured_slideshow_option_bar">
            <div>
                <p class="buttons" style="<?php if ($image_count <= 2): echo "display:none;";
    endif;
    ?>">
                    <span id="siteevent_featured_<?php echo $this->identity ?>_prev8" class="featured_slideshow_controllers-prev featured_slideshow_controllers prev" title="Previous" ></span>
                    <span id="siteevent_featured_<?php echo $this->identity ?>_stop8" class="featured_slideshow_controllers-stop featured_slideshow_controllers" title="Stop"></span>
                    <span id="siteevent_featured_<?php echo $this->identity ?>_play8" class="featured_slideshow_controllers-play featured_slideshow_controllers" title="Play"></span>
                    <span id="siteevent_featured_<?php echo $this->identity ?>_next8" class="featured_slideshow_controllers-next featured_slideshow_controllers" title="Next" ></span>
                </p>
            </div>
            <span id="siteevent_featured_<?php echo $this->identity ?>_caption"></span>
            <span id="siteevent_featured_<?php echo $this->identity ?>_current_numbering" class="featured_slideshow_pagination" style="<?php if ($image_count <= 2): echo "display:none;";
              endif;
              ?>"></span>
        </div>
    </div>
<?php else: ?>

    <div id="layout_siteevent_slideshow_siteevent_<?php echo $this->identity; ?>">
        <!--    <div class="seaocore_content_loader"></div>-->
    </div>

    <script type="text/javascript">
        var requestParams = $merge(<?php echo json_encode($this->params); ?>, {'content_id': '<?php echo $this->identity; ?>'})
        var params = {
            'detactLocation': <?php echo $this->detactLocation; ?>,
            'responseContainer': 'layout_siteevent_slideshow_siteevent_<?php echo $this->identity; ?>',
            requestParams: requestParams
        };

        en4.seaocore.locationBased.startReq(params);
    </script>  

<?php endif; ?>