<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Facebookse_AdminSettingsController extends Core_Controller_Action_Admin {

    protected $_navigation;

    public function init() {
        $session = new Zend_Session_Namespace();
    }

    public function indexAction() {

        if (!empty($_POST['facebookse_lsettings'])) {
            $_POST['facebookse_lsettings'] = trim($_POST['facebookse_lsettings']);
        }
        $facebookse_form_content = array('fbfriend_siteinvite', 'fbadmin_userid', 'fb_my_facebook_link', 'fb_invite_fbfriend', 'fb_friends_page', 'overwrite_headmeta_active', 'fblanguage_id', 'fblike_type', 'submit', 'scrape_sitepageurl', 'fbapp_namespace');
        include_once APPLICATION_PATH . '/application/modules/Facebookse/controllers/license/license1.php';
        $client_id = Engine_Api::_()->getApi('settings', 'core')->core_facebook_appid;
        $client_secret = Engine_Api::_()->getApi('settings', 'core')->core_facebook_secret;
        if (!empty($client_id) && !empty($client_secret)) {
            //CHECKING IF THE FILLED APP ID AND APP SECRET IS VALID OR NOT IF NOT THEN SHOW ERROR MESSAGE.
            $url = "https://graph.facebook.com/oauth/access_token";
            $postString = "client_id=$client_id&client_secret=$client_secret&type=client_cred";
            $response = @file_get_contents('https://graph.facebook.com/oauth/access_token?' . $postString);
            if (!empty($response)) {
                $response_temp = explode("access_token=", $response);
                if (empty($response_temp[1])) {
                    $response = Zend_Json::decode($response);
                }
            }


            if (empty($response)) {
                $graph_url = 'https://graph.facebook.com/oauth/access_token?' . $postString;
                $ch = curl_init();
                $timeout = 5;
                curl_setopt($ch, CURLOPT_URL, $graph_url);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                ob_start();
                curl_exec($ch);
                curl_close($ch);
                $response = Zend_Json::decode(ob_get_contents());
                ob_end_clean();
            }
            if (is_array($response) && !empty($response['error'])) {
                $facebookAppError = 'The Facebook Application details (App ID or App Secret) entered by you are incorrect. Please click <a href="%s/admin/user/settings/facebook" target= "_blank" >here</a> to enter them correctly.';
                if (!empty($form)) {
                    $facebookAppError = Zend_Registry::get('Zend_Translate')->_($facebookAppError);
                    $facebookAppError = sprintf($facebookAppError, Zend_Controller_Front::getInstance()->getBaseUrl());
                    $form->getDecorator('errors')->setOption('escape', false);
                    if (!empty($facebookAppError)) {
                        $form->addError($facebookAppError);
                    }
                }
            }
        }
        $values = $form->getValues();
        foreach ($values as $key => $value) {
            if ($key == 'fbapp_namespace' && $value) {
                Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
            }
        }
    }

    //THIS FUNCTION IS USED FOR LIKE SETTINGS CREATING AND UPDATING BY ADMIN.
    public function likesettingsAction() {
        $fbLikeButton = Engine_Api::_()->getApi("settings", "core")->getSetting('fblike.type', 'default');
        $this->view->navigation = $this->getNavigation();
        $pagelevel_id = $this->_getParam('level_id', '');
        $this->view->content_type = $pagelevel_id;
        //$this->view->module = $pagelevel_id;
        $this->view->form = $form = new Facebookse_Form_Admin_Likesettings();

        // Make form
        $this->view->subform = $subform = new Facebookse_Form_Admin_Likelayout();
        $form->show_likes($pagelevel_id);
        $subform->show_likes($pagelevel_id);

        //SHOWING THE FORM FILLED IF ACTION IS NOT FOR FORM POST.
        if (!$this->getRequest()->isPost()) {

            //GETTING THE SETTING FROM FACEBOOKSE_LIKES TABLE.
            if (!empty($pagelevel_id)) {

                $mixsettingsTable = Engine_Api::_()->getDbtable('mixsettings', 'facebookse');
                $select = $mixsettingsTable->select()->where('resource_type=?', $pagelevel_id);

                $mixsettingsResults = $mixsettingsTable->fetchRow($select);

                $settings_like = array();
                $this->view->likesetting_showlike = 1;
                $this->view->likesetting_showlike = $settings_like[$mixsettingsResults->resource_type . '_1'] = $mixsettingsResults->enable;

                $settings_like[$pagelevel_id . '_3'] = $mixsettingsResults->like_faces;
                if ($fbLikeButton == 'default') {
                    $settings_like[$pagelevel_id . '_4'] = $mixsettingsResults->send_button;
                    $settings_like[$pagelevel_id . '_5'] = $mixsettingsResults->like_width;
                    $settings_like['likefont'] = $mixsettingsResults->like_font;
                    $settings_like['like_color'] = $mixsettingsResults->like_color;
                    $settings_like['layout_style'] = $mixsettingsResults->layout_style;
                    $settings_like[$pagelevel_id . '_2'] = $mixsettingsResults->like_type;
                } else {
                    $settings_like[$pagelevel_id . '_4'] = $mixsettingsResults->send_button;
                    $settings_like['like_commentbox'] = $mixsettingsResults->like_commentbox;
                    if (!empty($mixsettingsResults->action_type) && $mixsettingsResults->action_type != 'og.likes') {
                        $settings_like['action_type'] = 'custom';
                        $settings_like['actiontype_custom'] = $mixsettingsResults->action_type;
                    } else
                        $settings_like['action_type'] = $mixsettingsResults->action_type;

                    $settings_like['objecttype_custom'] = $mixsettingsResults->object_type;
                    $settings_like['fbbutton_liketext'] = $mixsettingsResults->fbbutton_liketext;
                    $settings_like['fbbutton_unliketext'] = $mixsettingsResults->fbbutton_unliketext;
                    $settings_like['show_customicon'] = $mixsettingsResults->show_customicon;
                    $settings_like['fbbutton_likeicon'] = $mixsettingsResults->fbbutton_likeicon;
                    $settings_like['fbbutton_unlikeicon'] = $mixsettingsResults->fbbutton_unlikeicon;
                }
                $settings_like['pagelevel_id'] = $pagelevel_id;

                $form->populate($settings_like);
            }
        }

        //WHEN USER SUBMIT THE FORM.
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $this->getRequest()->getPost();
            //WE ARE DELETING ALL ROWS FROM BOTH TABLES FACEBOOKSE_LIKE AND FACEBOOKSE_SHARE AND THEN CREATING NEW ROWS ACCORDING TO NEW SETTINGS.

            if (!empty($pagelevel_id)) {
                
                if($pagelevel_id == 'home' && isset($values['facebook_home_url']) && !empty($values['facebook_home_url'])){
                    Engine_Api::_()->getApi('settings', 'core')->setSetting('facebook_home_url', $values['facebook_home_url']);
                }

                $resource_type = $pagelevel_id;
                $pagelevel_id_temp = explode("_", $pagelevel_id);
                $pagelevel_id = $pagelevel_id_temp[0];
                $like_arraycommon = array(
                    'enable' => $values[$resource_type . '_1'],
                    'like_faces' => @$values[$resource_type . '_3'][0],
                );
                if ($fbLikeButton == 'default') {
                    $like_arraydefault = array(
                        'like_type' => $values[$resource_type . '_2'],
                        'send_button' => @$values[$resource_type . '_4'][0],
                        'like_width' => $values[$resource_type . '_5'],
                        'like_font' => $values['likefont'],
                        'like_color' => $values['like_color'],
                        'layout_style' => $values['layout_style'],
                    );
                } else {
                    if ($values['action_type'] == 'custom')
                        $values['action_type'] = $values['actiontype_custom'];
                    $like_arraydefault = array(
                        'action_type' => $values['action_type'],
                        'like_commentbox' => @$values['like_commentbox'][0],
                        'object_type' => $values['objecttype_custom'],
                        'send_button' => @$values[$resource_type . '_4'][0],
                        'fbbutton_liketext' => $values['fbbutton_liketext'],
                        'fbbutton_unliketext' => $values['fbbutton_unliketext'],
                        'show_customicon' => $values['show_customicon'],
                        'fbbutton_likeicon' => $values['fbbutton_likeicon'],
                        'fbbutton_unlikeicon' => $values['fbbutton_unlikeicon'],
                    );
                }
                $like_array = array_merge($like_arraycommon, $like_arraydefault);




                include_once APPLICATION_PATH . '/application/modules/Facebookse/controllers/license/license2.php';
                $this->view->likesetting_showlike = $values[$resource_type . '_1'];
            }
        }
    }

    //THIS FUNCTION FOR CREATING AND UPDATING ENTRY IN FACEBOOKSE_MATAINFO
    public function opengraphAction() {

        $this->view->navigation = $this->getNavigation();
        $pagelevel_id = $this->_getParam('level_id');
        $faceboookse_admin_tab = 'opengraph';

        // Make form
        $this->view->form = $form = new Facebookse_Form_Admin_Opengraph();

        $metainfo = $this->getmetainfo($pagelevel_id, 0);

        //CHECKING IF THE PAGE IS FOR EDIT OR MAKING A NEW ENTRY.
        $imageUrl = '';
        if (!empty($pagelevel_id)) {
            //$metainfo = $this->getmetainfo($pagelevel_id, 0);
            $fbmetainfoTable = $metainfo['fbmetainfoTable'];
            if (!empty($fbmetainfoTable)) {
                $pagelevel_id_temp = explode("_", $fbmetainfoTable->resource_type);

                $imageUrl = Engine_Api::_()->facebookse()->itemphoto($fbmetainfoTable, $pagelevel_id_temp[0]);
            }
        }
        $form->showform($pagelevel_id, $imageUrl);

        //SHOWING PRE-FILLED FORM.
        if (!empty($metainfo['metainfoid'])) {
            $this->view->enable = $fbmetainfoTable->opengraph_enable;
            $settings['pagelevel_id'] = $pagelevel_id;
            $settings['opengraph_enable'] = $fbmetainfoTable->opengraph_enable;
            $settings['title'] = $fbmetainfoTable->title;
            $settings['description'] = $fbmetainfoTable->description;
            $settings['fbadmin_appid'] = $fbmetainfoTable->fbadmin_appid;
            //$settings['fbadmin_userid'] = $fbmetainfoTable->fbadmin_userid;
            if ($pagelevel_id != 'home' && $pagelevel_id != 'blog' && $pagelevel_id != 'ynblog' && $pagelevel_id != 'music' && $pagelevel_id != 'sitepagemusic' && $pagelevel_id != 'sitebusinessmusic' && $pagelevel_id != 'sitegroupmusic') {
                $ogtypes_array = unserialize($fbmetainfoTable->types);
                if (!empty($ogtypes_array)) {
                    foreach ($ogtypes_array as $value) {
                        $ogtype = explode("-", $value);
                        $settings[$ogtype[0] . '_ogtype'] = $ogtype[0] . '-' . $ogtype[1];
                    }
                }
            }
            $form->populate($settings);
        } else {
            if (!empty($pagelevel_id)) {
                $this->view->enable = 0;
                $settings['pagelevel_id'] = $pagelevel_id;
                $settings['opengraph_enable'] = 0;
                $settings['fbadmin_appid'] = 1;

                $form->populate($settings);
            }
        }
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $session->open_graph = 1;
            //$metainfo = $this->getmetainfo($pagelevel_id, 0);
            $fbmetainfoTable = $metainfo['fbmetainfoTable'];
            $temp_values = $form->getValues();

            $values = $this->getRequest()->getPost();
            if (!empty($pagelevel_id)) {
                $resource_type = $pagelevel_id;
                $pagelevel_id_temp = explode("_", $pagelevel_id);
                $pagelevel_id = $pagelevel_id_temp[0];
                if (!empty($values['opengraph_enable'])) {
                    $values['opengraph_enable'] = $values['opengraph_enable'][0];
                } else {
                    $values['opengraph_enable'] = 0;
                }
                if ($values['plugin_type'] == 'grouplike') {
                    $table = Engine_Api::_()->getDbtable('categories', $pagelevel_id);
                    foreach ($table->fetchAll($table->select()) as $row) {
                        if (isset($values[$row['category_id'] . '_ogtype']) && !empty($values[$row['category_id'] . '_ogtype'])) {
                            $serialize_array[] = $values[$row['category_id'] . '_ogtype'];
                        }
                    }

                    if (!empty($serialize_array)) {
                        $ogtype = serialize($serialize_array);
                        $values['types'] = $ogtype;
                    }
                } else if ($values['plugin_type'] == 'bloglike') {
                    if (!empty($values['1_ogtype'])) {
                        $serialize_array[0] = $values['1_ogtype'];
                        $ogtype = serialize($serialize_array);
                        $values['types'] = $ogtype;
                    } else {
                        $values['types'] = '';
                    }
                } else if ($values['plugin_type'] == 'homelike') {
                    $serialize_array[0] = '0-homepage';
                    $ogtype = serialize($serialize_array);
                    $values['types'] = $ogtype;
                } else if ($values['plugin_type'] == 'profilelike') {
                    $serialize_array[0] = '0-profilepage';
                    $ogtype = serialize($serialize_array);
                    $values['types'] = $ogtype;
                } elseif ($values['plugin_type'] == 'other') {
                    $classexists = ucfirst($pagelevel_id) . '_Model_DbTable_Categories';
                    if (class_exists($classexists)) {
                        $table = Engine_Api::_()->getDbtable('categories', $pagelevel_id);
                        foreach ($table->fetchAll($table->select()) as $row) {
                            if ($values[$row['category_id'] . '_ogtype'] != 0) {
                                $serialize_array[] = $values[$row['category_id'] . '_ogtype'];
                            }
                        }
                        if (!empty($serialize_array)) {
                            $ogtype = serialize($serialize_array);
                            $values['types'] = $ogtype;
                        }
                    } else {
                        if (!empty($values['1_ogtype'])) {
                            $serialize_array[0] = $values['1_ogtype'];
                            $ogtype = serialize($serialize_array);
                            $values['types'] = $ogtype;
                        } else {
                            $values['types'] = '';
                        }
                    }
                }

                // Begin database transaction
                if (empty($matainfo_id)) {
                    $db = Engine_Api::_()->getItemtable('facebookse_mixsetting')->getAdapter();
                } else {
                    $db = Engine_Db_Table::getDefaultAdapter();
                }
                $db->beginTransaction();

                try {


                    $opengraph_array = array(
                        //'entity' => $values[$pagelevel_id . '_1'],
                        'opengraph_enable' => $values['opengraph_enable'],
                        'title' => @$values['title'],
                        'description' => @$values['description'],
                        'types' => @$values['types'],
                        'fbadmin_appid' => @$values['fbadmin_appid']
                    );

                    if (!empty($temp_values['ContentPhoto'])) {
                        $opengraph_array ['photo_id'] = $temp_values['ContentPhoto'];
                    }


                    include_once APPLICATION_PATH . '/application/modules/Facebookse/controllers/license/license2.php';
                    // Add the photo
                    if (!empty($temp_values['ContentPhoto'])) {
                        $fileElement = $form->ContentPhoto;
                        $fbmetainfoTable->setPhoto($fileElement);
                        $fbmetainfoTable->save();
                    }
                    //CLEARING THE CACHE AT THE FACEBOOKSIDE WHEN ADMIN DO CHANGES FOR SITE HOME PAGE TITIEL AND DESCRIPTION.
                    if ($values['pagelevel_id'] == 'home') {
                        $siteHomeUrl = ( _ENGINE_SSL ? 'https://' : 'http://' )
                                . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRequest()->getBaseUrl();
                        Engine_Api::_()->facebookse()->scrapeFbAdminPage($siteHomeUrl);
                    }
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
                return $this->_helper->redirector->gotoRoute(array('action' => 'opengraph'));
            }
        }

        //CHECKING IF THE "property" content type  is overwritten in the HeadMeta.php file at: "application/libraries/zend/view/helper/HeadMeta.php" file.
        $this->view->showTip = Engine_Api::_()->facebookse()->checkMetaProperty();

        //CHECK IF FACEBOOK COMMENT BOX IS ENABLED.
        $this->view->enable_contentcommenttype = 0;
        if (!empty($pagelevel_id)) {
            $permissionTable_Comments = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->getMetainfo('', $pagelevel_id);
            if ($permissionTable_Comments) {
                $permissionTable_Comments = $permissionTable_Comments->toArray();
            }
            if (!empty($permissionTable_Comments)) {
                $comment_setting = $permissionTable_Comments['commentbox_enable'];
                $this->view->enable_contentcommenttype = $comment_setting;
            }
        }
    }

    //THIS FUNCTION RETURNS THE FACEBOOKSE_METAINFO TABLE INFO.
    function getmetainfo($pagelevel_id = 0, $lastinsert_id = 0) {
        $metainfo_id = 0;
        if (empty($pagelevel_id)) {
            $metainfo = array('fbmetainfoTable' => '',
                'metainfoid' => ''
            );
            return $metainfo;
        }
        if (empty($lastinsert_id)) {
            $table_post = Engine_Api::_()->getDbtable('mixsettings', 'facebookse');
            $fbmetainfo_tableName = $table_post->info('name');
            $coreTable = Engine_Api::_()->getDbtable('modules', 'core');
            $coreTableName = $coreTable->info('name');
            $select = $table_post->select()
                    ->setIntegrityCheck(false)
                    ->from($fbmetainfo_tableName);

            if ($pagelevel_id != 'home')
                $select->join($coreTableName, "$coreTableName . name = $fbmetainfo_tableName . module")->where($coreTableName . '.enabled = ?', 1);
            $select->where($fbmetainfo_tableName . '.resource_type = ?', $pagelevel_id)
                    ->orwhere($fbmetainfo_tableName . '.module = ?', $pagelevel_id)
                    ->limit(1);

            $fbmetainfoTable = $table_post->fetchRow($select);

            $metainfo_id = $fbmetainfoTable->mixsetting_id;

            if (!empty($metainfo_id)) {

                if (!Engine_Api::_()->core()->hasSubject('facebookse_mixsettings')) {
                    Engine_Api::_()->core()->setSubject($fbmetainfoTable);
                }
            } else {
                $fbmetainfoTable = $table_post->createRow();
                $metainfo_id = 0;
            }
        } else {
            $fbmetainfoTable = Engine_Api::_()->getItem('facebookse_mixsettings', $lastinsert_id);
            $metainfo_id = $lastinsert_id;
        }
        $metainfo = array('fbmetainfoTable' => $fbmetainfoTable,
            'metainfoid' => $metainfo_id
        );
        return $metainfo;
    }

    function getlikecodeAction() {
        $this->_helper->layout->disableLayout();
        $this->view->like_button = $_GET;
    }

    //THIS FUNCTION IS USED TO GENERATE FACEBOOK SHARE BUTTON CODE.
    function getsharecodeAction() {
        $this->_helper->layout->disableLayout();
        if ($_GET['format'] == 'smoothbox') {
            $this->view->share_button = $_GET;
        }
    }

