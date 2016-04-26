
/* $Id: core.js 9572 2011-12-27 23:41:06Z john $ */



(function() { // START NAMESPACE
var $ = 'id' in document ? document.id : window.$;

sm4.sitetagcheckin = {
	
//  like : function(action_id, comment_id, sitetagcheckin ) {
//    en4.core.request.send(new Request.JSON({
//      url : en4.core.baseUrl + 'sitetagcheckin/activity/like',
//      data : {
//        format : 'json',
//        action_id : action_id,
//        comment_id : comment_id,
//        subject : en4.core.subject.guid,
//				getUpdate: true,
//				noList: true,
//				sitetagcheckin_id: sitetagcheckin
//      }
//    }), {
//      'element' : $('activity-item-'+ sitetagcheckin +'-'+action_id),
//      'updateHtmlMode': 'comments'
//    }, {"force":true});
//  },

  like : function(action_id, comment_id, sitetagcheckin) { 
      $.mobile.loading().loader("show");
      $.ajax({
        type: "POST", 
        dataType: "json", 
        url: sm4.core.baseUrl + 'sitetagcheckin/activity/like',
        data: {
          'action_id': action_id, 
          'comment_id' :comment_id, 
          'subject' : sm4.core.subject.guid,
          'format':'json',
          getUpdate: true,
				  noList: true,
				  sitetagcheckin_id: sitetagcheckin
        },
        success:function( responseJSON, textStatus, xhr ) { 
          $.mobile.loading().loader("hide");
          if ($.type(comment_id) == null) {
            $(document).data('loaded', true);
            $('#activity-item-'+ sitetagcheckin +'-'+action_id).html(responseJSON.body);      
            sm4.core.photoGallery.set($('#activity-item-'+ sitetagcheckin +'-'+action_id));
          }
          else {
            $('#activity-item-'+ sitetagcheckin +'-'+action_id).html(responseJSON.body);
          }
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
        }.bind(this),
   
        error: function( xhr, textStatus, errorThrown ) {    
        }
      });
    },
    
//  unlike : function(action_id, comment_id, sitetagcheckin) {
//    en4.core.request.send(new Request.JSON({
//      url : en4.core.baseUrl + 'sitetagcheckin/activity/unlike',
//      data : {
//        format : 'json',
//        action_id : action_id,
//        comment_id : comment_id,
//        subject : en4.core.subject.guid,
//				getUpdate: true,
//				noList: true,
//				sitetagcheckin_id: sitetagcheckin
//      }
//    }), {
//       'element' : $('activity-item-'+ sitetagcheckin +'-'+action_id),
//       'updateHtmlMode': 'comments'
//    }, {"force":true});
//  },
  
  
  unlike : function(action_id, comment_id, sitetagcheckin) { 
      $.mobile.loading().loader("show");
      $.ajax({
        type: "POST", 
        dataType: "json",
        url: sm4.core.baseUrl + 'sitetagcheckin/activity/unlike',
        data: {
          format : 'json',
          action_id : action_id,
          comment_id : comment_id,
          subject : sm4.core.subject.guid,
          getUpdate: true,
          noList: true,
          sitetagcheckin_id: sitetagcheckin
        },
        success:function( responseJSON, textStatus, xhr ) { console.log(responseJSON.body)
          $.mobile.loading().loader("hide");
          if ($.type(comment_id) == null) {
            $(document).data('loaded', true);
            $('#activity-item-'+ sitetagcheckin +'-'+action_id).html(responseJSON.body);      
            sm4.core.photoGallery.set($('#activity-item-'+ sitetagcheckin +'-'+action_id));
          }
          else {
            $('#activity-item-'+ sitetagcheckin +'-'+action_id).html(responseJSON.body);
          }
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
        }.bind(this)
        
      });
    },

  comment : function(action_id, body, sitetagcheckin) {
    if( body.trim() == '' )
    {
      return;
    }

    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sitetagcheckin/activity/comment',
      data : {
        format : 'json',
        action_id : action_id,
        body : body,
        subject : sm4.core.subject.guid,
				getUpdate: true,
				noList: true,
				sitetagcheckin_id: sitetagcheckin
      }
    }), {
      'element' : $('activity-item-'+ sitetagcheckin +'-'+action_id),
      'updateHtmlMode': 'comments'
    }, {"force":true});
  },

  attachComment : function(formElement, sitetagcheckin, is_enter_submit) {
		var bind = this;
		if(is_enter_submit == 1){
      formElement.addEvent((Browser.Engine.trident || Browser.Engine.webkit) ? 'keydown':'keypress',function (event){
        if (event.shift && event.key == 'enter') {      	
        } else if(event.key == 'enter') {
          event.stop();    
          bind.comment(formElement.action_id.value, formElement.body.value, sitetagcheckin);
        }
      });
       // add blur event
      formElement.body.addEvent('blur',function(){
        formElement.style.display = "none";
			  if($("checkin-feed-comment-form-open-li_"+sitetagcheckin + '_'+formElement.action_id.value))
        $("checkin-feed-comment-form-open-li_"+sitetagcheckin + '_'+formElement.action_id.value).style.display = "block";
      } );
    }
    formElement.addEvent('submit', function(event){
      event.stop();
      bind.comment(formElement.action_id.value, formElement.body.value, sitetagcheckin);
    });
  },

  viewComments : function(action_id, sitetagcheckin){
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sitetagcheckin/activity/viewComment',
      data : {
        format : 'json',
        action_id : action_id,
        nolist : true,
				getUpdate: true,
			  sitetagcheckin_id: sitetagcheckin
      }
    }), {
      'element' : $('activity-item-'+ sitetagcheckin +'-'+action_id),
      'updateHtmlMode': 'comments'
    }, {"force":true});
  },

  viewLikes : function(action_id){
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sitetagcheckin/activity/viewLike',
      data : {
        format : 'json',
        action_id : action_id,
        nolist : true,
				getUpdate: true,
				sitetagcheckin_id: sitetagcheckin
      }
    }), {
      'element' : $('activity-item-'+ sitetagcheckin +'-'+action_id),
      'updateHtmlMode': 'comments'
    }, {"force":true});
  },
  
  activityremove : function(e, comment_id, action_id) { 
      if ($.type (e) != 'undefined' && $.type($(e) == 'object')) { 
        feedElement = $(e);
        var commentinfo = feedElement.data('message').split('-');
        if (commentinfo[0] == 0) {
          $.mobile.activePage.find('#popupDialog').popup("open");
          $.mobile.activePage.find('#popupDialog').parent().css('z-index', '11000')
          $.mobile.activePage.find('#popupDialog').popup("open");
        }
        else {           
          $.mobile.activePage.find('#popupDialog-Comment').parent().css('z-index', '11000')
          $.mobile.activePage.find('#popupDialog-Comment').popup("open");
        } 
        
      }
      else { 
        var commentinfo = feedElement.data('message').split('-');

        if (commentinfo[0] == 0) { 
          $('activity-item-'+sitetagcheckin_id+'-'+commentinfo[1]).remove();
          
        } else {
          $('#comment-'+commentinfo[0]).remove();
        }
        $.post(feedElement.data('url'));
      }
    },
    
    paginateFeeds : function (page, locationid, location, category, url) {
      
      $.ajax({
        type: "POST", 
        dataType: "html", 
        url: url,
        'data' : {
						'format' : 'html',
						'subject' : sm4.core.subject.guid,
						'isajax' : '1',
						'page' : page,
            'show_map' : 1,
						'location_id' : locationid,
						'location' : location,
            'category': category,
            'feed_type': 'checkins'
					},
        success:function( responseHTML, textStatus, xhr ) {
         $('#sitetagcheckin_feed_items').html(responseHTML);
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
        }
      });
      
      
    }
    
    

};

})(); // END NAMESPACE
