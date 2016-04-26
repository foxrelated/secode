/* $Id: core.js 2010-11-04 9:40:21Z SocialEngineAddOns Copyright 2009-2010 BigStep Technologies Pvt. Ltd. $ */

var content_type_undefined;
var content_typo = '';

en4.sitelike = {
  getToolTip: function(poster_id,poster_type,resource_id,resource_type){ 
    var request = new Request.HTML({
      'url' :  en4.core.baseUrl + 'sitelike/index/get-tool-tip',
      'data' : {
        'format' : 'html',
        'task' : 'ajax',
        'poster_id' : poster_id,
        'poster_type' : poster_type,
        'resource_id' : resource_id,
        'resource_type' : resource_type,
        'type':"tooltip"
    
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        var id="tooltip_"+poster_id+"_"+resource_type+"_"+resource_id;
        $(id).style.display="block";
        $(id).innerHTML = responseHTML;
      }
    });

    request.send();

  }
};
en4.sitelike.do_like = {

  // FUNCTION FOR CREATING A FEEDBACK 
  createLike : function( resource_id, resource_type, content_type )
  {
    if (content_type == 'browsemixinfo') {
      var like_id = $(resource_type + '_browselike_'+ resource_id).value
      content_type = resource_type
    }
    else if (content_type == 'mixinfo') {
      var like_id = $(resource_type + '_mixinfolike_'+ resource_id).value
      content_type = resource_type
    }
    else if (content_type == 'welcomemixinfo') {
      var like_id = $(resource_type + '_welcomemixinfolike_'+ resource_id).value
      content_type = resource_type
    }
    else {
      if($(content_type + '_like_'+ resource_id))
        var like_id = $(content_type + '_like_'+ resource_id).value
    }
   
    var request = new Request.JSON({
      url : en4.core.baseUrl + 'sitelike/index/globallikes',
      data : {
        format : 'json',
        'resource_id' : resource_id,
        'resource_type' : resource_type,
        'like_id' : like_id
      }
    });
    request.send();
    return request;
  }
		
},


window.addEvent('domready',function() {
  //opacity / display fix
  $$('.jq-checkpointSubhead').setStyles({
    opacity: 0,
    display: 'none'
  });
  //put the effect in place
  $$('.jq-checkpoints li').each(function(el,i) {
    el.addEvents({
      'mouseenter': function() {
        el.getElement('div').style.display = 'block';
        el.getElement('div').fade('in');
      },
      'mouseleave': function() {
        el.getElement('div').style.display = 'none';
        el.getElement('div').fade('out');
      }
    });
  });
});

var update_tooltip = function () {
  //opacity / display fix
  $$('.jq-checkpointSubhead').setStyles({
    opacity: 0,
    display: 'none'
  });
  //put the effect in place
  $$('.jq-checkpoints li').each(function(el,i) {
    el.addEvents({
      'mouseenter': function() {
        el.getElement('div').style.display = 'block';
        el.getElement('div').fade('in');
      },
      'mouseleave': function() {
        el.getElement('div').style.display = 'none';
        el.getElement('div').fade('out');
      }
    });
  });
}

var user_likes_profile = function(resource_id, resource_type) {
  var content_type = resource_type;
  // SENDING REQUEST TO AJAX
  var request = en4.sitelike.do_like.createLike(resource_id, resource_type,content_type);
  // RESPONCE FROM AJAX
  request.addEvent('complete', function(responseJSON) {
    if(responseJSON.like_id )
    {
      $(resource_type +'_like_'+ resource_id).value = responseJSON.like_id;
      $(resource_type +'_most_likes_'+ resource_id).style.display = 'none';
      $(resource_type +'_unlikes_'+ resource_id).style.display = 'block';
    //$('blog_num_of_like_'+ resource_id).innerHTML = responseJSON.num_of_like;
    }
    else
    {
      $(resource_type +'_like_'+ resource_id).value = 0;
      $(resource_type +'_most_likes_'+ resource_id).style.display = 'block';
      $(resource_type +'_unlikes_'+ resource_id).style.display = 'none';
    // $('blog_num_of_like_'+ resource_id).innerHTML = responseJSON.num_of_like;
    }
  });
  
}
var show_app_likes = function (app_name, thisobj) {
 
  if($('dynamic_app_info') != null) {
		
		$('dynamic_app_info').innerHTML = '<center><img src="application/modules/Seaocore/externals/images/loading.gif" /></center>';
  }

  if ($type($(active_tab + '_' + active_tab))) {
    $(active_tab + '_' + active_tab).erase('class');
  }

  $(app_name + '_' + app_name).set('class', 'selected');
  active_tab = app_name;
  var showappinfo = new Request.HTML({
    'url' : url,
    'data' : {
      'format' : 'html',
      'resource_type' : app_name,
      'isajax' : 1
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      $('dynamic_app_info').innerHTML = responseHTML;
			en4.core.runonce.trigger();
      
    }
  });

  showappinfo.send ();
}

