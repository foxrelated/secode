/* $Id: core.js 2011-01-06 9:40:21Z SocialEngineAddOns Copyright 2010-2011 BigStep Technologies Pvt. Ltd. $ */

var enable_likemodule, enable_likeintsetting, enable_fboldversion, fbappid, local_language, fbcommentboxhtml, enable_fbcommentbox, curr_fbscrapeUrl, subject_guid, SeaoCommentbox_obj, call_advfbjs;
var FB_Comment_Setting = 2;
var defalutCommentClass = '.comments_options';

function runFacebookSdk() {
    window.fbAsyncInit = function () {
        FB.JSON.stringify = function (value) {
            return JSON.encode(value);
        };
        FB.init({
            appId: fbappid,
            status: true, // check login status
            cookie: true, // enable cookies to allow the server to access the session
            xfbml: true // parse XFBML

        });

        setFBContent();
        call_userfeed = 1;
    };
    (function () {
        var catarea = $('global_footer');
        if (catarea == null) {
            catarea = $('global_content');
        }
        if (catarea != null && (typeof $('fb-root') == 'undefined' || $('fb-root') == null)) {
            var newdiv = document.createElement('div');
            newdiv.id = 'fb-root';
            newdiv.inject(catarea, 'after');
            var e = document.createElement('script');
            e.async = true;
            e.src = document.location.protocol + '//connect.facebook.net/' + local_language + '/all.js';
            document.getElementById('fb-root').appendChild(e);
        }
    }());
}


window.addEvent('load', function () {
    var call_userfeed = 0;
    if (typeof call_advfbjs != 'undefined' && call_advfbjs == 1) {

        if (typeof FB == 'undefined') {

            runFacebookSdk();
        }
        else {
            setFBContent();
            call_userfeed = 1;

        }
    }
});

function  getFbCommentCount(fbcomment_url) {

    //SENDING AJAX REQUEST TO FIND WETHERE USER HAS ALREADY LIKED THE VIWING CONTENT OR NOT.

    url = en4.core.baseUrl + 'facebookse/index/getfbcommentcount';
    var request = new Request.HTML({
        'url': url,
        'method': 'get',
        'data': {
            'format': 'html',
            'curr_url': fbcomment_url,
            'is_ajax': '1'
        },
        onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {

            //if (responseHTML) { 
            $('fb_totalcomments').innerHTML = responseHTML + ' comments';

            //}
        }
    });
    request.send();
}

function FB_like(type, id) {
    if ($('comment-info'))
        var comment_info = $('comment-info').innerHTML;

    en4.core.request.send(new Request.HTML({
        url: en4.core.baseUrl + 'widget/index/mod/facebookse/name/facebookse-comments',
        data: {
            format: 'html',
            subject: subject_guid,
            task: '2',
            type: type,
            id: id,
            curr_url: curr_fbscrapeUrl,
            like_unlike: 'like'

        },
        onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
            $('temp_postcontent').innerHTML = responseHTML;
            var Comments_Likes = $('temp_postcontent').getElementById('FB_comments_options').innerHTML;
            $('temp_postcontent').innerHTML = '';
            $('FB_comments_options').innerHTML = Comments_Likes;
            $('comment-info').innerHTML = comment_info;
        }
    }));
}

function FB_unlike(type, id) {
    if ($('comment-info'))
        var comment_info = $('comment-info').innerHTML;
    en4.core.request.send(new Request.HTML({
        url: en4.core.baseUrl + 'widget/index/mod/facebookse/name/facebookse-comments',
        data: {
            format: 'html',
            task: '2',
            subject: subject_guid,
            type: type,
            id: id,
            curr_url: curr_fbscrapeUrl,
            like_unlike: 'unlike'
        },
        onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
            $('temp_postcontent').innerHTML = responseHTML;
            var Comments_Likes = $('temp_postcontent').getElementById('FB_comments_options').innerHTML;
            $('temp_postcontent').innerHTML = '';
            $('FB_comments_options').innerHTML = Comments_Likes;
            $('comment-info').innerHTML = comment_info;
            // setTimeout('callFBParse();', 1000);
        }
    }));
}

//SHOWING ALL THE MEMBERS WHO HAS LIKED THE PARTICULAR POST.
function FB_showLikes(type, id) {
    en4.core.request.send(new Request.HTML({
        url: en4.core.baseUrl + 'widget/index/mod/facebookse/name/facebookse-comments',
        data: {
            format: 'html',
            task: '2',
            subject: en4.core.subject.guid,
            type: type,
            id: id,
            viewAllLikes: true,
            like_unlike: 'like_unlike'
        },
        onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
            if ($('temp_postcontent')) {
                $('temp_postcontent').innerHTML = responseHTML;
                var Comments_Likes = $('temp_postcontent').getElementById('FB_comments_options').innerHTML;
                $('temp_postcontent').innerHTML = '';
                $('FB_comments_options').innerHTML = Comments_Likes;
            }
        }
    }));
}

