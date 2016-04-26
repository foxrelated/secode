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
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js');
?>
<?php if (in_array('mapview', $this->typesOfViews)): ?>
    <?php
//GET API KEY
    $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
    ?>
<?php endif; ?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<?php if ($this->loaded_by_ajax):
    ?>
    <script type="text/javascript">
        var params = {
            requestParams:<?php echo json_encode($this->allParams) ?>,
            responseContainer: $$('.layout_siteevent_profile_siteevent')
        }
        
        en4.siteevent.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
    </script>
<?php endif; ?>
<?php if ($this->showContent):?>
<script type="text/javascript">
  
    var rsvp = '<?php echo $this->rsvp; ?>';
    var viewType = '<?php echo $this->viewType; ?>';
    var showEventType = '<?php echo $this->showEventType; ?>';
    function filter_rsvp() {
        if ($$('.seaocore_profile_list_more<?php echo $this->identity ?>'))
            $$('.seaocore_profile_list_more<?php echo $this->identity ?>').setStyle('display', 'none');
        if ($("siteevent_map_canvas_<?php echo $this->identity ?>"))
            $("siteevent_map_canvas_<?php echo $this->identity ?>").getParent('.maps_se').setStyle('display', 'none')
        $('profile_siteevents<?php echo $this->identity ?>').innerHTML = '<div class="seaocore_content_loader"></div>';

        var requestParams = $merge({
            format: 'html',
            subject: en4.core.subject.guid,
            isajax: true,
            pagination: 0,
            rsvp: rsvp,
            page: 1,
            is_filtering: true,
            viewType: viewType,
            showEventType: showEventType,
            identity: <?php echo $this->identity; ?>
        }, <?php echo json_encode($this->allParams); ?>);
        //var url = en4.core.baseUrl + 'widget/index/mod/siteevent/name/profile-siteevent';

        var request = new Request.HTML({
            url: en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
            data: requestParams,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                $('profile_siteevents<?php echo $this->identity ?>').getParent().innerHTML = responseHTML;
                en4.core.runonce.trigger();
            }
        });
        request.send();
    }

    en4.core.runonce.add(function() {
        var anchor = $('profile_siteevents<?php echo $this->identity ?>').getParent();
<?php if ($this->paginator->getTotalItemCount() > 0) : ?>
            $('profile_lists_next_<?php echo $this->identity ?>').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';
            $('seaocore_loading_<?php echo $this->identity ?>').style.display = 'none';
<?php endif; ?>
<?php if ($this->paginator->getCurrentPageNumber() < 2): ?>
            $$('.host_profile_events_links_filter<?php echo $this->identity ?>').removeEvents('click').addEvent('click', function(event) {
                var el = $(event.target);
                if (!el.hasClass('host_profile_events_links_filter<?php echo $this->identity ?>') && !el.get('data-page'))
                    el = el.getParent('.host_profile_events_links_filter<?php echo $this->identity ?>');
                var container;
                if (el.get('data-page') == 1) {
                    if ($$('.seaocore_profile_list_more<?php echo $this->identity ?>'))
                        $$('.seaocore_profile_list_more<?php echo $this->identity ?>').setStyle('display', 'none');
                    if ($("siteevent_map_canvas_<?php echo $this->identity ?>")) {
                        $("siteevent_map_canvas_<?php echo $this->identity ?>").getParent('.maps_se').setStyle('display', 'none');
                    }
                    $('profile_siteevents<?php echo $this->identity ?>').innerHTML = '<div class="seaocore_content_loader"></div>';
                    container = anchor;
                } else {
                    container = $('profile_siteevents<?php echo $this->identity ?>');
                    $('seaocore_loading_<?php echo $this->identity ?>').style.display = 'block';
                    $('profile_lists_next_<?php echo $this->identity ?>').style.display = 'none';
                }

                en4.core.request.send(new Request.HTML({
                    url: en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                    data: $merge(<?php echo json_encode($this->allParams); ?>, {
                        format: 'html',
                        subject: en4.core.subject.guid,
                        page: el.get('data-page'),
                        viewType: el.get('data-fillter'),
                        contentViewType: el.get('data-viewType'),
                        isajax: true,
                        rsvp: rsvp,
                        pagination: 1,
                        is_filtering: true,
                        showEventType: showEventType,
                        identity: <?php echo $this->identity; ?>
                    }),
                    evalScripts: true,
                    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {

                        if (el.get('data-page') == 1) {
                            container.empty();
                        } else {
                            el.set('data-page', (parseInt(el.get('data-page')) + 1))
                        }
                        Elements.from(responseHTML).inject(container);
                        en4.core.runonce.trigger();
                        Smoothbox.bind(container);
                    }
                }));
            });
        <?php endif; ?>
    });
