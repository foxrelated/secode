<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    goals
 * @copyright  Copyright 2014 Stars Developer
 * @license    http://www.starsdeveloper.com 
 * @author     Stars Developer
 */

class Goal_Widget_GoalCategoriesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

$this->view->base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
    //get completed tasks
    $table = Engine_Api::_()->getDbtable('categories','goal');
    $select = $table->select();
    $categories = $table->fetchAll($select);
    $this->view->categories = $categories;
    
   }
}