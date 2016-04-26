
/* $Id: core.js 2010-08-17 9:40:21Z SocialEngineAddOns Copyright 2009-2010 BigStep Technologies Pvt. Ltd. $ */

en4.suggestion = {

  };

en4.suggestion.mixs = {
	
  };

en4.suggestion.friends = {
	
  }

function setCountingLimit(modName, divId, tem_display_count) {
  switch(modName)	{
    case 'album':
      if(divId) {
        var count	=	album_wid_content_count;
      }
      if(tem_display_count) {
        album_get_display_count =  album_get_display_count - tem_display_count;
        document.getElementById(modName + '_get_count').innerHTML = album_get_display_count;
      }
      break;
    case 'blog':
      if(divId) {
        var count	=	blog_wid_content_count;
      }
      if(tem_display_count) {
        blog_get_display_count =  blog_get_display_count - tem_display_count;
        document.getElementById(modName + '_get_count').innerHTML = blog_get_display_count;
      }
      break;
    case 'classified':
      if(divId) {
        var count	=	classified_wid_content_count;
      }
      if(tem_display_count) {
        classified_get_display_count =  classified_get_display_count - tem_display_count;
        document.getElementById(modName + '_get_count').innerHTML = classified_get_display_count;
      }
      break;
    case 'document':
      if(divId) {
        var count	=	document_wid_content_count;
      }
      if(tem_display_count) {
        document_get_display_count =  document_get_display_count - tem_display_count;
        document.getElementById(modName + '_get_count').innerHTML = document_get_display_count;
      }
      break;
    case 'event':
      if(divId) {
        var count	=	event_wid_content_count;
      }
      if(tem_display_count) {
        event_get_display_count =  event_get_display_count - tem_display_count;
        document.getElementById(modName + '_get_count').innerHTML = event_get_display_count;
      }
      break;
    case 'forum':
      if(divId) {
        var count	=	forum_wid_content_count;
      }
      if(tem_display_count) {
        forum_get_display_count =  forum_get_display_count - tem_display_count;
        document.getElementById(modName + '_get_count').innerHTML = forum_get_display_count;
      }
      break;
    case 'group':
      if(divId) {
        var count	=	group_wid_content_count;
      }
      if(tem_display_count) {
        group_get_display_count =  group_get_display_count - tem_display_count;
        document.getElementById(modName + '_get_count').innerHTML = group_get_display_count;
      }
      break;
    case 'list':
      if(divId) {
        var count	=	list_wid_content_count;
      }
      if(tem_display_count) {
        list_get_display_count =  list_get_display_count - tem_display_count;
        document.getElementById(modName + '_get_count').innerHTML = list_get_display_count;
      }
      break;
    case 'music':
      if(divId) {
        var count	=	music_wid_content_count;
      }
      if(tem_display_count) {
        music_get_display_count =  music_get_display_count - tem_display_count;
        document.getElementById(modName + '_get_count').innerHTML = music_get_display_count;
      }
      break;
    case 'poll':
      if(divId) {
        var count	=	poll_wid_content_count;
      }
      if(tem_display_count) {
        poll_get_display_count =  poll_get_display_count - tem_display_count;
        document.getElementById(modName + '_get_count').innerHTML = poll_get_display_count;
      }
      break;
    case 'video':
      if(divId) {
        var count	=	video_wid_content_count;
      }
      if(tem_display_count) {
        video_get_display_count =  video_get_display_count - tem_display_count;
        document.getElementById(modName + '_get_count').innerHTML = video_get_display_count;
      }
      break;
    case 'sitepage':
      if(divId) {
        var count	=	sitepage_wid_content_count;
      }
      if(tem_display_count) {
        sitepage_get_display_count =  sitepage_get_display_count - tem_display_count;
        document.getElementById(modName + '_get_count').innerHTML = sitepage_get_display_count;
      }
      break;
    case 'sitebusiness':
      if(divId) {
        var count	=	sitebusiness_wid_content_count;
      }
      if(tem_display_count) {
        sitebusiness_get_display_count =  sitebusiness_get_display_count - tem_display_count;
        document.getElementById(modName + '_get_count').innerHTML = sitebusiness_get_display_count;
      }
      break;
    case 'sitegroup':
      if(divId) {
        var count	=	sitegroup_wid_content_count;
      }
      if(tem_display_count) {
        sitegroup_get_display_count =  sitegroup_get_display_count - tem_display_count;
        document.getElementById(modName + '_get_count').innerHTML = sitegroup_get_display_count;
      }
      break;
    case 'mix':
      var count	=	mix_wid_content_count;
      document.getElementById(divId).style.opacity = 100;
      break;
    case 'friend':
    case 'friendfewfriend':
      if(divId) {
        var count	=	friend_wid_content_count;
        document.getElementById(divId).style.opacity = 100;
      }
      if(tem_display_count) {
        friend_get_display_count =  friend_get_display_count - tem_display_count;
        document.getElementById('friend_get_count').innerHTML = friend_get_display_count;
      }
      break;
    case 'explore':
      var count	=	explore_wid_content_count;
      document.getElementById(divId).style.opacity = 100;
      break;
    case 'findFriend':
      var count	=	findFriend_wid_content_count;
      document.getElementById(divId).style.opacity = 100;
      break;
    case 'recipe':
      if(divId) {
        var count	=	recipe_wid_content_count;
      }
      if(tem_display_count) {
        recipe_get_display_count =  recipe_get_display_count - tem_display_count;
        document.getElementById(modName + '_get_count').innerHTML = recipe_get_display_count;
      }
      break;
    default:
      var temp_count_variable_name = window[modName + '_wid_content_count'];
      var temp_display_count = window[modName +  '_get_display_count'];
      if(divId) {
        var count = temp_count_variable_name;
      }
      if(tem_display_count) {
        temp_display_count =  temp_display_count - tem_display_count;
        document.getElementById(modName + '_get_count').innerHTML = temp_display_count;
      }
      break;
  }
  if(count) {
    return	count;
  }
}

