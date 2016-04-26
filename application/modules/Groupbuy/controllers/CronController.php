<?php
class Groupbuy_CronController extends Core_Controller_Action_Standard{
	   public function indexAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);  
        $obj1 = new Groupbuy_Plugin_Task_Subscription();
        $obj1->execute();
        $obj2 = new Groupbuy_Plugin_Task_Running();
        $obj2->execute();  
        $obj3 = new Groupbuy_Plugin_Task_SendMail();
        $obj3->execute();              
      }
}
