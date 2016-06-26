/* $Id: core.js 2014-05-26 00:00:00Z SocialEngineAddOns Copyright 2013-2014 BigStep Technologies Pvt. Ltd. $ */

var menuName = '';
var doAdvancedMiniMenuContentHide = '';
var urlActionName = '';
var tempFlag = 0;
var tempGetTabContent = 0;
var isUserLogin;
var hideUserFormLightBox = '';
var containerElementId = 0;
var previousParent = false; // VARIABLE FOR KEEPING THE PREVIOUSLY CLICKED MENU TAB
var scrollPosition={
  left:0,
  top:0
};

   en4.advancedMenu = { 
    obj_nav :  $("nav"),
    settings : {
      show_delay : 0,
      hide_delay : 0,
      _ie6 : /MSIE 6.+Win/.test(navigator.userAgent),
      _ie7 : /MSIE 7.+Win/.test(navigator.userAgent)
    },
    init : function(obj, level) {
      obj.lists = obj.getChildren();
      obj.lists.each(function(el,ind){
        en4.advancedMenu.handlNavElement(el);
        if((en4.advancedMenu.settings._ie6 || en4.advancedMenu.settings._ie7) && level){
          en4.advancedMenu.ieFixZIndex(el, ind, obj.lists.size());
        }
      });
      if(en4.advancedMenu.settings._ie6 && !level){
        document.execCommand("BackgroundImageCache", false, true);
      }
    },
   handlNavElement : function(list) {
      if(list !== undefined){
        if ('ontouchstart' in window) {
          list.getElement('a').removeEvent('mouseover');
          list.getElement('a').removeEvent('mousemove');
          list.getElement('a').removeEvent('mousedown');
          list.getElement('a').removeEvent('mouseup');
          list.getElement('a').addEvent('click', function(event) {
           // event.stop();
            var el = $(event.target).getParent('li'), open = el.retrieve("open", true);
            if(el.getElement("ul")){
              event.stop();
            }
            if (open && en4.advancedMenu.previousClick && en4.advancedMenu.previousClick != el) {
              var prev= en4.advancedMenu.previousClick;
              if (prev.hasClass('level1parent')) {
                en4.advancedMenu.fireNavEvent(prev, false);
                en4.advancedMenu.previousClick.store('open', true);         
                if(!el.hasClass('level1parent')){ 
                  var pre_prev = prev.getParent('li');  
                  en4.advancedMenu.fireNavEvent(pre_prev, false);
                  pre_prev.store('open', true);
                }
                
              }else if(!el.hasClass('level1parent') || el.getParent('li') != en4.advancedMenu.previousClick){
                en4.advancedMenu.fireNavEvent(en4.advancedMenu.previousClick, false);
                en4.advancedMenu.previousClick.store('open', true);
                if(en4.advancedMenu.previousClick.getElement('i')){
                  en4.advancedMenu.previousClick.getElement('i').removeClass('isDown');
                }
              }
            }
            
            if (!el.hasClass('level1parent')) {
              en4.advancedMenu.fireNavEvent(el, open);
              if (open && el.getElement('i')) {
                el.getElement('i').addClass('isDown');
              } else if (en4.advancedMenu.previousClick.getElement('i')) {
                en4.advancedMenu.previousClick.getElement('i').removeClass('isDown');
              }
              en4.advancedMenu.previousClick = open ? el : false;
              el.store('open', !open);
            } 
            if(el.hasClass('level1parent')){
              window.location = el.getElement('a').href;
            }
          });
        } else {
          list.onmouseover = function() {
            en4.advancedMenu.fireNavEvent(this, true);
          };
          list.onmouseout = function() {
            en4.advancedMenu.fireNavEvent(this, false);
          };
        }
        if(list.getElement("ul")){
          en4.advancedMenu.init(list.getElement("ul"), true);
        }
      }
    
    },
    ieFixZIndex : function(el, i, l) {
      if(el.tagName.toString().toLowerCase().indexOf("iframe") == -1){
        el.style.zIndex = l - i;
      } else {
        el.onmouseover = "null";
        el.onmouseout = "null";
      }
    },
    fireNavEvent : function(elm,ev) {
      if(ev){
        elm.addClass("over");
        if(elm.getElement("a")){
          elm.getElement("a").addClass("over");
        }
      
        if (elm.getChildren()[1] && !('ontouchstart' in window)) { 
          en4.advancedMenu.show(elm.getChildren()[1]);
          var temp_size = elm.getSize().x;
          if(temp_size < 200)
            temp_size = 200;
          if(elm.hasClass('standard_nav'))
            elm.getChildren()[1].style.width = temp_size+'px';
          if(elm.getChildren()[1].hasClass('sitemenu_standnav_submenu')){
            elm.getChildren()[1].style.marginLeft = elm.getParent().style.width;
          }
        }
      } else {
        elm.removeClass("over");
        if(elm.getElement("a")){
        elm.getElement("a").removeClass("over");
      }
      if (elm.getChildren()[1]) {
          en4.advancedMenu.hide(elm.getChildren()[1]);
        }
      }
    },
    show : function (sub_elm) {
      if (sub_elm.hide_time_id) {
        clearTimeout(sub_elm.hide_time_id);
      }
      sub_elm.show_time_id = setTimeout(function() {
        if (!sub_elm.hasClass("shown-sublist")) {
          sub_elm.addClass("shown-sublist");
        }
      }, en4.advancedMenu.settings.show_delay);
    },
    hide : function (sub_elm) {
      if (sub_elm.show_time_id) {
        clearTimeout(sub_elm.show_time_id);
      }
      sub_elm.hide_time_id = setTimeout(function(){
        if (sub_elm.hasClass("shown-sublist")) {
          sub_elm.removeClass("shown-sublist");
        }
      }, en4.advancedMenu.settings.hide_delay);
    }
  };

