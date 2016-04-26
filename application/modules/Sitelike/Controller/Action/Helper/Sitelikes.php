<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Sitelikes.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelike_Controller_Action_Helper_Sitelikes extends Zend_Controller_Action_Helper_Abstract {

  function postDispatch() {

		//GET THE USER ID.
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $front = Zend_Controller_Front::getInstance();

		//GET THE CURRENT MODULE NAME.
    $module = $front->getRequest()->getModuleName();
		//GET THE CURRENT ACTION.
    $action = $front->getRequest()->getActionName();
		//GET THE CURRENT CONTROLLER.
    $controller = $front->getRequest()->getControllerName();
    $view = $this->getActionController()->view;
    $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

    //SET THE ACTIVITY FEED.
    if ((($module == 'core' || $module == 'seaocore') && $controller == 'comment' && $action == 'like') || (($module == 'activity' ||
$module == 'advancedactivity' ) && $controller == 'index' && $action == 'like')) {

			//GET THE COMMENT ID.
      $comment_id = $front->getRequest()->getParam('comment_id', null);
			//CHECK THE COMMENT ID.
      if (empty($comment_id)) {
        $subject = Engine_Api::_()->user()->getViewer();

				if (Engine_Api::_()->core()->hasSubject())
				$object = Engine_Api::_()->core()->getSubject();
				$action_id = $front->getRequest()->getParam('action_id', null);
				$canAddFeed = true;
				if (!empty($action_id)) {
					$canAddFeed = false;
					$api = Engine_Api::_()->getDbtable('actions', 'activity');
					$action = $api->getActionById($action_id);
					$object = $action->getObject();
					$commentable = $action->getTypeInfo()->commentable;
					if ($commentable == 3)
						$canAddFeed = true;
				}
				if ($canAddFeed ) {
					Engine_Api::_()->sitelike()->setLikeFeed($subject, $object);
				}
			}
		}

    //REMOVE THE ACTIVITY FEED.
    if ((($module == 'core' || $module == 'seaocore') && $controller == 'comment' && $action == 'unlike') || (($module == 'activity'
|| $module == 'advancedactivity' ) && $controller == 'index' && $action == 'unlike')) {
			//GET THE COMMENT ID.
      $comment_id = $front->getRequest()->getParam('comment_id');
			//CHECK THE COMMENT ID.
      if (empty($comment_id)) {
        $subject = Engine_Api::_()->user()->getViewer();

        if (Engine_Api::_()->core()->hasSubject())
        $object = Engine_Api::_()->core()->getSubject();

        $action_id = $front->getRequest()->getParam('action_id', null);
        $canRemoveFeed = true;
        if (!empty($action_id)) {
          $canRemoveFeed = false;
          $api = Engine_Api::_()->getDbtable('actions', 'activity');
          $action = $api->getActionById($action_id);
          $object = $action->getObject();
          $commentable = $action->getTypeInfo()->commentable;
          if ($commentable == 3)
            $canRemoveFeed = true;
        }
        if ($canRemoveFeed) {
          Engine_Api::_()->sitelike()->removeLikeFeed($subject, $object);
        }
      }
    }

		$sitelikeButtonLikeUpdatefile = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelike.button.likeupdatefile', 1);
// 		if (empty($sitelikeButtonLikeUpdatefile)) {
// 
// 			$view->headLink()
// 				->prependStylesheet(Zend_Controller_Front::getInstance()->getBaseUrl() . '/sitelike/index/likesettingcss');
// 		}	else {
// 			$view->headLink()
// 	      ->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/styles/likesettings.css');
// 		}
    // Here we are showing Link in forum view page where user can suggest his friend.
    if ($module == 'forum' && $controller == 'topic' && $action == 'view') {
      $forum_is_allow = Engine_Api::_()->getApi('settings', 'core')->getSetting('like.forum.show');
      if (!empty($forum_is_allow) && !empty($user_id)) {
        $view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/scripts/core.js');
        $MODULE_NAME = 'sitelike';
        $RESOURCE_TYPE = 'forum_topic';
        $RESOURCE_ID = Zend_Controller_Front::getInstance()->getRequest()->getParam('topic_id', null);
        // This function check that, This content like or not.
        $check_availability = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($RESOURCE_TYPE, $RESOURCE_ID);
        
        if (!empty($check_availability)) {
          $like_id = $check_availability[0]['like_id'];
          $label = $view->translate('Unlike');
          $label_class = "like_thumbdown_icon";
          $like_unlike_url = $base_url . '/sitelike/index/globallikes/resource_type/' . $RESOURCE_TYPE . '/resource_id/' . $RESOURCE_ID . '/like_id/' . $check_availability[0]['like_id'] . '/smoothbox/0';
        } else {
          $like_id = 0;
          $label = $view->translate('Like');
          $label_class = "like_thumbup_icon";
          $like_unlike_url = $base_url . '/sitelike/index/globallikes/resource_type/' . $RESOURCE_TYPE . '/resource_id/' . $RESOURCE_ID . '/like_id/0/smoothbox/0';
        }
        $forum = "'forum_topic'";
        $like_unlike_label = '<div class="sitelike_button" id="like_button" ><a id="display_num_of_like1" href="javascript:void(0);"><i class="' . $label_class . '"></i><span>' . $label . '</span></i></a><input type="hidden" id="forum_topic_like_' . $RESOURCE_ID . '"  value = "' . $like_id . '" />';
        $script = <<<EOF
				window.addEvent('domready', function()
				{ 
					if ($('display_like_unlike_link') == null) {
						var child_node = $('global_content').getElement('.forum_topic_title_wrapper');
						var newdiv = document.createElement('div');
						newdiv.id = 'display_like_unlike_link';
						newdiv.innerHTML = '{$like_unlike_label}';
						child_node.parentNode.insertBefore(newdiv, child_node);
					}
					$('display_num_of_like1').addEvent('click', function()
					{ 						
						forums_likes('{$RESOURCE_ID}', 'forum_topic', 'forum_topic' );
					});
				});
EOF;
        $view->headScript()->appendScript($script);


        // Find out the number of like in table for this content.
        $num_of_like = Engine_Api::_()->getApi('like', 'seaocore')->likeCount($RESOURCE_TYPE, $RESOURCE_ID);
        if (!empty($num_of_like)) {
          $number_of_like_url = $base_url . '/sitelike/index/likelist/resource_type/' . $RESOURCE_TYPE . '/resource_id/' . $RESOURCE_ID;
          $label = $view->translate(array('%s Person Likes This', '%s People Like This', $num_of_like), $view->locale()->toNumber($num_of_like));
          $number_of_like_label = '<a href="javascript:void(0);" >' . $view->translate($label) . '</a>';
          $script = <<<EOF
					window.addEvent('domready', function()
					{ 
						if ($('display_num_of_like') == null) {
							var child_node = $('global_content').getElement('.global_form');
							var newdiv = document.createElement('div');
							newdiv.id = 'likes_forum_num_likes';
							newdiv.innerHTML = '{$number_of_like_label}';
							child_node.parentNode.insertBefore(newdiv, child_node);
						}
						$('likes_forum_num_likes').addEvent('click', function()
						{ 						
							Smoothbox.open ('{$number_of_like_url}');
						}); 
					});
EOF;
          $view->headScript()->appendScript($script);
        }
      }
    }
	}
}