<?php


class Ynaffiliate_Form_Admin_Signup_Affiliate extends Engine_Form
{
  public function init()
  {
    // Get step and step number
    $stepTable = Engine_Api::_()->getDbtable('signup', 'user');
    $stepSelect = $stepTable->select()->where('class = ?', str_replace('_Form_Admin_', '_Plugin_', get_class($this)));
    $step = $stepTable->fetchRow($stepSelect);
    $stepNumber = 1 + $stepTable->select()
      ->from($stepTable, new Zend_Db_Expr('COUNT(signup_id)'))
      ->where('`order` < ?', $step->order)
      ->query()
      ->fetchColumn()
      ;
    $stepString = $this->getView()->translate('Step %1$s', $stepNumber);
    $this->setDisableTranslator(true);


    // Custom
    $this->setTitle($this->getView()->translate('%1$s: Affiliate', $stepString));
    
    // Element: enable
    $this->addElement('Radio', 'enable', array(
      'label' => 'Enable Affiliate',
      'description' => 'YNAFFILIATE_FORM_ADMIN_SIGNUP_FIELDS_ENABLE_DESCRIPTION',
      'multiOptions' => array(
        '1' => 'Yes, enable affiliate',
        '0' => 'No, do not include this step.',
      ),
      'value'=>1,
    ));

	// Element: enable
    $this->addElement('Radio', 'visible', array(
      'label' => 'Visible Affiliate Step',
      'description' => 'YNAFFILIATE_FORM_ADMIN_SIGNUP_FIELDS_VISIBLE_DESCRIPTION',
      'multiOptions' => array(
        '1' => 'Yes, display affiliate step on signup step',
        '0' => 'No, do not display this step.',
      ),
      'value'=>0,
    ));
	
    // Element: submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));

    // Populate
    $this->populate(array(
      'enable' => $step->enable,
    ));
  }
}