</script>

<div class="siteevent_browse_lists_view_options b_medium">
    <?php if ($this->paginator->getCurrentPageNumber() < 2) : ?>
        <?php if ($this->showEventType == 'all') : ?>
            <div class="fleft">
                <?php if($this->showEventUpcomingPastCount):?>
                    <a href="javascript:void(0);" class="<?php if ($this->viewType == 'upcoming'): ?>bold un<?php endif; ?>host_profile_events_links_filter<?php echo $this->identity ?>" data-page = '1' data-fillter="upcoming"   data-viewType ="<?php echo $this->contentViewType ?>"   ><?php echo $this->translate('Upcoming'); ?>&nbsp;(<?php echo $this->locale()->toNumber($this->totalUpcomingEventCount); ?>)</a>
                    | <a href="javascript:void(0);"  class="<?php if ($this->viewType == 'past'): ?>bold un<?php endif; ?>host_profile_events_links_filter<?php echo $this->identity ?>" data-page = '1' data-fillter="past" data-viewType ="<?php echo $this->contentViewType ?>"><?php echo $this->translate('Past'); ?>&nbsp;(<?php echo $this->locale()->toNumber($this->totalPastEventCount); ?>)</a>
                <?php else: ?>
                    <a href="javascript:void(0);" class="<?php if ($this->viewType == 'upcoming'): ?>bold un<?php endif; ?>host_profile_events_links_filter<?php echo $this->identity ?>" data-page = '1' data-fillter="upcoming"   data-viewType ="<?php echo $this->contentViewType ?>"   ><?php echo $this->translate('Upcoming'); ?></a>
                    | <a href="javascript:void(0);"  class="<?php if ($this->viewType == 'past'): ?>bold un<?php endif; ?>host_profile_events_links_filter<?php echo $this->identity ?>" data-page = '1' data-fillter="past" data-viewType ="<?php echo $this->contentViewType ?>"><?php echo $this->translate('Past'); ?></a>
                <?php endif;?>
            </div>
        <?php endif; ?>

        <?php if (count($this->typesOfViews) > 1): ?>  
            <?php if (in_array('mapview', $this->typesOfViews)): ?>
                <span  class="seaocore_tab_select_wrapper fright <?php if ($this->contentViewType == 'mapview'): ?>bold un<?php endif; ?>host_profile_events_links_filter<?php echo $this->identity ?>" data-page = '1' data-viewType="mapview" data-fillter ="<?php echo $this->viewType ?>" ><div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Map View"); ?></div>
                    <span class="seaocore_tab_icon tab_icon_map_view" ></span></span>
            <?php endif; ?>
            <?php if (in_array('gridview', $this->typesOfViews)): ?>
                <span  class="seaocore_tab_select_wrapper fright <?php if ($this->contentViewType == 'gridview'): ?>bold un<?php endif; ?>host_profile_events_links_filter<?php echo $this->identity ?>" data-page = '1' data-viewType="gridview" data-fillter ="<?php echo $this->viewType ?>" ><div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
                    <span class="seaocore_tab_icon tab_icon_grid_view" ></span></span>
            <?php endif; ?>
            <?php if (in_array('listview', $this->typesOfViews)): ?>
                <span  class="seaocore_tab_select_wrapper fright <?php if ($this->contentViewType == 'listview'): ?>bold un<?php endif; ?>host_profile_events_links_filter<?php echo $this->identity ?>" data-page = '1' data-viewType="listview"   data-fillter ="<?php echo $this->viewType ?>"   ><div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
                    <span class="seaocore_tab_icon tab_icon_list_view" ></span></span>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($this->paginator->getCurrentPageNumber() < 2 && $this->showEventFilter && !empty($this->EventFilterTypes) && count($this->EventFilterTypes) > 1) : ?>
        <div class="o_hidden txt_center">
            <a href="javascript:void(0);" onclick="rsvp = -1;
            filter_rsvp(-1);" <?php if ($this->rsvp == -1) echo 'class="bold"'; ?>><?php echo $this->translate('All'); ?></a>
            <?php if (!empty($this->EventFilterTypes) && in_array('ledOwner', $this->EventFilterTypes)): ?>
             |
             <a href="javascript:void(0);" onclick="rsvp = -4;
             filter_rsvp(-4);" <?php if ($this->rsvp == -4) echo 'class="bold"'; ?>><?php echo $this->translate('Leading'); ?></a>
             <?php endif; ?>    
            <?php if (!empty($this->EventFilterTypes) && in_array('host', $this->EventFilterTypes)): ?>
             |
             <a href="javascript:void(0);" onclick="rsvp = -2;
             filter_rsvp(-2);" <?php if ($this->rsvp == -2) echo 'class="bold"'; ?>><?php echo $this->translate('Hosting'); ?></a>
             <?php endif; ?>    
            <?php if (!Engine_Api::_()->siteevent()->isTicketBasedEvent() && !empty($this->EventFilterTypes) && in_array('joined', $this->EventFilterTypes)): ?>   
                | <a href="javascript:void(0);" onclick="rsvp = 2;
                filter_rsvp(2);" <?php if ($this->rsvp == 2) echo 'class="bold"'; ?>><?php echo $this->translate('Attending'); ?></a>
                |
                <a href="javascript:void(0);" onclick="rsvp = 1;
                filter_rsvp(1);" <?php if ($this->rsvp == 1) echo 'class="bold"'; ?>><?php echo $this->translate('Maybe Attending'); ?></a>
                |
                <a href="javascript:void(0);" onclick="rsvp = 0;
                filter_rsvp(0);" <?php if ($this->rsvp == 0) echo 'class="bold"'; ?>><?php echo $this->translate('Not Attending'); ?></a>
               <?php endif; ?>
               <?php if (!empty($this->EventFilterTypes) && in_array('liked', $this->EventFilterTypes)): ?> 
                |
                <a href="javascript:void(0);" onclick="rsvp = -3;
                filter_rsvp(-3);" <?php if ($this->rsvp == -3) echo 'class="bold"'; ?>><?php echo $this->translate('Liked'); ?></a>
                <?php endif; ?>
               <?php if (!empty($this->EventFilterTypes) && in_array('userreviews', $this->EventFilterTypes)): ?> 
                |
                <a href="javascript:void(0);" onclick="rsvp = -5;
                filter_rsvp(-5);" <?php if ($this->rsvp == -5) echo 'class="bold"'; ?>><?php echo $this->translate('Rated'); ?></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

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