/**
 * @description dropdown Navigation
 * @param {String} id id of ul element with navigation lists
 * @param {Object} settings object with settings
 */

var NavigationSitemenu = function() {
  en4.advancedMenu.obj_nav = $(arguments[0]) ||$("nav");
  if (arguments[1]) {
    en4.advancedMenu.settings = Object.extend(en4.advancedMenu.settings, arguments[1]);
  }
  if (en4.advancedMenu.obj_nav) {
    en4.advancedMenu.init(en4.advancedMenu.obj_nav, false);
  }
};

function advancedMenuUserLoginOrSignUp(type, isLoginPage, isSignupPage)
{
  scrollPosition['top'] = window.getScrollTop();
  scrollPosition['left'] = window.getScrollLeft();
  
  if( !isLoginPage )
    isLoginPage = 0;
  if( !isSignupPage )
    isSignupPage = 0;
  
  if( type == 'login' && isLoginPage == 0 )
  {
    if( $("user_login_form_tab") ) {
      $("user_login_form_tab").style.display = 'block';
      if( !$("user_login_form_tab").hasClass("active") ) {
        $("user_login_form_tab").addClass("active");
      }
    }
    
    if( $("user_signup_form_tab") ) {
      $("user_signup_form_tab").style.display = 'none';
      if( $("user_signup_form_tab").hasClass("active") ) {
        $("user_signup_form_tab").removeClass("active");
      }
    }
    
    if($("user_signup_form")) $("user_signup_form").style.display = 'none';
    if($("user_login_form")) $("user_login_form").style.display = 'block';

    SmoothboxSEAO.open({element:$("user_form_default_sea_lightbox").setStyle('display','block')});
    SmoothboxSEAO.setHtmlScroll("hidden");
  }
  else if( type == 'signup' && isSignupPage == 0 )
  {
    if( $("user_signup_form_tab") ) {
      $("user_signup_form_tab").style.display = 'block';
      if( !$("user_signup_form_tab").hasClass("active") ) {
        $("user_signup_form_tab").addClass("active");
      }
    }
    
    if( $("user_login_form_tab") ) {
      $("user_login_form_tab").style.display = 'none';
      if( $("user_login_form_tab").hasClass("active") ) {
        $("user_login_form_tab").removeClass("active");
      }
    }
    
    if($("user_login_form")) $("user_login_form").style.display = 'none';
    if($("user_signup_form")) $("user_signup_form").style.display = 'block';

    SmoothboxSEAO.open({element:$("user_form_default_sea_lightbox").setStyle('display','block')});
    SmoothboxSEAO.setHtmlScroll("hidden");
  }

	if($("user_form_default_sea_lightbox") ){
		var smooothBoxWrapper = $("user_form_default_sea_lightbox").parentNode.parentNode;
		if( smooothBoxWrapper )
			smooothBoxWrapper.style.width = '520px';
	}
}

