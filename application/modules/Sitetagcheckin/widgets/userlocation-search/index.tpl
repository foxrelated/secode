<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h3><?php echo $this->translate('Browse Members by Locations') ?></h3>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitetagcheckin/externals/styles/style_sitetagcheckin.css');

  $language = $_COOKIE['en4_language'];
  $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
   $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&sensor=true&key=$apiKey");
?>
<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'sitetagcheckin', array(
    'topLevelId' => (int) @$this->topLevelId,
    'topLevelValue' => (int) @$this->topLevelValue
  ))
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    var url = '<?php echo $this->url() ?>';
    var requestActive = false;
    var browseContainer, formElement, page, totalUsers, userCount, currentSearchParams;

    formElement = $$('.field_search_criteria')[0];
    browseContainer = $('browsemembers_results');

    // On search
    formElement.addEvent('submit', function(event) {
      return false;
      //event.stop();
      //searchMembers();
    });

    var searchMembers = window.searchMembers = function() {
      if( requestActive ) return;
      requestActive = true;

      currentSearchParams = formElement.toQueryString();

      var param = (currentSearchParams ? currentSearchParams + '&' : '') + 'ajax=1&format=html';

      var request = new Request.HTML({
        url: url,
        onComplete: function(requestTree, requestHTML) {
          requestTree = $$(requestTree);
          browseContainer.empty();
          requestTree.inject(browseContainer);
          requestActive = false;
          Smoothbox.bind();
        }
      });
      request.send(param);
    }

    var browseMembersViewMore = window.browseMembersViewMore = function() {
      if( requestActive ) return;
      $('browsemembers_loading').setStyle('display', '');
      $('browsemembers_viewmore').setStyle('display', 'none');

      var param = (currentSearchParams ? currentSearchParams + '&' : '') + 'ajax=1&format=html&page=' + (parseInt(page) + 1);

      var request = new Request.HTML({
        url: url,
        onComplete: function(requestTree, requestHTML) {
          requestTree = $$(requestTree);
          browseContainer.empty();
          requestTree.inject(browseContainer);
          requestActive = false;
          Smoothbox.bind();
        }
      });
      request.send(param);
    }

    window.addEvent('onChangeFields', function() {
      var firstSep = $$('li.browse-separator-wrapper')[0];
      var lastSep;
      var nextEl = firstSep;
      var allHidden = true;
      do {
        nextEl = nextEl.getNext();
        if( nextEl.get('class') == 'browse-separator-wrapper' ) {
          lastSep = nextEl;
          nextEl = false;
        } else {
          allHidden = allHidden && ( nextEl.getStyle('display') == 'none' );
        }
      } while( nextEl );
      if( lastSep ) {
        lastSep.setStyle('display', (allHidden ? 'none' : ''));
      }
    });
  });
</script>
<?php 
  $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">
	en4.core.runonce.add(function()
	{
		var contentAutocomplete = new Autocompleter.Request.JSON('displayname', '<?php echo $this->url(array('module' => 'sitetagcheckin', 'controller' => 'location', 'action' => 'getmember'), 'default', true) ?>', {
			'postVar' : 'text',
			'minLength': 1,
			'selectMode': 'pick',
			'autocompleteType': 'tag',
			'className': 'seaocore-autosuggest tag-autosuggest',
			'customChoices' : true,
			'filterSubset' : true,
			'multiple' : false,
			'injectChoice': function(token){
					var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id':token.label});
					new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice1'}).inject(choice);
					this.addChoiceEvents(choice).inject(this.choices);
					choice.store('autocompleteChoice', token);

				},
		});

		contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
			document.getElementById('user_id').value = selected.retrieve('autocompleteChoice').id;
		});

	});
</script>

<?php if( $this->form ): ?>
	<div class="stcheckin_advanced_search stcheckin_advanced_member_search global_form_box">
  	<?php echo $this->form->render($this) ?>
	</div>
  <div class="" id="eventlocation_location_pops_loding_image" style="display: none;">
	<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' />
	<?php //echo $this->translate("Loading ...") ?>