function setFBContent() {

    if (typeof FB == 'undefined') {
        setTimeout('setFBContent();', 50);
    }
    else {
        FB.Event.subscribe('edge.create', function (response) {
            Like_UnlikeResponse('like')
        });

        FB.Event.subscribe('edge.remove',
                function (response) {
                    Like_UnlikeResponse('unlike');
                }
        );
        FB.Event.subscribe('comment.create', function (response) {

            var commentQuery = FB.Data.query("SELECT text, fromid FROM comment WHERE post_fbid='" + response.commentID +
                    "' AND object_id IN (SELECT comments_fbid FROM link_stat WHERE url='" + response.href + "')");
            var userQuery = FB.Data.query("SELECT name FROM user WHERE uid in (select fromid from {0})", commentQuery);
            FB.Data.waitOn([commentQuery, userQuery], function () {
                var commentRow = commentQuery.value[0];
                var userRow = userQuery.value[0];
                /* console.log(userRow.name + " (id: " + commentRow.fromid + ") posted the comment: " + commentRow.text); */
                addcomments(response['commentID'], response['href'], 'create', commentRow.text, userRow.name, commentRow.fromid);

            });
            callFBParse('fbcomment_form');
        });

        FB.Event.subscribe('comment.remove',
                function (response) {
                    removecomments(response.commentID);
                    callFBParse('fbcomment_form');
                }
        );
        if ($('contentlike-fb') != null) {
            FB.getLoginStatus(function (response) {
                //DISPLAY THE LIKE BUTTON
                $('contentlike-fb').style.display = 'block';

                if (response.status === 'connected') {
                    en4.facebookse.fbuser_id = response.authResponse.userID;
                    en4.facebookse.fb_access_token = response.authResponse.accessToken;
                    FB.api('me/permissions', function (response) {
                        if (typeof response.data[0] != 'undefined' && typeof response.data[0].publish_actions != 'undefined' && response.data[0].publish_actions == 1) {

                            en4.facebookse.post_like_session_active = 1;
                            en4.facebookse.post_like_fb_active = true;
                            en4.facebookse.setTokenAfterFB();
                        }

                    });

                } else if (response.status === 'not_authorized') {
                    en4.facebookse.post_like_fb_active = false;
                } else {
                    en4.facebookse.post_like_fb_active = false;
                }

            });
        }
        if ($('fbcomment_form'))
            setTimeout("$('fbcomment_form').style.display = 'block';", 3000);
    }
}