var show_browse_mixinfo = function  (tab_show) { 
  var active_tab_old = active_tab;
  if($('browse_mixinfo_global_content') != null)
  {
		$('browse_mixinfo_global_content').innerHTML = '<center><img src="application/modules/Seaocore/externals/images/loading.gif" /></center>';
  }

  var request = new Request.HTML({
    'url' : url_browsemixinfo,
    'data' : {
      'format' : 'html',
      'task' : 'ajax',
      'tab_show' : tab_show,
      'isajax' : 1
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      $('dynamic_app_like_anchor').getParent().innerHTML = responseHTML;
			en4.core.runonce.trigger();
      
    }
  });
									
  request.send();
}

var paginatebrowseapplikes = function(page) {
   
  if($('browse_mixinfo_global_content') != null)
  {
		$('browse_mixinfo_global_content').innerHTML = '<center><img src="application/modules/Seaocore/externals/images/loading.gif" /></center>';
  }


  var request = new Request.HTML({
    'url' : url_browsemixinfo,
    'data' : {
      'format' : 'html',
      'tab_show' : active_tab,
      'isajax' : 1,
      'page' : page
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      $('dynamic_app_like_anchor').getParent().innerHTML = responseHTML;
                
      
    }
  });
									
  request.send();

   
}

var paginateapplikes = function(page) {
   
  if($('dynamic_app_info') != null) {
		
		$('dynamic_app_info').innerHTML = '<center><img src="application/modules/Seaocore/externals/images/loading.gif" /></center>';
  }

  var request = new Request.HTML({
    'url' : url,
    'data' : {
      'format' : 'html',
      'resource_type' : appname,
      'isajax' : 1,
      'page' : page
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      $('dynamic_app_info').innerHTML = responseHTML;
                
      
    }
  });
  request.send();
}

var app_likes = function(resource_id, resource_type, content_type) {
  // SENDING REQUEST TO AJAX
  var request = en4.sitelike.do_like.createLike(resource_id, resource_type,content_type);
  // RESPONCE FROM AJAX
  request.addEvent('complete', function(responseJSON) {
    if(responseJSON.like_id )
    {
      $(content_type + '_like_'+ resource_id).value = responseJSON.like_id;
      $(content_type + '_most_likes_'+ resource_id).style.display = 'none';
      $(content_type + '_unlikes_'+ resource_id).style.display = 'block';
      if(content_type == 'my-friend_' + resource_type) {
        $(content_type + '_num_of_like_'+ resource_id).innerHTML = '<a id="likes_viewall_link_' + resource_id + '"  onclick="showusers(this);return false;" class="likes_viewall_link" href="' + $('likes_viewall_link_' + resource_id).href + '">' + responseJSON.num_of_like  + '</a>';
      }
      else {
        $(content_type + '_num_of_like_'+ resource_id).innerHTML = responseJSON.num_of_like;
      }
    } else {
      $(content_type + '_like_'+ resource_id).value = 0;
      $(content_type + '_most_likes_'+ resource_id).style.display = 'block';
      $(content_type + '_unlikes_'+ resource_id).style.display = 'none';
      if(content_type == 'my-friend_' + resource_type) {
        $(content_type + '_num_of_like_'+ resource_id).innerHTML = '<a id="likes_viewall_link_' + resource_id + '"  onclick="showusers(this);return false;" class="likes_viewall_link" href="' + $('likes_viewall_link_' + resource_id).href + '">' + responseJSON.num_of_like  + '</a>';
      }
      else
      {
        $(content_type + '_num_of_like_'+ resource_id).innerHTML = responseJSON.num_of_like;
      }
    }
  });
}