var takeContent	=	function( mod_id, mod_name, div_id, modFlag, is_middleLayoutEnabled )	{
  // This is the "Process Flag", which value will be 1 If already any other process is going on and loggden user click on cross ( Cancel content ). Its value will be 0 if no process is going on.
  if( processFlag == 1 ){
    return;
  }
  if( document.getElementById(processFlagDiv) ){
    document.getElementById(processFlagDiv).style.opacity = 100;
  }
  processFlag = 1;
  processFlagDiv = div_id;
  fade(div_id, 2); // Calling function for "Fade-out".
  var widget_content_count = setCountingLimit(mod_name, div_id, 0);
  en4.core.request.send(new Request.HTML({
    url : en4.core.baseUrl + 'suggestion/main/get-content',
    data : {
      format : 'html',
      mod_name:	mod_name,
      mod_id:	mod_id,
      widget_content_count:widget_content_count,
      display_mod_str: display_mod_str,
      modFlag: modFlag,
      div_id: div_id,
      is_middleLayoutEnabled: is_middleLayoutEnabled
    },
    'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
    {
      // "See All" link which are showing on "PeopleYouMayKnow widgets". should be removed when there are no responced are return.
      if( (mod_name == 'friend') && (friend_wid_content_count == 0) ) {
	if( document.getElementById('pymk_see_al') ) {
	  document.getElementById('pymk_see_al').innerHTML = '';
	}
      }

      document.getElementById(div_id).innerHTML	=	responseHTML; // Set responce in provided div.

      fade(div_id, -2); // Calling function for "Fade-in".
      processFlag = 0;

      // Only run for "Find Friend Suggestion" & "Explore Suggestion" becouse here we show message not display in user pages.
      if( (responseElements == '') && ((mod_name == 'findFriend') || (mod_name == 'explore') || (is_middleLayoutEnabled == 1) ) ) {
				document.getElementById(div_id).innerHTML = '<div class="seaocore_tip">' + en4.core.language.translate('Sorry, no more suggestions.') + '</div>';
      }
    }
  }))
}

// close the popup.
var cancelPopup = function ()
{
  parent.Smoothbox.close();
};

