<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Widget_ShowSameCategoriesController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        // Check subject
        if (!Engine_Api::_()->core()->hasSubject('video')) {
            return $this->setNoRender();
        }
        
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('video');

        // Set default title
        if (!$this->getElement()->getTitle()) {
            $this->getElement()->setTitle('Related Videos');
        }

        $numberOfVideos = $this->_getParam('numberOfVideos', 5);
        $itemTable = Engine_Api::_()->getItemTable($subject->getType());

        // Get other with same tags
        $select = $itemTable->select()
                ->where('category_id = ?', $subject->category_id)
                ->limit($numberOfVideos)
                ->order(new Zend_Db_Expr(('rand()')));
        
        $this->view->videos = $itemTable->fetchAll($select);
		
		$this ->view -> height = $this -> _getParam('height', 160);
		$this ->view -> width = $this -> _getParam('width', 160);
		$this ->view -> margin_left = $this -> _getParam('margin_left', 0);
    }
}