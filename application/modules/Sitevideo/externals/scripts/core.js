/* $Id: core.js 2011-08-26 9:40:21Z SocialEngineAddOns Copyright 2010-2011 BigStep Technologies Pvt. Ltd. $ */
en4.sitevideo = {
};
/*
 * 
 * Watch later
 */

en4.sitevideo.watchlaters = {
    add: function (video_id)
    {
        (new Request.JSON({
            'format': 'json',
            'url': en4.core.baseUrl + 'sitevideo/watchlater/add-to-watchlater',
            'data': {
                'format': 'json',
                'video_id': video_id,
            },
            'onSuccess': function (responseJSON, responseText)
            {
                $$('a.removewatchlater_' + video_id).each(function (el) {
                    el.style.display = 'inline-block';
                });
                $$('a.addwatchlater_' + video_id).each(function (el) {
                    el.style.display = 'none';
                });
            }
        })).send();
    },
    remove: function (video_id)
    {
        (new Request.JSON({
            'format': 'json',
            'url': en4.core.baseUrl + 'sitevideo/watchlater/remove-from-watchlater-json',
            'data': {
                'format': 'json',
                'video_id': video_id,
            },
            'onSuccess': function (responseJSON, responseText)
            {
                $$('a.removewatchlater_' + video_id).each(function (el) {
                    el.style.display = 'none';
                });
                $$('a.addwatchlater_' + video_id).each(function (el) {
                    el.style.display = 'inline-block';
                });
            }
        })).send();
    }
}
/*
 * Subscribe channels
 */
en4.sitevideo.subscriptions = {
    subscribe: function (channel_id)
    {
        (new Request.JSON({
            'format': 'json',
            'url': en4.core.baseUrl + 'sitevideo/subscription/subscribe-channel',
            'data': {
                'format': 'json',
                'channel_id': channel_id,
            },
            'onSuccess': function (responseJSON, responseText)
            {
                $$('a.unsubscription_' + channel_id).each(function (el) {
                    el.style.display = 'inline-block';
                });
                $$('a.subscription_' + channel_id).each(function (el) {
                    el.style.display = 'none';
                });
            }
        })).send();
    },
    unsubscribe: function (channel_id)
    {
        (new Request.JSON({
            'format': 'json',
            'url': en4.core.baseUrl + 'sitevideo/subscription/unsubscribe-channel',
            'data': {
                'format': 'json',
                'channel_id': channel_id,
            },
            'onSuccess': function (responseJSON, responseText)
            {
                $$('a.unsubscription_' + channel_id).each(function (el) {
                    el.style.display = 'none';
                });
                $$('a.subscription_' + channel_id).each(function (el) {
                    el.style.display = 'inline-block';
                });
            }
        })).send();
    }

}
/*
 * 
 * ratings
 */
en4.sitevideo.ratings = {
    setRating: function (subject_pre_rate, resource_id)
    {
        var subject_rating = subject_pre_rate;
        for (var x = 1; x <= parseInt(subject_rating); x++) {
            $('rate_' + resource_id + '_' + x).set('class', 'seao_rating_star_generic rating_star_y');
        }

        for (var x = parseInt(subject_rating) + 1; x <= 5; x++) {
            $('rate_' + resource_id + '_' + x).set('class', 'seao_rating_star_generic seao_rating_star_disabled');
        }

        var remainder = Math.round(subject_rating) - subject_rating;
        if (remainder <= 0.5 && remainder != 0) {
            var last = parseInt(subject_rating) + 1;
            $('rate_' + resource_id + '_' + last).set('class', 'seao_rating_star_generic rating_star_half_y');
        }
    }
}

/**
 * likes
 */