var url = '';
window.addEvent('domready', function () {
  if (typeof action_module != 'undefined') {
    url = en4.core.baseUrl + 'suggestion/index/show-popup';
    document.getElementById(action_module+'_members_search_inputd').addEvent('blur', function () {
      if (memberSearch != '') {
        document.getElementById(action_module+'_members_search_inputd').value = memberSearch;
      }
      else {
        document.getElementById(action_module+'_members_search_inputd').value = en4.core.language.translate('Search Members');
      }
    });

    document.getElementById(action_module+'_members_search_inputd').addEvent('focus', function () {
      if (memberSearch != '') {
        document.getElementById(action_module+'_members_search_inputd').value = memberSearch;
      }
      else {
        document.getElementById(action_module+'_members_search_inputd').value = '';
        document.getElementById('newcheckbox').style.display = 'block';
      }
      if (show_selected == 1 && friends_count > 0) {
        show_selected = 0;
        paginateMembers (1);
      }

    });
  }
});

//UPDATING THE CURRENT FRIEND PAGE.
function update_html () {
	var tempCount = '';
	// suggestion_string_temp: This variable contained the complete string of selected friend-ids.
	// tempSelectedFriend: Contain selected friend-ids, which are going to display on page.
  if (suggestion_string_temp != '') {
    var suggestion_string_2 = tempSelectedFriend.split(',');
    if (suggestion_string_2.length > 0) {
      for (i = 0; i< suggestion_string_2.length; i++) {
        var checkbox_info = suggestion_string_2[i];
					if( checkbox_info ) {
						if( document.getElementById('check_' + checkbox_info) ){
							document.getElementById('check_' + checkbox_info).className = "suggestion_pop_friend selected";
							friends_count--;
							moduleSelect(checkbox_info);
						}else {


							tempCount = suggestion_string_temp.search(checkbox_info);
							if( tempCount < 0 ) {
								suggestion_string_temp  += ',' + checkbox_info;
							}


						}
					}
      }
			suggestion_string = suggestion_string_temp;
    }
    return false;
  }
}

//GETTING THE SEARCHED FRIENDS.
function  show_searched_friends (request, event) {
  if (typeof event != 'undefined' && event.keyCode == 13) {
    return false;
  }
  if (request == 0) {
    show_selected = 0;
    document.getElementById('show_all').className = 'selected';
    document.getElementById('selected_friends').className = '';
  }
  else {
    document.getElementById('show_all').className = '';
    document.getElementById('selected_friends').className = 'selected';
  }
  var current_search = trim( document.getElementById(action_module+'_members_search_inputd').value );
  if (show_selected == 1) {
    current_search = '';
    memberSearch = '';
    document.getElementById(action_module+'_members_search_inputd').value = en4.core.language.translate('Search Members');
  }
  var request = new Request.HTML({
    url : url,
    method: 'GET',
    data : {
      format : 'html',
      'task': 'ajax',
      searchs : current_search,
      'selected_checkbox':suggestion_string,
      'show_selected':show_selected,
      'action_id':action_session_id,
      'notification_type':notification_type,
      'entity':entity,
      'item_type':item_type,
      'findFriendFunName':findFriendFunName,
      'notificationType':notificationType,
      'modError':modError,
      'modName':modName,
      'modItemType':modItemType,
      'selected_friend_flag':request,
      'getArray':paginationArray
    },
    'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
    {
      document.getElementById('main_box').innerHTML = responseHTML;
      update_html();
      if( document.getElementById('newcheckbox') ) {
        if( paginationArray[memberPage] && (paginationArray[memberPage] == 1) && (dontHaveResult == 1) ) {
          popupFlag = 1;
          document.getElementById('newcheckbox').addClass('selected');
        }else {
          popupFlag = 0;
          document.getElementById('newcheckbox').removeClass('selected');
        }
        document.getElementById('newcheckbox').setProperty('onclick', 'selectAllFriend("' + displayUserStr + '")');
      }
    }
  });
  request.send();
  return false;
}

