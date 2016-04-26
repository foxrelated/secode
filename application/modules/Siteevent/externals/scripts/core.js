/* $Id: core.js 6590 2014-01-02 00:00:00Z SocialEngineAddOns Copyright 2013-2014 BigStep Technologies Pvt. Ltd. $ */

var tab_content_id_sitestore = 0;
var eventMemberTipInnerHTML = '';
var eventMemberTipNotGoingInnerHTML = '';
var eventMemberTipMayBeGoingInnerHTML = '';
en4.siteevent = {
  maps: [],
  infowindow: [],
  markers: []
};

en4.siteevent.ajaxTab = {
  click_elment_id: '',
  attachEvent: function(widget_id, params) {
    params.requestParams.content_id = widget_id;
    var element;

    $$('.tab_' + widget_id).each(function(el) {
      if (el.get('tag') == 'li') {
        element = el;
        return;
      }
    });
    var onloadAdd = true;
    if (element) {
      if (element.retrieve('addClickEvent', false))
        return;
      element.addEvent('click', function() {
        if (en4.siteevent.ajaxTab.click_elment_id == widget_id)
          return;
        en4.siteevent.ajaxTab.click_elment_id = widget_id;
        en4.siteevent.ajaxTab.sendReq(params);
      });
      element.store('addClickEvent', true);
      var attachOnLoadEvent = false;
      if (tab_content_id_sitestore == widget_id) {
        attachOnLoadEvent = true;
      } else {
        $$('.tabs_parent').each(function(element) {
          var addActiveTab = true;
          element.getElements('ul > li').each(function(el) {
            if (el.hasClass('active')) {
              addActiveTab = false;
              return;
            }
          });
          element.getElementById('main_tabs').getElements('li:first-child').each(function(el) { 
            if(el.getParent('div') && el.getParent('div').hasClass('tab_pulldown_contents')) 
              return;
            el.get('class').split(' ').each(function(className) {
              className = className.trim();
              if (className.match(/^tab_[0-9]+$/) && className == "tab_" + widget_id) {
                attachOnLoadEvent = true;
                if (addActiveTab || tab_content_id_sitestore == widget_id) {
                  element.getElementById('main_tabs').getElements('ul > li').removeClass('active');
                  el.addClass('active');
                  element.getParent().getChildren('div.' + className).setStyle('display', null);
                }
                return;
              }
            });
          });
        });
      }
      if (!attachOnLoadEvent)
        return;
      onloadAdd = false;

    }

    en4.core.runonce.add(function() {
      if (onloadAdd)
        params.requestParams.onloadAdd = true;
      en4.siteevent.ajaxTab.click_elment_id = widget_id;
      en4.siteevent.ajaxTab.sendReq(params);
    });


  },
  sendReq: function(params) {
    params.responseContainer.each(function(element) {   
      if((typeof params.loading) == 'undefined' || params.loading==true){
       element.empty();
      new Element('div', {
        'class': 'siteevent_profile_loading_image'
      }).inject(element);
      }
    });
    var url = en4.core.baseUrl + 'widget';

    if (params.requestUrl)
      url = params.requestUrl;

    var request = new Request.HTML({
      url: url,
      data: $merge(params.requestParams, {
        format: 'html',
        subject: en4.core.subject.guid,
        is_ajax_load: true
      }),
      evalScripts: true,
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        params.responseContainer.each(function(container) {
          container.empty();
          Elements.from(responseHTML).inject(container);
          en4.core.runonce.trigger();
          Smoothbox.bind(container);
        });

      }
    });
    request.send();
  }
};

/*
 ---
 
 script: Fx.Scroll.Carousel.js
 
 description: Extends Fx.Scroll to work like a carousel
 
 license: MIT-style license.
 
 authors: Ryan Florence
 
 docs: http://moodocs.net/rpflo/mootools-rpflo/Fx.Scroll.Carousel
 
 requires:
 - more/1.2.4.2: [Fx.Scroll]
 
 provides: [Fx.Scroll.Carousel]
 
 ...
 */


Fx.Scroll.Carousel = new Class({
  Extends: Fx.Scroll,
  options: {
    mode: 'horizontal',
    childSelector: false,
    loopOnScrollEnd: true,
    noOfItemPerPage: 4,
    noOfItemScroll: 1,
    navs: {
      frwd: 'siteevent_crousal_frwd',
      prev: 'siteevent_crousal_prev'
    }
  },
  initialize: function(element, options) {
    this.parent(element, options);
    this.cacheElements();
    this.currentIndex = 0;
    this.resetScroll();
    var self = this;
    $(this.options.navs.frwd).addEvent('click', function() {
      self.toNext();
      self.resetScroll();
    });

    $(this.options.navs.prev).addEvent('click', function() {
      self.toPrevious();
      self.resetScroll();
    });
  },
  cacheElements: function() {
    var cs = this.options.childSelector;
    if (cs) {
      els = this.element.getElements(cs);
    } else if (this.options.mode == 'horizontal') {
      els = this.element.getElements('.ltItem');
    } else {
      els = this.element.getChildren();
    }
    this.elements = els;

    return this;
  },
  toNext: function() {
    if (this.checkLink())
      return this;
    this.currentIndex = this.getNextIndex();
    this.toElement(this.elements[this.currentIndex]);
    this.fireEvent('next');
    return this;
  },
  toPrevious: function() {
    if (this.checkLink())
      return this;
    this.currentIndex = this.getPreviousIndex();
    this.toElement(this.elements[this.currentIndex]);
    this.fireEvent('previous');
    return this;
  },
  getNextIndex: function() {
    //this.currentIndex++;
    this.currentIndex = this.currentIndex + this.options.noOfItemScroll;
    if (this.currentIndex == this.elements.length || this.checkScroll()) {
      this.fireEvent('loop');
      this.fireEvent('nextLoop');
      return 0;
    } else {
      return this.currentIndex;
    }
  },
  getPreviousIndex: function() {
    //this.currentIndex--;
    this.currentIndex = this.currentIndex - this.options.noOfItemScroll;
    var check = this.checkScroll();
    if (this.currentIndex < 0 || check) {
      this.fireEvent('loop');
      this.fireEvent('previousLoop');
      return (check) ? this.getOffsetIndex() : this.elements.length - 1;
    } else {
      return this.currentIndex;
    }
  },
  getOffsetIndex: function() {
    var visible = (this.options.mode == 'horizontal') ?
            this.element.getStyle('width').toInt() / this.elements[0].getStyle('width').toInt() :
            this.element.getStyle('height').toInt() / this.elements[0].getStyle('height').toInt();
    return this.currentIndex + 1 - visible;
  },
  checkLink: function() {
    return (this.timer && this.options.link == 'ignore');
  },
  checkScroll: function() {
    if (!this.options.loopOnScrollEnd)
      return false;
    if (this.options.mode == 'horizontal') {
      var scroll = this.element.getScroll().x;
      var total = this.element.getScrollSize().x - this.element.getSize().x;
    } else {
      var scroll = this.element.getScroll().y;
      var total = this.element.getScrollSize().y - this.element.getSize().y;
    }
    return (scroll == total);
  },
  getCurrent: function() {
    return this.elements[this.currentIndex];
  },
  resetScroll: function() {
    if (this.elements.length <= this.options.noOfItemPerPage) {
      $(this.options.navs.frwd).style.visibility = 'hidden';
      $(this.options.navs.prev).style.visibility = 'hidden';
    } else {
      var visibleflag = 'visible';
      if (this.currentIndex == 0 || this.elements.length <= this.options.noOfItemPerPage) {
        visibleflag = 'hidden';
      }
      $(this.options.navs.prev).style.visibility = visibleflag;
      visibleflag = 'visible';
      if (((this.currentIndex + this.options.noOfItemPerPage) >= this.elements.length)) {
        visibleflag = 'hidden';
      }
      $(this.options.navs.frwd).style.visibility = visibleflag;
    }
  }

});

/**
 * @description dropdown Navigation
 * @param {String} id id of ul element with navigation lists
 * @param {Object} settings object with settings
 */


