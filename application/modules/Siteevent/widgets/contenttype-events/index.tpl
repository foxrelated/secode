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
include APPLICATION_PATH . '/application/modules/' . ucfirst($this->subject()->getModuleName()) . '/views/scripts/Adintegration.tpl';
$widgetmodulename = $this->moduleName . '/widget';
$moduleName = $this->moduleName;

$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_rating.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css');
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js');
?>
<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()
        ->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<?php if ($this->moduleName != 'sitereview' && empty($this->isajax)) : ?>
    <script type="text/javascript">
        en4.siteeventcontenttype.profileTabParams[<?php echo $this->identity ?>] = {
            type: 'event',
            requestParams:<?php echo json_encode($this->paramsLocation) ?>,
            requestUrl: en4.core.baseUrl + '<?php echo ($this->user_layout) ? $widgetmodulename : 'widget'; ?>'
        };
    </script>
<?php endif; ?>

<?php if ($this->moduleName == 'sitereview' && empty($this->isajax)): ?>
    <script type="text/javascript">
        var params = {
            requestParams:<?php echo json_encode($this->paramsLocation) ?>,
            responseContainer: $$('.layout_siteevent_contenttype_events')
        }
        en4.siteevent.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
    </script>
<?php endif; ?>

<?php if (empty($this->isajax)) : ?>
    <div id="id_<?php echo $this->identity; ?>">
    <?php endif; ?>

    <?php if (!empty($this->show_content)) : ?>
        <a id="profile_siteevents<?php echo $this->identity ?>"></a>
        <?php if ($this->showtoptitle == 1): ?>
            <div class="layout_simple_head" id="layout_event">
                <?php echo $this->translate($this->subject()->getTitle()); ?><?php echo $this->translate("'s Events"); ?>
            </div>
        <?php endif; ?>	
        <?php if ($this->integratedModule && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting($this->moduleName . '.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting($this->modulename . '.adeventwidget', 3) && $communityad_integration && Engine_Api::_()->$moduleName()->showAdWithPackage($this->subject())): ?>
            <div class="layout_right" id="communityad_event">
                <?php echo $this->content()->renderWidget("communityad.ads", array("itemCount" => Engine_Api::_()->getApi('settings', 'core')->getSetting($this->moduleName . '.adeventwidget', 3), "loaded_by_ajax" => 0, 'widgetId' => 'event_contenttype')); ?>
            </div>
            <div class="layout_middle">
            <?php endif; ?>
            <?php $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1); ?>
            <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
            <script type="text/javascript">
                en4.core.runonce.add(function () {
                    showContentTypeEvent();
                });

                function showContentTypeEvent() {
    <?php if (!$this->renderOne): ?>
                        $$('.host_profile_events_links_filter<?php echo $this->identity ?>').removeEvents('click').addEvent('click', function (event) {
                            el = $(event.target);
                            var filterType = '<?php echo $this->filterType; ?>';
                            if (el.get('data-fillter') == 'upcoming') {
                                filterType = 'upcoming';
                            } else if (el.get('data-fillter') == 'onlyUpcoming') {
                                filterType = 'onlyUpcoming';
                            } else if (el.get('data-fillter') == 'onlyOngoing') {
                                filterType = 'onlyOngoing';
                            } else if (el.get('data-fillter') == 'past') {
                                filterType = 'past';
                            }
                            if ($$('.seaocore_profile_list_more'))
                                $$('.seaocore_profile_list_more').setStyle('display', 'none');
                            loadContentTypeEvent($(event.target), 1, '<?php echo $this->ownertype; ?>', filterType);
                        });
    <?php endif; ?>
                }

                function loadContentTypeEvent(el, byfilter, ownertypevalue, filterType) {
                    var anchor = $('profile_siteevents<?php echo $this->identity ?>').getParent();
                    if (byfilter == 1) {
                        $('siteevent_view_lists').innerHTML = '<div class="seaocore_content_loader"></div>';
                    } else {
                        var ele = $('profile_siteevents<?php echo $this->identity ?>').getParent();
                        ele.innerHTML = '<div class="seaocore_content_loader"></div>';
                    }

                    en4.core.request.send(new Request.HTML({
                        url: en4.core.baseUrl + 'widget/index/mod/siteevent/name/contenttype-events',
                        data: {
                            format: 'html',
                            subject: en4.core.subject.guid,
                            page: el.get('data-page'),
                            filterType: filterType,
                            is_ajax_load: true,
                            identity: '<?php echo $this->identity; ?>',
                            eventviewType: '<?php echo $this->eventviewType; ?>',
                            layouts_order: '<?php echo $this->defaultOrder; ?>',
                            defaultView: '<?php echo $this->defaultView; ?>',
                            list_view: '<?php echo $this->list_view; ?>',
                            grid_view: '<?php echo $this->grid_view; ?>',
                            map_view: '<?php echo $this->map_view; ?>',
                            detactLocation: '<?php echo $this->detactLocation; ?>',
                            defaultLocationDistance: '<?php echo $this->defaultLocationDistance; ?>',
                            eventInfo:<?php echo json_encode($this->statistics); ?>,
                            ratingType: '<?php echo $this->ratingType; ?>',
                            showContent:<?php echo json_encode($this->showContent); ?>,
                            category_id: '<?php echo $this->category_id; ?>',
                            subcategory_id: '<?php echo $this->subcategory_id; ?>',
                            subsubcategory_id: '<?php echo $this->subsubcategory_id; ?>',
                            title_truncation: '<?php echo $this->title_truncation; ?>',
                            title_truncationGrid: '<?php echo $this->title_truncationGrid; ?>',
                            show_content: '<?php echo $this->show_content; ?>',
                            ownertype: ownertypevalue,
                            eventFilterTypes:<?php echo json_encode($this->eventFilterTypes); ?>,
                            eventFilterTypesCount: <?php echo $this->eventFilterTypesCount; ?>,
                            eventOwnerTypeCount: <?php echo $this->eventOwnerTypeCount; ?>,
                            columnWidth: '<?php echo $this->columnWidth; ?>',
                            columnHeight: '<?php echo $this->columnHeight; ?>',
                            itemCount:'<?php echo $this->limit; ?>'
                        }
                    }), {
                        'element': anchor,
                        'force': true
                    });
                }
            </script>
            <?php if ($this->enableLocation && $this->map_view && $this->paginator->count() > 0): ?>

                <?php
//GET API KEY
                $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
                $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
                ?>

            <?php endif; ?>

            <div id="siteevent_location_map_none" style="display: none;"></div>  

            <?php //if($this->is_ajax_load): ?>
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

            <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
            <?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>
            <?php $latitude = $this->settings->getSetting('siteevent.map.latitude', 0); ?>
            <?php $longitude = $this->settings->getSetting('siteevent.map.longitude', 0); ?>
            <?php $defaultZoom = $this->settings->getSetting('siteevent.map.zoom', 1); ?>
            <?php $enableBouce = $this->settings->getSetting('siteevent.map.sponsored', 1); ?>
            <?php //if(empty($this->isajax)):?>

            <?php if ($this->canCreate): ?>
                <div class="clr seaocore_add">
                    <?php if (Engine_Api::_()->siteevent()->hasPackageEnable()): ?>
                        <a class= "buttonlink icon_siteevent_add" href='<?php echo $this->url(array('action' => 'index', 'parent_type' => $this->subject()->getType(), 'parent_id' => $this->subject()->getIdentity()), 'siteevent_package', true) ?>'><?php echo $this->translate('Create an Event'); ?></a>
                    <?php else: ?>
                        <a class='buttonlink icon_siteevent_add' href="<?php echo $this->url(array('action' => 'create', 'parent_type' => $this->subject()->getType(), 'parent_id' => $this->subject()->getIdentity()), 'siteevent_general', true); ?>"><?php echo $this->translate('Create an Event'); ?></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($this->paramsLocation['eventFilterTypesCount']) && ($this->paramsLocation['eventFilterTypesCount'] >= 1 || $this->eventOwnerTypeCount >= 1) || $this->eventFilterTypesCount) : ?>
                <div class="siteevent_members_search b_medium o_hidden">

                    <?php if (isset($this->paramsLocation['eventFilterTypesCount']) && $this->paramsLocation['eventFilterTypesCount'] > 1 || $this->eventFilterTypesCount) : ?>
                        <div class="siteevent_members_search_filters fleft p5">
                            <?php
                            $this->fillter = 'upcoming';
                            if ($this->eventFilterTypes && in_array("onlyOngoing", $this->eventFilterTypes)) {
                                if (in_array("upcoming", $this->eventFilterTypes)) {
                                    $this->fillter = 'onlyUpcoming';
                                } else {
                                    $this->fillter = 'upcoming';
                                }
                            }
                            ?>
                            <?php if ($this->eventFilterTypes && in_array("onlyOngoing", $this->eventFilterTypes)): ?>
                                <a href="javascript:void(0);" class="<?php if ($this->filterType == 'onlyOngoing'): ?>bold<?php endif; ?> host_profile_events_links_filter<?php echo $this->identity ?>" data-page = '1' data-fillter="onlyOngoing"><?php echo $this->translate('Ongoing'); ?></a>

                            <?php endif ?>

                            <?php if (in_array("onlyOngoing", $this->eventFilterTypes) && in_array("upcoming", $this->eventFilterTypes)): ?>
                                |
                            <?php endif; ?>
                            <?php if (in_array("upcoming", $this->eventFilterTypes)): ?>
                                <a href="javascript:void(0);" class="<?php if ($this->filterType == 'onlyUpcoming' || $this->filterType == 'upcoming'): ?>bold<?php endif; ?> host_profile_events_links_filter<?php echo $this->identity ?>" data-page = '1' data-fillter="<?php echo $this->fillter ?>"><?php echo $this->translate('Upcoming'); ?></a>
                                | 

                            <?php endif ?>
                            <?php if (in_array("past", $this->eventFilterTypes)): ?>
                                <a href="javascript:void(0);"  class="<?php if ($this->filterType == 'past'): ?>bold<?php endif; ?> host_profile_events_links_filter<?php echo $this->identity ?>" data-page = '1' data-fillter="past"><?php echo $this->translate('Past'); ?></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($this->paramsLocation['eventFilterTypesCount'] >= 1) : ?> 
                        <div class="siteevent_members_search_right fright">
                            <select class="mleft5" id="" name="eventOwnerType" onchange="loadContentTypeEvent(this, 1, this.value, '<?php echo $this->filterType; ?>')">
                                <option value="lead" <?php if ($this->ownertype == 'lead') echo "selected"; ?> ><?php echo $this->translate("Leading") ?></option>
                                <option value="host" <?php if ($this->ownertype == 'host') echo "selected"; ?> ><?php echo $this->translate("Hosting") ?></option>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if ($this->paginator->count() > 0): ?>
                <div id="siteevent_view_lists" class="clr">
                    <?php if (($this->list_view && $this->grid_view) || ($this->map_view && $this->grid_view) || ($this->list_view && $this->map_view)): ?>
                        <div class="siteevent_browse_lists_view_options">
                            <div class="fleft"> 
                                <?php if ($this->categoryName && $doNotShowTopContent != 1): ?>
                                    <h4 class="siteevent_browse_lists_view_options_head">
                                        <?php echo $this->translate($this->categoryName); ?>
                                    </h4>
                                <?php endif; ?>
                                <?php echo $this->translate(array('%s event found.', '%s events found.', $this->totalResults), $this->locale()->toNumber($this->totalResults)) ?>
                            </div>

                            <?php if ($this->enableLocation && $this->map_view): ?> 
                                <span class="seaocore_tab_select_wrapper fright">
                                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Map View"); ?></div>
                                    <span class="seaocore_tab_icon tab_icon_map_view" onclick="switchviewSEContent(2);" ></span>
                                </span>
                            <?php endif; ?>
                            <?php if ($this->grid_view): ?>
                                <span class="seaocore_tab_select_wrapper fright">
                                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
                                    <span class="seaocore_tab_icon tab_icon_grid_view" onclick="switchviewSEContent(1);" ></span>
                                </span>
                            <?php endif; ?>
                            <?php if ($this->list_view): ?>
                                <span class="seaocore_tab_select_wrapper fright">
                                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
                                    <span class="seaocore_tab_icon tab_icon_list_view" onclick="switchviewSEContent(0);" ></span>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php //endif;  ?>

                    <?php if ($this->list_view): ?>

                        <div id="siteevent_grid_view" <?php if ($this->defaultView != 0): ?> style="display: none;" <?php endif; ?>>
                            <?php if (empty($this->eventviewType)): ?>

                                <ul class="siteevent_browse_list">
                                    <?php foreach ($this->paginator as $siteevent): ?>
                                        <?php ?>
                                        <?php if (!empty($siteevent->sponsored)): ?>
                                            <li class="list_sponsered1 b_medium">
                                            <?php else: ?>
                                            <li class="b_medium">
                                            <?php endif; ?>
                                            <div class='siteevent_browse_list_photo b_medium'>
                                                <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $siteevent->featured): ?>
                                                    <i class="siteevent_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
                                                <?php endif; ?>
                                                <?php if (!empty($this->statistics) && in_array('newLabel', $this->statistics) && $siteevent->newlabel): ?>
                                                    <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                                <?php endif; ?>

                                                <?php echo $this->htmlLink($siteevent->getHref(), $this->itemPhoto($siteevent, 'thumb.main', '', array('align' => 'center'))) ?>

                                                <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($siteevent->sponsored)): ?>
                                                    <div class="siteevent_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505'); ?>">
                                                        <?php echo $this->translate('SPONSORED'); ?>        
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) && !empty($this->statistics) && in_array('ratingStar', $this->statistics)): ?>
                                                <div class="siteevent_browse_list_rating">
                                                    <?php if (!empty($siteevent->rating_editor) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_editor')): ?>
                                                        <div class="clr">	
                                                            <div class="siteevent_browse_list_rating_stats">
                                                                <?php echo $this->translate("Editor Rating"); ?>
                                                            </div>
                                                            <?php $ratingData = $this->ratingTable->ratingbyCategory($siteevent->event_id, 'editor', $siteevent->getType()); ?>
                                                            <div class="siteevent_ur_show_rating_star fnone o_hidden">
                                                                <span class="siteevent_browse_list_rating_stars">
                                                                    <span class="fleft">
                                                                        <?php echo $this->showRatingStarSiteevent($siteevent->rating_editor, 'editor', 'big-star'); ?>
                                                                    </span>
                                                                    <?php if (count($ratingData) > 1): ?>
                                                                        <i class="fright arrow_btm"></i>
                                                                    <?php endif; ?>
                                                                </span>

                                                                <?php if (count($ratingData) > 1): ?>
                                                                    <div class="siteevent_ur_show_rating br_body_bg b_medium">
                                                                        <div class="siteevent_profile_rating_parameters siteevent_ur_show_rating_box">

                                                                            <?php foreach ($ratingData as $reviewcat): ?>

                                                                                <div class="o_hidden">
                                                                                    <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                                                                        <div class="parameter_title">
                                                                                            <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                                                                        </div>
                                                                                        <div class="parameter_value">
                                                                                            <?php echo $this->showRatingStarSiteevent($reviewcat['avg_rating'], 'editor', 'small-box', $reviewcat['ratingparam_name']); ?>
                                                                                        </div>
                                                                                    <?php else: ?>
                                                                                        <div class="parameter_title">
                                                                                            <?php echo $this->translate("Overall Rating"); ?>
                                                                                        </div>	
                                                                                        <div class="parameter_value" style="margin: 0px 0px 5px;">
                                                                                            <?php echo $this->showRatingStarSiteevent($reviewcat['avg_rating'], 'editor', 'big-star'); ?>
                                                                                        </div>
                                                                                    <?php endif; ?> 
                                                                                </div>
                                                                            <?php endforeach; ?>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div> 
                                                    <?php endif; ?>
                                                    <?php if (!empty($siteevent->rating_users) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_users')): ?>
                                                        <div class="clr">
                                                            <div class="siteevent_browse_list_rating_stats">
                                                                <?php echo $this->translate("User Ratings"); ?><br />
                                                                <?php
                                                                $totalUserReviews = $siteevent->review_count;
                                                                if ($siteevent->rating_editor) {
                                                                    $totalUserReviews = $siteevent->review_count - 1;
                                                                }
                                                                ?>
                                                                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowreview', 1)): ?>
                                                                    <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $totalUserReviews), $this->locale()->toNumber($totalUserReviews)) ?>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?php $ratingData = $this->ratingTable->ratingbyCategory($siteevent->event_id, 'user', $siteevent->getType()); ?>
                                                            <div class="siteevent_ur_show_rating_star fnone o_hidden">
                                                                <span class="siteevent_browse_list_rating_stars">
                                                                    <span class="fleft">
                                                                        <?php echo $this->showRatingStarSiteevent($siteevent->rating_users, 'user', 'big-star'); ?>
                                                                    </span>
                                                                    <?php if (count($ratingData) > 1): ?>
                                                                        <i class="fright arrow_btm"></i>
                                                                    <?php endif; ?>
                                                                </span>

                                                                <?php if (count($ratingData) > 1): ?>
                                                                    <div class="siteevent_ur_show_rating  br_body_bg b_medium">
                                                                        <div class="siteevent_profile_rating_parameters siteevent_ur_show_rating_box">

                                                                            <?php foreach ($ratingData as $reviewcat): ?>

                                                                                <div class="o_hidden">
                                                                                    <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                                                                        <div class="parameter_title">
                                                                                            <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                                                                        </div>
                                                                                        <div class="parameter_value">
                                                                                            <?php echo $this->showRatingStarSiteevent($reviewcat['avg_rating'], 'user', 'small-box', $reviewcat['ratingparam_name']); ?>
                                                                                        </div>
                                                                                    <?php else: ?>
                                                                                        <div class="parameter_title">
                                                                                            <?php echo $this->translate("Overall Rating"); ?>
                                                                                        </div>	
                                                                                        <div class="parameter_value" style="margin: 0px 0px 5px;">
                                                                                            <?php echo $this->showRatingStarSiteevent($reviewcat['avg_rating'], 'user', 'big-star'); ?>
                                                                                        </div>
                                                                                    <?php endif; ?> 
                                                                                </div>

                                                                            <?php endforeach; ?>
                                                                        </div>
                                                                    </div> 
                                                                <?php endif; ?> 
                                                            </div>
                                                        </div>  
                                                    <?php endif; ?>

                                                    <?php if (!empty($siteevent->rating_avg) && ($ratingValue == 'rating_avg')): ?>
                                                        <div class="clr">
                                                            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowreview', 1)): ?>
                                                                <div class="siteevent_browse_list_rating_stats">
                                                                    <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $siteevent->review_count), $this->locale()->toNumber($siteevent->review_count)) ?>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php $ratingData = $this->ratingTable->ratingbyCategory($siteevent->event_id, null, $siteevent->getType()); ?>
                                                            <div class="siteevent_ur_show_rating_star fnone o_hidden">
                                                                <span class="siteevent_browse_list_rating_stars">
                                                                    <span class="fleft">
                                                                        <?php echo $this->showRatingStarSiteevent($siteevent->rating_avg, $ratingType, 'big-star'); ?>
                                                                    </span>
                                                                    <?php if (count($ratingData) > 1): ?>
                                                                        <i class="fright arrow_btm"></i>
                                                                    <?php endif; ?>
                                                                </span>

                                                                <?php if (count($ratingData) > 1): ?>
                                                                    <div class="siteevent_ur_show_rating  br_body_bg b_medium">
                                                                        <div class="siteevent_profile_rating_parameters siteevent_ur_show_rating_box">

                                                                            <?php foreach ($ratingData as $reviewcat): ?>

                                                                                <div class="o_hidden">
                                                                                    <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                                                                        <div class="parameter_title">
                                                                                            <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                                                                        </div>
                                                                                        <div class="parameter_value">
                                                                                            <?php echo $this->showRatingStarSiteevent($reviewcat['avg_rating'], $ratingType, 'small-box', $reviewcat['ratingparam_name']); ?>
                                                                                        </div>
                                                                                    <?php else: ?>
                                                                                        <div class="parameter_title">
                                                                                            <?php echo $this->translate("Overall Rating"); ?>
                                                                                        </div>	
                                                                                        <div class="parameter_value" style="margin: 0px 0px 5px;">
                                                                                            <?php echo $this->showRatingStarSiteevent($reviewcat['avg_rating'], 'user', 'big-star'); ?>
                                                                                        </div>
                                                                                    <?php endif; ?> 
                                                                                </div>

                                                                            <?php endforeach; ?>
                                                                        </div>
                                                                    </div> 
                                                                <?php endif; ?> 
                                                            </div>
                                                        </div>  
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>

                                            <div class='siteevent_browse_list_info'>  
                                                <div class='siteevent_browse_list_info_header o_hidden'>
                                                    <div class="siteevent_list_title">
                                                        <?php echo $this->htmlLink($siteevent->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncation), array('title' => $siteevent->getTitle())); ?>
                                                    </div>
                                                    <div class="clear"></div>
                                                </div>

                                                <?php if (!empty($this->statistics)) : ?>
                                                    <?php echo $this->eventInfo($siteevent, $this->statistics, array('ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation, 'showEventType' => $this->filterType)); ?>
                                                <?php endif; ?>

                                                <div class='siteevent_browse_list_info_stat seaocore_txt_light'>
                                                    <?php echo $this->locale()->toEventDateTime($siteevent->starttime, array('size' => $datetimeFormat)); ?>
                                                </div>

                                                <?php if ($this->descriptionPosition): ?>
                                                    <div class='siteevent_browse_list_info_blurb'>
                                                        <?php //if($this->bottomLine):  ?>
                                                        <?php //echo $this->viewMore($siteevent->getBottomLine(), 125, 5000);?>
                                                        <?php //else:  ?>
                                                        <?php echo $this->viewMore(strip_tags($siteevent->body), 125, 5000); ?>
                                                        <?php //endif;  ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>

                            <?php else: ?>

                                <ul class="siteevent_browse_list">
                                    <?php foreach ($this->paginator as $siteevent): ?>
                                        <?php ?>
                                        <li class="b_medium">
                                            <div class='siteevent_browse_list_photo b_medium'>
                                                <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $siteevent->featured): ?>
                                                    <i class="siteevent_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
                                                <?php endif; ?>
                                                <?php if (!empty($this->statistics) && in_array('newLabel', $this->statistics) && $siteevent->newlabel): ?>
                                                    <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                                <?php endif; ?>
                                                <?php echo $this->htmlLink($siteevent->getHref(), $this->itemPhoto($siteevent, 'thumb.main', '', array('align' => 'center'))) ?>
                                                <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($siteevent->sponsored)): ?>
                                                    <div class="siteevent_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505'); ?>">
                                                        <?php echo $this->translate('SPONSORED'); ?>                 
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <div class='siteevent_browse_list_info'>
                                                <div class="siteevent_browse_list_info_header">
                                                    <div class="siteevent_list_title_small">
                                                        <?php echo $this->htmlLink($siteevent->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncation), array('title' => $siteevent->getTitle())); ?>
                                                    </div>
                                                </div>

                                                <div class="siteevent_browse_list_information fleft">
                                                    <?php if (!empty($this->statistics)) : ?>
                                                        <?php echo $this->eventInfo($siteevent, $this->statistics, array('ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation, 'showEventType' => $this->filterType)); ?>
                                                    <?php endif; ?>
                                                </div>

                                                <?php if (!empty($this->statistics) && in_array('price', $this->statistics) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0) && !empty($siteevent->price) && $siteevent->price > 0) : ?>
                                                    <?php $priceInfos = $siteevent->getPriceInfo(); ?>
                                                    <?php $priceInfoCount = Count($priceInfos); ?>
                                                <?php endif; ?>

                                                <?php if (0)://if(Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.wheretobuy', 0)):  ?>
                                                    <div class="siteevent_browse_list_price_info">
                                                        <?php if ($priceInfoCount > 0): ?>
                                                            <?php
                                                            $minPrice = $siteevent->getWheretoBuyMinPrice();
                                                            $maxPrice = $siteevent->getWheretoBuyMaxPrice()
                                                            ?>
                                                            <?php if ($minPrice): ?>
                                                                <div class="siteevent_price">

                                                                    <?php if ($minPrice == $maxPrice): ?>
                                                                        <?php echo $this->locale()->toCurrency($minPrice, $currency); ?>
                                                                    <?php elseif ($priceInfoCount == 2): ?>
                                                                        <?php echo $this->translate('%s and %1s', $this->locale()->toCurrency($minPrice, $currency), $this->locale()->toCurrency($maxPrice, $currency)); ?>
                                                                    <?php else: ?>
                                                                        <?php echo $this->locale()->toCurrency($minPrice, $currency); ?> - <?php echo $this->locale()->toCurrency($maxPrice, $currency); ?>
                                                                    <?php endif ?>
                                                                </div>
                                                            <?php endif ?>
                                                            <div class="siteevent_browse_list_price_info_stats">
                                                                <?php echo $this->translate(array('at %s store', 'at %s stores', $priceInfoCount), $this->locale()->toNumber($priceInfoCount)) ?>
                                                            </div>

                                                            <?php $iPrice = 0; ?>
                                                            <?php foreach ($priceInfos as $priceInfo): ?>
                                                                <?php $url = $this->url(array('action' => 'redirect', 'id' => $siteevent->getIdentity()), 'siteevent_priceinfo', true) . '?url=' . @base64_encode($priceInfo->url); ?>
                                                                <div class="siteevent_browse_list_price_info_stats">
                                                                    <?php if ($priceInfo->price > 0): ?> <?php echo $this->locale()->toCurrency($priceInfo->price, $currency); ?> <?php endif; ?> - <a href="<?php echo $url; ?>" target="_blank"><?php echo $priceInfo->wheretobuy_id == 1 ? $priceInfo->title : $priceInfo->wheretobuy_title; ?></a>
                                                                </div>
                                                                <?php if ($iPrice > 1) break; ?>
                                                                <?php $iPrice++; ?>
                                                            <?php endforeach; ?>

                                                            <?php //elseif($siteevent->price > 0 && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.wheretobuy', 0) == 2):  ?>
                                                        <?php elseif ($siteevent->price > 0 && 0 == 2): ?>  
                                                            <div class="siteevent_price">
                                                                <?php echo $this->locale()->toCurrency($siteevent->price, $currency); ?>
                                                            </div>
                                                        <?php endif; ?>

                                                    </div>  
                                                <?php endif; ?>

                                                <?php if ($this->descriptionPosition): ?>
                                                    <div class='siteevent_browse_list_des o_hidden'>
                                                        <?php //if($this->bottomLine): ?>
                                                        <?php //echo $this->viewMore($siteevent->getBottomLine(), 300, 5000); ?>
                                                        <?php //else: ?>
                                                        <?php echo $this->viewMore(strip_tags($siteevent->body), 300, 5000); ?>
                                                        <?php //endif;   ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div class='siteevent_manage_list_options mtop10 clr fleft'>
                                                    <?php if ($siteevent->owner_id == $this->viewer()->getIdentity()) : ?>
                                                        <?php if ($this->can_edit) : ?>
                                                            <a href='<?php echo $this->url(array('action' => 'edit', 'event_id' => $siteevent->event_id), "siteevent_specific", true) ?>' class='buttonlink seaocore_icon_edit'>
                                                                <?php echo $this->translate("Dashboard"); ?></a>
                                                        <?php endif; ?>

                                                        <?php
                                                        if ($siteevent->draft == 1 && $this->can_edit)
                                                            echo $this->htmlLink(array('route' => "siteevent_specific", 'action' => 'publish', 'event_id' => $siteevent->event_id), $this->translate("Publish Event"), array(
                                                                'class' => 'buttonlink smoothbox icon_siteevent_publish'))
                                                            ?> 

                                                        <?php if (!$siteevent->closed && $this->can_edit): ?>
                                                            <a href='<?php echo $this->url(array('action' => 'close', 'event_id' => $siteevent->event_id), "siteevent_specific", true) ?>' class='buttonlink smoothbox icon_siteevent_cancel'><?php echo $this->translate("Cancel Event"); ?></a>
                                                        <?php elseif (1): ?>
                                                            <a href='<?php echo $this->url(array('action' => 'close', 'event_id' => $siteevent->event_id), "siteevent_specific", true) ?>' class='buttonlink smoothbox icon_siteevent_publish'><?php echo $this->translate("Re-publish Event"); ?></a>
                                                        <?php endif; ?>

                                                        <?php if ($this->can_edit): ?>
                                                            <a href='<?php echo $this->url(array('action' => 'delete', 'event_id' => $siteevent->event_id), "siteevent_specific", true) ?>' class='buttonlink seaocore_icon_delete'><?php echo $this->translate("Delete Event"); ?></a>
                                                        <?php endif; ?>
                                                    <?php endif; ?> 

                                                </div>
                                                <div class="fright siteevent_browse_list_info_btn">
                                                    <a href="<?php echo $siteevent->getHref(); ?>" class="siteevent_buttonlink"><?php echo $this->translate('Details &raquo;'); ?></a>
                                                </div>  
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>

                            <?php endif; ?>

                        </div>

                    <?php endif; ?>

                    <?php if ($this->grid_view): ?>
                        <div id="siteevent_image_view" class="siteevent_container" <?php if ($this->defaultView != 1): ?> style="display: none;" <?php endif; ?>>
                            <div class="siteevent_img_view">
                                <?php $isLarge = ($this->columnWidth > 170); ?>
                                <?php foreach ($this->paginator as $siteevent): ?>          
                                    <div class="siteevent_grid_view" style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;">
                                        <div class="siteevent_grid_thumb">
                                            <?php if (!empty($this->statistics) && in_array('newLabel', $this->statistics) && $siteevent->newlabel): ?>
                                                <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                            <?php endif; ?>
                                            <a href="<?php echo $siteevent->getHref() ?>" class="siteevent_thumb">
                                                <?php
                                                $url = $siteevent->getPhotoUrl($isLarge ? 'thumb.profile' : 'thumb.profile');
                                                if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/nophoto_event_thumb_normal.png';
                                                endif;
                                                ?>
                                                <span style="background-image: url(<?php echo $url; ?>);"></span>
                                            </a>
                                            <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $siteevent->featured): ?>
                                                <i class="siteevent_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
                                            <?php endif; ?>
                                            <?php if (!empty($this->titlePosition)) : ?>
                                                <div class="siteevent_grid_title">
                                                    <?php echo $this->htmlLink($siteevent->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncationGrid), array('title' => $siteevent->getTitle())) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($siteevent->sponsored)): ?>
                                            <div class="siteevent_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505'); ?>">
                                                <?php echo $this->translate('SPONSORED'); ?>                 
                                            </div>
                                        <?php endif; ?>
                                        <div class="siteevent_grid_info">
                                            <?php if (empty($this->titlePosition)) : ?>
                                                <div class="siteevent_grid_title">
                                                    <?php echo $this->htmlLink($siteevent->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncationGrid), array('title' => $siteevent->getTitle())) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($this->statistics)) : ?>
                                                <?php echo $this->eventInfo($siteevent, $this->statistics, array('view_type' => 'grid_view', 'titlePosition' => $this->titlePosition, 'ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation, 'showEventType' => $this->filterType)); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div id="siteevent_map_canvas_view_browse" <?php if ($this->defaultView != 2): ?> style="display: none;" <?php endif; ?>>
                        <div class="seaocore_map clr" style="overflow:hidden;">
                            <div id="siteevent_browse_map_canvas" class="siteevent_list_map"> </div>
                            <div class="clear mtop10"></div>
                            <?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
                            <?php if (!empty($siteTitle)) : ?>
                                <div class="seaocore_map_info"><?php echo $this->translate("Locations on %s", "<a href='' target='_blank'>$siteTitle</a>"); ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if ($this->enableLocation && $this->flageSponsored && $this->map_view && $enableBouce): ?>
                            <a href="javascript:void(0);" onclick="toggleBounce()" class="fleft siteevent_list_map_bounce_link"> <?php echo $this->translate('Stop Bounce'); ?></a>
                        <?php endif; ?>  
                    </div>

                    <div class="clear"></div>

                    <?php if ($this->paginator->count() > 1): ?>
                        <div class="seaocore_pagination">
                            <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
                            
                                <div id="siteevent_contenttype_previous" class="paginator_previous"> <?php $this->paramsLocation['page'] = $this->paginator->getCurrentPageNumber()- 1;?>
                                    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array('onclick' => 'en4.siteeventcontenttype.profileTabRequest({content_id : ' . $this->identity . ',requestParams:'. json_encode($this->paramsLocation).'});', 'class' => 'buttonlink icon_previous')); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
                            <?php $this->paramsLocation['page'] = $this->paginator->getCurrentPageNumber() + 1;?>
                                <div id="siteevent_contenttype_next" class="paginator_next">
                                    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array('onclick' => 'en4.siteeventcontenttype.profileTabRequest({content_id : ' . $this->identity . ',requestParams: '. json_encode($this->paramsLocation).'});', 'class' => 'buttonlink_right icon_next')); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </div>
            <?php elseif (isset($this->params['tag_id']) || isset($this->params['category_id'])): ?>
                <?php if ($this->filterType == 'past' && $this->paginator->getTotalItemCount()): ?>
                    <div id="siteevent_view_lists">
                        <div class="tip mtop10">
                            <span> 
                                <?php echo $this->translate('No past events could be found.'); ?>
                            </span> 
                        </div>
                    </div>
                <?php elseif (($this->filterType == 'upcoming' || $this->filterType == 'onlyUpcoming' || $this->filterType == 'onlyOngoing') && $this->paginator->getTotalItemCount()): ?>
                    <div id="siteevent_view_lists">
                        <div class="tip mtop10">
                            <span> <?php echo $this->translate('You do not have any event that match your search criteria.'); ?>
                                <?php if ($this->canCreate): ?>
                                    <?php if (Engine_Api::_()->siteevent()->hasPackageEnable()): ?>
                                        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'index', 'parent_type' => $this->subject()->getType(), 'parent_id' => $this->subject()->getIdentity()), "siteevent_package") . '">', '</a>'); ?>
                                    <?php else: ?>
                                        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'create', 'parent_type' => $this->subject()->getType(), 'parent_id' => $this->subject()->getIdentity()), "siteevent_general") . '">', '</a>'); ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </span> 
                        </div>
                    </div>
                <?php else: ?>
                    <div id="siteevent_view_lists">
                        <div class="tip mtip10">
                            <span> <?php echo $this->translate('No events have been created yet.'); ?>
                                <?php if ($this->canCreate): ?>
                                    <?php if (Engine_Api::_()->siteevent()->hasPackageEnable()): ?>
                                        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'index', 'parent_type' => $this->subject()->getType(), 'parent_id' => $this->subject()->getIdentity()), "siteevent_package") . '">', '</a>'); ?>
                                    <?php else: ?>
                                        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'create', 'parent_type' => $this->subject()->getType(), 'parent_id' => $this->subject()->getIdentity()), "siteevent_general") . '">', '</a>'); ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </span> 
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?><div id="siteevent_view_lists">
                    <div class="tip mtop10"> 
                        <span> 
                            <?php echo $this->translate('No events have been created yet.'); ?>
                            <?php if ($this->canCreate): ?>
                                <?php if (Engine_Api::_()->siteevent()->hasPackageEnable()): ?>
                                    <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'index', 'parent_type' => $this->subject()->getType(), 'parent_id' => $this->subject()->getIdentity()), "siteevent_package") . '">', '</a>'); ?>
                                <?php else: ?>
                                    <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'create', 'parent_type' => $this->subject()->getType(), 'parent_id' => $this->subject()->getIdentity()), "siteevent_general") . '">', '</a>'); ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </span>
                    </div></div>
            <?php endif; ?>
            <!-- </div>-->
            <script type="text/javascript" >
                function switchviewSEContent(flage) {
                    if (flage == 2) {
                        if ($('siteevent_map_canvas_view_browse')) {
                            $('siteevent_map_canvas_view_browse').style.display = 'block';
    <?php if ($this->enableLocation && $this->map_view && $this->paginator->count() > 0): ?>
                                google.maps.event.trigger(mapSEContent, 'resize');
                                mapSEContent.setZoom(<?php echo $defaultZoom ?>);
                                mapSEContent.setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>));
    <?php endif; ?>
                            if ($('siteevent_grid_view'))
                                $('siteevent_grid_view').style.display = 'none';
                            if ($('siteevent_image_view'))
                                $('siteevent_image_view').style.display = 'none';
                        }
                    } else if (flage == 1) {
                        if ($('siteevent_image_view')) {
                            if ($('siteevent_map_canvas_view_browse'))
                                $('siteevent_map_canvas_view_browse').style.display = 'none';
                            if ($('siteevent_grid_view'))
                                $('siteevent_grid_view').style.display = 'none';
                            $('siteevent_image_view').style.display = 'block';
                        }
                    } else {
                        if ($('siteevent_grid_view')) {
                            if ($('siteevent_map_canvas_view_browse'))
                                $('siteevent_map_canvas_view_browse').style.display = 'none';
                            $('siteevent_grid_view').style.display = 'block';
                            if ($('siteevent_image_view'))
                                $('siteevent_image_view').style.display = 'none';
                        }
                    }
                }
            </script>

            <script type="text/javascript">

                /* moo style */
                en4.core.runonce.add(function () {
                    //opacity / display fix
                    $$('.siteevent_tooltip').setStyles({
                        opacity: 0,
                        display: 'block'
                    });
                    //put the effect in place
                    $$('.jq-siteevent_tooltip li').each(function (el, i) {
                        el.addEvents({
                            'mouseenter': function () {
                                el.getElement('div').fade('in');
                            },
                            'mouseleave': function () {
                                el.getElement('div').fade('out');
                            }
                        });
                    });
    <?php if ($this->paginator->count() > 0): ?>
        <?php if ($this->enableLocation && $this->map_view): ?>
                            initializeSiteeventMap();
        <?php endif; ?>

                        switchviewSEContent(<?php echo $this->defaultView ?>);
    <?php endif; ?>
                });

            </script>

            <?php if ($this->enableLocation && $this->map_view && $this->paginator->count() > 0): ?>

                <script type="text/javascript">
                    //<![CDATA[
                    // this variable will collect the html which will eventually be placed in the side_bar
                    var side_bar_html = "";

                    // arrays to hold copies of the markers and html used by the side_bar
                    // because the function closure trick doesnt work there
                    var gmarkers = [];

                    // global "map" variable
                    var mapSEContent = null;
                    // A function to create the marker and set up the event window function
                    function createMarker(latlng, name, html) {
                        var contentString = html;
                        if (name == 0) {
                            var marker = new google.maps.Marker({
                                position: latlng,
                                map: mapSEContent,
                                animation: google.maps.Animation.DROP,
                                zIndex: Math.round(latlng.lat() * -100000) << 5
                            });
                        }
                        else {
                            var marker = new google.maps.Marker({
                                position: latlng,
                                map: mapSEContent,
                                draggable: false,
                                animation: google.maps.Animation.BOUNCE
                            });
                        }
                        gmarkers.push(marker);
                        google.maps.event.addListener(marker, 'click', function () {
                            infowindow.setContent(contentString);
                            google.maps.event.trigger(mapSEContent, 'resize');
                            infowindow.open(mapSEContent, marker);
                        });
                    }

                    function initializeSiteeventMap() {

                        // create the map
                        var myOptions = {
                            zoom: <?php echo $defaultZoom ?>,
                            center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
                            navigationControl: true,
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        }
                        mapSEContent = new google.maps.Map(document.getElementById("siteevent_browse_map_canvas"),
                                myOptions);

                        google.maps.event.addListener(mapSEContent, 'click', function () {
                            infowindow.close();
                            google.maps.event.trigger(mapSEContent, 'resize');
                        });


        <?php foreach ($this->locations as $location) : ?>
            <?php if ($this->siteevent[$location->event_id]->canView()): ?>
                                // obtain the attribues of each marker
                                var lat = <?php echo $location->latitude ?>;
                                var lng =<?php echo $location->longitude ?>;
                                var point = new google.maps.LatLng(lat, lng);
                <?php if (!empty($enableBouce)): ?>
                                    var sponsored = <?php echo $this->siteevent[$location->event_id]->sponsored ?>
                <?php else: ?>
                                    var sponsored = 0;
                <?php endif; ?>

                                var contentString = "<?php
                echo $this->string()->escapeJavascript($this->partial('application/modules/Siteevent/views/scripts/_mapInfoWindowContent.tpl', array(
                            'siteevent' => $this->siteevent[$location->event_id],
                            'ratingValue' => $ratingValue,
                            'ratingType' => $ratingType,
                            'postedby' => $this->postedby,
                            'statistics' => $this->statistics,
                            'showContent' => array("price", "location"),
                            'content_type' => null,
                            'postedbytext' => 'EVENT',
                            'ratingShow' => $ratingShow)), false);
                ?>";

                                var marker = createMarker(point, sponsored, contentString);
            <?php endif; ?>
        <?php endforeach; ?>
                    }

                    var infowindow = new google.maps.InfoWindow(
                            {
                                size: new google.maps.Size(250, 50)
                            });

                    function toggleBounce() {
                        for (var i = 0; i < gmarkers.length; i++) {
                            if (gmarkers[i].getAnimation() != null) {
                                gmarkers[i].setAnimation(null);
                            }
                        }
                    }
                </script>
            <?php endif; ?>

        <?php else: ?>
            <div id="layout_siteevent_browse_events_<?php echo $this->identity; ?>">
                <!--    <div class="seaocore_content_loader"></div>-->
            </div>
            <script type="text/javascript">
                var requestParams = $merge(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'})
                var params = {
                    'detactLocation': <?php echo $this->detactLocation; ?>,
                    'responseContainer': 'layout_siteevent_browse_events_<?php echo $this->identity; ?>',
                    requestParams: requestParams
                };

                var timeOutForLocationDetaction = 0;

    <?php if ($this->detactLocation && $this->isajax): ?>
                    var locationsParams = {};
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function (position) {
                            var lat = position.coords.latitude;
                            var lng = position.coords.longitude;

                            mapGetDirection = new google.maps.Map(document.getElementById("siteevent_location_map_none"), {
                                zoom: 8,
                                center: new google.maps.LatLng(lat, lng),
                                navigationControl: true,
                                mapTypeId: google.maps.MapTypeId.ROADMAP
                            });

                            if (!position.address) {
                                var service = new google.maps.places.PlacesService(mapGetDirection);
                                var request = {
                                    location: new google.maps.LatLng(lat, lng),
                                    radius: 500
                                };

                                service.search(request, function (results, status) {
                                    if (status == 'OK') {
                                        var index = 0;
                                        var radian = 3.141592653589793 / 180;

                                        locationsParams.location = (results[index].vicinity) ? results[index].vicinity : '';
                                        locationsParams.Latitude = lat;
                                        locationsParams.Longitude = lng;
                                        locationsParams.locationmiles = <?php echo $this->defaultLocationDistance ?>;
                                        setLocationsParams();
                                    }
                                });
                            } else {
                                var delimiter = (position.address && position.address.street != '' && position.address.city != '') ? ', ' : '';
                                var location = (position.address) ? (position.address.street + delimiter + position.address.city) : '';

                                locationsParams.location = location;
                                locationsParams.Latitude = lat;
                                locationsParams.Longitude = lng;
                                locationsParams.locationmiles = <?php echo $this->defaultLocationDistance ?>;
                                setLocationsParams();
                            }
                        }, function () {
                            timeOutForLocationDetaction = 1;
                            en4.seaocore.locationBased.sendReq(params);
                        }, {
                            maximumAge: 6000,
                            timeout: 3000
                        });

                        var locationTimeout = window.setTimeout(function () {
                            if (timeOutForLocationDetaction == 0) {
                                en4.seaocore.locationBased.sendReq(params);
                            }
                        }, 3000);

                        var setLocationsParams = function () {
                            if (!document.getElementById('location'))
                                return;
                            document.getElementById('location').value = locationsParams.location;
                            if (document.getElementById('Latitude'))
                                document.getElementById('Latitude').value = locationsParams.latitude;
                            if (document.getElementById('Longitude'))
                                document.getElementById('Longitude').value = locationsParams.longitude;
                            if (document.getElementById('locationmiles'))
                                document.getElementById('locationmiles').value = locationsParams.locationmiles;

                            params.requestParams = $merge(params.requestParams, locationsParams);

                            timeOutForLocationDetaction = 1;

                            en4.seaocore.locationBased.sendReq(params);
                        }
                    }
                    else {
                        timeOutForLocationDetaction = 1;
                        en4.seaocore.locationBased.sendReq(params);
                    }
    <?php elseif ($this->isajax): ?>
                    timeOutForLocationDetaction = 1;
                    en4.seaocore.locationBased.sendReq(params);
    <?php endif; ?>
            </script>  

            <?php //endif;   ?>  


        <?php endif; ?>

        <?php if (empty($this->isajax)) : ?>
        </div>
    <?php endif; ?>

    <?php if ($this->moduleName != 'sitereview') : ?>
        <script type="text/javascript">
            var adwithoutpackage = '<?php echo Engine_Api::_()->$moduleName()->showAdWithPackage($this->subject()); ?>';
            var event_ads_display = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting($moduleName . '.adeventwidget', 3); ?>';
            ;
            var is_ajax_divhide = '<?php echo $this->isajax; ?>';
            var execute_Request_Event = '<?php echo $this->show_content; ?>';
            //window.addEvent('domready', function () {
            var show_widgets = '<?php echo $this->widgets ?>';
            var ContenttypeeventtabId = '<?php echo $this->module_tabid; ?>';
            var ContenttypeeventTabIdCurrent = '<?php echo $this->identity_temp; ?>';
            var <?php echo $this->subject()->getShortType(); ?>_communityad_integration = 1;

            if (ContenttypeeventTabIdCurrent == ContenttypeeventtabId) {
                if (<?php echo $this->subject()->getShortType(); ?>_showtitle != 0) {
                    if ($('profile_status') && show_widgets == 1) {
                        $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->subject()->getTitle()) ?><?php echo $this->translate(' &raquo; '); ?><?php echo $this->translate('Events'); ?></h2>";
                    }
                    if ($('layout_event')) {
                        $('layout_event').style.display = 'block';
                    }
                }
                //hideWidgetsForModule('siteeventcontenttype');
                prev_tab_id = '<?php echo $this->identity; ?>';
                prev_tab_class = 'layout_siteevent_contenttype_events_<?php echo $this->identity; ?>';
                execute_Request_Event = true;
                hideLeftContainer(event_ads_display, <?php echo $this->subject()->getShortType(); ?>_communityad_integration, adwithoutpackage);
            }
            else if (is_ajax_divhide != 1) {
                if ($('global_content').getElement('.layout_siteevent_contenttype_events_<?php echo $this->identity; ?>')) {
                    $('global_content').getElement('.layout_siteevent_contenttype_events_<?php echo $this->identity; ?>').style.display = 'none';
                }
            }
            // });
            if ($("id_<?php echo $this->identity; ?>")) {
                $("id_<?php echo $this->identity; ?>").getParent('.layout_siteevent_contenttype_events').addClass("layout_siteevent_contenttype_events_<?php echo $this->identity; ?>");
            }
            $$('.tab_<?php echo $this->identity; ?>').addEvent('click', function () {
                $('global_content').getElement('.layout_siteevent_contenttype_events_<?php echo $this->identity; ?>').style.display = 'block';
                if (<?php echo $this->subject()->getShortType(); ?>_showtitle != 0) {
                    if ($('profile_status') && show_widgets == 1) {
                        $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->subject()->getTitle()) ?><?php echo $this->translate(' &raquo; '); ?><?php echo $this->translate('Events'); ?></h2>";
                    }
                }
                hideWidgetsForModule('siteeventcontenttype');
                $('id_' + <?php echo $this->identity ?>).style.display = "block";
                if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->identity; ?>') {
                    $$('.' + prev_tab_class).setStyle('display', 'none');
                }
                if (prev_tab_id != '<?php echo $this->identity; ?>') {
                    execute_Request_Event = false;
                    prev_tab_id = '<?php echo $this->identity; ?>';
                    prev_tab_class = 'layout_siteevent_contenttype_events_<?php echo $this->identity; ?>';
                }
                if (execute_Request_Event == false) {
                    ShowContent('<?php echo $this->identity; ?>', execute_Request_Event, '<?php echo $this->identity_temp ?>', 'event', 'siteevent', 'contenttype-events', <?php echo $this->subject()->getShortType(); ?>_showtitle, 'null', event_ads_display, <?php echo $this->subject()->getShortType(); ?>_communityad_integration, adwithoutpackage);

                    execute_Request_Event = true;
                }
                // 			if('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting($this->modulename . '.communityads', 1); ?>' && event_ads_display == 0)
                // 	{setLeftLayoutForPage(); 	} 
            });
        </script>
    <?php endif; ?>