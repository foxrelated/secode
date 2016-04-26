/* $Id: facebookse.js 2010-11-25 9:40:21Z SocialEngineAddOns Copyright 2009-2010 BigStep Technologies Pvt. Ltd. $ */
//JAVA SCRIPT FOR USER FACEBOOK FRIENDS SHOWING ADDING TO LIST.
//COPIED FROM USER MODULE/WIDGET/PROFILE-FRINED/INDEX.TPL
var toggleFriendsPulldown = function(event, element, user_id) {
    event = new Event(event);
    if( event.target.get('tag') != 'a' ) {
      return;
    }
    
    $$('.profile_friends_lists').each(function(otherElement) {
      if( otherElement.id == 'user_friend_lists_' + user_id ) {
        return;
      }
      var pulldownElement = otherElement.getElement('.pulldown_active');
      if( pulldownElement ) {
        pulldownElement.addClass('pulldown').removeClass('pulldown_active');
      }
    });
    if( element.hasClass('pulldown') ) {
      element.removeClass('pulldown').addClass('pulldown_active');
    } else {
      element.addClass('pulldown').removeClass('pulldown_active');
    }
    OverText.update();
  }
  
var handleFriendList = function(event, element, user_id, list_id) {
  new Event(event).stop();
  if( !element.hasClass('friend_list_joined') ) {
    // Add
    en4.user.friends.addToList(list_id, user_id);
    element.addClass('friend_list_joined').removeClass('friend_list_unjoined');
  } else {
    // Remove
    en4.user.friends.removeFromList(list_id, user_id);
    element.removeClass('friend_list_joined').addClass('friend_list_unjoined');
  }
}
  
var createFriendList = function(event, element, user_id) { 
  var list_name = element.value;
  element.value = '';
  element.blur();
  var request = en4.user.friends.createList(list_name, user_id);
  request.addEvent('complete', function(responseJSON) {
    if( responseJSON.status ) {
      var topRelEl = element.getParent();
      $$('.profile_friends_lists ul').each(function(el) {
        var relEl = el.getElement('input').getParent();
        new Element('li', {
          'html' : '\n\
<span><a href="javascript:void(0);" onclick="deleteFriendList(event, ' + responseJSON.list_id + ');">x</a></span>\n\
<div>' + list_name + '</div>',
          'class' : ( relEl == topRelEl ? 'friend_list_joined' : 'friend_list_unjoined' ) + ' user_profile_friend_list_' + responseJSON.list_id,
          'onclick' : 'handleFriendList(event, $(this), \'' + user_id + '\', \'' + responseJSON.list_id + '\');'
        }).inject(relEl, 'before');
      });
      OverText.update();
    } else {
      //alert('whoops');
    }
  });
}
var deleteFriendList = function(event, list_id) {
  event = new Event(event);
  event.stop();

  // Delete
  $$('.user_profile_friend_list_' + list_id).destroy();

  // Send request
  en4.user.friends.deleteList(list_id);
}

//END OF THIS JAVA SCRIPT CODE.
  
//THIS  FUNCTIONS SHOWS USER'S PRESENT FACEBOOK STATUS.

function showUserStatus() {
	$('initial_status').style.display = 'block';
	$('final_status').style.display = 'none';
	FB.api (
    '/me/statuses',
    { limit: 1 },
    function(response) {
    	$('initial_status').style.display = 'none';
			$('final_status').style.display = 'block';
    	$('user_status').innerHTML = ""
    	var updated_time = response.data[0].updated_time.split("+");
    	$('user_status').innerHTML = response.data[0].from.name + ': ' + response.data[0].message + '<span> on ' + humane_date(updated_time[0] + 'Z') + '</span>';
		}
  );
}
//THIS FUNCTION SHOWS USER'S RECENT FACEBOOK FEEDS.

