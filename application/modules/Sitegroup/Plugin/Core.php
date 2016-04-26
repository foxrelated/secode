<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Plugin_Core extends Zend_Controller_Plugin_Abstract {

  public function routeShutdown(Zend_Controller_Request_Abstract $request) {

    if (substr($request->getPathInfo(), 1, 5) == "admin") {
      $module = $request->getModuleName();
      $controller = $request->getControllerName();
      $action = $request->getActionName();
      if ($module == 'core' && $controller == 'admin-content' && $action == 'index') {
        $sitegroupLayoutCreate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate');
        if (!empty($sitegroupLayoutCreate)) {
          $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page', null);
          if (!empty($group_id)) {
            $coregroupTable = Engine_Api::_()->getDbtable('pages', 'core');
            $coregroupTableName = $coregroupTable->info('name');
            $select = $coregroupTable->select()
                    ->from($coregroupTableName)
                    ->where('page_id' . ' = ?', $group_id)
                    ->where('name' . ' = ?', 'sitegroup_index_view')
                    ->limit(1);
            $coregroupTableInfo = $coregroupTable->fetchRow($select);
          }
          if (!empty($coregroupTableInfo)) {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $redirector->gotoRoute(array('module' => 'sitegroup', 'controller' => 'layout', 'action' => 'layout', 'group' => $group_id), 'admin_default', false);
          }
        }
      }
    }

    //CHECK IF ADMIN
    if (substr($request->getPathInfo(), 1, 5) == "admin") {
      return;
    }


    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action = $request->getActionName();

// SITEGROUPURL WORK START
    $sitegroupUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupurl');
    if (!empty($sitegroupUrlEnabled) && (($module == 'sitegroup' && ($controller == 'index' || $controller == 'mobi') && $action == 'view') || ($module == 'core'))) {
      $front = Zend_Controller_Front::getInstance();

      // GET THE URL OF GROUP
      $urlO = $request->getRequestUri();
      $groupurl = '';

      // GET THE ROUTE BY WHICH GROUP WILL BE OPEN IF SHORTEN GROUPURL IS DISABLED
      $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manifestUrlS', "groupitem");

      // GET THE BASE URL
      $base_url = $front->getBaseUrl();

      // MAKE A STRING OF BASEUL WITH ROUTESTART
      $string_url = $base_url . '/' . $routeStartS.'/';

      // FIND OUT THE POSITION OF ROUTESTART IF EXIST
      $pos_routestart = strpos($urlO, $string_url);
      if ($pos_routestart === false) {
        $index_routestart = 0;
        $groupurlArray = explode($base_url . '/', $urlO);
        $mainGroupurl = strstr($groupurlArray[1], '/');

        // CHECK BASEDIRECTORY IS EXIST OR NOT
        if (empty($mainGroupurl)) {
          if (isset($groupurlArray[1])) {
            $groupurl = $groupurlArray[1];
          }
        } else {
          $groupurl = $mainGroupurl;
        }
      } else {
        $index_routestart = 1;
        $groupurlArray = explode($string_url, $urlO);
        $final_url = $groupurlArray[1];
        $mainGroupurl = explode('/', $final_url);
        if (isset($mainGroupurl[1]))
          $groupurl = $mainGroupurl[1];
      }

      // GET THE GROUP LIKES AFTER WHICH SHORTEN GROUPURL WILL BE WORK 
      $group_likes = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.likelimit.forurlblock', "5");
      $params_array = array();
      if ($front->getBaseUrl() == '' && empty($index_routestart)) {
        $params_array = $groupurlArray;
        $params_array[0] = NULL;
        array_shift($params_array);
      } else {
        $params_array = explode('/', $groupurlArray[1]);
      }

      if (!empty($index_routestart)) {
        if (isset($params_array['1']))
          $groupurl = $params_array['1'];
      }
      else {
        $groupurl = $params_array['0'];
      }
      
      $groupurl = explode('?',$groupurl);
      $groupurl = $groupurl[0];

      // MAKE THE OBJECT OF SITEGROUP
      $sitegroupObject = Engine_Api::_()->getItem('sitegroup_group', Engine_Api::_()->sitegroup()->getGroupId($groupurl));

      $bannedGroupurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');

      // GET THE ARRAY OF BANNED GROUPURLS
      $urlArray = $bannedGroupurlsTable->select()->from($bannedGroupurlsTable, 'word')
                      ->where('word = ?', $groupurl)
                      ->query()->fetchColumn();
      $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.change.url', 1);
      if (empty($urlArray) && (!empty($change_url)) && !empty($sitegroupObject) && ($sitegroupObject->like_count >= $group_likes)) {
        if ((!empty($index_routestart))) {
          $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
          unset($params_array[0]);
          $redirector->gotoUrl(implode("/", $params_array));
        }
        $request->setModuleName('sitegroup');
        $request->setControllerName('index');
        $request->setActionName('view');
        $request->setParam("group_url", $groupurl);
        $count = count($params_array);
        for ($i = 1; $i <= $count; $i++) {
          if( array_key_exists($i, $params_array) ) {
            $j = $i + 1;
            if(isset($params_array[$i]) && isset($params_array[$j]) && !empty($params_array[$i])) {
               $request->setParam($params_array[$i], $params_array[$j]);
            }
          }
        }
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
          $sr_response = Engine_Api::_()->sitemobile()->setupRequest($request);
        }
      }
    }

    // SITEGROUPURL WORK END
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('mobi'))
      return;

    $mobile = $request->getParam("mobile");
    $session = new Zend_Session_Namespace('mobile');

    if ($mobile == "1") {
      $mobile = true;
      $session->mobile = true;
    } elseif ($mobile == "0") {
      $mobile = false;
      $session->mobile = false;
    } else {
      if (isset($session->mobile)) {
        $mobile = $session->mobile;
      } else {
        //CHECK TO SEE IF MOBILE
        if (Engine_Api::_()->mobi()->isMobile()) {
          $mobile = true;
          $session->mobile = true;
        } else {
          $mobile = false;
          $session->mobile = false;
        }
      }
    }

    if (!$mobile) {
      return;
    }
    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action = $request->getActionName();
    if ($module == "sitegroup") {
      if ($controller == "index" && $action == "home") {
        $request->setControllerName('mobi');
        $request->setActionName('home');
      }

      if ($controller == "index" && $action == "index") {
        $request->setControllerName('mobi');
        $request->setActionName('index');
      }

      if ($controller == "index" && $action == "view") {

        $request->setControllerName('mobi');
        $request->setActionName('view');
      }

      if ($controller == "index" && $action == "map") {
        $request->setControllerName('index');
        $request->setActionName('mobilemap');
      }
    }

    //CREATE LAYOUT
    $layout = Zend_Layout::startMvc();

    //SET OPTIONS
    $layout->setViewBasePath(APPLICATION_PATH . "/application/modules/Mobi/layouts", 'Core_Layout_View')
            ->setViewSuffix('tpl')
            ->setLayout(null);
  }

  public function onRenderLayoutDefault($event) {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
		$membercategory_ids='';
    $sitegroup_layout_setting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layout.setting', 1);
    $sitegroup_hide_left_container = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.hide.left.container', 0);
    $sitegroup_slding_effect = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.slding.effect', 1);
		$show_option = 1;
    if ($module == "sitegroup") {
      if ($controller == "index" && $action == "index") {
        $siteinfo = $view->layout()->siteinfo;
        if (Zend_Controller_Front::getInstance()->getRequest()->getParam('categoryname', null)) {
          $siteinfo['keywords'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('categoryname', null);
        }
        if (Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategoryname', null)) {
          $siteinfo['keywords'] .= ',' . Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategoryname', null);
        }

        if (!empty($_GET['location'])) {
          if (Zend_Controller_Front::getInstance()->getRequest()->getParam('location', null)) {
            $siteinfo['keywords'] .= ',';
          }
          $siteinfo['keywords'] .= $_GET['location'];
        }
        $view->layout()->siteinfo = $siteinfo;
      }

      $category_name = "";
      $subcategory_name = "";
      if (Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null)) {
        $currentgroupid = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
        $sitegroupObject = Engine_Api::_()->getItem('sitegroup_group', $currentgroupid);
        $siteinfo = $view->layout()->siteinfo;
        $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($sitegroupObject->category_id);
        if (!empty($row->category_name)) {
          $category_name = $row->category_name;
          $siteinfo['keywords'] = $category_name;
        }
        $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($sitegroupObject->subcategory_id);
        if (!empty($row->category_name)) {
          $subcategory_name = $row->category_name;
          $siteinfo['keywords'] .= ',' . $subcategory_name;
        }

        if (!empty($sitegroupObject->location)) {
          $siteinfo['keywords'] .= ',' . $sitegroupObject->location;
        }
        $view->layout()->siteinfo = $siteinfo;
      }
      if ($controller == "index" && $action == "view") {
        if (Zend_Controller_Front::getInstance()->getRequest()->getParam('group_url', null)) {
          $currenttabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', 0);
          $group_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_url', null);
          $contentinformation = 0;
          $view = $event->getPayload();
          $id = Engine_Api::_()->sitegroup()->getGroupId($group_url);
          $sitegroup_title = '';
          if (!empty($id)) {
            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $id);
            $sitegroup_title = Engine_Api::_()->sitegroup()->parseString($sitegroup->title);
            $sitegroup_title = $view->string()->escapeJavascript($sitegroup_title);
          }
          
          $content_id = 0;
          $content_id3 = 0;
          $tab_id = 0;
          $tab_id3 = 0;
          $tab_id4 = 0;
          $widgetinformation = 0;
          $tempcontent_name = "";
          $tempcontent_id = 0;
          $newtabid = 0;
          $itemAlbumCount = 10;
          $itemPhotoCount = 100;
          if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0) && !empty($sitegroup)) {

            $row = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup')->getContentGroupId($id);
            if ($row !== null) {
              $contentgroup_id = $row->contentgroup_id;
              $siteinfo = $view->layout()->siteinfo;
              $siteinfo['description'] = $row->description;
              $siteinfo['keywords'] = $row->keywords;
              $view->layout()->siteinfo = $siteinfo;
              $rowinfo = Engine_Api::_()->getDbtable('content', 'sitegroup')->getContentInformation($contentgroup_id);

              if (!empty($rowinfo)) {
                foreach ($rowinfo as $key => $value) {
                  if ($value->name == 'advancedactivity.home-feeds') {
                    $content_id = $tab_id = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id;
                    }
                  } elseif ($value->name == 'seaocore.feed') {
                    $content_id = $tab_id = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id;
                    }
                  } elseif ($value->name == 'activity.feed') {
                    $content_id = $tab_id = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id;
                    }
                  } else if ($value->name == 'core.profile-links') {
                    $content_id3 = $tab_id3 = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id3;
                    }
                  } else if ($value->name == 'core.html-block') {
                    $content_id4 = $tab_id4 = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id4;
                    }
                  }
                }
              }
							if (!empty($contentgroup_id)) {
								$contentinfo = Engine_Api::_()->getDbtable('content', 'sitegroup')->getContentByWidgetName('core.container-tabs', $contentgroup_id);
								if (empty($contentinfo)) {
									$contentinformation = 0;
								} else {
									$contentinformation = 1;
								}

								$contentwidgetinfo = Engine_Api::_()->getDbtable('content', 'sitegroup')->getContentByWidgetName('sitegroup.widgetlinks-sitegroup', $contentgroup_id);
								if (empty($contentwidgetinfo)) {
									$widgetinformation = 0;
								} else {
									$widgetinformation = 1;
								}
							}
							$default_content_id = Engine_Api::_()->getDbtable('content', 'sitegroup')->getContentId($contentgroup_id, $sitegroup);
							$tempcontent_name = $default_content_id['content_name'];
							$tempcontent_id = $default_content_id['content_id'];
							if (empty($default_content_id['itemAlbumCount'])) {
								$itemAlbumCount = 10;
							} else {
								$itemAlbumCount = $default_content_id['itemAlbumCount'];
							}
							if (empty($default_content_id['itemPhotoCount'])) {
								$itemPhotoCount = 100;
							} else {
								$itemPhotoCount = $default_content_id['itemPhotoCount'];
							}
							if (empty($default_content_id['resource_type_integration'])) {
								$resource_type_integration = Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', 0);
								$ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitegroup.ad.$resource_type_integration", 3);
							} else {
								$resource_type_integration = $default_content_id['resource_type_integration'];
								$ads_display_integration = $default_content_id['ads_display_integration'];
							}

							$newtabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', $tempcontent_id);
							$tab_main = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
							if ($tab_main) {
								$tempcontent_name = Engine_Api::_()->getDbtable('content', 'sitegroup')->getCurrentTabName($tab_main);
								$tempcontent_id = $tab_main;
							}
            } else {
              $contentgroup_id = Engine_Api::_()->sitegroup()->getWidgetizedGroup()->page_id;
              $siteinfo = $view->layout()->siteinfo;
              $siteinfo['description'] = Engine_Api::_()->sitegroup()->getWidgetizedGroup()->description;
              $siteinfo['keywords'] = Engine_Api::_()->sitegroup()->getWidgetizedGroup()->keywords;
              $view->layout()->siteinfo = $siteinfo;
              $rowinfo = Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->getContentInformation($contentgroup_id);

              if (!empty($rowinfo)) {
                foreach ($rowinfo as $key => $value) {
                  if ($value->name == 'advancedactivity.home-feeds') {
                    $content_id = $tab_id = $value->admincontent_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id;
                    }
                  } elseif ($value->name == 'seaocore.feed') {
                    $content_id = $tab_id = $value->admincontent_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id;
                    }
                  } elseif ($value->name == 'activity.feed') {
                    $content_id = $tab_id = $value->admincontent_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id;
                    }
                  } else if ($value->name == 'core.profile-links') {
                    $content_id3 = $tab_id3 = $value->admincontent_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id3;
                    }
                  } else if ($value->name == 'core.html-block') {
                    $content_id4 = $tab_id4 = $value->admincontent_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id4;
                    }
                  }
                }
              }
							if (!empty($contentgroup_id)) {
								$contentinfo = Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->getContentByWidgetName('core.container-tabs', $contentgroup_id);
								if (empty($contentinfo)) {
									$contentinformation = 0;
								} else {
									$contentinformation = 1;
								}

								$contentwidgetinfo = Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->getContentByWidgetName('sitegroup.widgetlinks-sitegroup', $contentgroup_id);
								if (empty($contentwidgetinfo)) {
									$widgetinformation = 0;
								} else {
									$widgetinformation = 1;
								}
							}
							$default_content_id = Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->getContentId($contentgroup_id, $sitegroup);
							$tempcontent_name = $default_content_id['content_name'];
							$tempcontent_id = $default_content_id['content_id'];
							if (empty($default_content_id['itemAlbumCount'])) {
								$itemAlbumCount = 10;
							} else {
								$itemAlbumCount = $default_content_id['itemAlbumCount'];
							}
							if (empty($default_content_id['itemPhotoCount'])) {
								$itemPhotoCount = 100;
							} else {
								$itemPhotoCount = $default_content_id['itemPhotoCount'];
							}
							if (empty($default_content_id['resource_type_integration'])) {
								$resource_type_integration = Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', 0);
								$ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitegroup.ad.$resource_type_integration", 3);
							} else {
								$resource_type_integration = $default_content_id['resource_type_integration'];
								$ads_display_integration = $default_content_id['ads_display_integration'];
							}

							$newtabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', $tempcontent_id);
							$tab_main = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
							if ($tab_main) {
								$tempcontent_name = Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->getCurrentTabName($tab_main);
								$tempcontent_id = $tab_main;
							}
            }

          } else {
            $table = Engine_Api::_()->getDbtable('pages', 'core');
            $select = $table->select()
                    ->where('name = ?', 'sitegroup_index_view')
                    ->limit(1);
            $row = $table->fetchRow($select);
            if ($row !== null) {
              $group_id = $row->page_id;
              $table = Engine_Api::_()->getDbtable('content', 'core');
              $select = $table->select()
                      ->where("name IN ('sitegroup.info-sitegroup', 'seaocore.feed', 'advancedactivity.home-feeds', 'activity.feed', 'sitegroup.location-sitegroup', 'core.profile-links', 'core.html-block')")
                      ->where('page_id = ?', $group_id)
                      ->order('content_id ASC');
              $rowinfo = $table->fetchAll($select);
              if (!empty($rowinfo)) {
                foreach ($rowinfo as $key => $value) {
                  if ($value->name == 'advancedactivity.home-feeds') {
                    $content_id = $tab_id = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id;
                    }
                  } elseif ($value->name == 'seaocore.feed') {
                    $content_id = $tab_id = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id;
                    }
                  } elseif ($value->name == 'activity.feed') {
                    $content_id = $tab_id = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id;
                    }
                  } else if ($value->name == 'core.profile-links') {
                    $content_id3 = $tab_id3 = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id3;
                    }
                  } else if ($value->name == 'core.html-block') {
                    $content_id4 = $tab_id4 = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id4;
                    }
                  }
                }
              }
            }

            $table = Engine_Api::_()->getDbtable('content', 'core');
            $tablename = $table->info('name');
            if (!empty($group_id)) {
              $selectContent = $table->select()
                      ->from($tablename)
                      ->where('name =?', 'core.container-tabs')
                      ->where('page_id =?', $group_id)
                      ->limit(1);
              $contentinfo = $selectContent->query()->fetchAll();
              if (empty($contentinfo)) {
                $contentinformation = 0;
              } else {
                $contentinformation = 1;
              }
            }

            if (!empty($group_id)) {
              $selectContent = $table->select()
                      ->from($tablename)
                      ->where('name =?', 'sitegroup.widgetlinks-sitegroup')
                      ->where('page_id =?', $group_id)
                      ->limit(1);
              $contentwidgetinfo = $selectContent->query()->fetchAll();
              if (empty($contentwidgetinfo)) {
                $widgetinformation = 0;
              } else {
                $widgetinformation = 1;
              }
            }

            $selectGroup = Engine_Api::_()->sitegroup()->getWidgetizedGroup();
            if (!empty($selectGroup)) {
              $groupid = $selectGroup->page_id;
              if (!empty($groupid)) {
                $tableCore = Engine_Api::_()->getDbtable('content', 'core');
                $select = $tableCore->select();
                $select_content = $select
                        ->from($tableCore->info('name'))
                        ->where('page_id = ?', $groupid)
                        ->where('type = ?', 'container')
                        ->where('name = ?', 'main')
                        ->limit(1);
                $content = $select_content->query()->fetchAll();
                if (!empty($content)) {
                  $select = $tableCore->select();
                  $select_container = $select
                          ->from($tableCore->info('name'), array('content_id'))
                          ->where('page_id = ?', $groupid)
                          ->where('type = ?', 'container')
                          ->where('name = ?', 'middle')
                          ->limit(1);
                  $container = $select_container->query()->fetchAll();
                  if (!empty($container)) {
                    $select = $tableCore->select();
                    $container_id = $container[0]['content_id'];
                    $select_middle = $select
                            ->from($tableCore->info('name'))
                            ->where('parent_content_id = ?', $container_id)
                            ->where('type = ?', 'widget')
                            ->where('name = ?', 'core.container-tabs')
                            ->where('page_id = ?', $groupid)
                            ->limit(1);
                    $middle = $select_middle->query()->fetchAll();
                    if (!empty($middle)) {
                      $content_id = $middle[0]['content_id'];
                    } else {
                      $content_id = $container_id;
                    }
                  }
                }

                if (!empty($content_id) && !empty($sitegroup)) {
                  $select = $tableCore->select();
                  $select_middle = $select
                          ->from($tableCore->info('name'), array('content_id', 'name', 'params'))
                          ->where('parent_content_id = ?', $content_id)
                          ->where('type = ?', 'widget')
													->where("name NOT IN ('sitegroup.title-sitegroup', 'seaocore.like-button','seaocore.seaocore-follow', 'sitegroup.photorecent-sitegroup', 'Facebookse.facebookse-commonlike', 'sitegroup.thumbphoto-sitegroup', 'sitegroup.contactdetails-sitegroup','sitelike.common-like-button')")
                          ->where('page_id = ?', $groupid);

                  $middle = $select_middle->query()->fetchAll();

                  $itemAlbumCount = 10;
                  $itemPhotoCount = 100;
                  $select = $tableCore->select();
                  $select_photo = $select
                          ->from($tableCore->info('name'), array('params'))
                          ->where('parent_content_id = ?', $content_id)
                          ->where('type = ?', 'widget')
                          ->where('name = ?', 'sitegroup.photos-sitegroup')
                          ->where('page_id = ?', $groupid)
                          ->order('order DESC');

                  $middlePhoto = $select_photo->query()->fetchColumn();
                  if (!empty($middlePhoto)) {
                    $photoParamsDecodedArray = Zend_Json_Decoder::decode($middlePhoto);
                    if (isset($photoParamsDecodedArray['itemCount']) && !empty($photoParamsDecodedArray)) {
                      $itemAlbumCount = $photoParamsDecodedArray['itemCount'];
                    }
                    if (isset($photoParamsDecodedArray['itemCount_photo']) && !empty($photoParamsDecodedArray)) {
                      $itemPhotoCount = $photoParamsDecodedArray['itemCount_photo'];
                    }
                  }

                  $flag = false;
                  $editpermission = '';
                  $isManageAdmin = '';
                  $resource_type_integration = Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', 0);
                  $ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitegroup.ad.$resource_type_integration", 3);
                  $viewer = Engine_Api::_()->user()->getViewer();
                  $viewer_id = $viewer->getIdentity();
                  if (!empty($viewer_id)) {
                    $level_id = $viewer->level_id;
                  } else {
                    $level_id = 0;
                  }
                  foreach ($middle as $value) {
                    $content_name = $value['name'];
                    switch ($content_name) {
                      case 'sitegroup.overview-sitegroup':
                        if (!empty($sitegroup)) {
                          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'overview');
                          if (!empty($isManageAdmin)) {
                            $editpermission = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
                            if (!empty($editpermission) && empty($sitegroup->overview)) {
                              $flag = true;
                            } elseif (empty($editpermission) && empty($sitegroup->overview)) {
                              $flag = false;
                            } elseif (!empty($editpermission) && !empty($sitegroup->overview)) {
                              $flag = true;
                            } elseif (empty($editpermission) && !empty($sitegroup->overview)) {
                              $flag = true;
                            }
                          }
                        }
                        break;
                      case 'sitegroup.location-sitegroup':
                        if (!empty($sitegroup)) {
                          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'map');
                          if (!empty($isManageAdmin)) {
                            $value['id'] = $sitegroup->getIdentity();
                            $location = Engine_Api::_()->getDbtable('locations', 'sitegroup')->getLocation($value);
                            if (!empty($location)) {
                              $flag = true;
                            }
                          }
                        }
                        break;
                      case 'seaocore.feed':
                        $flag = true;
                        break;
                      case 'advancedactivity.home-feeds':
                        $flag = true;
                        break;
                      case 'activity.feed':
                        $flag = true;
                        break;
                      case 'sitegroup.info-sitegroup':
                        $flag = true;
                        break;
                      case 'core.html-block':
                        $flag = true;
                        break;
                      case 'core.profile-links':
                        $flag = true;
                        break;
                      case 'sitegroupintegration.profile-items':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration')) {
                          $content_params = $value['params'];
                          $paramsDecodedArray = Zend_Json_Decoder::decode($content_params);
                          $resource_type_integration = $paramsDecodedArray['resource_type'];
                          $ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitegroup.ad.$resource_type_integration", 3);

                          if (Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', 0)) {
                            $resource_type_integration = Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', 0);
                            $ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitegroup.ad.$resource_type_integration", 3);
                          }
                          //PACKAGE BASE AND MEMBER LEVEL SETTINGS PRIYACY START
                          if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", $resource_type_integration)) {
                              $flag = true;
                            }
                          } else {
                            $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, $resource_type_integration);
                            if (!empty($isGroupOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          $resource_type = $resource_type_integration;
													$pieces = explode("_", $resource_type);
													if ($resource_type == 'document_0' || $resource_type == 'folder_0' || $resource_type == 'quiz_0') {
														$paramsIntegration['listingtype_id'] = $listingTypeId = $pieces[1];
														$paramsIntegration['resource_type'] = $resource_type = $pieces[0];
													}	else {
														$paramsIntegration['listingtype_id'] = $listingTypeId = $pieces[2];
														$paramsIntegration['resource_type'] = $resource_type = $pieces[0] . '_' . $pieces[1];
													}

													$paramsIntegration['group_id'] = $sitegroup->group_id;
													$paginator = Engine_Api::_()->getDbtable('contents', 'sitegroupintegration')->getResults($paramsIntegration);
													if ($paginator->getTotalItemCount() <= 0) {
														$flag = false;
													} else {
														$flag = true;
													}
                          //PACKAGE BASE AND MEMBER LEVEL SETTINGS PRIYACY END
                        }
                        break;
                      case 'sitegroupnote.profile-sitegroupnotes':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote')) {
                          if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupnote") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'sncreate');
                            if (!empty($isGroupOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL NOTES
                          $noteCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroupnote', 'notes');
                          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sncreate');
                          if (!empty($isManageAdmin) || !empty($noteCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                      case 'sitegroupevent.profile-sitegroupevents':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) {
                          if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupevent") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'secreate');
                            if (!empty($isGroupOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL EVENTS
                          $eventCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroupevent', 'events');
                          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'secreate');
                          if (!empty($isManageAdmin) || !empty($eventCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
case 'sitevideo.contenttype-videos':
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
              if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupvideo") == 1) {
                  $flag = true;
                }
              } else {
                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'svcreate');
                if (!empty($isGroupOwnerAllow)) {
                  $flag = true;
                }
              }
              //TOTAL EVENTS
							$videoCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitevideo', 'videos');
              $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'svcreate');
              if (!empty($isManageAdmin) || !empty($videoCount)) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;												case 'siteevent.contenttype-events':
													if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
														if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
															if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupevent") == 1) {
																$flag = true;
															}
														} else {
															$isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'secreate');
															if (!empty($isGroupOwnerAllow)) {
																$flag = true;
															}
														}
														//TOTAL EVENTS
														$eventCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'siteevent', 'events');
														$isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'secreate');
														if (!empty($isManageAdmin) || !empty($eventCount)) {
															$flag = true;
														} else {
															$flag = false;
														}
													}
												break;
                      case 'sitegroup.discussion-sitegroup':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
                          if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupdiscussion") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'sdicreate');
                            if (!empty($isGroupOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL TOPICS
                          $topicCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroup', 'topics');
                          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sdicreate');
                          if (!empty($isManageAdmin) || !empty($topicCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                      case 'sitegroup.photos-sitegroup':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
                          if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupalbum") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'spcreate');
                            if (!empty($isGroupOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL ALBUMS
                          $albumCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroup', 'albums');
                          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'spcreate');
                          if (!empty($isManageAdmin) || !empty($albumCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                      case 'sitegroupmusic.profile-sitegroupmusic':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
                          if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupmusic") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'smcreate');
                            if (!empty($isGroupOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL PLAYLISTS
                          $musicCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroupmusic', 'playlists');
                          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'smcreate');
                          if (!empty($isManageAdmin) || !empty($musicCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;

                      case 'sitegroupmember.profile-sitegroupmembers':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
                          if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupmember") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'smecreate');
                            if (!empty($isGroupOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL PLAYLISTS
                          $memberCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroup', 'membership');
                          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'smecreate');
                          if (!empty($isManageAdmin) || !empty($memberCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;

                      case 'sitegroupdocument.profile-sitegroupdocuments':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument')) {
                          if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupdocument") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'sdcreate');
                            if (!empty($isGroupOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL DOCUMENTS
                          $documentCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroupdocument', 'documents');
                          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sdcreate');
                          if (!empty($isManageAdmin) || !empty($documentCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                      case 'sitegroupreview.profile-sitegroupreviews':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
                          //TOTAL REVIEW
                          $reviewCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroupreview', 'reviews');
                          $level_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'sitegroupreview_review', 'create');
                          if (!empty($level_allow) || !empty($reviewCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                      case 'sitegroupvideo.profile-sitegroupvideos':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {
                          if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupvideo") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'svcreate');
                            if (!empty($isGroupOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL VIDEO
                          $videoCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroupvideo', 'videos');
                          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'svcreate');
                          if (!empty($isManageAdmin) || !empty($videoCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                      case 'sitegrouppoll.profile-sitegrouppolls':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll')) {
                          if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegrouppoll") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'splcreate');
                            if (!empty($isGroupOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL POLL
                          $pollCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegrouppoll', 'polls');
                          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'splcreate');
                          if (!empty($isManageAdmin) || !empty($pollCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                      case 'sitegroupoffer.profile-sitegroupoffers':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer')) {
                          if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupoffer") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'offer');
                            if (!empty($isGroupOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL OFFERS
                          $can_edit = 1;
                          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
                          if (empty($isManageAdmin)) {
                            $can_edit = 0;
                          }

                          $can_offer = 1;
                          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'offer');

                          if (empty($isManageAdmin)) {
                            $can_offer = 0;
                          }

                          $can_create_offer = '';

                          //OFFER CREATION AUTHENTICATION CHECK
                          if ($can_edit == 1 && $can_offer == 1) {
                            $can_create_offer = 1;
                          }

                          //TOTAL OFFER
                          $offerCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroupoffer', 'offers');
                          if (!empty($can_create_offer) || !empty($offerCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                      case 'sitegroupform.sitegroup-viewform':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform')) {
                          if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupform") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'form');
                            if (!empty($isGroupOwnerAllow)) {
                              $flag = true;
                            }
                          }
                        }
                        break;
                      case 'sitegrouptwitter.feeds-sitegrouptwitter':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouptwitter')) {
                          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'twitter');
                          if (!empty($isManageAdmin)) {
                            $flag = true;
                          }
                        }
                        break;											
                    }
                    if (!empty($flag)) {
                      $content_ids = $value['content_id'];
                      $content_names = $value['name'];
                      break;
                    }
                  }
                }

                $params = array('content_id' => $content_ids, 'content_name' => $content_names);

                $tempcontent_name = $params['content_name'];
                $tempcontent_id = $params['content_id'];

                $newtabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', $tempcontent_id);
                
                 //////////////////////
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
									$select = $tableCore->select();
									$select_member = $select
																	->from($tableCore->info('name'), array('params'))
																	->where('content_id = ?', $newtabid)
																	->where('type = ?', 'widget')
																	->where('name = ?', 'sitegroupmember.profile-sitegroupmembers'); 
									$member_params = $select_member->query()->fetchColumn(); //print_R($member_params);die;
									if (!empty($member_params)) {
										$photoParamsDecodedArray = Zend_Json_Decoder::decode($member_params); 
										if (isset($photoParamsDecodedArray['show_option']) && !empty($photoParamsDecodedArray)) {
											$show_option = $photoParamsDecodedArray['show_option'];
										} else {
									   	$show_option = 1;
										}
										if (isset($photoParamsDecodedArray['membercategory_id']) && !empty($photoParamsDecodedArray)) {
											$membercategory_id = $photoParamsDecodedArray['membercategory_id'];
											$membercategory_ids =  json_encode($membercategory_id);
										}
									} else {
										$show_option = 1;
										$membercategory_ids = null;
									}
								}
								//////////

                $tab_main = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
                if ($tab_main) {
                  $current_tab_name = $tableCore->select()
                          ->from($tableCore->info('name'), array('name'))
                          ->where('content_id = ?', $tab_main)
                          ->query()
                          ->fetchColumn();
                  $tempcontent_name = $current_tab_name;
                  $tempcontent_id = $tab_main;
                }
              }
            }
          }

          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && !empty($contentinfo)) {
            $group_communityads = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1);
          } else {
            $group_communityads = 0;
          }
          
          $siteinfo = $view->layout()->siteinfo;
          if (!empty($sitegroup)) {
            if ($sitegroup->category_id) {
              $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($sitegroup->category_id);
              if (!empty($row->category_name)) {
                $category_name = $row->category_name;
                $siteinfo['keywords'] = $category_name;
              }
              $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($sitegroup->subcategory_id);
              if (!empty($row->category_name)) {
                $subcategory_name = $row->category_name;
                $siteinfo['keywords'] .= ',' . $subcategory_name;
              }
            }
            if (!empty($sitegroup->location)) {
              $siteinfo['keywords'] .= ',' . $sitegroup->location;
            }
          }
          $script = null;
          $view->layout()->siteinfo = $siteinfo;
          $is_ajax = Zend_Controller_Front::getInstance()->getRequest()->getParam('isajax', null);
          if (empty($is_ajax)) {
            $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manifestUrlS', "groupitem");
            $group_url_integration = $routeStartS . '/' . Engine_Api::_()->sitegroup()->getGroupUrl($sitegroup->group_id);
            $overview = $view->translate('Overview');
            $form = $view->translate('Form');
            $review = $view->translate('Reviews');
            $document = $view->translate('Documents');
            $offer = $view->translate('Offers');
            $poll = $view->translate('Polls');
            $event = $view->translate('Events');
            $note = $view->translate('Notes');
            $photo = $view->translate('Photos');
            $discussion = $view->translate('Discussions');
            $map = $view->translate('Map');
            $link = $view->translate('Links');
            $video = $view->translate('Videos');
            $music = $view->translate('Music');
            $member = $view->translate('Member');
            $group_url = Engine_Api::_()->sitegroup()->getGroupUrl($sitegroup->group_id);
            $script = <<<EOF
      var sitegroup_layout_setting = '$sitegroup_layout_setting';
	    var group_communityads = '$group_communityads';
	    var contentinformation = '$contentinformation';
      var group_hide_left_container = '$sitegroup_hide_left_container';
      var sitegroup_slding_effect = '$sitegroup_slding_effect';
	    var group_showtitle = 0;
	    var prev_tab_class = '';
	    if(contentinformation == 0) {
	      group_showtitle = 1;
	    }
      window.addEvent('domready', function() {
	    	if($('main_tabs')) {
					switch ("$tempcontent_name") {
            case 'sitegroup.photos-sitegroup':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Photo, '$tempcontent_id', 'photo', 'sitegroup', 'photos-sitegroup', group_showtitle, 'null', photo_ads_display, group_communityad_integration, adwithoutpackage,$itemAlbumCount, $itemPhotoCount);
						  	if($('global_content').getElement('.layout_sitegroup_photos_sitegroup')) {
									hideLeftContainer (photo_ads_display, group_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitegroup_photos_sitegroup';
								group_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitegroup_photos_sitegroup')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitegroup_photos_sitegroup'));
                }
							}
            break;
            case 'sitegroupvideo.profile-sitegroupvideos':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Video, '$tempcontent_id', 'video', 'sitegroupvideo', 'profile-sitegroupvideos', group_showtitle, 'null', video_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegroupvideo_profile_sitegroupvideos')) {
									hideLeftContainer (video_ads_display, group_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitegroupvideo_profile_sitegroupvideos';
								group_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitegroupvideo_profile_sitegroupvideos')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitegroupvideo_profile_sitegroupvideos'));
                }
							}
            break;
            case 'sitegroupnote.profile-sitegroupnotes':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Note, '$tempcontent_id', 'note', 'sitegroupnote', 'profile-sitegroupnotes', group_showtitle, 'null', note_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegroupnote_profile_sitegroupnotes')) {
									hideLeftContainer (note_ads_display, group_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitegroupnote_profile_sitegroupnotes';
								group_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitegroupnote_profile_sitegroupnotes')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitegroupnote_profile_sitegroupnotes'));
                }
							}
            break;
            case 'sitegroupreview.profile-sitegroupreviews':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Review, '$tempcontent_id', 'review', 'sitegroupreview', 'profile-sitegroupreviews', group_showtitle,'$group_url', review_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegroupreview_profile_sitegroupreviews')) {
									hideLeftContainer (review_ads_display, group_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitegroupreview_profile_sitegroupreviews';
								group_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitegroupreview_profile_sitegroupreviews')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitegroupreview_profile_sitegroupreviews'));
                }
							}
            break;
            case 'sitegroupform.sitegroup-viewform':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Form, '$tempcontent_id', 'form', 'sitegroupform', 'sitegroup-viewform', group_showtitle, '$group_url', form_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegroupform_sitegroup_viewform')) {
									hideLeftContainer (form_ads_display, group_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitegroupform_sitegroup_viewform';
								group_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitegroupform_sitegroup_viewform')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitegroupform_sitegroup_viewform'));
                }
							}
            break;
            case 'sitegroupdocument.profile-sitegroupdocuments':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Document, '$tempcontent_id', 'document', 'sitegroupdocument', 'profile-sitegroupdocuments', group_showtitle, 'null', document_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegroupdocument_profile_sitegroupdocuments')) {
									hideLeftContainer (document_ads_display, group_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitegroupdocument_profile_sitegroupdocuments';
								group_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitegroupdocument_profile_sitegroupdocuments')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitegroupdocument_profile_sitegroupdocuments'));
                }
							}
            break;
            case 'sitegroupevent.profile-sitegroupevents':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Event, '$tempcontent_id', 'event', 'sitegroupevent', 'profile-sitegroupevents', group_showtitle,'null', event_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegroupevent_profile_sitegroupevents')) {
									hideLeftContainer (event_ads_display, group_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitegroupevent_profile_sitegroupevents';            
								group_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitegroupevent_profile_sitegroupevents')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitegroupevent_profile_sitegroupevents'));
                }
							}
            break;
            case 'siteevent.contenttype-events':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Event, '$tempcontent_id', 'event', 'siteevent', 'contenttype-events', group_showtitle,'null', event_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_siteevent_contenttype_events')) {
									hideLeftContainer (event_ads_display, group_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_siteevent_contenttype_events';            
								group_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_siteevent_contenttype_events')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_siteevent_contenttype_events'));
                }
							}
            break;
            case 'sitegrouppoll.profile-sitegrouppolls':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Poll, '$tempcontent_id', 'poll', 'sitegrouppoll', 'profile-sitegrouppolls', group_showtitle,'null', poll_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegrouppoll_profile_sitegrouppolls')) {
									hideLeftContainer (poll_ads_display, group_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitegrouppoll_profile_sitegrouppolls';            
								group_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitegrouppoll_profile_sitegrouppolls')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitegrouppoll_profile_sitegrouppolls'));
                }
							}
            break;
            case 'sitegroupmusic.profile-sitegroupmusic':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Music, '$tempcontent_id', 'music', 'sitegroupmusic', 'profile-sitegroupmusic', group_showtitle,'null', music_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegroupmusic_profile_sitegroupmusic')) {
									hideLeftContainer (music_ads_display, group_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitegroupmusic_profile_sitegroupmusic';            
								group_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitegroupmusic_profile_sitegroupmusic')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitegroupmusic_profile_sitegroupmusic'));
                }
							}
            break;
            
            case 'sitegroupmember.profile-sitegroupmembers':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Member, '$tempcontent_id', 'member', 'sitegroupmember', 'profile-sitegroupmembers', group_showtitle,'null', member_ads_display, group_communityad_integration,adwithoutpackage, 'null', 'null', 'null', 'null', 'null', 'null', 'null','$show_option', '$membercategory_ids', '1');
						  	if($('global_content').getElement('.layout_sitegroupmember_profile_sitegroupmember')) {
									hideLeftContainer (member_ads_display, group_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitegroupmember_profile_sitegroupmember';            
								group_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitegroupmember_profile_sitegroupmember')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitegroupmember_profile_sitegroupmember'));
                }
							}
            break;
            
            case 'sitegroupoffer.profile-sitegroupoffers':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Offer, '$tempcontent_id', 'offer', 'sitegroupoffer', 'profile-sitegroupoffers', group_showtitle,'null', offer_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegroupoffer_profile_sitegroupoffers')) {
									hideLeftContainer (offer_ads_display, group_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitegroupoffer_profile_sitegroupoffers';
								group_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitegroupoffer_profile_sitegroupoffers')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitegroupoffer_profile_sitegroupoffers'));
                }
							}
            break;
            case 'sitegroup.discussion-sitegroup':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Discusssion, '$tempcontent_id', 'discussion', 'sitegroup', 'discussion-sitegroup', group_showtitle, 'null', discussion_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegroup_discussion_sitegroup')) {
									hideLeftContainer (discussion_ads_display, group_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitegroup_discussion_sitegroup';
                group_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitegroup_discussion_sitegroup')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitegroup_discussion_sitegroup'));
                }
							}
            break;
            case 'sitegroup.overview-sitegroup':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                hideLeftContainer (overview_ads_display, group_communityad_integration, adwithoutpackage);
                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitegroup_overview_sitegroup';
          
                group_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitegroup_overview_sitegroup')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitegroup_overview_sitegroup'));
                }
							}
            break;
            case 'core.profile-links':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {

                if($('main_tabs').getElement('.tab_layout_core_profile_links')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_core_profile_links'));
                }
								group_showtitle = 0;
                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_core_profile_links';
							}
            break;
            case 'sitegroup.location-sitegroup':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
      					hideLeftContainer (location_ads_display, group_communityad_integration, adwithoutpackage);

                if($('main_tabs').getElement('.tab_layout_sitegroup_location_sitegroup')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitegroup_location_sitegroup'));
                }
								group_showtitle = 0;
                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitegroup_location_sitegroup';
							}
            break;
            case 'sitegroup.info-sitegroup':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
								group_showtitle = 0;
                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitegroup_info_sitegroup';
                if($('main_tabs').getElement('.tab_layout_sitegroup_info_sitegroup')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitegroup_info_sitegroup'));
                }
							}
            break;
          case 'sitegroupintegration.profile-items':       
             if(is_ajax_divhide == '' && "$tab_main" == '') {
               if($newtabid == "$tempcontent_id" && $newtabid != 0) {                
                ShowContent('$tempcontent_id', execute_Request_$resource_type_integration, '$tempcontent_id', 'null', 'sitegroupintegration', 'profile-items', group_showtitle, '$group_url_integration', $ads_display_integration, group_communityad_integration,
  adwithoutpackage, null,null,'$resource_type_integration', null, 1);
                  prev_tab_id = "$newtabid";
                  prev_tab_class = 'layout_sitegroupintegration_profile_items';
                  if($('global_content').getElement('.layout_sitegroupintegration_profile_items')) {
                    $('global_content').getElement('.layout_sitegroupintegration_profile_items').style.display = 'block';
                  }
               }
             }
            break;
            case 'sitegrouptwitter.feeds-sitegrouptwitter':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
								group_showtitle = 0;
                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitegrouptwitter_feeds_sitegrouptwitter';
                if($('main_tabs').getElement('.tab_layout_sitegrouptwitter_feeds_sitegrouptwitter')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitegrouptwitter_feeds_sitegrouptwitter'));
                }
							}
            break;  
            case 'core.html-block':
              tabContainerSwitch($('main_tabs').getElement('.tab_layout_core_html_block'));
              break;
            case 'activity.feed':
              tabContainerSwitch($('main_tabs').getElement('.tab_layout_activity_feed'));
              break;
            case 'seaocore.feed':
              tabContainerSwitch($('main_tabs').getElement('.tab_layout_seaocore_feed'));
              break;
            case 'advancedactivity.home-feeds':
              tabContainerSwitch($('main_tabs').getElement('.tab_layout_advancedactivity_home_feeds'));
              break;
					 }
			    if($('main_tabs').getElement('.tab_$tab_id')){
			      $('main_tabs').getElement('.tab_$tab_id').addEvent('click', function() {

            if($('global_content').getElement('.layout_seaocore_feed')) {
							$('global_content').getElement('.layout_seaocore_feed').id = "layout_seaocore_feed";
							scrollToTopForGroup($('layout_seaocore_feed'));
            } else if($('global_content').getElement('.layout_activity_feed')) {
							$('global_content').getElement('.layout_activity_feed').id = "layout_activity_feed";
							scrollToTopForGroup($('layout_activity_feed'));
            } else if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
							$('global_content').getElement('.layout_advancedactivity_home_feeds').id = "layout_advancedactivity_home_feeds";
							scrollToTopForGroup($('layout_advancedactivity_home_feeds'));
            }
			      if($('profile_status')) {
			        $('profile_status').innerHTML = "<h2>$sitegroup_title</h2>";
            }
            if($('main_tabs').getElement('.tab_layout_activity_feed')) {
              tabContainerSwitch($('main_tabs').getElement('.tab_layout_activity_feed'));
            }

						setLeftLayoutForGroup();
			      prev_tab_id = '$tab_id';

					});
				  }          
          
          if($('main_tabs').getElement('.tab_$tab_id3')){
			      $('main_tabs').getElement('.tab_$tab_id3').addEvent('click', function() {
            if($('main_tabs').getElement('.tab_layout_core_profile_links')) {
              tabContainerSwitch($('main_tabs').getElement('.tab_layout_core_profile_links'));
            }

			      prev_tab_id = '$tab_id3';
						setLeftLayoutForGroup();
            });
				  }
          if($('main_tabs').getElement('.tab_$tab_id4')){
			      $('main_tabs').getElement('.tab_$tab_id4').addEvent('click', function() {
//             if($('main_tabs').getElement('.tab_layout_core_html_block')) {
//               tabContainerSwitch($('main_tabs').getElement('.tab_layout_core_html_block'));
//             }

			      prev_tab_id = '$tab_id4';
						setLeftLayoutForGroup();
            });
				  }
				}
				else
	      {          
	       switch ("$tempcontent_name") {
            case 'sitegroup.photos-sitegroup':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitegroup_title &raquo; $photo </h2>";
								}
								$('global_content').getElement('.layout_sitegroup_photos_sitegroup > h3').innerHTML = "<div class='layout_simple_head'>$sitegroup_title's  $photo</div>";
		            ShowContent('$tempcontent_id', execute_Request_Photo, '$tempcontent_id', 'photo', 'sitegroup', 'photos-sitegroup', group_showtitle, 'null', photo_ads_display, group_communityad_integration, adwithoutpackage,$itemAlbumCount, $itemPhotoCount);
						  	if($('global_content').getElement('.layout_sitegroup_photos_sitegroup')) {
									$('global_content').getElement('.layout_sitegroup_photos_sitegroup').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitegroup_photos_sitegroup';
							  }
							  hideWidgets();
							}
            break;
            case 'sitegroupvideo.profile-sitegroupvideos':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitegroup_title &raquo; $video </h2>";
								}
								$('global_content').getElement('.layout_sitegroupvideo_profile_sitegroupvideos > h3').innerHTML = "<div class='layout_simple_head'>$sitegroup_title's  $video</div>";
		            ShowContent('$tempcontent_id', execute_Request_Video, '$tempcontent_id', 'video', 'sitegroupvideo', 'profile-sitegroupvideos', group_showtitle, 'null', video_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegroupvideo_profile_sitegroupvideos')) {
									$('global_content').getElement('.layout_sitegroupvideo_profile_sitegroupvideos').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitegroupvideo_profile_sitegroupvideos';
							  }
								hideWidgets();
							}
            break;
            case 'sitegroupnote.profile-sitegroupnotes':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitegroup_title &raquo; $note </h2>";
								}
								$('global_content').getElement('.layout_sitegroupnote_profile_sitegroupnotes > h3').innerHTML = "<div class='layout_simple_head'>$sitegroup_title's  $note</div>";
		            ShowContent('$tempcontent_id', execute_Request_Note, '$tempcontent_id', 'note', 'sitegroupnote', 'profile-sitegroupnotes', group_showtitle, 'null', note_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegroupnote_profile_sitegroupnotes')) {
									$('global_content').getElement('.layout_sitegroupnote_profile_sitegroupnotes').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitegroupnote_profile_sitegroupnotes';
							  }
								hideWidgets();
							}
            break;
            case 'sitegroupreview.profile-sitegroupreviews':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitegroup_title &raquo; $review </h2>";
								}
								$('global_content').getElement('.layout_sitegroupreview_profile_sitegroupreviews > h3').innerHTML = "<div class='layout_simple_head'>$sitegroup_title's  $review </div>";
		            ShowContent('$tempcontent_id', execute_Request_Review, '$tempcontent_id', 'review', 'sitegroupreview', 'profile-sitegroupreviews', group_showtitle,'null', review_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegroupreview_profile_sitegroupreviews')) {
									$('global_content').getElement('.layout_sitegroupreview_profile_sitegroupreviews').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitegroupreview_profile_sitegroupreviews';
							  }
								hideWidgets();
							}
            break;
            case 'sitegroupform.sitegroup-viewform':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitegroup_title &raquo; $form </h2>";
								}
								$('global_content').getElement('.layout_sitegroupform_sitegroup_viewform > h3').innerHTML = "<div class='layout_simple_head'>$sitegroup_title's  $form </div>";
		            ShowContent('$tempcontent_id', execute_Request_Form, '$tempcontent_id', 'form', 'sitegroupform', 'sitegroup-viewform', group_showtitle, '$group_url', form_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegroupform_sitegroup_viewform')) {
									$('global_content').getElement('.layout_sitegroupform_sitegroup_viewform').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitegroupform_sitegroup_viewform';
							  }
								hideWidgets();
							}
            break;
            case 'sitegroupdocument.profile-sitegroupdocuments':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitegroup_title &raquo; $document </h2>";
								}
								$('global_content').getElement('.layout_sitegroupdocument_profile_sitegroupdocuments > h3').innerHTML = "<div class='layout_simple_head'>$sitegroup_title's  $document </div>";
		            ShowContent('$tempcontent_id', execute_Request_Document, '$tempcontent_id', 'document', 'sitegroupdocument', 'profile-sitegroupdocuments', group_showtitle, 'null', document_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegroupdocument_profile_sitegroupdocuments')) {
									$('global_content').getElement('.layout_sitegroupdocument_profile_sitegroupdocuments').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitegroupdocument_profile_sitegroupdocuments';
							  }
								hideWidgets();
							}
            break;
            case 'sitegroupevent.profile-sitegroupevents':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitegroup_title &raquo; $event </h2>";
								}
								$('global_content').getElement('.layout_sitegroupevent_profile_sitegroupevents > h3').innerHTML = "<div class='layout_simple_head'>$sitegroup_title's  $event </div>";
		            ShowContent('$tempcontent_id', execute_Request_Event, '$tempcontent_id', 'event', 'sitegroupevent', 'profile-sitegroupevents', group_showtitle,'null', event_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegroupevent_profile_sitegroupevents')) {
									$('global_content').getElement('.layout_sitegroupevent_profile_sitegroupevents').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitegroupevent_profile_sitegroupevents';
							  }
								hideWidgets();
							}
            break;
            case 'sitegrouppoll.profile-sitegrouppolls':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitegroup_title &raquo; $poll </h2>";
								}
								$('global_content').getElement('.layout_sitegrouppoll_profile_sitegrouppolls > h3').innerHTML = "<div class='layout_simple_head'>$sitegroup_title's  $poll </div>";
		            ShowContent('$tempcontent_id', execute_Request_Poll, '$tempcontent_id', 'poll', 'sitegrouppoll', 'profile-sitegrouppolls', group_showtitle,'null', poll_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegrouppoll_profile_sitegrouppolls')) {
									$('global_content').getElement('.layout_sitegrouppoll_profile_sitegrouppolls').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitegrouppoll_profile_sitegrouppolls';
							  }
								hideWidgets();
							}
            break;
            case 'sitegroupmusic.profile-sitegroupmusic':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitegroup_title &raquo; $music </h2>";
								}
								$('global_content').getElement('.layout_sitegroupmusic_profile_sitegroupmusic > h3').innerHTML = "<div class='layout_simple_head'>$sitegroup_title's  $music </div>";
		            ShowContent('$tempcontent_id', execute_Request_Music, '$tempcontent_id', 'music', 'sitegroupmusic', 'profile-sitegroupmusic', group_showtitle,'null', music_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegroupmusic_profile_sitegroupmusic')) {
									$('global_content').getElement('.layout_sitegroupmusic_profile_sitegroupmusic').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitegroupmusic_profile_sitegroupmusic';
							  }
								hideWidgets();
							}
            break;
            
            case 'sitegroupmember.profile-sitegroupmembers':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitegroup_title &raquo; $member </h2>";
								}
								$('global_content').getElement('.layout_sitegroupmember_profile_sitegroupmember > h3').innerHTML = "<div class='layout_simple_head'>$sitegroup_title's  $member </div>";
		            ShowContent('$tempcontent_id', execute_Request_Member, '$tempcontent_id', 'member', 'sitegroupmember', 'profile-sitegroupmembers', group_showtitle,'null', member_ads_display, group_communityad_integration,adwithoutpackage,'null', 'null', 'null', 'null', 'null', 'null', 'null','$show_option', '$membercategory_ids', '1');
						  	if($('global_content').getElement('.layout_sitegroupmember_profile_sitegroupmember')) {
									$('global_content').getElement('.layout_sitegroupmember_profile_sitegroupmember').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitegroupmember_profile_sitegroupmember';
							  }
								hideWidgets();
							}
            break;
            
            
            case 'sitegroupoffer.profile-sitegroupoffers':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitegroup_title &raquo; $offer </h2>";
								}
								$('global_content').getElement('.layout_sitegroupoffer_profile_sitegroupoffers > h3').innerHTML = "<div class='layout_simple_head'>$sitegroup_title's  $offer </div>";
		            ShowContent('$tempcontent_id', execute_Request_Offer, '$tempcontent_id', 'offer', 'sitegroupoffer', 'profile-sitegroupoffers', group_showtitle,'null', offer_ads_display, group_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitegroupoffer_profile_sitegroupoffers')) {
									$('global_content').getElement('.layout_sitegroupoffer_profile_sitegroupoffers').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitegroupoffer_profile_sitegroupoffers';
							  }
								hideWidgets();
							}
            break;
            case 'sitegroup.discussion-sitegroup':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Discusssion, '$tempcontent_id', 'discussion', 'sitegroup', 'discussion-sitegroup', group_showtitle, 'null', discussion_ads_display, group_communityad_integration,adwithoutpackage);
		            if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitegroup_title &raquo; $discussion </h2>";
								}
								$('global_content').getElement('.layout_sitegroup_discussion_sitegroup > h3').innerHTML = "<div class='layout_simple_head'>$sitegroup_title's  $discussion </div>";
						  	if($('global_content').getElement('.layout_sitegroup_discussion_sitegroup')) {
									$('global_content').getElement('.layout_sitegroup_discussion_sitegroup').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitegroup_discussion_sitegroup';
							  }
								hideWidgets();
							}
            break;
            case 'sitegroup.overview-sitegroup':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {

                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitegroup_title &raquo; $overview </h2>";
								}
								$('global_content').getElement('.layout_sitegroup_overview_sitegroup > h3').innerHTML = "<div class='layout_simple_head'>$sitegroup_title's  $overview</div>";

						    if($('global_content').getElement('.layout_sitegroup_overview_sitegroup')) {
									 $('global_content').getElement('.layout_sitegroup_overview_sitegroup').style.display = 'block';
									 prev_tab_id = "$newtabid";
									 prev_tab_class = 'layout_sitegroup_overview_sitegroup';
							  }
								hideWidgetsForModule('sitegroupoverview');
							}
            break;
            case 'core.profile-links':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitegroup_title &raquo; $link </h2>";
								}
								$('global_content').getElement('.layout_core_profile_links > h3').innerHTML = "<div class='layout_simple_head'>$sitegroup_title's  $link</div>";
                hideWidgetsForModule('sitegrouplink');
							}
            break;
            case 'sitegroup.location-sitegroup':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitegroup_title &raquo; $map </h2>";
								}
								$('global_content').getElement('.layout_sitegroup_location_sitegroup > h3').innerHTML = "<div class='layout_simple_head'>$sitegroup_title's $map</div>";
						    if($('global_content').getElement('.layout_sitegroup_location_sitegroup')) {
									 $('global_content').getElement('.layout_sitegroup_location_sitegroup').style.display = 'block';
									 prev_tab_id = "$newtabid";
									 prev_tab_class = 'layout_sitegroup_location_sitegroup';
							  }
								hideWidgetsForModule('sitegrouplocation');
							}
            break;
            case 'sitegroupintegration.profile-items':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {                
                ShowContent('$tempcontent_id', execute_Request_$resource_type_integration, '$tempcontent_id', 'null', 'sitegroupintegration', 'profile-items', group_showtitle, '$group_url_integration', $ads_display_integration, group_communityad_integration,
  adwithoutpackage, null,null,'$resource_type_integration', null, 1);
                  prev_tab_id = "$newtabid";
                  prev_tab_class = 'layout_sitegroupintegration_profile_items';
                  if($('global_content').getElement('.layout_sitegroupintegration_profile_items')) {
                    $('global_content').getElement('.layout_sitegroupintegration_profile_items').style.display = 'block';
                  }
                 hideWidgetsForModule('sitegroupintegration');
               }
            break;          
            case 'activity.feed':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
						    hideWidgetsForModule('sitegroupactivityfeed');

							}
            break;
           case 'seaocore.feed':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                hideWidgetsForModule('sitegroupseaocoreactivityfeed');

								if($('global_content').getElement('.layout_sitegroup_group_cover_information_sitegroup')) { 	                            $('global_content').getElement('.layout_sitegroup_group_cover_information_sitegroup').style.display = 'block';
								}
								if($('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo')) { 	                            $('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo').style.display = 'block';
								}
		
								if($('global_content').getElement('.layout_sitegroupmember_groupcover_photo_sitegroupmembers')) { 	                            	$('global_content').getElement('.layout_sitegroupmember_groupcover_photo_sitegroupmembers').style.display = 'block';
								}
						  }
            break;
           case 'advancedactivity.home-feeds':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                hideWidgetsForModule('sitegroupadvancedactivityactivityfeed');
	
						  }
            break;            
            case 'sitegroup.info-sitegroup':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
						    hideWidgetsForModule('sitegroupinfo');

							}
            break;
            case 'sitegrouptwitter.feeds-sitegrouptwitter':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
						    hideWidgetsForModule('sitegrouptwitter');

							}
            break;
					}
				  if($widgetinformation == 0) {
				    if($('global_content').getElement('.layout_sitegroup_location_sitegroup')) {
							$('global_content').getElement('.layout_sitegroup_location_sitegroup').style.display = 'block';
						}
						if($('global_content').getElement('.layout_sitegroup_info_sitegroup')) {
							$('global_content').getElement('.layout_sitegroup_info_sitegroup').style.display = 'block';
						}

						if($('global_content').getElement('.layout_sitegroup_group_cover_information_sitegroup')) { 	                            $('global_content').getElement('.layout_sitegroup_group_cover_information_sitegroup').style.display = 'block';
					}
 							if($('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo')) { 	                            $('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo').style.display = 'block';
								}
		
						if($('global_content').getElement('.layout_sitegroupmember_groupcover_photo_sitegroupmembers')) { 	                            $('global_content').getElement('.layout_sitegroupmember_groupcover_photo_sitegroupmembers').style.display = 'block';
					}
						if($('global_content').getElement('.layout_core_profile_links')) {
							$('global_content').getElement('.layout_core_profile_links').style.display = 'block';
						}
					}

    			$$('.tab_$tab_id').addEvent('click', function() {
    			  if($('profile_status')) {
    			    $('profile_status').innerHTML = "<h2>$sitegroup_title</h2>";
						}
						if($('global_content').getElement('.layout_sitegroup_location_sitegroup')) {
							$('global_content').getElement('.layout_sitegroup_location_sitegroup').style.display = 'none';
						}
            if($('global_content').getElement('.layout_sitegroup_photos_sitegroup')) {
					    $('global_content').getElement('.layout_sitegroup_photos_sitegroup').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitegroupvideo_profile_sitegroupvideos')) {
					    $('global_content').getElement('.layout_sitegroupvideo_profile_sitegroupvideos').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitegroup_discussion_sitegroup')) {
					    $('global_content').getElement('.layout_sitegroup_discussion_sitegroup').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitegroupoffer_profile_sitegroupoffers')) {
					    $('global_content').getElement('.layout_sitegroupoffer_profile_sitegroupoffers').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitegroupdocument_profile_sitegroupdocuments')) {
					    $('global_content').getElement('.layout_sitegroupdocument_profile_sitegroupdocuments').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitegroupreview_profile_sitegroupreviews')) {
					    $('global_content').getElement('.layout_sitegroupreview_profile_sitegroupreviews').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitegrouppoll_profile_sitegrouppolls')) {
					    $('global_content').getElement('.layout_sitegrouppoll_profile_sitegrouppolls').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitegroupnote_profile_sitegroupnotes')) {
					    $('global_content').getElement('.layout_sitegroupnote_profile_sitegroupnotes').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitegroupevent_profile_sitegroupevents')) {
					    $('global_content').getElement('.layout_sitegroupevent_profile_sitegroupevents').style.display = 'none';
            }
            if($('global_content').getElement('.layout_siteevent_contenttype_events')) {
					    $('global_content').getElement('.layout_siteevent_contenttype_events').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitegroupintegration_profile_items')) {					    
              $$('.layout_sitegroupintegration_profile_items').setStyle('display', 'none');
            } 
            if($('global_content').getElement('.layout_sitegroupintegration_profile_items')) {
					    $('global_content').getElement('.layout_sitegroupintegration_profile_items').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitegroupmusic_profile_sitegroupmusic')) {
					    $('global_content').getElement('.layout_sitegroupmusic_profile_sitegroupmusic').style.display = 'none';
            }
      			if($('global_content').getElement('.layout_sitegroupform_sitegroup_viewform')) {
						  $('global_content').getElement('.layout_sitegroupform_sitegroup_viewform').style.display = 'none';
            }
						if($('global_content').getElement('.layout_core_profile_links')) {
							$('global_content').getElement('.layout_core_profile_links').style.display = 'none';
						}
						if($('global_content').getElement('.layout_sitegroup_info_sitegroup')) {
							$('global_content').getElement('.layout_sitegroup_info_sitegroup').style.display = 'none';
						}
// 						if($('global_content').getElement('.layout_sitegrouptwitter_feeds_sitegrouptwitter')) {
// 							$('global_content').getElement('.layout_sitegrouptwitter_feeds_sitegrouptwitter').style.display = 'none';
// 						}
						if($('global_content').getElement('.layout_sitegroup_overview_sitegroup')) {
							$('global_content').getElement('.layout_sitegroup_overview_sitegroup').style.display = 'none';
					  }
						if($('global_content').getElement('.layout_activity_feed')) {
							$('global_content').getElement('.layout_activity_feed').style.display = 'block';
						}
        	  if($('global_content').getElement('.layout_seaocore_feed')) {
							$('global_content').getElement('.layout_seaocore_feed').style.display = 'block';
						}
            if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
							$('global_content').getElement('.layout_advancedactivity_home_feeds').style.display = 'block';
						}

						setLeftLayoutForGroup();
            if($('global_content').getElement('.layout_seaocore_feed')) {
							$('global_content').getElement('.layout_seaocore_feed').id = "layout_seaocore_feed";
							scrollToTopForGroup($('layout_seaocore_feed'));
            } else if($('global_content').getElement('.layout_activity_feed')) {
							$('global_content').getElement('.layout_activity_feed').id = "layout_activity_feed";
							scrollToTopForGroup($('layout_activity_feed'));
            } else if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
							$('global_content').getElement('.layout_advancedactivity_home_feeds').id = "layout_advancedactivity_home_feeds";
							scrollToTopForGroup($('layout_advancedactivity_home_feeds'));
            }


