<?php

class Ynidea_MyIdeasController extends Core_Controller_Action_User
{

    /**
     * idea box home page
     */
    public function indexAction()
    {
        //Require User
        if(!$this->_helper->requireUser->isValid()) return;
        
        // Render
        $this -> _helper -> content -> setNoRender() -> setEnabled();
    }

}
