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
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()
->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>

<script type="text/javascript">
    var myLatlng;
    function initializeSidebarMap() {
        var myLatlng = new google.maps.LatLng(<?php echo $this->location->latitude; ?>,<?php echo $this->location->longitude; ?>);
        var myOptions = {
            zoom: <?php echo $this->location->zoom; ?>,
            center: myLatlng,
            navigationControl: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        var map = new google.maps.Map(document.getElementById("siteevent_view_map_canvas_sidebar"), myOptions);

        var marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            title: "<?php echo str_replace('"', ' ', $this->siteevent->getTitle()) ?>"

        });

        google.maps.event.addListener(marker, 'click', function() {
            //infowindow.open(map,marker);
        });

        $$('.tab_layout_siteevent_location_sidebar_siteevent').addEvent('click', function() {
            google.maps.event.trigger(map, 'resize');
            map.setZoom(<?php echo $this->location->zoom; ?>);
            map.setCenter(myLatlng);
        });

        google.maps.event.addListener(map, 'click', function() {
            //infowindow.close();
            google.maps.event.trigger(map, 'resize');
            map.setZoom(<?php echo $this->location->zoom; ?>);
            map.setCenter(myLatlng);
        });
    }
</script>

<div class="siteevent_profile_map b_dark clr">
    <ul>
        <li class="seaocore_map">
            <div id="siteevent_view_map_canvas_sidebar" style="height:<?php echo $this->height; ?>px"></div>
        </li>
    </ul>
</div>	

<div class='clr o_hidden'>
    <ul class="siteevent_side_widget siteevent_profile_event_info">
        <li class="clr">
            <div class="siteevent_listings_stats">
                <i class="siteevent_icon_strip siteevent_icon siteevent_icon_location" title="<?php echo $this->translate("Location") ?>"></i>
                <div class="o_hidden">
                    <?php echo $this->location->location; ?> - <b>
                        <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $this->location->event_id, 'resouce_type' => 'siteevent_event'), $this->translate("Get Directions"), array('onclick' => 'openSmoothbox(this);return false')); ?></b>
                </div>
            </div>
        </li>

        <?php if (in_array('startDate', $this->showContent) || in_array('endDate', $this->showContent)) : ?>
            <?php $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null; ?>
            <?php $dateTimeInfo = array(); ?>
            <?php $dateTimeInfo['occurrence_id'] = $occurrence_id; ?>
            <?php $dateTimeInfo['showStartDateTime'] = in_array('startDate', $this->showContent); ?>
            <?php $dateTimeInfo['showEndDateTime'] = in_array('endDate', $this->showContent); ?>
            <?php $this->eventDateTime($this->siteevent, $dateTimeInfo); ?>
        <?php endif; ?>
    </ul>
</div>

<script type="text/javascript">
    window.addEvent('domready', function() {
        initializeSidebarMap();
    });
</script>