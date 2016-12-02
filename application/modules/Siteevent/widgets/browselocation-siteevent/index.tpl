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

<?php if (!$this->is_ajax): ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Siteevent/widgets/location-search/index.tpl' ?>
<?php endif; ?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css');
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()
->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");

$latitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.map.latitude', 0);
$longitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.map.longitude', 0);
$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
$datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium');
?>

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

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js') ?>

<script type="text/javascript">

    var current_page = '<?php echo $this->current_page; ?>';

    var paginatePageLocations = function(page) {

        var formElements = document.getElementById('filter_form');
        var parms = formElements.toQueryString();
        var param = (parms ? parms + '&' : '') + 'is_ajax=1&format=html&page=' + page;
        document.getElementById('page_location_loding_image').style.display = '';
        //var url = en4.core.baseUrl + 'widget/index/mod/siteevent/name/browselocation-siteevent';
        var url = en4.core.baseUrl + 'widget/index/content_id/<?php echo sprintf('%d', $this->identity) ?>';
        clearOverlays();
        gmarkers = [];

        en4.core.request.send(new Request.HTML({
            method: 'post',
            'url': url,
            'data': param,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                document.getElementById('page_location_loding_image').style.display = 'none';
                document.getElementById('list_location_map_anchor').getParent().innerHTML = responseHTML;
                setMarker();
            }
        }));
    };

    var pageAction = function(page) {
        paginatePageLocations(page);
    }
</script>

<?php $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1); ?>
<?php if (empty($this->is_ajax)) : ?>
    <div class="list_browse_location" id="list_browse_location" >
        <?php if (count($this->paginator) > 0): ?>
            <div class="list_map_container_right" id ="list_map_container_right"></div>
            <div id="list_map_container" class="list_map_container absolute" style="visibility:hidden;">
                <div class="list_map_container_topbar" id='list_map_container_topbar' style ='display:none;'>
                    <a id="largemap" href="javascript:void(0);" onclick="smallLargeMap(1)" class="bold fleft">&laquo; <?php echo $this->translate('Large Map'); ?></a>
                    <a id="smallmap" href="javascript:void(0);" onclick="smallLargeMap(0)" class="bold fleft"><?php echo $this->translate('Small Map'); ?> &raquo;</a>
                </div>

                <div class="list_map_container_map_area fleft seaocore_map" id="listlocation_map">
                    <div class="list_map_content" id="listlocation_browse_map_canvas" ></div>
                    <?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
                    <?php if (!empty($siteTitle)) : ?>
                        <div class="seaocore_map_info"><?php echo $this->translate("Locations on %s", "<a href='' target='_blank'>$siteTitle</a>"); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <div class="list_map_container_list" id="list_content_content">
