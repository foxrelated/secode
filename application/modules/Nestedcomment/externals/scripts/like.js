/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: like.js 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

////// FUNCTION FOR CREATING A LIKE OR DISLIKE
function nestedcomment_content_type_likes(resource_id, resource_type) {

    content_type_undefined = 0;
    var content_type = nestedcomment_content_type;
    if (nestedcomment_content_type === '') {
        content_type_undefined = 1;
        var content_type = resource_type;
    }

    // SENDING REQUEST TO AJAX
    var request = nestedcomment_content_create_like(resource_id, resource_type, content_type);

    // RESPONCE FROM AJAX
    request.addEvent('complete', function(responseJSON) {
        if (content_type_undefined === 0) {
            if (responseJSON.like_id) {
                if ($(content_type + '_like_' + resource_id))
                    $(content_type + '_like_' + resource_id).value = responseJSON.like_id;
                if ($(content_type + '_most_likes_' + resource_id))
                    $(content_type + '_most_likes_' + resource_id).style.display = 'none';
                if ($(content_type + '_unlikes_' + resource_id))
                    $(content_type + '_unlikes_' + resource_id).style.display = 'inline-block';
                if ($(content_type + '_num_of_like_' + resource_id)) {
                    $(content_type + '_num_of_like_' + resource_id).innerHTML = responseJSON.num_of_like;
                }
            } else {
                if ($(content_type + '_like_' + resource_id))
                    $(content_type + '_like_' + resource_id).value = 0;
                if ($(content_type + '_most_likes_' + resource_id))
                    $(content_type + '_most_likes_' + resource_id).style.display = 'inline-block';
                if ($(content_type + '_unlikes_' + resource_id))
                    $(content_type + '_unlikes_' + resource_id).style.display = 'none';
                if ($(content_type + '_num_of_like_' + resource_id)) {
                    $(content_type + '_num_of_like_' + resource_id).innerHTML = responseJSON.num_of_like;
                }
            }
        }
    });
}

function nestedcomment_content_create_like(resource_id, resource_type, content_type) {
    if ($(content_type + '_like_' + resource_id)) {
        var like_id = $(content_type + '_like_' + resource_id).value;
    }
    var request = new Request.HTML({
        url: en4.core.baseUrl + 'nestedcomment/like/like',
        data: {
            format: 'html',
            'resource_id': resource_id,
            'resource_type': resource_type,
            'like_id': like_id
        },
        onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        }
    });
    request.send();
    return request;
}
//FUNCTION FOR LIKE OR UNLIKE.