var Like_UnlikeResponse = function (like_unlike) {

    if (typeof fblike_moduletype != 'undefined' && fblike_moduletype && fblike_moduletype_id) {
        //SENDING AJAX REQUEST TO FIND WETHERE USER HAS ALREADY LIKED THE VIWING CONTENT OR NOT.

        if (like_unlike == 'like') {
            if (enable_likemodule == 1 && enable_likeintsetting == 1) {
                var CONTENT_LIKE = 0;

                if (window.seaocore_content_type_likes) {
                    CONTENT_LIKE = 1;
                    seaocore_content_type_likes(fblike_moduletype_id, fblike_moduletype);
                }
                if (CONTENT_LIKE == 0) {
                    if (FB_Comment_Setting != 1 && window.en4.core.comments.like && $('comments') && $('comments').getElement('.comments_options')) {
                        CONTENT_LIKE = 1;
                        en4.core.comments.like(fblike_moduletype, fblike_moduletype_id);
                    }
                    else if (FB_Comment_Setting != 1 && window.en4.seaocore.nestedcomments.like && $('global_content') && $('global_content').getElement('.seaocore_replies_wrapper')) {
                        CONTENT_LIKE = 1;
                        en4.seaocore.nestedcomments.like(fblike_moduletype, fblike_moduletype_id, '', 'DESC', '0');
                    }
                    else if (FB_Comment_Setting != 1 && window.en4.seaocore.comments.like && $('comments_' + fblike_moduletype + '_' + fblike_moduletype_id)) {
                        CONTENT_LIKE = 1;
                        en4.seaocore.comments.like(fblike_moduletype, fblike_moduletype_id);
                    } else if (window.FB_like && typeof enable_fbcommentbox != 'undefined' && enable_fbcommentbox == 1) {
                        CONTENT_LIKE = 1;
                        FB_like(fblike_moduletype, fblike_moduletype_id);
                    }
                }
            }
        }
        else {
            if (enable_likemodule == 1 && enable_likeintsetting == 1) {
                var CONTENT_LIKE = 0;

                if (window.seaocore_content_type_likes) {
                    CONTENT_LIKE = 1;
                    seaocore_content_type_likes(fblike_moduletype_id, fblike_moduletype);
                }
                if (CONTENT_LIKE == 0) {
                    if (FB_Comment_Setting != 1 && window.en4.core.comments.unlike && $('comments') && $('comments').getElement('.comments_options')) {
                        CONTENT_LIKE = 1;
                        en4.core.comments.unlike(fblike_moduletype, fblike_moduletype_id);
                    }
                    else if (FB_Comment_Setting != 1 && window.en4.seaocore.nestedcomments.unlike && $('global_content') && $('global_content').getElement('.seaocore_replies_wrapper')) {
                        CONTENT_LIKE = 1;
                        en4.seaocore.nestedcomments.unlike(fblike_moduletype, fblike_moduletype_id, '', 'DESC', '0');
                    }
                    else if (FB_Comment_Setting != 1 && window.en4.seaocore.comments.unlike && $('comments_' + fblike_moduletype + '_' + fblike_moduletype_id)) {
                        CONTENT_LIKE = 1;
                        en4.seaocore.comments.unlike(fblike_moduletype, fblike_moduletype_id);
                    } else if (window.FB_unlike && typeof enable_fbcommentbox != 'undefined' && enable_fbcommentbox == 1) {
                        CONTENT_LIKE = 1;
                        FB_unlike(fblike_moduletype, fblike_moduletype_id);
                    }
                }
            }

        }
        //}
        //});
        //request.send();
    }


}

var FBParseId;

var callFBParse = function (id) {
    FBParseId = id;
    if (typeof FB != 'undefined') {
        if (typeof id != 'undefined' && id != '')
            FB.XFBML.parse(document.getElementById(id));
        else
            FB.XFBML.parse();
    }
    else {
        runFacebookSdk();
        setTimeout('callFBParse(FBParseId);', 50);
    }

}

var showFbCommentBox = function (curr_url, module, successfbcommentbox) {

    var req = new Request.HTML({
        'url': en4.core.baseUrl + 'widget/index/mod/facebookse/name/facebookse-comments',
        'data': {
            'task': '1',
            'format': 'html',
            'subject': en4.core.subject.guid,
            'curr_url': curr_url,
            'module_type': module

        },
        onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {


            if (module == 'siteestore') {
                if ($('content'))
                    Elements.from(responseHTML).inject($('content'), 'after');

            }

            else {
                if ($('global_content').getElement('.comments') != null) {
                    if (successfbcommentbox == 2 && $('global_content').getElement(defalutCommentClass) != null) {


                        var FBcontainer = $('global_content').getElement(defalutCommentClass).getParent().getParent();
                        var newdiv_FB = document.createElement('div');
                        newdiv_FB.id = 'advfbcomments'
                        newdiv_FB.innerHTML = responseHTML;
                        FBcontainer.appendChild(newdiv_FB);
                        newdiv_FB.setStyle('display', 'block');
                    }
                    else {

                        if (typeof SeaoCommentbox_obj != 'undefined') {
                            SeaoCommentbox_obj.innerHTML = responseHTML;

                        }

                    }

                }
                else if (module != 'siteevent') {
                    var FBcontainer = $('global_content').getElement('.layout_main').getElement('.layout_middle');
                    var newdiv_FB = document.createElement('div');
                    fbcommentboxhtml = responseHTML;
                    newdiv_FB.id = 'advfbcomments'
                    newdiv_FB.innerHTML = responseHTML;
                    FBcontainer.appendChild(newdiv_FB);
                }
            }
            if (window.callFBParse)
                setTimeout('callFBParse(\'fbcomment_form\' );', 50);

        }
    });
    req.send();

}

var prev_commentid = 0;
function addcomments(_commentid, _address, _action,
        _commentMessage, _userName, _userId) {
    if (prev_commentid == _commentid)
        return;
    prev_commentid = _commentid;

    var req = new Request.JSON({
        method: 'POST',
        url: en4.core.baseUrl + 'facebookse/index/create-notification/',
        data:
                {
                    commentId: _commentid,
                    pageUrl: _address,
                    actionTaken: _action,
                    userName: _userName,
                    userId: _userId,
                    body: _commentMessage,
                    subject: en4.core.subject.guid
                }
    });
    req.send();
}

