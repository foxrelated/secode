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

<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css')
        ->prependStylesheet($baseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css')
        ->prependStylesheet($baseUrl . 'application/modules/Siteevent/externals/styles/style_rating.css');
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js');
?>

<?php if (!empty($this->titleLink)): ?>
    <span class="fright siteevent_widgets_more_link mright5">
        <?php echo $this->titleLink; ?>
    </span>
<?php endif; ?>

<?php if ($this->enableLocation): ?>
    <?php
    $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
    ?>
<?php endif; ?>

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

    <?php if (empty($this->is_ajax)): ?>

        <div class="layout_core_container_tabs">
            <?php if ($this->tabCount > 1 || count($this->layouts_views) > 1): ?>
                <div class="tabs_alt tabs_parent tabs_parent_siteevent_home">
                    <ul id="main_tabs" identity='<?php echo $this->identity ?>'>
                        <?php if ($this->tabCount > 1): ?>
                            <?php foreach ($this->tabs as $key => $tab): ?>
                                <li class="tab_li_<?php echo $this->identity ?> <?php echo $key == 0 ? 'active' : ''; ?>" rel="<?php echo $tab; ?>">
                                    <a  href='javascript:void(0);' ><?php echo $this->translate(ucwords(str_replace('_', ' ', $tab))); ?> </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php
                        for ($i = count($this->layouts_views) - 1; $i >= 0; $i--):
                            ?>
                            <li class="seaocore_tab_select_wrapper fright" rel='<?php echo $this->layouts_views[$i] ?>'>
                                <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate(ucwords(str_replace('_', ' ', $this->layouts_views[$i]))) ?></div>
                                <span id="<?php echo $this->layouts_views[$i] . "_" . $this->identity ?>"class="seaocore_tab_icon tab_icon_<?php echo $this->layouts_views[$i] ?>" onclick="siteeventTabSwitchview($(this));" ></span>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <div id="dynamic_app_info_siteevent_<?php echo $this->identity; ?>">
            <?php endif; ?>
            <?php if (in_array('list_view', $this->layouts_views)): ?> 
                <div class="siteevent_container" id="list_view_siteevent_" style="<?php echo $this->defaultLayout !== 'list_view' ? 'display: none;' : '' ?>">
                    <ul class="siteevent_browse_list siteevent_list_view">
                        <?php if ($this->totalCount): ?>
                            <?php foreach ($this->paginator as $siteevent): ?>

                                <?php if ($this->listViewType == 'list'): ?>    
                                    <li class="b_medium">
                                        <div class='siteevent_browse_list_photo b_medium'>
                                            <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $siteevent->featured): ?>
                                                <i class="siteevent_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
                                            <?php endif; ?>
                                            <?php if (!empty($this->statistics) && in_array('newLabel', $this->statistics) && $siteevent->newlabel): ?>
                                                <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                            <?php endif; ?>

                                            <?php echo $this->htmlLink($siteevent->getHref(array('showEventType' => $this->showEventType)), $this->itemPhoto($siteevent, 'thumb.main', '', array('align' => 'center'))); ?>

                                            <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($siteevent->sponsored)): ?>
                                                <div class="siteevent_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505'); ?>">
                                                    <?php echo $this->translate('SPONSORED'); ?>             
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class='siteevent_browse_list_info'>
                                            <div class='siteevent_browse_list_info_header'>
                                                <div class="siteevent_list_title_small o_hidden">
                                                    <?php echo $this->htmlLink($siteevent->getHref(array('showEventType' => $this->showEventType)), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncationList), array('title' => $siteevent->getTitle())); ?>
                                                </div>
                                            </div>

                                            <div class='siteevent_browse_list_info'>
                                                <?php if (!empty($this->statistics)) : ?>
                                                    <?php echo $this->eventInfo($siteevent, $this->statistics, array('ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation, 'showEventType' => $this->showEventType)); ?>
                                                <?php endif; ?>
                                            </div>

                                            <div class="clr dblock fright siteevent_browse_list_info_btn">
                                                <a href="<?php echo $siteevent->getHref(array('showEventType' => $this->showEventType)); ?>" class="siteevent_buttonlink"><?php echo $this->translate('Details &raquo;'); ?></a>
                                            </div> 
                                        </div>
                                    </li>
                                <?php else: ?>
                                    <?php if (!empty($siteevent->sponsored)): ?>
                                        <li class="list_sponsered b_medium">
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

                                            <?php echo $this->htmlLink($siteevent->getHref(array('showEventType' => $this->showEventType)), $this->itemPhoto($siteevent, 'thumb.main', '', array('align' => 'center'))) ?>
                                            <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($siteevent->sponsored)): ?>
                                                <div class="siteevent_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505'); ?>">
                                                    <?php echo $this->translate('SPONSORED'); ?>                 
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (!empty($this->statistics) && in_array('price', $this->statistics)): ?>
                                            <?php $priceInfos = $siteevent->getPriceInfo(); ?>
                                            <?php $priceInfoCount = Count($priceInfos); ?>
                                            <div class='siteevent_browse_list_info'>
                                                <?php $wheretobuyEnabled = 0; //Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.wheretobuy', 0);  ?>
                                                <?php if ($wheretobuyEnabled): ?>
                                                    <div class="siteevent_browse_list_price_info">
                                                        <?php if ($priceInfoCount > 0): ?>
                                                            <?php $minPrice = $siteevent->getWheretoBuyMinPrice();
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

                                                        <?php elseif ($siteevent->price > 0): ?>
                                                            <div class="siteevent_price">
                                                                <?php echo $this->locale()->toCurrency($siteevent->price, $currency); ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="siteevent_browse_list_price_info_stats">
                                                                <?php echo $this->translate("No price available.") ?>
                                                            </div>  
                                                        <?php endif; ?>
                                                    </div>  
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if (!empty($this->statistics) && (in_array('ratingStar', $this->statistics) && (!empty($siteevent->rating_editor) || !empty($siteevent->rating_users) || !empty($siteevent->$ratingValue) ))) : ?>
                                                <div class="siteevent_browse_list_rating">
                                                    <div class="siteevent_browse_list_show_rating fright">  
                                                        <?php if (!empty($siteevent->rating_editor) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_editor')): ?>
                                                            <div class="clr">	
                                                                <div class="siteevent_browse_list_rating_stats">
                                                                    <?php echo $this->translate("Editor Rating"); ?>
                                                                </div>
                                                                <div class="siteevent_ur_show_rating_star fnone o_hidden">
                                                                    <span class="siteevent_browse_list_rating_stars">
                                                                        <?php echo $this->ShowRatingStarSiteevent($siteevent->rating_editor, 'editor', 'big-star'); ?>
                                                                    </span>
                                                                </div>
                                                            </div> 
                                                        <?php endif; ?>
                                                        <?php if (!empty($siteevent->rating_users) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_users')): ?>
                                                            <div class="clr">
                                                                <div class="siteevent_browse_list_rating_stats">
                                                                    <?php echo $this->translate("User Ratings"); ?><br />
                                                                    <?php $totalUserReviews = ($siteevent->rating_editor) ? ($siteevent->review_count - 1) : $siteevent->review_count ?>
                                                                    <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $totalUserReviews), $this->locale()->toNumber($totalUserReviews)) ?>
                                                                </div>
                                                                <div class="siteevent_ur_show_rating_star fnone o_hidden">
                                                                    <span class="siteevent_browse_list_rating_stars">
                                                                        <?php echo $this->ShowRatingStarSiteevent($siteevent->rating_users, 'user', 'big-star'); ?>
                                                                    </span>
                                                                </div>
                                                            </div>  
                                                        <?php endif; ?>

                                                        <?php if (!empty($siteevent->rating_avg) && ($ratingValue == 'rating_avg')): ?>
                                                            <div class="clr">
                                                                <div class="siteevent_browse_list_rating_stats">
                                                                    <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $siteevent->review_count), $this->locale()->toNumber($siteevent->review_count)) ?>
                                                                </div>
                                                                <div class="siteevent_ur_show_rating_star fnone o_hidden">
                                                                    <span class="siteevent_browse_list_rating_stars">
                            <?php echo $this->ShowRatingStarSiteevent($siteevent->rating_avg, $ratingType, 'big-star'); ?>
                                                                    </span>
                                                                </div>
                                                            </div>  
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <div class="siteevent_browse_list_info">
                                                <div class="siteevent_browse_list_info_header">
                                                    <div class="siteevent_list_title_small">
                                                        <?php echo $this->htmlLink($siteevent->getHref(array('showEventType' => $this->showEventType)), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncationList), array('title' => $siteevent->getTitle())); ?>
                                                    </div>
                                                </div>  

                                                <div class='siteevent_browse_list_info_blurb'>
 
                                                    <?php echo $this->viewMore(strip_tags($siteevent->body), 125, 5000); ?>
                                                </div>

                                                <?php if (!empty($this->statistics)) : ?>
                                                    <?php echo $this->eventInfo($siteevent, $this->statistics, array('ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation, 'showEventType' => $this->showEventType)); ?>
                    <?php endif; ?>
                                            </div>

                                            <div class='siteevent_browse_list_info_stat seaocore_txt_light'>
                    <?php //echo $this->timestamp(strtotime($siteevent->creation_date))   ?>
                                            </div>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="tip">
                                <span>

                                    <?php echo $this->translate('No events were found that match this criteria.'); ?>

                                </span>
                            </div>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if (in_array('grid_view', $this->layouts_views)): ?> 

                <div class="image_view siteevent_container" id="grid_view_siteevent_" style="<?php echo $this->defaultLayout !== 'grid_view' ? 'display: none;' : '' ?>">
                    <div class="siteevent_img_view">
                        <?php if ($this->totalCount): ?>
                            <?php $isLarge = ($this->columnWidth > 170); ?>
            <?php foreach ($this->paginator as $siteevent): ?>          
                                <div class="siteevent_grid_view" style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;">
                                    <div class="siteevent_grid_thumb">
                                        <?php if (!empty($this->statistics) && in_array('newLabel', $this->statistics) && $siteevent->newlabel): ?>
                                            <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                        <?php endif; ?>
                                        <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $siteevent->featured): ?>
                                            <i class="siteevent_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
                                        <?php endif; ?>
                                        <a href="<?php echo $siteevent->getHref(array('showEventType' => $this->showEventType)) ?>" class ="siteevent_thumb">
                                            <?php
                                            $url = $siteevent->getPhotoUrl($isLarge ? 'thumb.profile' : 'thumb.profile');

                                            if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/nophoto_event_thumb_normal.png';
                                            endif;
                                            ?>
                                            <span style="background-image: url(<?php echo $url; ?>);"></span>
                                        </a>

                                        <?php if (!empty($this->titlePosition)) : ?>
                                            <div class="siteevent_grid_title">
                                            <?php echo $this->htmlLink($siteevent->getHref(array('showEventType' => $this->showEventType)), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncationGrid), array('title' => $siteevent->getTitle())) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($siteevent->sponsored)): ?>
                                        <div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsored.color', '#fc0505'); ?>;'>
                                        <?php echo $this->translate('SPONSORED'); ?>     				
                                        </div>
                                        <?php endif; ?>
                                        <div class="siteevent_grid_info">
                                                <?php if (empty($this->titlePosition)) : ?>
                                                <div class="siteevent_grid_title">
                                                <?php echo $this->htmlLink($siteevent->getHref(array('showEventType' => $this->showEventType)), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncationGrid), array('title' => $siteevent->getTitle())) ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($this->statistics)) : ?>
                                                <?php echo $this->eventInfo($siteevent, $this->statistics, array('view_type' => 'grid_view', 'titlePosition' => $this->titlePosition, 'ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation, 'showEventType' => $this->showEventType)); ?>
                                            <?php endif; ?>
                                            
                                        </div>
                                    
                            <?php 
                            if ($this->params['shareOptions']) {
                               
                                 $this->subject = $siteevent;
                                include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_shareEventButtons.tpl';
                            }
                            ?>
                                    </div>
                                <?php endforeach; ?>
                        <?php else: ?>
                            <div class="tip">
                                <span>
                                    <?php echo $this->translate("No events were found that match this criteria."); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($this->enableLocation): ?>
                <div class="siteevent_container siteevent_map_view o_hiddden" id="map_view_siteevent_" style="<?php echo $this->defaultLayout !== 'map_view' ? 'display: none;' : '' ?>">
                    <div class="seaocore_map clr" style="overflow:hidden;">
                        <div id="rmap_canvas_<?php echo $this->identity ?>" class="siteevent_list_map"> </div>
                        <?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
                        <?php if (!empty($siteTitle)) : ?>
                            <div class="seaocore_map_info"><?php echo $this->translate("Locations on %s", "<a href='' target='_blank'>$siteTitle</a>"); ?></div>
                        <?php endif; ?>
                    </div>	
                    <a  href="javascript:void(0);" onclick="srToggleBounce(<?php echo $this->identity ?>)" class="fleft siteevent_list_map_bounce_link" style="<?php echo $this->flagSponsored ? '' : 'display:none' ?>"> <?php echo $this->translate('Stop Bounce'); ?></a>
                </div>
            <?php endif; ?>
                
            <?php if ($this->showViewMore): ?>    
                <div class="seaocore_view_more mtop10">
                    <?php
                    echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
                        'id' => '',
                        'class' => 'buttonlink icon_viewmore'
                    ))
                    ?>
                </div>
            <?php endif; ?>    
            <div class="seaocore_loading" id="" style="display: none;">
                <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
            <?php echo $this->translate("Loading ...") ?>
            </div>
            <?php if (empty($this->is_ajax)): ?>
            </div>
        </div>
        <script type="text/javascript">
            function sendAjaxRequestSiteevent(params) {
                var url = en4.core.baseUrl + 'widget';

                if (params.requestUrl)
                    url = params.requestUrl;

                var request = new Request.HTML({
                    url: url,
                    data: $merge(params.requestParams, {
                        format: 'html',
                        subject: en4.core.subject.guid,
                        is_ajax: true,
                        loaded_by_ajax:false,
                    }),
                    evalScripts: true,
                    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                        if (params.requestParams.page == 1) {
                            params.responseContainer.empty();
                            Elements.from(responseHTML).inject(params.responseContainer);
    <?php if ($this->enableLocation): ?>
                                srInitializeMap(params.requestParams.content_id);
    <?php endif; ?>
                        } else {
                            var element = new Element('div', {
                                'html': responseHTML
                            });
                            params.responseContainer.getElements('.seaocore_loading').setStyle('display', 'none');

                            if ($$('.siteevent_list_view') && element.getElement('.siteevent_list_view')) {
                                Elements.from(element.getElement('.siteevent_list_view').innerHTML).inject(params.responseContainer.getElement('.siteevent_list_view'));
                            }

                            if ($$('.siteevent_img_view') && element.getElement('.siteevent_img_view')) {
                                Elements.from(element.getElement('.siteevent_img_view').innerHTML).inject(params.responseContainer.getElement('.siteevent_img_view'));
                            }
                        }
                        en4.core.runonce.trigger();
                        Smoothbox.bind(params.responseContainer);
                    }
                });
                en4.core.request.send(request);
            }

            en4.core.runonce.add(function() {
                <?php if (count($this->tabs) > 1): ?>
                    $$('.tab_li_<?php echo $this->identity ?>').addEvent('click', function(event) {
                        if (en4.core.request.isRequestActive())
                            return;
                        var element = $(event.target);
                        if (element.tagName.toLowerCase() == 'a') {
                            element = element.getParent('li');
                        }
                        var type = element.get('rel');

                        element.getParent('ul').getElements('li').removeClass("active")
                        element.addClass("active");
                        var params = {
                            requestParams:<?php echo json_encode($this->params) ?>,
                            responseContainer: $('dynamic_app_info_siteevent_' + '<?php echo $this->identity ?>')
                        }
                        params.requestParams.content_type = type;
                        params.requestParams.page = 1;
                        params.requestParams.content_id = '<?php echo $this->identity ?>';
                        params.responseContainer.empty();
                        new Element('div', {
                            'class': 'seaocore_content_loader'
                        }).inject(params.responseContainer);
                        sendAjaxRequestSiteevent(params);
                    });
                <?php endif; ?>
            });

            <?php $latitude = $this->settings->getSetting('siteevent.map.latitude', 0); ?>
            <?php $longitude = $this->settings->getSetting('siteevent.map.longitude', 0); ?>
            <?php $defaultZoom = $this->settings->getSetting('siteevent.map.zoom', 1); ?>

            function siteeventTabSwitchview(element) {
                if (element.tagName.toLowerCase() == 'span') {
                    element = element.getParent('li');
                }
                var type = element.get('rel');

                var identity = element.getParent('ul').get('identity');
                $('dynamic_app_info_siteevent_' + identity).getElements('.siteevent_container').setStyle('display', 'none');
                $('dynamic_app_info_siteevent_' + identity).getElement("#" + type + "_siteevent_").style.display = 'block';
            }
        </script>

        <?php if ($this->enableLocation): ?>
            <?php $latitude = $this->settings->getSetting('siteevent.map.latitude', 0); ?>
            <?php $longitude = $this->settings->getSetting('siteevent.map.longitude', 0); ?>
            <?php $defaultZoom = $this->settings->getSetting('siteevent.map.zoom', 1); ?>
            <script type="text/javascript">
                // var rgmarkers = [];

                function srInitializeMap(element_id) {
                    en4.siteevent.maps[element_id] = [];
                    en4.siteevent.maps[element_id]['markers'] = [];
                    // create the map
                    var myOptions = {
                        zoom: <?php echo $defaultZoom ?>,
                        center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
                        navigationControl: true,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    }

                    en4.siteevent.maps[element_id]['map'] = new google.maps.Map(document.getElementById("rmap_canvas_" + element_id), myOptions);

                    google.maps.event.addListener(en4.siteevent.maps[element_id]['map'], 'click', function() {
                        en4.siteevent.maps[element_id]['infowindow'].close();
                        google.maps.event.trigger(en4.siteevent.maps[element_id]['map'], 'resize');
                        en4.siteevent.maps[element_id]['map'].setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>));
                    });
                    if ($("rmap_canvas_" + element_id)) {
                        if($("map_view_" + element_id)) {
                            $("map_view_" + element_id).addEvent('click', function() {
                                google.maps.event.trigger(en4.siteevent.maps[element_id]['map'], 'resize');
                                en4.siteevent.maps[element_id]['map'].setZoom(<?php echo $defaultZoom ?>);
                                en4.siteevent.maps[element_id]['map'].setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>));
                            });
                        }
                        $$("li.tab_"+element_id).addEvent('click', function() {
                            google.maps.event.trigger(en4.siteevent.maps[element_id]['map'], 'resize');
                            en4.siteevent.maps[element_id]['map'].setZoom(<?php echo $defaultZoom ?>);
                            en4.siteevent.maps[element_id]['map'].setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>));
                        });
                    }

                    en4.siteevent.maps[element_id]['infowindow'] = new google.maps.InfoWindow(
                            {
                                size: new google.maps.Size(250, 50)
                            });

                }

                function setSRMarker(element_id, latlng, bounce, html, title_list) {
                    var contentString = html;
                    if (bounce == 0) {
                        var marker = new google.maps.Marker({
                            position: latlng,
                            map: en4.siteevent.maps[element_id]['map'],
                            title: title_list,
                            animation: google.maps.Animation.DROP,
                            zIndex: Math.round(latlng.lat() * -100000) << 5
                        });
                    }
                    else {
                        var marker = new google.maps.Marker({
                            position: latlng,
                            map: en4.siteevent.maps[element_id]['map'],
                            title: title_list,
                            draggable: false,
                            animation: google.maps.Animation.BOUNCE
                        });
                    }
                    en4.siteevent.maps[element_id]['markers'].push(marker);

                    google.maps.event.addListener(marker, 'click', function() {
                        en4.siteevent.maps[element_id]['infowindow'].setContent(contentString);
                        google.maps.event.trigger(en4.siteevent.maps[element_id]['map'], 'resize');

                        en4.siteevent.maps[element_id]['infowindow'].open(en4.siteevent.maps[element_id]['map'], marker);
                    });
                }
                function srToggleBounce(element_id) {
                    var markers = en4.siteevent.maps[element_id]['markers'];
                    for (var i = 0; i < markers.length; i++) {
                        if (markers[i].getAnimation() != null) {
                            markers[i].setAnimation(null);
                        }
                    }
                }
                en4.core.runonce.add(function() {
                    srInitializeMap("<?php echo $this->identity ?>");
                });
            </script>
        <?php endif; ?>
    <?php endif; ?>

    <script type="text/javascript">
        en4.core.runonce.add(function() {
            var view_more_content = $('dynamic_app_info_siteevent_<?php echo $this->identity ?>').getElements('.seaocore_view_more');
            view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
            view_more_content.removeEvents('click');
            view_more_content.addEvent('click', function() {
                if (en4.core.request.isRequestActive())
                    return;
                var params = {
                    requestParams:<?php echo json_encode($this->params) ?>,
                    responseContainer: $('dynamic_app_info_siteevent_' +<?php echo sprintf('%d', $this->identity) ?>)
                }
                params.requestParams.content_type = "<?php echo $this->content_type ?>";
                params.requestParams.page =<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>;
                params.requestParams.content_id = '<?php echo $this->identity ?>';
                view_more_content.setStyle('display', 'none');
                params.responseContainer.getElements('.seaocore_loading').setStyle('display', '');

                sendAjaxRequestSiteevent(params);
            });

            <?php if ($this->enableLocation): ?>
                <?php foreach ($this->locations as $location) : ?>
                    var point = new google.maps.LatLng(<?php echo $location->latitude ?>,<?php echo $location->longitude ?>);
                    var contentString = "<?php
                    echo $this->string()->escapeJavascript($this->partial('application/modules/Siteevent/views/scripts/_mapInfoWindowContent.tpl', array(
                                'siteevent' => $this->locationsEvent[$location->event_id],
                                'ratingValue' => $ratingValue,
                                'ratingType' => $ratingType,
                                'statistics' => $this->statistics,
                                'content_type' => $this->content_type,
                                'postedbytext' => 'Event',
                                'statistics' => $this->statistics,
                                'showEventType' => $this->showEventType,
                                'ratingShow' => $ratingShow)), false);
                    ?>";

                    setSRMarker(<?php echo $this->identity ?>, point,<?php echo!empty($this->flagSponsored) ? $this->locationsEvent[$location->event_id]->sponsored : 0 ?>, contentString, "<?php echo $this->string()->escapeJavascript($this->locationsEvent[$location->event_id]->getTitle()) ?>");
                <?php endforeach; ?>
            <?php endif; ?>
        });
    </script>

