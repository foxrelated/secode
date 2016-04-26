/* $Id: core.js 2010-11-04 9:40:21Z SocialEngineAddOns Copyright 2009-2010 BigStep Technologies Pvt. Ltd. $ */

sm4.sitelike = {
};

sm4.sitelike.do_like = {

  // FUNCTION FOR CREATING A FEEDBACK 
  createLike: function(resource_id, resource_type, content_type)
  {
    if (content_type == 'browsemixinfo') {
      var like_id = $('#' + resource_type + '_like_' + resource_id).val();
      content_type = resource_type
    }
    else if (content_type == 'mixinfo') {
      var like_id = $('#' + resource_type + '_mixinfolike_' + resource_id).val();
      content_type = resource_type
    }
    else if (content_type == 'welcomemixinfo') {
      var like_id = $('#' + resource_type + '_welcomemixinfolike_' + resource_id).val();
      content_type = resource_type
    }
    else {
      if ($('#' + content_type + '_like_' + resource_id))
        var like_id = $('#' + content_type + '_like_' + resource_id).val();
    }

    if (like_id == 0) {
      $('#li_' + content_type + '_like_' + resource_id).find('.ui-icon-thumbs-up').removeClass('ui-icon-thumbs-up').addClass('ui-icon-spinner');
    } else {
      $('#li_' + content_type + '_like_' + resource_id).find('.ui-icon-thumbs-down').removeClass('ui-icon-thumbs-down').addClass('ui-icon-spinner');
    }
    $.ajax({
      url: sm4.core.baseUrl + 'sitelike/index/globallikes',
      type: "GET",
      dataType: "json",
      data: {
        format: 'json',
        'resource_id': resource_id,
        'resource_type': resource_type,
        'like_id': like_id
      },
      success: function(responseJSON) {
        if (responseJSON.like_id)
        {
          $('#' + content_type + '_like_' + resource_id).val(responseJSON.like_id);
          $('#' + content_type + '_most_likes_' + resource_id).css('display', 'none');
          $('#' + content_type + '_unlikes_' + resource_id).css('display', 'block');
          $('#li_' + content_type + '_like_' + resource_id).find('.ui-icon-spinner').removeClass('ui-icon-spinner').addClass('ui-icon-thumbs-down');
          if (content_type == 'my-friend_' + resource_type) {
            $('#' + content_type + '_num_of_like_' + resource_id).html('<a id="likes_viewall_link_' + resource_id + '"  class="smoothbox likes_viewall_link" href="' + $('#likes_viewall_link_' + resource_id).href + '">' + responseJSON.num_of_like + '</a>');
          }
          else
          {
            $('#' + content_type + '_num_of_like_' + resource_id).html(responseJSON.num_of_like);
          }
        }
        else
        {
          $('#' + content_type + '_like_' + resource_id).val(0);
          $('#' + content_type + '_most_likes_' + resource_id).css('display', 'block');
          $('#' + content_type + '_unlikes_' + resource_id).css('display', 'none');
          $('#li_' + content_type + '_like_' + resource_id).find('.ui-icon-spinner').removeClass('ui-icon-spinner').addClass('ui-icon-thumbs-up');
          if (content_type == 'my-friend_' + resource_type) {
            $('#' + content_type + '_num_of_like_' + resource_id).html('<a id="likes_viewall_link_' + resource_id + '"  class="smoothbox likes_viewall_link" href="' + $('#likes_viewall_link_' + resource_id).href + '">' + responseJSON.num_of_like + '</a>');
          }
          else
          {
            $('#' + content_type + '_num_of_like_' + resource_id).html(responseJSON.num_of_like);
          }
        }

        sm4.core.runonce.trigger();
        sm4.core.dloader.refreshPage();
      }
    });
  }
}

function show_app_likes(app_name, thisobj, url, dynamicId) {
  if ($.mobile.showPageLoadingMsg) {
    $.mobile.showPageLoadingMsg();
  } else {
    $.mobile.loading().loader("show");
  }
  $.ajax({
    'url': url,
    type: "GET",
    dataType: "html",
    'data': {
      'format': 'html',
      'resource_type': thisobj.value,
      'isajax': 1
    },
    success: function(responseHTML) {
      if ($.mobile.hidePageLoadingMsg) {
        $.mobile.hidePageLoadingMsg();
      } else {
        $.mobile.loading().loader("hide");
      }
      $.mobile.activePage.find('#dynamic_app_info_' + dynamicId).html(responseHTML);
      sm4.core.runonce.trigger();
      sm4.core.refreshPage();
    }
  });
}