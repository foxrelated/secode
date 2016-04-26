/* $Id: core.js 2011-05-05 9:40:21Z SocialEngineAddOns Copyright 2010-2011 BigStep Technologies Pvt. Ltd. $ */
var tab_content_id_sitegroup = 0;
var prev_tab_id = 0;
var ads_display = '';
var changeurl = 0;
var content_integration_tab_id = '';
var manageinfo = function(manage_id, owner_id, url, group_id)
{ 
  var total_div = $('count_div').value;
  if(total_div >=  1 ) {
    total_div = total_div - 1;
  }
  var parentnode = $(manage_id + '_group_main').parentNode;
  var childnode =  $(manage_id + '_group_main');

  en4.core.request.send(new Request.JSON({
    url : url,
     
    data : {
      format : 'json',
      managedelete_id : manage_id,
      owner_id : owner_id,
      group_id :group_id
    },
    onSuccess : function(responseJSON) {
      parentnode.removeChild(childnode);
      if (owner_id == viewer_id )
      {
        window.location = url;
      }
      if(total_div == 0) {
      //$(manage_id+ '_group').innerHTML = '';
      //parentnode.removeChild(childnode);

      } else {
        // $(manage_id+ '_group').innerHTML = '';
        $('count_div').value = total_div;
      }
    }
  }))
};
//var execute_Request = 1;
var ShowContent = function (tabid, execute_Request, temptabid, type, modulename, widgetname,
  group_showtitle,group_url, ads_display, group_communityad_integration, adwithoutpackage, itemCount,
  itemCount_photo, resourcetype, albumorder, changeurl,title_truncation,show_posted_date) {
  
  //EXECUTE THIS BELOW LINE IF YOU HAVE TO HIDE GROUP LEFT DIV.
  if (typeof group_url == 'undefined' || group_url == null) {
    var group_url = '';
  }
  
  if((group_communityads == 1 && ads_display != 0 && group_communityad_integration == 1 && adwithoutpackage == 1)  || execute_Request == false)
		hideLeftContainer(ads_display, group_communityad_integration, adwithoutpackage);

  $('id_'+ tabid).innerHTML = '<div class="seaocore_content_loader"></div>';
  var url = en4.core.baseUrl + 'widget/index/mod/' + modulename+ '/name/'+widgetname;
  
   
  if(typeof changeurl != 'undefined' && changeurl == 1) {        
    var integratedUrl= en4.core.baseUrl + group_url + '/resource_type/' + resourcetype+ '/tab/'+tabid;
    if (history.pushState)
      history.pushState( {}, document.title, integratedUrl );
    else{
      window.location.hash = integratedUrl;
    } 
  } 
  scrollToTopForGroup($('id_'+ tabid));
  var callBackReturn=function(){    
    if(group_showtitle == 1) {
      if($('layout_' + type)) {
        $('layout_'+ type).style.display = 'block'; 
      }
    }
    if(typeof type != 'undefined' && type == 'document' && window.addCarousal) {
      addCarousal();      	
    }

    if (window.InitiateAction) {
      InitiateAction ();
    }     
  };
  if(modulename == 'sitegroupmember'){
    en4.sitegroupmember.profileTabRequest({
      content_id : tabid,
      callBackFunction:callBackReturn
    });
  }  else if(modulename == 'siteevent'){
    en4.siteeventcontenttype.profileTabRequest({
      content_id : tabid,
      callBackFunction:callBackReturn
    });
  }else{
    temp2 = new Request.HTML({ 
      method: 'get',
      'url': url,
      'data' : {
        'group_url': group_url,
        'format' : 'html',
        'subject' : en4.core.subject.guid,
        'isajax' : '1',
        'identity_temp' : temptabid,
        'itemCount' : itemCount,
        'itemCount_photo' : itemCount_photo,
        'resource_type' : resourcetype,
        'albumsorder': albumorder,
        'title_truncation': title_truncation,
        'show_posted_date' : show_posted_date
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('id_'+ tabid).innerHTML = responseHTML;

         
        if (modulename == 'sitegroupvideo') {
          showsearchvideocontent ();
          if(en4.sitevideoview)
            en4.sitevideoview.attachClickEvent(Array('item_photo_sitegroupvideo_video','sitegroupvideo_profile_title'));
        }
       	 
        else if (modulename == 'sitegroupdocument') {
          showsearchdocumentcontent ();
        }
       	 
        else if (modulename == 'sitegroupnote') {
          showsearchnotecontent ();
        }
       	 
        else if (modulename == 'sitegrouppoll') {
          showsearchpollcontent ();
        }
       	 
        else if (modulename == 'sitegroupmember') { 
          showsearchmembercontent(tabid);
        }
			
        else if (modulename == 'sitegroupevent') {
          showsearcheventcontent ();
        }
        else if (modulename == 'sitegroupintegration') {
          showsearchnotecontent ();
        }
        else if(modulename == 'sitegroupmusic') {
          if(en4.sitegroupmusic)
            en4.sitegroupmusic.player.enablePlayers();		  
          if(window.showlink) { 
            showlink();
          }
        }
        callBackReturn();
      //        if(group_showtitle == 1) {
      //          if($('layout_' + type)) {
      //            $('layout_'+ type).style.display = 'block'; 
      //          }
      //        }
      //        if(typeof type != 'undefined' && type == 'document' && window.addCarousal) {
      //          addCarousal();      	
      //        }
      //
      //        if (window.InitiateAction) {
      //          InitiateAction ();
      //        }     
      }
    });
    temp2.send();
  }
}