var NavigationSiteevent = function() {
  var main = {
    obj_nav: $(arguments[0]) || $("nav"),
    settings: {
      show_delay: 0,
      hide_delay: 0,
      _ie6: /MSIE 6.+Win/.test(navigator.userAgent),
      _ie7: /MSIE 7.+Win/.test(navigator.userAgent)
    },
    init: function(obj, level) {
      obj.lists = obj.getChildren();
      obj.lists.each(function(el, ind) {
        main.handlNavElement(el);
        if ((main.settings._ie6 || main.settings._ie7) && level) {
          main.ieFixZIndex(el, ind, obj.lists.size());
        }
      });
      if (main.settings._ie6 && !level) {
        document.execCommand("BackgroundImageCache", false, true);
      }
    },
    handlNavElement: function(list) {
      if (list !== undefined) {
        list.onmouseover = function() {
          main.fireNavEvent(this, true);
        };
        list.onmouseout = function() {
          main.fireNavEvent(this, false);
        };
        if (list.getElement("ul")) {
          main.init(list.getElement("ul"), true);
        }
      }
    },
    ieFixZIndex: function(el, i, l) {
      if (el.tagName.toString().toLowerCase().indexOf("iframe") == -1) {
        el.style.zIndex = l - i;
      } else {
        el.onmouseover = "null";
        el.onmouseout = "null";
      }
    },
    fireNavEvent: function(elm, ev) {
      if (ev) {
        elm.addClass("over");
        elm.getElement("a").addClass("over");
        if (elm.getChildren()[1]) {
          main.show(elm.getChildren()[1]);
        }
      } else {
        elm.removeClass("over");
        elm.getElement("a").removeClass("over");
        if (elm.getChildren()[1]) {
          main.hide(elm.getChildren()[1]);
        }
      }
    },
    show: function(sub_elm) {
      if (sub_elm.hide_time_id) {
        clearTimeout(sub_elm.hide_time_id);
      }
      sub_elm.show_time_id = setTimeout(function() {
        if (!sub_elm.hasClass("shown-sublist")) {
          sub_elm.addClass("shown-sublist");
        }
      }, main.settings.show_delay);
    },
    hide: function(sub_elm) {
      if (sub_elm.show_time_id) {
        clearTimeout(sub_elm.show_time_id);
      }
      sub_elm.hide_time_id = setTimeout(function() {
        if (sub_elm.hasClass("shown-sublist")) {
          sub_elm.removeClass("shown-sublist");
        }
      }, main.settings.hide_delay);
    }
  };
  if (arguments[1]) {
    main.settings = Object.extend(main.settings, arguments[1]);
  }
  if (main.obj_nav) {
    main.init(main.obj_nav, false);
  }
};

function removeAdsWidget(widgetIdentity) {
  en4.core.request.send(new Request.JSON({
    url: en4.core.baseUrl + 'siteevent/index/remove-ads-widget',
    data: {
      content_id: widgetIdentity,
      format: 'json'
    },
    onSuccess: function(responseJSON) {
      $('siteevent_ads_plugin_' + widgetIdentity).destroy();
      if ($$(".tab_" + widgetIdentity)) {
        $$(".tab_" + widgetIdentity).destroy();
      }
    }
  }));
}


