/* $Id: manager.js 19.10.13 08:20 jungar $ */
var HeeventManager = {
  post:function (params) {
      console.log('post');
    var arg = {
      url:params.url,
      method:'post',
      data:params.data,
      evalScripts:false,
      onSuccess:params.success,
      onFailure:params.error
    };
      console.log(params.url);
        var pay = params.url.split('/');
        var event = pay[pay.length-1].split('_');
        var eventId = event[event.length-1]

    if (params.data && params.data.format == 'json')
      new Request.JSON(arg).send();
    else
      new Request.HTML(arg).send();
  },

  get:function (params) {
    var arg = {
      url:params.url,
      method:'get',
      evalScripts:false,
      data:params.data,
      onSuccess:params.success,
      onFailure:params.error
    };

    if (params.data && params.data.format == 'json')
      new Request.JSON(arg).send();
    else
      new Request.HTML(arg).send();
  },

  fireEvent:function (node, eventName, targetSelector) {
    // Make sure we use the ownerDocument from the provided node to avoid cross-window problems
    var doc;
    if (node.ownerDocument) {
      doc = node.ownerDocument;
    } else if (node.nodeType == 9) {
      // the node may be the document itself, nodeType 9 = DOCUMENT_NODE
      doc = node;
    } else {
      throw new Error("Invalid node passed to fireEvent: " + node.id);
    }

    if (doc.createEventObject) {
      // IE-style
      var event = doc.createEventObject();
      event.synthetic = true; // allow detection of synthetic events
      if(targetSelector)
        event.target = node.getElement(targetSelector);
      node.fireEvent(eventName, event);
    } else if (node.dispatchEvent) {
      // Gecko-style approach is much more difficult.
      var eventClass = "";

      // Different events have different event classes.
      // If this switch statement can't map an eventName to an eventClass,
      // the event firing is going to fail.
      switch (eventName) {
        case "click": // Dispatching of 'click' appears to not work correctly in Safari. Use 'mousedown' or 'mouseup' instead.
        case "mousedown":
        case "mouseup":
          eventClass = "MouseEvents";
          break;

        case "focus":
        case "change":
        case "blur":
        case "select":
          eventClass = "HTMLEvents";
          break;

        case "keypress":
        case "keydown":
        case "keyup":
          eventClass = "KeyboardEvent";
          break;

        default:
          throw "fireEvent: Couldn't find an event class for event '" + eventName + "'.";
          break;
      }
      var event = doc.createEvent(eventClass);
      if(targetSelector && node.getElement(targetSelector)){
        event.target = node.getElement(targetSelector);
//        console.log(node.getElement(targetSelector));
      }
      var bubbles = eventName == "change" ? false : true;
      event.initEvent(eventName, bubbles, true); // All events created as bubbling and cancelable.
      node.dispatchEvent(event);
    }
  },

  getCurrentLocation:function (callback) {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function (position) {
//          console.log(position);
          var geocoder = new google.maps.Geocoder();
          var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
//          console.log(latlng);
          geocoder.geocode({'latLng':latlng}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
              callback(results);
            } else {
              callback('Geocoder failed due to: ' + status);
            }
          });

        });
    }
  },

  getImageDimensions:function (path, callback) {
    var img = new Image();
    img.onload = function () {
      var dim = {
        width:this.width,
        height:this.height
      };
      callback(dim);
    };
    img.src = path;
  },

  initActionsOn: function(element) {


    element.getElements('.events_action').removeEvent('click').addEvent('click', function (e) {
      var target = e.target;
      if (target.tagName == 'I'){
        target = target.getParent();
      }
      if (target.tagName == 'BUTTON' && target.get('href')) {
        var btn = target;
        var url = btn.get('href');
        var confirmation = btn.get('confirm');
        if (confirmation && !confirm(confirmation)) {
          return;
        }
        var data = {};
        var doToggle = function (el) {
          var toggleText = el.get('toggle-text');
          if (toggleText) {
            el.set('toggle-text', el.get('html'));
            el.set('html', toggleText);
          }

          var toggleHref = el.get('toggle-href');
          if (toggleHref) {
            el.set('toggle-href', el.get('href'));
            el.set('href', toggleHref);
          }

          if (el.hasClass('disabled')) {
            el.removeClass('disabled');
            el.erase('disabled');
          } else {
            el.addClass('disabled');
            el.set('disabled', 'disabled');
          }
        };

        if (btn.hasClass('rsvp_btn')) { // RSVP Buttons {
          var oldValue = parseInt(this.getElements('.active').get('value'));
          this.getElements('.rsvp_btn').removeClass('active').removeClass('disabled');

          btn.addClass('active disabled');
          this.addClass('member');
          var joinBtn = this.getElement('.join_btn');
          var value = parseInt(btn.get('value'));
          if (joinBtn) {
            var rugo = new Element('div');
            rugo.set('text', joinBtn.get('text'));
            joinBtn.grab(rugo, 'before');
            joinBtn.dispose();
            var rsvpBtns = this.getElements('.rsvp_btn');
            var l = rsvpBtns.length;
            for(var i = 0; i < l; i ++){
              var rsvpBtn = rsvpBtns[i];
              rsvpBtn.set('href', rsvpBtn.get('alt-href'));
              rsvpBtn.set('alt-href', '');
            }

          } else {
            data.format = 'json';
          }
          var guestsEl = $('guests_' + this.get('event-guid'));
          if (guestsEl) {
            var guests = parseInt(guestsEl.get('guest-count'));

            if (!oldValue && value)
              guests++;
            else if (oldValue && !value)
              guests--;
            guestsEl.set('text', en4.core.language.translate(['%s guest', '%s guests', guests], guests));
            guestsEl.set('guest-count', guests);


          }

           var guestsEl2 = $('2guests_' + this.get('event-guid'));
          if (guestsEl2) {
            var guests2 = parseInt(guestsEl2.get('guest-count'));

            if (!oldValue && value)
              guests2--;
            else if (oldValue && !value)
              guests2++;
              guestsEl2.set('text', en4.core.language.translate('Available ' + guests2));
              guestsEl2.set('guest-count', guests2);


          }
          data.rsvp = value;
          data.option_id = value;
        } // } RSVP Buttons
        else if (btn.hasClass('invite_btn')) { // Request Invite/Cancel Invite Request Buttons {
          var toggle = this.getElement('.disabled');
         /* doToggle(toggle);
          doToggle(btn);*/
        } // } Request Invite/Cancel Invite Request Buttons
        else if (btn.hasClass('confirm_btn')) {// Accept Invite/Reject Invite Buttons {
          if (btn.get('value') == 'accept') {
            data.format = 'html';
            data.rsvp = 2;
            data.submit = '';
            this.getElements('.confirm_btn').dispose();
           /* doToggle(this.getElements('.rsvp_btn'));*/
          } else {

          }
        } // } Accept Invite/Reject Invite Buttons
        else if (btn.hasClass('option_btn')) {// Accept Invite/Reject Invite Buttons {
          if (btn.get('value') == 'delete') {
            this.getParent('li').dispose();
          } else {

          }
        }
        else if(btn.hasClass('ticket_btn')) {
            var oldValue = parseInt(this.getElements('.active').get('value'));
            //this.getElements('.rsvp_btn').removeClass('active').removeClass('disabled');

            //btn.addClass('active disabled');
            //this.addClass('member');
            var joinBtn = this.getElement('.join_btn');
            var value = parseInt(btn.get('value'));
            if (joinBtn) {
                var rugo = new Element('div');
                rugo.set('text', joinBtn.get('text'));
                joinBtn.grab(rugo, 'before');
                joinBtn.dispose();
                var rsvpBtns = this.getElements('.rsvp_btn');
                var l = rsvpBtns.length;
                for(var i = 0; i < l; i ++){
                    var rsvpBtn = rsvpBtns[i];
                    rsvpBtn.set('href', rsvpBtn.get('alt-href'));
                    rsvpBtn.set('alt-href', '');
                }

            } else {
                data.format = 'json';
            }
            var guestsEl = $('guests_' + this.get('event-guid'));
            if (guestsEl) {
                var guests = parseInt(guestsEl.get('guest-count'));
                if (!oldValue && value)
                    guests++;
                else if (oldValue && !value)
                    guests--;
                guestsEl.set('text', en4.core.language.translate(['%s guest', '%s guests', guests], guests));
                guestsEl.set('guest-count', guests);
            }
            data.rsvp = value;
            data.option_id = value;
            var ev_id=btn.get('data-id');
            //Smoothbox.open($('ticket_form' + ev_id));
            showBuy_form(ev_id);
                 click_event = function (e) {
                data.quantity = parseInt($('ticket_quantity_'+ev_id).value);
                data.ev_id = ev_id;
                data.price = parseInt($('price_heevent'+ev_id).value);
                submitHETicketsForm(url,data)
            }
            $$('.ticket_buy'+ev_id).removeEvent('click',click_event).addEvent('click',click_event );
            return;

        }

        var params = {
          url:url,
          data:data,
          success:function (response) {
//            console.log(response);
          },
          error:function (error) {
//            console.log(error);
          }
        };
        _hem.post(params);
//        console.log(btn.get('href'));
      }
    });
  },
  getFormValues: function(form){
    var params = form.toQueryString().parseQueryString();
    for(var param in params){
      var value = params[param];
      if('array' == $type(value)){
        params[param] = value.pop();
      }
      var floatVal = parseFloat(params[param]);
      if(!isNaN(floatVal) && params[param].length == (floatVal+'').length){
        params[param] = floatVal;
      }
    }
    return params;
  },

  ajaxPagination:function (paginatorEl, content) {
    if (paginatorEl) {
      var cb = function (e) {
        if (e.target.tagName == 'A') {
          paginatorEl.removeEvents('click', cb);
          content.setStyle('opacity', '.5');
          content.setStyle('filter', 'alpha(opacity=50)');
          var a = e.target;
          new Request.HTML({
            url:a.href,
            method:'get',
            data:{format:"html"},
            evalScripts:false,
            onSuccess:function (a, b, html, js) {
              content.innerHTML = html;
              eval(js);
              content.setStyle('opacity', '1');
//              content.setStyle('filter', 'alpha(opacity=100)');
              window.scrollTo(0,0);
            }
          }).send();
          e.preventDefault();
        }
      };
      paginatorEl.addEvent('click', cb);
    }
  },
  ajaxSearch:function (form, content) {
    if(form){
      var data = this.getFormValues(form);
      data.format = 'html';
      content.setStyle('opacity', '.5');
//      content.setStyle('filter', 'alpha(opacity=50)');
      if(form.request && form.request.isRunning()){
        form.request.cancel();
      }
      form.request = new Request.HTML({
        url:window.location.href,
        method:'get',
        data:data,
        evalScripts:false,
        onSuccess:function (a, b, html, js) {
          content.innerHTML = html;
          eval(js);
          content.setStyle('opacity', '1');
          content.setStyle('filter', 'alpha(opacity=100)');
        }
      }).send();
    }
    return false;
  }
};
var _hem = HeeventManager;
window.addEvent('domready', function () {
  if(navigator.userAgent.indexOf('MSIE ') > 0) // Identify IE
    document.body.addClass('heevent-ie');
  var profilePage = $('global_page_heevent-profile-index');
  if (profilePage) {
    var tabsWidget = profilePage.getElement('.layout_core_container_tabs');
    if (tabsWidget) {
      tabsWidget.addClass('heevent-block');
      var tabContainers = tabsWidget.getElements('.generic_layout_container');
      tabContainers.addClass('heevent-widget');
      var childCounts = tabsWidget.getElement('.tabs_alt').getElements('span');
      var l = childCounts.length;
      for (var k = 0; k < l; k++) {
        var childCount = childCounts[k];
        var count = parseInt(childCount.innerHTML.substr(1));
        count = count ? count : 0;
        childCount.set('text', count);
        childCount.addClass('heevent-badge');
      }
      var tabContsChildren = tabContainers.getChildren();
      l = tabContsChildren.length;
      for (var i = 0; i < l; i++) {
        var tabContChildren = tabContsChildren[i];
        tabContChildren.addClass('heevent-widget-inner');
      }
    }
  }
  try{
  _hem.initActionsOn($(document.body));
  } catch(e){
//    console.log(e);
  }

});
function price_changer(d, id){
    var count = parseInt(d.value)+1
var p = parseInt($('price_heevent'+id).value);
    $$('.heticket_price_'+id+' #price_tag').set('html',p*count);
}

function submitHETicketsForm(url,data){
    var params = {
        url:url,
        data:data,
        success:function (response) {
//            console.log(response);
        },
        error:function (error) {
//            console.log(error);
        }
    };
HeeventManager.post(params);
    hideBuy_form(data.ev_id);
}
function showBuy_form(id){
    var bg = $$('.background_buy_form'+id);
    bg.setStyle('display', 'block');
    var form = $('heevent-buy-form'+id);
    form.show();
    setTimeout(function(){
        form.setStyle('transform', 'translate(0, 0)');
    }, 20);

}
function hideBuy_form(id){
    $$('.background_buy_form'+id).hide();
    $('heevent-buy-form'+id).set('style', '');
}
function printDiv(divID) {
    //Get the HTML of div
    var divElements = document.getElementById(divID).innerHTML;
    //Get the HTML of whole page
    var oldPage = document.body.innerHTML;

    //Reset the page's HTML with div's HTML only
    document.body.innerHTML =
        "<html><head><title></title></head><body>" +
            divElements + "</body>";

    //Print Page
    window.print();

    //Restore orignal HTML
    document.body.innerHTML = oldPage;


}