//EXECUTING CODE WHEN DOM IS READY IF NECESSARY
 
var hideLeftContainer = function (ads_display, group_communityad_integration, adwithoutpackage) {
  if(group_communityads == 1 && ads_display != 0 && group_communityad_integration != 0 && adwithoutpackage == 1) {
		
		if(group_hide_left_container == 0)
		  return;
		
    //EXECUTE THIS BELOW LINE IF YOU HAVE TO HIDE GROUP LEFT DIV.
    var leftcontainer = $('global_content').getElement('.layout_left');
    var rightcontainer = $('global_content').getElement('.layout_right');  
    if (leftcontainer) {
      if (leftcontainer.style.display != 'none') { 
        leftcontainer.setStyle('display', 'none');
        if($('thumb_icon')) {
          $('thumb_icon').style.display = 'block';
        }		
      }
      else {	   
        return;
      }
    }
    if(rightcontainer) {
      if (rightcontainer.style.display != 'none') { 
        rightcontainer.setStyle('display', 'none');
        if($('thumb_icon')) {
          $('thumb_icon').style.display = 'block';
        }			

      }
      else {
        return;
      }
    }
		if($('global_content').getElement('.layout_sitegroup_group_cover_information_sitegroup')) { 	                            			  $('global_content').getElement('.layout_sitegroup_group_cover_information_sitegroup').style.display = 'none';
		}

		if($('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo')) { 	                            $('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo').style.display = 'block';
		}
		if($('global_content').getElement('.layout_sitegroupmember_groupcover_photo_sitegroupmembers')) { 	                            $('global_content').getElement('.layout_sitegroupmember_groupcover_photo_sitegroupmembers').style.display = 'none';
		}	
  }
}
 
var ShowUrlColumn = function (group_id) {
  var e4 = $('group_url_msg-wrapper');
  if($('group_url_msg-wrapper')) {
    $('group_url_msg-wrapper').setStyle('display', 'none');
  }
  if($('group_url-element')) {
    var groupurlcontainer = $('group_url-element');
    var language = en4.core.language.translate('Check Availability');
    var newdiv = document.createElement('div');
    newdiv.id = 'url_varify';
    newdiv.innerHTML = '<a href="javascript:void(0);"  name="check_availability" id="check_availability" onclick="GroupUrlBlur(\'' + group_id + '\');return false;" class="check_availability_button">'+language+'</a> <br />';

    groupurlcontainer.insertBefore(newdiv, groupurlcontainer.childNodes[2]);
    checkDraft();
  }
}


function checkDraft(){
  if($('draft')){
    if($('draft').value==0) {
      $("search-wrapper").style.display="none";
      $("search").checked= false;
    } else{
      $("search-wrapper").style.display="block";
      $("search").checked= true;
    }
  }
}