function removecomments(_commentid) {

    var req = new Request.JSON({
        method: 'POST',
        url: en4.core.baseUrl + 'facebookse/index/remove-notification/',
        data:
                {
                    comment_id: _commentid,
                    subject: en4.core.subject.guid
                }
    });
    req.send();
}

en4.facebookse = {
    post_like_fb_active: false,
    post_like_session_active: false,
    objectToLike: '',
    like_actionId: '',
    fb_access_token: '',
    fbuser_id: '',
    objectToLikeOrig: '',
    objectActionType: 'og.likes',
    ActionObject: 'object',
    fbfriends: {},
    options: {
        likesettings: {}

    },
    fb_content_type_like: function () {

        if (this.post_like_fb_active == false)
            this._redirecttoLogin();
        else {

            FB.api(
                    'https://graph.facebook.com/me/' + this.objectActionType + '?' + this.ActionObject + '=' + this.objectToLike + '&access_token=' + this.fb_access_token,
                    'post',
                    {
                        privacy: {
                            value: 'ALL_FRIENDS'
                        },
                    },
                    function (response) {
                        console.log(response);
                        if (!response) {
                            alert('Error occurred.');
                        } else if (response.error) {

                            if (response.error.code == 3501) {

                                var action_id = response.error.message.split(':');
                                this.like_actionId = action_id[1].trim();
                                fbpost_likecount--;
                                this._resetLikeUnlike('like');
                            }

                        } else {
                            this.like_actionId = response.id;
                            this._resetLikeUnlike('like');
                        }
                    }.bind(this)
                    );
        }
    },
    fb_content_type_update: function () {
        FB.api(
                'https://graph.facebook.com/' + this.like_actionId,
                'post',
                {
                    access_token: this.fb_access_token,
                    message: $('post_comment').value

                },
        function (response) {
            this.resetCommentBox();
        }.bind(this)
                );
    },
    fb_content_type_unlike: function (like_actionId) {
        if (this.like_actionId == '')
            this.like_actionId = like_actionId;

        FB.api(
                'https://graph.facebook.com/' + this.like_actionId,
                'delete',
                {
                    access_token: this.fb_access_token

                },
        function (response) {

            if (!response) {
                alert('Error occurred.');
            } else if (response.error) {

                this.fb_content_type_update();

            } else {

                this._resetLikeUnlike('unlike');
            }
        }.bind(this)
                );

    },
    resetCommentBox: function () {
        $('post_comment').value = '';
        $('show_fbPostCommentbox').style.display = 'none';

    },
    _redirecttoLogin: function () {

        FB.login(function (response) {
            this.fbuser_id = response.authResponse.userID;
            this.fb_access_token = response.authResponse.accessToken;
            this.post_like_session_active = true;
            this.post_like_fb_active = true;
            this.fb_content_type_like();
            this.setTokenAfterFB();

            // handle the response
        }.bind(this), {scope: 'publish_actions,user_likes'});

    },
    _resetLikeUnlike: function (action) {

        if (action == 'like') {
            var jsonData = JSON.parse(this.fbfriends);
            if ($('fbuser_image')) {
                $('fbuser_image').getFirst('img').set('src', 'https://graph.facebook.com/' + this.fbuser_id + '/picture');
                $('fbuser_image').removeClass('dnone');
            }
            $(fblike_moduletype + '_fbunlike_' + fblike_moduletype_id).style.display = 'inline-block';
            $(fblike_moduletype + '_fblike_' + fblike_moduletype_id).style.display = 'none';
            if ($('fblike_postcommentbox'))
                $('fblike_postcommentbox').getFirst('img').set('src', 'https://graph.facebook.com/' + this.fbuser_id + '/picture');
            if ($('fblikebutton_showfaces'))
                $('fblikebutton_showfaces').style.display = 'block';
            if ($('show_fbPostCommentbox'))
                this._showCommentBox();

            if (fbpost_likecount < 1)
                $('post_likecount').innerHTML = en4.core.language.translate('You like this.')
            else if (jsonData.length > 0) {
                var friendHTML = '';
                for (i = 0; i < jsonData.length; i++) {
                    friendHTML = friendHTML + '<a href=https://facebook.com/' + jsonData[i]['id'] + ' target="_blank">' + jsonData[i]['name'] + '</a>';
                    var flag = false;
                    if (jsonData.length > 1 && ((jsonData.length == fbpost_likecount))) {

                        if (i == parseInt(jsonData.length) - parseInt(2)) {
                            friendHTML = friendHTML + ' ' + en4.core.language.translate('and') + ' ';
                            flag = true;
                        }
                    }

                    if (i < (parseInt(jsonData.length) - parseInt(1)) && flag == false)
                        friendHTML = friendHTML + ', ';
                }

                if (fbpost_likecount == parseInt(jsonData.length)) {
                    if (fbpost_likecount == 1) {
                        friendHTML = en4.core.language.translate('You and') + ' ' + friendHTML + ' ' + en4.core.language.translate('like this.');
                    }
                    else {
                        friendHTML = en4.core.language.translate('You') + ', ' + friendHTML + ' ' + en4.core.language.translate('like this.');
                    }

                }
                else {
                    friendHTML = en4.core.language.translate('You') + ', ' + friendHTML + ' ' + en4.core.language.translate('and') + ' ' + en4.core.language.translate('%s others like this.', (parseInt(fbpost_likecount) - parseInt(jsonData.length)));
                }

                $('post_likecount').innerHTML = friendHTML;

            }
            else
                $('post_likecount').innerHTML = en4.core.language.translate('You and %s other people like this.', fbpost_likecount);
            fbpost_likecount++;
            Like_UnlikeResponse('like')
        }
        else {
            $(fblike_moduletype + '_fbunlike_' + fblike_moduletype_id).style.display = 'none';
            $(fblike_moduletype + '_fblike_' + fblike_moduletype_id).style.display = 'inline-block';
            if ($('fblikebutton_showfaces'))
                $('fblikebutton_showfaces').style.display = 'none';
            if ($('show_fbPostCommentbox'))
                $('show_fbPostCommentbox').style.display = 'none';
            var templikecount = parseInt(fbpost_likecount) - parseInt(1);
            if (fbpost_likecount <= 1)
                $('post_likecount').innerHTML = en4.core.language.translate('Be the first to like this content.')
            else
                $('post_likecount').innerHTML = en4.core.language.translate('%s people like this.', templikecount)
            fbpost_likecount--;
            Like_UnlikeResponse('unlike')
        }

    },
    loadFbLike: function (FBlikeSetting) {

        this.options.likesettings = FBlikeSetting;
        
        this.objectToLike = FBlikeSetting.objectToLike;
        var req = new Request.HTML({
            'url': en4.core.baseUrl + 'widget/index/mod/facebookse/name/facebookse-commonlike',
            'data': $merge({'subject': en4.core.subject.guid, 'isajax': true}, FBlikeSetting),
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {

                if ($('contentlike-fb')) {
                    call_advfbjs = 1;
                    $('contentlike-fb').innerHTML = responseHTML;
                    $('contentlike-fb').style.display = 'block';

                }
                en4.core.runonce.trigger();

            }
        });

        req.send();
    },
    setTokenAfterFB: function () {
        var req = new Request.JSON({
            'url': en4.core.baseUrl + 'facebookse/index/settoken',
            'data': {'access_token': this.fb_access_token, 'format': 'json'},
            onSuccess: function (responseJSON) {
                en4.facebookse.fbuser_id = responseJSON.fbUserId;
            }
        });

        req.send();
    },
    _showCommentBox: function () {

        //PLACE COMMENT BOX IN FOOTER
        var commentboxHTML = $('show_fbPostCommentbox').innerHTML;

        //GET THE CURRENT POSITION OF LIKE BUTTON:
        var position = $('contentlike-fb').getParent('div').getPosition();
        var height = $('contentlike-fb').getFirst('div').getHeight()

        $('show_fbPostCommentbox').destroy();
        (function () {
            var catarea = $('global_footer');
            if (catarea == null) {
                catarea = $('global_content');
            }
            if (catarea != null) {
                var newdiv = document.createElement('div');
                newdiv.id = 'show_fbPostCommentbox';
                newdiv.setStyles({
                    top: parseInt(position.y) + parseInt(height),
                    left: parseInt(position.x)
                });
                newdiv.inject(catarea, 'after');
                newdiv.innerHTML = commentboxHTML;
                newdiv.set('class', 'fblikebutton_postcommentbox_wrap');
            }

        }());

    },
    OpenShareWindow: function (url) {

        window.open('https://www.facebook.com/sharer/sharer.php?u=' + this.objectToLikeOrig, 'mywindow', 'width=500,height=500');
    },
    scrapeSiteUrl: function (url) {
        var req = new Request.JSON({
            method: 'POST',
            url: en4.core.baseUrl + 'facebookse/index/scrapeurl/',
            data:
                    {
                        format: 'json',
                        scrapeurl: url,
                        subject: en4.core.subject.guid
                    }
        });
        req.send();

    }
}