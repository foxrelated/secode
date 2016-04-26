<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AlbumController.php 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Nestedcomment_AlbumController extends Core_Controller_Action_Standard {

    public function init() {
        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid())
            return;


        if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
            if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
                    null !== ($photo = Engine_Api::_()->getItem('advalbum_photo', $photo_id))) {
                Engine_Api::_()->core()->setSubject($photo);
            } else if (0 !== ($album_id = (int) $this->_getParam('album_id')) &&
                    null !== ($album = Engine_Api::_()->getItem('advalbum', $album_id))) {
                Engine_Api::_()->core()->setSubject($album);
            }
        } else {
            if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
                    null !== ($photo = Engine_Api::_()->getItem('album_photo', $photo_id))) {
                Engine_Api::_()->core()->setSubject($photo);
            } else if (0 !== ($album_id = (int) $this->_getParam('album_id')) &&
                    null !== ($album = Engine_Api::_()->getItem('album', $album_id))) {
                Engine_Api::_()->core()->setSubject($album);
            }
        }
    }

    public function composeUploadAction() {
        if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
            $this->_redirect('login');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
            return;
        }

        if (empty($_FILES['Filedata'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }

        $nestedcomment_albumcomposeuploade = Zend_Registry::isRegistered('nestedcomment_albumcomposeuploade') ? Zend_Registry::get('nestedcomment_albumcomposeuploade') : null;
        if (empty($nestedcomment_albumcomposeuploade))
            return;

        // Get album
        $viewer = Engine_Api::_()->user()->getViewer();
        $table = Engine_Api::_()->getDbtable('albums', 'album');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $type = $this->_getParam('type', 'comment');

            if (empty($type))
                $type = 'comment';

            $album = $this->getSpecialAlbum($viewer, $type);

            if (Engine_Api::_()->hasModuleBootstrap('sitealbum')) {
                $photoTable = Engine_Api::_()->getDbtable('photos', 'sitealbum');
            } else {
                $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
            }
            $photo = $photoTable->createRow();
            $photo->setFromArray(array(
                'owner_type' => 'user',
                'owner_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
            ));
            $photo->save();
            $photo->setPhoto($_FILES['Filedata']);
            $photo->order = $photo->photo_id;
            $photo->album_id = $album->album_id;
            $photo->save();

            if (!$album->photo_id) {
                $album->photo_id = $photo->getIdentity();
                $album->save();
            }

            // Authorizations
            $auth = Engine_Api::_()->authorization()->context;
            $auth->setAllowed($photo, 'everyone', 'view', true);
            $auth->setAllowed($photo, 'everyone', 'comment', true);

            $db->commit();

            $this->view->status = true;
            $this->view->photo_id = $photo->photo_id;
            $this->view->album_id = $album->album_id;
            $this->view->src = $photo->getPhotoUrl();
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Photo saved successfully');
        } catch (Exception $e) {
            $db->rollBack();
            //throw $e;
            $this->view->status = false;
        }
    }

    public function getSpecialAlbum(User_Model_User $user, $type) {
        if (!in_array($type, array('comment'))) {
            throw new Album_Model_Exception('Unknown special album type');
        }

        $nestedcomment_albumgetspecial = Zend_Registry::isRegistered('nestedcomment_albumgetspecial') ? Zend_Registry::get('nestedcomment_albumgetspecial') : null;
        if (empty($nestedcomment_albumgetspecial))
            return;

        $table = Engine_Api::_()->getDbtable('albums', 'album');
        $select = $table->select()
                ->where('owner_type = ?', $user->getType())
                ->where('owner_id = ?', $user->getIdentity())
                ->where('type = ?', $type)
                ->order('album_id ASC')
                ->limit(1);

        $album = $table->fetchRow($select);

        // Create wall photos album if it doesn't exist yet
        if (null === $album) {
            $translate = Zend_Registry::get('Zend_Translate');
            $album = $table->createRow();
            $album->owner_type = 'user';
            $album->owner_id = $user->getIdentity();
            $album->title = $translate->_(ucfirst(str_replace("_", " ", $type)) . ' Photos');
            $album->type = $type;
            $album->search = 1;
            $album->save();

            // Authorizations
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            foreach ($roles as $i => $role) {
                $auth->setAllowed($album, $role, 'view', true);
                $auth->setAllowed($album, $role, 'comment', true);
            }
        }

        return $album;
    }

}
