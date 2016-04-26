<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2010-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Grouppoll_Widget_VoteGrouppollsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    //GET LOGGED IN USER INFORMATION
		$viewer = Engine_Api::_()->user()->getViewer();

		//GET SUBJECT AND GROUP ID
		$subject = Engine_Api::_()->core()->getSubject('group');
		$group_id = $subject->group_id;

		//SET NO RENDER IF VIEWER CAN NOT VIEW THE DOCUMENTS
		if ( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

  	//NUMBER OF POLLS IN LISTING
  	$total_grouppolls = Engine_Api::_()->getApi('settings', 'core')->grouppoll_vote_widgets;
    $values = array();
    $values['group_id'] = $group_id;
    $values['total_grouppolls'] = $total_grouppolls;
    $this->view->listVotedPolls = $listVotedPolls = Engine_Api::_()->getDbtable('polls', 'grouppoll')->getPollListing('Most Voted', $values);

    if ( Count($listVotedPolls) <= 0 ) {
      return $this->setNoRender();
    }
  }
}
?>