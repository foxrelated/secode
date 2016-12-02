
/* $Id: core.js 9572 2011-12-27 23:41:06Z john $ */

var TagAutoSuggestionMap = new Class({
	  Implements : [Events, Options],
	  options : {
      tagsuggestOptions : {
        'url' : en4.core.baseUrl+'sitetagcheckin/checkin/suggest',
        'data' : {
          'format' : 'json'
        }
			},
    },
    location:'',
		call_empty_suggest : false,
    add_location:false,
    navigator_location_shared :false,
	
    initialize : function(options) {
      this.elements = new Hash(this.elements);
      this.params = new Hash(this.params);
			this.setOptions(options);
			if($('autosuggest_location_'+this.options.checkInOptions.locationDiv))
			$('autosuggest_location_'+this.options.checkInOptions.locationDiv).destroy();		
			
			this.elements.locationdiv = new Element('div', {
			'id' : 'autosuggest_location_'+this.options.checkInOptions.locationDiv,
			'class' : 'seaocheckinshowlocation',
			'style': 'display:block'
	    }).inject(document.getElement("." + this.options.checkInOptions.locationDiv));
			
			this.elements.input= new Element('input', {
				'type':'text',          
				'id' : 'location_'+this.options.checkInOptions.locationDiv,
				'name':'location',
				'class' : 'seaotagcheckinlocationfeild sitetagcheckin_icon_add',
					'autocomplete' : 'off',
					'value' : '',
					'style':'display:block;width:508px;',
					'title': en4.core.language.translate('Where have you been?'),
					'events' : {
           'click' : this.checkinToggle.bind(this)
					}																			 
			}).inject(this.elements.locationdiv);
			
		  if(this.elements.input) {
				this.elements.overText = new OverText(this.elements.input, {
					textOverride : en4.core.language.translate('Where have you been?'),
					'element' : 'label',
					'isPlainText' : true,
					 poll: true,
           pollInterval: 500,
					 positionOptions: {
						position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
						edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
						offset: {
							x: ( en4.orientation == 'rtl' ? -22 : 22 ),
							y: 4
						}
					}
				});
			}
    },

		checkinToggle: function(){
		 this.elements.input.style.display = 'block';
		 this.suggest=this.getSuggest();
     this.elements.input.focus();
			if(!this.navigator_location_shared && this.options.checkInOptions.tagParams == 0)
			this.getCurrentLocation();
			if(this.options.checkInOptions.tagParams != 0) {
				this.setLocation(this.options.checkInOptions.tagParams);
			}
    },
    getCurrentLocation: function() {
      var locationTimeLimit = 12000;
      var self = this;
      var locationTimeout = window.setTimeout(function() {
        try {
          self.navigator_location_shared=false;
          if(self.watchID)
            navigator.geolocation.clearWatch(self.watchID);
        } catch (e) {}
     
        var data = {
          'accuracy': 0,
          'latitude': 0,
          'longitude': 0,
          'label': '',
          'vicinity': ''
        };

        self.location=data;
      }, locationTimeLimit);

      try {
     
        self.watchID = navigator.geolocation.watchPosition(function(position) {
          self.navigator_location_shared = true;
          window.clearTimeout(locationTimeout);
          self.navigator_location_shared=true;
          var delimiter = (position.address && position.address.street != '' && position.address.city != '') ? ', ' : '';            
          var data = {
            'accuracy': position.coords.accuracy,
            'latitude': position.coords.latitude,
            'longitude': position.coords.longitude,
            'label': (position.address) ? (position.address.street + delimiter + position.address.city) : '',
            'vicinity': (position.address) ? (position.address.street + delimiter + position.address.city) : ''
          };        
          if(!position.address){
            data.vicinity=self.getAddress(position.coords);           
            self.location=data;
            self.suggest.setOptions({
              'postData': self.getLocation()
            });
          } else {
            if(!self.add_location)
              self.location=data;
              self.suggest.setOptions({
                'postData': self.getLocation()
              });
          }
        }, function(){
          self.getEmptySuggest();
        });
      //}
      } catch (e) {
        self.getEmptySuggest();
      }
    },
		getEmptySuggest : function(){
      if(this.call_empty_suggest)
        return;
      this.elements.input.focus();         
      if(this.suggest && this.suggest.element.value =='' ){        
        this.suggest.queryValue =' ';
        this.suggest.prefetch();
      }
     
      this.call_empty_suggest = true;     
    },
		getAddress :function(location){
      var self=this;
      var map = new google.maps.Map(new Element('div'), {
        mapTypeId: google.maps.MapTypeId.ROADMAP, 
        center: new google.maps.LatLng(location.latitude, location.longitude), 
        zoom: 15
      });
      var service = new google.maps.places.PlacesService(map);
      var request = {
        location: new google.maps.LatLng(location.latitude,location.longitude), 
        radius: 500
      };
      
      service.search(request, function(results, status) {
        if (status == 'OK') {
          self.location.vicinity=results[0].vicinity;
          self.suggest.setOptions({
            'postData': self.getLocation()
          });
          var index = 0;
          var radian = 3.141592653589793/180;
          var my_distance = 1000; 
          var R = 6371; // km
          for (var i = 0; i < results.length; i++){
            var lat2 = results[i].geometry.location.lat();
            var lon2 = results[i].geometry.location.lng(); 
            var dLat = (lat2-location.latitude) * radian;
            var dLon = (lon2-location.longitude) * radian;
            var lat1 = location.latitude * radian;
            lat2 = lat2 * radian;
            var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2); 
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
            var d = R * c;
          
            if(d < my_distance) {
              index = i;
              my_distance = d;
            }
          } 
					self.getEmptySuggest();
          return results[index].vicinity;
        }
      });
    },

	getSuggest: function() {

      if( !this.suggest ) { 
        var width = this.elements.input.getSize().x;
        this.suggestContener = new Element('div', {
          'class':'sitecheckin-autosuggest-contener',
          'styles' : {
            'width' : width+ 'px',
            'display':'none'
          }
        });
                
        this.choicesSliderArea = new Element('div', {
          'class':'sitecheckin-autosuggest'          
        });
      
        this.choices = new Element('ul', {
          'class':'tag-autosuggest seaocore-autosuggest sitetagcheckin-autosuggestlist-feed' ,
          'styles' : {
            'width' : width+ 'px' 
          }
        }).inject(this.choicesSliderArea);
        
        this.choicesSliderArea.inject(this.suggestContener);
        new Element('div', {
          'class':'clr'
        }).inject(this.suggestContener);
        this.suggestMap = new Element('div', {
          'class':'sitecheckin-autosuggest-map',
          'styles' : {
            'position':'relative'
          }
        }).inject(this.suggestContener);
        
        this.suggestContener.inject(this.elements.input, 'after');
        this.scroller=new SEAOMooVerticalScroll(this.choicesSliderArea, this.choices,{});
        var self = this;
        var options = $merge(this.options.tagsuggestOptions, {         
          'cache': false,
          'selectMode': 'pick',
          'postVar': 'suggest',
          'minLength':0,
          'className': 'searchbox_autosuggest',      
          'filterSubset' : true,         
          'tokenValueKey': 'label',
          'tokenFormat' : 'object',
          'customChoices' : this.choices,
          'postData': this.getLocation(),
          'indicatorClass':'checkin-loading',
					'maxChoices' : 25,
          'injectChoice' : function(token) {
            if(token.type !="just_use"){
              var choice = new Element('li', {
                'class': 'autocompleter-choices',
                'value': this.markQueryValue(token.label),
                'html': token.photo || '',
                'id': token.id
              });
              var divEl= new Element('div', {
                'html' : this.markQueryValue(token.label),
                'class' : 'autocompleter-choice'
              });         
              if(token.type !='place'){
                new Element('div', {
                  'html' : this.markQueryValue(token.category) +' &#8226; '+this.markQueryValue(token.vicinity)  
                }).inject(divEl);           
              }
              divEl.inject(choice);
              this.addChoiceEvents(choice).inject(this.choices);      
							choice.store('autocompleteChoice', token);      
            }else{
//               var choice = new Element('li', {
//                 'class': 'autocompleter-choices',
//                 'value': "text",            
//                 'id': "just_use_li"
//               });
//               var divEl= new Element('div', {
//                 'html' : this.markQueryValue(token.li_html),
//                 'class' : 'autocompleter-choice chekin_autosuggest_just_use'
//               });         
//             
//               divEl.inject(choice);
//               this.addChoiceEvents(choice).inject(this.choices);
//               choice.store('autocompleteJustUseChoice', true);
            }
            
            self.scroller.update();
            
          },
          'onShow': function() { 
            self.suggestContener.setStyles({
              'width': width,
              'display':"block"
            });         
            (function(){ 
              self.scroller.update();
            }).delay(500);
          },
          'onHide': function() {
            self.suggestContener.setStyles({              
              'display':"none"
            }); 
          },
          'onSelect': function(input, choice) {                      
//             if(choice.retrieve('autocompleteJustUseChoice', false)){
//               self.suggestMap.style.display="none";
//             }else{
              self.suggestMap.style.display="block";
              var data = choice.retrieve('autocompleteChoice'); 
              self.setMarker(data, choice);         
            //}
          },
          'onChoiceSelect' : function(choice) {           
            var data = choice.retrieve('autocompleteChoice');          
                     
            if (data.latitude == undefined) {
              var map = new google.maps.Map(new Element('div'), {
                mapTypeId: google.maps.MapTypeId.ROADMAP, 
                center: new google.maps.LatLng(0, 0), 
                zoom: 15
              });
              var service = new google.maps.places.PlacesService(map);
              service.getDetails({
                'placeId': data.place_id
              }, function(place, status) {
                if (status == 'OK') {
                  data.name = place.name;
                 // data.google_id = place.id;
                  data.latitude = place.geometry.location.lat();
                  data.longitude = place.geometry.location.lng();
                  data.vicinity = (place.vicinity) ? place.vicinity : place.formatted_address;
                  data.icon = place.icon;
                  data.types = place.types.join(',');
                  data.prefixadd=data.types.indexOf('establishment') > -1 ? en4.core.language.translate('at'):en4.core.language.translate('in');
                  choice.store('autocompleteChoice', data);
                }
              });
            }
            self.setLocation(data);
          },
          'emptyChoices' : function() {            
            this.fireEvent('onHide', [this.element, this.choices]);
          },
					'onBlur':  function(){
            var selfAuto=this;
             (function(){ 
              selfAuto.hideChoices(true);
            }).delay(500);
          },
          'onFocus' :  function(){
            if(self.call_empty_suggest && !self.add_location)
              this.prefetch.delay(this.options.delay + 50, this);
          }
        });
   
        this.suggest = new Autocompleter.Request.JSON(this.elements.input, this.options.tagsuggestOptions.url, options);
      }

      return this.suggest;
    },
    getLocation:function(){
      var location = {
        'latitude': 0, 
        'longitude': 0,
        'location_detected':''
      };

      if (this.isValidLocation(false, true)) {
        location.latitude = this.location.latitude;
        location.longitude = this.location.longitude;   
        location.location_detected=this.location.vicinity;
      }

      return location;
    },
    getLocationHTML:function(){     
			label = (this.location.type == 'place' && this.location.vicinity)?  ((this.location.name && this.location.name != location.vicinity) ?  this.location.vicinity : this.location.vicinity) : this.location.label;
      var content = en4.core.language.translate(this.location.prefixadd)+' '+'<a href="javascript:void(0)">'+label+'</a>';
      return content;
    },
    setLocation:function(location){
			var checkinStr='';
      this.location = location;
      if (this.isValidLocation(location)) {
        var checkin_hash = new Hash(location); 
				checkinStr= checkin_hash.toQueryString();
      }
      this.add_location=true;
			if(location.label != false) {
        this.elements.input.set('value',location.label);
			}
      var self=this;
			this.suggest.setOptions({
			'postData': this.getLocation()
			});
			
      this.elements.crosslocation = new Element('span', {        
				'id' : 'cross_tag_location',
				'class': 'tag',
				'style' : 'display:block',
				'html': (this.location.type == 'place' && this.location.vicinity)?  ((this.location.name && this.location.name != this.location.vicinity) ?  this.location.vicinity : this.location.vicinity) : this.location.label
      });
      
      this.elements.crosslink = new Element('a', {
        'href' : 'javascript:void(0);',   
				'html':'X',																		 
        'events' : {
           'click' : this.removeLocation.bind(this)
        }
      }).inject(this.elements.crosslocation);					
			
		 this.elements.crosslocation.inject(this.elements.input, "before");	 			
		 this.elements.input.style.display="none";	

		 $('checkinstr_status').value = checkinStr;
		 // checkinelement.inject($('seaocheckinform'));	
		 this.suggestContener.style.display= 'none';
    },
    removeLocation:function(){
      this.elements.crosslocation.destroy();
      delete this.elements.crosslocation;			

      this.add_location=false;
      this.elements.input.style.display='block';
      this.suggest.setOptions({
        'postData': this.getLocation()
      });
      this.location="";
      this.elements.input.value='';
			
      var self=this;       
      ( function(){
        self.elements.input.focus();
      }).delay(100);
    },
    isValidLocation:function(location, checkin_params) {
      var location = (location) ? location : this.location;  
      return  (checkin_params)
      ? (location && location.latitude && this.location.longitude)
      : (location && location.label != undefined && location.label != '');
    },
    setMarker: function(checkin, choice) {
      var self = this;
      if (checkin.latitude == undefined) {
        var map = new google.maps.Map(new Element('div'), {
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          center: new google.maps.LatLng(0, 0), 
          zoom: 15
        });
        var service = new google.maps.places.PlacesService(map);
        service.getDetails({
           'placeId': checkin.place_id
        }, function(place, status) {
          if (status == 'OK') {
           // checkin.google_id = place.id;
            checkin.name = place.name;
            checkin.vicinity = (place.vicinity) ? place.vicinity : place.formatted_address;
            checkin.latitude = place.geometry.location.lat();
            checkin.longitude = place.geometry.location.lng();
            checkin.icon = place.icon;
            checkin.types = place.types.join(',');
            checkin.prefixadd=checkin.types.indexOf('establishment') > -1 ? en4.core.language.translate('at'):en4.core.language.translate('in');
            choice.store('autocompleteChoice', checkin);
            self.setMarker(checkin, choice);
          }
        });
        return;
      }

      var myLatlng = new google.maps.LatLng(checkin.latitude, checkin.longitude);
      var new_map = false;
      if (this.map == undefined || !this.suggestMap.getFirst()) {
        new_map = true;
        this.map = new google.maps.Map(this.suggestMap, {
          navigationControl: false,
          mapTypeControl: false,
          scaleControl: false,
          draggable: false,
          streetViewControl:false,
          zoomControl: false,
          mapTypeId: google.maps.MapTypeId.ROADMAP, 
          center: myLatlng, 
          zoom: 15
        });
      }

      if (new_map) {
        this.marker = new google.maps.Marker({
          position: myLatlng, 
          map: this.map
        });
        this.map.setCenter(myLatlng);
      } else {
        this.marker = (this.marker == undefined) ? new google.maps.Marker({
          position: myLatlng, 
          map: this.map
        }) : this.marker;
        this.marker.setPosition(myLatlng);
        this.map.panTo(myLatlng);
      }
    }
});
