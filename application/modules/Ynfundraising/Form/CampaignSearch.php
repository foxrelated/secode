<?php
class Ynfundraising_Form_CampaignSearch extends Engine_Form
{
/*----- Init Form Function -----*/
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'ynfundraising_filter_form',
        'class' => 'global_form_box',
      	'style' => 'margin-bottom: 15px',
        'method' => 'GET',
      ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;
      //print_r(Zend_Controller_Front::getInstance()->getRouter());
    //Text filter element
    $this->addElement('Text', 'search', array(
      'label' => 'Search',
      'onchange' => 'this.form.submit();',
    ));

    //Browse By Filter Element
    $this->addElement('Select', 'show', array(
      'label' => 'View',
      'multiOptions' => array(
      	''  => 'All',
        '1' => 'My Own Campaigns',
        '2' => 'My Donated Campaigns',
      ),
      'value' => '1',
      'onchange' => 'this.form.submit();',
    ));

    //Campaing Search - Browse By
//     $this->addElement('Select', 'browse', array(
//       'label' => 'Browse By',
//       'multiOptions' => array(
//         '' => 'All',
//         'ideabox' => 'Idea Box',
//       ),
//     ));

	//Type Filter
    if(Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->checkIdeaboxPlugin ())
	{
	    $this->addElement('Select', 'type', array(
	      'label' => 'Type',
	      'multiOptions' => array(
	        ''  => 'All',
	        'idea' => 'Idea',
	        'trophy' => 'Trophy',
	        'user' => 'User',
	    ),
	      'value' => '',
	      'onchange' => 'this.form.submit();',
	    ));
	}

    //Campaign Search - Status Filter
    $this->addElement('Select', 'status', array(
      'label' => 'Status',
      'multiOptions' => array(
        ''  => 'All',
        'ongoing' => 'Ongoing',
        'closed' => 'Closed',
        'reached' => 'Reached',
        'expired' => 'Expired',
    ),
      'value' => '',
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Hidden', 'page', array(
      'order' => 100
    ));

    $this->addElement('Hidden', 'tag', array(
    		'order' => 101
    ));

    // Element: order
    $this->addElement ( 'Hidden', 'orderby', array (
    		'order' => 102,
    		'value' => 'campaign_id'
    ) );

    // Element: direction
    $this->addElement ( 'Hidden', 'direction', array (
    		'order' => 103,
    		'value' => 'DESC'
    ) );
/*
    // Buttons
    $this->addElement('Button', 'button', array(
    		'label' => 'Search',
    		'type' => 'submit',
    ));
*/

  }
}