<?php endif; ?>
    <a id="list_location_map_anchor" class="pabsolute"></a>
    <?php if (count($this->paginator) > 0): ?>
        <ul class="siteevent_browse_list" id="seaocore_browse_list"><?php if (!empty($this->is_ajax)) : ?>	
                <li style="border:none;padding-top:1px;"><p>
                        <?php echo $this->translate(array('%s event found.', '%s events found.', $this->totalresults), $this->locale()->toNumber($this->totalresults)) ?>
                    </p></li>
                <?php foreach ($this->paginator as $item): ?>

                    <?php if (!empty($item->location) || !empty($this->locationVariable)) : ?>
                        <li class="b_medium">
                            <div class='siteevent_browse_list_photo b_medium'>
                                <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $item->featured): ?>
                                    <i class="siteevent_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
                                <?php endif; ?>
                                <?php if (!empty($this->statistics) && in_array('newLabel', $this->statistics) && $item->newlabel): ?>
                                    <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                <?php endif; ?>

                                <?php echo $this->htmlLink($item->getHref(array('showEventType' => $this->showEventType)), $this->itemPhoto($item, 'thumb.main', '', array('align' => 'center')), array('title' => $item->getTitle(), 'target' => '_parent', 'class' => !empty($item->location) ? "marker_photo_" . $item->event_id : 'un_location_list')); ?>
                                <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($item->sponsored)): ?>
                                    <div class="siteevent_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505'); ?>">
                                        <?php echo $this->translate('SPONSORED'); ?>                 
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class='siteevent_browse_list_info'>
                                <div class='siteevent_browse_list_info_header'>
                                    <div class="siteevent_list_title_small o_hidden">
                                        <?php
                                        echo $this->htmlLink($item->getHref(array('showEventType' => $this->showEventType)), $item->getTitle(), array('title'
                                            => $item->getTitle(), 'target' => '_parent', 'class' => !empty($item->location) ? "marker_" . $item->event_id . "" : 'un_location_list'));
                                        ?>
                                    </div>
                                </div>

                                <!-- EVENT INFO WORK -->
                                <?php if (!empty($this->statistics)) : ?>
                                    <?php echo $this->eventInfo($item, $this->statistics, array('ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation, 'showEventType' => $this->showEventType)); ?>
                                <?php endif; ?>
                                <!-- END EVENT INFO WORK -->

                                <?php if ((!empty($item->location) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1))): ?>
                                        <?php if (!empty($item->distance) && isset($item->distance)): ?>
                                        <div class="seaocore_browse_list_info_stat">
                                            <?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.proximity.search.kilometer')): ?>
                                                <b><?php echo $this->translate("approximately %s miles", round($item->distance, 2)); ?></b>
                                             <?php else: ?>
                                                <b><?php $distance = (1 / 0.621371192) * $item->distance;
                                                    echo $this->translate("approximately %s kilometers", round($distance, 2));
                                                    ?></b>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <div class="clr siteevent_browse_location_paging" style="margin-top:10px;">
                <?php echo $this->paginationControl($this->result, null, array("pagination/pagination.tpl", "siteevent"), array("orderby" => $this->orderby)); ?>
                <?php if (count($this->paginator) > 1): ?>
                    <div class="fleft" id="page_location_loding_image" style="display: none;margin:5px;">
                        <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="" />
                    </div>
                <?php endif; ?>
            </div>	
        <?php endif; ?>
    <?php else: ?>
        <div class="tip"> 
            <span><?php echo $this->translate("No events have been posted yet."); ?></span>
        </div>
    <?php endif; ?>
    <?php if (empty($this->is_ajax)) : ?>	
        </div>
    </div>

    <script type="text/javascript" >

        /* moo style */
        window.addEvent('domready', function() {
            //smallLargeMap(1);
            var Clientwidth = $('global_content').getElement(".layout_siteevent_browselocation_siteevent").clientWidth;
                var offsetWidth = '';
            if($('list_map_container'))
             offsetWidth = $('list_map_container').offsetWidth;
            if($('listlocation_browse_map_canvas'))
            $('listlocation_browse_map_canvas').setStyle("height", offsetWidth);

            if (document.getElementById("smallmap"))
                document.getElementById("smallmap").style.display = "none";
            if ($('list_map_right'))
                $('list_map_right').style.display = 'none';

    <?php if ($this->paginator->count() > 0): ?>
        <?php if ($this->enableLocation): ?>
                    initialize();
        <?php endif; ?>
    <?php endif; ?>
        });

        if ($('seaocore_browse_list')) {

            var elementStartY = $('listlocation_map').getPosition().x;
            var offsetWidth = $('list_map_container').offsetWidth;
            var actualRightPostion = window.getSize().x - (elementStartY + offsetWidth);

            function setMapContent() {

                if (!$('seaocore_browse_list')) {
                    return;
                }

                var element = $("list_map_container");
                if (element.offsetHeight > $('seaocore_browse_list').offsetHeight) {
                    if (!element.hasClass('absolute')) {
                        element.addClass('absolute');
                        element.removeClass('fixed');
                        if (element.hasClass('bottom'))
                            element.removeClass('bottom');
                    }
                    return;
                }

                var elementPostionStartY = $('seaocore_browse_list').getPosition().y;
                var elementPostionStartX = $('list_map_container').getPosition().x;
                var elementPostionEndY = elementPostionStartY + $('seaocore_browse_list').offsetHeight - element.offsetHeight;

                if (((elementPostionEndY) < window.getScrollTop())) {
                    if (element.hasClass('absolute'))
                        element.removeClass('absolute');
                    if (element.hasClass('fixed'))
                        element.removeClass('fixed');
                    if (!element.hasClass('bottom'))
                        element.addClass('bottom');
                }
                else if (((elementPostionStartY) < window.getScrollTop())) {
                    if (element.hasClass('absolute'))
                        element.removeClass('absolute');
                    if (!element.hasClass('fixed'))
                        element.addClass('fixed');
                    if (element.hasClass('bottom'))
                        element.removeClass('bottom');
                    element.setStyle("right", actualRightPostion);
                    element.setStyle("width", offsetWidth);
                }
                else if (!element.hasClass('absolute')) {
                    element.addClass('absolute');
                    element.removeClass('fixed');
                    if (element.hasClass('bottom'))
                        element.removeClass('bottom');
                }
            }

            window.addEvent('scroll', function() {
                setMapContent();
            });

        }

        function smallLargeMap(option) {
            if (option == '1') {
                $('listlocation_browse_map_canvas').setStyle("height", '400px');
                document.getElementById("largemap").style.display = "none";
                document.getElementById("smallmap").style.display = "block";
                if (!$('list_map_container').hasClass('list_map_container_exp'))
                    $('list_map_container').addClass('list_map_container_exp');
            } else {
                $('listlocation_browse_map_canvas').setStyle("height", offsetWidth);
                document.getElementById("largemap").style.display = "block";
                document.getElementById("smallmap").style.display = "none";
                if ($('list_map_container').hasClass('list_map_container_exp'))
                    $('list_map_container').removeClass('list_map_container_exp');

            }
            setMapContent();
            google.maps.event.trigger(map, 'resize');
        }
    </script>

    <script type="text/javascript" >
        function owner(thisobj) {
            var Obj_Url = thisobj.href;
            Smoothbox.open(Obj_Url);
        }
    </script>

<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/infobubble.js");
?>

    <script type="text/javascript" >
        //<![CDATA[
        // this variable will collect the html which will eventually be placed in the side_bar
        var side_bar_html = "";

        // arrays to hold copies of the markers and html used by the side_bar
        // because the function closure trick doesnt work there
        var gmarkers = [];
        var infoBubbles;
        var markerClusterer = null;
        // global "map" variable
        var map = null;
        // A function to create the marker and set up the event window function
        function createMarker(latlng, name, html, title_page, page_id) {
            var contentString = html;
            if (name == 0) {
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    title: title_page,
                    // page_id : page_id,
                    animation: google.maps.Animation.DROP,
                    zIndex: Math.round(latlng.lat() * -100000) << 5
                });
            }
            else {
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    title: title_page,
                    //page_id: page_id,
                    draggable: false,
                    animation: google.maps.Animation.BOUNCE
                });
            }

            gmarkers.push(marker);
            google.maps.event.addListener(marker, 'click', function() {
                google.maps.event.trigger(map, 'resize');
                map.setCenter(marker.position);
                //map.setZoom(<?php //echo '5';   ?> );
                infoBubbles.open(map, marker);
                infoBubbles.setContent(contentString);
            });

            //Show tooltip on the mouse over.
            $$('.marker_' + page_id).each(function(locationMarker) {
                locationMarker.addEvent('mouseover', function(event) {
                    google.maps.event.trigger(map, 'resize');
                    map.setCenter(marker.position);
                    infoBubbles.open(map, marker);
                    infoBubbles.setContent(contentString);
                });
            });

            //Show tooltip on the mouse over.
            $$('.marker_photo_' + page_id).each(function(locationMarker) {
                locationMarker.addEvent('mouseover', function(event) {
                    google.maps.event.trigger(map, 'resize');
                    map.setCenter(marker.position);
                    infoBubbles.open(map, marker);
                    infoBubbles.setContent(contentString);
                });
            });
        }

        function initialize() {

            // create the map
            var myOptions = {
                zoom: <?php echo '1'; ?>,
                center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
                //  mapTypeControl: true,
                // mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
                navigationControl: true,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }

            map = new google.maps.Map(document.getElementById("listlocation_browse_map_canvas"),
                    myOptions);

            google.maps.event.addListener(map, 'click', function() {
    <?php if ($this->enableLocation && $this->paginator->count() > 0): ?>
                    infoBubbles.close();
    <?php endif; ?>
            });
            setMarker();

        }

        function clearOverlays() {
            infoBubbles.close();
            google.maps.event.trigger(map, 'resize');

            if (gmarkers) {
                for (var i = 0; i < gmarkers.length; i++) {
                    gmarkers[i].setMap(null);
                }
            }
            if (markerClusterer) {
                markerClusterer.clearMarkers();
            }
        }

        function setMapCenterZoomPoint(bounds, maplocation) {
            if (bounds && bounds.min_lat && bounds.min_lng && bounds.max_lat && bounds.max_lng) {
                var bds = new google.maps.LatLngBounds(new google.maps.LatLng(bounds.min_lat, bounds.min_lng), new google.maps.LatLng(bounds.max_lat, bounds.max_lng));
            }
            if (bounds && bounds.center_lat && bounds.center_lng) {
                maplocation.setCenter(new google.maps.LatLng(bounds.center_lat, bounds.center_lng), 4);
            } else {
                maplocation.setCenter(new google.maps.LatLng(lat, lng), 4);
            }
            if (bds) {
                maplocation.setCenter(bds.getCenter());
                maplocation.fitBounds(bds);
            }
        }

        infoBubbles = new InfoBubble({
            maxWidth: 400,
            maxHeight: 400,
            shadowStyle: 1,
            padding: 0,
            backgroundColor: '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.tooltip.bgcolor', '#ffffff'); ?>',
            borderRadius: 5,
            arrowSize: 10,
            borderWidth: 1,
            borderColor: '#2c2c2c',
            disableAutoPan: true,
            hideCloseButton: false,
            arrowPosition: 50,
            //backgroundClassName: 'sitetag_checkin_map_tip',
            arrowStyle: 0
        });
    </script>
