
/* $Id: core.js 9572 2011-12-27 23:41:06Z john $ */



(function() { // START NAMESPACE
var $ = 'id' in document ? document.id : window.$;

en4.sitetagcheckin = {
	
  like : function(action_id, comment_id, sitetagcheckin ) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sitetagcheckin/activity/like',
      data : {
        format : 'json',
        action_id : action_id,
        comment_id : comment_id,
        subject : en4.core.subject.guid,
				getUpdate: true,
				noList: true,
				sitetagcheckin_id: sitetagcheckin
      }
    }), {
      'element' : $('activity-item-'+ sitetagcheckin +'-'+action_id),
      'updateHtmlMode': 'comments'
    }, {"force":true});
  },

  unlike : function(action_id, comment_id, sitetagcheckin) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sitetagcheckin/activity/unlike',
      data : {
        format : 'json',
        action_id : action_id,
        comment_id : comment_id,
        subject : en4.core.subject.guid,
				getUpdate: true,
				noList: true,
				sitetagcheckin_id: sitetagcheckin
      }
    }), {
       'element' : $('activity-item-'+ sitetagcheckin +'-'+action_id),
       'updateHtmlMode': 'comments'
    }, {"force":true});
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
        subject : en4.core.subject.guid,
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

};

})(); // END NAMESPACE
