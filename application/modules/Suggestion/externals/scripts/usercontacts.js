/* $Id: usercontacts.js 2010-08-17 9:40:21Z SocialEngineAddOns Copyright 2009-2010 BigStep Technologies Pvt. Ltd. $ */

var aaf_main_page_invite = false;
var fbappid;
var invite_mainpage_url; 
//THIS FUNCTION IS USED TO SHOW THE ALL GOOGLE CONTACTS IN PARSING MODE.DEFAULT WE ARE SHOWING ONLY THOSE CONTACTS WHICH ARE SITE MEMBERS BUT NOT USER'S FRIENDS.
function show_contacts_google (id) { 
  $('id_nonsite_success_mess').style.display = 'none';
	$('id_success_frequ').style.display = 'none';
	$('show_contacts').style.display = 'none';
	if (id == 1) {
		var child_window = window.open (en4.core.baseUrl + 'suggestion/usercontacts/getgooglecontacts' ,'mywindow','width=500,height=500');
	}
	if (window.opener!= null) {
		if (id == 0) {
			var href = window.location.href;
		  var access_token = href.split('#access_token=');
		if (typeof access_token[1] == 'undefined' ) {
		  var token = getQuerystring('token', href);
		  var redirect_href = href;
		}
		else { 
		  var redirect_href = access_token[0];
		  var access_token = access_token[1].split('&token_type');
		  var token = access_token[0];
		  redirect_href = redirect_href + '?token=' + token;
		   
		}
			
			if (window.opener.aaf_main_page_invite)
			   window.opener.location.href = redirect_href;
			else {
			  window.opener.get_contacts_google (token);
			}
			close();
		}
	}
}
//CALLING THIS FUNCTION FROM CHILD WINDOW BEFORE CLOSING THE CHILD WINDOW.WHICH GETS THE GOOGLE CONTACTS.
function get_contacts_google (token) { 
	Smoothbox.open("<div style='height:30px;'><center><b>" + translate_suggestion['Importing Contacts'] + "</b><br /><img src='" + en4.core.staticBaseUrl + "application/modules/Suggestion/externals/images/loadings.gif' alt='' /></center></div>");
	var postData = {
		'token' : token,
		'task' : 'get_googlecontacts'
	};
	
	en4.core.request.send(new Request( {
		url : en4.core.baseUrl + 'suggestion/usercontacts/getgooglecontacts',
		method : 'post',
		data : postData,
		onSuccess : function(responseObject)
		{ 
			Smoothbox.close();
			$('network_friends').style.display = 'block'; 
			if( $('skipinviterlink') ){ $('skipinviterlink').style.display = 'block'; }
			$('show_contacts').style.display = 'block';
			$('show_contacts').innerHTML = responseObject;
			window.location.hash ='show_contacts';
		}
	}));
}

  
//THIS FUNCTION IS USED TO SHOW THE ALL YAHOO CONTACTS IN PARSING MODE.DEFAULT WE ARE SHOWING ONLY THOSE CONTACTS WHICH ARE SITE MEMBERS BUT NOT USER'S FRIENDS.
function show_contacts_yahoo (id) {
	$('id_nonsite_success_mess').style.display = 'none';
	$('id_success_frequ').style.display = 'none';
	$('show_contacts').style.display = 'none';
	if (id == 1) { 
		var child_window = window.open (en4.core.baseUrl + 'suggestion/usercontacts/getyahoocontacts' ,'mywindow','width=500,height=500');
	}
	
	if (window.opener!= null) {
		if (id == 0) {
		var href = window.location.href;
		var oauth_verifier = getQuerystring('oauth_verifier', href);
		if (window.opener.aaf_main_page_invite)
			 window.opener.location.href = href;
		else {
		  window.opener.get_contacts_yahoo(oauth_verifier);
		}
		close();
		}
	}
}
//CALLING THIS FUNCTION FROM CHILD WINDOW BEFORE CLOSING THE CHILD WINDOW.WHICH GETS ALL YAHOO CONTACTS.
function get_contacts_yahoo (oauth_verifier) {
	Smoothbox.open("<div style='height:30px;'><center><b>" + translate_suggestion['Importing Contacts'] + "</b><br /><img src='" + en4.core.staticBaseUrl + "application/modules/Suggestion/externals/images/loadings.gif' alt='' /></center></div>");
	var postData = {
		'oauth_verifier' : oauth_verifier,
		'task' : 'get_yahoocontact'
	};

	en4.core.request.send(new Request({
		url : en4.core.baseUrl + 'suggestion/usercontacts/getyahoocontacts',
		method : 'post',
		data : postData,
		onSuccess : function(responseObject)
		{ 
			Smoothbox.close();
			$('network_friends').style.display = 'block';
			if( $('skipinviterlink') ){ $('skipinviterlink').style.display = 'block'; }
			$('show_contacts').style.display = 'block';
			$('show_contacts').innerHTML = responseObject;
			window.location.hash ='show_contacts';
		}
	}));
}