//PAGINATING FRIENDS PAGE.
function paginateMembers (page) {
  if (show_selected == 1) {
    document.getElementById('show_all').className = '';
    document.getElementById('selected_friends').className = 'selected';
  }
  else {
    document.getElementById('show_all').className = 'selected';
    document.getElementById('selected_friends').className = '';
  }
  var request = new Request.HTML({
    url : url,
    method: 'GET',
    data : {
      format : 'html',
      'task': 'ajax',
      searchs : memberSearch,
      'selected_checkbox':suggestion_string,
      'page':page,
      'action_id':action_session_id,
      'show_selected':show_selected,
      'notification_type':notification_type,
      'entity':entity,
      'item_type':item_type,
      'findFriendFunName':findFriendFunName,
      'notificationType':notificationType,
      'modError':modError,
      'modName':modName,
      'modItemType':modItemType,
      'getArray':paginationArray
    },
    'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
    {
      document.getElementById('main_box').innerHTML = responseHTML;
      update_html();
      if( document.getElementById('newcheckbox') ) {
        if( paginationArray[memberPage] && (paginationArray[memberPage] == 1) && (dontHaveResult == 1) ) {
          popupFlag = 1;
          document.getElementById('newcheckbox').addClass('selected');
        }else {
          popupFlag = 0;
          document.getElementById('newcheckbox').removeClass('selected');
        }
        document.getElementById('newcheckbox').setProperty('onclick', 'selectAllFriend("' + displayUserStr + '")');
      }
    }
  });
  request.send();
  return false;
}


// Function call when click on "Friend Div"
function moduleSelect ( friend_id )
{
  // If "friend_id" are not in array, it means that "friend-div" should be selected other vise should not be selected.
  if( SelectedPopupContent[friend_id] ) { // Should not be selected
    friends_count--;
    delete SelectedPopupContent[friend_id];
		suggestion_string = suggestion_string.replace(',' +  friend_id, "");
		// Class: Which are showing friend is selected or not in the popups.
    document.getElementById('check_' + friend_id ).className = "suggestion_pop_friend";
    // "Select All - Checkbox" should be disabled when ever all selected checkbox is unchecked.
    if( friends_count == 0 ) {
      popupFlag = 0;
      document.getElementById('newcheckbox').removeClass('selected');
    }
  }else { // Should be selected
    SelectedPopupContent[friend_id] = 1;
    friends_count++;
		suggestion_string  += ',' + friend_id;
		// Class: Which are showing friend is selected or not in the popups.
    document.getElementById('check_' + friend_id ).className = "suggestion_pop_friend selected";
  }
  if( friends_count < 0 ){
    friends_count = 0;
  }
  
	if(select_text_flag) {
		document.getElementById('selected_friends').innerHTML = select_text_flag +  ' (' + friends_count + ')';
	}else {
		document.getElementById('selected_friends').innerHTML = en4.core.language.translate('Selected') +  ' (' + friends_count + ')';
	}
}

var show_all = function () {
  memberSearch = '';
  document.getElementById('newcheckbox').style.display = 'block';
  document.getElementById(action_module + '_members_search_inputd').value = en4.core.language.translate('Search Members');
  if (show_selected == 1) {
    show_selected = 0;
    paginateMembers (1);
  }
  else {
    paginateMembers (memberPage);
  }
  document.getElementById('newcheckbox').style.display = 'block';
}

// When click on "Selected" then we are calling this function. It will return all selcted friend list.
var selected_friends = function () {
  document.getElementById('newcheckbox').style.display = 'none';
  if (friends_count > 0) {
    //tem_friend_count = friends_count;
    memberSearch = '';
    show_selected = 1;
    show_searched_friends(1);
    document.getElementById('newcheckbox').style.display = 'none';
  }
  else if (show_selected == 1) {
    show_all ();
  }
}


//SUBMIT THE FORM IF USER HAS SELECTED ATLEAST ONE FRIEND. 
function doCheckAll()
{
  var suggestion_string_1 = suggestion_string.split(',');
  if(suggestion_string_1.length == 1)
  {
    document.getElementById('check_error').innerHTML = '<ul class="form-errors"><li><ul class="errors"><li>' + en4.core.language.translate('Please select at-least one entry above to send suggestion to.') + '</li></ul></li></ul>';
  }
  else
  {
    document.getElementById('hidden_checkbox').innerHTML = "";
    var hidden_checkbox = '';
    for ($i = 1;$i < suggestion_string_1.length; $i++ ) {
      // var checked_id_temp = suggestion_string_1[$i].split('-');
			var checked_id_temp = suggestion_string_1[$i];
      if( checked_id_temp ) {
        delete SelectedPopupContent[1];
        hidden_checkbox = hidden_checkbox + '<input type="hidden" name="check_' + checked_id_temp + '"  value="' + checked_id_temp + '"/>';
      }
    }
    document.getElementById('hidden_checkbox').innerHTML = hidden_checkbox;
    document.suggestion.submit();
  }
}


