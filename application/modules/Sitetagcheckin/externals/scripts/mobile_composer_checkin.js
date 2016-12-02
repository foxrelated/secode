
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
    add_location:false,
    initialize : function(options) {
      this.elements = new Hash(this.elements);
      this.params = new Hash(this.params);
      this.parent(options);
    },
    
    attach : function() {
      var self = this;
      if(!this.elements.stchekinlink){
        var composer=this.getComposer(); 
        var addLinkBefore=composer.getMenu().getElement(".aaf_cm_sep");
        this.elements.stchekinlink = new Element('a', {
          'id' : 'compose-' + this.getName() + '-activator',
          'class' : 'compose-activator buttonlink aaf_st_enable',
          'href' : 'javascript:void(0);',
          'html' : this._lang(this.options.title),
          'events' : {
            'click' : this.toggleEvent.bind(this)
          }
        }).inject(addLinkBefore,'before');         
        
        //        if(this.options.post_chekin !=0 ){        
        //          this.setLocation(this.options.post_chekin);
        //        }
        
        var width = composer.elements.body.getSize().x;
        this.elements.stchekinsuggestContainer= new Element('div', {
          'class' :'stchekin_suggest_container dnone',
          'styles' : {
            'width' : (width-10 )+ 'px'
          }
        }).inject(this.elements.stchekinlink,'after');  
        this.elements.stchekinsuggestContainerSearchDiv= new Element('div', {          
          }).inject(this.elements.stchekinsuggestContainer);  
        
        new Element('p', {
          'class':'label',
          'html' : this._lang('Enter the location:')
        }).inject(this.elements.stchekinsuggestContainerSearchDiv);
        
        this.elements.stchekinsearchText= new Element('input', {
          'type':'text',
          'id' :'aff_mobile_aft_search_stch',
          'autocomplete' : 'off'
        }).inject(this.elements.stchekinsuggestContainerSearchDiv);
        
        new Element('button', {
          'class' :'',
          'type': 'button',
          'html' : this._lang('Search'),
          'events' : {
            'click' : this.search.bind(this)
          }
        }).inject(this.elements.stchekinsuggestContainerSearchDiv);
        
        new Element('a', {        
          'class':'aaf-add-friend-close',
          'href' : 'javascript:void(0);',
          'html' : this._lang('Close'),
          'events' : {
            'click' : this.toggleEvent.bind(this)
          }
        }).inject(this.elements.stchekinsuggestContainerSearchDiv);
        
        this.elements.stchekinsuggestContainerSearchListDiv= new Element('div', {          
          }).inject(this.elements.stchekinsuggestContainer); 
        
        this.elements.stchekinloading = new Element('div', {
          'class' :'add_friend_suggest_container_loading',
          'html' : '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" style="margin-top:10px;" />'    
        });
        
        // Submit
        composer.addEvent('editorSubmit', this.submit.bind(this));
       
      }
         
      
      return this;
    },

    detach : function() {
      //   this.parent();
      return this;
    },
    toggleEvent :function() {
      $$(".add_friend_suggest_container").each(function(el){
        if(el.hasClass("aaf_enable")){
          el.toggleClass('aaf_disable');
          el.toggleClass('aaf_enable'); 
        }
      });
      this.elements.stchekinsuggestContainer.toggleClass('dnone');
      this.elements.stchekinsuggestContainer.toggleClass('dblock');
      
      if(this.elements.stchekinsuggestContainer.hasClass("dblock")){
       
        if(this.elements.stchekinsearchText)
          this.elements.stchekinsearchText.value = ''; 
      }
    },
    loading :function(){
      this.elements.stchekinsuggestContainerSearchListDiv.empty();     
      this.elements.stchekinloading.inject(this.elements.stchekinsuggestContainerSearchListDiv); 
    },
    search : function(){   
      if(this.elements.stchekinsearchText.value =='')
        return;
      this.getLocation({      
        'suggest': this.elements.stchekinsearchText.value
      });
    },
    getLocation :function(params){
      var self=this;      
      this.loading();
     
      var req = new Request.JSON({
        url : self.options.suggestOptions.url,
        data :$merge(params, {
          'format' : 'json'         
        })
      }).addEvent('onComplete', self.queryResponse.bind(self));
      en4.core.request.send(req);
    },
    queryResponse: function(response) {
     
      this.elements.stchekinsuggestContainerSearchListDiv.empty();     
      this.choices = new Element('ul', {
        'class':'aaf-mobile-aad-tag-autosuggest'
        
      }).inject(this.elements.stchekinsuggestContainerSearchListDiv);
      response.each(this.injectChoice ,this);
    },
    injectChoice : function(token){
      
      //  if(token.type != "just_use"){
      var choice = new Element('li', {
        'class': 'autocompleter-choices',
        'value': this.markQueryValue(token.label),         
        'id': token.id
      });
      if(token.type != "just_use"){
        var divEl =  new Element('div', {
          'html' : this.markQueryValue(token.label),
          'class' : 'autocompleter-choice'
        }); 
      } else {
        var divEl =  new Element('div', {
          'html' : this.markQueryValue(token.li_html),
          'class' : 'autocompleter-choice chekin_autosuggest_just_use'
        });         
      }        
      if(token.type != 'place' && token.type != "just_use"){
        new Element('div', {
          'html' : this.markQueryValue(token.category) +' &#8226; '+this.markQueryValue(token.vicinity)
        }).inject(divEl);           
      }
      divEl.inject(choice);
      this.addChoiceEvents(choice).inject(this.choices); 
      choice.store('autocompleteChoice', token); 
    //  }      
    },
    /**
     * markQueryValue
     *
     * Marks the queried word in the given string with <span class="autocompleter-queried">*</span>
     * Call this i.e. from your custom parseChoices, same for addChoiceEvents
     *
     * @param		{String} Text
     * @return		{String} Text
     */
    markQueryValue: function(str) {
      return (!this.options.markQuery || !this.queryValue) ? str
      : str.replace(new RegExp('(' + ((this.options.filterSubset) ? '' : '^') + this.queryValue.escapeRegExp() + ')', (this.options.filterCase) ? '' : 'i'), '<span class="autocompleter-queried">$1</span>');
    },
    /**
     * addChoiceEvents
     *
     * Appends the needed event handlers for a choice-entry to the given element.
     *
     * @param		{Element} Choice entry
     * @return		{Element} Choice entry
     */
    addChoiceEvents: function(el) {
      return el.addEvents({
        // 'mouseover': this.choiceOver.bind(this, el),
        'click': this.choiceSelect.bind(this, el)
      });
    },
    choiceSelect: function(choice) {
      var data = choice.retrieve('autocompleteChoice'); 
      this.setLocation(data);
      this.elements.stchekinsuggestContainerSearchListDiv.empty();
      this.toggleEvent();
    },
    setLocation :function(location){
      this.add_location=true;
      this.location=location;
      var self=this;  
      this.elements.stchekinlocationdiv = new Element('div', {
        'class':'aaf-add-friend-tagcontainer'
      });
      new Element('span', {        
        'class' : 'aff-tag-with',
        'html':this._lang('at')+':'
      }).inject(this.elements.stchekinlocationdiv);
      this.elements.stchekinlocationspan = new Element('span', {        
        'class' : 'tag',
        'html': (location.type == 'place' && location.vicinity)?  ((location.name && location.name != location.vicinity) ?  location.vicinity : location.vicinity) : location.label
      });
        
      this.elements.stchekinremovelink = new Element('a', {
        'html':'X',
        'href' : 'javascript:void(0);',         
        'events' : {
          'click' : self.removeLocation.bind(this)
        }
      }).inject(this.elements.stchekinlocationspan);
      
      this.elements.stchekinlocationspan.inject(this.elements.stchekinlocationdiv);
      this.elements.stchekinlocationdiv.inject(this.getComposer().getMenu(),'before');
      
      if(this.elements.stchekinlink.hasClass("aaf_st_enable")){
        this.elements.stchekinlink.removeClass("aaf_st_enable").addClass("aaf_st_disable"); 
      }
      
      
      //
      if (location.type == 'place') {
        (function(){    
          var map = new google.maps.Map(new Element('div'), {
            mapTypeId: google.maps.MapTypeId.ROADMAP, 
            center: new google.maps.LatLng(0, 0), 
            zoom: 15
          });
          var service = new google.maps.places.PlacesService(map);
        
          service.getDetails({
            'placeId': location.place_id
          }, function(place, status) {
            if (status == 'OK') {           
            //  location.google_id = place.id;
              location.name = place.name;
              location.vicinity = (place.vicinity) ? place.vicinity : place.formatted_address;
              location.latitude = place.geometry.location.lat();
              location.longitude = place.geometry.location.lng();
              location.icon = place.icon;
              location.types = place.types.join(',');
              location.prefixadd=location.types.indexOf('establishment') > -1 ? en4.core.language.translate('at'):en4.core.language.translate('in');
              self.location=location;
            }
          });
        }).delay(2000); 
      }
    //
    },
    removeLocation : function(){
      this.elements.stchekinlocationdiv.destroy();
      this.add_location=false;
      this.location="";
      if(this.elements.stchekinlink.hasClass("aaf_st_disable")){
        this.elements.stchekinlink.removeClass("aaf_st_disable").addClass("aaf_st_enable"); 
      }
    },
    submit:function(){
      var checkinStr='';
      if (this.add_location) {
        var checkinHash = new Hash(this.location);
        checkinStr= checkinHash.toQueryString();
        if(this.options.allowEmpty)
          this.getComposer().options.allowEmptyWithoutAttachment = true;
      } 
      this.makeFormInputs({
        checkin: checkinStr
      });           
    },
 
    makeFormInputs : function(data) {    
      $H(data).each(function(value, key) {
        this.setFormInputValue(key, value);
      }.bind(this));
    },
    // make tag hidden input and set value into composer form
    setFormInputValue : function(key, value) {
      var elName = 'aafComposerForm' + key.capitalize();
      var composerObj=this.getComposer();
      if( !composerObj.elements.has(elName) ) {     
        composerObj.elements.set(elName, new Element('input', {
          'type' : 'hidden',
          'name' : 'composer[' + key + ']',
          'value' : value || ''
        }).inject(composerObj.getInputArea()));
      }
      composerObj.elements.get(elName).value = value;
    }    

  });



})(); // END NAMESPACE
