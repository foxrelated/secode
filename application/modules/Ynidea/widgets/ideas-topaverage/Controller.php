<?php

class Ynidea_Widget_IdeasTopaverageController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
       // Process form
        $limit = 15;
        if($this->_getParam('number') != '' && $this->_getParam('number') >= 0)
        {
            $limit = $this->_getParam('number');
        }
        $params = array();
		$params['limit'] = $limit;
        $params['orderby'] = 'vote_ave';
		$params['direction'] = 'DESC';
        $paginator = Engine_Api::_()->getApi('core', 'ynidea')->getIdeaPaginator($params);
        if(!$paginator->getTotalItemCount()) 
		{
			$this->setNoRender();
		} 
		$this->view->list_idea_html = $this->view->partial(Ynidea_Api_Core::partialViewFullPath('_list_idea.tpl'), array('arr_ideas'=>$paginator,'orderby'=>$params['orderby']));
    }

}
