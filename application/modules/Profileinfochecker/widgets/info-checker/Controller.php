<?php

class Profileinfochecker_Widget_InfoCheckerController extends Engine_Content_Widget_Abstract
{

  public $_fieldType = 'user';
  protected $_requireProfileType = true;

  public function indexAction()
  {
	$pageSubject = Engine_Api::_()->core()->hasSubject() ? Engine_Api::_()->core()->getSubject() : null;
    // Don't render this if not logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity()) {
      return $this->setNoRender();
    }
	
	$this->view->label = "<a href=members/edit/profile style='font-weight:bold'>".Zend_Registry::get('Zend_Translate')->_('Add profile information')."</a>";
		
    if ($pageSubject != null) {
      if( !$viewer->isSelf($pageSubject) ) {
        $label = "Add member information";
	    $this->view->label = "<a href=members/edit/profile/id/".$pageSubject->getIdentity()." style='font-weight:bold'>".Zend_Registry::get('Zend_Translate')->_('Add member information')."</a>";	  
      }	
	
	  if (isset($pageSubject->user_id)) {
   	    if( !$pageSubject->authorization()->isAllowed($viewer, 'edit') ) {
	      return $this->setNoRender();
	    }			
	  }
	}
  	
    $user = Engine_Api::_()->user()->getViewer();
	if ($pageSubject) $user = $pageSubject;
    // Set data
    $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps($this->_fieldType);
    $metaData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMeta($this->_fieldType);
    $optionsData = Engine_Api::_()->getApi('core', 'fields')->getFieldsOptions($this->_fieldType);
	$valueData = Engine_Api::_()->fields()->getFieldsValues($user);
	$profile_type = Engine_Api::_()->fields()->getFieldsSearch($user);
	

    // Get top level fields
    $topLevelMaps = $mapData->getRowsMatching(array('field_id' => 0, 'option_id' => 0));
    $topLevelFields = array();
    foreach( $topLevelMaps as $map ) {
      $field = $map->getChild();
      $topLevelFields[$field->field_id] = $field;
    } 
  
    // Do we require profile type?
    // No
    if( !$this->_requireProfileType ) {
      $this->topLevelOptionId = '0';
      $this->topLevelFieldId = '0';
    }
    // Yes
    else {

      // Get top level field
      // Only allow one top level field
      if( count($topLevelFields) > 1 ) {
        throw new Engine_Exception('Only one top level field is currently allowed');
      }
      $topLevelField = array_shift($topLevelFields);
      // Only allow the "profile_type" field to be a top level field (for now)
      if( $topLevelField->type !== 'profile_type' ) {
        throw new Engine_Exception('Only profile_type can be a top level field');
      }

      // Get top level options
      $topLevelOptions = array();
      foreach( $optionsData->getRowsMatching('field_id', $topLevelField->field_id) as $option ) {
        $topLevelOptions[$option->option_id] = $option->label;
      }
	  $this->view->topLevelOptions = $topLevelOptions;

      // Get selected option
      $option_id = $profile_type['profile_type'];
      if( empty($option_id) || empty($topLevelOptions[$option_id]) ) {
        $option_id = current(array_keys($topLevelOptions));
      }
      $topLevelOption = $optionsData->getRowMatching('option_id', $option_id);

      if( !$topLevelOption ) {
        throw new Engine_Exception('Missing option');
      }
      $this->view->topLevelOption = $topLevelOption;
      $this->view->topLevelOptionId = $topLevelOption->option_id;
	  $all_field = 0;
	  $filled_info = 0;
	  $old_id = 0;
      // Get second level fields
      $secondLevelMaps = array();
      if( !empty($option_id) ) {
        $secondLevelMaps = $mapData->getRowsMatching('option_id', $option_id);
        if( !empty($secondLevelMaps) ) {
          foreach( $secondLevelMaps as $map ) {
            $oField = $map->getChild();
	  		if ($oField || $oField->type != 'profile_type' || !$oField->display) {
		 	  if ($oField->type != 'heading' ) {
				$all_field++;
			    $Data = $valueData->getRowsMatching('field_id', $oField->field_id);
				foreach( $Data as $val ) {
				  if (isset($old_id) && $old_id == $val->field_id) continue;	  
				    if (!empty($val->value) )
					  $filled_info++;	
				  $old_id = $val->field_id;	  	
				}
	  		  }
       		}
          }
        }
      }
    }  
	$settings = Engine_Api::_()->getApi('settings', 'core');	
	$prcent_hide = isset($settings->checker_percent) ? $settings->checker_percent : 100;
	$percent = round(100*$filled_info/$all_field);
	if ($percent >= $prcent_hide)
	  return $this->setNoRender();

	$this->view->bordercolor = isset($settings->checker_bgcolor) ? $settings->checker_bgcolor : '#5f93b4';
	$this->view->backgroundcolor = isset($settings->checker_tcolor) ? $settings->checker_tcolor : '#d0e2ec';
  
	$this->view->percent = $percent; 
  }
}  