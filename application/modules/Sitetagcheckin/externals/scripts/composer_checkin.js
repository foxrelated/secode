
/* $Id: composer_photo.js 9572 2011-12-27 23:41:06Z john $ */



(function() { // START NAMESPACE
  var $ = 'id' in document ? document.id : window.$;



  ComposerCheckin.Plugin.STCheckin = new Class({

    Extends : ComposerCheckin.Plugin.Interface,

    name : 'checkin',

    options : {
      title : en4.core.language.translate('Share Location'),
      lang : {}
    },
    location:'',
    call_empty_suggest : false,
    add_location:false,
    navigator_location_shared :false,
    initialize : function(options) {
      this.elements = new Hash(this.elements);
      this.params = new Hash(this.params);
      this.parent(options);
    },
    
    attach : function() {     
      if(!this.elements.link){
        var composer = this.getComposer(); 

        var addLinkAfter = composer.getActivatorContent().getElement(".aaf_activaor_end");
        if(composer.getActivatorContent().getElement(".adv_post_add_user"))
          addLinkAfter = composer.getActivatorContent().getElement(".adv_post_add_user");
        this.elements.link = new Element('span', {
          'id' : 'compose-' + this.getName() + '-activator',
          'class' : 'adv-post-checkin',         
          'events' : {
            'click' : this.checkinToggle.bind(this)
          }
        }).inject(addLinkAfter,"after");
        
        create_tooltip(this).inject(this.elements.link);
        var composer_tray = composer.getTray();
        this.elements.container = new Element('div', {
          'id' : 'compose-' + this.getName() + '-container-checkin',
          'class' : 'adv_post_container_checkin dnone',
          'title':this._lang('Where are you?'),
          'style':{
            dispaly:'none'
          }
        });
        this.elements.contentdisplay = new Element('div', {
          'id' : 'compose-' + this.getName() + '-container-display',
          'class' : '',
          'title':this._lang('Where are you?')
        }).inject(this.elements.container);
        this.elements.input =  new Element('input', {
          'type':'text',          
          'id' : 'compose-' + this.getName(),
          'name':'compose-' + this.getName(),
          'class' : 'compose-textarea'        
        }).inject(this.elements.container);
        
        this.elements.container.inject(composer_tray,"before");
        
      
        this.elements.overText = new ComposerCheckin.OverText(this.elements.input, {
          textOverride : this._lang('Where are you?'),
          'element' : 'label',
          'isPlainText' : true,
          'positionOptions' : {
            position: ( en4.orientation  ==  'rtl' ? 'upperRight' : 'upperLeft' ),
            edge: ( en4.orientation  ==  'rtl' ? 'upperRight' : 'upperLeft' ),
            offset: {
              x: ( en4.orientation  ==  'rtl' ? -4 : 4 ),
              y: 2
            }
          }
        });
        this.elements.comnposerTrydisplay = new Element('span', {
          'id' : 'compose-' + this.getName() + '-composer-display'
        // 'class' : 'adv_post_container_tagged_cont'         
        }).inject(composer.elements.body.getParent().getParent().getLast('div').getLast('span'),'before');
        // Submit
        composer.addEvent('editorSubmit', this.submit.bind(this));
        this.suggest = this.getSuggest(); 
        // After Submit
        composer.addEvent('editorSubmitAfter', this.submitAfter.bind(this));
        composer.addEvent('editorReset', this.resetcontent.bind(this));
      }
         
      return this;
    },

    detach : function() {
      //   this.parent();
      return this;
    },
    activate: $empty,

    deactivate : $empty,

    poll : function() {
    
    },
    resetcontent : function(){
      if(this.elements.container && this.elements.container.hasClass("dblock")){
        this.elements.container.removeClass("dblock").addClass("dnone"); 
      }
      if (this.add_location)
        this.removeLocation();
    },
    checkinToggle : function(){
      this.elements.container.toggleClass('dnone');
      this.elements.container.toggleClass('dblock');
      
      if(this.elements.container.hasClass("dblock")){
        if(!this.navigator_location_shared){
          this.getCurrentLocation();
        }
        this.getComposer().focus();
        var self = this;       
        if(!this.elements.locationspan){
          ( function(){
            self.elements.input.focus();
          }).delay(100);
        }       
      }
     
    },
    getCurrentLocation : function() {
      var locationTimeLimit = 12000;
     
      var self = this;
      var locationTimeout = window.setTimeout(function() {
        try {
          self.navigator_location_shared = false;
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

        self.location = data
      }, locationTimeLimit);

      try {
     
        self.watchID = navigator.geolocation.watchPosition(function(position) {
          self.navigator_location_shared = true;
          window.clearTimeout(locationTimeout);
           
          self.navigator_location_shared = true;
          var delimiter = (position.address && position.address.street !=  '' && position.address.city !=  '') ? ', ' : '';            
          var data = {
            'accuracy': position.coords.accuracy,
            'latitude': position.coords.latitude,
            'longitude': position.coords.longitude,
            'label': (position.address) ? (position.address.street + delimiter + position.address.city) : '',
            'vicinity': (position.address) ? (position.address.street + delimiter + position.address.city) : ''
          };        
          if(!position.address){
            data.vicinity = self.getAddress(position.coords);           
            self.location = data;
            self.suggest.setOptions({
              'postData': self.getLocation()
            });
          } else {
            if(!self.add_location)
              self.location = data;
            self.suggest.setOptions({
              'postData': self.getLocation()
            });
            self.getEmptySuggest();
          }
                
          
        }, function(){
          self.getEmptySuggest();
        });
      //}
      } catch (e) {
        self.getEmptySuggest();
      }
    },
    getAddress : function(location){
      var self = this;
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
        if (status  ==  'OK') {
          self.location.vicinity = results[0].vicinity;
          
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
    getSuggest : function() {
      if( !this.suggest ) { 
        var width = (this.getComposer().elements.body.getSize().x - 4);
        if(width < 0)
          width = 0;
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
        this.scroller = new SEAOMooVerticalScroll(this.choicesSliderArea, this.choices,{});
        var self = this;
        var options = $merge(this.options.suggestOptions, {         
          'cache': false,
          'selectMode': 'pick',
          'postVar': 'suggest',
          'minLength':0,
          'className': 'searchbox_autosuggest',
          //    'autocompleteType': 'tag',
          //    'multiple': false,        
          'filterSubset' : true,         
          'tokenValueKey': 'label',
          'tokenFormat' : 'object',
          'customChoices' : this.choices,
          'maxChoices': 25,
          'postData': this.getLocation(),
          'indicatorClass':'checkin-loading',
          'injectChoice' : function(token) {
            if(token.type != "just_use"){
              var choice = new Element('li', {
                'class': 'autocompleter-choices',
                'value': this.markQueryValue(token.label),
                'html': token.photo || '',
                'id': token.id
              });
              var divEl =  new Element('div', {
                'html' : this.markQueryValue(token.label),
                'class' : 'autocompleter-choice'
              });         
              if(token.type != 'place'){
                new Element('div', {
                  'html' : this.markQueryValue(token.category) +' &#8226; '+this.markQueryValue(token.vicinity)  
                }).inject(divEl);           
              }
              divEl.inject(choice);
              this.addChoiceEvents(choice).inject(this.choices);                 
            }else{
              var choice = new Element('li', {
                'class': 'autocompleter-choices',
                'value': "text", 
                'html': token.photo || '',
                'id': "just_use_li"
              });
              var divEl =  new Element('div', {
                'html' : this.markQueryValue(token.li_html),
                'class' : 'autocompleter-choice chekin_autosuggest_just_use'
              });         
            
              divEl.inject(choice);
              this.addChoiceEvents(choice).inject(this.choices);
              choice.store('autocompleteJustUseChoice', true);
            }
            choice.store('autocompleteChoice', token);      
            self.scroller.update();
            
          },
          'onShow': function() { 
            if(self.add_location || self.elements.container.hasClass("dnone")){
              this.hideChoices(true);
              return;
            }            
            self.suggestContener.setStyles({
              'width': (self.getComposer().elements.body.getSize().x -8 ),
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
            if(choice.retrieve('autocompleteJustUseChoice', false)){
              self.suggestMap.style.display = "none";
            }else{
              self.suggestMap.style.display = "block";
              var data = choice.retrieve('autocompleteChoice'); 
              self.setMarker(data, choice);         
            }         
          },
          'onChoiceSelect' : function(choice) {           
            var data = choice.retrieve('autocompleteChoice');          
                  
            if (data.type  ==  'place') {
              var map = new google.maps.Map(new Element('div'), {
                mapTypeId: google.maps.MapTypeId.ROADMAP, 
                center: new google.maps.LatLng(0, 0), 
                zoom: 15
              });
              var service = new google.maps.places.PlacesService(map);
              service.getDetails({
                'placeId': data.place_id
              }, function(place, status) {
                if (status  ==  'OK') {
                  data.name = place.name;
                 // data.google_id = place.id;
                  data.latitude = place.geometry.location.lat();
                  data.longitude = place.geometry.location.lng();
                  data.vicinity = (place.vicinity) ? place.vicinity : place.formatted_address;
                  data.icon = place.icon;
                  data.types = place.types.join(',');
                  data.prefixadd = data.types.indexOf('establishment') > -1 ? self._lang('at'):self._lang('in');
                  choice.store('autocompleteChoice', data);
                // self.toggleLoader(false);
                   self.location = data;
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
             // selfAuto.hideChoices(true);
            }).delay(500);
          },
          'onFocus' :  function(){
            if(self.call_empty_suggest && !self.add_location)
              this.prefetch.delay(this.options.delay + 50, this);
          }
        });
      
        this.suggest = new Autocompleter.Request.JSON(this.elements.input, this.options.suggestOptions.url, options);
               
      }

      return this.suggest;
    },
    addJustUseLi : function(){
      
    },
    getLocation : function(){
      var location = {
        'latitude': 0, 
        'longitude': 0,
        'location_detected':''
      };

      if (this.isValidLocation(false, true)) {
        location.latitude = this.location.latitude;
        location.longitude = this.location.longitude;   
        location.location_detected = (this.location.vicinity) ? this.location.vicinity:this.location.label;
      }

      return location;
    },
    getLocationHTML : function(){     
      var location =this.location;
      var content  = en4.core.language.translate(this.location.prefixadd)+' '+'<a href = "javascript:void(0)">'+((location.type == 'place' && location.vicinity)? ((location.name && location.name != location.vicinity) ? location.name +', '+ location.vicinity : location.vicinity) : location.label)+'</a>';
      return content;
    },
    setLocation : function(location){
      this.location = location;
      if (this.isValidLocation(location)) {
        var checkin_hash = new Hash(location); 
      }
      this.add_location = true;
      this.elements.contentdisplay.empty();
      var content  = this.getComposer().elements.body.getParent().getParent().getLast('div');
      content.getFirst('span').innerHTML = ' &mdash;';
      content.getLast('span').innerHTML = '.';
      // this.elements.input.set('value',location.label);
      this.elements.input.style.display = 'none';
      this.elements.input.value ='';
      this.elements.overText.hide();
      this.getComposer().focus();
      var self = this;
      this.elements.locationspan = new Element('span', {        
        'class' : 'tag',
        'html': (location.type == 'place' && location.vicinity)?  ((location.name && location.name != location.vicinity) ? location.name +', '+ location.vicinity : location.vicinity) : location.label
      });
        
      this.elements.link = new Element('a', {
        'html':'X',
        'href' : 'javascript:void(0);',         
        'events' : {
          'click' : self.removeLocation.bind(this)
        }
      }).inject(this.elements.locationspan);
      this.elements.comnposerTrydisplay.innerHTML = this.getLocationHTML();
      this.suggest.setOptions({
        'postData': this.getLocation()
      });
      this.elements.locationspan.inject(this.elements.contentdisplay);
    },
    removeLocation : function(){
      this.elements.locationspan.destroy();
      delete this.elements.locationspan;
      this.elements.contentdisplay.empty();
      this.elements.comnposerTrydisplay.empty();
      var content  = this.getComposer().elements.body.getParent().getParent().getLast('div');
      var removeContent = true;     
      content.getElements('span').each(function(el){
        if(el.get('class') !=  'aaf_mdash' && el.get('class') !=  'aaf_dot'  && el.innerHTML != ''){
          removeContent = false;          
        }
      });
      if(removeContent){
        content.getLast('span').empty();
        content.getFirst('span').empty();
      }
      this.add_location = false;
      this.elements.input.style.display = 'block';
      this.navigator_location_shared = false;
      
      this.suggest.setOptions({
        'postData': this.getLocation()
      });
      this.location = "";
      this.elements.input.value = '';
      var self = this;       
      ( function(){
        if(self.elements.container.hasClass("dblock"))
        self.elements.input.focus();
      }).delay(100);
        
    },
    isValidLocation : function(location, checkin_params) {
      location = (location) ? location : this.location;  
      return  (checkin_params)
      ? (location && location.latitude && this.location.longitude)
      : (location && location.label !=  undefined && location.label !=  '');
    },
    submit : function(){
      var checkinStr = '';
      if (this.add_location && this.isValidLocation()) {
        var checkinHash = new Hash(this.location);
        checkinStr =  checkinHash.toQueryString();
        if(this.options.allowEmpty)
        this.getComposer().options.allowEmptyWithoutAttachment = true;
      } 
      this.makeFormInputs({
        checkin: checkinStr
      });           
    },
    submitAfter : function(){
      if(this.elements.container && this.elements.container.hasClass("dblock")){
        this.elements.container.removeClass("dblock").addClass("dnone") 
      }
      if (this.add_location)
        this.removeLocation();
      this.getComposer().options.allowEmptyWithoutAttachment = false;
    },
    setMarker : function(checkin, choice) {
      var self = this;
      // var map = this.suggestMap;
    
      if (checkin.latitude  ==  undefined) {
        var map = new google.maps.Map(new Element('div'), {
          mapTypeId: google.maps.MapTypeId.ROADMAP, 
          center: new google.maps.LatLng(0, 0), 
          zoom: 15
        });
        var service = new google.maps.places.PlacesService(map);
        service.getDetails({
           'placeId': checkin.place_id
        }, function(place, status) {
          if (status  ==  'OK') {
            //checkin.google_id = place.id;
            checkin.name = place.name;
            checkin.vicinity = (place.vicinity) ? place.vicinity : place.formatted_address;
            checkin.latitude = place.geometry.location.lat();
            checkin.longitude = place.geometry.location.lng();
            checkin.icon = place.icon;
            checkin.types = place.types.join(',');
            checkin.prefixadd = checkin.types.indexOf('establishment') > -1 ? self._lang('at'):self._lang('in');
            choice.store('autocompleteChoice', checkin);
            self.setMarker(checkin, choice);
          }
        });

        return;
      }

      var myLatlng = new google.maps.LatLng(checkin.latitude, checkin.longitude);
      var new_map = false;
      if (this.map  ==  undefined || !this.suggestMap.getFirst()) {
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

    },
    makeFormInputs : function(data) {    
      $H(data).each(function(value, key) {
        this.setFormInputValue(key, value);
      }.bind(this));
    },
    // make chekin hidden input and set value into composer form
    setFormInputValue : function(key, value) {
      var elName = 'aafComposerForm' + key.capitalize();
      var composerObj = this.getComposer();
      if(composerObj.elements.has(elName)) 
        composerObj.elements.get(elName).destroy();    
      composerObj.elements.set(elName, new Element('input', {
        'type' : 'hidden',
        'name' : 'composer[' + key + ']',
        'value' : value || ''
      }).inject(composerObj.getInputArea()));   
      composerObj.elements.get(elName).value = value;
    }    

  });



})(); // END NAMESPACE