</div>
<?php endif ?>
<script type="text/javascript">
  var flag = '<?php echo $this->advanced_search; ?>';
  var mapShow = '<?php echo $this->mapShow; ?>';
  var mapGetDirection;
  var myLatlng;
	window.addEvent('domready', function() {
	  if(document.getElementById('sitepage_location').value == '') {
			submiForm();
		}
		
		if ($$('.browse-separator-wrapper')) {
			$$('.browse-separator-wrapper').setStyle("display",'none');
		}
	
	  $('eventlocation_location_pops_loding_image').injectAfter($('done-element'));
	  
	  
		new google.maps.places.Autocomplete(document.getElementById('sitepage_location'));
    if (navigator.geolocation && mapShow) {
      navigator.geolocation.getCurrentPosition(function(position){
			var lat = position.coords.latitude;
			var lng = position.coords.longitude;
			
			var myLatlng = new google.maps.LatLng(lat,lng);
			
			var myOptions = {
				zoom: 8 ,
				center: myLatlng,
				navigationControl: true,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}

			mapGetDirection = new google.maps.Map(document.getElementById("eventlocation_location_map_none"), myOptions);
    
        if(!position.address) {
          var service = new google.maps.places.PlacesService(mapGetDirection);
          var request = {
            location: new google.maps.LatLng(lat,lng), 
            radius: 500
          };
          
          service.search(request, function(results, status) { 
            if (status  ==  'OK') {
              var index = 0;
              var radian = 3.141592653589793/ 180;
              var my_distance = 1000; 
              for (var i = 0; i < results.length; i++){
              var R = 6371; // km
              var lat2 = results[i].geometry.location.lat();
              var lon2 = results[i].geometry.location.lng(); 
              var dLat = (lat2-lat) * radian;
              var dLon = (lon2-lng) * radian;
              var lat1 = lat * radian;
              var lat2 = lat2 * radian;

              var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2); 
              var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
              var d = R * c;
              if(d < my_distance) {
                index = i;
                my_distance = d;
              }
            }      
           
              document.getElementById('sitepage_location').value = (results[index].vicinity) ? results[index].vicinity :'';
              document.getElementById('Latitude').value = lat;
              document.getElementById('Longitude').value = lng;
              document.getElementById('locationmiles').value = 1000;
              
              //form submit by ajax
              submiForm();
            } 
          });
        } else {
          var delimiter = (position.address && position.address.street !=  '' && position.address.city !=  '') ? ', ' : '';
          var location = (position.address) ? (position.address.street + delimiter + position.address.city) : '';
          document.getElementById('sitepage_location').value = location;
					document.getElementById('Latitude').value = lat;
					document.getElementById('Longitude').value = lng;
					document.getElementById('locationmiles').value = 1000;
          //form submit by ajax
          submiForm();
        }
      });
    } else {
			submiForm();
		}

		advancedSearchEvents(flag);
		
	});

	function submiForm() {
	
//   	if ($('category_id').options[$('category_id').selectedIndex].value == 0) { 
// 			$('category_id').value = 0;
// 		}

    var mapShow = '<?php echo $this->mapShow; ?>';
		var  formElements = document.getElementById('filter_form');
		var url = en4.core.baseUrl + 'widget/index/mod/sitetagcheckin/name/bylocation-user';
		var parms = formElements.toQueryString(); 

		var param = (parms ? parms + '&' : '') + 'is_ajax=1&format=html';
		document.getElementById('eventlocation_location_pops_loding_image').style.display ='';
		en4.core.request.send(new Request.HTML({
			method : 'post',
			'url' : url,
			'data' : param,
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
				document.getElementById('eventlocation_location_pops_loding_image').style.display ='none';
				if (mapShow)
					$('eventlocation_map_container_topbar').style.display ='block';
					
				document.getElementById('eventlocation_location_map_anchor').getParent().innerHTML = responseHTML;
				if (mapShow)
					setMarker();
			  en4.core.runonce.trigger();
			  if (mapShow)
					$('eventlocation_map_container').style.visibility = 'visible'; 
				if ($('seaocore_browse_list')) {
					if (mapShow) {
						var elementStartY = $('eventlocation_map').getPosition().x ;
						var offsetWidth = $('eventlocation_map_container').offsetWidth;
						var actualRightPostion = window.getSize().x - (elementStartY + offsetWidth);
					}
				}
				switchview(<?php echo $this->defaultView ?>);
			}
		}), {
         "force":true
    });
	}

	function locationPage() {
		var  sitepage_location = document.getElementById('sitepage_location');

		if (document.getElementById('Latitude').value) {
			document.getElementById('Latitude').value = 0;
		}
		
		if(document.getElementById('Longitude').value) {
			document.getElementById('Longitude').value = 0;
		}
	}
	
	function locationSearch() {

	  var  formElements = document.getElementById('filter_form');
    formElements.addEvent('submit', function(event) { 
      event.stop();
      submiForm();
    });
  }

	function advancedSearchEvents() {
	
		if (flag == 0) {
		  if ($('fieldset-grp2'))
				$('fieldset-grp2').style.display = 'none';
				
			if ($('fieldset-grp1'))
				$('fieldset-grp1').style.display = 'none';
				
			flag = 1;
			$('advanced_search').value = 0;
			if ($('sitepage_street'))
			$('sitepage_street').value = '';
			if ($('sitepage_country'))
			$('sitepage_country').value = '';
			if ($('sitepage_state'))
			$('sitepage_state').value = '';
			if ($('sitepage_city'))
			$('sitepage_city').value = '';
			if ($('profile_type'))
			$('profile_type').value = '';
			changeFields($('profile_type'));
			if ($('orderby'))
			$('orderby').value = '';
		} else {
		  if ($('fieldset-grp2'))
				$('fieldset-grp2').style.display = 'block';
				
			if ($('fieldset-grp1'))
				$('fieldset-grp1').style.display = 'block';
				
			flag = 0;
			$('advanced_search').value = 1;
		}
  }
</script>

<div id="eventlocation_location_map_none" style="display: none;"></div>