en4.siteevent.create = {
  selectRef: {},
  activeEventType: '',
  repeatEventHTML: '',
  counter: 0,
  customdates: new Array(),
  customdates_temp: new Array(),
  customdates_temp_another: new Array(),
  default_counter: 0,
  activemode: 'create',
  prev_eventtype: 'never',
  is_online: function(isonline) {

    if (isonline == true) {
      $('venue_name-wrapper').setStyle('display', 'none');
      $('online_events-wrapper').setStyle('display', 'block');
      if ($('location-wrapper'))
        $('location-wrapper').setStyle('display', 'none');
      if ($('location_map-wrapper'))
        $('location_map-wrapper').setStyle('display', 'none');
      $('is_online').value = 1;
    } else {
      $('venue_name-wrapper').setStyle('display', 'block');
      $('online_events-wrapper').setStyle('display', 'none');
      if ($('location-wrapper'))
        $('location-wrapper').setStyle('display', 'block');
      if ($('location_map-wrapper') && $('location').value != '')
        $('location_map-wrapper').setStyle('display', 'block');
      $('is_online').value = 0;
    }
  },
  getLocation: function(fieldName) {
    // return;
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        if (!position.address) {
          var mapDetect = new google.maps.Map(new Element('div'), {
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center: new google.maps.LatLng(0, 0)
          });
          var service = new google.maps.places.PlacesService(mapDetect);
          var request = {
            location: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
            radius: 500
          };
          service.search(request, function(results, status) {
            if (status == 'OK') {
              var index = 0;
              var radian = 3.141592653589793 / 180;
              var my_distance = 1000;
              var R = 6371; // km
              for (var i = 0; i < results.length; i++) {
                var lat2 = results[i].geometry.location.lat();
                var lon2 = results[i].geometry.location.lng();
                var dLat = (lat2 - position.coords.latitude) * radian;
                var dLon = (lon2 - position.coords.longitude) * radian;
                var lat1 = position.coords.latitude * radian;
                lat2 = lat2 * radian;
                var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.sin(dLon / 2) * Math.sin(dLon / 2) * Math.cos(lat1) * Math.cos(lat2);
                var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                var d = R * c;

                if (d < my_distance) {
                  index = i;
                  my_distance = d;
                }
              }
              document.getElementById(fieldName).value = (results[index].vicinity) ? results[index].vicinity : '';
              if (document.getElementById(fieldName).value)
                en4.siteevent.create.getMap(lat2, lon2);
              document.getElementById(fieldName + '_map-wrapper').setStyle('display', 'block');
            }
          })
        } else {
          var delimiter = (position.address && position.address.street != '' && position.address.city != '') ? ', ' : '';
          var location = (position.address) ? (position.address.street + delimiter + position.address.city) : '';
          document.getElementById(fieldName).value = location;
          document.getElementById(fieldName + '_map-wrapper').setStyle('display', 'block');
          if (document.getElementById(fieldName).value)
            en4.siteevent.create.getMap(lat2, lon2);
        }
      });
    }
  },
  getMap: function(latitude, longitude) {

    var myLatlng = new google.maps.LatLng(latitude, longitude);
    var new_map = false;
    if (this.map == undefined) {
      new_map = true;
      this.map = new google.maps.Map($('location_map-element'), {
        navigationControl: false,
        mapTypeControl: false,
        scaleControl: false,
        draggable: false,
        streetViewControl: false,
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
  _resetCustomCalendar: function(type) {

    if (type == 'custom') {
      var startdatetime = new Date();
      var startday = startdatetime.getDate();
      var startmonth = parseInt(startdatetime.getMonth()) + parseInt(1);
      if (startdatetime.getDate() <= 9)
        var startday = '0' + startdatetime.getDate();
      if (startmonth <= 9)
        var startmonth = '0' + startmonth;
      if (Seaocore_CalendarFormat == 'd/m/Y')
        $('custom_repeat_starttime-date').value = startday + '/' + startmonth + '/' + startdatetime.getFullYear();
      else if (Seaocore_CalendarFormat == 'Y/m/d')
        $('custom_repeat_starttime-date').value = startdatetime.getFullYear() + '/' + startmonth + '/' + startday;
      else
        $('custom_repeat_starttime-date').value = startmonth + '/' + startday + '/' + startdatetime.getFullYear();
      $('calendar_output_span_custom_repeat_starttime-date').innerHTML = $('custom_repeat_starttime-date').value

      var enddatetime = new Date();
      var endday = enddatetime.getDate();
      var endmonth = parseInt(enddatetime.getMonth()) + parseInt(1);
      if (endday <= 9)
        var endday = '0' + endday;
      if (endmonth <= 9)
        var endmonth = '0' + endmonth;
      if (Seaocore_CalendarFormat == 'd/m/Y')
        $('custom_repeat_endtime-date').value = endday + '/' + endmonth + '/' + enddatetime.getFullYear();
      else if (Seaocore_CalendarFormat == 'Y/m/d')
        $('custom_repeat_endtime-date').value = enddatetime.getFullYear() + '/' + endmonth + '/' + endday;
      else
        $('custom_repeat_endtime-date').value = endmonth + '/' + endday + '/' + enddatetime.getFullYear();

      $('calendar_output_span_custom_repeat_endtime-date').innerHTML = $('custom_repeat_endtime-date').value;
    }

  },
  _repeatEvent: function(self, offset) {
    if (typeof self != 'undefined')
      this.selectRef = self;
    var openLighbox = true;
    var calenderElements = {};
    $('event_repeat_date_error').set('style', 'display:none');
    switch (this.selectRef.value) {

      case 'daily':

        calenderElements['daily_repeat_time-date'] = Seaocore_CalendarFormat;
        this.activeEventType = 'daily_repeat_event';
        break;

      case 'weekly':
        $('event_repeat_weekly_error').set('style', 'display:none;');
        calenderElements['weekly_repeat_time-date'] = Seaocore_CalendarFormat;
        this.activeEventType = 'weekly_repeat_event';
        break;

      case 'monthly':
        $('event_repeat_custom_error').set('style', 'display:none;');
        calenderElements['monthly_repeat_time-date'] = Seaocore_CalendarFormat;
        this.activeEventType = 'monthly_repeat_event';
        break;

      case 'custom':
        this._resetCustomCalendar('custom');
        calenderElements = {'custom_repeat_starttime-date': Seaocore_CalendarFormat, 'custom_repeat_endtime-date': Seaocore_CalendarFormat};
        this.activeEventType = 'custom_repeat_event';
        break;

      default: 
        

          //SHOW THE DETAILED INFO ON THE MAIN EVENT CREATION PAGE
          if ($('starttime-wrapper'))
            $('starttime-wrapper').set('style', 'display:block;');
          $('endtime-wrapper').set('style', 'display:block;');
          if ($('hidedates-wrapper'))
            $('hidedates-wrapper').destroy();      
        if ($('event_details_repeat_info'))
          $('event_details_repeat_info').destroy();
        openLighbox = false;
        this.activemode = 'create';
        this._resetRepeatEvent();
        break;

    }
    if (this.prev_eventtype != 'never' && this.selectRef.value != 'never') {
      this.activemode = 'edit';
    }
    else {
      this.activemode = 'create';
    }


    if (openLighbox == true) {

      $(this.activeEventType).set('style', 'display:block;');

      this.repeatEventHTML = $('Siteevent_repeatEvent').innerHTML;
      $('Siteevent_repeatEvent').innerHTML = '';
      if (SmoothboxSEAO.active && $('siteevents_create_quick')) {
        $('siteevents_create_quick').getParent().setStyle('display', 'none');
        if (!$('siteevent_lightbox_repeatevent')) {
          new Element('div', {
            'class': 'siteevent_repeatevent_form_wrapper create_quick',
            'id': 'siteevent_lightbox_repeatevent'
          }).inject($('siteevents_create_quick').getParent().getParent());
        }
        $('siteevent_lightbox_repeatevent').innerHTML = this.repeatEventHTML;
        $('siteevent_lightbox_repeatevent').setStyle('display', 'block');
        SmoothboxSEAO.doAutoResize();
      } else {
        SmoothboxSEAO.open('<div id="siteevent_lightbox_repeatevent" class="siteevent_repeatevent_form_wrapper">' + this.repeatEventHTML + '</div>');
        SmoothboxSEAO.wrapper.removeEvents('click').addEvent('click', function(event) {
          var el = $(event.target);
          if (el.hasClass('seao_smoothbox_lightbox_content') || el.getParent('.seao_smoothbox_lightbox_content'))
            return;
          event.stop();
          this._resetRepeatEvent();

        }.bind(this));
      }
      //ADDING A CALENDER ON INPUT ELEMENT.
      this._addCalendar(calenderElements, offset);



    }
  },
  _editrepeatEvent: function() {
    this.activemode = 'edit';
    this._repeatEvent();
    if (this.selectRef.value == 'custom') {
      this.customdates = Array.clone(this.customdates_temp_another);
      for (i = 0; i < this.customdates.length; i++) {
        if (typeof this.customdates[i] != 'undefined') {
          $('customdate-' + i).setStyle('display', 'block');
          this.counter = i;
        }
      }
      $('custom_repeat_dates').show();
      var custom_list_dates = $('custom_repeat_dates').getElements("ul li");
      this.counter = custom_list_dates.length;
    }
  },
  _resetRepeatEvent: function() {

    $('event_repeat_custom_error').set('style', 'display:none;');
    $('event_repeat_date_error').setStyle('display', 'none');
    if (this.activemode == 'create') {
      this.selectRef.selectedIndex = 'never';
      this.prev_eventtype = 'never';
      this.selectRef.getAllNext().each(function(el) {
        el.destroy();

      });

    }
    else if (this.activemode == 'edit') {
      this.selectRef.value = this.prev_eventtype;
      this.selectRef.options[this.selectRef.options.selectedIndex].setAttribute("selected", "selected");
    }

    if ($('siteevent_lightbox_repeatevent') != null) { 
      $('Siteevent_repeatEvent').innerHTML = $('siteevent_lightbox_repeatevent').innerHTML;
    }

    $(this.activeEventType).set('style', 'display:none;');
    this.repeatEventHTML = '';
    for (i = 0; i < this.customdates_temp.length; i++) {


      if (typeof this.customdates_temp[i] != 'undefined') {

        if ($('customdate-' + this.customdates_temp[i])) {
          $('customdate-' + this.customdates_temp[i]).destroy();
          if (typeof this.customdates[this.customdates_temp[i]] != 'undefined') {
            delete this.customdates[this.customdates_temp[i]];
          }
        }
      }
    }

    if ($('custom_list_ul').getFirst() == null) {
      $('custom_repeat_dates').hide();
    }
    this.customdates_temp = new Array();
    if ($('siteevent_lightbox_repeatevent') && $('siteevent_lightbox_repeatevent').hasClass('create_quick')) {
      $('siteevents_create_quick').getParent().setStyle('display', 'block');
      $('siteevent_lightbox_repeatevent').setStyle('display', 'none');
      SmoothboxSEAO.doAutoResize();
    } else {
      SmoothboxSEAO.close();
      SmoothboxSEAO.attach();
    }
    //SmoothboxSEAO.wrapper.destroy();
  },
  _addCalendar: function(obj, offset) {

    if(offset == 1) {
        offset = 0;
    } else if(offset == 2) {
        offset = 1;
    } else if(offset == 3) {
        offset = 6;
    }
 

    for (var i in obj) {
      if ($(this.activeEventType).getElement('button')) {
        $(this.activeEventType).getElement('button').destroy();
      }
    }
     //IF SITE ADMIN HAS SET SETTING TO SHOW ADVANCED CALENDAR:
    var calendarClass = 'seaocore_event_calendar';  
    if(typeof siteevent_advcalender != undefined && siteevent_advcalender != 1)
      var calendarClass = 'event_calendar';
    if ($('eventrepeat_id').value == 'custom') {
      var myCal = {};
      Object.each(obj, function(value, key) {
        var json = {};

        json[key] = value;
        myCal[key] = new Calendar(json, {
          classes: [calendarClass],
          navigation: 1,
          pad: 0,
          direction: 0,
          draggable: false,
          months: Seaocore_CalendarMonths,
          days: Seaocore_CalendarDays,
          day_suffixes: ['', '', '', ''],
          offset: offset ? offset : 0,
          onShowStart: function() {
            if (typeof cal_custom_repeat_starttime_onShowStart == 'function')
              cal_custom_repeat_starttime_onShowStart(myCal);
            if (typeof cal_custom_repeat_endtime_onShowStart == 'function')
              cal_custom_repeat_endtime_onShowStart(myCal);


          },
          onHideStart: function() {
            if (typeof cal_custom_repeat_starttime_onHideStart == 'function')
              cal_custom_repeat_starttime_onHideStart(myCal);
          },
        });

      })

    }
    else {
      myCal = new Calendar(obj, {
        classes: [calendarClass],
        navigation: 1,
        pad: 0,
        direction: 0,
        draggable: false,
        months: Seaocore_CalendarMonths,
        days: Seaocore_CalendarDays,
        day_suffixes: ['', '', '', ''],
        offset: offset? offset : 0,
      });
      cal_event_repeat_endtime_onHideStart(myCal);
    }

  },
  //Custom Date Work
  _addCustomDate: function() {
    if ($('custom_repeat_starttime-date').value == '')
      return;
    //CHECK IF BOTH DATES AND TIME ARE SAME THEN WE WILL NOT ADD THAT DATE AND SHOW THERE AN ERROR MESSAGE.
    var errorMsg = this._checkDates('custom_repeat_starttime', 'custom_repeat_endtime');
    if (errorMsg != '') {
      $('event_repeat_custom_error').set('style', 'display:none;')
      $('event_repeat_date_error').setStyle('display', 'block');
      $('event_repeat_date_error').getElement('li').innerHTML = en4.core.language.translate(errorMsg);
      return;
    }
    else
      $('event_repeat_date_error').setStyle('display', 'none');
    $('event_repeat_custom_error').set('style', 'display:none;');
    this.counter++;
    var catarea = $('custom_list_ul');
    var count = this.counter;

    //CHECK IF BOTH DATES ARE EQUAL:
    var cal_bound_start = seao_getstarttime($('custom_repeat_starttime-date').value);
    var cal_bound_end = seao_getstarttime($('custom_repeat_endtime-date').value);
    var date1 = new Date(cal_bound_start);
    if ($('custom_repeat_endtime-date').value == '') {
      $('custom_repeat_endtime-date').value = $('custom_repeat_starttime-date').value;
      $('calendar_output_span_custom_repeat_endtime-date').innerHTML = $('custom_repeat_starttime-date').value;
    }
    var date2 = new Date(cal_bound_end);

    var newli = document.createElement('li');
    newli.id = 'customdate-' + this.counter;
    if (+date1 === +date2) {
      newli.innerHTML = $('custom_repeat_starttime-date').value + ' ' + en4.core.language.translate('from') + ' ' + $('custom_repeat_starttime-hour').value + ': ' + $('custom_repeat_starttime-minute').value + ' ' + ($('custom_repeat_starttime-ampm') != null ? $('custom_repeat_starttime-ampm').value : '') + ' ' + 'to' + ' ' + $('custom_repeat_endtime-hour').value + ': ' + $('custom_repeat_endtime-minute').value + ' ' + ($('custom_repeat_endtime-ampm') != null ? $('custom_repeat_endtime-ampm').value : '') + ' <a href="javascript:void(0);" onclick="en4.siteevent.create._removeCustomDate(' + this.counter + ');">X</a>';

    }
    else{
      
      newli.innerHTML = $('custom_repeat_starttime-date').value + ' ' + en4.core.language.translate('from') + ' ' +  $('custom_repeat_starttime-hour').value + ': ' + $('custom_repeat_starttime-minute').value + ' ' + ($('custom_repeat_starttime-ampm') != null ?$('custom_repeat_starttime-ampm').value : '')  + ' '  + 'to' + ' ' + $('custom_repeat_endtime-date').value +  ' ' + $('custom_repeat_endtime-hour').value + ': ' + $('custom_repeat_endtime-minute').value + ' ' + ($('custom_repeat_endtime-ampm') != null ? $('custom_repeat_endtime-ampm').value : '' ) + ' <a href="javascript:void(0);" onclick="en4.siteevent.create._removeCustomDate(' + this.counter + ');">X</a>';
      
      
    }   
   
		newli.inject(catarea, 'top');
    $('custom_repeat_dates').show();
    this.customdates[this.counter] = {'starttime': {'date': $('custom_repeat_starttime-date').value, 'hour': $('custom_repeat_starttime-hour').value, 'minute': $('custom_repeat_starttime-minute').value, 'ampm': ($('custom_repeat_starttime-ampm') != null ? $('custom_repeat_starttime-ampm').value : '')}, 'endtime': {'date': $('custom_repeat_endtime-date').value, 'hour': $('custom_repeat_endtime-hour').value, 'minute': $('custom_repeat_endtime-minute').value, 'ampm': ($('custom_repeat_endtime-ampm') != null ? $('custom_repeat_endtime-ampm').value : '')}};
    this.customdates_temp[this.counter] = this.counter;


  },
  _removeCustomDate: function(counter) {


    $('customdate-' + counter).setStyle('display', 'none');
    delete this.customdates[counter];
    var hidedatescontainer = true;
    for (i = 0; i < this.customdates.length; i++) {
      if (typeof this.customdates[i] != 'undefined') {
        hidedatescontainer = false;
      }
    }
    if (hidedatescontainer) {
      $('custom_repeat_dates').hide();
      this.counter = 0;
    }
  },
  _checkDates: function(dateelement_start, dateelement_end) {
    var errorMsg = '';
    var checkdate = true;
    //CHECK IF BOTH START AND END DATE ARE SAME.THEN SHOW WARNING POPUP
    var cal_bound_start = seao_getstarttime($(dateelement_start + '-date').value);
    var cal_bound_end = seao_getstarttime($(dateelement_end + '-date').value);
    var startdatetime = new Date(cal_bound_start);
    var enddatetime = new Date(cal_bound_end);

    var starttimefieldseconds = parseInt(($(dateelement_start + '-hour').value) * 60 * 60) + parseInt(($(dateelement_start + '-minute').value) * 60);
    if ($(dateelement_start + '-ampm') && $(dateelement_start + '-ampm').value == 'PM' && $(dateelement_start + '-hour').value != 12)
      starttimefieldseconds = parseInt(starttimefieldseconds) + parseInt(12 * 60 * 60);

    if ($(dateelement_start + '-ampm') && $(dateelement_start + '-ampm').value == 'AM' && $(dateelement_start + '-hour').value == 12)
      starttimefieldseconds = parseInt(starttimefieldseconds) - parseInt(12 * 60 * 60);

    var endtimefieldseconds = parseInt(($(dateelement_end + '-hour').value) * 60 * 60) + parseInt(($(dateelement_end + '-minute').value) * 60);
    if ($(dateelement_end + '-ampm') && $(dateelement_end + '-ampm').value == 'PM' && $(dateelement_end + '-hour').value != 12)
      endtimefieldseconds = parseInt(endtimefieldseconds) + parseInt(12 * 60 * 60);

    if ($(dateelement_end + '-ampm') && $(dateelement_end + '-ampm').value == 'AM' && $(dateelement_end + '-hour').value == 12)
      endtimefieldseconds = parseInt(endtimefieldseconds) - parseInt(12 * 60 * 60);

    if (startdatetime.getTime() == enddatetime.getTime()) {
      //check for the day start time and end time either they are equal or not.
      if (starttimefieldseconds >= endtimefieldseconds)
        checkdate = false;

    } else if (startdatetime.getTime() > enddatetime.getTime()) {
      checkdate = false;
    }

    if (checkdate == true && (typeof editFullEventDate == 'undefined' || editFullEventDate)) {
      var currentTime = new Date();
      currentTimeSeconds = parseInt(currentTime.getHours() * 60 * 60) + parseInt(currentTime.getMinutes()) * 60 + parseInt(currentTime.getSeconds());
      var currentTime = new Date((parseInt(currentTime.getMonth()) + parseInt(1)) + '/' + currentTime.getDate() + '/' + currentTime.getFullYear());

      if ((currentTime.getTime() == startdatetime.getTime() && currentTimeSeconds > starttimefieldseconds) || currentTime.getTime() > startdatetime.getTime()) {
        checkdate = false;
        errorMsg = en4.core.language.translate("Start time should be greater than the current time.");

      }
    } else if (!checkdate) {
      errorMsg = en4.core.language.translate("End time should be greater than the Start time.");

    }

    return errorMsg;
  },
  save: function() {
    if (this.selectRef.value != 'custom') {

      //SHOW THE DETAILED INFO ON THE MAIN EVENT CREATION PAGE
      if ($('starttime-wrapper'))
        $('starttime-wrapper').set('style', 'display:block;');
      $('endtime-wrapper').set('style', 'display:block;');
      if ($('hidedates-wrapper'))
        $('hidedates-wrapper').destroy();
    }

    this.customdates_temp = new Array();
    switch (this.selectRef.value) {

      case 'daily':

        $('id_daily-repeat_interval_select').options[$('id_daily-repeat_interval_select').options.selectedIndex].setAttribute("selected", "selected");

        infoHTML = (parseInt($('id_daily-repeat_interval_select').value) > parseInt(1)) ? " " + en4.core.language.translate('Every') + " " + $('id_daily-repeat_interval_select').value + " " + en4.core.language.translate('Days:') + " " : " " + en4.core.language.translate('Daily Event:') + " ";

        infoHTML = infoHTML + $('starttime-hour').value + ':' + (($('starttime-minute').value == 0) ? '00' : $('starttime-minute').value) + ' ' + ($('starttime-ampm') != null ? $('starttime-ampm').value : '') + ' ' + en4.core.language.translate('to') + ' ' + $('endtime-hour').value + ':' + (($('endtime-minute').value == 0) ? '00' : $('endtime-minute').value) + ' ' + ($('endtime-ampm') != null ? $('endtime-ampm').value : '');
        infoHTML = infoHTML + ' ' + en4.core.language.translate('until') + ' ' + $('daily_repeat_time-date').value;

        if ($('event_details_repeat_info'))
          $('event_details_repeat_info').destroy();
        infoHTML = '<div class="form-wrapper" id="event_details_repeat_info"><div class=form-label></div><div class=form-element>' + infoHTML + ' <a class="edit" href="javascript:void(0)" onclick="javascript:en4.siteevent.create._editrepeatEvent();">' + '[' + en4.core.language.translate('edit') + ']</a></div></div>';
        Elements.from(infoHTML).reverse().inject($('eventrepeat_id-wrapper'), 'after');

        break;

      case 'weekly':
        //THERE MUST BE ATLEAT ONE WEEKDAY SELECTED BY USER.

        var infoHTML = '';

        $('id_weekly-repeat_interval_select').options[$('id_weekly-repeat_interval_select').options.selectedIndex].setAttribute("selected", "selected");

        infoHTML = infoHTML + $('id_weekly-repeat_interval_select').value > 1 ? " " + en4.core.language.translate('Every') + " " + $('id_weekly-repeat_interval_select').value + " " + en4.core.language.translate('weeks') + " - " : "";
        var checkboxes = new Array();
        var count = 0;
        $('weekly_repeat_event').getElements('input').each(function(el) {

          if (el.type == 'checkbox') {

            if (el.checked == 1) {
              el.setAttribute("checked", "checked")
              checkboxes[count++] = el.value;
            }
            else {
              el.removeAttribute("checked")
            }
          }

        });
        if (parseInt(count) == parseInt(0)) {
          $('event_repeat_weekly_error').set('style', 'display:block;');
          return;
        }
        else {
          $('event_repeat_weekly_error').set('style', 'display:none;');
        }
        infoHTML = infoHTML + en4.core.language.translate('Every') + ' ';
        for (i = 0; i < count; i++) {

          if (i == 0)
            infoHTML = infoHTML + en4.core.language.translate(checkboxes[i].capitalize());
          else if (i > 0 && i < (count - 1))
            infoHTML = infoHTML + ' ' + en4.core.language.translate(checkboxes[i].capitalize());
          else if (i == (count - 1) && i > 0) {
            infoHTML = infoHTML + ' & ' + en4.core.language.translate(checkboxes[i].capitalize());
          }

        }
        infoHTML = infoHTML + ': ';


        //ADD TIME
        infoHTML = infoHTML + $('starttime-hour').value + ':' + (($('starttime-minute').value == 0) ? '00' : $('starttime-minute').value) + ' ' + ($('starttime-ampm') != null ? $('starttime-ampm').value : '') + ' ' + en4.core.language.translate('to') + ' ' + $('endtime-hour').value + ':' + (($('endtime-minute').value == 0) ? '00' : $('endtime-minute').value) + ' ' + ($('endtime-ampm') != null ? $('endtime-ampm').value : '');
        infoHTML = infoHTML + ' ' + en4.core.language.translate('until') + ' ' + $('weekly_repeat_time-date').value;

        if ($('event_details_repeat_info'))
          $('event_details_repeat_info').destroy();
        infoHTML = '<div class="form-wrapper" id="event_details_repeat_info"><div class=form-label></div><div class=form-element>' + infoHTML + ' <a class="edit" href="javascript:void(0)" onclick="javascript:en4.siteevent.create._editrepeatEvent();">' + '[' + en4.core.language.translate('edit') + ']</a></div></div>';
        Elements.from(infoHTML).reverse().inject($('eventrepeat_id-wrapper'), 'after');
        break;

      case 'monthly':
        var infoHTML = '';
        $('id_monthly-repeat_interval_select').options[$('id_monthly-repeat_interval_select').options.selectedIndex].setAttribute("selected", "selected");

        $('id_monthly-relative_day_select').options[$('id_monthly-relative_day_select').options.selectedIndex].setAttribute("selected", "selected");

        $('id_monthly-day_of_week_select').options[$('id_monthly-day_of_week_select').options.selectedIndex].setAttribute("selected", "selected");

        $('id_monthly-absolute_day_select').options[$('id_monthly-absolute_day_select').options.selectedIndex].setAttribute("selected", "selected");

        infoHTML = infoHTML + ($('id_monthly-repeat_interval_select').value > 1 ? " " + en4.core.language.translate('In Every') + " " + $('id_monthly-repeat_interval_select').value + " " + en4.core.language.translate('months') + ", " : "");

        if ($('monthly_day'))
          $('monthly_day').destroy();

        //CHECK IF THE MODE IS WEEK DAY.
        if ($('relative_day_week_month').isDisplayed()) {
          infoHTML = infoHTML + (en4.core.language.translate($('id_monthly-relative_day_select').value) + " " + en4.core.language.translate($('id_monthly-day_of_week_select').value.capitalize())) + " "+en4.core.language.translate("of every month")+": ";
          var hiddeninput = '<input type="hidden" value="relative_weekday" name="monthly_day" id="monthly_day">';
        }
        else {
          infoHTML = infoHTML + ($('id_monthly-repeat_interval_select').value == 1 ? en4.core.language.translate("Day") + " " + $('id_monthly-absolute_day_select').value + " " + en4.core.language.translate('of every month') + ": " : en4.core.language.translate("Day") + " " + $('id_monthly-absolute_day_select').value + " " + en4.core.language.translate('of the month') + ": ");
          var hiddeninput = '<input type="hidden" value="absolute_day" name="monthly_day" id="monthly_day">';

        }
        infoHTML = infoHTML + $('starttime-hour').value + ':' + (($('starttime-minute').value == 0) ? '00' : $('starttime-minute').value) + ' ' + ($('starttime-ampm') != null ? $('starttime-ampm').value : '') + ' ' + en4.core.language.translate('to') + ' ' + $('endtime-hour').value + ':' + (($('endtime-minute').value == 0) ? '00' : $('endtime-minute').value) + ' ' + ($('endtime-ampm') != null ? $('endtime-ampm').value : '')
        infoHTML = infoHTML + ' ' + en4.core.language.translate('until')+ ' ' + $('monthly_repeat_time-date').value;

        if ($('event_details_repeat_info'))
          $('event_details_repeat_info').destroy();
        infoHTML = '<div class="form-wrapper" id="event_details_repeat_info"><div class=form-label></div><div class=form-element>' + infoHTML + ' <a class="edit" href="javascript:void(0)" onclick="javascript:en4.siteevent.create._editrepeatEvent();">[' + en4.core.language.translate('edit') + ']</a></div></div>' + hiddeninput;
        Elements.from(infoHTML).reverse().inject($('eventrepeat_id-wrapper'), 'after');



        break;

      case 'custom':
        var addDate = false;
        for (i = 0; i < this.customdates.length; i++) {
          if (typeof this.customdates[i] != 'undefined') {
            addDate = true;
          }
        }
        if (parseInt(this.customdates.length) == parseInt(0) || addDate == false) {
          $('event_repeat_date_error').setStyle('display', 'none');
          $('event_repeat_custom_error').set('style', 'display:block;');
          return;
        }
        else {
          $('event_repeat_custom_error').set('style', 'display:none;');
        }
        if ($('event_details_repeat_info'))
          $('event_details_repeat_info').destroy();
        if (this.customdates.length > 0) {
          var infoHTML = '<div class="form-wrapper" style="display:block;" id="event_details_repeat_info"><div class=form-label></div><div class=form-element>' + en4.core.language.translate('on the following days') + ': <a class="edit" href="javascript:void(0)" onclick="javascript:en4.siteevent.create._editrepeatEvent();">[' + en4.core.language.translate('edit') + ']</a><div><ul id="custom_saved_list" class="siteevent-date-list" style="margin-top:10px;"></ul></div></div></div>';
          Elements.from(infoHTML).reverse().inject($('eventrepeat_id-wrapper'), 'after');
        }


        //CREATE LI ELEMENT
        var customdates_count = 0;

        this.customdates_temp_another = Array.clone(this.customdates);
        for (i = 0; i < this.customdates.length; i++) {

          var newli = document.createElement('li');
          if (typeof this.customdates[i] != 'undefined') {
            customdates_count++;
            //CHECK IF BOTH DATES ARE EQUAL:
            var cal_bound_start = seao_getstarttime(this.customdates[i].starttime.date);
            var cal_bound_end = seao_getstarttime(this.customdates[i].endtime.date);

            var date1 = new Date(cal_bound_start);
            var date2 = new Date(cal_bound_end);

            var starttime_date = this.customdates[i].starttime.hour + ':' + this.customdates[i].starttime.minute + ' ' + (this.customdates[i].starttime.ampm != null ? this.customdates[i].starttime.ampm : '');

            var endtime_date = this.customdates[i].endtime.hour + ':' + this.customdates[i].endtime.minute + ' ' + (this.customdates[i].endtime.ampm != null ? this.customdates[i].endtime.ampm : '');

            if (+date1 === +date2)
              var html = this.customdates[i].starttime.date + ' ' + en4.core.language.translate('from') + ' ' + starttime_date + ' ' + 'to' + ' ' + endtime_date;
            else
              var html = this.customdates[i].starttime.date + ' ' + en4.core.language.translate('from') + ' ' + starttime_date + ' ' + 'to' + ' ' + this.customdates[i].endtime.date + ' ' + endtime_date;
            var saveCustomHidden = '';

            if (editFullSiteeventDates || (parseInt(this.default_counter) + 1) < parseInt(customdates_count)) {
              saveCustomHidden = '<input type="hidden" name="customdate_' + i + '" value="' + this.customdates[i].starttime.date + ' ' + starttime_date + '-' + this.customdates[i].endtime.date + ' ' + endtime_date + '">';
            }

            newli.innerHTML = '<li>' + html + saveCustomHidden + '</li>';

            newli.inject($('custom_saved_list'), 'bottom');
          }


        }


        //CHECK IF THERE IS NO CUSTOM DATES
        if ($('custom_saved_list') && $('custom_saved_list').getFirst() == null) {
          $('event_details_repeat_info').destroy();
          this.selectRef.value = 'never';
          if ($('starttime-wrapper'))
            $('starttime-wrapper').set('style', 'display:block;');
          $('endtime-wrapper').set('style', 'display:block;');
          if ($('hidedates-wrapper'))
            $('hidedates-wrapper').set('style', 'display:none;');
        }

        //MAKE A HIDDEN FIELD FOR COUNT OF CUSTOM DATES:
        if (customdates_count > 0) {
          var countcustomdates = '<input type="hidden" name="countcustom_dates" value="' + customdates_count + '">';
          Elements.from(countcustomdates).inject($('custom_saved_list'), 'after');
          //HIDE THE START TIEM AND END TIME FROM THE EVENT CREATION PAGE:
          if ($('starttime-wrapper'))
            $('starttime-wrapper').set('style', 'display:none;');
          $('endtime-wrapper').set('style', 'display:none;');
          if (!$('hidedates-wrapper')) {
            var hidedatetime = '<div class="form-wrapper" id="hidedates-wrapper"><div class="form-label" id="hidedates-label"><label class="optional" for="hidedates">'+en4.core.language.translate('Date & Time')+'</label></div><div class="form-element" id="starttime-element">'+en4.core.language.translate('Specific dates and times are set for this event.')+'</div></div>';
            Elements.from(hidedatetime).inject($('endtime-wrapper'), 'after');
          }
        }

        break;

      default:
        this._resetRepeatEvent();
        break;


    }
    this.prev_eventtype = this.selectRef.value;
    this.activemode = 'save';
    this._resetRepeatEvent();
  },
  //ADD EITHER SITE USER AS HOST OR ANY OTHER USER WHO IS NOT SITE USER
  addHostname: function(action) {

    //IF SITE USER HAS TO BE ADDED
    if (action == false) {
      var dblock = 'display:block;';
      var dnone = 'display:none;';
    } else {
      var dblock = 'display:none;';
      var dnone = 'display:block;';
    }

    $('sitemember_desc').set('style', dblock);
    $('custom_member').set('style', dnone);
    $('host_name_custom').set('style', dblock);
    $('host_name_site').set('style', dnone);
    $('user_id').value = 0;
    $('host').value = '';


  }

}



en4.siteevent.member = {
  defaultPopUPHeight: 0,
  request: function(event_id, occurrence_id) {
    $('event_membership').innerHTML = '<img src="'+en4.core.staticBaseUrl+'application/modules/Seaocore/externals/images/load.gif" />';
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'siteevent/member-ajax-based/request',
      data: {
        format: 'json',
        event_id: event_id,
        occurrence_id: occurrence_id
      },
      onComplete: function(response) {
        $('event_membership').innerHTML = '';
        $('event_membership').innerHTML = '<a id="siteevent_member_' + event_id + '" href="javascript: void(0);" onClick="en4.siteevent.member.cancel(' + event_id + ', ' + occurrence_id + ')"><span>' + en4.core.language.translate("Cancel Invite Request") + '</span></a>';
        window.location.href = window.location.href;
      }
    }));
  },
  join: function(event_id, occurrence_id) {
    $('event_membership').innerHTML = '<img src="'+en4.core.staticBaseUrl+'application/modules/Seaocore/externals/images/load.gif" />';
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'siteevent/member-ajax-based/join',
      data: {
        format: 'json',
        event_id: event_id,
        occurrence_id: occurrence_id
      }, //responseTree, responseElements, responseHTML, responseJavaScript
      onComplete: function(response) {
        $('event_membership').innerHTML = '';
        $('event_membership').innerHTML = '<a id="siteevent_member_' + event_id + '" href="javascript: void(0);" onClick="en4.siteevent.member.leave(' + event_id + ', ' + occurrence_id + ')"><span>' + en4.core.language.translate("Leave Event") + '</span></a>';
        en4.siteevent.member.selectRsvp(2, 2, event_id, occurrence_id);
        window.location.href = window.location.href;
      }
    }));
  },
  leave: function(event_id, occurrence_id) {
    $('event_membership').innerHTML = '<img src="'+en4.core.staticBaseUrl+'application/modules/Seaocore/externals/images/load.gif" />';
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'siteevent/member-ajax-based/leave',
      data: {
        format: 'json',
        event_id: event_id,
        occurrence_id: occurrence_id
      },
      onComplete: function(response) {
        $('going-rsvp').style.display = "none";
        $('not-going-rsvp').style.display = "none";
        $('maybe-going-rsvp').style.display = "none";
        $('siteevent-member-tip').style.display = "none";
        $('siteevent-member-tip-not-going').style.display = "none";
        $('siteevent-member-tip-maybe-going').style.display = "none";
        $('event_membership').innerHTML = '';
        if (response.showLink == 2) {
          $('event_membership').innerHTML = '<a id="siteevent_member_' + event_id + '" href="javascript: void(0);" onClick="en4.siteevent.member.join(' + event_id + ', ' + occurrence_id + ')"><span>' + en4.core.language.translate("Join Event") + '</span></a>';
        } else if (response.showLink == 1) {
          $('event_membership').innerHTML = '<a id="siteevent_member_' + event_id + '" href="javascript: void(0);" onClick="en4.siteevent.member.request(' + event_id + ', ' + occurrence_id + ')"><span>' + en4.core.language.translate("Request Invite") + '</span></a>';
        }
        window.location.href = window.location.href;
      }
    }));
  },
  cancel: function(event_id, occurrence_id) {
    $('event_membership').innerHTML = '<img src="'+en4.core.staticBaseUrl+'application/modules/Seaocore/externals/images/load.gif" />';
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'siteevent/member-ajax-based/cancel',
      data: {
        format: 'json',
        event_id: event_id,
        occurrence_id: occurrence_id
      },
      onComplete: function(response) {
        $('event_membership').innerHTML = '';
        $('event_membership').innerHTML = '<a id="siteevent_member_' + event_id + '" href="javascript: void(0);" onClick="en4.siteevent.member.request(' + event_id + ', ' + occurrence_id + ')"><span>' + en4.core.language.translate("Request Invite") + '</span></a>';
        window.location.href = window.location.href;
      }
    }));
  },
  accept: function(event_id, occurrence_id) {
    $('event_membership').innerHTML = '<img src="'+en4.core.staticBaseUrl+'application/modules/Seaocore/externals/images/load.gif" />';
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'siteevent/member-ajax-based/accept',
      data: {
        format: 'json',
        event_id: event_id,
        occurrence_id: occurrence_id
      },
      onComplete: function(response) {
        $('event_membership').innerHTML = '';
        $('event_membership').innerHTML = '<a id="siteevent_member_' + event_id + '" href="javascript: void(0);" onClick="en4.siteevent.member.leave(' + event_id + ', ' + occurrence_id + ')"><span>' + en4.core.language.translate("Leave Event") + '</span></a>';
        window.location.href = window.location.href;
      }
    }));
  },
  ignore: function(event_id, occurrence_id) {
    $('event_membership').innerHTML = '<img src="'+en4.core.staticBaseUrl+'application/modules/Seaocore/externals/images/load.gif" />';
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'siteevent/member-ajax-based/reject',
      data: {
        format: 'json',
        event_id: event_id,
        occurrence_id: occurrence_id
      },
      onComplete: function(response) {
        $('event_membership').innerHTML = '';
        $('event_membership').innerHTML = '<a id="siteevent_member_' + event_id + '" href="javascript: void(0);" onClick="en4.siteevent.member.leave(' + event_id + ', ' + occurrence_id + ')"><span>' + en4.core.language.translate("Join Event") + '</span></a>';
        window.location.href = window.location.href;
      }
    }));
  },
  selectRsvp: function(option_id, rsvp, event_id, occurrence_id) {
		if(option_id == 1) {
			
			if($('siteevent-member-tip-maybe-going').innerHTML != '') {
				eventMemberTipMayBeGoingInnerHTML=$('siteevent-member-tip-maybe-going').innerHTML;
			}
			$('maybe-going-rsvp').style.display="block";

      if ($('siteevent-member-tip-maybe-going').innerHTML != '') {
        eventMemberTipMayBeGoingInnerHTML = $('siteevent-member-tip-maybe-going').innerHTML;
      }
      $('maybe-going-rsvp').style.display = "block";

      $('going-rsvp').style.display = "none";
      $('not-going-rsvp').style.display = "none";
      $('siteevent-member-tip').style.display = "none";
      $('siteevent-member-tip-not-going').style.display = "none";
      $('siteevent-member-tip-maybe-going').style.display = "block";
      $('siteevent-member-tip-maybe-going').innerHTML = eventMemberTipMayBeGoingInnerHTML;

      var parent = $('siteevent-member-tip-maybe-going').getParent('.layout_siteevent_invite_rsvp_siteevent');
      if (parent) {
        var rightPostion = document.body.getCoordinates().width - parent.getCoordinates().left - parent.getCoordinates().width;
        $('siteevent-member-tip-maybe-going').inject(document.body);
        $('siteevent-member-tip-maybe-going').setStyles({
          'position': 'absolute',
          'top': parent.getCoordinates().bottom,
          'right': rightPostion
        });
      }

      var divElement = new Element('div', {
        'id': 'tip-loaded',
        'styles': {
          'position': 'fixed',
          'left': '0px',
          'right': '0px', 'top': '0px', 'bottom': '0px'
        },
        'onclick': 'hideSiteeventMemberTipMayBeGoing()'
      });
      divElement.inject(document.body);
      setTimeout(function() {
        $('rsvp-maybe-going').focus()
      }, '100');

    } else if (option_id == 2) {
                
        if(option_id == 2) {
            isEventFull(occurrence_id, selectRsvpAction, {});
        }
        else {
            selectRsvpAction({rsvp:option_id});
        }         
        
    } else if (option_id == 0) {
      if ($('siteevent-member-tip-not-going').innerHTML != '') {
        eventMemberTipNotGoingInnerHTML = $('siteevent-member-tip-not-going').innerHTML;
      }
      $('maybe-going-rsvp').style.display = "none";
      $('going-rsvp').style.display = "none";
      $('siteevent-member-tip').style.display = "none";
      $('siteevent-member-tip-maybe-going').style.display = "none";
      $('not-going-rsvp').style.display = "block";
      $('siteevent-member-tip-not-going').style.display = "block";
      $('siteevent-member-tip-not-going').innerHTML = eventMemberTipNotGoingInnerHTML;
      var parent = $('siteevent-member-tip-not-going').getParent('.layout_siteevent_invite_rsvp_siteevent');
      if (parent) {
        var rightPostion = document.body.getCoordinates().width - parent.getCoordinates().left - parent.getCoordinates().width;
        $('siteevent-member-tip-not-going').inject(document.body);
        $('siteevent-member-tip-not-going').setStyles({
          'position': 'absolute',
          'top': parent.getCoordinates().bottom,
          'right': rightPostion
        });
      }
      var divElement = new Element('div', {
        'id': 'tip-loaded',
        'styles': {
          'position': 'fixed',
          'left': '0px',
          'right': '0px', 'top': '0px', 'bottom': '0px'
        },
        'onclick': 'hideSiteeventMemberTipNotGoing()'
      });
      divElement.inject(document.body);
      setTimeout(function() {
        $('rsvp-not-going').focus()
      }, '100');
    }
    new Request.JSON({
      url: en4.core.baseUrl + 'siteevent/widget/profile-rsvp-ajax/',
      method: 'post',
      data: {
        'event_id': event_id,
        'option_id': option_id,
        'subject': en4.core.subject.guid,
        'format': 'json',
        occurrence_id: occurrence_id
      }, //responseTree, responseElements, responseHTML, responseJavaScript
      onComplete: function(response)
      {
      }
    }).send();
  },
  sendActivityRsvpGoing: function(occurrence_id) {
    if ($('rsvp-going').value == '')
      return;
    $('siteevent_loading_image').innerHTML = '<img src="'+en4.core.staticBaseUrl+'application/modules/Seaocore/externals/images/load.gif" />';
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'siteevent/widget/send-activity',
      data: {
        format: 'json',
        'body': $('rsvp-going').value,
        'subject': en4.core.subject.guid,
        'reason': 1,
        occurrence_id: occurrence_id
      },
      onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('siteevent-member-tip').innerHTML = "";
        $('siteevent-member-tip').style.display = 'none';
        if ($('tip-loaded'))
          $('tip-loaded').destroy();
        window.location.href = window.location.href;
      }
    }));
  },
  sendActivityRsvpNotGoing: function(occurrence_id) {
    if ($('rsvp-not-going').value == '')
      return;
    $('siteevent_not_going_loading_image').innerHTML = '<img src="'+en4.core.staticBaseUrl+'application/modules/Seaocore/externals/images/load.gif" />';
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'siteevent/widget/send-activity',
      data: {
        format: 'json',
        'body': $('rsvp-not-going').value,
        'subject': en4.core.subject.guid,
        'reason': 2,
        occurrence_id: occurrence_id
      },
      onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('siteevent-member-tip-not-going').innerHTML = "";
        $('siteevent-member-tip-not-going').style.display = 'none';
        if ($('tip-loaded'))
          $('tip-loaded').destroy();
        window.location.href = window.location.href;
      }
    }));
  },
  sendActivityRsvpMayBeGoing: function(occurrence_id) {
    if ($('rsvp-maybe-going').value == '')
      return;
    $('siteevent_maybe_going_loading_image').innerHTML = '<img src="'+en4.core.staticBaseUrl+'application/modules/Seaocore/externals/images/load.gif" />';
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'siteevent/widget/send-activity',
      data: {
        format: 'json',
        'body': $('rsvp-maybe-going').value,
        'subject': en4.core.subject.guid,
        'reason': 3,
        occurrence_id: occurrence_id
      },
      onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('siteevent-member-tip-maybe-going').innerHTML = "";
        $('siteevent-member-tip-maybe-going').style.display = 'none';
        if ($('tip-loaded'))
          $('tip-loaded').destroy();
        window.location.href = window.location.href;
      }
    }));
  },
  saveRSVP: function(rsvp, event_id, occurrence_id, action, element_id) {

    if(rsvp == 2) {
        isEventFull(occurrence_id, saveRSVPAction, {rsvp:rsvp});
    }
    else {
        saveRSVPAction({event_id:event_id, rsvp:rsvp,occurrence_id:occurrence_id, element_id:element_id, action:action});
    }    
  },
  acceptInvite: function(event_id, occurrence_id, action, element_id) {

    if (element_id == 'invite') {
      $('manage_events_popup').setStyle('display', 'none');
      var newdiv = document.createElement('div');
      newdiv.id = 'join_form_options_popup';
      newdiv.innerHTML = $('join_form_options').innerHTML;
      newdiv.inject($('manage_events_popup'), 'after');
      $('join_form_options_popup').getElementById('cancel').removeAttribute('onclick');
      $('join_form_options_popup').getElementById('cancel').addEvent('click', function() {
        $('manage_events_popup').setStyle('display', 'block');
        $('join_form_options_popup').destroy();
         if($('TB_ajaxContent')) {
        $('TB_ajaxContent').setStyle('height', this.defaultPopUPHeight);
         }
      }.bind(this));
      if($('TB_ajaxContent')) {
        this.defaultPopUPHeight = $('TB_ajaxContent').getStyle('height');

        $('TB_ajaxContent').setStyle('height', 210);
      }
    }
    else {
      Smoothbox.open('<div id="join_form_options_popup">' + $('join_form_options').innerHTML + '</div>');

    }

    $('join_form_options_popup').getElementById('submit').removeEvents('click').addEvent('click', function(event) {
      event.stop();

      this.innerHTML = '<img src="'+en4.core.staticBaseUrl+'application/modules/Siteevent/externals/images/loading.gif">';
      var rsvp = this.getParent('form').toQueryString().parseQueryString().rsvp;
      en4.siteevent.member.saveRSVP(rsvp, event_id, occurrence_id, action, element_id);
      if (element_id == 'calendar') {
        var invitecount = invite_count - 1;
        getInvitedList('calendar', invitecount, event_id)
      }
    });
  },

  setOccurrenceMsgGuest: function(occurrence_id) {

    $('toValues').value = '';
    $('toValues-element').getElements('.tag').each(function(el) {
      el.destroy();

    });
    $('toValues-wrapper').setStyle('display', 'none');
    messageAutocomplete.setOptions({postData: {
        'occurrence_id': occurrence_id
      }});
    if (window.checkGuests) {
      checkGuests();
    }
  }
};


