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

<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()
->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<script type="text/javascript">
    var myLatlng;
    function initialize() {
        var myLatlng = new google.maps.LatLng(<?php echo $this->location->latitude; ?>,<?php echo $this->location->longitude; ?>);
        var myOptions = {
            zoom: <?php echo $this->location->zoom; ?>,
            center: myLatlng,
            navigationControl: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        var map = new google.maps.Map(document.getElementById("siteevent_view_map_canvas"), myOptions);

        var contentString = "<?php
echo $this->string()->escapeJavascript($this->partial('application/modules/Siteevent/views/scripts/_mapInfoWindowContent.tpl', array(
            'siteevent' => $this->siteevent,
            'ratingValue' => $ratingValue,
            'ratingType' => $ratingType,
//                'postedby' => 1,
            'postedbytext' => 'Event',
            'statistics' => array('venueName', 'location'),
//                'showContent' => array("price", "location"),
            'content_type' => null,
            'ratingShow' => $ratingShow
        )), false);
?>";

        var infowindow = new google.maps.InfoWindow({
            content: contentString,
            size: new google.maps.Size(250, 50)

        });

        var marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            title: "<?php echo str_replace('"', ' ', $this->siteevent->getTitle()) ?>"

        });

        google.maps.event.addListener(marker, 'click', function() {
            infowindow.open(map, marker);
        });

        $$('.tab_layout_siteevent_location_siteevent').addEvent('click', function() {
            google.maps.event.trigger(map, 'resize');
            map.setZoom(<?php echo $this->location->zoom; ?>);
            map.setCenter(myLatlng);
        });

        google.maps.event.addListener(map, 'click', function() {
            infowindow.close();
            google.maps.event.trigger(map, 'resize');
            map.setZoom(<?php echo $this->location->zoom; ?>);
            map.setCenter(myLatlng);
        });
    }
</script>

<div class="siteevent_profile_map b_dark clr">
    <ul class="sitepage_profile_location">
        <li class="seaocore_map">
            <div id="siteevent_view_map_canvas"></div>
            <?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
            <?php if (!empty($siteTitle)) : ?>
                <div class="seaocore_map_info">
                    <?php echo $this->translate("Locations on %s", "<a href='' target='_blank'>$siteTitle</a>"); ?>
                </div>
            <?php endif; ?>
        </li>
    </ul>
</div>	

<div class='profile_fields clr'>
    <h4>
        <span><?php echo$this->translate('Location Information') ?></span>
    </h4>
    <ul>
        <li>
            <span><?php echo $this->translate('Location:'); ?> </span>
            <span><b><?php echo $this->location->location; ?></b> - <b>
                    <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $this->location->event_id, 'resouce_type' => 'siteevent_event'), $this->translate("Get Directions"), array('onclick' => 'openSmoothbox(this);return false')); ?>
                </b></span>
        </li>
        <?php if (!empty($this->location->formatted_address)): ?>
            <li>
                <span><?php echo $this->translate('Formatted Address:'); ?> </span>
                <span><?php echo $this->location->formatted_address; ?> </span>
            </li>
        <?php endif; ?>
        <?php if (!empty($this->location->address)): ?>
            <li>
                <span><?php echo $this->translate('Street Address:'); ?> </span>
                <span><?php echo $this->location->address; ?> </span>
            </li>
        <?php endif; ?>
        <?php if (!empty($this->location->city)): ?>
            <li>
                <span><?php echo $this->translate('City:'); ?></span>
                <span><?php echo $this->location->city; ?> </span>
            </li>
        <?php endif; ?>
        <?php if (!empty($this->location->zipcode)): ?>
            <li>
                <span><?php echo $this->translate('Zipcode:'); ?></span>
                <span><?php echo $this->location->zipcode; ?> </span>
            </li>
        <?php endif; ?>
        <?php if (!empty($this->location->state)): ?>
            <li>
                <span><?php echo $this->translate('State:'); ?></span>
                <span><?php echo $this->location->state; ?></span>
            </li>
        <?php endif; ?>
        <?php if (!empty($this->location->country)): ?>
            <li>
                <span><?php echo $this->translate('Country:'); ?></span>
                <span><?php echo $this->location->country; ?></span>
            </li>
        <?php endif; ?>
    </ul>
</div>

<script type="text/javascript">
    window.addEvent('domready', function() {
        initialize();
    });
</script>