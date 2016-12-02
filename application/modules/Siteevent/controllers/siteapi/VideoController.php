<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: VideoController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_VideoController extends Siteapi_Controller_Action_Standard {

    public function init() {

        $event_id = $this->_getParam('event_id');

        $type_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.show.video', 1);

        $video_id = $this->_getParam('video_id', $this->_getParam('video_id', null));

        if (!empty($event_id)) {
            $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        }
        if ($type_video) {
            if ($video_id) {
                $video = Engine_Api::_()->getItem('video', $video_id);
                if ($video) {
                    Engine_Api::_()->core()->setSubject($video);
                }
            }
        } else {
            if ($video_id) {
                $reviewVideo = Engine_Api::_()->getItem('siteevent_video', $video_id);
                $event_id = $reviewVideo->event_id;
                $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

                if ($reviewVideo) {
                    Engine_Api::_()->core()->setSubject($reviewVideo);
                }
            }
        }

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
            $this->_forward('throw-error', 'index', 'siteevent', array(
                "error_code" => "unauthorized"
            ));
        return;
    }

    /**
     * Throw the init constructor errors.
     *
     * @return array
     */
    public function throwErrorAction() {
        $message = $this->getRequestParam("message", null);
        if (($error_code = $this->getRequestParam("error_code")) && !empty($error_code)) {
            if (!empty($message))
                $this->respondWithValidationError($error_code, $message);
            else
                $this->respondWithError($error_code);
        }

        return;
    }

    /**
     * Return the "Browse Search" form. 
     * 
     * @return array
     */
    public function searchFormAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->getVideoBrowseSearchForm(), true);
    }

    /**
     * Get browse video page.
     * 
     * @return array
     */
    public function browseAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $response = array();
        $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams('video', null, 'create')->checkRequire();
        $response['canEdit'] = $this->_helper->requireAuth()->setAuthParams('video', null, 'edit')->checkRequire();
        // Prepare
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $limit = $this->_getParam('limit', 10);
        $page = $this->_getParam('page', 1);

        $event_id = $this->_getParam('event_id');
        if ($this->getRequestParam('event_id')) {
            $values['event_id'] = $this->getRequestParam('event_id');
            $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        }

        if (!isset($siteevent) && empty($siteevent))
            $this->respondWithError('no_record');

        //VIDEO TABLE
        $videoTable = Engine_Api::_()->getDbtable('videos', 'siteevent');
        $type_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.show.video');
        //TOTAL VIDEO COUNT FOR THIS EVENT
        $counter = $videoTable->getEventVideoCount($siteevent->event_id);

        //AUTHORIZATION CHECK
        $allowed_upload_video = Engine_Api::_()->siteevent()->allowVideo($siteevent, $viewer, $counter);
        //FETCH RESULTS
        $paginator = Engine_Api::_()->getDbTable('clasfvideos', 'siteevent')->getEventVideos($siteevent->event_id, 1, $type_video);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($limit);

        $counter = $paginator->getTotalItemCount();
        $can_edit = $siteevent->authorization()->isAllowed($viewer, "edit");

        //IS SITEVIDEOVIEW MODULE ENABLED
        $sitevideoviewEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideoview');

        // check to see if request is for specific user's listings
        $user = $this->getRequestParam('user', null);
        if ($user)
            $values['user_id'] = $user;

        $user_id = $this->getRequestParam('user_id', null);
        if ($user_id)
            $values['user_id'] = $user_id;

        try {
            foreach ($paginator as $video) {
                $browseVideo = $video->toArray();

                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video);
                $browseVideo = array_merge($browseVideo, $getContentImages);

                // Add owner images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video, true);
                $browseVideo = array_merge($browseVideo, $getContentImages);

                $browseVideo["owner_title"] = $video->getOwner()->getTitle();
                $isAllowedView = $video->authorization()->isAllowed($viewer, 'view');
                $browseVideo["allow_to_view"] = empty($isAllowedView) ? 0 : 1;
                $browseVideo["like_count"] = $video->likes()->getLikeCount();
                $browseVideo["rating_count"] = Engine_Api::_()->video()->ratingCount($video->getIdentity());
                $browseVideo['video_url'] = Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->getVideoURL($video);
                $menus = array();
                if ($this->getRequestParam('menu', true)) {
                    if (!empty($viewer_id)) {
                        if ($video->authorization()->isAllowed($viewer, 'edit')) {
                            $menus[] = array(
                                'label' => $this->translate('Edit Video'),
                                'name' => 'edit',
                                'url' => 'advancedevents/video/edit/' . $siteevent->getIdentity() . '/' . $video->getIdentity(),
                            );
                        }

                        if ($video->authorization()->isAllowed($viewer, 'delete')) {
                            $menus[] = array(
                                'label' => $this->translate('Delete Video'),
                                'name' => 'delete',
                                'url' => 'advancedevents/video/delete/' . $siteevent->getIdentity() . '/' . $video->getIdentity(),
                            );
                        }
                        if (isset($menus) && !empty($menus))
                            $browseVideo['menu'] = $menus;
                    }
                }

                $response['totalItemCount'] = $paginator->getTotalItemCount();
                $response['response'][] = $browseVideo;
            }
        } catch (Exception $ex) {
            
        }

        $this->respondWithSuccess($response, true);
    }

    //ACTION FOR CREATE VIDEO
    public function createAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');


        //GET EVENT ID
        $event_id = $this->_getParam('event_id');


        //GET CONTENT ID
        $content_id = $this->_getParam('content_id');


        //GET SITEEVENT OBJECT
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (empty($siteevent))
            $this->respondWithError('no_record');


        //GET VIEWER INFO
        $viewer = Engine_Api::_()->user()->getViewer();

        //VIDEO TABLE
        $videoTable = Engine_Api::_()->getDbtable('videos', 'siteevent');

        //TOTAL VIDEO COUNT FOR THIS EVENT
        $counter = $videoTable->getEventVideoCount($event_id);
        $allowed_upload_video = Engine_Api::_()->siteevent()->allowVideo($siteevent, $viewer, $counter);
        if (empty($allowed_upload_video)) {
            $this->respondWithError('unauthorized');
        }

        $viewer_id = $viewer->getIdentity();

        //VIDEO UPLOAD PROCESS