function GroupUrlBlur(group_id) {
  if ($('group_url_alert') == null) {
    var groupurlcontainer = $('group_url-element');
    var newdiv = document.createElement('span');
    newdiv.id = 'group_url_alert';
    newdiv.innerHTML = '<img src="'+en4.core.staticBaseUrl+'application/modules/Sitegroup/externals/images/loading.gif" />';
    groupurlcontainer.insertBefore(newdiv, groupurlcontainer.childNodes[3]);
  }
  else {
    $('group_url_alert').innerHTML = '<img src="'+en4.core.staticBaseUrl+'application/modules/Sitegroup/externals/images/loading.gif" />';
  }

  //var url = '<?php echo $this->url(array('action' => 'groupurlvalidation' ), 'sitegroup_general', true);?>';
  en4.core.request.send(new Request.JSON({
    url : en4.core.baseUrl + 'sitegroup/index/groupurlvalidation',
    method : 'get',
    data : {
      group_url : $('group_url').value,
      check_url : 1,
      group_id : group_id,
      format : 'html'
    },

    onSuccess : function(responseJSON) {
      //$('group_url_msg-wrapper').setStyle('display', 'block');
      if (responseJSON.success == 0) {
        $('group_url_alert').innerHTML = responseJSON.error_msg;
        if ($('group_url_alert')) {
          $('group_url_alert').innerHTML = responseJSON.error_msg;
        }
      }
      else {
        $('group_url_alert').innerHTML = responseJSON.success_msg;
        if ($('group_url_alert')) {
          $('group_url_alert').innerHTML = responseJSON.success_msg;
        }
      }
    }
  }));
}

var ShowDashboardGroupContent = function (GroupUrl, GroupId,show_url,edit_url,group_id) {

  $('show_tab_content').innerHTML = '<center><img src="'+en4.core.staticBaseUrl+'application/modules/Sitegroup/externals/images/spinner_temp.gif" /></center>'; 
  var request = new Request.HTML({
    'url' : GroupUrl,
    'method' : 'get',
    'data' : {
      'format' : 'html',
      'is_ajax' : 1
                
    },
    onSuccess :  function(responseTree, responseElements, responseHTML, responseJavaScript)  {
      if (Show_Tab_Selected) {
        $('id_'+ Show_Tab_Selected).set('class', '');
        Show_Tab_Selected = GroupId;
      }	
      $('id_' + GroupId).set('class', 'selected');
				
      $('show_tab_content').innerHTML = responseHTML; 
      if (window.InitiateAction) {
        InitiateAction ();
      }

      if (($type(show_url) && show_url == 1) && ($type(edit_url) && edit_url == 1)) {
        ShowUrlColumn(group_id);
      }
      if (window.activ_autosuggest) { 
        activ_autosuggest ();
      }
      
      var e4 = $('group_url_msg-wrapper');
      if($('group_url_msg-wrapper'))
        $('group_url_msg-wrapper').setStyle('display', 'none');
				
			if(typeof cat != 'undefined' && typeof subcatid != 'undefined' && typeof subcatname != 'undefined' && typeof subsubcatid != 'undefined') {
				subcategory(cat, subcatid, subcatname,subsubcatid);
			}
			
      if (document.getElementById("category_name")) {
				$('category_name').focus();
			}
      en4.core.runonce.trigger();
    }
    
  });

  request.send();
}


var requestActive = false;
window.addEvent('load', function() {
  formElement = $('global_wrapper').getElementById('global_content').getElement('.global_form');
  if (formElement && typeof formElement != 'undefined' ) {
    formElement.addEvent('submit', function(event) { 
      if (typeof submitformajax != 'undefined' && submitformajax == 1) {
        submitformajax = 0;
        event.stop();
        Savevalues();
      }
    });
  };
});

var InitiateAction = function () { 
  formElement = $('global_wrapper').getElementById('global_content').getElement('.global_form');
  if (formElement && typeof formElement != 'undefined' ) {
    formElement.addEvent('submit', function(event) {
      if (typeof submitformajax != 'undefined' && submitformajax == 1) {
        submitformajax = 0;
        event.stop();
        Savevalues();
      }
    })
  };
}

