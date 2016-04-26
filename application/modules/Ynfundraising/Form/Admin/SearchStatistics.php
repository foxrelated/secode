<?php
class Ynfundraising_Form_Admin_SearchStatistics extends Ynfundraising_Form_StatisticsSearch {
	public function init(){
		parent::init();
		$this->clearDecorators()
		->addDecorator('FormElements')
		->addDecorator('Form')
		->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
		->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

		$this->button->clearDecorators()
		->addDecorator('ViewHelper')
		->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
		->addDecorator('HtmlTag2', array('tag' => 'div'));
	}
}
?>