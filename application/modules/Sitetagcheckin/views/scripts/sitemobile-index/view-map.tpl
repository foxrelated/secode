<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: save-location.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<?php
//GET API KEY

$siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
  $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
  $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");

$lable=isset($this->checkin['vicinity']) ? ((isset($this->checkin['name']) && $this->checkin['name'] != $this->checkin['vicinity']) ? ( $this->checkin['name'] . ", " .$this->checkin['vicinity']) : $this->checkin['vicinity']) : $this->checkin['label'];
?>
<?php if($this->format):?>
<?php
	$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
	$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitetagcheckin/externals/styles/style_sitetagcheckin.css');
?>
<div class="sm-ui-map-popup">
	<div class="sm-ui-map-popup-head">
    <?php echo $lable; ?>
  </div>
  <div class="sm-ui-map-wrapper" style="height:200px;">
    <div id="sitetagechekin-view-map" class="sm-ui-map"></div>
    <?php $showContect = "Locations on "; ?>
  </div>
</div>
<?php else: ?>
<div class="sm-ui-map-popup">
	<div class="sm-ui-map-popup-head">
    <?php echo $lable; ?>
  </div>
  <div class="sm-ui-map-wrapper" style="height:200px;">
    <div id="sitetagechekin-view-map" class="sm-ui-map"></div>
    <?php $showContect = "Locations on "; ?>
    <div class="sm-ui-map-info"><?php echo $this->translate($showContect); ?><a href="" target="_blank"><?php echo $siteTitle; ?></a></div>
  </div>
</div>


<?php endif;?>
<script type="text/javascript">
var myLatlng;
  var directionsService = new google.maps.DirectionsService();
  var directionsDisplay;
  var mapGetDirection;
  var scrollGetDirection;
   function initializeGetDirectionMap() { 
    
    directionsDisplay = new google.maps.DirectionsRenderer();
    var myLatlng = new google.maps.LatLng(<?php echo $this->checkin['latitude'] ? $this->checkin['latitude'] : 0; ?>,<?php echo  $this->checkin['longitude'] ? $this->checkin['longitude'] : 0; ?>);
    var myOptions = {
      zoom: 8 ,
      center: myLatlng,
      //navigationControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    mapGetDirection = new google.maps.Map($.mobile.activePage.find('#sitetagechekin-view-map').get(0), myOptions);

    directionsDisplay.setMap(mapGetDirection);
    directionsDisplay.setPanel(document.getElementById('panel'));
    var marker = new google.maps.Marker({
      position: myLatlng,
      map: mapGetDirection
    });
  
  }
 sm4.core.runonce.add(function() { 
    initializeGetDirectionMap();
    //scrollGetDirection= new SEAOMooVerticalScroll('panel-content-area', 'panel-scroll', {} );
   
      });
   
 
</script>
