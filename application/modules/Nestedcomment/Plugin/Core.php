<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Nestedcomment_Plugin_Core extends Zend_Controller_Plugin_Abstract {

    public function onRenderLayoutDefault($event) {
        $view = $event->getPayload();
        $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_comment.css')
                ->appendStylesheet($view->layout()->staticBaseUrl
                        . 'application/modules/Nestedcomment/externals/styles/style_nestedcomment.css');
        $view->headTranslate(array('Write a comment...', 'Write a reply...', 'Attach a Photo', 'Post a comment...', 'Post a reply...'));
        $view->headScript()
                ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/composernestedcomment.js');
        $view->headScript()
                ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/composer_nested_comment_tag.js');
        $view->headScript()
                ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/comment_photo.js');
        $view->headScript()
                ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/core.js')
                ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/composer.js')
                ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/composer_tag.js')
                ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/like.js')
                ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/composer_photo.js')
                ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/composer_link.js');
    }

    public function onRenderLayoutMobileDefault($event) {
        return $this->onRenderLayoutDefault($event);
    }

}
