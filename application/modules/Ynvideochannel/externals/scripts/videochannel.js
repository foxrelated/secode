function ynvideochannelAddNewPlaylist(ele, guid, url) {
    var nextEle = ele.getNext();
    if(nextEle.hasClass("ynvideochannel_active_add_playlist")) {
        //click to close
        nextEle.removeClass("ynvideochannel_active_add_playlist");
        nextEle.setStyle("display", "none");
    } else {
        //click to open
        nextEle.addClass("ynvideochannel_active_add_playlist");
        nextEle.setStyle("display", "block");
    }
    $$('.play_list_span').each(function(el){
        if(el === nextEle){
            //do not empty the current box
        } else {
            el.empty();
            el.setStyle("display", "none");
            el.removeClass("ynvideochannel_active_add_playlist");
        }
    });
    var data = guid;
    var request = new Request.HTML({
        url : url,
        data : {
            subject: data,
        },
        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            var spanEle = nextEle;
            spanEle.innerHTML = responseHTML;
            eval(responseJavaScript);

            var popup = spanEle.getParent('.ynvideochannel-action-pop-up');
            var layout_parent = popup.getParent('#global_content');
            var y_position = popup.getPosition(layout_parent).y;
            var p_height = layout_parent.getHeight();
            var c_height = popup.getHeight();
            if(p_height - y_position < (c_height + 21)) {
                layout_parent.addClass('popup-padding-bottom');
                var margin_bottom = parseInt(layout_parent.getStyle('padding-bottom').replace( /\D+/g, ''));
                layout_parent.setStyle('padding-bottom', (margin_bottom + c_height + 21 + y_position - p_height)+'px');
            }
        }
    });
    request.send();
}

function ynvideochannelAddToPlaylist(ele, playlistId, guild, url) {
    var checked = ele.get('checked');
    var data = guild;
    var request = new Request.JSON({
        url : url,
        data : {
            subject: data,
            playlist_id: playlistId,
            checked: checked,
        },
        onSuccess: function(responseJSON) {
            if (!responseJSON.status) {
                ele.set('checked', !checked);
            }
            var div = ele.getParent('.ynvideochannel-action-pop-up');
            var notices = div.getElement('.add-to-playlist-notices');
            var notice = new Element('div', {
                'class' : 'add-to-playlist-notice',
                text : responseJSON.message
            });
            notices.adopt(notice);
            notice.fade('in');
            (function() {
                notice.fade('out').get('tween').chain(function() {
                    notice.destroy();
                });
            }).delay(2000, notice);
        }
    });
    request.send();
}

function ynvideochannelAddToFavorite(ele, video_id, url, favtext, unfavtext) {
    var request = new Request.JSON({
        url : url,
        data : {
            id: video_id
        },
        onSuccess: function(responseJSON) {
            if (responseJSON.result) {
                if (responseJSON.added == 1) {
                    var html = '<i class="fa fa-star"></i>' + ' ' + unfavtext;
                    ele.innerHTML = html;
                } else {
                    var html = '<i class="fa fa-star-o"></i>' + ' ' + favtext;
                    ele.innerHTML = html;
                }
            }

            var div = ele.getParent('.ynvideochannel-action-pop-up');
            var notices = div.getElement('.add-to-playlist-notices');
            var notice = new Element('div', {
                'class' : 'add-to-playlist-notice',
                text : responseJSON.message
            });
            notices.adopt(notice);
            notice.fade('in');
            (function() {
                notice.fade('out').get('tween').chain(function() {
                    notice.destroy();
                });
            }).delay(2000, notice);
        }
    });
    request.send();
}

window.addEvent('domready', function(){
    $$('a.ynvideochannel-action-link.show-hide-btn').removeEvents('click').addEvent('click', function() {
        var parent = this.getParent('.action-container');
        var popup = parent.getElement('.ynvideochannel-action-pop-up');

        $$('.action-container').each(function(el) {
            el.removeClass("ynvideochannel-action-shown");
        });

        var pageParent = popup.getParent('#global_content');
        var otherPopup = pageParent.getElement('.ynvideochannel_button_more_explain');
        if (otherPopup != null) {
            otherPopup.hide();
        }

        $$('.ynvideochannel-action-pop-up').each(function(el) {
            if (el != popup) el.hide();
        });

        if (!popup.isDisplayed()) {
            parent.addClass("ynvideochannel-action-shown");
            var loading = popup.getElement('.add-to-playlist-loading');
            if (loading) {
                var url = loading.get('rel');
                loading.show();
                var checkbox = popup.getElement('.box-checkbox');
                checkbox.hide();
                var request = new Request.HTML({
                    url : url,
                    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                        elements = Elements.from(responseHTML);
                        if (elements.length > 0) {
                            checkbox.empty();
                            checkbox.adopt(elements);
                            eval(responseJavaScript);
                            loading.hide();
                            checkbox.show();
                            var layout_parent = popup.getParent('#global_content');
                            var y_position = popup.getPosition(layout_parent).y;
                            var p_height = layout_parent.getHeight();
                            var c_height = popup.getHeight();
                            if(p_height - y_position < (c_height + 1)) {
                                layout_parent.addClass('popup-padding-bottom');
                                var margin_bottom = parseInt(layout_parent.getStyle('padding-bottom').replace( /\D+/g, ''));
                                layout_parent.setStyle('padding-bottom', (margin_bottom + c_height + 1 + y_position - p_height)+'px');
                            }
                        }
                    }
                });
                request.send();
            }
        } else {
            parent.removeClass("ynvideochannel-action-shown");
        }
        popup.toggle();
        var layout_parent = popup.getParent('#global_content');
        if (layout_parent.hasClass('popup-padding-bottom')) {
            layout_parent.setStyle('padding-bottom', '0');
        }
        var y_position = popup.getPosition(layout_parent).y;
        var p_height = layout_parent.getHeight();
        var c_height = popup.getHeight();
        if (popup.isDisplayed()) {
            if(p_height - y_position < (c_height + 1)) {
                layout_parent.addClass('popup-padding-bottom');
                layout_parent.setStyle('padding-bottom', (c_height + 1 + y_position - p_height)+'px');
            }
            else if (layout_parent.hasClass('popup-padding-bottom')) {
                layout_parent.setStyle('padding-bottom', '0');
            }
        }
        else {
            if (layout_parent.hasClass('popup-padding-bottom')) {
                layout_parent.setStyle('padding-bottom', '0');
            }
        }
    });

    $$('a.ynvideochannel-action-link.cancel').removeEvents('click').addEvent('click', function() {
        var parent = this.getParent('.ynvideochannel-action-pop-up');
        if (parent) {
            parent.hide();
            var layout_parent = parent.getParent('#global_content');
            if (layout_parent.hasClass('popup-padding-bottom')) {
                layout_parent.setStyle('padding-bottom', '0');
            }
        }
    });
});


//Function outerclick
    (function($,$$){
        var events;
        var check = function(e){
        var target = $(e.target);
        var parents = target.getParents();
        events.each(function(item){
            var element = item.element;
            if (element != target && !parents.contains(element))
                item.fn.call(element, e);
            });
        };

        Element.Events.outerClick = {
            onAdd: function(fn){
              if(!events) {
                document.addEvent('click', check);
                events = [];
              }
              events.push({element: this, fn: fn});
            },
            onRemove: function(fn){
              events = events.filter(function(item){
                return item.element != this || item.fn != fn;
              }, this);
              if (!events.length) {
                document.removeEvent('click', check);
                events = null;
              }
            }
        };
    })(document.id,$$);