<?php endif; ?>

<script type="text/javascript" >

    function setMarker() {

        <?php if (count($this->locations) > 0) : ?>
            <?php foreach ($this->locations as $location) : ?>
                // obtain the attribues of each marker
                var lat = <?php echo $location->latitude ?>;
                var lng =<?php echo $location->longitude ?>;
                var point = new google.maps.LatLng(lat, lng);
                var page_id = <?php echo $this->list[$location->event_id]->event_id ?>;
                <?php if (!empty($enableBouce)): ?>
                            var sponsored = <?php echo $this->list[$location->event_id]->sponsored ?>
                <?php else: ?>
                            var sponsored = 0;
                <?php endif; ?>
                // create the marker
                var contentString = "<?php
                echo $this->string()->escapeJavascript($this->partial('application/modules/Siteevent/views/scripts/_mapInfoWindowContent.tpl', array(
                            'siteevent' => $this->list[$location->event_id],
                            'ratingValue' => $ratingValue,
                            'ratingType' => $ratingType,
                            'statistics' => $this->statistics,
                            'showEventType' => $this->showEventType,
                            'ratingShow' => $ratingShow)), false);
                ?>";
                //var marker = createMarker(point,sponsored,contentString,"<?php //echo str_replace('"',' ',$this->string()->escapeJavascript($this->list[$location->event_id]->getTitle()));   ?>", page_id);
                var marker = createMarker(point, sponsored, contentString, '', page_id);

            <?php endforeach; ?>
            if($('list_map_container'))
            $('list_map_container').style.display = 'block';
            google.maps.event.trigger(map, 'resize');
        <?php else: ?>
        if($('list_map_container'))
            $('list_map_container').style.display = 'none';
        <?php endif; ?>
    //  markerClusterer = new MarkerClusterer(map, gmarkers, {
    //  });
        <?php if (!empty($this->locations)): ?>
                    setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->locations)); ?>, map);
        <?php endif; ?>

        //$$('.un_location_list').each(function(el) { 
        $$('.un_location_list').addEvent('mouseover', function(event) {
            infoBubbles.close();
        });
        //  });
    }
</script>