function showAdvancedMiniMenuIconContent(menuIconName, element, actionName, noOfUpdates, showSuggestion)
{ 
  if (element.hasClass('updates_pulldown') || tempFlag == 1) 
  {
    tempFlag = 0;
    if( !actionName )
      return;
    
    // IF NOT SAME MENU, THEN CLOSE PREVIOUS MENU
    if( menuName && menuIconName != menuName )
    {
      doAdvancedMiniMenuContentHide = true;
      toggleAdvancedMiniMenu(menuName);
    }
    
    menuName = menuIconName;
    urlActionName = actionName;
if(actionName == 'get-cart-products'){
  var actionUrl = en4.core.baseUrl + 'sitestoreproduct/product/' + actionName +'/isOtherModule/1';
}else{
  var actionUrl = en4.core.baseUrl + 'sitemenu/index/' + actionName;
}
    var newContentRequest = new Request.HTML({
      url : actionUrl,
      method : 'POST',
      onRequest: function(){
        element.removeClass('updates_pulldown');
        element.addClass('updates_pulldown_active');
      },
      data : {
        format : 'html',
        page : 1,
        noOfUpdates : noOfUpdates,
        isajax: 1,
        showSuggestion : showSuggestion 
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) 
      {
        document.getElementById(menuIconName+'_pulldown_contents').innerHTML = responseHTML;
        en4.core.runonce.trigger();

        if( menuIconName == 'sitemenu_mini_friend_request' && $("new_friend_request_count") ) {
          $("new_friend_request_count").style.display = 'none';
          if( $("new_friend_request_count_parent") )
            $("new_friend_request_count_parent").style.display = 'none';
        }
        else if( menuIconName == 'core_mini_messages' && $("new_message_count") ){
          $("new_message_count").style.display = 'none';
          if($("new_message_count_parent"))
            $("new_message_count_parent").style.display = 'none';
        }
        else if( menuIconName == 'sitemenu_mini_notification' && $("new_notification_count") ){
          $("new_notification_count").style.display = 'none';
          if($("new_notification_count_parent"))
            $("new_notification_count_parent").style.display = 'none';
        }
          
      }
    }
    );
    newContentRequest.send();
  }
}

function advancedMiniMenuContentHide()
{
  doAdvancedMiniMenuContentHide = false;
}

function toggleAdvancedMiniMenu(menuName) {
  if(doAdvancedMiniMenuContentHide)
  {
    $(menuName+'_updates_pulldown').removeClass('updates_pulldown_active');
    $(menuName+'_updates_pulldown').addClass('updates_pulldown');
    doAdvancedMiniMenuContentHide = false;
  }
  else if($(menuName+'_updates_pulldown').hasClass('updates_pulldown_active'))
    doAdvancedMiniMenuContentHide = true;
}

function advancedMenuUserLoginFormAction()
{
  // INJECT FORGOT PASSWORD LINK
  var wrapperDiv = document.createElement("div");
  wrapperDiv.id = "forgot_password";
  wrapperDiv.innerHTML = "<span class='fright'><a href='"+en4.core.baseUrl+"user/auth/forgot'>"+en4.core.language.translate('Forgot Password?')+"</a></span>";
  wrapperDiv.inject($('password-wrapper'), 'after');
  $("remember-wrapper").inject($("forgot_password"), 'before');
  
  // INJECT TWITTER AND FACEBOOK LINK
	if( $("twitter-wrapper") || $("facebook-wrapper") ) {
		var wrapperDiv = document.createElement("div");
		wrapperDiv.id = "sitemenu_loginform_sociallinks";
		wrapperDiv.inject($('user_form_login'), 'top');
    
    if( $("facebook-wrapper") ) {	
      $("facebook-element").title = en4.core.language.translate("Login with Facebook");
      $("facebook-wrapper").inject($("sitemenu_loginform_sociallinks"), 'top');
    }

    if( $("twitter-wrapper") ) {
      $("twitter-element").title = en4.core.language.translate("Login with Twitter");
      $("twitter-wrapper").inject($("sitemenu_loginform_sociallinks"), 'top');
    }
	}
}

