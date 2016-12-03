
/* $Id: core.js 2010-08-17 9:40:21Z SocialEngineAddOns Copyright 2009-2010 BigStep Technologies Pvt. Ltd. $ */
en4.siteeventinvite = {}
en4.siteeventinvite.friends = {
		
	request: '',
	//GETTING THE SEARCHED FRIENDS.
  show_searched_friends: function(request, event) {
  if (typeof event != 'undefined' && event.keyCode == 13) {
    return false;
  }
  
  if (en4.siteeventinvite.friends.request != '')//abort previous request.
		 this.request.cancel();
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
  var element = $('main_box').getElement('.seaocore_popup_content_inner');
  if (element)
    element.empty();
    new Element('div', {      
      'class' : 'siteevent_profile_loading_image'      
    }).inject(element);
  this.request = new Request.HTML({
    url : url,
    method: 'GET',
    data : {
      format : 'html',
      'task': 'ajax',
      searchs : current_search,
      'selected_checkbox':eventinvite_string,
      'show_selected':show_selected,
      'action_id':action_session_id,      
      'selected_friend_flag':request,
      'getArray':paginationArray
    },
    'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
    {
      document.getElementById('main_box').innerHTML = responseHTML;
      this.update_html();
      if( document.getElementById('newcheckbox') ) {
        if( paginationArray[memberPage] && (paginationArray[memberPage] == 1) && (dontHaveResult == 1) ) {
          popupFlag = 1;
          document.getElementById('newcheckbox').addClass('selected');
        }else {
          popupFlag = 0;
          document.getElementById('newcheckbox').removeClass('selected');
        }
        document.getElementById('newcheckbox').setProperty('onclick', 'en4.siteeventinvite.friends.selectAllFriend("' + displayUserStr + '")');
      }
    }.bind(this)
  });
	
  this.request.send();	
  return false;
},
	
	// When click on "Select All" then we are calling this function which selected all the friend which are showing on page.
  selectAllFriend: function( friendStr ) {
  if( popupFlag == 0 ){
    popupFlag = 1;
  }else{
    popupFlag = 0;
  }
  var subcatss = friendStr . split("::");
  for (var i=0; i < subcatss.length; ++i){
    var friend_id = subcatss[i].split("_");
    var check_name_new = 'check_' +  friend_id;
    this.newmoduleSelect(check_name_new, friend_id);
  }

  paginationArray[memberPage] = popupFlag;
},

newmoduleSelect: function(check_name, friend_id)
{
  if(document.getElementById(check_name)) {
		// popupFlag: Variable if it value is 0 then "Removed the Selection" and if 1 "Friend Selected".
    if( popupFlag ) {// Check All
      if( !SelectedPopupContent[friend_id] ) {
        this.moduleSelect(friend_id);
      }
      document.getElementById('newcheckbox').addClass('selected');
    }else {// Uncheck All
      if( SelectedPopupContent[friend_id] ) {
        this.moduleSelect(friend_id);
      }
      document.getElementById('newcheckbox').removeClass('selected');
    }
  }
},
show_all: function() {
  memberSearch = '';
  document.getElementById('newcheckbox').style.display = 'block';
  document.getElementById(action_module + '_members_search_inputd').value = en4.core.language.translate('Search Members');
  if (show_selected == 1) {
    show_selected = 0;
    this.paginateMembers (1);
  }
  else {
    this.paginateMembers (memberPage);
  }
  document.getElementById('newcheckbox').style.display = 'block';
},

// When click on "Selected" then we are calling this function. It will return all selcted friend list.
 selected_friends: function() {
  document.getElementById('newcheckbox').style.display = 'none';
  if (friends_count > 0) {
    //tem_friend_count = friends_count;
    memberSearch = '';
    show_selected = 1;
    this.show_searched_friends(1);
    document.getElementById('newcheckbox').style.display = 'none';
  }
  else if (show_selected == 1) {
   this.show_all ();
  }
},


//SUBMIT THE FORM IF USER HAS SELECTED ATLEAST ONE FRIEND. 
doCheckAll: function()
{
  var eventinvite_string_1 = eventinvite_string.split(',');
  if(friends_count == 0)
  {
    document.getElementById('check_error').innerHTML = '<ul class="form-errors"><li><ul class="errors"><li>' + en4.core.language.translate('Please select at-least one entry above to send invitation to.') + '</li></ul></li></ul>';
  }
  else
  {
    document.getElementById('hidden_checkbox').innerHTML = "";
    var hidden_checkbox = '';
		if (parseInt(friends_count) > parseInt(1)) {
			for ($i = 0;$i < eventinvite_string_1.length; $i++ ) {
				// var checked_id_temp = eventinvite_string_1[$i].split('-');
				var checked_id_temp = eventinvite_string_1[$i];
				if( checked_id_temp ) {
					delete SelectedPopupContent[1];
					hidden_checkbox = hidden_checkbox + '<input type="hidden" name="check_' + checked_id_temp + '"  value="' + checked_id_temp + '"/>';
				}
			}
		}
		else {
		  var checked_id_temp = eventinvite_string;
			if( checked_id_temp ) {
				delete SelectedPopupContent[1];
				hidden_checkbox = hidden_checkbox + '<input type="hidden" name="check_' + checked_id_temp + '"  value="' + checked_id_temp + '"/>';
			}	
		}

    document.getElementById('hidden_checkbox').innerHTML = hidden_checkbox;
    document.eventinvite.submit();
  }
},
//PAGINATING FRIENDS PAGE.
paginateMembers: function(page) {
  if (show_selected == 1) {
    document.getElementById('show_all').className = '';
    document.getElementById('selected_friends').className = 'selected';
  }
  else {
    document.getElementById('show_all').className = 'selected';
    document.getElementById('selected_friends').className = '';
  }
  var element = $('main_box').getElement('.seaocore_popup_content_inner');
  if (element)
  element.empty();
      new Element('div', {      
        'class' : 'siteevent_profile_loading_image'      
      }).inject(element);
  var request = new Request.HTML({
    url : siteeventinvite_url,
    method: 'GET',
    data : {
      format : 'html',
      'task': 'ajax',
      searchs : memberSearch,
      'selected_checkbox':eventinvite_string,
      'page':page,
      'action_id':action_session_id,
			'occurrence_id':occurrence_id,
			'siteevent_id':siteevent_id,
      'show_selected':show_selected,      
      'getArray':paginationArray
    },
    'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
    {
      document.getElementById('main_box').innerHTML = responseHTML;
      this.update_html();
      if( document.getElementById('newcheckbox') ) {
        if( paginationArray[memberPage] && (paginationArray[memberPage] == 1) && (dontHaveResult == 1) ) {
          popupFlag = 1;
          document.getElementById('newcheckbox').addClass('selected');
        }else {
          popupFlag = 0;
          document.getElementById('newcheckbox').removeClass('selected');
        }
        document.getElementById('newcheckbox').setProperty('onclick', 'en4.siteeventinvite.friends.selectAllFriend("' + displayUserStr + '")');
      }
    }.bind(this)
  });
  request.send();
  return false;
},

// Function call when click on "Friend Div"
moduleSelect: function( friend_id )
{
  // If "friend_id" are not in array, it means that "friend-div" should be selected other vise should not be selected.
  if( SelectedPopupContent[friend_id] ) { // Should not be selected
    friends_count--;
    delete SelectedPopupContent[friend_id];
		if (friends_count >=1)
			eventinvite_string = eventinvite_string.replace(',' +  friend_id, "");
		else
			eventinvite_string = '';
		// Class: Which are showing friend is selected or not in the popups.
    document.getElementById('check_' + friend_id ).className = "eventinvite_pop_friend";
    // "Select All - Checkbox" should be disabled when ever all selected checkbox is unchecked.
    if( friends_count == 0 ) {
      popupFlag = 0;
      document.getElementById('newcheckbox').removeClass('selected');
    }
  }else { // Should be selected
    SelectedPopupContent[friend_id] = 1;
    friends_count++;
		if (eventinvite_string != '')
			eventinvite_string  += ',' + friend_id;
		else
			eventinvite_string = friend_id;
		// Class: Which are showing friend is selected or not in the popups.
    document.getElementById('check_' + friend_id ).className = "eventinvite_pop_friend selected";
  }
  if( friends_count < 0 ){
    friends_count = 0;
  }
  
	if(select_text_flag) {
		document.getElementById('selected_friends').innerHTML = select_text_flag +  ' (' + friends_count + ')';
	}else {
		document.getElementById('selected_friends').innerHTML = en4.core.language.translate('Selected') +  ' (' + friends_count + ')';
	}
},
	
//UPDATING THE CURRENT FRIEND PAGE.
update_html: function() {
	var tempCount = '';
	// suggestion_string_temp: This variable contained the complete string of selected friend-ids.
	// tempSelectedFriend: Contain selected friend-ids, which are going to display on page.
  if (eventinvite_string_temp != '') {
    var eventinvite_string_2 = tempSelectedFriend.split(',');
    if (eventinvite_string_2.length > 0) {
      for (i = 0; i< eventinvite_string_2.length; i++) {
        var checkbox_info = eventinvite_string_2[i];
					if( checkbox_info ) {
						if( document.getElementById('check_' + checkbox_info) ){
							document.getElementById('check_' + checkbox_info).className = "eventinvite_pop_friend selected";
							friends_count--;
							this.moduleSelect(checkbox_info);
						}else {


							tempCount = eventinvite_string_temp.search(checkbox_info);
							if( tempCount < 0 ) {
								eventinvite_string_temp  += ',' + checkbox_info;
							}


						}
					}
      }
			eventinvite_string = eventinvite_string_temp;
    }
    return false;
  }
}	
}

// close the popup.
var cancelPopup = function ()
{
  parent.Smoothbox.close();
};

var url = '';
window.addEvent('domready', function () {
  if (typeof action_module != 'undefined') {
    siteeventinvite_url = en4.core.baseUrl + 'siteeventinvite/index/invite-friends';
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
        en4.siteeventinvite.friends.paginateMembers (1);
      }

    });
  }
});

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