var forums_likes = function(resource_id, resource_type, content_type) {
  // SENDING REQUEST TO AJAX
  var request = en4.sitelike.do_like.createLike(resource_id, resource_type,content_type);     
  // RESPONCE FROM AJAX
  request.addEvent('complete', function(responseJSON) {
    if(responseJSON.like_id ) {
      var option_string = ", 'forum_topic' , 'forum_topic'";
      var div_content = '<a id="display_num_of_like1" href="javascript:void(0);" onclick="forums_likes(' + resource_id +  option_string + ')"><i class="like_thumbdown_icon"></i><span>Unlike</span></i></a><input type="hidden" id="forum_topic_like_'+ resource_id + '"  value = "'+ responseJSON.like_id +'" />';
      $('like_button').innerHTML = div_content;
    } else {
      var option_string = ", 'forum_topic' , 'forum_topic'";
      var div_content = '<a id="display_num_of_like1" href="javascript:void(0);"  onclick="forums_likes(' + resource_id +  option_string + ')"><i class="like_thumbup_icon"></i><span>Like</span></i></a><input type="hidden" id="forum_topic_like_'+ resource_id + '"  value = "0" />';
      $('like_button').innerHTML = div_content;
	 
    }
  });
}

var show_duration_likes = function (module_tab_id, module_active_tab, content_html_id, module_name) {

  if (module_active_tab == 1) {
    $(module_tab_id + '2').erase('class');
    $(module_tab_id + '3').erase('class');
    $(module_tab_id + '1').set('class', 'active');
  } else if (module_active_tab == 2) {
    $(module_tab_id + '1').erase('class');
    $(module_tab_id + '3').erase('class');
    $(module_tab_id + '2').set('class', 'active');
  } else if(module_active_tab == 3) {
    $(module_tab_id + '1').erase('class');
    $(module_tab_id + '2').erase('class');
    $(module_tab_id + '3').set('class', 'active');
  }
  if($(content_html_id) != null) {
		$(content_html_id).innerHTML = '<center><img src="application/modules/Seaocore/externals/images/loading.gif" /></center>';
  }

  var request = new Request.HTML({
    'url' : url,
    'data' : {
      'format' : 'html',
      'task' : 'ajax',
      'tab_show' : module_active_tab,
      'modules' : module_name
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      $(content_html_id).innerHTML = responseHTML;
      update_tooltip ();
    }
  });
  request.send();
}

var sitelikeAttachClickEvent = function(el,duration){
  
  if(en4.sitevideoview){
    var openlightbox = 0;
    var img= el.getElements('img:last-child');
    en4.sitevideoview.attach_event_classes.each(function(className){
      if(img.hasClass(className) && img.hasClass(className)=='true'){
        openlightbox = 1;
      }
    });
    
    if(openlightbox == 1){
      en4.sitevideoview.open(el,duration);
    } else {
      window.location.href = el.get('href');
    }
  }else{
    window.location.href = el.get('href');
  }
  
}

en4.sitelike.ajaxTab = {
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
				if (en4.sitelike.ajaxTab.click_elment_id == widget_id)
					return;
				en4.sitelike.ajaxTab.click_elment_id = widget_id;
				en4.sitelike.ajaxTab.sendReq(params);
			});
			element.store('addClickEvent', true);
			var attachOnLoadEvent = false;
			if (tab_content_id_sitelike == widget_id) {
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
			en4.sitelike.ajaxTab.click_elment_id = widget_id;
			en4.sitelike.ajaxTab.sendReq(params);
		});
		
		
	},
	sendReq: function(params) {
		params.responseContainer.each(function(element) { 
			if((typeof params.loading) == 'undefined' || params.loading==true){
				element.empty();
				new Element('div', {
					'class': 'sitelike_profile_loading_image'
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