var Savevalues = function() {
  if( requestActive ) return;

  if (!($('category_name'))) {
    if( typeof manage_admin_formsubmit != 'undefined' && manage_admin_formsubmit == 1 ) {
      if($('user_id').value == '' )
      {
        submitformajax = 1;
        return;
      }
      else
      {
        manage_admin_formsubmit = 1;
      }
    }
  } else {
    if($('category_name').value == '' )
    {
      submitformajax = 1;
      return;
    }
    else
    {
      manage_admin_formsubmit = 1;
    }

  }
  requestActive = true;
  var groupurl = $('global_content').getElement('.global_form').action;
  if ($('subject') && groups_id) {
    $('subject').value = 'sitegroup_group_' + groups_id;

  }  
  currentValues = formElement.toQueryString();
  $('show_tab_content_child').innerHTML = '<center><img src="'+en4.core.staticBaseUrl+'application/modules/Sitegroup/externals/images/spinner_temp.gif" /></center>';
  if (typeof group_url != 'undefined') {
    var param = (currentValues ? currentValues + '&' : '') + 'is_ajax=1&format=html&group_url=' + group_url;
  }
  else {
    var param = (currentValues ? currentValues + '&' : '') + 'is_ajax=1&format=html';
  }

  var request = new Request.HTML({
    url: groupurl,
    onSuccess :  function(responseTree, responseElements, responseHTML, responseJavaScript)  {
      if ($('show_tab_content')) { 
        $('show_tab_content').innerHTML =responseHTML;
      }
        
      else if ($('show_tab_content_child')) { 
        $('show_tab_content_child').innerHTML =responseHTML;
      }
      InitiateAction (); 
      requestActive = false;
      if (window.activ_autosuggest) { 
        activ_autosuggest ();
      }
      if (document.getElementById("category_name")) {
				$('category_name').focus();
		  }
      
    }
  });
  request.send(param);
}


en4.sitegroup = {

  rotate : function(photo_id, angle) {
    request = new Request.JSON({
      url : en4.core.baseUrl + 'sitegroup/photo/rotate',
      data : {
        format : 'json',
        photo_id : photo_id,
        angle : angle
      },
      onComplete: function(response) {
        // Check status
        if( $type(response) == 'object' &&
          $type(response.status) &&
          response.status == false ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
          return;
        } else if( $type(response) != 'object' ||
          !$type(response.status) ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
          return;
        }
        $('media_image').src=response.href;
        // Ok, let's refresh the group I guess
        if($('canReload'))
          $('canReload').value=1;
        else
          window.location.reload(true);
        $('media_image').style.marginTop="0px";
      }
    });
    request.send();
    return request;
  },

  flip : function(photo_id, direction) {
    request = new Request.JSON({
      url : en4.core.baseUrl + 'sitegroup/photo/flip',
      data : {
        format : 'json',
        photo_id : photo_id,
        direction : direction
      },
      onComplete: function(response) {
        // Check status
        if( $type(response) == 'object' &&
          $type(response.status) &&
          response.status == false ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
          return;
        } else if( $type(response) != 'object' ||
          !$type(response.status) ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
          return;
        }
        $('media_image').src=response.href;
        // Ok, let's refresh the group I guess
        if($('canReload'))
          $('canReload').value=1;
        else
          window.location.reload(true);
        $('media_image').style.marginTop="0px";
      }
    });
    request.send();
    return request;
  },
  groupStatistics : function(group_id){
    new Request.JSON({
      url : en4.core.baseUrl + 'sitegroup/insights/group-statistics',
      data : {
        format : 'json',
        group_id : group_id
      }
    }).send();
  }

};