//THIS FUNCTION IS USED TO SHOW THE ALL YAHOO CONTACTS IN PARSING MODE.DEFAULT WE ARE SHOWING ONLY THOSE CONTACTS WHICH ARE SITE MEMBERS BUT NOT USER'S FRIENDS.
function show_contacts_linkedin (id) {
	$('id_nonsite_success_mess').style.display = 'none';
	$('id_success_frequ').style.display = 'none';
	$('show_contacts').style.display = 'none';
	if (id == 1) { 
		var child_window = window.open (en4.core.baseUrl + 'suggestion/usercontacts/getlinkedincontacts' ,'mywindow','width=500,height=500');
	}

	if (window.opener!= null) {
		if (id == 0) {
		var href = window.location.href;
		var oauth_verifier = getQuerystring('oauth_verifier', href);
		if (window.opener.aaf_main_page_invite)
			 window.opener.location.href = href;
		else { 
		  window.opener.get_contacts_linkedin(oauth_verifier);
		}
		close();
		}
	}
}
//CALLING THIS FUNCTION FROM CHILD WINDOW BEFORE CLOSING THE CHILD WINDOW.WHICH GETS ALL YAHOO CONTACTS.
function get_contacts_linkedin (oauth_verifier) {
	Smoothbox.open("<div style='height:30px;'><center><b>" + translate_suggestion['Importing Contacts'] + "</b><br /><img src='" + en4.core.staticBaseUrl + "application/modules/Suggestion/externals/images/loadings.gif' alt='' /></center></div>");
	var postData = {
		'oauth_verifier' : oauth_verifier,
		'task' : 'get_yahoocontact'
	};

	en4.core.request.send(new Request({
		url : en4.core.baseUrl + 'suggestion/usercontacts/getlinkedincontacts',
		method : 'post',
		data : postData,
		onSuccess : function(responseObject)
		{ 
			Smoothbox.close();
			$('network_friends').style.display = 'block';
			if( $('skipinviterlink') ){ $('skipinviterlink').style.display = 'block'; }
			$('show_contacts').style.display = 'block';
			$('show_contacts').innerHTML = responseObject;
			window.location.hash ='show_contacts';
		}
	}));
}
  
  
//THIS FUNCTION IS USED TO SHOW THE ALL WINDOW LIVE CONTACTS IN PARSING MODE.DEFAULT WE ARE SHOWING ONLY THOSE CONTACTS WHICH ARE SITE MEMBERS BUT NOT USER'S FRIENDS.
function show_contacts_windowlive (id) {
	$('id_nonsite_success_mess').style.display = 'none';
	$('id_success_frequ').style.display = 'none';
	$('show_contacts').style.display = 'none';
	if (id == 1) { 
		var child_window = window.open (en4.core.baseUrl + 'suggestion/usercontacts/getwindowlivecontacts' ,'mywindow','width=900,height=900');
	}
	
	if (window.opener!= null) {
		if (id == 0) { 
		  if (window.opener.aaf_main_page_invite)
			   window.opener.location.href = window.location.href;
			else {
			   window.opener.get_contacts_windowlive();
			}	
			close();
		}
	}
}
 