function advancedMenuCloseUserLightBoxForm()
{
 
  document.getElementById('sitemenu_user_form_lightbox').style.display='none';
  SmoothboxSEAO.setHtmlScroll("auto");
  window.scroll(scrollPosition['left'],scrollPosition['top']); // horizontal and vertical scroll targets
 
}

function advancedMenuStopLightBoxClickEvent()
{
  hideUserFormLightBox = false;
}

/**
 * MARK ALL NOTIFICATIONS AS READ
 */
function advancedMenuMarkNotificationsAsRead()
{
  $("notifications_main").getElements('li').each(function(el){ 
    if( el.hasClass("notifications_unread") ) 
      el.removeClass("notifications_unread"); 
  });
  
  en4.core.request.send(new Request.JSON({
    url : en4.core.baseUrl + 'sitemenu/index/mark-notifications-as-read',
    method : 'POST',
    data : {
      format : 'json'
    },
    onSuccess : function(responseJSON) 
    {
    }
  })
  );
}

function advancedMenuNotificationClick(event)
{
  var current_link = event.target;
  var notification_li = $(current_link).getParent('li');

  var forward_link;
  if( current_link.get('href') ) {
    forward_link = current_link.get('href');
  } else{
    forward_link = $(current_link).getElements('a:last-child').get('href');
    if(forward_link=='' || $(current_link).get('tag')=='img'){
      var a_el=$(current_link).getParent('a');
      if(a_el)
        forward_link = $(current_link).getParent('a').get('href');
    }  
    if(forward_link=='' || $(current_link).get('tag')=='span'){
//      if($(notification_li) && $(notification_li).getElements('a:last-child') && $(notification_li).getElements('a:last-child')[0])
//        forward_link = $(notification_li).getElements('a:last-child')[0].get('href');
      forward_link = $(notification_li).getElements('a:last-child').get('href');
    }
  }
  if(forward_link){
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'activity/notifications/markread',
      data : {
        format     : 'json',
        'actionid' : notification_li.get('value')
      },
      onSuccess : window.location = forward_link
    }));
  }
}

function advancedMenuAddMessageIconHtml(message_id)
{
  if( $("message_conversation_"+message_id).hasClass("seocore_pulldown_item_list_new") )
    $("sitemenu_message_icon_"+message_id).setAttribute("title", en4.core.language.translate("Mark as Read"));
  else
    $("sitemenu_message_icon_"+message_id).setAttribute("title", en4.core.language.translate("Mark as Unread"));
}

function advancedMenuRemoveMessageIconHtml(message_id)
{
  if($("sitemenu_message_icon_"+message_id).hasClass("seocore_message_icon"))
    $("sitemenu_message_icon_"+message_id).innerHTML = "";
}

/**
 * Mark message as read or unread
 * @param {int} message_id
 */
function advancedMenuMarkMessageReadUnread(message_id)
{
  var is_message_read;
  // Mark Unread
  if( $("message_conversation_"+message_id).hasClass("seocore_pulldown_item_list_new") )
  {
    $("message_conversation_"+message_id).removeClass("seocore_pulldown_item_list_new");
    $("sitemenu_message_icon_"+message_id).setAttribute("title", en4.core.language.translate("Mark as Read"));
    is_message_read = 1;
  }
  else
  {
    $("message_conversation_"+message_id).addClass("seocore_pulldown_item_list_new");
    $("sitemenu_message_icon_"+message_id).setAttribute("title", en4.core.language.translate("Mark as Unread"));
    is_message_read = 0;
  }

  en4.core.request.send(new Request.JSON({
    url : en4.core.baseUrl + 'sitemenu/index/mark-message-read-unread',
    method : 'POST',
    data : {
      format : 'json',
      messgae_id : message_id,
      is_read : is_message_read
    },
    onSuccess : function(responseJSON) 
    {
    }
  }));
}
  
/**
 * Get Total no of products in cart
 */