en4.sitegroup.ajaxTab = {
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
        if (en4.sitegroup.ajaxTab.click_elment_id == widget_id)
          return;
        en4.sitegroup.ajaxTab.click_elment_id = widget_id;
        en4.sitegroup.ajaxTab.sendReq(params);
      });
      element.store('addClickEvent', true);
      var attachOnLoadEvent = false;
      if (tab_content_id_sitegroup == widget_id) {
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
                if (addActiveTab) {
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
      en4.sitegroup.ajaxTab.click_elment_id = widget_id;
      en4.sitegroup.ajaxTab.sendReq(params);
    });


  },
  sendReq: function(params) {
    params.responseContainer.each(function(element) { 
      if((typeof params.loading) == 'undefined' || params.loading==true){
       element.empty();
      new Element('div', {
        'class': 'sitegroup_profile_loading_image'
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

var hideWidgetsForModule = function(widgetname) {

// 	if(widgetname == 'sitegroupmember') {
// 		if(sitegroup_layout_setting == 0) {
// 			if($('global_content').getElement('.layout_sitegroup_group_cover_information_sitegroup')) {
// 				$('global_content').getElement('.layout_sitegroup_group_cover_information_sitegroup').style.display = 'block';
// 			}
// 
// 			if($('global_content').getElement('.layout_sitegroupmember_groupcover_photo_sitegroupmembers')) {
// 				$('global_content').getElement('.layout_sitegroupmember_groupcover_photo_sitegroupmembers').style.display = 'block';
// 			}
// 		} else {
// 			if($('global_content').getElement('.layout_sitegroup_group_cover_information_sitegroup')) {
// 				$('global_content').getElement('.layout_sitegroup_group_cover_information_sitegroup').style.display = 'block';
// 			}
// 
// 			if($('global_content').getElement('.layout_sitegroupmember_groupcover_photo_sitegroupmembers')) {
// 				$('global_content').getElement('.layout_sitegroupmember_groupcover_photo_sitegroupmembers').style.display = 'block';
// 			}
// 		}
// 	}
// 	else {
// 		if(sitegroup_layout_setting == 0) {
// 			if($('global_content').getElement('.layout_sitegroup_group_cover_information_sitegroup')) {
// 				$('global_content').getElement('.layout_sitegroup_group_cover_information_sitegroup').style.display = 'block';
// 			}
// 
// 			if($('global_content').getElement('.layout_sitegroupmember_groupcover_photo_sitegroupmembers')) {
// 				$('global_content').getElement('.layout_sitegroupmember_groupcover_photo_sitegroupmembers').style.display = 'block';
// 			}
// 		} else {
// 			if($('global_content').getElement('.layout_sitegroup_group_cover_information_sitegroup')) {
// 				$('global_content').getElement('.layout_sitegroup_group_cover_information_sitegroup').style.display = 'block';
// 			}
// 
// 			if($('global_content').getElement('.layout_sitegroupmember_groupcover_photo_sitegroupmembers')) {
// 				$('global_content').getElement('.layout_sitegroupmember_groupcover_photo_sitegroupmembers').style.display = 'block';
// 			}
// 		}
// 	}
	
	if(sitegroup_layout_setting == 1) {
		 return;
	}
	
  if(widgetname == 'sitegroupactivityfeed') {
    if($('global_content').getElement('.layout_activity_feed')) {
      $('global_content').getElement('.layout_activity_feed').style.display = 'block';
    }
  }
  else {
    if($('global_content').getElement('.layout_activity_feed')) {
      $('global_content').getElement('.layout_activity_feed').style.display = 'none';
    }
  }
  if(widgetname == 'sitegroupadvancedactivityactivityfeed') {
    if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
      $('global_content').getElement('.layout_advancedactivity_home_feeds').style.display = 'block';
    }
  }
  else {
    if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
      $('global_content').getElement('.layout_advancedactivity_home_feeds').style.display = 'none';
    }
  }
  
  if(widgetname == 'sitegroupseaocoreactivityfeed') {
    if($('global_content').getElement('.layout_seaocore_feed')) {
      $('global_content').getElement('.layout_seaocore_feed').style.display = 'block';
    }
  } else {
    if($('global_content').getElement('.layout_seaocore_feed')) {
      $('global_content').getElement('.layout_seaocore_feed').style.display = 'none';
    }
  }

  if(widgetname == 'sitegroupinfo') {
    if($('global_content').getElement('.layout_sitegroup_info_sitegroup')) {
      $('global_content').getElement('.layout_sitegroup_info_sitegroup').style.display = 'block';
    }
  }
  else {
    if($('global_content').getElement('.layout_sitegroup_info_sitegroup')) {
      $('global_content').getElement('.layout_sitegroup_info_sitegroup').style.display = 'none';
    }
  }
  if(widgetname == 'sitegroupoverview') {
    if($('global_content').getElement('.layout_sitegroup_overview_sitegroup')) {
      $('global_content').getElement('.layout_sitegroup_overview_sitegroup').style.display = 'block';
    }
  }
  else {
    if($('global_content').getElement('.layout_sitegroup_overview_sitegroup')) {
      $('global_content').getElement('.layout_sitegroup_overview_sitegroup').style.display = 'none';
    }
  }
  if(widgetname == 'sitegrouplocation') {
    if($('global_content').getElement('.layout_sitegroup_location_sitegroup')) {
      $('global_content').getElement('.layout_sitegroup_location_sitegroup').style.display = 'block';
    }
  }
  else {
    if($('global_content').getElement('.layout_sitegroup_location_sitegroup')) {
      $('global_content').getElement('.layout_sitegroup_location_sitegroup').style.display = 'none';
    }
  }
  if(widgetname == 'sitegrouplink') {
    if($('global_content').getElement('.layout_core_profile_links')) {
      $('global_content').getElement('.layout_core_profile_links').style.display = 'block';
    }
  }
  else {
    if($('global_content').getElement('.layout_core_profile_links')) {
      $('global_content').getElement('.layout_core_profile_links').style.display = 'none';
    }
  }

  if(widgetname == 'sitegroupintegration') {
    if($('global_content').getElement('.layout_sitegroupintegration_profile_items')) {
      $('global_content').getElement('.layout_sitegroupintegration_profile_items').style.display = 'block';
    }
  }
  else {
    if($('global_content').getElement('.layout_sitegroupintegration_profile_items')) {
      $('global_content').getElement('.layout_sitegroupintegration_profile_items').style.display = 'none';
    }
  }
//   if(widgetname == 'sitegrouptwitter') {
//     if($('global_content').getElement('.layout_sitegrouptwitter_feeds_sitegrouptwitter')) {
//       $('global_content').getElement('.layout_sitegrouptwitter_feeds_sitegrouptwitter').style.display = 'block';
//     }
//   }
//   else {
//     if($('global_content').getElement('.layout_sitegrouptwitter_feeds_sitegrouptwitter')) {
//       $('global_content').getElement('.layout_sitegrouptwitter_feeds_sitegrouptwitter').style.display = 'none';
//     }
//   }
}

var deleteMemberCategory = function(manage_id, url, group_id)
{
  var total_div = $('count_div').value;
  if(total_div >=  1 ) {
    total_div = total_div - 1;
  }
  var parentnode = $(manage_id + '_group').parentNode;
  var childnode =  $(manage_id + '_group');
	
  en4.core.request.send(new Request.JSON({
    url : url,
		
    data : {
      format : 'json',
      category_id : manage_id,
      group_id :group_id
    },
    onSuccess : function(responseJSON) {
      parentnode.removeChild(childnode);
      if(total_div == 0) {
      //$(manage_id+ '_group').innerHTML = '';
      //parentnode.removeChild(childnode);
      } else {
        // $(manage_id+ '_group').innerHTML = '';
        $('count_div').value = total_div;
      }
      if (document.getElementById("category_name")) {
				$('category_name').focus();
		  }
    }
  }))
};
en4.sitegroupmember={
  profileTabParams:Array(),

  profileTabRequest : function(options){

    var params=en4.sitegroupmember.profileTabParams[options.content_id];
    var url = en4.core.baseUrl+'widget';
    if(params.requestUrl)
      url= params.requestUrl;
    
    params.requestParams.content_id = options.content_id;
    if(params.searchFormElement && $(params.searchFormElement)){
      url= url +'?'+$(params.searchFormElement).toQueryString();
    }
    if(params.loadingElement && $(params.loadingElement)){
      $(params.loadingElement).innerHTML = '<div class="seaocore_content_loader"></div>'; 
    }
    if(options.requestParams){
      params.requestParams = $merge(params.requestParams , options.requestParams);
    }
    var request = new Request.HTML({
      url : url,
      data : $merge(params.requestParams,{
        format : 'html',
        subject: en4.core.subject.guid,
        is_ajax_load:true
      }),
      evalScripts : true,
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        var container = $('id_'+options.content_id);
        //  params.responseContainer.each(function(container){
        container.empty();
        Elements.from(responseHTML).inject(container);
        en4.core.runonce.trigger();
        Smoothbox.bind(container);
        //  });
        if(options.callBackFunction){
          options.callBackFunction();
        }
      }
    });
    request.send();
  }
}


en4.siteeventcontenttype={
  profileTabParams:Array(),

  profileTabRequest : function(options){

    var params=en4.siteeventcontenttype.profileTabParams[options.content_id];
    var url = en4.core.baseUrl+'widget';
    if(params.requestUrl)
      url= params.requestUrl;
    
    params.requestParams.content_id = options.content_id;
    if(params.searchFormElement && $(params.searchFormElement)){
      url= url +'?'+$(params.searchFormElement).toQueryString();
    }
    if(params.loadingElement && $(params.loadingElement)){
      $(params.loadingElement).innerHTML = '<div class="seaocore_content_loader"></div>'; 
    }
    if(options.requestParams){
      params.requestParams = $merge(params.requestParams , options.requestParams);
    }
    var request = new Request.HTML({
      url : url,
      data : $merge(params.requestParams,{
        format : 'html',
        subject: en4.core.subject.guid,
        is_ajax_load:true
      }),
      evalScripts : true,
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        var container = $('id_'+options.content_id);
        //  params.responseContainer.each(function(container){
        container.empty();
        Elements.from(responseHTML).inject(container);
        en4.core.runonce.trigger();
        Smoothbox.bind(container);
        //  });
        if(options.callBackFunction){
          options.callBackFunction();
        }
      }
    });
    request.send();
  }
}

var SitegroupCoverPhoto = new Class({
  Implements:[Options],
  options:{
    element:null,
    buttons:'seao_cover_options',
    photoUrl:'',
    position_url:'',
    position:{
      top:0,
      left:0
    }
  },
  block:null,
  buttons:null,
  element:null,
  changeButton:null,
  saveButton:null,

  initialize:function (options) {
    if (options.block == null) {
      return;
    }
    this.block=options.block;
    this.setOptions(options);
    //    this.block = this.element.getParent();
    this.getCoverPhoto();
  },
  attach:function () {
    var self = this;
    if(!$(this.options.buttons)){
      return;
    }
    this.element = self.block.getElement('.cover_photo');
    this.buttons = $(this.options.buttons);
    this.saveButton = this.buttons.getElement('.save-button');
    this.editButton = this.buttons.getElement('.edit-button');
    if(this.saveButton){
      this.saveButton.getElement('.positions-save').addEvent('click', function () {
        self.reposition.save();
      });
      this.saveButton.getElement('.positions-cancel').addEvent('click', function () {
        self.reposition.stop(1);
      });
    }
  },

  get:function (type) {
    if (type == 'block') {
      return this.block;
    }

    return this.element;
  },

  getButton:function (type) {
    if (type == 'save') {
      return this.saveButton;
    }

    return this.editButton;
  },
  getCoverPhoto:function (reposition_enable) {
    var self = this;

    new Request.HTML({
      'method':'get',
      'url':self.options.photoUrl,
      'data':{
        'format':'html'
      },
      'onComplete':function (responseTree, responseElements, responseHTML, responseJavaScript) {
        //        var block = self.get('block');
        //        var cover_block = block.getElementById('tl-cover');
        //
        if ( responseHTML.length > 0) {
          self.block.set('html', responseHTML);
          Smoothbox.bind(self.block);
          self.attach();
          if(reposition_enable){
            Smoothbox.close();
            self.options.position={
              top: 0,
              left : 0
            };
            setTimeout(function () {
              self.reposition.start()
            }, '2000');
          }
        }
      }
    }).send();
  },
  reposition:{
    drag:null,
    active:false,
    start:function () {
      if (this.active) {
        return;
      }

      var self = document.seaoCoverPhoto;
      var cover = self.get();
      this.active = true;
      //self.getButton().fireEvent('click');
  
      self.getButton().addClass('dnone');
      self.buttons.addClass('sitegroup_cover_options_btm');
      self.getButton('save').removeClass('dnone');
      if(self.options.columnHeight && self.block.getElement('.cover_photo_wap')){
        self.block.setStyle('height',self.options.columnHeight+'px');
      }
      self.block.getElement('.cover_tip_wrap').removeClass('dnone');
      cover.addClass('draggable');
      var cont = cover.getParent();

      var verticalLimit = cover.offsetHeight.toInt() - cont.offsetHeight.toInt();
      var horizontalLimit = cover.offsetWidth.toInt() - cont.offsetWidth.toInt();
      var limit = {
        x:[0, 0], 
        y:[0, 0]
      };

      if (verticalLimit > 0) {
        limit.y = [-verticalLimit, 0]
      }

      if (horizontalLimit > 0) {
        limit.x = [-horizontalLimit , 0]
      }

      this.drag = new Drag(cover, {
        limit:limit,
        onComplete:function (el) {
          self.options.position.top = el.getStyle('top').toInt();
          self.options.position.left = el.getStyle('left').toInt();
        }
      }).detach();

      this.drag.attach();
    },
    stop:function(reload){
      var self =document.seaoCoverPhoto;
      self.reposition.drag.detach();
      self.getButton('save').addClass('dnone');
      self.block.getElement('.cover_tip_wrap').addClass('dnone');
      self.buttons.removeClass('sitegroup_cover_options_btm');
      self.getButton().removeClass('dnone');

      self.get().removeClass('draggable');
      self.reposition.drag = null;
      self.reposition.active = false;
      if(reload)
        self.getCoverPhoto();
    },
    save:function () {
      if (!this.active) {
        return;
      }
      var self = document.seaoCoverPhoto;
      var current = this;
      new Request.JSON({
        method:'get',
        url:self.options.positionUrl,
        data:{
          'format':'json', 
          'position':self.options.position
        },
        onSuccess:function (response) {
          current.stop();
        }
      }).send();
    }
  }
});

function scrollToTopForGroup(id) {
	
	if(sitegroup_slding_effect == 0)
	  return false;	
		
  if(document.getElement('body').get('id'))	{
		var scroll = new Fx.Scroll(document.getElement('body').get('id'), {
			wait: false,
			duration: 1000,
			offset: {
				'x': -200, 
				'y': -100
			},
			transition: Fx.Transitions.Quad.easeInOut
		});

		scroll.toElement(id);  
	}
	return;
}

function setLeftLayoutForGroup(showLayout) {
	
		if(group_hide_left_container == 0)
		return;
	
   if(showLayout == 1) {
			if ($$('.layout_left')) {
				$$('.layout_left').setStyle('display', 'none');
				if($('thumb_icon')) {
					$('thumb_icon').style.display = 'block';
				}
			} 
			if($$('.layout_right')) {
				$$('.layout_right').setStyle('display', 'none');
				if($('thumb_icon')) {
					$('thumb_icon').style.display = 'block';
				}
			}
			
			if($('global_content').getElement('.layout_sitegroup_group_cover_information_sitegroup')) { 	                            	$('global_content').getElement('.layout_sitegroup_group_cover_information_sitegroup').style.display = 'none';
			}
			
			if($('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo')) { 	                            $('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo').style.display = 'block';
			}

			if($('global_content').getElement('.layout_sitegroupmember_groupcover_photo_sitegroupmembers')) { 	                              $('global_content').getElement('.layout_sitegroupmember_groupcover_photo_sitegroupmembers').style.display = 'none';
			}	
	 } else {
			if ($$('.layout_left')) {
				$$('.layout_left').setStyle('display', 'block');
				if($('thumb_icon')) {
					$('thumb_icon').style.display = 'none';
				}
			} 
			if($$('.layout_right')) {
				$$('.layout_right').setStyle('display', 'block');
				if($('thumb_icon')) {
					$('thumb_icon').style.display = 'none';
				}
			}
			
			if($('global_content').getElement('.layout_sitegroup_group_cover_information_sitegroup')) { 	                            	$('global_content').getElement('.layout_sitegroup_group_cover_information_sitegroup').style.display = 'block';
			}
			
			if($('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo')) { 	                            $('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo').style.display = 'block';
			}
			
			if($('global_content').getElement('.layout_sitegroupmember_groupcover_photo_sitegroupmembers')) { 	                                 $('global_content').getElement('.layout_sitegroupmember_groupcover_photo_sitegroupmembers').style.display = 'block';
			}	
	 }
}