//CALLING THIS FUNCTION FROM CHILD WINDOW BEFORE CLOSING THE CHILD WINDOW.WHICH GETS ALL WINDOW LIVE CONTACTS.
function get_contacts_windowlive () {
	Smoothbox.open("<div style='height:30px;'><center><b>" + translate_suggestion['Importing Contacts'] + "</b><br /><img src='" + en4.core.staticBaseUrl + "application/modules/Suggestion/externals/images/loadings.gif' alt='' /></center></div>");
	var postData = {
		'task' : 'get_windowcontact'
	};

	en4.core.request.send(new Request({
		url : en4.core.baseUrl + 'suggestion/usercontacts/getwindowlivecontacts',
		method : 'post',
		data : postData,
		onSuccess : function(responseObject)
		{ 
			Smoothbox.close();
			$('network_friends').style.display = 'block';
			if( $('skipinviterlink') ){ $('skipinviterlink').style.display = 'block'; }
			$('show_contacts').style.display = 'block';
			$('show_contacts').innerHTML = responseObject;
			window.location.hash ='show_contacts';
		}
	}));
}

//THIS FUNCTION IS USED TO SHOW THE ALL AOL CONTACTS IN PARSING MODE.DEFAULT WE ARE SHOWING ONLY THOSE CONTACTS WHICH ARE SITE MEMBERS BUT NOT USER'S FRIENDS.
function show_contacts_aol (id) {
	$('id_nonsite_success_mess').style.display = 'none';
	$('id_success_frequ').style.display = 'none';
	$('show_contacts').style.display = 'none';
	if (id == 1) {
		var child_window = window.open (en4.core.baseUrl + 'suggestion/usercontacts/aollogin' ,'mywindow','width=500,height=500');
	}
	if (window.opener!= null) {
		if (id == 0) { 
		  if (window.opener.aaf_main_page_invite)
			   window.opener.location.href = window.location.href;
			else {
			   window.opener.get_contacts_aol();
			}  
			close();
		}
	}
}
//CALLING THIS FUNCTION FROM CHILD WINDOW BEFORE CLOSING THE CHILD WINDOW.WHICH GETS THE AOL CONTACTS.
function get_contacts_aol () {
	Smoothbox.open("<div style='height:30px;'><center><b>" + translate_suggestion['Importing Contacts'] + "</b><br /><img src='" + en4.core.staticBaseUrl + "application/modules/Suggestion/externals/images/loadings.gif' alt='' /></center></div>");
	var postData = {
		'task' : 'get_aolcontacts'
	};
	
	en4.core.request.send(new Request( {
		url : en4.core.baseUrl + 'suggestion/usercontacts/getaolcontacts',
		method : 'post',
		data : postData,
		onSuccess : function(responseObject)
		{ 
			Smoothbox.close();
			$('network_friends').style.display = 'block';
			if( $('skipinviterlink') ){ $('skipinviterlink').style.display = 'block'; }
			$('show_contacts').style.display = 'block';
			$('show_contacts').innerHTML = responseObject;
			window.location.hash ='show_contacts';
		}
	}));
}

//RETURNING THE QUERY STRING .
function getQuerystring(key, href) {
	key = key.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	var regex = new RegExp("[\\?&]"+key+"=([^&#]*)");
	var qs = regex.exec(href);
	if(qs == null)
		return '';
	else
		return qs[1];
}

//THIS FUNCTION IS USED TO SET AND UNSET ALL CHECKBOX IN CASE OF ADD FRIENDS.
function checkedAll () {
	if ($('select_all').checked)
		checked = true;
	else
		checked = false;
	var total_contacts = $('total_contacts').value;

	for (var i =1; i <= total_contacts; i++) 
	{
		$('contact_' + i).checked = checked;
	}
}

