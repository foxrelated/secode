/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: core.js 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

en4.nestedcomment = { editCommentInfo:{}};
var tempUnlike = 0;
var tempLike = 0;
en4.nestedcomment.nestedcomments = {
    
    loadCommentReplies:function(comment_id) {
       $$('.reply'+comment_id).setStyle('display', 'inline-block');
       $('replies_show_'+comment_id).setStyle('display', 'none');
       $('replies_hide_'+comment_id).setStyle('display', 'inline-block');
    },
    hideCommentReplies:function(comment_id) {
       $$('.reply'+comment_id).setStyle('display', 'none');
       $('replies_hide_'+comment_id).setStyle('display', 'none');
       $('replies_show_'+comment_id).setStyle('display', 'inline-block');
    },
    showReplyEditForm:function(reply_id, is_enter_submit) {

        if(document.getElementsByClassName('reply_edit')) {
            var elements = document.getElementsByClassName('reply_edit');
            for (var i = 0; i < elements.length; i++){
                elements[i].style.display = 'none';
            }
        }
        if(document.getElementsByClassName('comment_edit')) {
            var elements = document.getElementsByClassName('comment_edit');
            for (var i = 0; i < elements.length; i++){
                elements[i].style.display = 'none';
            }
        }
        
        var elements = document.getElementsByClassName('comments_body')
        for (var i = 0; i < elements.length; i++){
            elements[i].style.display = 'inline-block';
        }
        if($('activity-reply-edit-form-'+reply_id).getElementById('compose-container') == null) { 
            en4.nestedcomment.nestedcomments.attachReply($('activity-reply-edit-form-'+reply_id), is_enter_submit, 'edit');
            if($('seaocore_comments_attachment_'+reply_id))
                $('seaocore_comments_attachment_'+reply_id).destroy();
            $('activity-reply-edit-form-'+reply_id).body.value = replyAttachment.editReply[reply_id].body;
         }

        $('activity-reply-edit-form-'+reply_id).style.display = 'block';  
        $('reply_body_' + reply_id).style.display = 'none';  
        $('reply_edit_'+reply_id).style.display = 'block';  
    },
    showCommentEditForm:function(comment_id, is_enter_submit) {
         if(document.getElementsByClassName('reply_edit')) {
            var elements = document.getElementsByClassName('reply_edit');
            for (var i = 0; i < elements.length; i++){
                elements[i].style.display = 'none';
            }
        }
        
        if(document.getElementsByClassName('comment_edit')) {
            var elements = document.getElementsByClassName('comment_edit');
            for (var i = 0; i < elements.length; i++){
                elements[i].style.display = 'none';
            }
        }
        var elements = document.getElementsByClassName('comments_body')
        for (var i = 0; i < elements.length; i++){
            elements[i].style.display = 'inline-block';
        }
       
       if($('activity-comment-edit-form-'+comment_id).getElementById('compose-container') == null) {  
            en4.nestedcomment.nestedcomments.attachComment($('activity-comment-edit-form-'+comment_id), is_enter_submit, 'edit');
            
            if($('seaocore_comments_attachment_'+comment_id))
              $('seaocore_comments_attachment_'+comment_id).destroy();
           
            $('activity-comment-edit-form-'+comment_id).body.value = commentAttachment.editComment[comment_id].body; 
       } 
       
        $('activity-comment-edit-form-'+comment_id).style.display = 'block';  
        $('comments_body_' + comment_id).style.display = 'none';  
        $('comment_edit_'+comment_id).style.display = 'block';  
    },
       comment: function(action_id, body, extendClass, formElementPhotoValue, formElementTypeValue, formElementPhotoSrc, form_values, action, comment_id) {
        
        if (body.trim() == '' && formElementPhotoValue == '')
        {
            return;
        }
        var show_all_comments_value = 0;
        if (typeof show_all_comments != 'undefined') {
            show_all_comments_value = show_all_comments.value;
        }
        if(formElementPhotoSrc) {
            var CommentHTML = '<div class="comments_author_photo"><a href="' + en4.user.viewer.href + '" ><img src="' + en4.user.viewer.iconUrl + '"  class="thumb_icon item_photo_user  thumb_icon"></a></div><div class="comments_info"><span class="comments_author"><a href="' + en4.user.viewer.href + '" class="sea_add_tooltip_link" rel="user 1">' + en4.user.viewer.title + '</a></span><span class="comments_body">' + body + '</span><div class="seaocore_comments_attachment" id="seaocore_comments_attachment"><div class="seaocore_comments_attachment_photo"><a><img src="'+formElementPhotoSrc+'" alt="" class="thumbs_photo thumb_normal item_photo_album_photo  thumb_normal"></a><div class="seaocore_comments_attachment_info"><div class="seaocore_comments_attachment_title"></div><div class="seaocore_comments_attachment_des"></div></div></div><ul class="comments_date"><li class="comments_timestamp">' + en4.advancedactivity.fewSecHTML + '</li></ul></div>';
        } else {
            var CommentHTML = '<div class="comments_author_photo"><a href="' + en4.user.viewer.href + '" ><img src="' + en4.user.viewer.iconUrl + '"  class="thumb_icon item_photo_user  thumb_icon"></a></div><div class="comments_info"><span class="comments_author"><a href="' + en4.user.viewer.href + '" class="sea_add_tooltip_link" rel="user 1">' + en4.user.viewer.title + '</a></span><span class="comments_body">' + body + '</span><ul class="comments_date"><li class="comments_timestamp">' + en4.advancedactivity.fewSecHTML + '</li></ul></div>';
        }
        if(action == 'create') {
            if ($("feed-comment-form-open-li_" + extendClass + action_id)) {
                new Element('li', {
                    'html': CommentHTML,
                  //  'styles': {'display': 'inline-block'}
                }).inject($("feed-comment-form-open-li_" + extendClass + action_id), 'before');
            } else {
                new Element('li', {
                    'html': CommentHTML,
                 //   'styles': {'display': 'inline-block'}
                }).inject($('comment-likes-activity-item-' + extendClass + action_id).getElement('.comments').getElement('ul'));
            }
        }
       
        form_values += '&format=json';
        form_values += '&subject=' + en4.core.subject.guid;
        form_values += '&isShare=' + adfShare;
        form_values += '&show_all_comments=' + show_all_comments_value;
        form_values += '&onViewPage=' + extendClass;
        form_values += '&photo_id=' + formElementPhotoValue;
        form_values += '&type=' + formElementTypeValue;
        var url; 
        if(action == 'create') {
           url = en4.core.baseUrl + 'advancedactivity/index/comment'; 
        }  else if(action == 'edit') {
           url = en4.core.baseUrl + 'nestedcomment/index/comment-edit';
           $('comment-' + comment_id).innerHTML = CommentHTML;
        } 
              
        en4.core.request.send(new Request.JSON({
            url: url,
            data: $merge(form_values.parseQueryString(),{
                body : body 
            }),
            onComplete: function(e) {
             
            }
        }), {
            'force': true,
            'element': $('comment-likes-activity-item-' + extendClass + action_id)
        });
    },    
    reply: function(comment_id, body, extendClass, action_id,formElementPhotoValue, formElementTypeValue, formElementPhotoSrc, form_values, action) {
        if (body.trim() == '' && formElementPhotoValue == '')
        {
            return;
        }
        var show_all_comments_value = 0;
        if (typeof show_all_comments != 'undefined') {
            show_all_comments_value = show_all_comments.value;
        }
        
        if(formElementPhotoSrc) {
            var CommentHTML = '<div class="comments_author_photo"><a href="' + en4.user.viewer.href + '" ><img src="' + en4.user.viewer.iconUrl + '"  class="thumb_icon item_photo_user  thumb_icon"></a></div><div class="comments_info"><span class="comments_author"><a href="' + en4.user.viewer.href + '" class="sea_add_tooltip_link" rel="user 1">' + en4.user.viewer.title + '</a></span><span class="comments_body">' + body + '</span><div class="seaocore_comments_attachment" id="seaocore_comments_attachment"><div class="seaocore_comments_attachment_photo"><a><img src="'+formElementPhotoSrc+'" alt="" class="thumbs_photo thumb_normal item_photo_album_photo  thumb_normal"></a><div class="seaocore_comments_attachment_info"><div class="seaocore_comments_attachment_title"></div><div class="seaocore_comments_attachment_des"></div></div></div><ul class="comments_date"><li class="comments_timestamp">' + en4.advancedactivity.fewSecHTML + '</li></ul></div>';
        } else {
            var CommentHTML = '<div class="comments_author_photo"><a href="' + en4.user.viewer.href + '" ><img src="' + en4.user.viewer.iconUrl + '"  class="thumb_icon item_photo_user  thumb_icon"></a></div><div class="comments_info"><span class="comments_author"><a href="' + en4.user.viewer.href + '" class="sea_add_tooltip_link" rel="user 1">' + en4.user.viewer.title + '</a></span><span class="comments_body">' + body + '</span><ul class="comments_date"><li class="comments_timestamp">' + en4.advancedactivity.fewSecHTML + '</li></ul></div>';
        }
        if(action == 'create') {
            if ($("feed-reply-form-open-li_" + extendClass + comment_id)) {
                new Element('li', {
                    'html': CommentHTML,
                    //'styles': {'display': 'inline-block'}
                }).inject($("feed-reply-form-open-li_" + extendClass + comment_id), 'before');
            } else {
                new Element('li', {
                    'html': CommentHTML,
                  //  'styles': {'display': 'inline-block'}
                }).inject($('comment-likes-activity-item-' + extendClass + action_id).getElement('.comments').getElement('ul'));
            }
        }
        var url; 
        if(action == 'create') {
           url = en4.core.baseUrl + 'nestedcomment/index/reply'; 
        }  else if(action == 'edit') {
           url = en4.core.baseUrl + 'nestedcomment/index/reply-edit';
           $('reply-' + comment_id).innerHTML = CommentHTML;
        }
        
        form_values += '&format=json';
        form_values += '&subject=' + en4.core.subject.guid;
        form_values += '&isShare=' + adfShare;
        form_values += '&show_all_comments=' + show_all_comments_value;
        form_values += '&onViewPage=' + extendClass;
        form_values += '&photo_id=' + formElementPhotoValue;
        form_values += '&type=' + formElementTypeValue;
        en4.core.request.send(new Request.JSON({
            url: url,
            data: $merge(form_values.parseQueryString(),{
                body : body 
            }),
        }), {
            'force': true,
            'element': $('comment-likes-activity-item-' + extendClass + action_id)
        });
    },            
    attachComment: function(formElement, is_enter_submit, action, body) {
        var bind = this;
        var hasViewPage = formElement.get('id').indexOf('view') < 0 ? 0 : 1;
        var extendClass = '';
        if (hasViewPage) {
            extendClass = 'view-';
        }

        var composerObj = new ComposerNestedActivityComment($(formElement.body.get('id')), {
            overText : true,
            lang: {
                'Post Something...': en4.core.language.translate('Write a comment...')
            },
            //hideSubmitOnBlur : false,
            allowEmptyWithAttachment : false,
            submitElement: 'submit'
        });
        formElement.store('composer',composerObj);
        if(typeof action != 'undefined' && action == 'edit' && commentAttachment.editComment[formElement.comment_id.value].body != '') {
            composerObj.setContent(commentAttachment.editComment[formElement.comment_id.value].body);
            composerObj.focus();
            formElement.store('composer',composerObj);
        }
        
        composerObj.addPlugin(new ComposerNestedActivityComment.Plugin.Tag({
         enabled: true,
         suggestOptions: {
             'url': en4.core.baseUrl + 'nestedcomment/friends/suggest-tag/includeSelf/1',
             'postData': {
                 'format': 'json',
                 'subject': en4.core.subject.guid,
                 'taggingContent' :activityTaggingContent
             },
             'maxChoices': 10
         },
         'suggestProto': 'request.json'
        }));
       
        if(smiliesEnabled) {
            var emoticons_parent_icons = new Element('div', {
                    'id': 'emoticons-parent-icons_' + formElement.get('id'), 
										'class' : 'seao_emoticons'         
                }).inject($(formElement.get('id')));

            emoticons_parent_icons.innerHTML = $('emoticons-comment-icons').innerHTML;
            
            $('emoticons-comment-button').setAttribute('id','emoticons-comment-button_' + formElement.get('id'));
            $('emotion_comment_label').setAttribute('id','emotion_comment_label_' + formElement.get('id'));
            $('emotion_comment_symbol').setAttribute('id','emotion_comment_symbol_' + formElement.get('id'));
            $('emoticons-comment-board').setAttribute('id','emoticons-comment-board_' + formElement.get('id'));
        }
         
        if(photoEnabled) {
            var commentNestedPhoto = new commentPhoto();
            commentNestedPhoto.getPhotoContent(formElement.get('id'), {requestOptions : {
                 'url'  : requestOptionsURLNestedComment
                },
                fancyUploadOptions : {
                  'url'  : fancyUploadOptionsURLNestedComment,
                  'path' : en4.core.basePath + 'externals/fancyupload/Swiff.Uploader.swf'
                }});

            if(typeof action != 'undefined' && action == 'edit' && commentAttachment.editComment[formElement.comment_id.value].attachment_body != '') {
               commentNestedPhoto.activate();
               commentNestedPhoto.doProcessResponse(commentAttachment.editComment[formElement.comment_id.value].attachment_body);
            } 
        }
      
        var formElementPhotoValue = '';
        var formElementTypeValue = '';
        var formElementPhotoSrc= '';

        if (is_enter_submit == 1) {
            formElement.addEvent((Browser.Engine.trident || Browser.Engine.webkit) ? 'keydown' : 'keypress', function(event) {
                if (event.shift && event.key == 'enter') {
                } else if (event.key == 'enter') {
                    event.stop();
                 
                if(formElement.photo_id && formElement.photo_id.value)
                    formElementPhotoValue = formElement.photo_id.value;
                if(formElement.type && formElement.type.value)
                    formElementTypeValue = formElement.type.value;
                if(formElement.src && formElement.src.value)
                    formElementPhotoSrc = formElement.src.value;
                
                
                if(composerObj.getPlugin('tag').getAAFTagsFromComposer().toQueryString() != '')
                        composerObj.getPlugin('tag').getComposer().fireEvent('editorSubmit');
                    
                    var form_values = composerObj.getForm().toQueryString();
                    
                    form_values = form_values.replace("body=&", "");
                     
                    if ((formElementPhotoValue == '' && composerObj.getContent() =='') || formElement.retrieve('sendReq', false))
                    {
                        return;
                    }
                 
                    if(typeof action != 'undefined' && action == 'edit') {
                        bind.comment(formElement.action_id.value, composerObj.getContent(), extendClass, formElementPhotoValue, formElementTypeValue,formElementPhotoSrc, form_values, 'edit', formElement.comment_id.value);
                    } else {
                        bind.comment(formElement.action_id.value, composerObj.getContent(), extendClass, formElementPhotoValue, formElementTypeValue,formElementPhotoSrc, form_values, 'create', 0);
                    }
                    //       formElement.store('sendReq', true);
//          setTimeout(function() {
                    formElement.body.value = '';
                    formElement.style.display = "none";
//          }, 2000);
                }
            });

        }
        formElement.addEvent('submit', function(event) {
            event.stop();
            if(formElement.photo_id && formElement.photo_id.value)
            formElementPhotoValue = formElement.photo_id.value;
        if(formElement.type && formElement.type.value)
            formElementTypeValue = formElement.type.value;
        if(formElement.src && formElement.src.value)
            formElementPhotoSrc = formElement.src.value;
            if ((formElementPhotoValue == '' && composerObj.getContent() =='') || formElement.retrieve('sendReq', false))
            {
                return;
            }
            
         if(composerObj.getPlugin('tag').getAAFTagsFromComposer().toQueryString() != '')
                        composerObj.getPlugin('tag').getComposer().fireEvent('editorSubmit');
                    var form_values = composerObj.getForm().toQueryString();
                    form_values = form_values.replace("body=&", "");

             if(typeof action != 'undefined' && action == 'edit') {
                bind.comment(formElement.action_id.value, composerObj.getContent(), extendClass, formElementPhotoValue, formElementTypeValue,formElementPhotoSrc, form_values, 'edit', formElement.comment_id.value);
            } else {
                bind.comment(formElement.action_id.value, composerObj.getContent(), extendClass, formElementPhotoValue, formElementTypeValue,formElementPhotoSrc, form_values, 'create', 0);
            }
            // formElement.store('sendReq', true);
//      setTimeout(function() {
//        formElement.store('sendReq', false);
            formElement.body.value = '';
            formElement.style.display = "none";
//      }, 1000);
        });
    },
            
    attachReply: function(formElement, is_enter_submit, action, body) {
        var bind = this;
        //formElement.style.display = "none";
        var hasViewPage = formElement.get('id').indexOf('view') < 0 ? 0 : 1;
        var extendClass = '';
        if (hasViewPage) {
            extendClass = 'view-';
        }
        
        var composerObj = new ComposerNestedActivityComment($(formElement.body.get('id')), {
            overText : true,
            lang: {
                'Post Something...': en4.core.language.translate('Write a reply...')
            },
            hideSubmitOnBlur : false,
            allowEmptyWithAttachment : false,
            submitElement: 'submit'
        });
       // composerObj.focus();
       
       
        if(typeof action != 'undefined' && action == 'edit' && replyAttachment.editReply[formElement.comment_id.value].body != '') {
            composerObj.setContent(replyAttachment.editReply[formElement.comment_id.value].body);
            composerObj.focus();
        }
         formElement.store('composer',composerObj);
        composerObj.addPlugin(new ComposerNestedActivityComment.Plugin.Tag({
         enabled: true,
         suggestOptions: {
             'url': en4.core.baseUrl + 'nestedcomment/friends/suggest-tag/includeSelf/1',
             'postData': {
                 'format': 'json',
                 'subject': en4.core.subject.guid,
                 'taggingContent' :activityTaggingContent
             },
             'maxChoices': 10
         },
         'suggestProto': 'request.json'
        }));
        
        if(smiliesEnabled) {
            var emoticons_parent_icons = new Element('div', {
              'id': 'emoticons-parent-icons_' + formElement.get('id'),
							'class' : 'seao_emoticons' 
            }).inject($(formElement.get('id')));
        
            emoticons_parent_icons.innerHTML = $('emoticons-comment-icons').innerHTML;
            
            if( is_enter_submit == 0 ) {
                //emoticons_parent_icons.inject(formElement.body.get('id'), 'before');
            }
            
            $('emoticons-comment-button').setAttribute('id','emoticons-comment-button_' + formElement.get('id'));
            $('emotion_comment_label').setAttribute('id','emotion_comment_label_' + formElement.get('id'));
            $('emotion_comment_symbol').setAttribute('id','emotion_comment_symbol_' + formElement.get('id'));
            $('emoticons-comment-board').setAttribute('id','emoticons-comment-board_' + formElement.get('id'));
        }
        
        if(photoEnabled) {
            var commentNestedPhoto = new commentPhoto();
            commentNestedPhoto.getPhotoContent(formElement.get('id'), {requestOptions : {
                 'url'  : requestOptionsURLNestedComment
                },
                fancyUploadOptions : {
                  'url'  : fancyUploadOptionsURLNestedComment,
                  'path' : en4.core.basePath + 'externals/fancyupload/Swiff.Uploader.swf'
                }});
           if(typeof action != 'undefined' && action == 'edit' && replyAttachment.editReply[formElement.comment_id.value].attachment_body != '') {
               commentNestedPhoto.activate();
               commentNestedPhoto.doProcessResponse(replyAttachment.editReply[formElement.comment_id.value].attachment_body);
            } 
        }
        
        var formElementPhotoValue = '';
        var formElementTypeValue = '';
        var formElementPhotoSrc= '';
        if (is_enter_submit == 1) {
            formElement.addEvent((Browser.Engine.trident || Browser.Engine.webkit) ? 'keydown' : 'keypress', function(event) {
                if (event.shift && event.key == 'enter') {
                } else if (event.key == 'enter') {
                    event.stop();
                            if(formElement.photo_id && formElement.photo_id.value)
            formElementPhotoValue = formElement.photo_id.value;
        if(formElement.type && formElement.type.value)
            formElementTypeValue = formElement.type.value;
        if(formElement.src && formElement.src.value)
            formElementPhotoSrc = formElement.src.value;
        if(composerObj.getPlugin('tag').getAAFTagsFromComposer().toQueryString() != '')
                        composerObj.getPlugin('tag').getComposer().fireEvent('editorSubmit');
                    if ((formElementPhotoValue == '' && composerObj.getContent() =='') || formElement.retrieve('sendReq', false))
            {
                return;
            }
                    var form_values = composerObj.getForm().toQueryString();
                    form_values = form_values.replace("body=&", "");
                    if(typeof action != 'undefined' && action == 'edit') {
                       bind.reply(formElement.comment_id.value, composerObj.getContent(), extendClass, formElement.action_id.value, formElementPhotoValue, formElementTypeValue, formElementPhotoSrc,form_values, 'edit');
                    } else {
                       bind.reply(formElement.comment_id.value, composerObj.getContent(), extendClass, formElement.action_id.value, formElementPhotoValue, formElementTypeValue, formElementPhotoSrc,form_values,'create'); 
                    }
                    //       formElement.store('sendReq', true);
//          setTimeout(function() {
                    formElement.body.value = '';
                    formElement.style.display = "none";
//          }, 2000);
                }
            });

            // add blur event
//            formElement.body.addEvent('blur', function() {
//                //  setTimeout(function(){ 
//                formElement.style.display = "none";
//                if ($("feed-reply-form-open-li_" + extendClass + formElement.comment_id.value))
//                    $("feed-reply-form-open-li_" + extendClass + formElement.comment_id.value).style.display = "none";
//                // },20);
//            });
        }
        formElement.addEvent('submit', function(event) {
            event.stop();
            if(formElement.photo_id && formElement.photo_id.value)
            formElementPhotoValue = formElement.photo_id.value;
        if(formElement.type && formElement.type.value)
            formElementTypeValue = formElement.type.value;
        if(formElement.src && formElement.src.value)
            formElementPhotoSrc = formElement.src.value;
        if(composerObj.getPlugin('tag').getAAFTagsFromComposer().toQueryString() != '')
                        composerObj.getPlugin('tag').getComposer().fireEvent('editorSubmit');
                  if ((formElementPhotoValue == '' && composerObj.getContent() =='') || formElement.retrieve('sendReq', false))
            {
                return;
            }
                    var form_values = composerObj.getForm().toQueryString();
                    form_values = form_values.replace("body=&", "");
            
            if(typeof action != 'undefined' && action == 'edit') {
                bind.reply(formElement.comment_id.value, formElement.body.value, extendClass, formElement.action_id.value, formElementPhotoValue, formElementTypeValue, formElementPhotoSrc,form_values,'edit' );
             } else {
                bind.reply(formElement.comment_id.value, formElement.body.value, extendClass, formElement.action_id.value, formElementPhotoValue, formElementTypeValue, formElementPhotoSrc,form_values,'create' ); 
             }
            // formElement.store('sendReq', true);
//      setTimeout(function() {
//        formElement.store('sendReq', false);
            formElement.body.value = '';
            formElement.style.display = "none";
//      }, 1000);
        });
    }, 
    loadComments: function(type, id, page, order, parent_comment_id, taggingContent, pre, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies) {

        if ($('view_more_comments_' + parent_comment_id) && pre == 3) {
            $('view_more_comments_' + parent_comment_id).style.display = 'inline-block';
            $('view_more_comments_' + parent_comment_id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';
        }
        if ($('view_previous_comments_' + parent_comment_id) && pre == 2) {
            $('view_previous_comments_' + parent_comment_id).style.display = 'inline-block';
            $('view_previous_comments_' + parent_comment_id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';
        }
        if ($('view_later_comments_' + parent_comment_id) && pre == 1) {
            $('view_later_comments_' + parent_comment_id).style.display = 'inline-block';
            $('view_later_comments_' + parent_comment_id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';
        }
        
        en4.core.request.send(new Request.HTML({
            url: en4.core.baseUrl + 'nestedcomment/comment/list',
            data: {
                format: 'html',
                type: type,
                id: id,
                page: page,
                order: order,
                parent_div: 1,
                parent_comment_id: parent_comment_id,
                taggingContent: taggingContent,
                showAsNested: showAsNested,
                showAsLike: showAsLike,
                showDislikeUsers: showDislikeUsers,
                showLikeWithoutIcon: showLikeWithoutIcon,
                showLikeWithoutIconInReplies: showLikeWithoutIconInReplies,
                showSmilies : showSmilies,
                photoLightboxComment: photoLightboxComment,
                commentsorder : commentsorder
                
            }
        }), {
            'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
        });
    },
    loadcommentssortby: function(type, id, order, parent_comment_id, taggingContent, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies) {
        if ($('sort' + '_' + type + '_' + id + '_' + parent_comment_id)) {
            $('sort' + '_' + type + '_' + id + '_' + parent_comment_id).style.display = 'inline-block';
            $('sort' + '_' + type + '_' + id + '_' + parent_comment_id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';
        }
        en4.core.request.send(new Request.HTML({
            url: en4.core.baseUrl + 'nestedcomment/comment/list',
            data: {
                format: 'html',
                type: type,
                id: id,
                order: order,
                parent_div: 1,
                parent_comment_id: parent_comment_id,
                taggingContent: taggingContent,
                showAsNested: showAsNested,
                showAsLike: showAsLike,
                showDislikeUsers: showDislikeUsers,
                showLikeWithoutIcon: showLikeWithoutIcon,
                showLikeWithoutIconInReplies: showLikeWithoutIconInReplies,
                showSmilies : showSmilies,
                photoLightboxComment: photoLightboxComment,
                commentsorder : commentsorder
            }
        }), {
            'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
        });
    },
    like: function(type, id, comment_id, order, parent_comment_id, option, taggingContent, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies,page) {
      if(tempLike == 0) {
        tempUnlike = tempLike = 1;
        if ($('like_comments_' + comment_id) && (option == 'child')) {
            $('like_comments_' + comment_id).style.display = 'inline-block';
            $('like_comments_' + comment_id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';
        }
        if ($('like_comments_' + type + '_' + id) && (option == 'parent')) {
            $('like_comments_' + type + '_' + id).style.display = 'inline-block';
            $('like_comments_' + type + '_' + id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';
        }
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'nestedcomment/comment/like',
            data: {
                format: 'json',
                type: type,
                id: id,
                comment_id: comment_id,
                order: order,
                parent_comment_id: parent_comment_id,
                taggingContent: taggingContent,
                showAsNested: showAsNested,
                showAsLike: showAsLike,
                showDislikeUsers: showDislikeUsers,
                showLikeWithoutIcon: showLikeWithoutIcon,
                showLikeWithoutIconInReplies: showLikeWithoutIconInReplies,
                page: page,
                showSmilies : showSmilies,
                photoLightboxComment: photoLightboxComment,
                commentsorder : commentsorder
            },
            onComplete: function(e) {
                tempUnlike = tempLike = 0;
                if ($('sitereview_most_likes_' + id)) {
                    $('sitereview_most_likes_' + id).style.display = 'none';
                }
                if ($('sitereview_unlikes_' + id)) {
                    $('sitereview_unlikes_' + id).style.display = 'block';
                }
            }
        }), {
            'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
        });}
    },
    unlike: function(type, id, comment_id, order, parent_comment_id, option, taggingContent, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies,page) {
        if(tempUnlike == 0) {
        tempLike = tempUnlike = 1;
        if ($('unlike_comments_' + comment_id) && (option == 'child')) {
            $('unlike_comments_' + comment_id).style.display = 'inline-block';
            $('unlike_comments_' + comment_id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';
        }
        if ($('unlike_comments_' + type + '_' + id) && (option == 'parent')) {
            $('unlike_comments_' + type + '_' + id).style.display = 'inline-block';
            $('unlike_comments_' + type + '_' + id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';
        }
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'nestedcomment/comment/unlike',
            data: {
                format: 'json',
                type: type,
                id: id,
                comment_id: comment_id,
                order: order,
                parent_comment_id: parent_comment_id,
                taggingContent: taggingContent,
                showAsNested: showAsNested,
                showAsLike: showAsLike,
                showDislikeUsers: showDislikeUsers,
                showLikeWithoutIcon: showLikeWithoutIcon,
                showLikeWithoutIconInReplies: showLikeWithoutIconInReplies,
                page: page,
                showSmilies : showSmilies,
                photoLightboxComment: photoLightboxComment,
                commentsorder : commentsorder
            },
            onComplete: function(e) {
                tempLike = tempUnlike = 0;
                if ($('sitereview_most_likes_' + id)) {
                    $('sitereview_most_likes_' + id).style.display = 'block';
                }
                if ($('sitereview_unlikes_' + id)) {
                    $('sitereview_unlikes_' + id).style.display = 'none';
                }
            }
        }), {
            'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
        });}
    },
    showLikes: function(type, id, order, parent_comment_id, taggingContent, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies) {
        en4.core.request.send(new Request.HTML({
            url: en4.core.baseUrl + 'nestedcomment/comment/list',
            data: {
                format: 'html',
                type: type,
                id: id,
                viewAllLikes: true,
                order: order,
                parent_comment_id: parent_comment_id,
                taggingContent: taggingContent,
                showAsNested: showAsNested,
                showAsLike: showAsLike,
                showDislikeUsers: showDislikeUsers,
                showLikeWithoutIcon: showLikeWithoutIcon,
                showLikeWithoutIconInReplies: showLikeWithoutIconInReplies,
                showSmilies : showSmilies,
                photoLightboxComment: photoLightboxComment,
                commentsorder : commentsorder
            }
        }), {
            'element': $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
        });
    },
    deleteComment: function(type, id, comment_id, order, parent_comment_id, taggingContent, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies) {
        if (!confirm(en4.core.language.translate('Are you sure you want to delete this?'))) {
            return;
        }
        if ($('comment-' + comment_id)) {
            $('comment-' + comment_id).destroy();
        }
        (new Request.JSON({
            url: en4.core.baseUrl + 'nestedcomment/comment/delete',
            data: {
                format: 'json',
                type: type,
                id: id,
                comment_id: comment_id,
                order: order,
                parent_comment_id: parent_comment_id,
                taggingContent: taggingContent,
                showAsNested: showAsNested,
                showAsLike: showAsLike,
                showDislikeUsers: showDislikeUsers,
                showLikeWithoutIcon: showLikeWithoutIcon,
                showLikeWithoutIconInReplies: showLikeWithoutIconInReplies,
                showSmilies : showSmilies,
                photoLightboxComment: photoLightboxComment,
                commentsorder : commentsorder
            },
            onComplete: function(e) {
                try {
                    var replyCount = $$('.seaocore_replies_options span')[0];
                    var m = replyCount.get('html').match(/\d+/);
                    var newCount = (parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0);
                    replyCount.set('html', replyCount.get('html').replace(m[0], e.commentsCount));
                    if(e.commentsCount == 0 || e.commentsCount == 1) {
                        if($("seaocore_replies_sorting"))
                          $("seaocore_replies_sorting").style.display = 'none';
                    
                        if($("seaocore_replies_li"))
                          $("seaocore_replies_li").style.display = 'none';
                    }
                } catch (e) {
                }
            }
        })).send();
    }
};

function showReplyData(option, id) {
    if (option == 1) {
        if($('seaocore_data-' + id))
        $('seaocore_data-' + id).style.display = 'none';
        if ($('comment-' + id))
            $('comment-' + id).className = "seaocore_replies_list seaocore_comments_hide";
        if($('show_' + id))
        $('show_' + id).style.display = 'block';
        if($('hide_' + id))
        $('hide_' + id).style.display = 'none';
    } else {
        if($('seaocore_data-' + id))
        $('seaocore_data-' + id).style.display = 'block';
        if($('show_' + id))
        $('show_' + id).style.display = 'none';
        if($('hide_' + id))
        $('hide_' + id).style.display = 'block';
        if ($('comment-' + id))
            $('comment-' + id).className = "seaocore_replies_list";
    }
}

function sortComments(order, type, id, parent_comment_id, taggingContent, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies) {
    en4.nestedcomment.nestedcomments.loadcommentssortby(type, id, order, parent_comment_id, taggingContent, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies);
}

function showReplyForm(type, id, comment_id) {
    
    if(document.getElementsByClassName('comments_form_nestedcomments_comments')) {
        var elements = document.getElementsByClassName('comments_form_nestedcomments_comments');
        for (var i = 0; i < elements.length; i++){
            if(elements[i] != $('comments-form_' + type + '_' + id + '_' + comment_id)) {
                elements[i].style.display = 'none';
            }
        }
    }
    $('comments-form_' + type + '_' + id + '_' + comment_id).body.focus();
    if ($('comments-form_' + type + '_' + id + '_' + comment_id).style.display == 'none') {
        $('comments-form_' + type + '_' + id + '_' + comment_id).style.display = 'block';
        $('comments-form_' + type + '_' + id + '_' + comment_id).body.focus();
        if (($('comments-form_' + type + '_' + id + '_' + comment_id).getElementById('compose-container')) == null) {
            makeComposer($($('comments-form_' + type + '_' + id + '_' + comment_id).body).id, type, id, comment_id, null, 'Write a reply...');
            tagContentComment();
        }
    }
    else {
        $('comments-form_' + type + '_' + id + '_' + comment_id).style.display = 'none';
    }
}


function showEditForm(type, id, comment_id, parent_comment_id) {
    
    showReplyData(0, comment_id);
    if(document.getElementsByClassName('comment_edit')) {
        var elements = document.getElementsByClassName('comment_edit');
        for (var i = 0; i < elements.length; i++){
            elements[i].style.display = 'none';
        }
    }
    
    if(document.getElementsByClassName('seaocore_replies_comment')) {
        var elements = document.getElementsByClassName('seaocore_replies_comment');
        for (var i = 0; i < elements.length; i++){
            elements[i].style.display = 'block';
        }
    }
    
    if(document.getElementsByClassName('comment_close')) {
        var elements = document.getElementsByClassName('comment_close');
        for (var i = 0; i < elements.length; i++){
            elements[i].style.display = 'none';
        }
    }
    
    if($('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id).style.display =='' || $('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id).style.display == 'none') {
        $('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id).style.display = 'block';
        $('seaocore_edit_comment_' + comment_id).style.display = 'block';
        $('seaocore_comment_data-' + comment_id).style.display = 'none';
        if($('close_edit_box-'+ comment_id)) {
            $('close_edit_box-'+ comment_id).style.display = 'block';
        }
    } else {
        $('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id).style.display = 'none';
         $('seaocore_edit_comment_' + comment_id).style.display = 'none';
        $('seaocore_comment_data-' + comment_id).style.display = 'block';
        if($('close_edit_box-'+ comment_id))
        $('close_edit_box-'+ comment_id).style.display = 'none';
    }
    $('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id).body.focus();
    if (($('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id).getElementById('compose-container')) == null) {
        makeComposer($($('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id).body).id, type, id, comment_id, parent_comment_id);
        tagContentComment();
    }
}

var makePhotoComposer = function() {
    
    if (composeInstanceComment.options.type)
        type = composeInstanceComment.options.type;
    composeInstanceComment.addPlugin(new ComposerNestedComment.Plugin.Photo({
        title: 'Add Photo',
        lang: {
            'Add Photo': en4.core.language.translate('Add Photo'),
            'Select File': en4.core.language.translate('Select File'),
            'cancel': en4.core.language.translate('cancel'),
            'Loading...': en4.core.language.translate('Loading...'),
            'Unable to upload photo. Please click cancel and try again': en4.core.language.translate('Unable to upload photo. Please click cancel and try again')
        },
        requestOptions: {
            'url': en4.core.baseUrl + 'nestedcomment/album/compose-upload/type/comment'
        },
        fancyUploadOptions: {
            'url': en4.core.baseUrl + 'nestedcomment/album/compose-upload/format/json/type/comment',
            'path': en4.core.basePath + 'externals/fancyupload/Swiff.Uploader.swf'
        }
    }));
}

var makeLinkComposer = function() {
    composeInstanceComment.addPlugin(new ComposerNestedComment.Plugin.Link({
        title: en4.core.language.translate('Add Link'),
        lang: {
            'cancel': en4.core.language.translate('cancel'),
            'Last': en4.core.language.translate('Last'),
            'Next': en4.core.language.translate('Next'),
            'Attach': en4.core.language.translate('Attach'),
            'Loading...': en4.core.language.translate('Loading...'),
            'Don\'t show an image': en4.core.language.translate('Don\'t show an image'),
            'Choose Image:': en4.core.language.translate('Choose Image:'),
            '%d of %d': en4.core.language.translate('%d of %d')
        },
        requestOptions: {
            'url': en4.core.baseUrl + 'core/link/preview'
        }
    }));
}

function makeComposer(body, type, id, comment_id,parent_comment_id, overtext) {
    
    if(typeof parent_comment_id != 'undefined' && parent_comment_id != null) {
      menuElement =  'compose-containe-menu-items_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id;
      var formEle = $('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id);
    } else {
       menuElement =  'compose-containe-menu-items_' + type + '_' + id + '_' + comment_id;
       var formEle = $('comments-form_' + type + '_' + id + '_'+ comment_id);
    }
    var lanText = 'Write a comment...';
    if(typeof overtext != 'undefined') {
        lanText = overtext;
    }
    
    // @todo integrate this into the composer
   // if (!DetectMobileQuick() && !DetectIpad()) {
        composeInstanceComment = new ComposerNestedComment(body, {
            menuElement: menuElement,
            baseHref: en4.core.baseUrl,
            lang: {
                'Post Something...': en4.core.language.translate('Write a comment...')
            },
            type: type,
            id: id,
            parent_comment_id: comment_id,
            edit_comment_id: parent_comment_id,
            taggingContent: taggingContent,
            showAsNested: showAsNested,
            showAsLike: showAsLike,
            showDislikeUsers: showDislikeUsers,
            showLikeWithoutIcon: showLikeWithoutIcon,
            showLikeWithoutIconInReplies: showLikeWithoutIconInReplies,
            overText: true,
            showLangText :lanText,
            showSmilies : showSmilies,
                photoLightboxComment: photoLightboxComment,
                commentsorder : commentsorder
        });
    //}
    if (showAddPhoto == 1) {
        makePhotoComposer();
    }

    if (showAddLink == 1) {
        makeLinkComposer();
    }
     
    if (showSmilies == 1) {
        makeSmilies(formEle, menuElement);
    }
    
    if(typeof parent_comment_id != 'undefined' && parent_comment_id != null) {
        composerContent = $('seaocore_comment_data-'+ comment_id);
        if(composerContent.getElementById('seaocore_comments_attachment'))
        composerContent.removeChild(composerContent.getElementById('seaocore_comments_attachment'));
    
        composeInstanceComment.setContent(en4.nestedcomment.editCommentInfo[comment_id].body);
        if(en4.nestedcomment.editCommentInfo[comment_id].attachment_type == 'album_photo') {
            composeInstanceComment.getPlugin('photo').activate();
            composeInstanceComment.getPlugin('photo').doProcessResponse(en4.nestedcomment.editCommentInfo[comment_id].attachment_body);
        } else if(en4.nestedcomment.editCommentInfo[comment_id].attachment_type == 'core_link'){
           composeInstanceComment.getPlugin('link').activate();
           composeInstanceComment.getPlugin('link').doAttach(en4.nestedcomment.editCommentInfo[comment_id].attachment_body.url);
        } 
    }

}

function makeSmilies(formEle, menuElement) {
   
   if(nestedCommentPressEnter == 1) {
    var emoticons_parent_icons = new Element('div', {
            'id': 'emoticons-parent-icons_' + formEle.get('id'), 
            'class' : 'seao_emoticons',
             'styles': {
                'display': 'none'
            }
        }).inject(menuElement);
        emoticons_parent_icons.inject(formEle.getElementById('compose-container'), 'after');
   }
   else {
      var emoticons_parent_icons = new Element('div', {
            'id': 'emoticons-parent-icons_' + formEle.get('id'), 
            'class' : 'seao_emoticons seao_inside_smile',
            'styles': {
                'display': 'none'
            }
        }).inject(menuElement); 
        
        emoticons_parent_icons.inject($("composer_container_icons_" + formEle.get('action-id')));
   }
    
    emoticons_parent_icons.innerHTML = $('emoticons-nested-comment-icons').innerHTML;

    $('emoticons-nested-comment-button').setAttribute('id','emoticons-nested-comment-button_' + formEle.get('id'));
    $('emotion_nested_comment_label').setAttribute('id','emotion_nested_comment_label_' + formEle.get('id'));
    $('emotion_nested_comment_symbol').setAttribute('id','emotion_nested_comment_symbol_' + formEle.get('id'));
    $('emoticons-nested-comment-board').setAttribute('id','emoticons-nested-comment-board_' + formEle.get('id'));
    

    
}

function tagContentComment() {

    composeInstanceComment.addPlugin(new ComposerNestedComment.Plugin.Nctag({
        enabled: true,
        suggestOptions: {
            'url': en4.core.baseUrl + 'nestedcomment/friends/suggest-tag/includeSelf/1',
            'postData': {
                'format': 'json',
                'subject': en4.core.subject.guid,
                'taggingContent': taggingContent
            },
            'maxChoices': 10
        },
        'suggestProto': 'request.json'
    }));
}

en4.nestedcomment.ajaxTab = {
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
                if (en4.nestedcomment.ajaxTab.click_elment_id == widget_id)
                    return;
                en4.nestedcomment.ajaxTab.click_elment_id = widget_id;
                en4.nestedcomment.ajaxTab.sendReq(params);
            });
            element.store('addClickEvent', true);
            var attachOnLoadEvent = false;
            if (widget_id) {
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
                        el.get('class').split(' ').each(function(className) {
                            className = className.trim();
                            if (className.match(/^tab_[0-9]+$/) && className == "tab_" + widget_id) {
                                attachOnLoadEvent = true;
                                if (addActiveTab || tab_content_id_sitestore == widget_id) {
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
            en4.nestedcomment.ajaxTab.click_elment_id = widget_id;
            en4.nestedcomment.ajaxTab.sendReq(params);
        });


    },
    sendReq: function(params) {
        params.responseContainer.each(function(element) {
            element.empty();
            new Element('div', {
                'class': 'nestedcomment_profile_loading_image'
            }).inject(element);
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

var hideCommentEmotionIconClickEnable=false;
var hideNestedCommentEmotionIconClickEnable=false;
function setCommentEmoticonsBoard(obj){
     var formEle = obj.getParent().getParent();
     $('emotion_comment_label_' + formEle.get('id')).innerHTML="";
     $('emotion_comment_symbol_' + formEle.get('id')).innerHTML="";
     hideCommentEmotionIconClickEnable=true; 
     hideNestedCommentEmotionIconClickEnable=true; 
     var  a=$('emoticons-comment-button_' + formEle.get('id'));
     a.toggleClass('emoticons_comment_active');
     a.toggleClass('');
     var  el=$('emoticons-comment-board_' + formEle.get('id') );
     var hasClose = el.hasClass("seaocore_comment_embox_closed");
     $$('.seaocore_comment_embox').removeClass('seaocore_comment_embox_open').addClass('seaocore_comment_embox_closed'); 
     if(hasClose){
          el.removeClass('seaocore_comment_embox_closed').addClass('seaocore_comment_embox_open');  
     } 
 }

function addCommentEmotionIcon(iconCode, obj){ 
    var content; 
    var formEle = obj.getParent().getParent().getParent().getParent();
    var composerObj = formEle.retrieve('composer');
    content=composerObj.elements.body.get('html');
    content=content.replace(/(<br>)$/g, "");
    content =  content +' '+ iconCode; 
    composerObj.setContent(content);
        $$('div.compose-content').each(function (el, index) {
                if (index == 0)
                {
                    el.set('tabindex', '0');
                    el.focus();
                }
            });
 
 }
  //hide on body click
  en4.core.runonce.add(function() {
      $(document.body).addEvent('click',function(e) {
          hideCommentEmotionIconClickEvent();
      });
  });   

function hideCommentEmotionIconClickEvent(){
    if(!hideCommentEmotionIconClickEnable && $$('.seaocore_comment_embox')) { 
       $$('.seaocore_comment_embox').removeClass('seaocore_comment_embox_open').addClass('seaocore_comment_embox_closed'); 
    }
    hideCommentEmotionIconClickEnable=false;
}  

function setCommentEmotionLabelPlate(label,symbol,obj){
    var formEle = obj.getParent().getParent().getParent().getParent();
    $('emotion_comment_label_'  + formEle.get('id')).innerHTML=label;
    $('emotion_comment_symbol_' + formEle.get('id')).innerHTML=symbol;
}
 
function setNestedCommentEmoticonsBoard(obj){
    if(composeInstanceComment)
       composeInstanceComment.focus();
    if(nestedCommentPressEnter == 1) {
      var formEle = obj.getParent().getParent();
    } else {
      var formEle = obj.getParent().getParent().getParent().getParent();  
    }

    $('emotion_nested_comment_label_' + formEle.get('id')).innerHTML="";
    $('emotion_nested_comment_symbol_' + formEle.get('id')).innerHTML="";
     hideNestedCommentEmotionIconClickEnable=true;
     hideCommentEmotionIconClickEnable=true;
     var  a=$('emoticons-nested-comment-button_' + formEle.get('id'));
     a.toggleClass('emoticons_comment_active');
     a.toggleClass('');
     var  el=$('emoticons-nested-comment-board_' + formEle.get('id') );
    // el.toggleClass('seaocore_comment_embox_open');
    // el.toggleClass('seaocore_comment_embox_closed');
     
     var hasClose = el.hasClass("seaocore_comment_embox_closed");
     $$('.seaocore_comment_embox').removeClass('seaocore_comment_embox_open').addClass('seaocore_comment_embox_closed'); 
     if(hasClose){
          el.removeClass('seaocore_comment_embox_closed').addClass('seaocore_comment_embox_open');  
     }
     
}
    

function addNestedCommentEmotionIcon(iconCode, obj){ 
     var content; 
     if('useContentEditable' in composeInstanceComment.options && composeInstanceComment.options.useContentEditable)
       content=composeInstanceComment.elements.body.get('html');  
     else  
     content=composeInstanceComment.getContent();
        content=content.replace(/(<br>)$/g, "");
        content =  content +' '+ iconCode; 
       composeInstanceComment.setContent(content);
       
           $$('div.compose-content').each(function (el, index) {
                if (index == 0)
                {
                    el.set('tabindex', '0');
                    el.focus();
                }
            });
 }
  en4.core.runonce.add(function() {
      $(document.body).addEvent('click',function(e) {
          hideNestedCommentEmotionIconClickEvent();
      });
  });   

function hideNestedCommentEmotionIconClickEvent(){
    if(!hideNestedCommentEmotionIconClickEnable && $$('.seaocore_comment_embox')) {       
       $$('.seaocore_comment_embox').removeClass('seaocore_comment_embox_open').addClass('seaocore_comment_embox_closed'); 
    }
    hideNestedCommentEmotionIconClickEnable=false;
    hideCommentEmotionIconClickEnable=false;
}  

function setNestedCommentEmotionLabelPlate(label,symbol,obj){
    
    if(nestedCommentPressEnter == 1) {
      var formEle = obj.getParent().getParent().getParent().getParent();
    } else {
      var formEle = obj.getParent().getParent().getParent().getParent().getParent().getParent(); 
    }
    if($('emotion_nested_comment_label_'  + formEle.get('id')))
    $('emotion_nested_comment_label_'  + formEle.get('id')).innerHTML=label;
    if($('emotion_nested_comment_symbol_' + formEle.get('id')))
    $('emotion_nested_comment_symbol_' + formEle.get('id')).innerHTML=symbol;


}

function showCommentBox(comment_box_id, body_box_id) {

    if($(comment_box_id).getElementById('compose-container') == null) {
      en4.nestedcomment.nestedcomments.attachComment($(comment_box_id), allowQuickComment);    
    } else {
        var composerObj = $(comment_box_id).retrieve('composer');
        composerObj.focus();
    }
    $(comment_box_id).style.display = 'block';
    $(body_box_id).focus();
}

function showReplyBox(reply_box_id, body_box_id) {
      
    if(document.getElementsByClassName('activity-reply-form')) {
        var elements = document.getElementsByClassName('activity-reply-form');
        for (var i = 0; i < elements.length; i++){
            if(elements[i] != $(reply_box_id)) {
                elements[i].style.display = 'none';
            }
        }
    } 
    
    if($(reply_box_id).getElementById('compose-container') == null) {
      en4.nestedcomment.nestedcomments.attachReply($(reply_box_id), allowQuickReply); 
    } else {
        var composerObj = $(reply_box_id).retrieve('composer');
        composerObj.focus();
    }
    $(reply_box_id).style.display = 'block';
    $(body_box_id).focus();
}

function showSortComments() {
        if($('sorting_dropdown_menu').style.display == '' || $('sorting_dropdown_menu').style.display == 'none') {
            $('sorting_dropdown_menu').style.display = 'block';
        } else {
            $('sorting_dropdown_menu').style.display = 'none';
        }
//        $('sorting_dropdown_menu').toggle();
    }