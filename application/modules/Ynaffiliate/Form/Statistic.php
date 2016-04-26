<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Ynaffiliate_Form_Statistic extends Engine_Form {

   public function init() {

      $this->setAttribs(array(
          'id' => 'ynaffiliate_statistic_form',
          'class' => "global_form ynaffiliate_statistic_form f1",
      ));

	  // status element
	  $this -> addElement('Select', 'approve_stat', array(
	  	'label' => 'Status',
	  	'multiOptions' => array(
			'all' => 'All',
			'waiting' => 'Waiting',
			'delaying' => 'Delaying',
			'approved' => 'Approved',
		)
	  ));
	  // type element
	  $this -> addElement('Select', 'group_by', array(
	  	'label' => 'Show by',
	  	'multiOptions' => array(
			'commission_rule' => 'Commission Rules',
			'commission_level' => 'User Network Levels',
		),
		'onChange' => 'changeGroupBy(this)'
	  ));
	  
	  // chart type element
	  $this -> addElement('Select', 'chart_type', array(
	  	'label' => 'Chart type',
	  	'multiOptions' => array(
			'line' => 'Line chart',
			'pie' => 'Pie chart',
		),
		'onChange' => 'changeChartType(this)'
	  ));
	  
	  // Init period
      $this->addElement('Select', 'period', array(
	      'label' => 'Duration',
	      'multiOptions' => array(
	        Zend_Date::WEEK => 'This week',
	        Zend_Date::MONTH => 'This month',
	        Zend_Date::YEAR => 'This year',
	      ),
	      'value' => 'week',
	      'class' => 'filter_elem',
	      'onChange' => 'changePeriod()'
	    ));
	
      // Init chunk
	  $this->addElement('Select', 'chunk', array(
	      'label' => 'Time Summary',
	      'multiOptions' => array(
	        Zend_Date::DAY => 'By day',
	        Zend_Date::WEEK => 'By week',
	        Zend_Date::MONTH => 'By month',
	        Zend_Date::YEAR => 'By year',
	      ),
	      'value' => 'day',
	      'class' => 'filter_elem',
      ));

      $this->addElement('Button', 'submit', array(
          'label' => 'View Chart',
          'type' => 'submit',
          'onclick' => 'return processStatisticsFilter($(this).getParent("form"))',
          'ignore' => true,
      ));
   }

}

?>
