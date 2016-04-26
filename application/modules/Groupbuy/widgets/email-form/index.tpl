<style type="text/css">
#subscription_email_form p.form-description
{
    background-color: #FAF6E4;
    background-image: url("./application/modules/Core/externals/images/tip.png");
    background-position: 6px 6px;
    background-repeat: no-repeat;
    border: 1px solid #E4DFC6;
    border-radius: 3px 3px 3px 3px;
    display: inline-block;
    float: left;
    margin-bottom: 15px;
    padding: 6px 6px 6px 27px;
    width: 135px;
}
</style>

<?php $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places");?>

<h3><?php echo $this->translate("Email Subscription"); ?></h3>  
<?php echo $this->form->render($this) ?>	

<script type="text/javascript" charset="utf-8">
en4.groupbuy = {
    removeCategory : function(a, b) {
        var d = $('add_more_id_' + ( a - 1));
        if(d) {
            d.show();
        }
        var c = $('category_id_' + a);
        if(c) {
            c.selectedIndex = 0;
        }
        var e = $('category_' + a + '-wrapper');
        if(e) {
            e.hide();
        }
    },
    addMoreCategory : function(a, b) {
        var c = $('add_more_id_' + a);
        if(c) {
            c.hide();
        }
        var e = $('category_' + b + '-wrapper');
        if(e) {
            e.show();
        }
    },
    valform : function val(f) {
        var f = f ? f : $('subscription_email_form');
        var data = {};
        var cat = f.category_id;
        var loc = f.location_id;
        var age = f.age;
        var email = f.email;
        var lat = f.slat;
        var long = f.slong;
        var within = f.within;
        data.category_id = cat.options[cat.selectedIndex].value;
        data.location_id = loc.options[loc.selectedIndex].value;
        data.age = age.value.trim();
        data.email = email.value.trim();
        data.lat = lat.value.trim();
        data.long = long.value.trim();
        data.within = within.value.trim();
        data.url = f.action;
        return data;
    },
    validateSubscription : function(f, warn) {
        var f = f ? f : $('subscription_email_form');
        var data = en4.groupbuy.valform(f);
        if(!data.email.test(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/)) {
            var warning = '<?php
            $translate = Zend_Registry::get('Zend_Translate');
            echo $translate->translate('Invalid email address.');
            ?>';
            warn(warning);
            f.email.focus();
            return false;
        }

        if(!data.category_id) {
            var warning = '<?php
            $translate = Zend_Registry::get('Zend_Translate');
            echo $translate->translate('Select a category.');
            ?>';
            warn(warning);
            f.category_id.focus();
            return false;
        }

        if(!data.location_id) {
            var warning = '<?php
            $translate = Zend_Registry::get('Zend_Translate');
            echo $translate->translate('Select a location.');
            ?>';
            warn(warning);

            f.location_id.focus();
            return false;
        }
        if(!data.age.test(/^\d{1,2}$/)) {
            var warning = '<?php
            $translate = Zend_Registry::get('Zend_Translate');
            echo $translate->translate('Type your age.');
            ?>';
            warn(warning);

            f.age.focus();
            return false;
        }

        if(data.age < 2 || data.age > 99) {
            var warning = '<?php
            $translate = Zend_Registry::get('Zend_Translate');
            echo $translate->translate('Type your age.');
            ?>';
            warn(warning);
            f.age.focus();
            return false;
        }

        //f.subscribe.disabled = "disabled";
        return data;
    },
    subscribeEmail : function(f) {
        function warn(msg) {
            $$('form#subscription_email_form p.form-description')[0].innerHTML = msg;
        }

        var data = en4.groupbuy.validateSubscription(f, warn);
        if (!data) {
            return false
        }
        data.format = 'json';
        var request = new Request.JSON({
            'url' : data.url,
            'data' : data,
            onComplete : function(res) {
                console.log(res);
                if(res.error == false) {
                    $('subscription_email_form').innerHTML = res.message;
                } else {
                    warn(res.message);
                    $('subscription_email_form').subscribe.disable = false;
                }

            }
        });
        request.send();
        return false;
    }
}