en4.sitevideo.likes = {
    like: function (type, id, comment_id) {
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'core/comment/like',
            data: {
                format: 'json',
                type: type,
                id: id,
                comment_id: 0
            },
            onSuccess: function (responseJSON) {
                if ($type(responseJSON) == 'object' && $type(responseJSON.status)) {
                    if ($(type + 'like_link'))
                        $(type + 'like_link').style.display = "none";
                    if ($(type + 'unlike_link'))
                        $(type + 'unlike_link').style.display = "inline-block";
                }
            }
        }), {
            'element': $('comments')
        }, true);
    },
    unlike: function (type, id, comment_id) {
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'core/comment/unlike',
            data: {
                format: 'json',
                type: type,
                id: id,
                comment_id: comment_id
            },
            onSuccess: function (responseJSON) {
                if ($type(responseJSON) == 'object' && $type(responseJSON.status)) {
                    if ($(type + 'unlike_link'))
                        $(type + 'unlike_link').style.display = "none";
                    if ($(type + 'like_link'))
                        $(type + 'like_link').style.display = "inline-block";
                }
            }
        }), {
            'element': $('comments')
        }, true);
    }


};

//var getSitevideoVideo= function(url,isajax){
//  var videoUrl= url.replace("/sitevideos/", "/channels/");
//  videoUrl= videoUrl.replace("/light-box-view/channel_id/", "/view/channel_id/");
//  if (history.replaceState) {
//    history.replaceState( {}, document.title, videoUrl );
//  } else {
//    window.location.hash = videoUrl;
//  }
//  $$(".lightbox_btm_bl").each(function(el){ 
//    if(isajax)
//      el.innerHTML="<center><img src='"+en4.core.staticBaseUrl+"application/modules/Seaocore/externals/images/icons/loader-large.gif' style='height:30px;' /> </center>";
//  });
//    
//  var remove_extra = 0;
//  contentVideoSize['height'] = $("video_lightbox_left").getCoordinates().height - remove_extra;
//  if(isajax == 0 )
//    remove_extra = remove_extra + 289;
//  contentVideoSize['width'] = $("video_lightbox_left").getCoordinates().width - remove_extra;
//
//  addAgainscrollFalg = true;
//  en4.core.request.send(new Request.HTML({
//    url : url,
//    data : {
//      format : 'html',
//      isajax : isajax
//    },
//    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) { 
//      if($('white_content_default_channel')){
//        $('white_content_default_channel').innerHTML = responseHTML;        
//        switchFullModeVideo(fullmode_video); 
//      }      
//    }
//  }), true);
//}

/**
 * @description dropdown Navigation
 * @param {String} id id of ul element with navigation lists
 * @param {Object} settings object with settings
 */


var NavigationSitevideo = function () {
    var main = {
        obj_nav: $(arguments[0]) || $("nav"),
        settings: {
            show_delay: 0,
            hide_delay: 0,
            _ie6: /MSIE 6.+Win/.test(navigator.userAgent),
            _ie7: /MSIE 7.+Win/.test(navigator.userAgent)
        },
        init: function (obj, level) {
            obj.lists = obj.getChildren();
            obj.lists.each(function (el, ind) {
                main.handlNavElement(el);
                if ((main.settings._ie6 || main.settings._ie7) && level) {
                    main.ieFixZIndex(el, ind, obj.lists.size());
                }
            });
            if (main.settings._ie6 && !level) {
                document.execCommand("BackgroundImageCache", false, true);
            }
        },
        handlNavElement: function (list) {
            if (list !== undefined) {
                list.onmouseover = function () {
                    main.fireNavEvent(this, true);
                };
                list.onmouseout = function () {
                    main.fireNavEvent(this, false);
                };
                if (list.getElement("ul")) {
                    main.init(list.getElement("ul"), true);
                }
            }
        },
        ieFixZIndex: function (el, i, l) {
            if (el.tagName.toString().toLowerCase().indexOf("iframe") == -1) {
                el.style.zIndex = l - i;
            } else {
                el.onmouseover = "null";
                el.onmouseout = "null";
            }
        },
        fireNavEvent: function (elm, ev) {

            if (ev) {
                elm.addClass("over");
                elm.getElement("a").addClass("over");
                if (elm.getChildren()[1]) {
                    main.show(elm.getChildren()[1]);
                }
            } else {
                elm.removeClass("over");
                elm.getElement("a").removeClass("over");
                if (elm.getChildren()[1]) {
                    main.hide(elm.getChildren()[1]);
                }
            }
        },
        show: function (sub_elm) {
            if (sub_elm.hide_time_id) {
                clearTimeout(sub_elm.hide_time_id);
            }
            sub_elm.show_time_id = setTimeout(function () {
                if (!sub_elm.hasClass("shown-sublist")) {
                    sub_elm.addClass("shown-sublist");
                }
            }, main.settings.show_delay);
        },
        hide: function (sub_elm) {
            if (sub_elm.show_time_id) {
                clearTimeout(sub_elm.show_time_id);
            }
            sub_elm.hide_time_id = setTimeout(function () {
                if (sub_elm.hasClass("shown-sublist")) {
                    sub_elm.removeClass("shown-sublist");
                }
            }, main.settings.hide_delay);
        }
    };
    if (arguments[1]) {
        main.settings = Object.extend(main.settings, arguments[1]);
    }
    if (main.obj_nav) {
        main.init(main.obj_nav, false);
    }
};