<?php else: ?>

    <div id="layout_siteevent_recently_popular_random_events_<?php echo $this->identity; ?>">
         <div class="layout_core_container_tabs">
            <?php if ($this->tabCount > 1 || count($this->layouts_views) > 1): ?>
                <div class="tabs_alt tabs_parent tabs_parent_siteevent_home">
                    <ul id="main_tabs" identity='<?php echo $this->identity ?>'>
                        <?php if ($this->tabCount > 1): ?>
                            <?php foreach ($this->tabs as $key => $tab): ?>
                                <li class="tab_li_<?php echo $this->identity ?> <?php echo $key == 0 ? 'active' : ''; ?>" rel="<?php echo $tab; ?>">
                                    <a  href='javascript:void(0);' ><?php echo $this->translate(ucwords(str_replace('_', ' ', $tab))); ?> </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php
                        for ($i = count($this->layouts_views) - 1; $i >= 0; $i--):
                            ?>
                            <li class="seaocore_tab_select_wrapper fright" rel='<?php echo $this->layouts_views[$i] ?>'>
                                <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate(ucwords(str_replace('_', ' ', $this->layouts_views[$i]))) ?></div>
                                <span id="<?php echo $this->layouts_views[$i] . "_" . $this->identity ?>"class="seaocore_tab_icon tab_icon_<?php echo $this->layouts_views[$i] ?>"  ></span>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
                <?php endif;?>
            <div class="seaocore_content_loader"></div>
              </div>
    </div>
    <?php if(!$this->detactLocation): ?>
   <script type="text/javascript">
     window.addEvent('domready',function(){
         en4.siteevent.ajaxTab.sendReq({
            loading:false,
            requestParams:$merge(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'}),
            responseContainer: [$('layout_siteevent_recently_popular_random_events_<?php echo $this->identity; ?>')]
        });
        });
    </script>
    <?php else: ?>
    <script type="text/javascript">
        var requestParams = $merge(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'})
        var params = {
            'detactLocation': <?php echo $this->detactLocation; ?>,
            'responseContainer': 'layout_siteevent_recently_popular_random_events_<?php echo $this->identity; ?>',
            requestParams: requestParams
        };

        en4.seaocore.locationBased.startReq(params);
    </script>
    <?php endif; ?>
<?php endif; ?>

<script type="text/javascript">
    en4.core.runonce.add(function () {
       showShareLinks();
    });  
</script>