//    function widgetsettingsAction() {
//        $this->view->navigation = $this->getNavigation();
//        $widget_type = $this->_getParam('level_id');
//        $this->view->widget_type = $widget_type;
//        $faceboookse_admin_tab = 'widgetsettings';
//        //Make form
//        $this->view->form = $form = new Facebookse_Form_Admin_Widgetsettings();
//        $form->show_likes($widget_type);
//
//        //SHOWING THE FORM FILLED IF ACTION IS NOT FOR FORM POST.
//        //GETTING THE SETTING FROM FACEBOOKSE_LIKES TABLE.
//        if (!empty($widget_type)) {
//            $base_url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
//            $permissionTable_Like = Engine_Api::_()->getDbtable('widgetsettings', 'facebookse');
//            $select = $permissionTable_Like->select()
//                    ->where('widget_type=?', $widget_type);
//            $permissionTable_Like = $permissionTable_Like->fetchAll($select)->toarray();
//            $form->populate($permissionTable_Like[0]);
//        }
//        //WHEN USER SUBMIT THE FORM.
//        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
//            $session->widget_setting = 1;
//            $fbwidgetinfoTable = Engine_Api::_()->getItem('facebookse_widgetsettings', $permissionTable_Like[0]['widgetsetting_id']);
//            $values = $form->getValues();
//            $fbwidgetinfoTable->setFromArray($values);
//            include_once APPLICATION_PATH . '/application/modules/Facebookse/controllers/license/license2.php';
//
//            //SAVING THE SETTINGS OF BORDER COLOR FIELD IN CORE SETTING TABLE.
//            if ($values['widget_type'] == 'activity_feed' || $values['widget_type'] == 'recommendation') {
//                Engine_Api::_()->getApi('settings', 'core')->setSetting($values['widget_type'] . '_border_color', $values['widget_border_color']);
//            }
//        }
//    }

    public function readmeAction() {
        
    }

    //SHOWING THE PLUGIN RELETED QUESTIONS AND ANSWERS
    public function faqAction() {
        $this->view->navigation = $this->getNavigation();
    }

    //SHOWING THE PREVIEW OF FACEBOOK LIKE BUTTION.
    public function showlikepreviewAction() {
        if ($_GET['format'] == 'smoothbox') {
            $this->view->like_button = $_GET;
        }
    }

    //SHOWING THE PREVIEW OF FACEBOOK SOCIAL PLUGINS.
    public function showfbsocialpluginpreviewAction() {
        if ($_GET['format'] == 'smoothbox') {
            $this->view->fb_social_pluginpreview = $_GET;
        }
    }

    //INTEGRATING FACEBOOK LIKE WITH SITE CONTENT LIKE.
    public function likeintsettingsAction() {
        $this->view->navigation = $this->getNavigation();
        $this->view->form = $form = new Facebookse_Form_Admin_Sitelikeint();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            include_once APPLICATION_PATH . '/application/modules/Facebookse/controllers/license/license2.php';
        }
    }

    //SHOWING THE FACEBOOK STATISTICS OF SITE URLS.
    public function statisticsAction() {
        $this->view->navigation = $this->getNavigation();
        $this->view->category = $category = $this->_getParam('category', 'general');
        $client_id = Engine_Api::_()->getApi('settings', 'core')->core_facebook_appid;
        $client_secret = Engine_Api::_()->getApi('settings', 'core')->core_facebook_secret;
        $this->view->facebookAppError = '';
        if (!empty($client_id) && !empty($client_secret)) {
            //CHECKING IF THE FILLED APP ID AND APP SECRET IS VALID OR NOT IF NOT THEN SHOW ERROR MESSAGE.
            $url = "https://graph.facebook.com/oauth/access_token";
            $postString = "client_id=$client_id&client_secret=$client_secret&type=client_cred";
            $response = @file_get_contents('https://graph.facebook.com/oauth/access_token?' . $postString);
            if (!empty($response)) {
                $response_temp = explode("access_token=", $response);
                if (empty($response_temp[1])) {
                    $response = Zend_Json::decode($response);
                }
            }
            if (empty($response)) {
                $graph_url = 'https://graph.facebook.com/oauth/access_token?' . $postString;
                $ch = curl_init();
                $timeout = 0;
                curl_setopt($ch, CURLOPT_URL, $graph_url);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                ob_start();
                curl_exec($ch);
                curl_close($ch);
                $response = Zend_Json::decode(ob_get_contents());
                ob_end_clean();
            }

            if (is_array($response) && !empty($response['error'])) {
                $facebookAppError = 'The Facebook Application details (App ID or App Secret) entered by you are incorrect. Please click <a href="%s/admin/user/settings/facebook" target= "_blank" >here</a> to enter them correctly.';


                $facebookAppError = Zend_Registry::get('Zend_Translate')->_($facebookAppError);
                $facebookAppError = sprintf($facebookAppError, Zend_Controller_Front::getInstance()->getBaseUrl());
                $this->view->facebookAppError = $facebookAppError;
            }
        }
        if (!is_array($response)) {
            ///if ($category == 'contentlikes') { //We are not using Facebook Like Statistics feature so we are going to comment this. 03/02/2014.
//				$this->view->formFilter = $formFilter = new Facebookse_Form_Admin_Filter();
//				$tmTable = Engine_Api::_()->getDbtable('statistics', 'facebookse');
//				$tmName = $tmTable->info('name');
//				$search_url = '';
//				$url_string = '';
//				if( $this->getRequest()->isPost() && $formFilter->isValid($this->getRequest()->getPost())) {
//						$value = $formFilter->getValues();
//						if (!empty($value['url'])) {
//							$search_url = $value['url'];
//						}
//					
//					}
//				$selectSiteUrl = $tmTable->select()
//												->setIntegrityCheck(false)
//												->from($tmName, array('url', 'statistic_id', 'content_id', 'resource_type'))
//												->where($tmName . '.url LIKE ?', '%' . $search_url . '%')
//                        ->where($tmName . '.content_id >?', 0)
//                        ->where($tmName . '.resource_type <>?', '')
//												->order('updated DESC');	
//       
//				$this->view->paginator = $paginator = Zend_Paginator::factory($selectSiteUrl);
//				$paginator->setCurrentPageNumber($this->_getParam('page', 1));
//				$paginator->setItemCountPerPage(20);
//				foreach ($paginator as $key => $item) { 
//					$paginator_temp[$key] ['url'] = $item->url;
//          $url = $item->url;
//          if (!empty($item->content_id) && !empty($item->resource_type))
//             $url = $item->url . '?contentid=' . $item->content_id. '&type=' .$item->resource_type;      
//					$xml = $this->getNoOfLikes($item->url);			
//				
//					$paginator_temp[$key] ['likes'] = (string)$xml->link_stat->like_count;
//					
//				}	
//				$this->view->paginator_temp = $paginator_temp;
//			
//			}
//			else {
            //GETTING THE SITE INSIGHTS FROM FACEBOOK.
            $this->view->active_users = $this->getActiveUsers();
            $this->view->formFilter = $formFilter = new Facebookse_Form_Admin_Statisticsgeneral();
            //$this->view->duration = $duration = $this->_getParam('duration', 1);
            $formFilter->duration_id->setValue(24);
            //	}
        }
    }

    public function getNoOfLikes($URL) {

        $call = "https://api.facebook.com/method/fql.query?query=SELECT%20like_count%20FROM%20link_stat%20WHERE%20url='" . urlencode($URL) . "'";
        $rss = simplexml_load_file($call);
        return $rss;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $call);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public function getActiveUsers() {

        $response = $this->getAccessTokenFb();
        $since_day = time() - 1 * 24 * 3600;
        $since_week = time() - 7 * 24 * 3600;
        $since_month = time() - 30 * 24 * 3600;
        $end_time = time();
        $client_id = Engine_Api::_()->getApi('settings', 'core')->core_facebook_appid;

        $likecount_day = Zend_Json::decode(@file_get_contents('https://graph.facebook.com/' . $client_id . '/insights/application_active_users/day?' . $response . '&since=' . $since_day . '&until=' . $end_time));
        if (empty($likecount_day)) {
            $graph_url = 'https://graph.facebook.com/' . $client_id . '/insights/application_active_users/day?' . $response . '&since=' . $since_day . '&until=' . $end_time;
            $ch = curl_init();
            $timeout = 0;
            curl_setopt($ch, CURLOPT_URL, $graph_url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            ob_start();
            curl_exec($ch);
            curl_close($ch);
            $likecount_day = Zend_Json::decode(ob_get_contents());
            ob_end_clean();
        }
        $count_daylikes = 0;
        if (!empty($likecount_day['data'][0]['values'])) {
            $count_daylikes = count($likecount_day['data'][0]['values']);
        }
        if ($count_daylikes >= 1) {
            $count_daylikes = --$count_daylikes;
        }

        $likecount_week = Zend_Json::decode(@file_get_contents('https://graph.facebook.com/' . $client_id . '/insights/application_active_users/week?' . $response . '&since=' . $since_week . '&until=' . $end_time));
        if (empty($likecount_week)) {
            $graph_url = 'https://graph.facebook.com/' . $client_id . '/insights/application_active_users/week?' . $response . '&since=' . $since_week . '&until=' . $end_time;
            $ch = curl_init();
            $timeout = 0;
            curl_setopt($ch, CURLOPT_URL, $graph_url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            ob_start();
            curl_exec($ch);
            curl_close($ch);
            $likecount_week = Zend_Json::decode(ob_get_contents());
            ob_end_clean();
        }
        $count_weeklikes = 0;
        if (!empty($likecount_week['data'][0]['values'])) {
            $count_weeklikes = count($likecount_week['data'][0]['values']);
        }

        if ($count_weeklikes >= 1) {
            $count_weeklikes = --$count_weeklikes;
        }

        $likecount_month = Zend_Json::decode(@file_get_contents('https://graph.facebook.com/' . $client_id . '/insights/application_active_users/month?' . $response . '&since=' . $since_month . '&until=' . $end_time));
        if (empty($likecount_month)) {
            $graph_url = 'https://graph.facebook.com/' . $client_id . '/insights/application_active_users/month?' . $response . '&since=' . $since_month . '&until=' . $end_time;
            $ch = curl_init();
            $timeout = 0;
            curl_setopt($ch, CURLOPT_URL, $graph_url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            ob_start();
            curl_exec($ch);
            curl_close($ch);
            $likecount_month = Zend_Json::decode(ob_get_contents());
            ob_end_clean();
        }

        $count_monthlikes = 0;
        if (!empty($likecount_month['data'][0]['values'])) {
            $count_monthlikes = count($likecount_month['data'][0]['values']);
        }


        if ($count_monthlikes >= 1) {
            $count_monthlikes = --$count_monthlikes;
        }
        $active_users = array();
        $daily = 0;
        $weekly = 0;
        $monthly = 0;
        if (!empty($likecount_day['data'][0]['values'][$count_daylikes]['value'])) {
            $daily = $likecount_day['data'][0]['values'][$count_daylikes]['value'];
        }

        if (!empty($likecount_week['data'][0]['values'][$count_weeklikes]['value'])) {
            $weekly = $likecount_week['data'][0]['values'][$count_weeklikes]['value'];
        }

        if (!empty($likecount_month['data'][0]['values'][$count_monthlikes]['value'])) {
            $monthly = $likecount_month['data'][0]['values'][$count_monthlikes]['value'];
        }
        $active_users = array('daily' => $daily, 'weekly' => $weekly, 'monthly' => $monthly);

        return $active_users;
    }

    public function getinsightinfoAction() {
        set_time_limit(0);
        $insightInfo_array = array('Application Installed Users' => 'application_installed_users', 'Application Widget Activity Views' => 'application_widget_activity_views', 'Application Widget Fan Views' => 'application_widget_fan_views', 'Application Widget Like Views' => 'application_widget_like_views', 'Application Widget Recommendation Views' => 'application_widget_recommendation_views');
        $client_id = Engine_Api::_()->getApi('settings', 'core')->core_facebook_appid;
        $fbAccessToken = $this->getAccessTokenFb();
        //FACEBOOK DOESN'T ALLOW THE TIME RANGE GRATER THEN 35 DAYS OR 840 HOURS SO WE ARE DIVING TIME INTO PARTS OF 720 HOURS IF THE DURATION IS GRATER THAN 720 HOURS.
        if ($_POST['duration'] <= 720) {
            $since = time() - $_POST['duration'] * 3600;
            $end_time = time();
            $infoInsight_array = array();
            //GETTING SOME SITE INSIGHT INFO FROM FACEBOOK.
            foreach ($insightInfo_array as $key => $value) {
                if ($value == 'application_installed_users') {
                    $duration = 'lifetime';
                } else {
                    $duration = 'day';
                }

                $insightInfo = Zend_Json::decode(@file_get_contents('https://graph.facebook.com/' . $client_id . '/insights/' . $value . '/' . $duration . '?' . $fbAccessToken . '&since=' . $since . '&until=' . $end_time));

                if (empty($insightInfo)) {
                    $graph_url = 'https://graph.facebook.com/' . $client_id . '/insights/' . $value . '/' . $duration . '?' . $fbAccessToken . '&since=' . $since . '&until=' . $end_time;
                    $ch = curl_init();
                    $timeout = 0;
                    curl_setopt($ch, CURLOPT_URL, $graph_url);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                    ob_start();
                    curl_exec($ch);
                    curl_close($ch);
                    $insightInfo = Zend_Json::decode(ob_get_contents());
                    ob_end_clean();
                }
                $item_count = 0;

                if ($value == 'application_installed_users') {
                    $total_appinstalled_users = 0;
                    if (!empty($insightInfo['data'][0]['values'])) {
                        $application_usercount = count($insightInfo['data'][0]['values']);
                        $total_appinstalled_users = $insightInfo['data'][0]['values'][--$application_usercount]['value'];
                    }

                    $infoInsight_array[$key] = $total_appinstalled_users;
                } else {
                    if (!empty($insightInfo['data'][0]['values'])) {
                        foreach ($insightInfo['data'][0]['values'] as $item) {
                            $item_count = $item_count + $item['value'];
                        }
                    }
                    $infoInsight_array[$key] = $item_count;
                }
            }
        } else {
            $divide_duration = $_POST['duration'] / 720;
            $infoInsight_array = array();
            while ($divide_duration > 0) {
                $since = time() - $divide_duration * 720 * 3600;
                $end_time = $since + 720 * 3600;
                $divide_duration--;

                //GETTING SOME SITE INSIGHT INFO FROM FACEBOOK.
                foreach ($insightInfo_array as $key => $value) {
                    if ($value == 'application_installed_users') {
                        $duration = 'lifetime';
                    } else {
                        $duration = 'day';
                    }

                    $insightInfo = Zend_Json::decode(@file_get_contents('https://graph.facebook.com/' . $client_id . '/insights/' . $value . '/' . $duration . '?' . $fbAccessToken . '&since=' . $since . '&until=' . $end_time));
                    if (empty($insightInfo)) {
                        $graph_url = 'https://graph.facebook.com/' . $client_id . '/insights/' . $value . '/' . $duration . '?' . $fbAccessToken . '&since=' . $since . '&until=' . $end_time;
                        $ch = curl_init();
                        $timeout = 0;
                        curl_setopt($ch, CURLOPT_URL, $graph_url);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                        ob_start();
                        curl_exec($ch);
                        curl_close($ch);
                        $insightInfo = Zend_Json::decode(ob_get_contents());
                        ob_end_clean();
                    }
                    $item_count = 0;
                    if ($value == 'application_installed_users') {
                        $application_usercount = count($insightInfo['data'][0]['values']);
                        $total_appinstalled_users = $insightInfo['data'][0]['values'][--$application_usercount]['value'];
                        if (empty($total_appinstalled_users)) {
                            $total_appinstalled_users = 0;
                        }
                        $infoInsight_array[$key] = $total_appinstalled_users;
                    } else {
                        foreach ($insightInfo['data'][0]['values'] as $item) {
                            $item_count = $item_count + $item['value'];
                        }
                    }
                    if (empty($infoInsight_array[$key])) {
                        $infoInsight_array[$key] = $item_count;
                    } else {
                        $infoInsight_array[$key] = $infoInsight_array[$key] + $item_count;
                    }
                }
            }
        }
        $infoInsight_descript = array('Application Installed Users' => 'Total installations of your application or connections to your Connect application.', 'Application Widget Activity Views' => 'Impressions of your activity plugin.', 'Application Widget Fan Views' => 'Impressions of your Like Box.', 'Application Widget Like Views' => 'Impressions of your Like plugin.', 'Application Widget Recommendation Views' => 'Impressions of your Recommendation plugin.');

        foreach ($infoInsight_descript as $key => $value) {
            $infoInsights[$key][] = $value;
            $infoInsights[$key][] = $infoInsight_array[$key];
        }

        $this->view->info_insights = $infoInsights;
    }

    public function getAccessTokenFb() {

        $url = "https://graph.facebook.com/oauth/access_token";
        $client_id = Engine_Api::_()->getApi('settings', 'core')->core_facebook_appid;
        $client_secret = Engine_Api::_()->getApi('settings', 'core')->core_facebook_secret;
        $postString = "client_id=$client_id&client_secret=$client_secret&type=client_cred";

        $response = @file_get_contents('https://graph.facebook.com/oauth/access_token?' . $postString);
        if (empty($response)) {
            $graph_url = 'https://graph.facebook.com/oauth/access_token?' . $postString;
            $ch = curl_init();
            $timeout = 0;
            curl_setopt($ch, CURLOPT_URL, $graph_url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            ob_start();
            curl_exec($ch);
            curl_close($ch);
            $response = ob_get_contents();
            ob_end_clean();
        }
        return $response;
    }

    public function fbconfigAction() {
        $this->view->navigation = $this->getNavigation(true);
    }

    //Facebook Comment box SocialPlugins Settings.

    public function commentsettingsAction() {
        $this->view->navigation = $this->getNavigation();
        $pagelevel_id = $this->_getParam('level_id', '');
        $this->view->content_type = $pagelevel_id;
        $this->view->form = $form = new Facebookse_Form_Admin_Commentsettings();

        $form->show_comments($pagelevel_id);

        //SHOWING THE FORM FILLED IF ACTION IS NOT FOR FORM POST.
        if (!$this->getRequest()->isPost()) {
            //GETTING THE SETTING FROM FACEBOOKSE_LIKES TABLE.
            if (!empty($pagelevel_id)) {

                $permissionTable_Comment = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->getMetainfo('', $pagelevel_id);
                $settings_comment = array();
                $this->view->commentsetting_showcomment = 0;

                if (!empty($permissionTable_Comment)) {
                    $settings_comment = $permissionTable_Comment->toArray();
                    $settings_comment['pagelevel_id'] = $pagelevel_id;
                    $settings_comment['enable'] = $settings_comment['commentbox_enable'];
                    $form->populate($settings_comment);
                    $this->view->commentsetting_showcomment = $settings_comment['commentbox_enable'];
                }
            }
        }
        //WHEN USER SUBMIT THE FORM.
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $this->getRequest()->getPost();
            //WE ARE DELETING ALL ROWS FROM BOTH TABLES FACEBOOKSE_LIKE AND FACEBOOKSE_SHARE AND THEN CREATING NEW ROWS ACCORDING TO NEW SETTINGS.
            $comment_setting_array = array(
                //'entity' => $values[$pagelevel_id . '_1'],
                'commentbox_enable' => $values['enable'],
                'commentbox_privacy' => $values['commentbox_privacy'],
                'commentbox_width' => $values['commentbox_width'],
                'commentbox_color' => $values['commentbox_color']
            );

            $pagelevel_id_temp = explode("_", $pagelevel_id);
            //$pagelevel_id =  $pagelevel_id_temp[0];
            include_once APPLICATION_PATH . '/application/modules/Facebookse/controllers/license/license2.php';
            $this->view->commentsetting_showcomment = $values['enable'];
            if (empty($values['enable'])) {
                //REVERTING BACK THE URL_SCRAPE COLUMN TO 0 VALUE FOR THIS MODULE TYPE.
                $tmTable = Engine_Api::_()->getDbtable('statistics', 'facebookse');
                $tmName = $tmTable->info('name');
                $tmTable->update(array('url_scrape' => '0'), array('url_type =?' => $pagelevel_id_temp[0]));
            }
        }
    }

    public function likeviewAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->navigation = $this->getNavigation();
        $this->view->form = $form = new Facebookse_Form_Admin_Likeview();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $this->getRequest()->getPost();

            $check_action = array_key_exists("default_settings", $values);
            if (!empty($check_action)) {
                foreach ($values as $key => $value) {
                    if ($key != 'default_settings') {
                        if (Engine_Api::_()->getApi('settings', 'core')->getSetting($key)) {
                            Engine_Api::_()->getApi('settings', 'core')->removeSetting($key);
                        }
                    }
                }
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('fblike.thumbsup.image')) {
                    Engine_Api::_()->getApi('settings', 'core')->removeSetting('fblike.thumbsup.image');
                }
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('fblike.thumbsdown.image')) {
                    Engine_Api::_()->getApi('settings', 'core')->removeSetting('fblike.thumbsdown.image');
                }
            } else {
                $sitelike_admin_tabb = 'like_button_view';
                foreach ($values as $key => $value) {
                    if (!empty($value)) {
                        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
                    }
                }
            }
            //$this->upgradeStyleCssFile();
            $this->_helper->redirector->gotoRoute(array('action' => 'likeview'));
        }
    }

    public function getNavigation($active = false) {
        if (is_null($this->_navigation)) {
            $navigation = $this->_navigation = new Zend_Navigation();
            $navigation_auth = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.navi.auth');

            if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
                $navigation->addPage(array(
                    'label' => 'Global Settings',
                    'route' => 'facebookse_admin',
                    'module' => 'facebookse',
                    'controller' => 'admin-settings',
                    'action' => 'index',
                    'active' => $active
                ));

                if (!empty($navigation_auth)) {

                    $navigation->addPage(array(
                        'label' => 'FB Like Button Settings',
                        'route' => 'facebookse_admin_like_settings',
                        'module' => 'facebookse',
                        'controller' => 'admin-settings',
                        'action' => 'likesettings',
                    ));

//          $navigation->addPage(array(
//						'label' => 'FB Like Button View',
//						'route' => 'facebookse_admin_like_view',
//						'module' => 'facebookse',
//						'controller' => 'admin-settings',
//						'action' => 'likeview',          
//					));

                    $navigation->addPage(array(
                        'label' => 'Likes Integration',
                        'route' => 'facebookse_admin_like_init_settings',
                        'module' => 'facebookse',
                        'controller' => 'admin-settings',
                        'action' => 'likeintsettings',
                    ));

                    $navigation->addPage(array(
                        'label' => 'Facebook Comments Box Settings',
                        'route' => 'facebookse_admin_comment_settings',
                        'module' => 'facebookse',
                        'controller' => 'admin-settings',
                        'action' => 'commentsettings',
                    ));


//                    $navigation->addPage(array(
//                        'label' => 'FB Social Plugins Settings',
//                        'route' => 'facebookse_admin_widget_settings',
//                        'module' => 'facebookse',
//                        'controller' => 'admin-settings',
//                        'action' => 'widgetsettings'
//                    ));

                    $navigation->addPage(array(
                        'label' => 'Open Graph Settings',
                        'route' => 'facebookse_admin_manage_opengraph',
                        'module' => 'facebookse',
                        'controller' => 'admin-settings',
                        'action' => 'opengraph'
                    ));

                    $navigation->addPage(array(
                        'label' => 'Statistics',
                        'route' => 'facebookse_admin_manage_statistics',
                        'module' => 'facebookse',
                        'controller' => 'admin-settings',
                        'action' => 'statistics'
                    ));

                    $navigation->addPage(array(
                        'label' => 'Manage Modules',
                        'route' => 'facebookse_admin_manage_modules',
                        'module' => 'facebookse',
                        'controller' => 'admin-manage',
                        'action' => 'index'
                    ));
                }

                $navigation->addPage(array(
                    'label' => 'FAQ',
                    'route' => 'facebookse_admin_faq',
                    'module' => 'facebookse',
                    'controller' => 'admin-settings',
                    'action' => 'faq'
                ));
            }
        }
        return $this->_navigation;
    }

    public function showcommentboxpreviewAction() {
        $this->_helper->layout->setLayout('admin-simple');
        if ($_GET['format'] == 'smoothbox') {
            $this->view->comment_box = $_GET;
        }
    }

}