function getDayEvents(date_current, category_id) {

  var data_month = {
    'date_current': date_current,
    category_id: category_id,
    'format': 'html',
    'is_ajax': true,
    viewtype: 'list'
  };

  if (typeof calendar_params != 'undefined')
    data_month = $merge(calendar_params, data_month);
  en4.core.request.send(new Request.HTML({
    'url' : en4.core.baseUrl + 'widget/index/mod/siteevent/name/calendarview-siteevent',
    'data' : data_month,
    onRequest : function() {
      SmoothboxSEAO.open('<div id="siteevent_dayevents" style="width:550px;min-height:70px;"><div class="seaocore_content_loader" style="margin:20px auto 0;"></div></div>');
    },
     onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if($$('.seao_smoothbox_lightbox_overlay').isVisible() == 'true') {
        SmoothboxSEAO.close();
        SmoothboxSEAO.open('<div id="siteevent_dayevents" style="width:550px;">'+ responseHTML + '</div>');
      }
    }
  }));
}

var seao_getstarttime = function(date) {
  if (seao_dateFormat == 'dmy') 
    date = en4.seaocore.covertdateDmyToMdy(date);
  return date;
}

var seao_dateFormat = '';
en4.core.runonce.add(function() {
	if($('siteevents_create')) {
  $('siteevents_create').addEvent('submit', function(e) {
    e.stop();
    //EVENT TYPE IS CUSTOM EVENT THEN WE WILL NOT CHECK THE DATE ISSUE.
    if ($('eventrepeat_id') && $('eventrepeat_id').value == 'custom') {
      this.submit();
      return;
    }


    var errorMsg = en4.siteevent.create._checkDates('starttime', 'endtime');
    if (errorMsg == '')
      this.submit();
    else {
      Smoothbox.open('<div style="width:450px;margin:10px;"><div class="tip"><span>' + en4.core.language.translate(errorMsg) + '</span></div><button onclick="Smoothbox.close();" style="margin-top:10px;">Close</button></div>');
    }

  });
	}

  if (seao_dateFormat == '') {
    en4.core.request.send(new Request.JSON({
      'url': en4.core.baseUrl + 'siteevent/index/get-date-format',
      date: {
        'format': 'html',
        'subject': en4.core.subject.guid,
      },
      onComplete: function(responseJSON, responseText) {
        seao_dateFormat = responseJSON.dateFormat;
        if($('siteevents_create')) {
					initializeCalendar();
				}
      }
    }));
  }
  
}); 