function getCartItemCount()
{
  var request = new Request.JSON({
    url : en4.core.baseUrl + 'sitemenu/index/get-cart-item-count',
    method: 'GET',
    data : {
      format : 'json'
    },    
    onSuccess : function(responseJson) {
      if( $("main_menu_cart_item_count") )
      {
        if( responseJson.cartProductCounts )
        {
          $("main_menu_cart_item_count").style.display = 'block';
          $("main_menu_cart_item_count").innerHTML = responseJson.cartProductCounts;
          if( !$("main_menu_cart_item_count").hasClass('seaocore_pulldown_count') )
            $("main_menu_cart_item_count").addClass('seaocore_pulldown_count');
        }
        else
          $("main_menu_cart_item_count").style.display = 'none';
      }
      if( $("new_item_count") )
      {
        if( responseJson.cartProductCounts )
        {
          $("new_item_count").style.display = 'block';
          $("new_item_count").innerHTML = responseJson.cartProductCounts;
          if( !$("new_item_count").hasClass('seaocore_pulldown_count') )
            $("new_item_count").addClass('seaocore_pulldown_count');
        }
        else
          $("new_item_count").style.display = 'none';
      }
    }
  });
  request.send();
}

function getSettingUrlLink(settingsUrl) {
  window.location = settingsUrl;
}

function checkNewUpdates() {
  en4.core.request.send(new Request.JSON({
    url : en4.core.baseUrl + 'sitemenu/index/check-new-updates',
    method : 'POST',
    data : {
      format : 'json'
    },
    onSuccess : function(responseJSON) 
    {
      if( responseJSON.newFriendRequest && $("new_friend_request_count") )
      {
        $("new_friend_request_count").style.display = 'inline-block';
        if($("new_friend_request_count_parent")){
          $("new_friend_request_count_parent").style.display = 'inline-block';
        }
        $("new_friend_request_count").innerHTML = responseJSON.newFriendRequest;
        if( !$("new_friend_request_count_parent") && !$("new_friend_request_count").hasClass('seaocore_pulldown_count') )
          $("new_friend_request_count").addClass('seaocore_pulldown_count');
      }
      else if($("new_friend_request_count")){
        $("new_friend_request_count").style.display = 'none';
        if($("new_friend_request_count_parent")){
          $("new_friend_request_count_parent").style.display = 'none';
        }
      }  
      if( responseJSON.newMessage && $("new_message_count") )
      {
        $("new_message_count").style.display = 'inline-block';
        if($("new_message_count_parent")){
          $("new_message_count_parent").style.display = 'inline-block';
        }
        $("new_message_count").innerHTML = responseJSON.newMessage;
        if( !$("new_message_count_parent") && !$("new_message_count").hasClass('seaocore_pulldown_count') )
          $("new_message_count").addClass('seaocore_pulldown_count');
      }
      else if($("new_message_count")){
        $("new_message_count").style.display = 'none';
        if($("new_message_count_parent")){
          $("new_message_count_parent").style.display = 'none';
        }
      }

      if( responseJSON.newNotification && $("new_notification_count") )
      {
        $("new_notification_count").style.display = 'inline-block';
        if($("new_notification_count_parent")){
          $("new_notification_count_parent").style.display = 'inline-block';
        }
        $("new_notification_count").innerHTML = responseJSON.newNotification;
        if( !$("new_notification_count_parent") && !$("new_notification_count").hasClass('seaocore_pulldown_count') )
          $("new_notification_count").addClass('seaocore_pulldown_count');
      }
      else if($("new_notification_count")){
        $("new_notification_count").style.display = 'none';
        if($("new_notification_count_parent")){
          $("new_notification_count_parent").style.display = 'none';
        }
      }
    }
  }));
}

function mainMenuScrolling(height_dif){
  if( height_dif < window.getScrollTop() ){
    if( $("global_header") )
      if( !$("global_header").hasClass('fixed'))
        $("global_header").addClass('fixed');
  } else {
    if( $("global_header") )
      if( $("global_header").hasClass('fixed'))
        $("global_header").removeClass('fixed');
  }
}

function mainMenuDropdownContent() {
  if(!(typeof NavigationSitemenu == 'function')){
    new Asset.javascript( en4.core.staticBaseUrl+'application/modules/Sitemenu/externals/scripts/core.js',{
      onLoad :addDropdownMenu
    });
  } else {
    addDropdownMenu();
  }
}