function trim(str, chars) { 
  return ltrim(rtrim(str, chars), chars);
}
 
function ltrim(str, chars) {
  chars = chars || "\\s";
  return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}
 
function rtrim(str, chars) {
  chars = chars || "\\s";
  return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}


// When click on "Select All" then we are calling this function which selected all the friend which are showing on page.
function selectAllFriend ( friendStr ) {
  if( popupFlag == 0 ){
    popupFlag = 1;
  }else{
    popupFlag = 0;
  }
  var subcatss = friendStr . split("::");
  for (var i=0; i < subcatss.length; ++i){
    var friend_id = subcatss[i].split("_");
    var check_name_new = 'check_' +  friend_id;
    newmoduleSelect(check_name_new, friend_id);
  }

  paginationArray[memberPage] = popupFlag;
}


function newmoduleSelect (check_name, friend_id)
{
  if(document.getElementById(check_name)) {
		// popupFlag: Variable if it value is 0 then "Removed the Selection" and if 1 "Friend Selected".
    if( popupFlag ) {// Check All
      if( !SelectedPopupContent[friend_id] ) {
        moduleSelect(friend_id);
      }
      document.getElementById('newcheckbox').addClass('selected');
    }else {// Uncheck All
      if( SelectedPopupContent[friend_id] ) {
        moduleSelect(friend_id);
      }
      document.getElementById('newcheckbox').removeClass('selected');
    }
  }
}

function getPopup( popupPath ) {
  Smoothbox.open(popupPath);
}