function showWallStream() {
 $('facebookse_recent_activityfeed').innerHTML = '<div style="clear:both;text-align:center;"><img src="http://static.ak.fbcdn.net/images/loaders/indicator_white_large.gif" alt="" /></div>';
	FB.api (
    '/me/home',
    { limit: 5 },
    function(response) {
    	$('initial_status').style.display = 'none';
			$('final_status').style.display = 'block';
    	if (response.data == '') {
    		$('facebookse_recent_activityfeed').innerHTML = '<div class="tip"><span>' +  translate_facebookse['No Feed items to display. Try'] + ' <a href="javascript:void(0);" onclick= "check_fbpermission()" >' + translate_facebookse['refreshing'] + '</a>.</span></div>';
    	}
    	else {
	    	api_response(response, 1);
    	}
		}
  );
}

//THIS FUNCTION SHOWS ALL RECENTS FACEBOOK FEEDS WHEN CLICKING ON NEXT LINK.

function showWallStream_next(order, time, time_prev) {
		$('facebookse_recent_activityfeed').innerHTML = '<img src="http://static.ak.fbcdn.net/images/loaders/indicator_white_large.gif" alt="" />';
	FB.api (
    '/me/home',
    { limit: 5,
    	until: time
    	 },
    function(response) {
    	if (response.data == '') {
    		$('facebookse_recent_activityfeed').innerHTML = '<div class="tip"><span>' +  translate_facebookse['No Feed items to display. Try'] + ' <a href="javascript:void(0);" onclick= "check_fbpermission()" >' + translate_facebookse['refreshing'] + '</a>.</span></div>';
				var newdiv = document.createElement('div');
				newdiv.id = 'next_prev';
				newdiv.innerHTML = '<a class="prev" href="javascript:void(0);" onclick="showWallStream()">' +  translate_facebookse['First'] + '</a>';
				$('facebookse_recent_activityfeed').appendChild(newdiv);
    	}
    	else {
    		api_response(response);
    	}
		}
  );
}


//THIS FUNCTION SHOWS ALL RECENTS FACEBOOK FEEDS WHEN CLICKING ON PREVIOUS LINK.
    
function showWallStream_prev(order, time, time_next) {
	$('facebookse_recent_activityfeed').innerHTML = '<img src="http://static.ak.fbcdn.net/images/loaders/indicator_white_large.gif" alt="" />';
	FB.api (
    '/me/home',
    { limit: 5,
    	since: time
    	 },
    function(response) {
    	if (response.data == '') {
    		$('facebookse_recent_activityfeed').innerHTML = '<div class="tip"><span>' +  translate_facebookse['No Feed items to display. Try'] + ' <a href="javascript:void(0);" onclick= "check_fbpermission()" >' + translate_facebookse['refreshing'] + '</a>.</span></div>';
				var newdiv = document.createElement('div');
				newdiv.id = 'next_prev';
				newdiv.innerHTML = '<a class="next" href = "javascript:void(0);" onclick="showWallStream()">'+  translate_facebookse['Next'] +'</a >';
				$('facebookse_recent_activityfeed').appendChild(newdiv);
    	}
    	else {
    		api_response(response);
    	}
		}
  );
}
    
