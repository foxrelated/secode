<?php

 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Grouppoll_Plugin_Core
{
	//DELETE POLL IF GROUP IS DELETED
	public function onItemDeleteBefore($event)
	{
    $payload = $event->getPayload();

    if ($payload instanceof Group_Model_Group) {

			//GET GROUP ID
      $group_id = $payload->getIdentity();

			if(!empty($group_id)) {
				//FETCH POLLS CORROSPONDING TO THAT GROUP ID
				$table   = Engine_Api::_()->getItemTable('grouppoll_poll');
				$select  = $table->select()
												->from($table->info('name'), 'poll_id')
												->where('group_id = ?', $group_id);
				$rows = $table->fetchAll($select)->toArray();
				if(!empty($rows)) {
					foreach($rows as $key => $poll_ids) {
						$poll_id = $poll_ids['poll_id'];
						$grouppoll = Engine_Api::_()->getItem('grouppoll_poll', $poll_id);

						//DELETE POLL AND OTHER BELONGINGS
						if(!empty($poll_id)) {
							$grouppoll->delete();
						}
					}
				}
			}
		}
	}

  //DELETE USERS BELONGINGS BEFORE THAT USER DELETION
  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    
    if( $payload instanceof User_Model_User ) {
      
    	//DELETE POLLS
      $grouppollTable = Engine_Api::_()->getDbtable('polls', 'grouppoll');
      $grouppollSelect = $grouppollTable->select()
                                        ->from($grouppollTable->info('name'), 'group_id')
                                        ->where('group_owner_id = ?', $payload->getIdentity());
			
      foreach( $grouppollTable->fetchAll($grouppollSelect) as $grouppoll ) {
        $grouppollsSelect = $grouppollTable->select()
                                        ->from($grouppollTable->info('name'), 'poll_id')
                                        ->where('group_id = ?', $grouppoll->group_id);
        foreach( $grouppollTable->fetchAll($grouppollsSelect) as $grouppolls ) {
					$grouppolls->delete();
        }
      }
    }
  }

	//SAVE GROUP-POLL PRIVACY AT CREATION AND EDITION TIME
	public function addActivity($event)
	{
		$front = Zend_Controller_Front::getInstance();
		$module = $front->getRequest()->getModuleName();
		$controller = $front->getRequest()->getControllerName();
		$action = $front->getRequest()->getActionName();

		if(($module == 'group' || $module == 'advgroup') && ($action == 'create' || $action == 'edit') && ($controller == 'index' || $controller == 'group'))
		{
			$payload = $event->getPayload();
			$group_id = $payload['object']->group_id;

			$group = Engine_Api::_()->getItem('group', $group_id);

			// Process privacy
      $auth = Engine_Api::_()->authorization()->context;
      
      $roles = array('officer', 'member', 'registered', 'everyone');

      $gpcreate = array_search($_POST['gpcreate'], $roles);

      $officerList = $group->getOfficerList();

      foreach( $roles as $i => $role ) {
        if( $role === 'officer' ) {
          $role = $officerList;
        }
        $auth->setAllowed($group, $role, 'gpcreate', ($i <= $gpcreate));
      }
		}
	}
}
?>