//SENDING USER SELECTED USERS TO ADD AS A FRIEND REQUEST.
function sendFriendRequests() {
	var sitemembers = new Array ();
	var checked = false;
	var total_contacts = $('total_contacts').value;
	for (var i =1; i <= total_contacts; i++) 
	{
		if ($('contact_' + i).checked) {
			checked = true;
			sitemembers [i] = $('contact_' + i).value;
		}
	}
	if (checked) {
		Smoothbox.open("<div style='height:30px;'><center><b>" + translate_suggestion['Sending Request'] + "</b><br /><img src='" + en4.core.staticBaseUrl + "application/modules/Suggestion/externals/images/loadings.gif' alt='' /></center></div>");
		var postData = {
			'sitemembers' : sitemembers,
			'task' : 'friend_requests',
		};
		
		en4.core.request.send(new Request({
			url : en4.core.baseUrl + 'suggestion/index/addtofriend',
			method : 'post',
			data : postData,
			onSuccess : function(responseObject)
			{ 
				Smoothbox.close();
         
				$('id_success_frequ').style.display = 'block';
				$('show_sitefriend').style.display = 'none';
				$('show_nonsitefriends').style.display = 'block';
			}
		}));
	}
	else {
		en4.core.showError("Please select at least one friend to add");
	}
}

//THIS FUNCTION IS USED TO SET AND UNSET ALL CHECKBOX IN CASE OF INVITE FRIENDS.
function nonsitecheckedAll () {
	if ($('nonsiteselect_all').checked)
		checked = true;
	else
		checked = false;
	var total_contacts = $('nonsitetotal_contacts').value;
	for (var i =1; i <= total_contacts; i++) 
	{
		$('nonsitecontact_' + i).checked = checked;
	}
}

//THIS FUNCTION IS USED TO HIDE THE ADD FRINEDS LIST AND SHOWING THE NONSITE MEMBERS LIST.
function skip_addtofriends () {
  if ($('nonsitetotal_contacts')) {
		var total_contacts = $('nonsitetotal_contacts').value;
		for (var i =1; i <= total_contacts; i++) 
		{
			$('nonsitecontact_' + i).checked = true;
		}
		$('show_sitefriend').style.display = 'none';
		$('show_nonsitefriends').style.display = 'block';
	}
	else {
   document.id_myform_temp.submit();
	}
}

//WHEN USER CLICKED ON THE SKIP BUTTON OF NONSITE MEMBERS LIST.
function skipinvites () {
	document.id_myform_temp.submit();
}

function inviteFriends (socialtype) {
 	var nonsitemembers = new Array ();
	var checked = false;
	var total_checked = 0;
	var total_contacts = $('nonsitetotal_contacts').value;
	for (var i =1; i <= total_contacts; i++) 
	{
		if ($('nonsitecontact_' + i).checked) {
			total_checked++; 
			checked = true;
			nonsitemembers [i] = $('nonsitecontact_' + i).value;
		}
	}
	if (checked) {  
    Smoothbox.open("<div style='height:30px;'><center><b>" + translate_suggestion['Sending Request'] + "</b><br /><img src='" + en4.core.staticBaseUrl + "application/modules/Suggestion/externals/images/loadings.gif' alt='' /></center></div>");
		var postData = {
			'nonsitemembers' : nonsitemembers,
			'task' : 'join_network',
			'socialtype': socialtype
		};
		
		en4.core.request.send(new Request({
			url : en4.core.baseUrl + 'suggestion/index/invitetosite',
			method : 'post',
			data : postData,
			onSuccess : function(responseObject)
			{ 
			 
			
       $('id_success_frequ').style.display = 'none';
				
				$('id_nonsite_success_mess').style.display = 'block';
				$('show_nonsitefriends').innerHTML = '';
				if( $('id_csvcontacts').style.display == 'none' ) { 
          if ($('skipinviterlink') ) {
					   $('skipinviterlink').style.display = 'block';
					}
				}
				Smoothbox.close();
			}
		}));
	}
	else {
		en4.core.showError("Please select at least one friend to invite");
	}
}

var fileext = false;

//HERE WE ARE UPLOADING THE FILE ON SELECTING FILE FROM BROWSE BUTTON.
function savefilepath() {
	$('id_nonsite_success_mess').style.display = 'none';
	$('id_success_frequ').style.display = 'none';	
	$('id_csvformate_error_mess').style.display = 'none';	
	$('show_contacts_csv').style.display = 'none';	
	$('id_csvformate_error_mess').style.display = 'none';	
	$('show_contacts_csv').style.display = 'none';			
	var filename = $('Filedata').value;
  if (checkext(trim(filename)) == true) { 
		fileext = true;
		Smoothbox.open("<div style='height:30px;'><center><b>" + translate_suggestion['Uploading file'] + "</b><br /><img src='" + en4.core.staticBaseUrl + "application/modules/Suggestion/externals/images/loadings.gif' alt='' /></center></div>");
		//$('suggestion_file_upload').style.display = 'block';
		$('csvmasssubmit').style.display = 'block';
		window.setTimeout("document.csvimport.submit()",1);
	}
	else {
			fileext = false;
			$('id_csvformate_error_mess').style.display = 'block';
			//$('suggestion_file_upload').style.display = 'block';
			$('csvmasssubmit').style.display = 'none';
	}
}

