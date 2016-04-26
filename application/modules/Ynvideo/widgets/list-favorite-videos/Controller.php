<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Widget_ListFavoriteVideosController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $marginLeft = $this->_getParam('marginLeft', '');
        if (!empty($marginLeft)) {
            $this->view->marginLeft = $marginLeft;
        }
        $this->view->viewType = $this->_getParam('viewType', 'small');
        $numberOfVideos = $this->_getParam('numberOfVideos', 4);
        
        $table = Engine_Api::_()->getItemTable('video');
        $select = $table->select()
                ->where('search = ?', 1)
                ->where('status = ?', 1);
        $select->order('favorite_count DESC');
        $select->limit($numberOfVideos);
        
        $this->view->videos = $videos = $table->fetchAll($select);        
		$this ->view -> height = $this -> _getParam('height', 160);
		$this ->view -> width = $this -> _getParam('width', 160);
		$this ->view -> margin_left = $this -> _getParam('margin_left', 0);

        // Hide if nothing to show
        if ($videos->count() <= 0) {
            return $this->setNoRender();
        }
    }
}