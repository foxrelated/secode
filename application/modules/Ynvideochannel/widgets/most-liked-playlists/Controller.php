<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Widget_MostLikedPlaylistsController extends Engine_Content_Widget_Abstract
{
    public function indexAction() {
        $params= array(
            'order' => 'most_liked',
            'search' => 1
        );
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('ynvideochannel_playlist')->getPlaylistsPaginator($params);
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 6));
    }
}