//        $this->view->imageUpload = Engine_Api::_()->siteevent()->isUpload();

        $canEdit = $siteevent->authorization()->isAllowed($viewer, "edit");

        //FORM GENERATON
        if ($this->getRequest()->isGet()) {
            $getVideoCreateForm = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getVideoCreateForm();
            $this->respondWithSuccess($getVideoCreateForm, true);
        } else if ($this->getRequest()->isPost()) {

            // CONVERT POST DATA INTO THE ARRAY.
            $values = array();
            $getVideoCreateForm = Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->getVideoCreateForm();
            foreach ($getVideoCreateForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $values = @array_merge($values, array(
                        'owner_type' => $viewer->getType(),
                        'owner_id' => $viewer->getIdentity(),
            ));

            // START FORM VALIDATION
            $data = $values;
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'siteevent')->getVideoCreateFormValidators();
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }
            $values['event_id'] = $event_id;

            // IN CASE OF YOUTUBE AND VIMEO UPLOAD THE VIDEO AND RETURN THE VIDEO OBJECT.
            if (isset($values['url']))
                $siteevent = $video = $this->_composeUploadAction($values);

            // IN CASE OF DEVICE UPLOADED VIDEOS.
            if (isset($_FILES['filedata']) && !empty($_FILES['filedata']['name']))
                $siteevent = $video = $this->_uploadVideoAction($values);

            //VIDEO CREATION PROCESS

            $db = Engine_Api::_()->getDbtable('videos', 'siteevent')->getAdapter();
            $db->beginTransaction();

            // $params = $siteevent->main_video;
            $siteeventOtherInfo = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getOtherinfo($siteevent->event_id);
            $params = $siteeventOtherInfo->main_video;
            // Now try to create thumbnail
            $thumbnail = $this->_handleThumbnail($video->type, $video->code);
            $ext = ltrim(strrchr($thumbnail, '.'), '.');
            $thumbnail_parsed = @parse_url($thumbnail);
            if (@GetImageSize($thumbnail)) {
                $valid_thumb = true;
            } else {
                $valid_thumb = false;
            }


            if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
                $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
                $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;

                $src_fh = fopen($thumbnail, 'r');
                $tmp_fh = fopen($tmp_file, 'w');
                stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

                $image = Engine_Image::factory();
                $image->open($tmp_file)
                        ->resize(120, 240)
                        ->write($thumb_file)
                        ->destroy();

                try {
                    $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                        'parent_type' => $video->getType(),
                        'parent_id' => $video->getIdentity()
                    ));

                    // Remove temp file
                    @unlink($thumb_file);
                    @unlink($tmp_file);
                } catch (Exception $e) {
                    
                }

                $information = $this->_handleInformation($video['type'], $video['code']);

                $video->duration = $information['duration'];
                if (!$video->description) {
                    $video->description = $information['description'];
                }
                $video->photo_id = $thumbFileRow->file_id;
                $video->status = 1;
                $video->save();

                // Insert new action item
                $insert_action = true;
            }

            // CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            if (isset($values['auth_view']))
                $auth_view = $values['auth_view'];
            else
                $auth_view = "everyone";
            $viewMax = array_search($auth_view, $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
            }

            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            if (isset($values['auth_comment']))
                $auth_comment = $values['auth_comment'];
            else
                $auth_comment = "everyone";
            $commentMax = array_search($auth_comment, $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
            }
            // Add tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $video->tags()->addTagMaps($viewer, $tags);

            $db->commit();

            $db->beginTransaction();
            try {
                if ($insert_action) {
                    $owner = $video->getOwner();
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $video, 'video_new');
                    if ($action != null) {
                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
                    }
                }

                // Rebuild privacy
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                foreach ($actionTable->getActionsByObject($video) as $action) {
                    $actionTable->resetActivityBindings($action);
                }

                $db->commit();
                $this->successResponseNoContent('no_content', true);

                // Change request method POST to GET
                $this->setRequestMethod();
                $this->_forward('view', 'video', 'sitevent', array(
                    'video_id' => $video->getIdentity()
                ));
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    //ACTION FOR DELETE VIDEO
    public function deleteAction() {
        $this->validateRequestMethod('DELETE');

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        //GET VIEWER INFO
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET TAB ID
        $tab_selected_id = $this->_getParam('content_id');




        //GET VIDEO OBJECT
        $siteevent_video = Engine_Api::_()->getItem('siteevent_video', $this->getRequest()->getParam('video_id'));
        if (!$siteevent_video) {
            $this->respondWithError('no_record');
        }

        //GET SITEEVENT SUBJECT
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $siteevent_video->event_id);
        if (!$siteevent) {
            $this->respondWithError('no_record');
        }

        $can_edit = $siteevent_video->canEdit();

        //GET EVENT ID
        $event_id = $siteevent_video->event_id;

        //VIDEO OWNER AND EVENT OWNER CAN DELETE VIDEO
        if ($viewer_id != $siteevent_video->owner_id && $can_edit != 1) {
            $this->respondWithError('unauthorized');
        }

        $db = $siteevent_video->getTable()->getAdapter();
        $db->beginTransaction();

        try {

            Engine_Api::_()->getDbtable('videoratings', 'siteevent')->delete(array('videorating_id =?' => $this->getRequest()->getParam('video_id')));

            $siteevent_video->delete();

            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Return the "Edit Video" FORM AND HANDLE THE FORM POST ALSO.
     * 
     * @return array
     */
    public function editAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $video = $subject = Engine_Api::_()->core()->getSubject('siteevent_video');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($subject))
            $this->respondWithError('no_record');

        if ($viewer->getIdentity() != $video->owner_id && !$this->_helper->requireAuth()->setAuthParams($video, null, 'edit')->isValid())
            $this->respondWithError('unauthorized');

        // FIND OUT THE AUTH COMMENT AND AOUTH VIEW VALUE.
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        // CHECK VIDEO FORM POST OR NOT YET.
        if ($this->getRequest()->isGet()) {
            /* RETURN THE VIDEO EDIT FORM IN THE FOLLOWING CASES:      
             * - IF THERE ARE GET METHOD AVAILABLE.
             * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
             */

            // IF THERE ARE NO FORM POST YET THEN RETURN THE VIDEO FORM.
            $form = Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->getVideoCreateForm($subject);
            $formValues = $subject->toArray();

            foreach ($roles as $role) {
                if ($auth->isAllowed($subject, $role, 'view'))
                    $formValues['auth_view'] = $role;

                if ($auth->isAllowed($subject, $role, 'comment'))
                    $formValues['auth_comment'] = $role;
            }

            // SET THE TAGS  
            $tagStr = '';
            foreach ($subject->tags()->getTagMaps() as $tagMap) {
                $tag = $tagMap->getTag();
                if (!isset($tag->text))
                    continue;
                if ('' !== $tagStr)
                    $tagStr .= ', ';
                $tagStr .= $tag->text;
            }
            $formValues['tags'] = $tagStr;

            $this->respondWithSuccess(array(
                'form' => $form,
                'formValues' => $formValues
            ));
        } else if ($this->getRequest()->isPut()) {
            /* UPDATE THE VIDEO INFORMATION IN THE FOLLOWING CASES:  
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            // CONVERT POST DATA INTO THE ARRAY.
            $data = $values = array();
            foreach ($roles as $role) {
                if ($auth->isAllowed($subject, $role, 'view'))
                    $values['auth_view'] = $role;

                if ($auth->isAllowed($subject, $role, 'comment'))
                    $values['auth_comment'] = $role;
            }

            // SET THE TAGS  
            $tagStr = '';
            foreach ($subject->tags()->getTagMaps() as $tagMap) {
                $tag = $tagMap->getTag();
                if (!isset($tag->text))
                    continue;
                if ('' !== $tagStr)
                    $tagStr .= ', ';
                $tagStr .= $tag->text;
            }
            $values['tags'] = $tagStr;
            $values = $subject->toArray();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'video')->getForm();
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $data = $values;

            // START FORM VALIDATION
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'siteevent')->getVideoCreateFormValidators($subject);
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            // Process
            $db = Engine_Api::_()->getDbtable('videos', 'siteevent')->getAdapter();
            $db->beginTransaction();
            try {
                $video->setFromArray($values);
                $video->save();

                // CREATE AUTH STUFF HERE
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                if ($values['auth_view'])
                    $auth_view = $values['auth_view'];
                else
                    $auth_view = "everyone";
                $viewMax = array_search($auth_view, $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
                }

                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                if ($values['auth_comment'])
                    $auth_comment = $values['auth_comment'];
                else
                    $auth_comment = "everyone";
                $commentMax = array_search($auth_comment, $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
                }

                // Add tags
                $tags = preg_split('/[,]+/', $values['tags']);
                $video->tags()->setTagMaps($viewer, $tags);

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }

            $db->beginTransaction();
            try {
                // Rebuild privacy
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                foreach ($actionTable->getActionsByObject($video) as $action) {
                    $actionTable->resetActivityBindings($action);
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
            $this->successResponseNoContent('no_content', true);
        }
    }

    //ACTION FOR VIEW VIDEO
    public function viewAction() {

        $this->validateRequestMethod();

        //GET VIEWER INFO
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //IF SITEEVENTVIDEO SUBJECT IS NOT THEN RETURN
        if (!$this->_helper->requireSubject('siteevent_video')->isValid())
            $this->respondWithError('no_record');

        //GET VIDEO ITEM
        $siteevent_video = Engine_Api::_()->getItem('siteevent_video', $this->getRequest()->getParam('video_id'));

        //GET SITEEVENT ITEM
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $siteevent_video->event_id);

        if (!$siteevent) {
            $this->respondWithError('no_record');
        }

        //CHECKING THE USER HAVE THE PERMISSION TO VIEW THE VIDEO OR NOT
        if ($viewer_id != $siteevent_video->owner_id && $can_edit != 1 && ($siteevent_video->search != 1 || $siteevent_video->status != 1)) {
            $this->respondWithError('unauthorized');
        }
        if ($this->getRequestParam('menu', true))
            $params['gutterMenu'] = $this->_gutterMenus($siteevent_video, $siteevent);

        $params['response'] = $siteevent_video->toArray();

        //GET EVENT CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'siteevent');

        $category_id = $siteevent->category_id;
        if (!empty($category_id)) {

            $params['response']['categoryname'] = Engine_Api::_()->getItem('siteevent_category', $category_id)->getCategorySlug();

            $subcategory_id = $siteevent->subcategory_id;

            if (!empty($subcategory_id)) {

                $params['response']['subcategoryname'] = Engine_Api::_()->getItem('siteevent_category', $subcategory_id)->getCategorySlug();

                $subsubcategory_id = $siteevent->subsubcategory_id;

                if (!empty($subsubcategory_id)) {

                    $params['response']['subsubcategoryname'] = Engine_Api::_()->getItem('siteevent_category', $subsubcategory_id)->getCategorySlug();
                }
            }
        }

        $params['response']['location'] = $siteevent->location;

        // Add images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($siteevent_video);
        if (!empty($getContentImages))
            $params['response'] = array_merge($params['response'], $getContentImages);

        //contentURL
        $contentURL = Engine_Api::_()->getApi('Core', 'siteapi')->getContentURL($siteevent_video);
        if (!empty($contentURL))
            $params['response'] = array_merge($params['response'], $contentURL);

        $params['response']['event_type_title'] = 'Events';

        $params['response']['event_title'] = $siteevent->getTitle();

        // Add owner images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($siteevent_video, true);
        $params['response'] = array_merge($params['response'], $getContentImages);

        $params['response']["owner_title"] = $siteevent_video->getOwner()->getTitle();

        $videoTags = $siteevent_video->tags()->getTagMaps();
        if (!empty($videoTags)) {
            foreach ($videoTags as $tag) {
                $tagArray[$tag->getTag()->tag_id] = $tag->getTag()->text;
            }

            $params['response']['tags'] = $tagArray;
        }

        // Check if edit/delete is allowed
        $params['response']['can_edit'] = $can_edit = $this->_helper->requireAuth()->setAuthParams($video, null, 'edit')->checkRequire();
        $params['response']['can_delete'] = $can_delete = $this->_helper->requireAuth()->setAuthParams($video, null, 'delete')->checkRequire();

        // check if embedding is allowed
        $can_embed = true;
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('video.embeds', 1)) {
            $can_embed = false;
        } else if (isset($siteevent_video->allow_embed) && !$siteevent_video->allow_embed) {
            $can_embed = false;
        }
        $params['response']['can_embed'] = $can_embed;

        // increment count
        $embedded = "";
        if ($video->status == 1) {
            if (!$video->isOwner($viewer)) {
                $video->view_count++;
                $video->save();
            }
        }

        $params['response']['rating_count'] = Engine_Api::_()->video()->ratingCount($siteevent_video->getIdentity());

        $params['response']['rated'] = Engine_Api::_()->video()->checkRated($siteevent_video->getIdentity(), $viewer->getIdentity());

        $params['response']['videoEmbedded'] = $embedded;


        $params['response']['video_url'] = Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->getVideoURL($siteevent_video);


        $this->respondWithSuccess($params);
    }

    public function rateAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (($video_id = $this->getRequestParam('video_id')) && empty($video_id)) {
            $this->respondWithValidationError("parameter_missing", "video_id");
        }

        if (($rating = $this->getRequestParam('rating')) && empty($rating)) {
            $this->respondWithValidationError("parameter_missing", "rating");
        }

        $table = Engine_Api::_()->getDbtable('videoratings', 'siteevent');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            Engine_Api::_()->video()->setRating($video_id, $viewer_id, $rating);

            $video = Engine_Api::_()->getItem('siteevent_video', $video_id);
            $video->rating = Engine_Api::_()->video()->getRating($video->getIdentity());
            $video->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }

        $total = Engine_Api::_()->video()->ratingCount($video->getIdentity());

        $this->respondWithSuccess(array(
            "rating_count" => $total
        ));
    }

    /**
     * Get helper method
     *
     * @return array
     */
    private function _extractCode($url, $type) {
        switch ($type) {
            //youtube
            case "1":
                // change new youtube URL to old one
                $new_code = @pathinfo($url);
                $url = preg_replace("/#!/", "?", $url);

                // get v variable from the url
                $arr = array();
                $arr = @parse_url($url);
                if ($arr['host'] === 'youtu.be') {
                    $data = explode("?", $new_code['basename']);
                    $code = $data[0];
                } else {
                    $parameters = $arr["query"];
                    parse_str($parameters, $data);
                    $code = $data['v'];
                    if ($code == "") {
                        $code = $new_code['basename'];
                    }
                }
                return $code;
            //vimeo
            case "2":
                // get the first variable after slash
                $code = @pathinfo($url);
                return $code['basename'];
        }
    }

    /**
     * Check YouTube videos exist or not.
     *
     * @return array
     */
    private function _checkYouTube($code) {
        $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
        if (!$data = @file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=id&id=' . $code . '&key=' . $key))
            return false;

        $data = Zend_Json::decode($data);
        if (empty($data['items']))
            return false;
        return true;
    }

    /**
     * Check Vimeo videos exist or not.
     *
     * @return array
     */
    private function _checkVimeo($code) {
        $data = @simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
        $id = count($data->video->id);
        if ($id == 0)
            return false;
        return true;
    }

    /**
     * Handle thumbnail
     *
     * @return array
     */
    private function _handleThumbnail($type, $code = null) {
        switch ($type) {
            //youtube
            case "1":
                //https://i.ytimg.com/vi/Y75eFjjgAEc/default.jpg
                return "https://i.ytimg.com/vi/$code/default.jpg";
            //vimeo
            case "2":
                //thumbnail_medium
                $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
                $thumbnail = $data->video->thumbnail_medium;
                return $thumbnail;
        }
    }

    /**
     * Retrieves information and returns title and description.
     *
     * @return array
     */
    //FUNCTION FOR RETREVES INFORMATION AND RETURES TITLE AND DESCRIPTION
    private function _handleInformation($type, $code) {
        switch ($type) {

            //YOUTUBE
            case "1":
                $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
                $data = file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=' . $code . '&key=' . $key);
                if (empty($data)) {
                    return;
                }
                $data = Zend_Json::decode($data);
                $information = array();
                $youtube_video = $data['items'][0];
                $information['title'] = $youtube_video['snippet']['title'];
                $information['description'] = $youtube_video['snippet']['description'];
                $information['duration'] = Engine_Date::convertISO8601IntoSeconds($youtube_video['contentDetails']['duration']);
                return $information;

            //VIMEO
            case "2":
                //MEDIUM THUMBNAIL
                $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
                $thumbnail = $data->video->thumbnail_medium;
                $information = array();
                $information['title'] = $data->video->title;
                $information['description'] = $data->video->description;
                $information['duration'] = $data->video->duration;
                //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
                return $information;
        }
    }

    /**
     * Upload the video in case of YouTube and Vimeo
     *
     * @return array
     */
    private function _composeUploadAction($values) {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$viewer->getIdentity()) {
            $this->_forward('throw-error', 'index', 'video', array(
                "error_code" => "unauthorized"
            ));
            return;
        }


        $values['user_id'] = $viewer->getIdentity();
        $video_type = $this->_getParam('type');
        $video_title = $this->_getParam('title');
        $video_url = $this->_getParam('url');


        $code = $this->_extractCode($video_url, $video_type);

        // check if code is valid
        // check which API should be used
        if ($values['type'] == 1) {
            $valid = $this->_checkYouTube($code);
            if (empty($valid)) {
                $this->_forward('throw-error', 'index', 'siteevent', array(
                    "error_code" => "youtube_validation_fail"
                ));
                return;
            }
        }

        if ($values['type'] == 2) {
            $valid = $this->_checkVimeo($code);
            if (empty($valid)) {
                $this->_forward('throw-error', 'index', 'siteevent', array(
                    "error_code" => "vimeo_validation_fail"
                ));
                return;
            }
        }

        if (!empty($valid)) {
            $db = Engine_Api::_()->getDbtable('videos', 'siteevent')->getAdapter();
            $db->beginTransaction();

            try {

                $information = $this->_handleInformation($video_type, $code);

                // create video
                $table = Engine_Api::_()->getDbtable('videos', 'siteevent');
                $video = $table->createRow();
                $video['event_id'] = $values['event_id'];
                $video['title'] = $video_title;
                $video['description'] = $information['description'];
                $video['duration'] = $information['duration'];
                $video['owner_id'] = $viewer->getIdentity();
                $video['code'] = $code;
                $video['type'] = $video_type;
                $video->save();
            } catch (Exception $e) {
                $db->rollBack();
                $this->_forward('throw-error', 'index', 'siteevent', array(
                    "error_code" => "internal_server_error",
                    "message" => $e->getMessage()
                ));
                return;
            }
            return $video;
        }

        $this->_forward('throw-error', 'index', 'siteevent', array(
            "error_code" => "video_not_found"
        ));
    }

    private function _uploadVideoAction($values) {
        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->_forward('throw-error', 'index', 'siteevent', array(
                "error_code" => "invalid_file_size"
            ));
            return;
        }

        if (empty($_FILES['filedata'])) {
            $this->_forward('throw-error', 'index', 'siteevent', array(
                "error_code" => "no_record"
            ));
            return;
        }

        if (!isset($_FILES['filedata']) || !is_uploaded_file($_FILES['filedata']['tmp_name'])) {
            $this->_forward('throw-error', 'index', 'siteevent', array(
                "error_code" => "invalid_upload"
            ));
            return;
        }

        $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
        if (in_array(pathinfo($_FILES['filedata']['name'], PATHINFO_EXTENSION), $illegal_extensions)) {
            $this->_forward('throw-error', 'index', 'video', array(
                "error_code" => "invalid_upload"
            ));
            return;
        }

        $db = Engine_Api::_()->getDbtable('videos', 'siteevent')->getAdapter();
        $db->beginTransaction();
        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $params = array(
                'owner_type' => 'user',
                'owner_id' => $viewer->getIdentity()
            );

            $video = Engine_Api::_()->video()->createVideo($params, $_FILES['filedata'], $values);

            // sets up title and owner_id now just incase members switch page as soon as upload is completed
            $video->title = $_FILES['filedata']['name'];
            $video->owner_id = $viewer->getIdentity();
            $video->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }

        return $video;
    }

    /**
     * Get the list of gutter menus list.
     * 
     * @return array
     */
    private function _gutterMenus($subject, $siteevent) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $owner = $subject->getOwner();
        $menus = array();

        // CREATE VIDEO LINK
        if (($viewer->getIdentity() == $owner->getIdentity()) && Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'create')) {
            $menus[] = array(
                'label' => $this->translate('Post New Video'),
                'name' => 'create',
                'url' => 'advancedevents/video/create/' . $siteevent->getIdentity()
            );
        }

        if ($subject->authorization()->isAllowed($viewer, 'edit')) {
            $menus[] = array(
                'label' => $this->translate('Edit Video'),
                'name' => 'edit',
                'url' => 'advancedevents/video/edit/' . $siteevent->getIdentity() . '/' . $subject->getIdentity(),
            );
        }

        if ($subject->authorization()->isAllowed($viewer, 'delete')) {
            $menus[] = array(
                'label' => $this->translate('Delete Video'),
                'name' => 'delete',
                'url' => 'advancedevents/video/delete/' . $siteevent->getIdentity() . '/' . $subject->getIdentity(),
            );
        }

        $menus[] = array(
            'label' => $this->translate('Share'),
            'name' => 'share',
            'url' => 'activity/share',
            'urlParams' => array(
                "type" => $subject->getType(),
                "id" => $subject->getIdentity()
            )
        );

        if (($viewer->getIdentity() != $owner->getIdentity())) {
            $menus[] = array(
                'label' => $this->translate('Report'),
                'name' => 'report',
                'url' => 'report/create/subject/' . $subject->getGuid(),
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }

        return $menus;
    }

}
