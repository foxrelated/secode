<?php
class Groupbuy_AdminPageController extends Core_Controller_Action_Admin
{
    public function indexAction(){
        //Get Menu Navigation
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_admin_main', array(), 'groupbuy_admin_main_page');

        //Get Pages From Database And Set Page Paginator
        $table = new Groupbuy_Model_DbTable_Pages;
        $select = $table -> select();
        
        $paginator = $this -> view -> paginator = Zend_Paginator::factory($select);
        $paginator -> setCurrentPageNumber($this->_getParam('page',1));
	$paginator -> setItemCountPerPage(5);
    }
    
    public function editPageAction(){
        //Get Menu Navigation
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_admin_main', array(), 'groupbuy_admin_main_page');
        
        //Get Form Edit Instruction Page
        $form = $this -> view -> form = new Groupbuy_Form_Admin_Page();

        //Check Post Method
        if($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
             $values = $form -> getValues();
             $db = Engine_Db_Table::getDefaultAdapter();
             $db -> beginTransaction();

               try {
                    // Edit Page In The Database
                    $page_id = $values["pageId"];
                    $table = new Groupbuy_Model_DbTable_Pages;
                    $select = $table -> select() ->where('page_id = ?',$page_id);
                    $row = $table->fetchRow($select);

                    $row -> title = $values["title"];
                    $row -> description = $values["description"];
                    $row -> body = $values["body"];
                    $row -> modified_date = date('Y-m-d h:i:s');

                    //Database Commit
                    $row -> save();
                    $db -> commit();
                }
                catch( Exception $e ) {
                    $db -> rollBack();
                    throw $e;
                }
                
                //Return To Admin Page Control
                $this->_helper->redirector->gotoRoute(array( 'module'     => 'groupbuy',
                                                         'controller' => 'page',
                                                         'action'     => 'index'),null, true);
        }
        // Get Page Id - Throw Exception If There Is No Page Id
        if(!($page_id = $this -> _getParam('page_id'))) {
                                    throw new Zend_Exception('No page id specified');
                            }

        //Generate And Assign Form
        $table = new Groupbuy_Model_DbTable_Pages;
		$select = $table -> select() ->where('page_id = ?',$page_id);
                $page = $table->fetchRow($select);
                $form ->populate(array( 'pageId'      => $page_id,
                                        'title'       => $page -> title,
                                        'description' => $page ->description,
                                        'body'        => $page -> body));
             }
}

?>
