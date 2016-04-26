<?php

class Ynidea_Widget_CycleIdeasController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
    	$headScript = new Zend_View_Helper_HeadScript();
   		$headScript -> appendFile('//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js');
        $headScript -> appendFile('application/modules/Ynidea/externals/scripts/jquery.bxslider.min.js');

        $headLink = new Zend_View_Helper_HeadLink();
        $headLink -> prependStylesheet('application/css.php?request=application/modules/Ynidea/externals/styles/jquery.bxslider.css');        

        // Process form
        $limit = 5;
        if($this->_getParam('number') != '' && $this->_getParam('number') >= 0)
        {
            $limit = $this->_getParam('number');
        }
        $params = array();
		$params['limit'] = $limit;
        $params['orderby'] = 'active_date';
		$params['direction'] = 'DESC';
		$params['decision'] = 'featured';
        $paginator = Engine_Api::_()->getApi('core', 'ynidea')->getIdeaPaginator($params);
        if(!$paginator->getTotalItemCount()) 
		{
			$this->setNoRender();
		} 
		$this->view->paginator = $paginator;            
    }
}