//				    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '$tab_id') {
//				      $('id_' + prev_tab_id).style.display = "none";
//				    }
			    prev_tab_id = '$tab_id';
				 });

  			$$('.tab_$tab_id3').addEvent('click', function() {
  			  if($('profile_status')) {
  			    $('profile_status').innerHTML = "<h2>$sitegroup_title &raquo; $link </h2>";
					}
					$('global_content').getElement('.layout_core_profile_links > h3').innerHTML = "<div class='layout_simple_head'>$sitegroup_title's $link</div>";

					if($('global_content').getElement('.layout_sitegroup_location_sitegroup')) {
						$('global_content').getElement('.layout_sitegroup_location_sitegroup').style.display = 'none';
					}
					if($('global_content').getElement('.layout_core_profile_links')) {
						$('global_content').getElement('.layout_core_profile_links').style.display = 'block';
					}
					if($('global_content').getElement('.layout_sitegroup_info_sitegroup')) {
						$('global_content').getElement('.layout_sitegroup_info_sitegroup').style.display = 'none';
					}

					if($('global_content').getElement('.layout_activity_feed')) {
						$('global_content').getElement('.layout_activity_feed').style.display = 'none';
					}
        	if($('global_content').getElement('.layout_seaocore_feed')) {
						$('global_content').getElement('.layout_seaocore_feed').style.display = 'none';
					}
          if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
					 	$('global_content').getElement('.layout_advancedactivity_home_feeds').style.display = 'none';
					}
					if($('global_content').getElement('.layout_sitegroup_overview_sitegroup')) {
						$('global_content').getElement('.layout_sitegroup_overview_sitegroup').style.display = 'none';
				  }
			    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '$tab_id3') {
			      $('id_' + prev_tab_id).style.display = "none";
			    }
		    prev_tab_id = '$tab_id3';
		 	});        
     
          
     }
	 });