function api_response(response, onload) {
	if (!response || response.error) {
    alert('An error occurred. Please try again.');
	} 
	else {
		$('facebookse_recent_activityfeed').innerHTML = '';
    var feedarea = $('facebookse_recent_activityfeed'); 
    var result = '<table cellpadding="0" cellspacing="0" width="100%">';
    var feed_total = parseInt(response.data.length) - parseInt(1);
    for (i=0; i<response.data.length; i++) {
     if (response.data[i]) {
				if (response.data[i].properties) {

        }
				var created_date = response.data[i].created_time.split("+");
				var newdiv = document.createElement('div');
				if (typeof response.data[i].message == 'undefined') {
				if (typeof response.data[i].description != 'undefined') {
					response.data[i].message = '';
				}
				else { 	
					response.data[i].message = '';
					}
				}
				newdiv.innerHTML = '';
				var newsfeed_info = '<div class="fbconnect_userfeed_item">' +
						'<div class="profilepic"><img src="http://graph.facebook.com/' + response.data[i].from.id + '/picture" alt="" /></div>' +
						'<div class="feedcontent"><a href="http://www.facebook.com/profile.php?id='+ response.data[i].from.id + '" target="_blank">' + 
							response.data[i].from.name + 
						'</a> ';
             if (response.data[i].to) {
							newsfeed_info = newsfeed_info +' to <a href="http://www.facebook.com/profile.php?id='+ response.data[i].to.data[0].id + '" target="_blank">' + 
								response.data[i].to.data[0].name + 
							'</a> ';
            }
						newsfeed_info = newsfeed_info + '<p>' + 
						response.data[i].message +
						'</p><div class="streamattachments">';

          if (response.data[i].picture) {
						newsfeed_info = newsfeed_info + '<a href="' + response.data[i].link + '" target="_blank"><img src="' + response.data[i].picture + '" alt="" /></a>';
         }
				 if (response.data[i].name) {
						newsfeed_info = newsfeed_info + '<a href="' + response.data[i].link + '" target="_blank" >' + response.data[i].name + '</a><br />';
         }
					if (response.data[i].caption) {
						newsfeed_info = newsfeed_info + response.data[i].caption  + '<br />';
         }

					if (response.data[i].description) {
						newsfeed_info = newsfeed_info +  response.data[i].description + '<br />';
         }
         if (response.data[i].properties) {
           newsfeed_info = newsfeed_info + response.data[i].properties[0].name + ':<a href="' + response.data[i].properties[0].href  + '" target="_blank">' + response.data[i].properties[0].text + '</a><br />';
        }
					var actions = response.data[i].id.split("_");
        
					newsfeed_info = newsfeed_info + 	'</div><div class="commentable_item"><span class="ufupdate">' + humane_date(created_date[0] + 'Z') +
					'</span><a href="http://www.facebook.com/' + actions[0] + '/posts/' + actions[1] + '" target="_blank">like</a>'+ '<a href="http://www.facebook.com/' + actions[0] + '/posts/' + actions[1] + '" target="_blank">comment</a></div></div></div>';
				newdiv.innerHTML = newsfeed_info;
				feedarea.appendChild(newdiv);
			}
    }
    var url_param_time_until = urlparameter('until', response.paging.next);
    var url_param_time_since = urlparameter('since', response.paging.previous);
    url_param_time_since = url_param_time_since.replace("%3A", ':');
    url_param_time_since = url_param_time_since.replace("%3A", ':');
    url_param_time_since = url_param_time_since.replace("%2B", '+');
    url_param_time_until = url_param_time_until.replace("%3A", ':');
    url_param_time_until = url_param_time_until.replace("%3A", ':');
    url_param_time_until = url_param_time_until.replace("%2B", '+');
		var newdiv = document.createElement('div');
		newdiv.id = 'next_prev';
		if (onload) {
    	newdiv.innerHTML = '<a class="next" href = "javascript:void(0);" onclick="showWallStream_next(\'next\',\'' +url_param_time_until + '\',\'' + url_param_time_since + '\')">' +  translate_facebookse['Next'] + '</a >';
		}
		else {
			newdiv.innerHTML = '<a class="prev" href="javascript:void(0);" onclick="showWallStream_prev(\'prev\', \'' +  url_param_time_since + '\',\'' + url_param_time_until + '\')">' +  translate_facebookse['First'] + '</a><a class="next" href="javascript:void(0);" onclick="showWallStream_next(\'next\',\'' + url_param_time_until + '\',\'' + url_param_time_since + '\')">' +  translate_facebookse['Next'] + '</a >';
		}
  	feedarea.appendChild(newdiv);
	}
}

