<?php

class Winkgreeting_Form_Admin_Level extends Engine_Form
{
	protected $_public;

	public function setPublic($public)
	{
		$this->_public = $public;
	}

	public function init()
	{
		$this
			->setTitle('Member Level Settings')
			->setDescription('WINKGREETING_FORM_ADMIN_LEVEL_DESCRIPTION');

		$this->loadDefaultDecorators();
		$this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));

		// prepare user levels
		$table = Engine_Api::_()->getDbtable('levels', 'authorization');
		$select = $table->select();
		$user_levels = $table->fetchAll($select);
    
		foreach ($user_levels as $user_level)
			$levels_prepared[$user_level->level_id]= $user_level->getTitle();
    
		// category field
		$this->addElement('Select', 'level_id', array(
			'label' => 'Select Member Level',
			'multiOptions' => $levels_prepared,
			'onchange' => 'javascript:fetchLevelSettings(this.value);'
		));

		$this->addElement('Radio', 'wink', array(
			'label' => 'Allow Viewing And Using Wink?',
			'description' => 'WINK_FORM_ADMIN_LEVEL_VIEW_DESCRIPTION',
			'multiOptions' => array(
				1 => 'Yes, allow members to view and use Wink.',
				0 => 'No, do not allow to be viewed and used.'
			)
		));
		
		$this->addElement('Radio', 'greeting', array(
			'label' => 'Allow Viewing And Using Greeting?',
			'description' => 'GREETING_FORM_ADMIN_LEVEL_VIEW_DESCRIPTION',
			'multiOptions' => array(
				1 => 'Yes, allow members to view and use Greeting.',
				0 => 'No, do not allow to be viewed and used.'
			)
		));	

		if (!$this->_public) 
		{
			$this->addElement('Button', 'submit', array(
				'label' => 'Save Settings',
				'type' => 'submit',
			));
		}
	}
}