var initMap = function() {
    var position = {
        lat : 40.675658,
        lng : -73.995287
    };
    if($('longitude') && $('longitude').value) {
        position.lat = parseFloat($('latitude').value);
        position.lng = parseFloat($('longitude').value);
    }
    var myLatlng = new google.maps.LatLng(position.lat, position.lng);
    var myOptions = {
        zoom : 15,
        center : myLatlng,
        mapTypeId : google.maps.MapTypeId.ROADMAP
    };
    var mapEle = document.createElement('DIV');
    mapEle.id = 'map_canvas_edit';
    $('latitude-element').appendChild(mapEle);
    function deleteMarker() {
        if(marker) {
            marker.setMap(null);
            marker = null;
        }
    }

    function resetMarker(pos) {
        deleteMarker();
        marker = new google.maps.Marker({
            position : pos,
            animation : google.maps.Animation.DROP,
            draggable : true,
            map : map,
            title : "Drag this marker to set position of your deal!"
        });
        updatePosition(pos);
        return marker;
    }

    var map = new google.maps.Map(document.getElementById('map_canvas_edit'), myOptions);

    var input = document.getElementById('address');
    var autocomplete = new google.maps.places.Autocomplete(input);

    var marker = resetMarker(myLatlng);

    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        //infowindow.close();
        var place = autocomplete.getPlace();
        if(place.geometry.viewport) {
            deleteMarker();
            map.fitBounds(place.geometry.viewport);
        } else {
            var pos = place.geometry.location;
            marker = resetMarker(pos);
            map.setCenter(pos);
            map.setZoom(17);
            // Why 17? Because it looks good.
        }
    });
    function showInfo(msg) {

    }

    // Add dragging event listeners.
    google.maps.event.addListener(marker, 'dragstart', function() {
        showInfo('Dragging...');
    });
    google.maps.event.addListener(map, "rightclick", function(event) {resetMarker(event.latLng);
    });
    function updatePosition(pos) {
        if(pos && pos.lat && pos.lng) {
            $('latitude').value = pos.lat();
            $('longitude').value = pos.lng();
        }
    };

    google.maps.event.addListener(marker, 'dragend', function() {
        updatePosition(marker.getPosition());
    });
}
function viewGoogleMap(canvasId) {
    var ele = $(canvasId);
    if(ele == null || ele == undefined) {
        return;
    }
    var lat = ele.getAttribute('latitude');
    var lng = ele.getAttribute('longitude');
    if(lat) {
        lat = parseFloat(lat)
    }
    if(lng) {
        lng = parseFloat(lng)
    }
    if(!lat || !lng) {
        ele.innerHTML = "no position associate with this deal!";
        return;
    }
    var myLatlng = new google.maps.LatLng(lat, lng);
    var myOptions = {
        zoom : 13,
        center : myLatlng,
        mapTypeId : google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById(canvasId), myOptions);
    var marker = new google.maps.Marker({
        position : myLatlng,
        map : map,
        title : "Deal Position"
    });
}

$(window).addEvent('domready', function() {
    //viewGoogleMap('map_canvas');
    viewGoogleMapFromAddress('map_canvas');
});
function viewGoogleMapFromAddress(canvasId) {
    var position = {
        lat : 40.675658,
        lng : -73.995287
    };
    var ele = $(canvasId);
    if(ele == null || ele == undefined) {
        return;
    }
    var request = {
        address: ele.getAttribute('title') + ' - ' + ele.getAttribute('location')
    };
    var myLatlng = new google.maps.LatLng(position.lat, position.lng);
    var myOptions = {
        zoom : 15,
        center : myLatlng,
        mapTypeId : google.maps.MapTypeId.ROADMAP
    };
    var map = null;
    geocoder = new google.maps.Geocoder();

    function matchGeoCoder(request) {
        geocoder.geocode(request, showResults);
    }
    function showResults(results, status) {
        if(status == google.maps.GeocoderStatus.OK && results&& results.length){
            var result =  results[0];
            var latlng =  result.geometry.location;
            map =  new google.maps.Map(document.getElementById(canvasId), {
                zoom : 15,
                center : latlng,
                mapTypeId : google.maps.MapTypeId.ROADMAP
            });
            var marker = new google.maps.Marker({
                position : latlng,
                map : map,
                title : result.formatted_address
            });
             var infowindow = new google.maps.InfoWindow({
                content: $('company_component_inforbox').innerHTML
            });
            google.maps.event.addListener(marker, 'click', function() {
              infowindow.open(map,marker);
            });
            $('groupbuy_loading_google_map').style.display='none';
        } else {
            $('groupbuy_loading_google_map').innerHTML = 'Invalid Address!';
        }

    }
    matchGeoCoder(request);
}
</script>

<script type="text/javascript">

    function initialize() {
        var input = /** @type {HTMLInputElement} */(
                document.getElementById('location_subscribe'));

        var autocomplete = new google.maps.places.Autocomplete(input);

        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                return;
            }

            document.getElementById('slat').value = place.geometry.location.lat();
            document.getElementById('slong').value = place.geometry.location.lng();
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
                                document.getElementById('location_subscribe').value = json.results[0].formatted_address;
                                document.getElementById('slat').value = json.results[0].geometry.location.lat;
                                document.getElementById('slong').value = json.results[0].geometry.location.lng;
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