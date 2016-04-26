<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editlocation.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="siteevent_dashboard_content">
    <?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
    <h3><?php echo $this->translate("Edit Event Location") ?></h3>
    <p>
        <?php echo $this->translate("Edit the location of your event by clicking on 'Edit Event Location' below. You can accurately mark the position of your event on the map by dragging-and-dropping the marker on the map at the right position, and then clicking on Save Changes to save the position.") ?>
    </p>
    <br />

    <?php if (!empty($this->location)): ?>
        <div class="siteevent_edit_location_form b_medium siteevent_review_block">
            <div class="global_form_box">
                <div>
                    <div class="formlocation_edit_label"><?php echo $this->translate('Location: '); ?></div>
                    <div class="formlocation_add"><?php echo $this->location->location ?></div>
                    <?php
                    echo $this->htmlLink(array(
                        'route' => "siteevent_specific",
                        'action' => 'editaddress',
                        'event_id' => $this->siteevent->event_id
                            ), $this->translate("Edit Event Location"), array(
                        'class' => 'smoothbox icon_siteevents_map_edit buttonlink fright'
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="siteevent_edit_location_form b_medium siteevent_review_block">
            <?php echo $this->form->render($this); ?>
        </div>
        <div class="siteevent_edit_location_map b_medium siteevent_review_block">
            <div style="width:100%;">
                <div class="seaocore_map" style="padding:0px;">
                    <div id="mapCanvas"></div>
                    <?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
                    <?php if (!empty($siteTitle)) : ?>
                        <div class="seaocore_map_info">
                            <?php echo $this->translate("Locations on %s", "<a href='' target='_blank'>$siteTitle</a>"); ?>
                        </div>
                    <?php endif; ?>
                </div>	
            </div>
        </div>
    <?php else: ?>
        <div class="tip">
            <span>
                <?php $url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'editaddress', 'event_id' => $this->siteevent->event_id), "siteevent_specific", true); ?>
                <?php echo $this->translate('You have not added a location for your event. %1$sClick here%2$s to add a location for your event.', "<a onclick='javascript:Smoothbox.open(this.href);return false;' href='$url'>", "</a>"); ?>
            </span>
        </div>
    <?php endif; ?>
</div>

<?php if (!empty($this->location)): ?>
<?php $this->headScript()->appendFile("https://maps.google.com/maps/api/js?sensor=false"); ?>
<script type="text/javascript">
  var map;
  var geocoder = new google.maps.Geocoder();
  var tresponses;
  function geocodePosition(pos) {
    geocoder.geocode({
      latLng: pos
    }, function(responses) {
      if (responses && responses.length > 0) {
        updateMarkerAddress(responses[0].formatted_address);
        //	tresponses=responses;
        var len_add = responses[0].address_components.length;

        document.getElementById('address').value='';
        document.getElementById('country').value='';
        document.getElementById('state').value ='';
        document.getElementById('city').value ='';
        document.getElementById('zipcode').value ='';
        for (i=0; i< len_add; i++) {


          var types_location = responses[0].address_components[i].types;

          if(types_location=='country,political'){

            document.getElementById('country').value = responses[0].address_components[i].long_name;
          }else if(types_location=='administrative_area_level_1,political')
          {
            document.getElementById('state').value = responses[0].address_components[i].long_name;
          }else if(types_location=='administrative_area_level_2,political')
          {
            document.getElementById('city').value = responses[0].address_components[i].long_name;
          }else if(types_location=='postal_code')
          {
            document.getElementById('zipcode').value = responses[0].address_components[i].long_name;
          }
          else if(types_location=='street_address')
          {
            if(document.getElementById('address').value=='')
              document.getElementById('address').value = responses[0].address_components[i].long_name;
            else
              document.getElementById('address').value = document.getElementById('address').value+','+responses[0].address_components[i].long_name;

          }else if(types_location=='locality,political')
          {  if(document.getElementById('address').value=='')
              document.getElementById('address').value = responses[0].address_components[i].long_name;
            else
              document.getElementById('address').value = document.getElementById('address').value+','+responses[0].address_components[i].long_name;
          }else if(types_location=='route')
          {
            if(document.getElementById('address').value=='')
              document.getElementById('address').value = responses[0].address_components[i].long_name;
            else
              document.getElementById('address').value = document.getElementById('address').value+','+responses[0].address_components[i].long_name;
          }else if(types_location=='sublocality,political')
          {
            if(document.getElementById('address').value=='')
              document.getElementById('address').value = responses[0].address_components[i].long_name;
            else
              document.getElementById('address').value = document.getElementById('address').value+','+responses[0].address_components[i].long_name;

          }

        }

        document.getElementById('zoom').value=map.getZoom();
      } else {
        document.getElementById('address').value='';
        document.getElementById('country').value='';
        document.getElementById('state').value ='';
        document.getElementById('city').value ='';
        updateMarkerAddress('Cannot determine address at this location.');
      }
    });
  }


 function updateMarkerPosition(latLng) { 
    $('filter_form').getElementById('latitude').value = latLng.lat();
    $('filter_form').getElementById('longitude').value = latLng.lng();
  }

  function updateMarkerAddress(str) {
    document.getElementById('formatted_address').value = str;
  }

  function initialize() {
    var latLng = new google.maps.LatLng(<?php echo $this->location->latitude; ?>,<?php echo $this->location->longitude; ?>);
    map = new google.maps.Map(document.getElementById('mapCanvas'), {
      zoom: <?php echo $this->location->zoom; ?>,
      center: latLng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    var marker = new google.maps.Marker({
      position: latLng,
      title: 'Point Location',

      map: map,
      draggable: true
    });

   
    // Add dragging event listeners.
    google.maps.event.addListener(marker, 'dragstart', function() {
      updateMarkerAddress('Dragging...');
    });

    google.maps.event.addListener(marker, 'drag', function() {
      // updateMarkerStatus('Dragging...');
      updateMarkerPosition(marker.getPosition());
    });

    google.maps.event.addListener(marker, 'dragend', function() {
      //  updateMarkerStatus('Drag ended');



      geocodePosition(marker.getPosition());
    });
  }

  // Onload handler to fire off the app.
  google.maps.event.addDomListener(window, 'load', initialize);
</script>

<?php endif; ?>
