<?php
class Ynmultilisting_Form_Custom_Fields extends Fields_Form_Standard
{
	protected $_fieldType = 'ynmultilisting_listing';

	public $_error = array();

	protected $_name = 'fields';

	protected $_elementsBelongTo = 'fields';

	public function init()
	{
		// custom classified fields
		if( !$this->_item ) {
			$listing = new Ynmultilisting_Model_Listing(array());
			$this->setItem($listing);
		}

		parent::init();
		$this->removeElement('submit');
	}

	public function loadDefaultDecorators()
	{
		if( $this->loadDefaultDecoratorsIsDisabled() )
		{
			return;
		}

		$decorators = $this->getDecorators();
		if( empty($decorators) )
		{
			$this
			->addDecorator('FormElements')
			;
		}
	}
}