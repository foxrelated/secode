<?php

class Ynfundraising_Widget_CampaignsTopDonorsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Don't render this if not authorized
	    $viewer = Engine_Api::_()->user()->getViewer();
		// Process form
        $limit = 5;
        if($this->_getParam('number') != '' && $this->_getParam('number') >= 0)
        {
            $limit = $this->_getParam('number');
        }
	    $values = array('limit' => $limit, 'top' => true);
		$donors = Engine_Api::_()->getApi('core', 'ynfundraising')->getDonorPaginator($values);
		$arr_order = array();
		foreach( $donors as $donor ) 
		{
			$total = Engine_Api::_()->getApi('core', 'ynfundraising')->getTotalCampaignForDonor($donor->user_id);
			$donor->total_amount = $total;
			$arr_order[] = $donor;
		}
		usort($arr_order, "cmp");
		$this->view->donors = $arr_order;
		if(!$donors->getTotalItemCount())
			$this->setNoRender();
    }
}
function cmp($a, $b)
{
    return $a->total_amount < $b->total_amount;
}
