<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    PhotoController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupalbum_PhotoController extends Siteapi_Controller_Action_Standard {
    /*
     * Siteenablealbum enable checks and getting subject     
     *
     */

    public function init() {
        // Sitegroupalbum enable check
        $sitegroupalbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
        if (!$sitegroupalbumEnabled) {
            $this->respondWithError('unauthorized');
        }

        // Get subject
        if (!Engine_Api::_()->core()->hasSubject()) {
            if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
                    null !== ($photo = Engine_Api::_()->getItem('sitegroup_photo', $photo_id))) {
                Engine_Api::_()->core()->setSubject($photo);
            } else if (0 !== ($group_id = (int) $this->_getParam('group_id')) &&
                    null !== ($sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id))) {
                Engine_Api::_()->core()->setSubject($sitegroup);
            }
        }

        // Get group id
        $group_id = $this->_getParam('group_id');

        // Package based privacy start 
        if (isset($group_id) && !empty($group_id)) {
            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
            if (isset($sitegroup) && !empty($sitegroup)) {
                if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                    if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupalbum")) {
                        $this->respondWithError('unauthorized');
                    }
                } else {
                    $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'spcreate');
                    if (empty($isGroupOwnerAllow)) {
                        $this->respondWithError('unauthorized');
                    }
                }
            }
        }
        // Package based privacy end
        else {
            if (Engine_Api::_()->core()->hasSubject() != null) {
                $photo = Engine_Api::_()->core()->getSubject();
                $album = $photo->getCollection();
                $group_id = $album->group_id;
            }
        }
    }

    /*
     * Returns the albums of Directory group
     *
     */

    public function indexAction() {

        // Validate request method
        $this->validateRequestMethod();
        // Check subject
        if (!Engine_Api::_()->core()->hasSubject()) {
            $this->respondWithError('no_record');
        }

        $albums_per_group = $this->_getParam('itemCount', 10);

        // Get viewer 
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        // Get sitegroup subject
        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitegroup_group') {
            $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
        } else {
            $sitegroup = Engine_Api::_()->core()->getSubject()->getParent();
        }

        // Total albums
        $albumCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroup', 'albums');
        $photoCreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'spcreate');

        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            $canEdit = 0;
        } else {
            $canEdit = 1;
        }

        if (empty($photoCreate) && empty($albumCount) && empty($canEdit)) {
            $this->respondWithError('unauthorized');
        }

        $albumresponse = array();
        $photosresponse = array();
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'spcreate');
        if ($isManageAdmin || $canEdit) {
            $allowed_upload_photo = 1;
        } else {
            $allowed_upload_photo = 0;
        }

        // Albums order
        $albums_order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.albumsorder', 1);

        // Get current group number of album
        $currentAlbumGroupNumbers = $this->_getParam('group', 1);

        // Set album params
        $paramsAlbum = array();
        $paramsAlbum['group_id'] = $sitegroup->group_id;

        // Get album count
        $album_count = Engine_Api::_()->getDbtable('albums', 'sitegroup')->getAlbumsCount($paramsAlbum);
        $albumresponse['totalItemCount'] = $album_count;

        // Start album pagination
        $groups_vars = Engine_Api::_()->sitegroup()->makeGroup($album_count, $albums_per_group, $currentAlbumGroupNumbers);
        $groups_array = Array();
        for ($y = 0; $y <= $groups_vars[2] - 1; $y++) {
            if ($y + 1 == $groups_vars[1]) {
                $links = "1";
            } else {
                $links = "0";
            }
            $groups_array[$y] = Array('groups' => $y + 1,
                'links' => $links);
        }
        $maxgroups = $groups_vars[2];
        $pstarts = 1;
        // End album pagination
        // Set album params
        $paramsAlbum['start'] = $albums_per_group;
        $paramsAlbum['end'] = $groups_vars[0];
        if (empty($albums_order)) {
            $paramsAlbum['orderby'] = 'album_id ASC';
        } else {
            $paramsAlbum['orderby'] = 'album_id DESC';
        }
        $paramsAlbum['getSpecialField'] = 0;

        $fetchAlbums = Engine_Api::_()->getDbtable('albums', 'sitegroup')->getAlbums($paramsAlbum);

        $albumsData = array();
        if (!empty($fetchAlbums)) {
            foreach ($fetchAlbums as $album) {
                $albumsData = $album->toArray();
                $albumsData = array_merge($albumsData, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($album, false));
                $albumsData["allow_to_view"] = 1;
                $group_id = $album->group_id;
                $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
                $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'spcreate');
                if (empty($isManageAdmin)) {
                    $canCreatePhoto = 0;
                } else {
                    $canCreatePhoto = 1;
                }

                if ($canCreatePhoto == 1 && (Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup) || $album->default_value == 1)) {
                    $upload_photo = 1;
                }
                $albumsData["canUpload"] = empty($upload_photo) ? 0 : 1;
                $albumsData["photo_count"] = $album->count();
                $albumresponse['response'][] = $albumsData;
            }
        }


        $this->respondWithSuccess($albumresponse, true);
    }

    /**
     * Returns the contents of the album (photos)
     * 
     * 
     */
    public function viewalbumAction() {

        // Get sitegroup and album
        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitegroup_group') {
            $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
        } else {
            $sitegroup = Engine_Api::_()->core()->getSubject()->getParent();
        }

        $album_id = $this->_getParam('album_id', 0);
        $viewer = Engine_Api::_()->user()->getViewer();

        // Albums order
        $albums_order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.albumsorder', 1);

        $photoCreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'spcreate');

        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
        if (empty($isManageAdmin)) {
            $canView = 0;
        } else {
            $canView = 1;
        }

        if (empty($canView)) {
            $this->respondWithError('unauthorized');
        }

        $album = Engine_Api::_()->getItem('sitegroup_album', $album_id);
        $photos_per_group = $this->_getParam('itemCount_photo', 100);

        $paramsPhoto = array();
        $paramsPhoto['group_id'] = $group_id = $sitegroup->group_id;
        $paramsPhoto['album_id'] = $album_id;

        $total_photo = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotosCount($paramsPhoto);
        $currentGroupNumbers = $this->_getParam('group', 1);

        // Start photos pagination
        $group_vars = Engine_Api::_()->sitegroup()->makeGroup($total_photo, $photos_per_group, $currentGroupNumbers);
        $group_array = Array();
        for ($x = 0; $x <= $group_vars[2] - 1; $x++) {
            if ($x + 1 == $group_vars[1]) {
                $link = "1";
            } else {
                $link = "0";
            }
            $group_array[$x] = Array('group' => $x + 1,
                'link' => $link);
        }
        $paramsPhoto['start'] = $photos_per_group;
        $paramsPhoto['end'] = $group_vars[0];
        if (empty($albums_order)) {
            $paramsPhoto['photosorder'] = 'album_id ASC';
        } else {
            $paramsPhoto['photosorder'] = 'album_id DESC';
        }
        try {

            $paginator = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotos($paramsPhoto);
            $photos = array();

            $response = array();
            $response['album'] = $album->toArray();

            if ($viewer->getIdentity())
                $response['canUpload'] = $album->authorization()->isAllowed(null, 'photo');

            // Add images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($album);
            $response['album'] = array_merge($response['album'], $getContentImages);

            // Add images
            if (isset($album->owner_id) && !empty($album->owner_id)) {
                // Add owner images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($album, true);
                $response['album'] = array_merge($response['album'], $getContentImages);
                $response['album']["owner_title"] = $album->getOwner()->getTitle();
            } else {
                $response['album']["owner_title"] = "";
            }

            // Getting viewer like or not to content.
            $response['album']["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($album);

            // Getting like count.
            $response['album']["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($album);

            $canEdit = 0;
            if ($viewer->getIdentity()) {
                if (isset($viewer->level_id) && $viewer->level_id == 1)
                    $canEdit = 1;

                if ($viewer->getIdentity() == $album->getOwner()->getIdentity())
                    $canEdit = 1;
            }

            $response["canEdit"] = $canEdit;

            foreach ($paginator as $photo) {
                $tempAlbumPhoto = $photo->toArray();

                // Getting viewer like or not to content.
                $tempAlbumPhoto["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($photo);

                // Getting like count.
                $tempAlbumPhoto["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($photo);

                // Add owner images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo);
                $tempAlbumPhoto = array_merge($tempAlbumPhoto, $getContentImages);

                if ($viewer->getIdentity()) {
                    $menu = array();
                    if (!empty($canEdit)) {
                        $menu[] = array(
                            'label' => $this->translate("Edit Photo"),
                            'name' => 'edit',
                            'url' => 'advancedgroups/photos/editphoto/' . $group_id . '/' . $album_id . '/' . $photo_id,
                        );
                        $menu[] = array(
                            'label' => $this->translate("Delete Photo"),
                            'name' => 'delete',
                            'url' => 'advancedgroups/photos/deletephoto/' . $group_id . '/' . $album_id . '/' . $photo_id,
                        );
                    }


                    $menu[] = array(
                        'label' => $this->translate('Share'),
                        'name' => 'share',
                        'url' => 'activity/share',
                        'urlParams' => array(
                            "type" => $photo->getType(),
                            "id" => $photo->getIdentity()
                        )
                    );

                    $menu[] = array(
                        'label' => $this->translate('Report'),
                        'name' => 'report',
                        'url' => 'report/create/subject/' . $photo->getGuid(),
                        'urlParams' => array(
                            "type" => $photo->getType(),
                            "id" => $photo->getIdentity()
                        )
                    );

                    $menu[] = array(
                        'label' => $this->translate('Make Profile Photo'),
                        'name' => 'make_profile_photo',
                        'url' => 'members/edit/external-photo',
                        'urlParams' => array(
                            "photo" => $photo->getGuid()
                        )
                    );

                    $tempAlbumPhoto['menu'] = $menu;
                }

                $photos[] = $tempAlbumPhoto;
            }

            $response['albumPhotos'] = $photos;
            $response['gutterMenus'] = $this->_albumGutterMenus();
            $response['totalPhotoCount'] = count($paginator);
            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * Delete album
     *
     * @return array
     */
    public function deletealbumAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        $album_id = $this->_getParam("album_id");
        if (!Engine_Api::_()->authorization()->isAllowed('sitegroup_album', $viewer, 'delete'))
            $this->respondWithError('unauthorized');

        $album = Engine_Api::_()->getItem('sitegroup_album', $album_id);

        if (!$album)
            $this->respondWithError('no_record');

        $db = $album->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $album->delete();
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    public function editalbumAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // Get sitegroup and album
        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitegroup_group') {
            $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
        } else {
            $sitegroup = Engine_Api::_()->core()->getSubject()->getParent();
        }

        $ownerList = $sitegroup->getGroupOwnerList();

        $album_id = $this->_getParam("album_id");
        $photoCreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'spcreate');

        //    //START MANAGE-ADMIN CHECK
        //    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
        //    if (empty($isManageAdmin)) {
        //      return $this->setNoRender();
        //    }

        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            $canEdit = 0;
        } else {
            $canEdit = 1;
        }

        if (empty($photoCreate) && empty($canEdit)) {
            $this->respondWithError('unauthorized');
        }

        $album = Engine_Api::_()->getItem('sitegroup_album', $album_id);

        if (!$album)
            $this->respondWithError('no_record');

        if ($this->getRequest()->isGet()) {
            $editForm = array();
            $editForm[] = array(
                'title' => $this->translate("Edit Title"),
                'name' => 'title',
                'value' => $album->title,
                'hasValidator' => true
            );
            // Privacy
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manageadmin', 1))
                $ownerTitle = "Group Admins";
            else
                $ownerTitle = "Just Me";

            $user = Engine_Api::_()->user()->getViewer();
            $availableLabels = array(
                'registered' => 'All Registered Members',
                'owner_network' => 'Friends and Networks',
                'owner_member_member' => 'Friends of Friends',
                'owner_member' => 'Friends Only',
                'like_member' => 'Who Liked This Group',
            );

            $allowMemberInthisPackage = false;
            $allowMemberInthisPackage = Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupmember");
            $sitegroupMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
            if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                $availableLabels['member'] = 'Group Members Only';
            } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                $availableLabels['member'] = 'Group Members Only';
            }

            $availableLabels['owner'] = $ownerTitle;



            $tagOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_album', $user, 'auth_tag');

            $tagOptions = array_intersect_key($availableLabels, array_flip($tagOptions));
            if (count($tagOptions) > 1) {
                $editForm[] = array(
                    'type' => 'select',
                    'name' => 'auth_tag',
                    'label' => 'Tag Post Privacy',
                    'description' => 'Who may tag photos in this album?',
                    'multiOptions' => $tagOptions,
                    'value' => key($tagOptions),
                );
            } else if (count($tagOptions) == 1) {
                $editForm[] = array(
                    'type' => 'select',
                    'name' => 'auth_tag',
                    'label' => 'Tag Post Privacy',
                    'description' => 'Who may tag photos in this album?',
                    'value' => key($tagOptions),
                );
            }
            $editForm[] = array(
                'label' => $this->translate('show this album in search results'),
                'value' => 1,
                'type' => 'checkbox',
                'name' => 'search',
            );
            $editForm[] = array(
                'type' => 'submit',
                'name' => 'submit'
            );
            $response = array();
            $response['form'] = $editForm;
            $this->respondWithSuccess($response, TRUE);
        } elseif ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();

            // Process
            $db = $album->getTable()->getAdapter();
            $db->beginTransaction();
            try {

                // Get form values
                $album->setFromArray($values);
                $album->save();

                // Create suth stuff here
                $auth = Engine_Api::_()->authorization()->context;
                //$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
                if (!empty($sitegroupmemberEnabled)) {
                    $roles = array('owner', 'member', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                } else {
                    $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                }

                // Rebuild privacy
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                foreach ($actionTable->getActionsByObject($album) as $action) {
                    $actionTable->resetActivityBindings($action);
                }

                // Start tag 
                if (!isset($values['auth_tag']) && empty($values['auth_tag'])) {
                    $values['auth_tag'] = key(key($tagOptions));
                    if (empty($values['auth_tag'])) {
                        $values['auth_tag'] = 'registered';
                    }
                }
                $tagMax = array_search($values['auth_tag'], $roles);
                foreach ($roles as $i => $role) {
                    if ($role === 'like_member') {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
                }
                // Commit
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    public function albumfeaturedAction() {
        $album_id = $this->_getParam("album_id");
        $album = Engine_Api::_()->getItem('sitegroup_album', $album_id);

        if (!$album)
            $this->respondWithError('no_record');

        $album->featured = !$album->featured;
        $album->save();
        $this->successResponseNoContent('no_content', true);
    }

    /*
     * Adding album of the day
     *
     *
     */

    public function addalbumofdayAction() {

        // Form generation
        $album_id = $this->_getParam('album_id');

        // Check post
        if ($this->getRequest()->isPost()) {

            // Get form values
            $values = $this->_getAllParams();

            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitegroupalbum')->albumOfDayValidators();
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            // Begin transaction
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                // Get item of the day table
                $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitegroup');

                // Fetch result for resource_id
                $select = $dayItemTime->select()->where('resource_id = ?', $album_id)->where('resource_type = ?', 'sitegroup_album');

                $row = $dayItemTime->fetchRow($select);

                if (empty($row)) {
                    $row = $dayItemTime->createRow();
                    $row->resource_id = $album_id;
                }
                $row->start_date = $values["startdate"];
                $row->end_date = $values["enddate"];
                $row->resource_type = 'sitegroup_album';
                $row->save();
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        } else if ($this->getRequest()->isGet()) {

            $responseform = array();
            $responseform[] = array(
                'type' => 'date',
                'name' => 'startdate',
                'title' => $this->translate("Start Date"),
                'description' => $this->translate(" example : 2016-04-27 "),
                'required' => 'true'
            );
            $responseform[] = array(
                'type' => 'date',
                'name' => 'enddate',
                'title' => $this->translate("End Date"),
                'description' => $this->translate(" example : 2016-04-27 "),
                'required' => 'true'
            );
            $responseform[] = array(
                'type' => "submit",
                'name' => "submit",
            );
            $responseData = array();
            $responseData['form'] = $responseform;
            $this->respondWithSuccess($responseData, true);
        }
    }

    /*
     * Returns photo with detail
     *
     */

    public function viewphotoAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $group_id = $this->_getParam('group_id');
        $album_id = $this->_getParam('album_id');
        $photo_id = $this->_getParam('photo_id');

        // Get sitegroup and album
        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitegroup_group') {
            $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
        } else {
            $sitegroup = Engine_Api::_()->core()->getSubject()->getParent();
        }

        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitegroup_photo') {
            $photo = Engine_Api::_()->core()->getSubject('sitegroup_photo');
        } elseif ($this->_getParam('photo_id')) {
            $photo_id = $this->_getParam('photo_id');
            $photo = Engine_Api::_()->getItem('sitegroup_photo', $this->_getParam('photo_id'));
        } else
            $this->respondWithError('validation_fail', "photo_id missing");


        if (!$photo || !$sitegroup)
            $this->respondWithError('no_record');

        $photoData = $photo->toArray();

        $canEdit = 0;
        if ($viewer->getIdentity()) {
            if (isset($viewer->level_id) && $viewer->level_id == 1)
                $canEdit = 1;

            if ($viewer->getIdentity() == $photo->getOwner()->getIdentity())
                $canEdit = 1;
        }

        $response["canEdit"] = $canEdit;

        $filedata = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo, false);
        $table = Engine_Api::_()->getDbtable('files', 'storage');
        $file = $table->getFile($photoData['photo_id'])->toArray();
        $photoData['file_path'] = $filedata;

        if ($viewer->getIdentity()) {
            $menu = array();
            if (!empty($canEdit)) {
                $menu[] = array(
                    'title' => $this->translate("Edit Photo"),
                    'name' => 'edit',
                    'url' => 'advancedgroups/photos/editphoto/' . $group_id . '/' . $album_id . '/' . $photo_id,
                );
                $menu[] = array(
                    'title' => $this->translate("Delete Photo"),
                    'name' => 'delete',
                    'url' => 'advancedgroups/photos/deletephoto/' . $group_id . '/' . $album_id . '/' . $photo_id,
                );
            }

            $menu[] = array(
                'label' => $this->translate('Share'),
                'name' => 'share',
                'url' => 'activity/share',
                'urlParams' => array(
                    "type" => $photo->getType(),
                    "id" => $photo->getIdentity()
                )
            );

            $menu[] = array(
                'label' => $this->translate('Report'),
                'name' => 'report',
                'url' => 'report/create/subject/' . $photo->getGuid(),
                'urlParams' => array(
                    "type" => $photo->getType(),
                    "id" => $photo->getIdentity()
                )
            );

            $menu[] = array(
                'label' => $this->translate('Make Profile Photo'),
                'name' => 'make_profile_photo',
                'url' => 'members/edit/external-photo',
                'urlParams' => array(
                    "photo" => $photo->getGuid()
                )
            );
        }

        $response['photo'] = $photoData;
        $response['gutterMenus'] = $menu;
        $this->respondWithSuccess($response, true);
    }

    /*
     * Edit title and description of a particular photo
     *
     */

    public function editphotoAction() {

        // Getting viewer and group and photo
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitegroup_group') {
            $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
        } else {
            $sitegroup = Engine_Api::_()->core()->getSubject()->getParent();
        }

        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitegroup_photo') {
            $photo = Engine_Api::_()->core()->getSubject('sitegroup_photo');
        } elseif ($this->_getParam('photo_id')) {
            $photo_id = $this->_getParam('photo_id');
            $photo = Engine_Api::_()->getItem('sitegroup_photo', $this->_getParam('photo_id'));
        } else
            $this->respondWithError('validation_fail', "photo_id missing");

        // Checking for permissions 
        $photoCreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'spcreate');

        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            $canEdit = 0;
        } else {
            $canEdit = 1;
        }

        if (empty($photoCreate) && empty($albumCount) && empty($canEdit)) {
            $this->respondWithError('unauthorized');
        }

        if ($this->getRequest()->isGet()) {

            $editForm = array();
           $editForm['form'][] = array(
                'label' => $this->translate('Title'),
                'name' => 'title',
                'type' => 'text',
            );

            $editForm['form'][] = array(
                'label' => $this->translate('Description'),
                'name' => 'description',
                'type' => 'text',
            );

            $editForm['form'][] = array(
                'type' => 'submit',
                'label' => $this->translate('submit'),
                'name' => 'submit'
            );

            if (isset($photo->title))
                $editForm['formValues']['title'] = $photo->title;
            if (isset($photo->description))
                $editForm['formValues']['description'] = $photo->description;


            $this->respondWithSuccess($editForm, true);
        } elseif ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();

            $db = $photo->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                if (isset($values['title']) && !empty($values['title']))
                    $photo->title = $values['title'];

                if (isset($values['description']) && !empty($values['description']))
                    $photo->description = $values['description'];

                $photo->save();
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /*
     * Deletes a photo
     */

    public function deletephotoAction() {

        // Validate request method
        $this->validateRequestMethod('DELETE');

        // Getting viewer and group and photo
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitegroup_group') {
            $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
        } else {
            $sitegroup = Engine_Api::_()->core()->getSubject()->getParent();
        }

        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitegroup_photo') {
            $photo = Engine_Api::_()->core()->getSubject('sitegroup_photo');
        } elseif ($this->_getParam('photo_id')) {
            $photo_id = $this->_getParam('photo_id');
            $photo = Engine_Api::_()->getItem('sitegroup_photo', $this->_getParam('photo_id'));
        } else
            $this->respondWithError('validation_fail', "photo_id missing");


        // Checking for permissions 
        $photoCreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'spcreate');

        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');

        if (empty($isManageAdmin)) {
            $canEdit = 0;
        } else {
            $canEdit = 1;
        }

        if (empty($photoCreate) && empty($albumCount) && empty($canEdit)) {
            $this->respondWithError('unauthorized');
        }
        $db = $photo->getTable()->getAdapter();
        $db->beginTransaction();
        try {
            $photo->delete();
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /*
     * Add photo to album
     *
     */

    public function addphotoAction() {

        $this->validateRequestMethod('POST');

        // Getting viewer and group and photo
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (isset($_FILES) && $this->getRequest()->isPost()) {

            if (empty($viewer_id))
                $this->respondWithError('unauthorized');

            $params = $this->_getAllParams();

            if (Engine_Api::_()->core()->getSubject()->getType() == 'sitegroup_group') {
                $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
            } else {
                $sitegroup = Engine_Api::_()->core()->getSubject()->getParent();
            }

            foreach ($_FILES as $value) {
                Engine_Api::_()->getApi('Siteapi_Core', 'sitegroupalbum')->setPhoto($value, $sitegroup, 1, $params);
            }
            $this->successResponseNoContent('no_content', true);
        }
    }

    /*
     *   Returns menus of the album
     *
     *
     * @return array
     */

    private function _albumGutterMenus() {
        $album_id = $this->_getParam('album_id', 0);
        $group_id = $this->_getParam('group_id', 0);
        $album = Engine_Api::_()->getItem('sitegroup_album', $album_id);
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $gutterMenus = array();

        // Delete an album
        if (Engine_Api::_()->authorization()->isAllowed('sitegroup_album', $viewer, 'delete')) {
            $gutterMenus[] = array(
                'title' => $this->translate("Delete Album"),
                'url' => 'advancedgroups/photos/deletealbum/' . $group_id . '/' . $album_id,
                'name' => 'delete'
            );
        }

        // edit an album
        if (Engine_Api::_()->authorization()->isAllowed('sitegroup_album', $viewer, 'edit')) {
            $gutterMenus[] = array(
                'title' => $this->translate("Edit Album"),
                'url' => 'advancedgroups/photos/editalbum/' . $group_id . '/' . $album_id,
                'name' => 'edit'
            );
        }

        if ($album->featured) {
            $gutterMenus[] = array(
                'title' => $this->translate("Make Album non Featured"),
                'url' => 'advancedgroups/photos/albumfeatured/' . $group_id . '/' . $album_id,
                'name' => 'unfeatured'
            );
        } else {
            $gutterMenus[] = array(
                'title' => $this->translate("Make Featured"),
                'url' => 'advancedgroups/photos/albumfeatured/' . $group_id . '/' . $album_id,
                'name' => 'featured'
            );
        }

        $gutterMenus[] = array(
            'title' => $this->translate("Make Album of the Day"),
            'url' => 'advancedgroups/photos/addalbumofday/' . $group_id . '/' . $album_id,
            'name' => 'albumofday'
        );
        $gutterMenus[] = array(
            'title' => $this->translate("Add Photo"),
            'url' => 'advancedgroups/photos/addphoto/' . $group_id . '/' . $album_id,
            'name' => 'addphoto'
        );
        $gutterMenus[] = array(
            'title' => $this->translate("View Photo"),
            'url' => 'advancedgroups/photos/viewphoto/' . $group_id . '/' . $album_id . '/photo_id',
            'name' => 'viewphoto'
        );

        return $gutterMenus;
    }

}
