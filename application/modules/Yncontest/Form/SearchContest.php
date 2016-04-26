<?php
class Yncontest_Form_SearchContest extends Engine_Form {
  public function init()
  {  	
	$this
  	->addPrefixPath('Yncontest_Form_Decorator', APPLICATION_PATH . '/application/modules/Yncontest/Form/Decorator', 'decorator')
  	->addPrefixPath('Yncontest_Form_Element', APPLICATION_PATH . '/application/modules/Yncontest/Form/Element', 'element')
  	->addElementPrefixPath('Yncontest_Form_Decorator', APPLICATION_PATH . '/application/modules/Yncontest/Form/Decorator', 'decorator');

    $this->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
                //'method'=>'GET',
            ))
            ->setAction(
            	Zend_Controller_Front::getInstance()->getRouter()->assemble(
	            array(		      		
		          	'action' => 'listing',
	       	 	), 'yncontest_general', true)
		 	);

    //Search Title
    $this->addElement('Text', 'contest_name', array(
      'label' => 'Contest name',     
    ));
		    

    $plugin = Engine_Api::_() -> yncontest() -> getPlugins();
    
	$contest_type =array_merge(array('0' => 'All'),$plugin);
	$this->addElement('Select', 'contest_type', array(
      'label' => "Contest's type",
      'multiOptions' => $contest_type,           
    ));
	
	$this->addElement('ContestMultiLevel', 'category_id', array(
			'label' => 'Category',
			'required'=>false,
			'model'=>'Yncontest_Model_DbTable_Categories',
			'onchange'=>"en4.yncontest.changeCategory($(this),'category_id','Yncontest_Model_DbTable_Categories')",
			'title' => '',
			'value' => 0
	));
		
	$this->addElement('Select', 'browseby', array(
      'label' => 'Browse By',
      'multiOptions' => array(
      	'all'=>'',
        'featured_contest'  => 'Featured Contests',
      	'premium_contest'   => 'Premium Contests',
      	'endingsoon_contest'=> 'Ending Soon',   
      ),     
    ));
	$this->addElement('Select', 'contestsocial', array(
			'label' => 'Filter By',
			'multiOptions' => array(					
					'all_contest'  => 'All Contests',
					'friend_contest'   => 'Friend\'s Contests',					
			),
	));
    
	$this->addElement('Select', 'contest_status', array(
      'label' => 'Status',
      'multiOptions' => array(
      	'all'=>'',        
        'published' => 'Published',        
        'close' => 'Closed',  
    ),
      'value' => '',
      //'onchange' => 'this.form.submit();',
    ));
	
	$this->addElement('Hidden', 'page', array(
      'order' => 100
    ));

	$this->addElement('Button', 'submit', array(
      'label' => 'Search',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
      'submit',
      'cancel',
    ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
	
	// Populate
	/*
    if (Zend_Registry::isRegistered('contest_search_params')) {
    	$values = Zend_Registry::get('contest_search_params');
	    $this->populate($values);
    }
	*/
  }
}