//GETTING ALL CSV FILE CONTACTS.
function getcsvcontacts (filename) { 
	
	if (fileext) {  
	  if ($('uccess_fileupload_parent_sugg'))
	     $('uccess_fileupload_parent_sugg').style.display = 'none';
	  if (aaf_main_page_invite) { 
	   var filename = $('file_upload').value;
	    window.location.href = invite_mainpage_url + '?csv_filename=' + filename;
	  }
	  else { 
	    if (filename == '')
	      var filename = $('Filedata').value;
  		Smoothbox.open("<div style='height:30px;'><center><b>" + translate_suggestion['Importing Contacts'] + "</b><br /><img src='" + en4.core.staticBaseUrl + "application/modules/Suggestion/externals/images/loadings.gif' alt='' /></center></div>");
  		var postData = {
  			'task' : 'join_network',
  			'filename': filename
  		};
  			
  		en4.core.request.send(new Request({
  			url : en4.core.baseUrl + 'suggestion/usercontacts/getcsvcontacts',
  			method : 'post',
  			data : postData,
  			onSuccess : function(responseObject)
  			{ 
  				$('Filedata').value = '';
  				fileext = false;
  				Smoothbox.close();
  				$('csvmasssubmit').style.display = 'none';
  				$('id_csvformate_error_mess').style.display = 'none';	
  				$('csv_friends').style.display = 'block';
  				$('show_contacts_csv').style.display = 'block';	
  				$('show_contacts_csv').innerHTML = responseObject;
  				if( $('skipinviterlink') ){ $('skipinviterlink').style.display = 'block'; }
  				window.location.hash ='show_contacts_csv';
  			}
  
  		}));
	  }	
		
	}
	else { 
		$('id_success_frequ').style.display = 'none';
		$('id_nonsite_success_mess').style.display = 'none';
		$('csv_friends').style.display = 'none';
		$('id_csvformate_error_mess').style.display = 'block';	
 		
	}
}

function checkext(fis)
{
	var s,ext ;
    s=fis.length;
    ext=fis.substring(s-4,s);
	if((ext.toUpperCase()=='.CSV' || ext.toUpperCase()=='.TXT'))
	 {
		 return true;
	}
	else
	return false;
	
}

function showhide (hide_div, show_div) {
	 if ($('show_contacts')) {
		$('show_contacts').innerHTML = '';
	}
	if ($('show_contacts_csv')) {
		$('show_contacts_csv').innerHTML = '';
   }
	$(hide_div).style.display = 'none';
	$(show_div).style.display = 'block';
	$('network_friends').style.display = 'none';
	$('id_nonsite_success_mess').style.display = 'none';
	$('id_success_frequ').style.display = 'none';
	$('csv_friends').style.display = 'none';
	$('id_csvformate_error_mess').style.display = 'none';
}