function saveRSVPAction(options) {
    var action = options.action;
    var element_id = options.element_id;
    var occurrence_id = options.occurrence_id;
    var event_id = options.event_id;
    var rsvp = options.rsvp;
    if (action == 'rsvp')
      $('selected_rsvp_' + element_id + '_' + occurrence_id).disabled = true;
    if (action == 'reject')
      var url = en4.core.baseUrl + 'siteevent/member-ajax-based/reject?occurrence_id=' + occurrence_id;
    else
      var url = en4.core.baseUrl + 'siteevent/member-ajax-based/join?occurrence_id=' + occurrence_id;

    new Request.JSON({
      url: url,
      data: {
        format: 'json',
        'event_id': event_id,
        'option_id': rsvp,
        'occurrence_id': occurrence_id,
        ismanagepage: true
      },
      onComplete: function(responseJSON, responseText)
      {
        if ($('join_form_options_popup')) {
          $('join_form_options_popup').innerHTML = '<div style="margin-top: 40px;" class="txt_center bold">Event invite accepted.</div>';
          if($('TB_ajaxContent')) {
          $('TB_ajaxContent').setStyle('height', 100);
          }
          if (element_id == 'invite')
              if($('TB_ajaxContent')) {
                setTimeout("$('TB_ajaxContent').setStyle('height', en4.siteevent.member.defaultPopUPHeight);$('manage_events_popup').setStyle('display', 'block');$('join_form_options_popup').destroy();", "1000");
              }
          else
            setTimeout("Smoothbox.close();", "1000");
        }

        if (element_id != 'tooltip' && element_id != 'calendar') {
          if (action == 'join') {

            $('join_' + element_id + '_' + occurrence_id).setStyle('display', 'none');
            $('rsvp_' + element_id + '_' + occurrence_id).setStyle('display', 'inline-block');
            $('selected_rsvp_' + element_id + '_' + occurrence_id).value = rsvp;
            $('filtered_selected_' + event_id).innerHTML = $('selected_rsvp_' + element_id + '_' + occurrence_id).options[$('selected_rsvp_' + element_id + '_' + occurrence_id).options.selectedIndex].text;
          }
          else if (action == 'reject') {
            var classArray = $('userlist_' + element_id + '_' + occurrence_id).getPrevious().get('class').split(" ");
            if (typeof classArray[1] != 'undefined')
              $('userlist_' + element_id + '_' + occurrence_id).getPrevious().destroy();
            $('userlist_' + element_id + '_' + occurrence_id).destroy();

          }
          else {
            $('selected_rsvp_' + element_id + '_' + occurrence_id).disabled = false;
          }

          if (responseJSON.canInvite && responseJSON.canInvite == true && $('inviteguest_' + element_id + '_' + occurrence_id) != null)
            $('inviteguest_' + element_id + '_' + occurrence_id).style.display = 'inline-block';

          //UPDATE THE INVITE COUNT IN INVITE TAB.
          if ($('invite_count')) {
            if (responseJSON.invite_count > 0)
              $('invite_count').innerHTML = responseJSON.invite_count;
            else {

              $('invite_count').getParent('span').setStyle('display', 'none');
            }
          }
        } else if (element_id == 'tooltip')
          el_siteevent.store('tip-loaded', false)


      }
    }).send();
    }