function getTabContent(tab_id, module_id, viewby_field, content_limit, is_category, category_limit, truncation_limit_content, truncation_limit_category, category_id, content_height, is_title_inside, sub_menu_id)
  {
    if( sub_menu_id ) {
      if( $("sub_menu_"+containerElementId) && $("sub_menu_"+containerElementId).hasClass('over') ) {
        $("sub_menu_"+containerElementId).removeClass('over');
      }
      containerElementId = sub_menu_id;
    }
    else {
      if( $("sub_menu_"+containerElementId) && $("sub_menu_"+containerElementId).hasClass('over') ) {
        $("sub_menu_"+containerElementId).removeClass('over');
      }
      containerElementId = tab_id;
    }
    if(!document.getElementById('tab_content_' + tab_id).innerHTML){
      if(tempGetTabContent == 0){
//        tempGetTabContent = 1;

        var newContentRequest = new Request.HTML({
          url : en4.core.baseUrl + 'sitemenu/index/get-tab-content/',
          data : {
            format : 'html',
            moduleId : module_id,
            viewby_field : viewby_field,
            content_limit : content_limit,
            is_category : is_category,
            category_limit : category_limit,
            truncation_limit_content : truncation_limit_content,
            truncation_limit_category : truncation_limit_category,
            category_id : category_id,
            content_height : content_height,
            is_title_inside : is_title_inside
          },
          onRequest: function(){
            document.getElementById('tab_content_' + tab_id).innerHTML = "<div class='menu_loader'></div>";
            if( sub_menu_id )
              document.getElementById('tab_content_' + sub_menu_id).innerHTML = "<div class='menu_loader'></div>";
          },
          onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
//            tempGetTabContent = 0;
            document.getElementById('tab_content_' + tab_id).innerHTML = responseHTML;
//            if( !document.getElementById('tab_content_' + tab_id).hasClass('shown-sublist') )
//              document.getElementById('tab_content_' + tab_id).addClass('shown-sublist')
            if( sub_menu_id )
              document.getElementById('tab_content_' + sub_menu_id).innerHTML = responseHTML;
            if(en4.sitevideoview)
              en4.sitevideoview.attachClickEvent(Array('video_title','video_title'));
          if(en4.sitevideolightboxview)
              en4.sitevideolightboxview.attachClickEvent(Array('video_title','video_title'));
          }
        });
        newContentRequest.send();
        
      }
    }
  }
    
  function removeTabContent() {
    document.getElementById('detail').innerHTML = "";    
  }
  
function getStoreProductSuggest(advancedMenuContainerId, tempIndex, autoSuggestUrl){
  if( tempIndex && autoSuggestUrl )
    advancedMenuGetProductSearch(advancedMenuContainerId, tempIndex, autoSuggestUrl);

  $(advancedMenuContainerId).addEvent('keyup', function (e)  {  
    if (e.key == 'enter')
      $(advancedMenuContainerId+'Form').submit();
  });
}

function storeProductSelect(element, selected) {
  if(element.key == 'enter') {
    if(selected.retrieve('autocompleteChoice') != 'null' ) {
      var url = selected.retrieve('autocompleteChoice').sitestoreproduct_url;
      if (url == 'seeMoreLink') {
        advancedMenuSeemore();
      }
      else {
        window.location.href=url;
      }
    }
  }
}