function check_fbpermission () {
	FB.login(function(response) {
  if (response.session) {
    if (response.perms) {
      // user is logged in and granted some permissions.
      // perms is a comma separated list of granted permissions
      showWallStream ();
    } else {
      // user is logged in, but did not grant any permissions
    }
  } else {
    // user is not logged in
  }
}, {perms:'status_update,publish_stream,read_stream,user_status'});


}
    
//THIS FUNCTION EXECUTE WHEN USER WRITE STH IN USER STATUS BOX.

function setStatus() {
  status1 = document.getElementById('set_status').value;
  if (status1 == '') {
		return ;
  }
	$('initial_status').style.display = 'block';
	$('final_status').style.display = 'none';
  document.getElementById('set_status').value = translate_facebookse['What\'s on your mind?'];
  FB.api({
      method: 'status.set',
      status: status1
    },
    function(response) {
      if (response == 0){
        alert(translate_facebookse['Your Facebook status could not be updated. Please try again.']);
      }
      else{ 
      	showUserStatus();
      	$('facebookse_recent_activityfeed').innerHTML = '<img src="http://static.ak.fbcdn.net/images/loaders/indicator_white_large.gif" alt="" />';
        showWallStream ();
        document.getElementById('set_status').set('class', 'sink_textarea');
        document.getElementById('share_button').style.display = 'none';
        cleanForm();
      }
    }
  );
}
	
/**
* Pretty Dates
* http://ejohn.org/files/pretty.js
*/
// Takes an ISO time and returns a string representing how
// long ago the date represents.
function humane_date(date_str){
	var time_formats = [
		[60, 'Just Now'],
		[90, '1 Minute'], // 60*1.5
		[3600, 'Minutes', 60], // 60*60, 60
		[5400, '1 Hour'], // 60*60*1.5
		[86400, 'Hours', 3600], // 60*60*24, 60*60
		[129600, '1 Day'], // 60*60*24*1.5
		[604800, 'Days', 86400], // 60*60*24*7, 60*60*24
		[907200, '1 Week'], // 60*60*24*7*1.5
		[2628000, 'Weeks', 604800], // 60*60*24*(365/12), 60*60*24*7
		[3942000, '1 Month'], // 60*60*24*(365/12)*1.5
		[31536000, 'Months', 2628000], // 60*60*24*365, 60*60*24*(365/12)
		[47304000, '1 Year'], // 60*60*24*365*1.5
		[3153600000, 'Years', 31536000], // 60*60*24*365*100, 60*60*24*365
		[4730400000, '1 Century'], // 60*60*24*365*100*1.5
	];

	var time = ('' + date_str).replace(/-/g,"/").replace(/[TZ]/g," "),
		dt = new Date,
		seconds = ((dt - new Date(time) + (dt.getTimezoneOffset() * 60000)) / 1000),
		token = ' Ago',
		i = 0,
		format;
    
	if (seconds < 0) {
		seconds = Math.abs(seconds);
		token = '';
	}

	while (format = time_formats[i++]) {
		if (seconds < format[0]) {
			if (format.length == 2) {
				return format[1] + (i > 1 ? token : ''); // Conditional so we don't return Just Now Ago
			} else {
				return Math.round(seconds / format[2]) + ' ' + format[1] + (i > 1 ? token : '');
			}
		}
	}

	// overflow for centuries
	if(seconds > 4730400000)
		return Math.round(seconds / 4730400000) + ' Centuries' + token;

	return date_str;
};

/**
* URL Parameters
* http://www.netlobo.com/url_query_string_javascript.html
*/
//THIS FUNCTION RETURNS THE URL PARAMETER SINCE AND UNTIL.
function urlparameter( param, url )
{
  param = param.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+param+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var results = regex.exec( url);
  if( results == null )
    return "";
  else
    return results[1];
}

