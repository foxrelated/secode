<?php

class Replyrate_Form_Admin_Level extends Engine_Form
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
			->setDescription('REPLYRATE_FORM_ADMIN_LEVEL_DESCRIPTION');

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

		$this->addElement('Radio', 'view', array(
			'label' => 'Allow Viewing of Reply Rate?',
			'description' => 'REPLYRATE_FORM_ADMIN_LEVEL_VIEW_DESCRIPTION',
			'multiOptions' => array(
				1 => 'Yes, allow members to view Reply Rate.',
				0 => 'No, do not allow to be viewed.'
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