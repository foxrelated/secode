<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Widget_ShowSamePosterController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        // Check subject
        if (!Engine_Api::_()->core()->hasSubject('video')) {
            return $this->setNoRender();
        }        
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('video');

        // Set stitle
        $viewer = $subject->getOwner();
        $viewerTextLink = '<a href="' . $viewer->getHref() . '" title="' . $viewer->getTitle() . '">' 
            . $this->view->string()->truncate($viewer->getTitle(), 15) . '</a>';
                
        $this->getElement()->setTitle($viewerTextLink . Zend_Registry::get('Zend_Translate')->_("'s Other Videos"));

        // Get tags for this video
        $itemTable = Engine_Api::_()->getItemTable($subject->getType());

        $select = $itemTable->select()
                ->from($itemTable)
                ->where('owner_id = ?', $subject->owner_id)
                ->where('video_id != ?', $subject->getIdentity())
                ->where('search = ?', true) // ?
        ;

        // Get paginator
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 4));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Hide if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        }
		$this ->view -> height = $this -> _getParam('height', 160);
		$this ->view -> width = $this -> _getParam('width', 160);
		$this ->view -> margin_left = $this -> _getParam('margin_left', 0);
    }

}