function suggestionAddFriend( mod_name, friendId, blockDivId, noneDiveId, parentDivId, flag, isMiddle ) {
  // flag - 0: When click on first time on "Add Friend".
  // flag - 1: When click on "Confirm".
  // flag - 2: When click on "Undo".
  var browserName=navigator.appName;

  // If there are "friend_id" vvailable then it mens that friend request should be send and success message should be show in widgets.
  if( flag == 0 ) {
    if (browserName=="Microsoft Internet Explorer") {
      if(document.getElementById('item_photo_' + mod_name + '_' + friendId)) {
        document.getElementById('item_photo_' + mod_name + '_' + friendId).style.filter =  'alpha(opacity=' + 20 + ')';
      }
      if(document.getElementById('item_cancel_' + mod_name + '_' + friendId)) {
        document.getElementById('item_cancel_' + mod_name + '_' + friendId).style.filter =  'alpha(opacity=' + 20 + ')';
      }
      if(document.getElementById('item_title_' + mod_name + '_' + friendId)) {
        document.getElementById('item_title_' + mod_name + '_' + friendId).style.filter =  'alpha(opacity=' + 20 + ')';
      }
      if(document.getElementById('mutual_friend_' + mod_name + '_' + friendId)) {
        document.getElementById('mutual_friend_' + mod_name + '_' + friendId).style.filter = 'alpha(opacity=' + 20 + ')';
      }
    }else {
      if(document.getElementById('item_photo_' + mod_name + '_' + friendId)) {
        document.getElementById('item_photo_' + mod_name + '_' + friendId).style.opacity = 0.2;
      }
      if(document.getElementById('item_cancel_' + mod_name + '_' + friendId)) {
        document.getElementById('item_cancel_' + mod_name + '_' + friendId).style.opacity = 0.2;
      }
      if(document.getElementById('item_title_' + mod_name + '_' + friendId)) {
        document.getElementById('item_title_' + mod_name + '_' + friendId).style.opacity = 0.2;
      }
      if(document.getElementById('mutual_friend_' + mod_name + '_' + friendId)) {
        document.getElementById('mutual_friend_' + mod_name + '_' + friendId).style.opacity = 0.2;
      }
    }
  }else if( (flag == 2) || (flag == 1) ) {
    if (browserName=="Microsoft Internet Explorer") {
      if(document.getElementById('item_photo_' + mod_name + '_' + friendId)) {
        document.getElementById('item_photo_' + mod_name + '_' + friendId).style.filter = 'alpha(opacity=' + 100 + ')';
      }
      if(document.getElementById('item_cancel_' + mod_name + '_' + friendId)) {
        document.getElementById('item_cancel_' + mod_name + '_' + friendId).style.filter = 'alpha(opacity=' + 100 + ')';
      }
      if(document.getElementById('item_title_' + mod_name + '_' + friendId)) {
        document.getElementById('item_title_' + mod_name + '_' + friendId).style.filter = 'alpha(opacity=' + 100 + ')';
      }
      if(document.getElementById('mutual_friend_' + mod_name + '_' + friendId)) {
        document.getElementById('mutual_friend_' + mod_name + '_' + friendId).style.filter = 'alpha(opacity=' + 100 + ')';
      }
    }else {
      if(document.getElementById('item_photo_' + mod_name + '_' + friendId)) {
        document.getElementById('item_photo_' + mod_name + '_' + friendId).style.opacity = 100;
      }
      if(document.getElementById('item_cancel_' + mod_name + '_' + friendId)) {
        document.getElementById('item_cancel_' + mod_name + '_' + friendId).style.opacity = 100;
      }
      if(document.getElementById('item_title_' + mod_name + '_' + friendId)) {
        document.getElementById('item_title_' + mod_name + '_' + friendId).style.opacity = 100;
      }
      if(document.getElementById('mutual_friend_' + mod_name + '_' + friendId)) {
        document.getElementById('mutual_friend_' + mod_name + '_' + friendId).style.opacity = 100;
      }
    }
  }

  if( flag != 1 ) {
    if( (blockDivId != 0) ){
      document.getElementById(blockDivId).style.display = 'block';
    }
    if( (noneDiveId != 0) ){
      document.getElementById(noneDiveId).style.display = 'none';
    }
  }

  if( flag == 1 ) {
    var friendUrl = en4.core.baseUrl + 'suggestion/index/add-friend';
    en4.core.request.send(new Request.HTML({
      url : friendUrl,
      data : {
        format: 'html',
        friendId: friendId
      },
      'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
      {
        document.getElementById(noneDiveId).innerHTML = responseHTML;
        if (browserName=="Microsoft Internet Explorer") {
          if(document.getElementById('item_photo_' + mod_name + '_' + friendId)) {
            document.getElementById('item_photo_' + mod_name + '_' + friendId).style.filter = 'alpha(opacity=' + 100 + ')';
          }
          if(document.getElementById('item_cancel_' + mod_name + '_' + friendId)) {
            document.getElementById('item_cancel_' + mod_name + '_' + friendId).style.filter = 'alpha(opacity=' + 100 + ')';
          }
          if(document.getElementById('item_title_' + mod_name + '_' + friendId)) {
            document.getElementById('item_title_' + mod_name + '_' + friendId).style.filter = 'alpha(opacity=' + 100 + ')';
          }
          if(document.getElementById('mutual_friend_' + mod_name + '_' + friendId)) {
            document.getElementById('mutual_friend_' + mod_name + '_' + friendId).style.filter = 'alpha(opacity=' + 100 + ')';
          }
        }else {
          if(document.getElementById('item_photo_' + mod_name + '_' + friendId)) {
            document.getElementById('item_photo_' + mod_name + '_' + friendId).style.opacity = 100;
          }
          if(document.getElementById('item_cancel_' + mod_name + '_' + friendId)) {
            document.getElementById('item_cancel_' + mod_name + '_' + friendId).style.opacity = 100;
          }
          if(document.getElementById('item_title_' + mod_name + '_' + friendId)) {
            document.getElementById('item_title_' + mod_name + '_' + friendId).style.opacity = 100;
          }
          if(document.getElementById('mutual_friend_' + mod_name + '_' + friendId)) {
            document.getElementById('mutual_friend_' + mod_name + '_' + friendId).style.opacity = 100;
          }
        }
        setTimeout("takeContent(" + friendId + ", '" + mod_name + "', '" + parentDivId + "', '" + mod_name + "', " + isMiddle +")", 1000);
      }
    }));
}
}

var removeSuggNotification = function( entity, entity_id, notificationType, div_id, responseWithTip, getDisplayContent )
{
  en4.core.request.send(new Request.HTML({
    url : en4.core.baseUrl + 'suggestion/main/remove-notification',
    data : {
      format : 'html',
      entity: entity,
      entity_id: entity_id,
      notificationType: notificationType,
      responseWithTip: responseWithTip
    },
    'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
    {
      if( getDisplayContent != 0 ) {
        setCountingLimit(entity, 0, getDisplayContent);
      }
      if( div_id != '' ) {
        document.getElementById(div_id).innerHTML = responseHTML;
      }
    }
  }))
};