<!--//CASE IF VIEW TYPE IF MAP VIEW-->
<?php if ($this->contentViewType == 'mapview'): ?>
    <?php $flagSponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.map.sponsored', 1); ?>
    <?php if ($this->paginator->getCurrentPageNumber() < 2): ?>
        <?php $latitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.map.latitude', 0); ?>
        <?php $longitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.map.longitude', 0); ?>
        <?php $defaultZoom = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.map.zoom', 1); ?>
        <div id="profile_siteevents<?php echo $this->identity ?>" >
            <?php if ($this->paginator->getTotalItemCount() > 0) : ?>    
                <div id="siteevent_map_canvas_view_browse" class="maps_se">
                    <div class="seaocore_map clr" style="overflow:hidden;">
                        <div id="siteevent_map_canvas_<?php echo $this->identity ?>" class="siteevent_list_map"> </div>
                        <div class="clear mtop10"></div>
                        <?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
                        <?php if (!empty($siteTitle)) : ?>
                            <div class="seaocore_map_info"><?php echo $this->translate("Locations on %s", "<a href='' target='_blank'>$siteTitle</a>"); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if ($flagSponsored): ?>
                        <a  href="javascript:void(0);"  class="fleft siteevent_list_map_bounce_link map_bounce_store_<?php echo $this->identity ?>" style="display: none;"> <?php echo $this->translate('Stop Bounce'); ?></a>
                    <?php endif; ?>
                </div>

                <div class="seaocore_profile_list_more<?php echo $this->identity ?>">
                    <div id="profile_lists_next_<?php echo $this->identity ?>" class="seaocore_view_more mtop10 host_profile_events_links_filter<?php echo $this->identity ?>" data-page="<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>" data-fillter="<?php echo $this->viewType ?>" data-viewType="<?php echo $this->contentViewType ?>">
                        <?php
                        echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
                            'onclick' => '',
                            'class' => 'buttonlink_right icon_viewmore'
                        ));
                        ?>
                    </div>
                    <div class="seaocore_loading" id="seaocore_loading_<?php echo $this->identity ?>" style="display: none;">
                        <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
                        <?php echo $this->translate("Loading ...") ?>
                    </div>
                </div>
                <script type="text/javascript">
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
                en4.core.runonce.add(function() {
                    // create the map
                    var element_id =<?php echo $this->identity ?>;
                    en4.siteevent.maps[element_id] = [];
                    en4.siteevent.maps[element_id]['markers'] = [];
                    en4.siteevent.maps[element_id]['map'] = new google.maps.Map(document.getElementById("siteevent_map_canvas_<?php echo $this->identity ?>"), {
                        zoom: <?php echo $defaultZoom ?>,
                        center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
                        navigationControl: true,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    });

                    google.maps.event.addListener(en4.siteevent.maps[element_id]['map'], 'click', function() {
                        en4.siteevent.maps[element_id]['infowindow'].close();
                        google.maps.event.trigger(en4.siteevent.maps[element_id]['map'], 'resize');
                        en4.siteevent.maps[element_id]['map'].setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>));
                    });

                    $$("li.tab_" + element_id).addEvent('click', function() {
                        if (!document.getElementById("siteevent_map_canvas_<?php echo $this->identity ?>"))
                            return;
                        google.maps.event.trigger(en4.siteevent.maps[element_id]['map'], 'resize');
                        en4.siteevent.maps[element_id]['map'].setZoom(<?php echo $defaultZoom ?>);
                        en4.siteevent.maps[element_id]['map'].setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>));
                    });
                    en4.siteevent.maps[element_id]['infowindow'] = new google.maps.InfoWindow(
                            {
                                size: new google.maps.Size(250, 50)
                            });

                        <?php if ($flagSponsored): ?>
                            $$('.map_bounce_store_<?php echo $this->identity ?>').removeEvents('click').addEvent('click', function(event) {
                                var el = $(event.target);
                                var markers = en4.siteevent.maps[<?php echo $this->identity ?>]['markers'];
                                for (var i = 0; i < markers.length; i++) {
                                    if (markers[i].getAnimation() != null) {
                                        markers[i].setAnimation(null);
                                    }
                                }
                                el.style.display = 'none';
                            });
                        <?php endif; ?>
                    });
                </script>
            <?php else: ?>
                <div class="tip"> 
                    <span>
                        <?php echo $this->translate('You do not have any event that match your search criteria.'); ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if ($this->paginator->getTotalItemCount() > 0) : ?>
        <script type="text/javascript">
            en4.core.runonce.add(function() {
        <?php foreach ($this->paginator as $siteevent) : ?>
            <?php if ($siteevent->canView($this->viewer())): ?>
                        var point = new google.maps.LatLng(<?php echo $siteevent->latitude ?>,<?php echo $siteevent->longitude ?>);
                        var contentString = "<?php
                echo $this->string()->escapeJavascript($this->partial('application/modules/Siteevent/views/scripts/_mapInfoWindowContent.tpl', array(
                            'siteevent' => $siteevent,
                            'subject' => $this->subject,
                            'temp' => 1,
                            'ratingValue' => $ratingValue,
                            'ratingType' => $ratingType,
                            'statistics' => $this->statistics,
                            'content_type' => $this->content_type,
                            'postedbytext' => 'Event',
                            'showEventType' => $this->showEventType,
                            'ratingShow' => $ratingShow)), false);
                ?>";

                        setSRMarker(<?php echo $this->identity ?>, point,<?php echo!empty($flagSponsored) ? $siteevent->sponsored : 0 ?>, contentString, "<?php echo $this->string()->escapeJavascript($siteevent->getTitle()) ?>");
                        if (<?php echo!empty($flagSponsored) ? $siteevent->sponsored : 0 ?>) {
                            $$('.map_bounce_store_<?php echo $this->identity ?>').setStyle('display', '');
                        }
                    <?php endif; ?>
                <?php endforeach; ?>
            });
        </script>
    <?php endif; ?>