function showhideinviter (hide_div, show_div, calling_from) {

	if( calling_from == 1 ) {
		$('sub-title').style.display = 'none';
		$('sub-txt').style.display = 'none';
		$('webacc-logos').style.display = 'none';
		$('skipinviterlink').style.display = 'none';
		$('invite_info').style.display = 'none';
		$('header_title').style.display = 'none';

		$('inviter_form1').style.display = 'block';
		$('inviter_form2').style.display = 'block';

		$('help_link2').style.display = 'none';
		$('help_link1').style.display = 'block';

		$('network_friends').style.display = 'none';
		$('id_nonsite_success_mess').style.display = 'none';
		$('id_success_frequ').style.display = 'none';
		$('csv_friends').style.display = 'none';
		$('id_csvformate_error_mess').style.display = 'none';
	}else if( calling_from == 2 ) { 
		$(hide_div).style.display = 'none';
		$(show_div).style.display = 'block';

		$('sub-title').style.display = 'none';
		$('sub-txt').style.display = 'none';
		$('webacc-logos').style.display = 'none';
		$('skipinviterlink').style.display = 'none';
		$('invite_info').style.display = 'none';
		$('header_title').style.display = 'none';

		$('inviter_form1').style.display = 'block';
		$('inviter_form2').style.display = 'block';

		$('help_link2').style.display = 'none';
		$('help_link1').style.display = 'block';
		

		$('network_friends').style.display = 'none';
		$('id_nonsite_success_mess').style.display = 'none';
		$('id_success_frequ').style.display = 'none';
		$('csv_friends').style.display = 'none';
		$('id_csvformate_error_mess').style.display = 'none';
	}else {
		if ($('show_contacts')) {
			$('show_contacts').innerHTML = '';
		}
		if ($('show_contacts_csv')) {
			$('show_contacts_csv').innerHTML = '';
		}
		$(hide_div).style.display = 'none';
		$(show_div).style.display = 'block';
		$('network_friends').style.display = 'none';
		$('id_nonsite_success_mess').style.display = 'none';
		$('id_success_frequ').style.display = 'none';
		$('csv_friends').style.display = 'none';
		$('id_csvformate_error_mess').style.display = 'none';
		$('sub-title').style.display = 'block';
		$('sub-txt').style.display = 'block';
		$('webacc-logos').style.display = 'block';
		if( calling_from == 3 ) {
			$('skipinviterlink').style.display = 'none';
		}else if( calling_from == 4 ) {
			$('skipinviterlink').style.display = 'block';
		}
		$('invite_info').style.display = 'block';
		$('header_title').style.display = 'block';

		$('inviter_form1').style.display = 'none';
		$('inviter_form2').style.display = 'none';

		$('help_link2').style.display = 'block';
		$('help_link1').style.display = 'none';
		
	}
}



/**



*  Javascript trim, ltrim, rtrim
*  http://www.webtoolkit.info/
*
**/
 
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


//THIS FUNCTION IS USED TO SHOW THE ALL GOOGLE CONTACTS IN PARSING MODE.DEFAULT WE ARE SHOWING ONLY THOSE CONTACTS WHICH ARE SITE MEMBERS BUT NOT USER'S FRIENDS.
function show_contacts_Facebook (id) { 
  $('id_nonsite_success_mess').style.display = 'none';
	$('id_success_frequ').style.display = 'none';
	$('show_contacts').style.display = 'none';
	if (id == 1) {
		var child_window = window.open (en4.core.baseUrl + 'suggestion/usercontacts/getfacebookcontacts' ,'mywindow','width=800,height=500');
	}
	if (window.opener!= null) {
		if (id == 0) {
			var href = window.location.href;
			if (window.opener.aaf_main_page_invite)
			   window.opener.location.href = href;
			else {
			  window.opener.get_contacts_Facebook ();
			}
			close();
		}
	}
}
//CALLING THIS FUNCTION FROM CHILD WINDOW BEFORE CLOSING THE CHILD WINDOW.WHICH GETS THE GOOGLE CONTACTS.
function get_contacts_Facebook () { 
  Smoothbox.open("<div style='height:30px;'><center><b>" + translate_suggestion['Importing Contacts'] + "</b><br /><img src='" + en4.core.staticBaseUrl + "application/modules/Suggestion/externals/images/loadings.gif' alt='' /></center></div>");
	var postData = {
		'task' : 'get_facebookcontacts'
	};
	
	en4.core.request.send(new Request.JSON( {
		url : en4.core.baseUrl + 'suggestion/usercontacts/getfacebookcontacts',
		method : 'post',
		data : postData,
		onSuccess : function(responseJSON) {
	
			Smoothbox.close();
			$('network_friends').style.display = 'block'; 
			if( $('skipinviterlink') ){ $('skipinviterlink').style.display = 'block'; }
			$('show_contacts').style.display = 'block';
			$('show_contacts').innerHTML = responseJSON.friends_invite;
			FB.XFBML.parse($('show_contacts'));
		}
	}));
}
