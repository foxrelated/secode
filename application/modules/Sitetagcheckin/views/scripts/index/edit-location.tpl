<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-location.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitetagcheckin/externals/styles/style_sitetagcheckin.css'); ?>

<div class='layout_middle'>
	<div class="stchecking_pages_breadcrumbs">
		<?php echo $this->htmlLink($this->event->getHref(), $this->itemPhoto($this->event, 'thumb.icon', '', array('align' => 'left'))) ?>
		<h2>	
			<?php echo $this->event->__toString() ?>	
			<?php //echo $this->translate('&raquo; '); ?>
			<?php //echo $this->htmlLink($this->event->getHref(), $this->translate('Events')) ?>
		</h2>
	</div>
	<?php
	$this->headScript()
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
	?>
  <div class="sitetagcheckin_edit_content">
  <div id="show_tab_content">
  	<div class="sitetagcheckin_editlocation_wrapper">
			<?php if (!empty($this->location)): ?>
				<h4><?php echo $this->translate('Edit Location') ?></h4>
				<p>
					<?php echo $this->translate('Edit location by clicking on "Edit Location" below. You can also accurately mark the position of your entity on the map by dragging-and-dropping the marker(shown in red color) on the map at the right position, and then click on "Save Changes" to save the position.') ?>
				</p>
				<br />
				<div class="edit_form">
					<div class="global_form_box">
						<div>
							<div class="formlocation_edit_label"><?php echo $this->translate('Location: ');?></div>
							<?php
								echo $this->htmlLink(array(
								'route' => 'sitetagcheckin_specific',
								'event_id' => $this->event_id,
								'seao_locationid' => $this->seao_locationid,
								'action' => 'edit-address'
								), $this->translate('Edit Location'), array(
								
								'class' => 'smoothbox stcheckin_icon_map_edit buttonlink fright',
								));
							?>
							<div class="formlocation_add"><?php echo $this->location->location ?></div>
	
						</div>
					</div>
				</div>
				<div class="edit_form">
					<?php echo $this->form->render($this); ?>
				</div>	
				<div class="global_form">
						<div>
							<div class="seaocore_map" style="padding:0px;">
								<div id="mapCanvas"></div>
								<?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
								<?php if (!empty($siteTitle)) : ?>
								<div class="seaocore_map_info"><?php echo "Locations on "; ?><a href="" target="_blank"><?php echo $siteTitle; ?></a></div>
								<?php endif; ?>
							</div>	
						</div>
				</div>
			<?php else: ?>
				<div class="tip">
					<span>
					<?php echo $this->translate('You have not added a location for your event. Click'); ?>
											<a  onclick="javascript:Smoothbox.open('<?php echo Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'edit-address', 'event_id' => $this->event_id,), 'sitetagcheckin_specific', true) ?>');"
													href="javascript:void(0);"><?php echo $this->translate('here'); ?></a>
							<?php echo $this->translate('to add it.'); ?>
					</span>
				</div>
			<?php endif; ?>
			</div>
		</div>
  </div>
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
    document.getElementById('latitude').value = latLng.lat();
    document.getElementById('longitude').value = latLng.lng();
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
<style types="text/css">
	.edit_form .global_form_box > div .form-elements > div input[type="text"]{width:160px;}
</style>