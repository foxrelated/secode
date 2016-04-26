var fiterbydate_active = 0; en4.siteeventcommon=1;
//GET THE MEMBER LIST ACCORDING TO THE MEMBER RSVP.
var membersRsvp =  function(memberRsvp, event_occurrence) {
	
	 event_occurrence = event_occurrence || 0;
    rsvp = memberRsvp;
    //CHECK EITHER TO SHOW ALL EVENTS OR ONLY EVENT OCCURENCE EVENT.  
    
    $('lists_popup_content').innerHTML = '<div class="siteevent_profile_loading_image"></div>';
    en4.core.request.send(new Request.HTML({
         'url' : siteeventContentUrl,
        'data' : $merge({
            'format' : 'html',
            'subject' : en4.core.subject.guid,           
            'rsvp': rsvp,
            'is_ajax_load':1,
            occurrence_id: occurrence_id
                    
  })
    }), {
        'element' : $('lists_popup_content') != null ? $('lists_popup_content').getParent().getParent() : $('siteevent_profile_members_anchor').getParent()
    });
	
}


//PAGINATE THE EVENT MEMBERS
var paginateEventMembers = function(page) {	
    
  if($('lists_popup_content')) {
		$('lists_popup_content').addClass('siteevent_carousel_loader');
		$('lists_popup_content').setStyle('height', '252px');
	}
   en4.core.request.send(new Request.HTML({
      'url' : siteeventContentUrl,
      'data' : $merge({
        'format' : 'html',
        'subject' : en4.core.subject.guid,        
        'page' : page,
        'is_ajax_load':1,
        rsvp: rsvp,
        occurrence_id: occurrence_id
      }),
    }), {
      'element' : $('lists_popup_content') != null ? $('lists_popup_content').getParent().getParent() : $('siteevent_profile_members_anchor').getParent()
    });
}


//Filter GUEST LIST VIA DATE FILTER

var filterGuestByDate = function() {
	if(typeof totalMembersOccurrence != 'undefined')
    totalMembersOccurrence = 0;
  fiterbydate_active = 1;
	$('show_eventOccurrences').innerHTML = '<div class="siteevent_profile_loading_image"></div>';
  if( $('pagination_container'))
    $('pagination_container').style.display = 'none';
	var request = new Request.HTML({
      'url' : en4.core.baseUrl + 'widget/index/mod/siteeventrepeat/name/occurrences',
      'data' : $merge(requestParams, {
        'format' : 'html',
        'subject' : en4.core.subject.guid,        
        'page' : 1,
        'is_ajax_load':1,
        firstStartDate: $('starttime-date').value,
				lastStartDate: $('endtime-date').value,
        pagination: 1
      }),
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        fiterbydate_active = 0;
        $('show_eventOccurrences').innerHTML = responseHTML;
        Smoothbox.bind($('show_eventOccurrences'));
        en4.core.runonce.trigger();
      }
    });
    request.send();
}

 var initializeCalendar = function() {  
    
   var cal_bound_start = seao_getstarttime($('starttime-date').value);
     // check end date and make it the same date if it's too
    cal_endtime.calendars[0].start = new Date( cal_bound_start );
    // redraw calendar
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
    
    // check start date and make it the same date if it's too	
    
    cal_starttime.calendars[0].start = new Date( cal_bound_start );
    // redraw calendar
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);
  }
  var cal_starttime_onHideStart = function() {
    var cal_bound_start = seao_getstarttime($('starttime-date').value);
    var cal_bound_end = seao_getstarttime($('endtime-date').value);
    // check end date and make it the same date if it's too
    cal_endtime.calendars[0].start = new Date( cal_bound_start);
    // redraw calendar
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
    
    //CHECK IF THE END TIME IS LESS THEN THE START TIME THEN CHANGE IT TO THE START TIME.
     var startdatetime = new Date(cal_bound_start);
     var enddatetime = new Date(cal_bound_end);
     if(startdatetime.getTime() > enddatetime.getTime()) {
       $('endtime-date').value = $('starttime-date').value;
       $('calendar_output_span_endtime-date').innerHTML = $('endtime-date').value;
       cal_endtime.changed(cal_endtime.calendars[0]);
     }
  }
 