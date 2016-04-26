$(document).on('pagebeforeshow', function() {
    //for pymk widget
    if ($.mobile.activePage.find('#activity-feed-sitefeed').length > 0 && $.mobile.activePage.find('.layout_suggestion_suggestion_friend').length > 0) {
        if ($.mobile.activePage.find('.suggestion_suggestion_friend_li').length == 0) {
            $($.mobile.activePage.find('#activity-feed-sitefeed').find('.activty_ul_li')[0])
                    .after($('<li class="suggestion_suggestion_friend_li"/>')
                    .append($.mobile.activePage.find('.layout_suggestion_suggestion_friend')));
        }
    } else if ($.mobile.activePage.find('.add_other_content_feed') && $.mobile.activePage.find('.layout_suggestion_suggestion_friend')) {
        $.mobile.activePage.find('.add_other_content_feed')
                .after($.mobile.activePage.find('.layout_suggestion_suggestion_friend'));
    }//for pymk widget

    //for recommendation widget
    if ($.mobile.activePage.find('#activity-feed-sitefeed').length > 0 && $.mobile.activePage.find('.layout_suggestion_suggestion_mix').length > 0) {
        if ($.mobile.activePage.find('.suggestion_suggestion_mix_li').length == 0) {
            var content = $.mobile.activePage.find('#activity-feed-sitefeed').find('.activty_ul_li')
                    , i = (content.length > 10) ? 10 : content.length;
            $(content[i]).after($('<li class="suggestion_suggestion_mix_li"/>')
                    .append($.mobile.activePage.find('.layout_suggestion_suggestion_mix')));
        }
    } else if ($.mobile.activePage.find('.add_other_content_feed') && $.mobile.activePage.find('.layout_suggestion_suggestion_mix')) {
        $.mobile.activePage.find('.add_other_content_feed')
                .after($.mobile.activePage.find('.layout_suggestion_suggestion_mix'));
    }//for recommendation widget

});

var removeSuggNotification = function(entity, entity_id, notificationType, div_id, responseWithTip, getDisplayContent)
{
    sm4.core.request.send({
        type: "POST",
        dataType: "html",
        url: sm4.core.baseUrl + 'suggestion/main/remove-notification',
        data: {
            format: 'html',
            entity: entity,
            entity_id: entity_id,
            notificationType: notificationType,
            responseWithTip: responseWithTip
        },
        success: function(responseHTML)
        { 
            if (getDisplayContent != 0) {
                setCountingLimit(entity, 0, getDisplayContent);
            }
            if (div_id != '') {
                $.mobile.activePage.find('#'+div_id).html(responseHTML);
            }
            $.mobile.activePage.find('#' + div_id).removeClass('ui-li-has-thumb');
            $.mobile.activePage.find('#' + div_id).closest('ul').listview().listview('refresh');
            $.mobile.activePage.find('#' + div_id).find('a').addClass('ui-link');
            

        }
    });
};