// ===============================================	Start - Fading Effects ================================================

var TimeToFade = 400.0; // Set timing of Fading Effect.

function fade(eid, modFlag)
{
  var element = document.getElementById(eid);
  if(element == null)
    return;
   
  element.FadeState	=	modFlag;
  if(element.FadeState == null)
  {
    if(element.style.opacity == null 
      || element.style.opacity == ''
      || element.style.opacity == '1')
      {
      element.FadeState = 2;
    }
    else
    {
      element.FadeState = -2;
    }
  }
    
  if(element.FadeState == 1 || element.FadeState == -1)
  {
    element.FadeState = element.FadeState == 1 ? -1 : 1;
    element.FadeTimeLeft = TimeToFade - element.FadeTimeLeft;
  }
  else
  {
    element.FadeState = element.FadeState == 2 ? -1 : 1;
    element.FadeTimeLeft = TimeToFade;
    setTimeout("animateFade(" + new Date().getTime() + ",'" + eid + "')", 33);
  }  
}





function animateFade(lastTick, eid)
{  
  var curTick = new Date().getTime();
  var elapsedTicks = curTick - lastTick;
  
  var element = document.getElementById(eid);
 
  if(element.FadeTimeLeft <= elapsedTicks)
  {
    element.style.opacity = element.FadeState == 1 ? '1' : '0';
    element.style.filter = 'alpha(opacity = ' 
    + (element.FadeState == 1 ? '100' : '0') + ')';
    element.FadeState = element.FadeState == 1 ? 2 : -2;
    return;
  }
 
  element.FadeTimeLeft -= elapsedTicks;
  var newOpVal = element.FadeTimeLeft/TimeToFade;
  if(element.FadeState == 1)
    newOpVal = 1 - newOpVal;

  element.style.opacity = newOpVal;
  element.style.filter = 'alpha(opacity = ' + (newOpVal*100) + ')';
  
  setTimeout("animateFade(" + curTick + ",'" + eid + "')", 33);
}

// ===============================================	End - Fading Effects ================================================


function tempGetDayEvents(date_current, category_id) {
  		
  if ($('siteevent_dayevents') == null) {
    Smoothbox.open('<div id="siteevent_dayevents" style="width:550px;min-height:70px;"><div class="seaocore_content_loader" style="margin:20px auto 0;"></div></div>');
  }else {  
    if ($('days_eventlisting') != null)
      $('days_eventlisting').innerHTML = '<div class="seaocore_content_loader" style="margin:20px auto 0;"></div>' ;
    $$('.seaocore_view_more').setStyle('display', 'none');
  }
    
  var data_month = {
    'date_current'  : date_current,
    category_id : category_id,
    'format' : 'html',
    'is_ajax' : true,
    viewtype : 'list'
  };

  if (typeof calendar_params != 'undefined')
    data_month = $merge(calendar_params, data_month);
  en4.core.request.send(new Request.HTML({
    'url' : en4.core.baseUrl + 'widget/index/mod/siteevent/name/calendarview-siteevent',
    'data' : data_month,
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      Smoothbox.close();
      Smoothbox.open('<div id="siteevent_dayevents" style="width:550px;">'+ responseHTML + '</div>');
    }
  }));
}




function showSuggestionContent(url, init_div_id, getWidLimit, resource_type, getWidAjaxEnabled, mod_type, exploreWidgetLimit, getLayout) {

    
    var request = new Request.HTML({
      url : url,
      method: 'get',
      data : {
        format : 'html',
        'loadFlage' : 1,
        'itemCountPerPage': exploreWidgetLimit,
        'resource_type': resource_type,
        'getWidAjaxEnabled': getWidAjaxEnabled,
        'getWidLimit': getWidLimit,
        'mod_type': mod_type,
        'getLayout': getLayout
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $(init_div_id).className = '';
        $(init_div_id).innerHTML = responseHTML;
        if( $(init_div_id + '_myContent') ) {
          var friendPhotocontainer = $(init_div_id + '_myContent').innerHTML;
          $(init_div_id).innerHTML = friendPhotocontainer;
        }
      }
    });
    request.send();
}