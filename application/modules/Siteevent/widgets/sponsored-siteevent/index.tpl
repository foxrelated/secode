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

<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css');

$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/slideitmoo-1.1_full_source.js');
?>

<?php if ($this->is_ajax_load): ?>
    <?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>

    <a id="" class="pabsolute"></a>
    <?php $navsPRE = 'siteevent_SlideItMoo_' . $this->identity; ?>
    <?php if (!empty($this->showPagination)) : ?>
        <script language="javascript" type="text/javascript">
            var slideshow;
                    en4.core.runonce.add(function() {
            slideshow = new SlideItMoo({
            overallContainer: '<?php echo $navsPRE ?>_outer',
                    elementScrolled: '<?php echo $navsPRE ?>_inner',
                    thumbsContainer: '<?php echo $navsPRE ?>_items',
                    thumbsContainerOuter: '<?php echo $navsPRE ?>_outer',
                    itemsVisible:'<?php echo $this->limit; ?>',
                    elemsSlide:'<?php echo $this->limit; ?>',
                    duration:<?php echo $this->interval; ?>,
                    itemsSelector: '<?php echo $this->vertical ? '.siteevent_carousel_content_item' : '.siteevent_carousel_content_item'; ?>',
                    itemsSelectorLoading:'<?php echo $this->vertical ? 'siteevent_carousel_loader' : 'siteevent_carousel_loader'; ?>',
                    itemWidth:<?php echo $this->vertical ? ($this->blockWidth) : ($this->blockWidth + 10); ?>,
                    itemHeight:<?php echo ($this->blockHeight + 10) ?>,
                    showControls:1,
                    slideVertical: <?php echo $this->vertical ?>,
                    startIndex:1,
                    totalCount:'<?php echo $this->totalCount; ?>',
                    contentstartIndex: - 1,
                    url:en4.core.baseUrl + 'siteevent/index/homesponsored',
                    params:{
            vertical:<?php echo $this->vertical ?>,
                    ratingType:'<?php echo $this->ratingType ?>',
                    fea_spo:'<?php echo $this->fea_spo ?>',
                    popularity:'<?php echo $this->popularity ?>',
                    category_id:'<?php echo $this->category_id ?>',
                    subcategory_id:'<?php echo $this->subcategory_id ?>',
                    subsubcategory_id:'<?php echo $this->subsubcategory_id ?>',
                    detactLocation:'<?php echo $this->detactLocation; ?>',
                    defaultLocationDistance: '<?php echo $this->defaultLocationDistance; ?>',
                    latitude: '<?php echo $this->latitude; ?>',
                    longitude: '<?php echo $this->longitude; ?>',
                    title_truncation:'<?php echo $this->title_truncation ?>',
                    showOptions:<?php if ($this->showOptions): echo json_encode($this->showOptions);
        else:
            ?>  {'no':1} <?php endif; ?>,
                    blockHeight: '<?php echo $this->blockHeight ?>',
                    blockWidth: '<?php echo $this->blockWidth ?>',
                    showPagination: '<?php echo $this->showPagination ?>',
                    showEventType: '<?php echo $this->showEventType ?>'
            },
                    navs:{
            fwd:'<?php echo $navsPRE . ($this->vertical ? "_forward" : "_right") ?>',
                    bk:'<?php echo $navsPRE . ($this->vertical ? "_back" : "_left") ?>'
            },
                    transition: Fx.Transitions.linear, /* transition */
                    onChange: function() {
            }
            });
            });</script>
    <?php endif; ?>
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

    <?php if ($this->vertical): ?> 
        <ul class="seaocore_sponsored_widget">
            <li>
                <?php $siteevent_advsiteevent = true; ?>
                <div id="<?php echo $navsPRE ?>_outer" class="siteevent_carousel_vertical siteevent_carousel">
                    <div id="<?php echo $navsPRE ?>_inner" class="siteevent_carousel_content b_medium" style="width:<?php echo $this->blockWidth + 2; ?>px;">
                        <ul id="<?php echo $navsPRE ?>_items" class="siteevent_carousel_items_wrapper">
                            <?php foreach ($this->events as $siteevent): ?>
                                <?php
                                echo $this->partial(
                                        'list_carousel.tpl', 'siteevent', array(
                                    'siteevent' => $siteevent,
                                    'title_truncation' => $this->title_truncation,
                                    'ratingShow' => $ratingShow,
                                    'ratingType' => $ratingType,
                                    'ratingValue' => $ratingValue,
                                    'vertical' => $this->vertical,
                                    'showOptions' => $this->showOptions,
                                    'showEventType' => $this->showEventType,
                                    'blockHeight' => $this->blockHeight,
                                    'blockWidth' => $this->blockWidth,
                                ));
                                ?>	     
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php if (!empty($this->showPagination)) : ?>
                        <div class="siteevent_carousel_controller">
                            <div class="siteevent_carousel_button siteevent_carousel_up" id="<?php echo $navsPRE ?>_back" style="display:none;">
                                <i></i>
                            </div>
                            <div class="siteevent_carousel_button siteevent_carousel_up_dis" id="<?php echo $navsPRE ?>_back_dis" style="display:block;">
                                <i></i>
                            </div>

                            <div class="siteevent_carousel_button siteevent_carousel_down fright" id ="<?php echo $navsPRE ?>_forward">
                                <i></i>
                            </div>
                            <div class="siteevent_carousel_button siteevent_carousel_down_dis fright" id="<?php echo $navsPRE ?>_forward_dis" style="display:none;">
                                <i></i>
                            </div>
                        </div>  
                    <?php endif; ?>  
                    <div class="clr"></div>
                </div>
                <div class="clr"></div>
            </li>
        </ul>
    <?php else: ?>
        <div id="<?php echo $navsPRE ?>_outer" class="siteevent_carousel siteevent_carousel_horizontal" style="width: <?php echo (($this->limit <= $this->totalCount ? $this->limit : $this->totalCount) * ($this->blockWidth + 24)) + 60 ?>px; height: <?php echo ($this->blockHeight + 10) ?>px;">
            <?php if (!empty($this->showPagination)) : ?>  
                <div class="siteevent_carousel_button siteevent_carousel_left" id="<?php echo $navsPRE ?>_left" style="display:none;">
                    <i></i>
                </div>
                <div class="siteevent_carousel_button siteevent_carousel_left_dis" id="<?php echo $navsPRE ?>_left_dis" style="display:<?php echo $this->limit < $this->totalCount ? "block;" : "none;" ?>">
                    <i></i>
                </div>
            <?php endif; ?>
            <div id="<?php echo $navsPRE ?>_inner" class="siteevent_carousel_content" style="height: <?php echo ($this->blockHeight + 5) ?>px;">
                <ul id="<?php echo $navsPRE ?>_items" class="siteevent_carousel_items_wrapper">
                    <?php $i = 0; ?>
                    <?php foreach ($this->events as $siteevent): ?>
                        <?php
                        echo $this->partial(
                                'list_carousel.tpl', 'siteevent', array(
                            'siteevent' => $siteevent,
                            'title_truncation' => $this->title_truncation,
                            'ratingShow' => $ratingShow,
                            'ratingType' => $ratingType,
                            'ratingValue' => $ratingValue,
                            'vertical' => $this->vertical,
                            'showEventType' => $this->showEventType,
                            'showOptions' => $this->showOptions,
                            'blockHeight' => $this->blockHeight,
                            'blockWidth' => $this->blockWidth,
                        ));
                        ?>	
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php if (!empty($this->showPagination)) : ?>
                <div class="siteevent_carousel_button siteevent_carousel_right" id ="<?php echo $navsPRE ?>_right" style="display:<?php echo $this->limit < $this->totalCount ? "block;" : "none;" ?>">
                    <i></i>
                </div>
                <div class="siteevent_carousel_button siteevent_carousel_right_dis" id="<?php echo $navsPRE ?>_right_dis" style="display:none;">
                    <i></i>
                </div>
            <?php endif; ?>  
        </div>
    <?php endif; ?>

<?php else: ?>
    <div id="layout_siteevent_sponsored_events_<?php echo $this->identity; ?>">
        <!--    <div class="seaocore_content_loader"></div>-->
    </div>

    <script type="text/javascript">
        var requestParams = $merge(<?php echo json_encode($this->params); ?>, {'content_id': '<?php echo $this->identity; ?>'})
        var params = {
        'detactLocation': <?php echo $this->detactLocation; ?>,
                'responseContainer' : 'layout_siteevent_sponsored_events_<?php echo $this->identity; ?>',
                requestParams: requestParams
        };
        en4.seaocore.locationBased.startReq(params);
    </script>  
<?php endif; ?>