<?php else: ?>
    <?php $isLarge = ($this->columnWidth > 170); ?>
    <?php if ($this->paginator->getCurrentPageNumber() < 2): ?>
        <ul id="profile_siteevents<?php echo $this->identity ?>" <?php if ($this->contentViewType == 'listview'): ?>class="siteevent_browse_list"<?php else: ?> class="siteevent_grid_view_sidebar o_hidden" <?php endif; ?>>
        <?php endif; ?>
        <?php if ($this->paginator->getTotalItemCount() > 0) : ?>
            <?php foreach ($this->paginator as $siteevent): ?>
                <?php if ($this->contentViewType == 'listview'): ?>
                    <li class="b_medium">
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

                        <div class='siteevent_browse_list_info'>
                            <div class='siteevent_browse_list_info_header'>
                                <div class="siteevent_list_title_small o_hidden">
                                    <?php echo $this->htmlLink($siteevent->getHref(array('showEventType' => $this->showEventType)), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncation), array('title' => $siteevent->getTitle())) ?>
                                </div>
                            </div>
                            <div class="siteevent_browse_list_information fleft">
                                <?php $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1); ?>

                                <?php //echo $this->timestamp(strtotime($siteevent->creation_date))  ?>
                                
																<?php $totalReviews = Engine_Api::_()->getDbtable('userreviews', 'siteevent')->totalReviews($siteevent->event_id, $this->subject->getIdentity()); ?>
																<?php if($totalReviews): ?>
																	<div class="siteevent_listings_stats ">
                                    <i title="<?php echo $this->translate('As Guest'); ?>" class="siteevent_icon_strip siteevent_icon siteevent_icon_user"></i>
                                    <div class="o_hidden">
																		<?php echo $this->translate("Score from "); ?><?php 	echo $this->htmlLink(array('route' => 'siteevent_user_review', 'controller' => 'userreview', 'action' => 'view', 'event_id' => $siteevent->event_id, 'user_id' => $this->subject->getIdentity()), $this->translate(array('%s review', '%s reviews', $totalReviews), $this->locale()->toNumber($totalReviews))); ?>:
                                    <?php $averageUserReviews = Engine_Api::_()->getDbtable('userreviews', 'siteevent')->averageUserRatings(array('user_id' => $this->subject->getIdentity(), 'event_id' => $siteevent->event_id));
																				echo $this->ShowRatingStarSiteevent($averageUserReviews, 'user', 'small-star',null, false, false); ?>
                                    </div>
																	</div>
																<?php endif; ?>

                                <?php if (!empty($this->statistics)) : ?>
                                    <?php echo $this->eventInfo($siteevent, $this->statistics, array('ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation, 'showEventType' => $this->showEventType)); ?>
                                <?php endif; ?>
                            </div>

                            <div class='siteevent_browse_list_des o_hidden'>
                                <?php echo substr(strip_tags($siteevent->body), 0, 350);
                                if (strlen($siteevent->body) > 349)
                                    echo $this->translate("...");
                                ?>
                            </div>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="siteevent_grid_view" style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;">
                        <div class="siteevent_grid_thumb">
                            <?php if (!empty($this->statistics) && in_array('newLabel', $this->statistics) && $siteevent->newlabel): ?>
                                <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                <?php endif; ?>
                            <a href="<?php echo $siteevent->getHref(array('showEventType' => $this->showEventType)) ?>" class ="siteevent_thumb">
                                <?php
                                $url = $siteevent->getPhotoUrl($isLarge ? 'thumb.profile' : 'thumb.profile');
                                if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/nophoto_event_thumb_normal.png';
                                endif;
                                ?>
                                <span style="background-image: url(<?php echo $url; ?>); <?php if ($isLarge): ?> height:160px; <?php endif; ?> "></span>
                            </a>
                            <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $siteevent->featured): ?>
                                <i class="siteevent_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
                            <?php endif; ?>
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
                                <div class="bold">
                                <?php echo $this->htmlLink($siteevent->getHref(array('showEventType' => $this->showEventType)), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncationGrid), array('title' => $siteevent->getTitle())); ?>
                                </div>   
                            <?php endif; ?>                          
                            <?php $totalReviews = Engine_Api::_()->getDbtable('userreviews', 'siteevent')->totalReviews($siteevent->event_id, $this->subject->getIdentity()); ?>
                            <?php if($totalReviews): ?>
                              <div class="siteevent_listings_userrating siteevent_listings_host b_medium siteevent_listings_stats <?php if (!empty($this->titlePosition)) : ?>siteevent_listings_host_h<?php endif; ?>">
                                <?php echo $this->itemPhoto($this->user($this->subject), 'thumb.icon') ?>
                                <div class="o_hidden">
                                  <?php echo $this->translate("Scored ") ?>
                                  <?php 	echo $this->htmlLink(array('route' => 'siteevent_user_review', 'controller' => 'userreview', 'action' => 'view', 'event_id' => $siteevent->event_id, 'user_id' => $this->subject->getIdentity()), $this->translate(array('%s review', '%s reviews', $totalReviews), $this->locale()->toNumber($totalReviews))); ?>
                                  <span class="clr dblock mtop5">
                                    <?php $averageUserReviews = Engine_Api::_()->getDbtable('userreviews', 'siteevent')->averageUserRatings(array('user_id' => $this->subject->getIdentity(), 'event_id' => $siteevent->event_id));
                                    echo $this->ShowRatingStarSiteevent($averageUserReviews, 'user', 'small-star',null, false, false); ?>
                                  </span>
                                </div>
                              </div>
                            <?php endif; ?>
                          

                            <?php if (!empty($this->statistics)) : ?>
                                <?php echo $this->eventInfo($siteevent, $this->statistics, array('view_type' => 'grid_view', 'titlePosition' => $this->titlePosition, 'ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation, 'showEventType' => $this->showEventType)); ?>
                            <?php endif; ?>
                            </div>
                            
                             <?php 
                            if ($this->allParams['shareOptions']) {
                                 $this->subject = $siteevent;
                                include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_shareEventButtons.tpl';
                                ;
                            }
                            ?>
                        
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="tip"> 
                <span>
                    <?php echo $this->translate('You do not have any event that match your search criteria.'); ?>
                </span>
            </div>  
        <?php endif; ?> 
                    
        <?php if ($this->paginator->getCurrentPageNumber() < 2): ?>         
            </ul>
            <?php if ($this->paginator->getTotalItemCount() > 0) : ?>
                <div class="seaocore_profile_list_more<?php echo $this->identity ?>">
                    <div id="profile_lists_next_<?php echo $this->identity ?>" class="seaocore_view_more mtop10 host_profile_events_links_filter<?php echo $this->identity ?>" data-page="<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>" data-fillter="<?php echo $this->viewType ?>" data-viewType="<?php echo $this->contentViewType ?>">
                        <?php
                        echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
                            'onclick' => '',
                            'class' => 'buttonlink_right icon_viewmore'
                        ));
                        ?>
                    </div>
                    <div class="seaocore_loading" id="seaocore_loading_<?php echo $this->identity ?>" style="display: none;">
                        <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
                <?php echo $this->translate("Loading ...") ?>
                    </div>
                </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
<?php endif; ?>

<script type="text/javascript">
en4.core.runonce.add(function () {
showShareLinks();
});  
</script>