var tab_content_id_sitevideo = 0;
en4.sitevideo.ajaxTab = {
    click_elment_id: '',
    attachEvent: function (widget_id, params) {
        params.requestParams.content_id = widget_id;
        var element;

        $$('.tab_' + widget_id).each(function (el) {
            if (el.get('tag') == 'li') {
                element = el;
                return;
            }
        });
        var onloadAdd = true;
        if (element) {
            if (element.retrieve('addClickEvent', false))
                return;
            element.addEvent('click', function () {
                if (en4.sitevideo.ajaxTab.click_elment_id == widget_id)
                    return;
                en4.sitevideo.ajaxTab.click_elment_id = widget_id;
                en4.sitevideo.ajaxTab.sendReq(params);
            });
            element.store('addClickEvent', true);
            var attachOnLoadEvent = false;
            if (tab_content_id_sitevideo == widget_id) {
                attachOnLoadEvent = true;
            } else {
                $$('.tabs_parent').each(function (element) {
                    var addActiveTab = true;
                    element.getElements('ul > li').each(function (el) {
                        if (el.hasClass('active')) {
                            addActiveTab = false;
                            return;
                        }
                    });
                    element.getElementById('main_tabs').getElements('li:first-child').each(function (el) {
                        if (el.getParent('div') && el.getParent('div').hasClass('tab_pulldown_contents'))
                            return;
                        el.get('class').split(' ').each(function (className) {
                            className = className.trim();
                            if (className.match(/^tab_[0-9]+$/) && className == "tab_" + widget_id) {
                                attachOnLoadEvent = true;
                                if (addActiveTab || tab_content_id_sitevideo == widget_id) {
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

        en4.core.runonce.add(function () {
            if (onloadAdd)
                params.requestParams.onloadAdd = true;
            en4.sitevideo.ajaxTab.click_elment_id = widget_id;
            en4.sitevideo.ajaxTab.sendReq(params);
        });


    },
    sendReq: function (params) {
        params.responseContainer.each(function (element) {
            if ((typeof params.loading) == 'undefined' || params.loading == true) {
                element.empty();
                new Element('div', {
                    'class': 'sitevideo_profile_loading_image'
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
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                params.responseContainer.each(function (container) {
                    container.empty();
                    Elements.from(responseHTML).inject(container);
                    en4.core.runonce.trigger();
                    en4.sitevideolightboxview.attachClickEvent(Array('sitevideo_thumb_viewer'));
                    Smoothbox.bind(container);
                    if (params.requestParams.hasOwnProperty('justifiedViewId') && params.requestParams.showPhotosInJustifiedView == 1) {
                        showJustifiedView
                            (
                                    params.requestParams.justifiedViewId,
                                    params.requestParams.rowHeight,
                                    params.requestParams.maxRowHeight,
                                    params.requestParams.margin,
                                    params.requestParams.lastRow
                            );
                    }
                });

            }
        });
        request.send();
    }
};

function showJustifiedView(id, rowHeight, maxRowHeight, margin, lastRow)
{
    if ('undefined' != typeof window.jQuery) {
        var justifiedObj = jQuery("#" + id);
        if (justifiedObj.length > 0)
            justifiedObj.justifiedGallery(
                    {
                        rowHeight: rowHeight,
                        maxRowHeight: maxRowHeight,
                        margins: margin,
                        lastRow: lastRow
                    }
            ).on('jg.complete', function (e) {
                //Write complete trigger
            });
    }
}