//THIS  FUNCTIONS SHOWS ALL FACEBOOK NONSITE FRINEDS.
var limit = 5;
var start = 0;
var next = 0;
function showNonsiteFacebookFriend(prev_next) {
  limit_nonsitefriend = 6;
	if (prev_next == 'prev') {
		start = start-limit_nonsitefriend;
		next = next-limit_nonsitefriend;
	}
	else if (prev_next == 'next') {
		start = next;
		next = start + limit_nonsitefriend;
	}
	else {
		start = 0;
		next = start + limit_nonsitefriend;
	}
   
	var limit_feed = start + "," + limit_nonsitefriend + parseInt(1) ;
  var fbuser_id = $('id_fbuser').value;
	var fbuser_url = $('url_fbuser').value;
	var query = FB.Data.query('select name, pic_square from user where uid in (select uid2 from friend where uid1 ="' + fbuser_id + '") and has_added_app = 0 limit ' + limit_feed);
  query.wait(function(rows) { 
  	if ((rows.length == 0 || typeof rows.length == 'undefined' ) && prev_next != 'prev' && prev_next != 'next') { 
  		$('facebookse_invitefriends').style.display = 'none';
      $('facebookse_nonsitefbfriend').innerHTML = '<div class="tip"><span>'+  translate_facebookse['No such Facebook friends found.'] + ' </span></div>';
      return;
  	}
		$('facebookse_nonsitefbfriend').innerHTML = '';
  	var feedarea = $('facebookse_nonsitefbfriend'); 
  	if (rows.length == 0) {
  		$('facebookse_nonsitefbfriend').innerHTML = '<div class="tip"><span>'+  translate_facebookse['No such Facebook friends found.'] + ' </span></div>';
  	}
  	else {
     
			//var endcount = rows.length - 1;
      if (rows.length >= limit_nonsitefriend) {
				endcount = limit_nonsitefriend;
      }
      else {
        endcount = rows.length
      }
			
	   	for (i=0; i<endcount; i++) {
	    	//var created_date = response.data[i].created_time.split("+");
	    	var newdiv = document.createElement('div');
	    	newdiv.style.cssText = 'float:left;';
				if (typeof rows[i].pic_square == 'undefined' || rows[i].pic_square == '') {
					rows[i].pic_square = 'http://static.ak.fbcdn.net/pics/q_silhouette.gif';
				}
				newdiv.innerHTML = '<a href="' + fbuser_url + '"><img src="' + rows[i].pic_square + '" alt=""  title="Invite ' + rows[i].name + '" align="left" /></a>';
	
				feedarea.appendChild(newdiv);
				
	   	 }
  	}
    
   	 var newdiv = document.createElement('div');
		 newdiv.id = 'next_prev';
		 var innerHtml_div = '';
   	 if (rows.length != 0) {
   	 	if (start != 0) {
   	 		innerHtml_div += '<a href = "javascript:void(0);" onclick="showNonsiteFacebookFriend(\'prev\')" class="prev">' + translate_facebookse['Prev'] + '</a >';
   	 	}
      
   	 	if (rows.length > limit_nonsitefriend) {
   	 		innerHtml_div += '<a href = "javascript:void(0);" onclick="showNonsiteFacebookFriend(\'next\')" class="next">' + translate_facebookse['Next'] + '</a >';
   	 	}
   	 }
   	 else { 
   	 	if (prev_next == 'next' && start != 0) {
   	 		innerHtml_div += '<a href = "javascript:void(0);" onclick="showNonsiteFacebookFriend(\'prev\')" class="prev">'+ translate_facebookse['Prev'] +'</a >';
   	 	}
   	 	else if (prev_next == 'prev'){
   	 		innerHtml_div += '<a href = "javascript:void(0);" onclick="showNonsiteFacebookFriend(\'next\')" class="next">' + translate_facebookse['Next'] + '</a >';
   	 	}
   	 }
   	 newdiv.innerHTML = innerHtml_div;
   	 feedarea.appendChild(newdiv);
     if (start == 0 && rows.length <= limit_nonsitefriend) {
				if ($('next_prev')) {
					$('next_prev').set('style', 'display:none');
        }
     }
  	
   });
}