function advancedMenuGetPageResults(url) {
    if(url != 'null' ) {
      if (url == 'seeMoreLink') {
        advancedMenuSeemore();
      }
      else {
        window.location.href=url;
      }
    }
  }
  
  function advancedMenuGetProductSearch(textContainerId, productInjectContainerNo, autoSuggestUrl)
  {
    advancedMenuContentAutocomplete = new Autocompleter.Request.JSON(textContainerId, autoSuggestUrl, {
      'postVar' : 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest seaocore-autosuggest',
      'customChoices' : true,
      'filterSubset' : true,
      'multiple' : false,
      'injectChoice': function(token) {
	      if(typeof token.label != 'undefined' ) {
          if (token.sitestoreproduct_url != 'seeMoreLink') {
            var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id':token.label+productInjectContainerNo, 'sitestoreproduct_url':token.sitestoreproduct_url, onclick:'javascript:advancedMenuGetPageResults("'+token.sitestoreproduct_url+'")'});
            new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
          if(token.sitestoreproduct_url == 'seeMoreLink') {
            var titleAjax = $(textContainerId).value;
            var choice = new Element('li', {'class': 'autocompleter-choices', 'html': '', 'id':'stopevent', 'sitestoreproduct_url':''});
            new Element('div', {'html': 'See More Results for '+titleAjax ,'class': 'autocompleter-choicess', onclick:'javascript:advancedMenuSeemore()'}).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
         }
       }
    });
  }
  
  var advancedMenuDoSearching =function(el){
    el.submit();
  };
  
  function manageMiniMenus(){
    if( $("sitemenu_user_form_lightbox") )
    $("sitemenu_user_form_lightbox").inject(document.body);
  
  $(document.body).addEvent('click', function(event){
    $$(".sitemenu_lightbox_content_wrapper").addEvent('click', function(event) {
      if($(event.target).hasClass('sitemenu_lightbox_content_wrapper')){
        event.stopPropagation();
        advancedMenuCloseUserLightBoxForm();
      }
    });
    
    if( menuName ){
      toggleAdvancedMiniMenu(menuName);
    }
  });
  }
  
  function messageConversation(messageUrl){
    if(messageUrl != 'null' ) {
        window.location.href=messageUrl;
    }
  }
  	
	/**
 * Responsive work for mobile
 * @param {string} container
 */
function sitemenuMobileMenuLink(container)
{
  $(container).toggle();
  $(container).inject($("sitemenu_mobile_menu_link"), 'after');
}

function sitemenuSearchToggle(type) {
	if( type == 1 )
		$("sitemenu_search_toggle_content").style.display = 'block';
  else
		$("sitemenu_search_toggle_content").style.display = 'none';
}

//function advancedMenuOnBodyClick()
//{
//  if($('more_link_li') && $('more_link_li').hasClass('over')){
//    $('more_link_li').removeClass('over');
//  }else if($('more_link_li') && !$('more_link_li').hasClass('over')){
//    $('more_link_li').addClass('over');
//  }
//  
//}

//FUNCTION FOR RESPONSIVE MENU IN MOBILE AND TABLETS
function advancedMenuMainClick(menu_tab)
{
  //APPLY CLASS OPEN ON MAIN MENU TABS AND REMOVE OPEN CLASS FROM MAIN MENU AND SUB MENUS IF DIFFERENT MAIN MENU IS TABBED
  if(menu_tab && (menu_tab.getParent().hasClass('level0parent') || menu_tab.getParent().hasClass('level0 standard_nav')) && previousParent != menu_tab.getParent()){
    if(previousParent){
      previousParent.removeClass('open');
      var temp_child_elements = previousParent.getElements('li');
      Array.each(temp_child_elements, function(value, key){
        temp_child_elements[key].removeClass('open');
      });
      previousParent.getChildren('open').removeClass('open');
    }
    previousParent = menu_tab.getParent();
  
    
    if(menu_tab && (menu_tab.getParent().hasClass('level0parent') || menu_tab.getParent().hasClass('level0 standard_nav')) && menu_tab.getParent().hasClass('over') && !menu_tab.getParent().hasClass('open')){
      menu_tab.getParent().addClass('open');
      return false;
    }

    if(menu_tab && menu_tab.getParent().hasClass('level0parent')&& menu_tab.getParent().hasClass('mixed_menu')&& !menu_tab.getParent().hasClass('open')){
      menu_tab.getParent().addClass('open');
      return false;
    }
  
  }
  
  //REMOVE SUB ELEMENTS OPEN CLASS IF CHILDREN OF SAME MENU TAB ARE CLICKED
  if(menu_tab && menu_tab.getParent().hasClass('level1parent') && previousParent == menu_tab.getParent().getParent().getParent()){
    var temp_child_elements = previousParent.getElements('li');
      Array.each(temp_child_elements, function(value, key){
        temp_child_elements[key].removeClass('open');
      });
      previousParent.getChildren('open').removeClass('open');
  }
  
  if(menu_tab && menu_tab.getParent().hasClass('level1parent')&& menu_tab.getParent().hasClass('over')&& !menu_tab.getParent().hasClass('open')){
    menu_tab.getParent().addClass('open');
    return false;
  }
  return true;
}


//function advancedMenuOnBodyClick()
//{
//  if($('more_link_li') && $('more_link_li').hasClass('over')){
//    $('more_link_li').addClass('open');
//  }
//}