// 	 window.addEvent('domready', function() {
// 
//       if($('thumb_icon')) {
// 	      if($currenttabid == 0) {
// 	       $('thumb_icon').style.display = 'none';
// 			  }
// 	    }
// 		});
EOF;
          }
          if ("$tempcontent_name" == 'sitegroup.discussion-sitegroup' || "$tempcontent_name" == 'sitegroup.photos-sitegroup' || "$tempcontent_name" == 'sitegroupvideo.profile-sitegroupvideos' || "$tempcontent_name" == 'sitegroupnote.profile-sitegroupnotes' || "$tempcontent_name" == 'sitegroupreview.profile-sitegroupreviews' || "$tempcontent_name" == 'sitegroupform.sitegroup-viewform' || "$tempcontent_name" == 'sitegroupdocument.profile-sitegroupdocuments' || "$tempcontent_name" == 'sitegroupevent.profile-sitegroupevents' || "$tempcontent_name" == 'sitegrouppoll.profile-sitegrouppolls' || "$tempcontent_name" == 'sitegroupmusic.profile-sitegroupmusic' || "$tempcontent_name" == 'sitegroupmember.profile-sitegroupmembers' || "$tempcontent_name" == 'sitegroupoffer.profile-sitegroupoffers' || "$tempcontent_name" == 'sitegrouptwitter.feeds-sitegrouptwitter' || "$tempcontent_name" == 'sitevent.contenttype-events') {
            Engine_Api::_()->sitegroup()->showAdWithPackage($sitegroup);
            $view->headScript()
                    ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/scripts/hideWidgets.js');
          }

          $view->headScript()
                  ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/scripts/hideTabs.js')
                  ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/scripts/core.js');

          $view->headScript()
                  ->appendScript("   var group_communityads = '$group_communityads';
	    var contentinformation = '$contentinformation';
	    var group_showtitle = 0;
	    var prev_tab_class = '';");
          if (!empty($script)) {
            $view->headScript()
                    ->appendScript($script);
          }
        }
      }
    }
  }

  public function onStatistics($event) {
    $table = Engine_Api::_()->getDbTable('groups', 'sitegroup');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'group');
  }

  public function onUserDeleteBefore($event) {
    $payload = $event->getPayload();

    if ($payload instanceof User_Model_User) {

      $user_id = $payload->getIdentity();

      //GET GROUP TABLE
      $sitegroupTable = Engine_Api::_()->getDbtable('groups', 'sitegroup');

      Engine_Api::_()->getDbtable('claims', 'sitegroup')->delete(array('user_id =?' => $user_id));

      Engine_Api::_()->getDbtable('listmemberclaims', 'sitegroup')->delete(array('user_id = ?' => $user_id));

      Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->delete(array('user_id = ?' => $user_id));

      //START ALBUM CODE
      $table = Engine_Api::_()->getItemTable('sitegroup_photo');
      $select = $table->select()->where('user_id = ?', $user_id);
      $rows = $table->fetchAll($select);
      if (!empty($rows)) {
        foreach ($rows as $photo) {
          $photo->delete();
        }
      }

      $table = Engine_Api::_()->getItemTable('sitegroup_album');
      $select = $table->select()->where('owner_id = ?', $user_id);
      $rows = $table->fetchAll($select);
      if (!empty($rows)) {
        foreach ($rows as $album) {
          $album->delete();
        }
      }
      //END ALBUM CODE
      //START DISUCSSION CODE
      $sitegroupDiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion');
      if ($sitegroupDiscussionEnabled) {

        $table = Engine_Api::_()->getItemTable('sitegroup_topic');
        $select = $table->select()->where('user_id = ?', $user_id);
        $rows = $table->fetchAll($select);
        if (!empty($rows)) {
          foreach ($rows as $topic) {
            $topic->delete();
          }
        }

        $table = Engine_Api::_()->getItemTable('sitegroup_post');
        $select = $table->select()->where('user_id = ?', $user_id);
        $rows = $table->fetchAll($select);
        if (!empty($rows)) {
          foreach ($rows as $post) {
            $post->delete();
          }
        }

        Engine_Api::_()->getDbtable('topicwatches', 'sitegroup')->delete(array('user_id = ?' => $user_id));
      }
      //END DISUCSSION CODE

      $sitegroupSelect = $sitegroupTable->select()->where('owner_id = ?', $user_id);

      foreach ($sitegroupTable->fetchAll($sitegroupSelect) as $sitegroup) {
        Engine_Api::_()->sitegroup()->onGroupDelete($sitegroup->group_id);
      }

      //LIKE COUNT DREASE FORM GROUP TABLE.
      $likesTable = Engine_Api::_()->getDbtable('likes', 'core');
      $likesTableSelect = $likesTable->select()->where('poster_id = ?', $payload->getIdentity())->Where('resource_type = ?', 'sitegroup_group');
      $results = $likesTable->fetchAll($likesTableSelect);
      foreach ($results as $user) {
        $resource = Engine_Api::_()->getItem('sitegroup_group', $user->resource_id);
        $resource->like_count--;
        $resource->save();
      }
    }
  }

  public function addActivity($event) {
    $payload = $event->getPayload();
    $subject = $payload['subject'];
    $object = $payload['object'];

    // Only for object=event
    if (strpos( $payload['type'],'like_')===false && $object instanceof Sitegroup_Model_Group /* &&
      Engine_Api::_()->authorization()->context->isAllowed($object, 'member', 'view') */) {
      $event->addResponse(array(
          'type' => 'sitegroup_group',
          'identity' => $object->getIdentity()
      ));
    }
  }

  public function getActivity($event) {
    // Detect viewer and subject
    $payload = $event->getPayload();
    $user = null;
    $subject = null;
    if ($payload instanceof User_Model_User) {
      $user = $payload;
    } else if (is_array($payload)) {
      if (isset($payload['for']) && $payload['for'] instanceof User_Model_User) {
        $user = $payload['for'];
      }
      if (isset($payload['about']) && $payload['about'] instanceof Core_Model_Item_Abstract) {
        $subject = $payload['about'];
      }
    }
    if (null === $user) {
      $viewer = Engine_Api::_()->user()->getViewer();
      if ($viewer->getIdentity()) {
        $user = $viewer;
      }
    }
    if (null === $subject && Engine_Api::_()->core()->hasSubject()) {
      $subject = Engine_Api::_()->core()->getSubject();
    }


    // Get like groups
    if ($user && empty($subject)) {
      $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');
      if ($settingsCoreApi->sitegroup_feed_type && $settingsCoreApi->sitegroup_feed_onlyliked) {
        $data = Engine_Api::_()->sitegroup()->getMemberLikeGroupsOfIds($user);
        if (!empty($data) && is_array($data)) {
          $event->addResponse(array(
              'type' => 'sitegroup_group',
              'data' => $data,
          ));
        }
      }  else if (!$settingsCoreApi->sitegroup_feed_onlyliked && Engine_Api::_()->hasModuleBootstrap('sitegroupmember')) {
         $data = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getMembershipsOfIds($user);
        if (!empty($data) && is_array($data)) {
          $event->addResponse(array(
              'type' => 'sitegroup_group',
              'data' => $data,
          ));
        }
      }
      else if (!$settingsCoreApi->sitegroup_feed_onlyliked && Engine_Api::_()->hasModuleBootstrap('sitegroupmember')) {
         $data = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getMembershipsOfIds($user);
        if (!empty($data) && is_array($data)) {
          $event->addResponse(array(
              'type' => 'sitegroup_group',
              'data' => $data,
          ));
        }
      }      
    } else if ($subject && ($subject->getType() == 'sitegroup_group')) {
      $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');
      if ($settingsCoreApi->sitegroup_feed_type || 1) {
        $event->addResponse(array(
            'type' => 'sitegroup_group',
            'data' => array($subject->getIdentity()),
        ));
      }
    } else if ($subject && ($subject->getType() == 'user')) {
      
     	if (Engine_Api::_()->hasModuleBootstrap('sitegroupmember')) {
            $data = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getMembershipsOfIds($user);
        } else {
            $data = Engine_Api::_()->getApi('subCore', 'sitegroup')->getMemberFeedsForGroupOfIds($subject);
        }
     $event->addResponse(array(
          'type' => 'sitegroup_group',
          'data' => $data,
      ));
    }
  }

  public function onActivityActionCreateAfter($event) {
	
		$payload = $event->getPayload();		
		if ($payload->object_type == 'sitegroup_group' && ($payload->getTypeInfo()->type == 'sitegroup_post_self' || $payload->getTypeInfo()->type == 'sitegroup_post') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {

			$viewer = Engine_Api::_()->user()->getViewer();
			$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
			
			$notidicationSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.feed.type', 0);
			
			$group_id = $payload->getObject()->group_id;
			$user_id = $payload->getSubject()->user_id;

			$subject = Engine_Api::_()->getItem('sitegroup_group', $group_id);
			$owner = $subject->getOwner();

			$notifications = Engine_Api::_()->getDbtable('notifications', 'activity');

			//previous notification is delete.
			$notifications->delete(array('type =?' => "sitegroup_notificationpost", 'object_type = ?' => "sitegroup_group", 'object_id = ?' => $group_id, 'subject_id = ?' => $user_id));

			//GET GROUP TITLE
			$grouptitle = $subject->title;

			//GROUP URL
			$group_url = Engine_Api::_()->sitegroup()->getGroupUrl($subject->group_id);

			//GET GROUP URL
			$group_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true);

			//MAKING GROUP TITLE LINK
			$group_title_link = '<a href="'.$group_baseurl.'">'.$grouptitle.'</a>';

			//GET LOGGED IN USER INFORMATION
			$viewer = Engine_Api::_()->user()->getViewer();

			//Poster title and photo with link.
			$posterTitle = $viewer->getTitle();
			$posterUrl = $viewer->getHref();
			$poster_baseurl = 'http://' . $_SERVER['HTTP_HOST']. $posterUrl;
			$poster_title_link = "<a href='$poster_baseurl' style='font-weight:bold;text-decoration:none;'>" . $posterTitle . " </a>";

			$moduletype = 'user';
                        //GETTING THE USER PHOTO.
                        $file = $viewer->getPhotoUrl('thumb.icon');
                       if (empty($file)) {
                         $photo = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/application/modules/'.  ucfirst($moduletype).'/externals/images/nophoto_'.$moduletype.'_thumb_icon.png';
                       } else {
                          if (strpos($file, 'http') === FALSE) 
                                                   $photo = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $file;
                                     else
                                       $photo = $file;
                       }
			$image = "<img src='$photo' />";
			
			$post_baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . $payload->getHref();
			$posted_your_group = ' '.$view->translate('posted in group:').' ';
			$post = $posterTitle . $posted_your_group . $grouptitle;
			$postbody = $payload->body;
			$body_content = "<table cellspacing='0' cellpadding='0' border='0' style='border-collapse:collapse;' width='90%'><tr><td style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:20px;background-color:#fff;border-left:none;border-right:none;border-top:none;border-bottom:none;'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;' width='100%'><tr><td colspan='2' style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;border-bottom:1px solid #dddddd;padding-bottom:5px;'><a style='font-weight:bold;margin-bottom:10px;text-decoration:none;' href='$post_baseUrl'>" . $post . "</a></td></tr><tr><td valign='top' style='padding:10px 15px 10px 10px;'><a href='$poster_baseurl'  >" . $image . " </a></td><td valign='top' style='padding-top:10px;font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;width:100%;text-align:left;'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;width:100%;'><tr><td style='font-
			size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;'>" .$poster_title_link. "<br /><span style='color:#333333;margin-top:5px;display:block;'>" . $postbody . "</span></td></tr></table></td></tr></table></td></tr></table>";

			$manageTable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');
			$payload_body = strip_tags($payload->body);
			$payload_body = Engine_String::strlen($payload_body) > 50 ? Engine_String::substr($payload_body, 0, (53 - 3)) . '...' : $payload_body;

			//FETCH DATA
			$manageAdminsIds = $manageTable->getManageAdmin($group_id, $user_id);
			$sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');

			foreach ($manageAdminsIds as $value) {
				$action_notification = unserialize($value['action_notification']);
				$user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
				if (empty($sitegroupmemberEnabled)) {
					if (!empty($value['notification']) && in_array('posted', $action_notification)) {
						$row = $notifications->createRow();
						$row->user_id = $user_subject->getIdentity();
						$row->subject_type = $viewer->getType();
						$row->subject_id = $viewer->getIdentity();
						$row->object_type = $subject->getType();
						$row->object_id = $subject->getIdentity();
						$row->type = 'sitegroup_notificationpost';
						$row->params = null;
						$row->date = date('Y-m-d H:i:s');
						$row->save();
					}

					//EMAIL SEND TO ALL MANAGEADMINS.
					$action_email = json_decode($value['action_email']);
          if (!empty($value['email']) && in_array('posted', $action_email)) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, 'SITEGROUP_POSTNOTIFICATION_EMAIL', array(
            'group_title' => $grouptitle,
            'body_content' => $body_content,
            'post_body_body' => $payload_body,
            ));
          }
				}
			}

		  //START SEND EMAIL TO ALL MEMBER WHO HAVE JOINED THE GROUP INCLUDE MANAGE ADMINS.
      if (!empty($sitegroupmemberEnabled)) {
        $membersIds = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getJoinMembers($group_id, $viewer->getIdentity(), $viewer->getIdentity(), 0, 1);
        foreach ($membersIds as $value) {
          $action_email = json_decode($value['action_email']);
          $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
          if (!empty($value['email_notification']) && $action_email->emailposted == 1) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, 'SITEGROUP_POSTNOTIFICATION_EMAIL', array(
            'group_title' => $grouptitle,
            'body_content' => $body_content,
            'post_body_body' => $payload_body,
            ));
          }
          elseif(!empty($value['email_notification']) && $action_email->emailposted == 2) {
						$friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
            if(in_array($value['user_id'], $friendId)) {
							Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, 'SITEGROUP_POSTNOTIFICATION_EMAIL', array(
							'group_title' => $grouptitle,
							'body_content' => $body_content,
							'post_body_body' => $payload_body,
							));
						}
          }
        }
      }
			//END SEND EMAIL TO ALL MEMBER WHO HAVE JOINED THE GROUP INCLUDE MANAGE ADMINS.

			//START NOTIFICATION TO ALL FOLLOWERS.
			$isGroupAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->isGroupAdmins($viewer->getIdentity(), $group_id);
			if (!empty($isGroupAdmins)) {
				$followersIds = Engine_Api::_()->getDbTable('follows', 'seaocore')->getFollowers('sitegroup_group', $group_id, $viewer->getIdentity());
				$notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
				if (!empty($followersIds)) {
					//previous notification is delete.
					$notificationsTable->delete(array('type =?' => "sitegroup_notificationpost", 'object_type = ?' => "sitegroup_group", 'object_id = ?' => $group_id, 'subject_id = ?' => $group_id, 'subject_type = ?' => 'sitegroup_group'));
					foreach ($followersIds as $value) {
						$user_subject = Engine_Api::_()->user()->getUser($value['poster_id']);
						$row = $notificationsTable->createRow();
						$row->user_id = $user_subject->getIdentity();
						if (!empty($notidicationSettings)) {
							$row->subject_type = $subject->getType();
							$row->subject_id = $subject->getIdentity();
						}
						else {
							$row->subject_type = $viewer->getType();
							$row->subject_id = $viewer->getIdentity();
						}
						$row->type = "sitegroup_notificationpost";
						$row->object_type = $subject->getType();
						$row->object_id = $subject->getIdentity();
						$row->params = null;
						$row->date = date('Y-m-d H:i:s');
						$row->save();
					}
				}
			}
			//END NOTIFICATION TO ALL FOLLOWERS.
		}
  }
}
