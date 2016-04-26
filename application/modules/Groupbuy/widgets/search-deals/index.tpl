<?php $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places");?>

<script type="text/javascript">
 var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
</script>

<div class="clearfix">
  <?php echo $this->form->render($this) ?>
 
</div>

<script type="text/javascript">

    function initialize() {
        var input = /** @type {HTMLInputElement} */(
                document.getElementById('location_search'));

        var autocomplete = new google.maps.places.Autocomplete(input);

        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                return;
            }

            document.getElementById('lat').value = place.geometry.location.lat();
            document.getElementById('long').value = place.geometry.location.lng();
        });
    }

    google.maps.event.addDomListener(window, 'load', initialize);

    var getCurrentLocation = function(obj)
    {
        if(navigator.geolocation) {

            navigator.geolocation.getCurrentPosition(function(position) {

                var pos = new google.maps.LatLng(position.coords.latitude,
                        position.coords.longitude);

                if(pos)
                {

                    current_posstion = new Request.JSON({
                        'format' : 'json',
                        'url' : '<?php echo $this->url(array('action'=>'get-my-location'), 'ynlistings_general') ?>',
                        'data' : {
                            latitude : pos.lat(),
                            longitude : pos.lng(),
                        },
                        'onSuccess' : function(json, text) {

                            if(json.status == 'OK')
                            {
                                document.getElementById('location_search').value = json.results[0].formatted_address;
                                document.getElementById('lat').value = json.results[0].geometry.location.lat;
                                document.getElementById('long').value = json.results[0].geometry.location.lng;
                            }
                            else{
                                handleNoGeolocation(true);
                            }
                        }
                    });
                    current_posstion.send();

                }

            }, function() {
                handleNoGeolocation(true);
            });
        }
        else {
            // Browser doesn't support Geolocation
            handleNoGeolocation(false);
        }
        return false;
    };

    function handleNoGeolocation(errorFlag) {
        if (errorFlag) {
            document.getElementById('location_search').value = 'Error: The Geolocation service failed.';
        }
        else {
            document.getElementById('location_search').value = 'Error: Your browser doesn\'t support geolocation.';
        }
    }
</script>