function selectRsvpAction(options) {  

    if ($('siteevent-member-tip').innerHTML != '') {
      eventMemberTipInnerHTML = $('siteevent-member-tip').innerHTML;
    }

    $('maybe-going-rsvp').style.display = "none";
    $('not-going-rsvp').style.display = "none";
    $('siteevent-member-tip-not-going').style.display = "none";
    $('siteevent-member-tip-maybe-going').style.display = "none";
    $('going-rsvp').style.display = "block";
    $('siteevent-member-tip').style.display = "block";

    $('siteevent-member-tip').innerHTML = eventMemberTipInnerHTML;


    var parent = $('siteevent-member-tip').getParent('.layout_siteevent_invite_rsvp_siteevent');
    if (parent) {
      var rightPostion = document.body.getCoordinates().width - parent.getCoordinates().left - parent.getCoordinates().width;
      $('siteevent-member-tip').inject(document.body);
      $('siteevent-member-tip').setStyles({
        'position': 'absolute',
        'top': parent.getCoordinates().bottom,
        'right': rightPostion
      });
    }

    var divElement = new Element('div', {
      'id': 'tip-loaded',
      'styles': {
        'position': 'fixed',
        'left': '0px',
        'right': '0px', 'top': '0px', 'bottom': '0px'
      },
      'onclick': 'hideSiteeventMemberTip()'
    });
    divElement.inject(document.body);
    setTimeout(function() {
      $('rsvp-going').focus()
    }, '100');
}


function isEventFull(occurrence_id, callback, callbackOptions) {

    en4.core.request.send(new Request.JSON({
      'url': en4.core.baseUrl + 'siteevent/widget/check-event-full',
      'method' : 'get',
      data: {
        format: 'json',
        occurrence_id: occurrence_id
      },
      onComplete: function(responseJSON, responseText) {
          
        if(responseJSON.eventCapacity != 0) {
            var popupString = '<div class="global_form_popup" style="width:500px"><div class="tip"><span>' + en4.core.language.translate("You can not join this event now because capacity is full for this event. Still you want to join this event, leave this event and register in waitlist from this event profile page.") + '</span></div><div class="buttons mtop10"><button type="button" name="cancel" onclick="javascript:parent.Smoothbox.close();">' + en4.core.language.translate("Close") + '</button></div></div>';
        
            Smoothbox.open(popupString);   
        }
        else if (callback && typeof(callback) === "function") {
            callback(callbackOptions);
        } 
        
        //return responseJSON.eventCapacity;
      }
    }));
}