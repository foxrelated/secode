<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Grouppolls.php 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Grouppoll_Controller_Action_Helper_Grouppolls extends Zend_Controller_Action_Helper_Abstract
{

	function preDispatch()
	{
		//GET NAME OF MODULE, CONTROLLER AND ACTION
		$front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
		$controller = $front->getRequest()->getControllerName();
		$action = $front->getRequest()->getActionName();
		$view = $this->getActionController()->view;
		if(($module == 'group' || $module == 'advgroup') && $controller == 'profile' && $action == 'index') {

			//PUT GROUP POLL TAB AT GROUP PROFILE PAGE
			$page_table = Engine_Api::_()->getDbtable('pages', 'core');
			$page_tableName = $page_table->info('name');
			$select_page = $page_table->select()
													 ->from($page_tableName, 'page_id')
													 ->where('name = ?', 'group_profile_index')
													 ->limit(1);
		  $page = $select_page->query()->fetchAll();
			if(!empty($page)) {
				$page_id = $page[0]['page_id'];

				$content_table = Engine_Api::_()->getDbtable('content', 'core');
			  $content_tableName = $content_table->info('name');
				$select_content = $content_table->select()
																				->from($content_tableName)
																				->where('page_id = ?', $page_id)
																				->where('type = ?', 'widget')
																				->where('name = ?', 'grouppoll.profile-grouppolls')
																				->limit(1);
				$content = $select_content->query()->fetchAll();
				if(empty($content)) {
					$select_container = $content_table->select()
																						->from($content_tableName, 'content_id')
																						->where('page_id = ?', $page_id)
																						->where('type = ?', 'container')
																						->limit(1);
					$container = $select_container->query()->fetchAll();
					if(!empty($container)) {
						$container_id = $container[0]['content_id'];

						$select_middle = $content_table->select()
																							->from($content_tableName)
																							->where('parent_content_id = ?', $container_id)
																							->where('type = ?', 'container')
																							->where('name = ?', 'middle')
																							->limit(1);
						$middle = $select_middle->query()->fetchAll();
						if(!empty($middle)) {
							$middle_id = $middle[0]['content_id'];

							$select_tab = $content_table->select()
																							->from($content_tableName)
																							->where('type = ?', 'widget')
																							->where('name = ?', 'core.container-tabs')
																							->where('page_id = ?', $page_id)
																							->limit(1);
							$tab = $select_tab->query()->fetchAll();
							if(!empty($tab)) {
								$tab_id = $tab[0]['content_id'];
							}

							$widget = $content_table->createRow();
							$widget->page_id = $page_id;
							$widget->type = 'widget';
							$widget->name = 'grouppoll.profile-grouppolls';
							$widget->parent_content_id = ($tab_id ? $tab_id : $middle_id);
							$widget->order = 888;
							$widget->params = '{"title":"Polls","titleCount":true}';
							$widget->save();
							
						}
					}
				}
			}
		}
	}

	function postDispatch()
	{
		//GET NAME OF MODULE, CONTROLLER AND ACTION
		$front = Zend_Controller_Front::getInstance();
		$module = $front->getRequest()->getModuleName();
		$controller = $front->getRequest()->getControllerName();
		$action = $front->getRequest()->getActionName();
		$view = $this->getActionController()->view;

		//ADD POLL PRIVACY FIELDS AT GROUP CREATION AND EDITION 
		if(($module == 'group' || $module == 'advgroup') && ($action == 'create' || $action == 'edit') && ($controller == 'index' || $controller == 'group'))
		{
			$new_element =  $view->form;

			//COUNT TOTAL ELEMENTS IN GROUP FORM
			$total_elements = Count($new_element->getElements());

			$user = Engine_Api::_()->user()->getViewer();

			$availableLabels = array(
				'registered' => 'Registered Members',
				'member' => 'All Group Members',
				'officer' => 'Officers and Owner Only',
			);

			$options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_gpcreate');
			$options_create = array_intersect_key($availableLabels, array_flip($options));

			if(!empty($options_create)) {

				$tableModule = Engine_Api::_()->getDbtable('modules', 'core');
				$tableModuleName = $tableModule->info('name');
				$select  = $tableModule->select()
												->from($tableModuleName, 'name')
												->where('name = ?', 'groupdocument')
												->where('enabled = ?', 1);
				$groupdocument = $tableModule->fetchAll($select)->toArray();
				if(!empty($groupdocument)) {
					$groupdocument_enable = 1;
				}
				else {
					$groupdocument_enable = 0;
				}

				$new_element->addElement('Select', 'gpcreate', array(
					'label' => 'Polls Creation Privacy',
					'description' => 'Who may create polls in this group?',
					'multiOptions' => $options_create,
					'order' => $total_elements-2,
				));
				$new_element->gpcreate->getDecorator('Description')->setOption('placement', 'append');
			}

			//SHOW GROUP-POLL ELEMENT PREFIELD IN EDIT ACTION
			if($action == 'edit') {
				$group_id = $front->getRequest()->getParam('group_id');
				$group = Engine_Api::_()->getItem('group', $group_id);

				$auth = Engine_Api::_()->authorization()->context;
				$roles = array('officer', 'member', 'registered', 'everyone');

				foreach( $roles as $role )
				{
					if(!empty($options_create)) {
						if( 1 === $auth->isAllowed($group, $role, 'gpcreate') )
						{
							$new_element->gpcreate->setValue($role);
						}
					}
				}

				$authorization_table = Engine_Api::_()->getDbtable('allow', 'authorization');
				$authorization_tableName = $authorization_table->info('name');
				$gd_create_authorization = $authorization_table->select()
														->from($authorization_tableName, 'role')
														->where('resource_id = ?', $group_id)
														->where('action = ?', 'gpcreate');
				$create_authorizations = $gd_create_authorization->query()->fetchAll();
				$create_authorization_count = Count($create_authorizations);
				if($create_authorization_count > 0) {
					if($create_authorization_count == 1 && $create_authorizations['0']['role'] == 'group_list' && !empty($options_create)) {
						$set_value = 'officer';
						$new_element->gpcreate->setValue($set_value);
					}
				}
			}
		}
	}
}
?>