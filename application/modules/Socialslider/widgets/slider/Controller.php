<?php

/**
 * SocialEnginePro
 *
 * @category   Application_Extensions
 * @package    
 * @author     Isabek
 */
/**
 * @category   Application_Extensions
 * @package    
 */

/**
 *  $enable
 *  0 => all
 *  1 => registered members
 *  2 => unregistered members
 */
class Socialslider_Widget_SliderController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('socialslider.enable', 1);

        if ($enable == 0 || empty($enable)) {
            return $this->setNoRender();
        }

        $show = Engine_Api::_()->getApi('settings', 'core')->getSetting('socialslider.show', 0);

        if ($show != 0) {

            $viewer = Engine_Api::_()->user()->getViewer();

            if (($show == 1 && !$viewer->getIdentity()) || ($show == 2 && $viewer->getIdentity())) {
                return $this->setNoRender();
            }
        }

        $this->view->location = Engine_Api::_()->getApi('settings', 'core')->getSetting('socialslider.location', 'right');
        $buttonsTable = Engine_Api::_()->getDbtable('buttons', 'socialslider');
        $select = $buttonsTable->getSelect();

        if ($select === null) {
            return $this->setNoRender();
        }


        $paginator = Zend_Paginator::factory($select);
        $this->view->buttons = $buttons = $paginator;

        $username = $buttonsTable->isShowYoutube();

        if (!empty($username) && $username !== NULL) {
            $videos = $this->getVideoFeed($username->button_code);
            $this->view->videos = $videos;
        }
    }

    private function getVideoFeed($username) {

        $yt = new Zend_Gdata_YouTube();
        $yt->setMajorProtocolVersion(2);

        $yt->newVideoQuery()->setOrderBy('VIEW_COUNT');

        $videoFeed = $yt->getUserUploads($username);

        $tmp = array();
        $count = 1;

        foreach ($videoFeed as $videoEntry) {
            $tmp[] = $this->printVideoEntry($videoEntry);

            if ($count == 10)
                break;
            $count++;
        }
        return $tmp;
    }

    private function printVideoEntry($videoEntry) {

        $tmp = $videoEntry->getVideoThumbnails();

        return array($videoEntry->getVideoTitle(),
            $videoEntry->getVideoViewCount(),
            $tmp[4]['url'],
            $videoEntry->getVideoWatchPageUrl());
    }

}
