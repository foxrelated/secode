<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Fundraising
 * @copyright  Copyright 2012 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: CreateStep1.php
 * @author     Minh Nguyen
 */
class Ynfundraising_Form_PostNews extends Engine_Form {
	public function init() {
		$this->setTitle("Post a News Update");
		$this->setAttrib('class','global_form ynFRaisingPostNew')->setAttrib ( 'name', 'ynfundraising_post_news');
		// Element: title
		$this->addElement ( 'Text', 'title', array (
					'label' => '*News headline',
					'required' => true,
					'style'	=> 'width: 300px',
					'description' => '(max 256 characters)',
					'filters' => array (
							new Engine_Filter_Censor (),
							'StripTags',
						new Engine_Filter_StringLength ( array (
								'max' => '256' 
						) ) 
				) 
		) );
		$this->title->getDecorator ( "Description" )->setOption ( "placement", "append" );
		// Element: Link
		$this->addElement ( 'Text', 'link', array (
					'label' => 'Link',
					'required' => false,
					'description' => 'Ex: http://www.yoursite.com/ (max 256 characters)',
					'style'	=> 'width: 300px',
					'filters' => array (
						new Engine_Filter_StringLength ( array (
								'max' => '256' 
						) ) 
				) 
		) );
		$this->link->getDecorator ( "Description" )->setOption ( "placement", "append" );
		// Content
		$this->addElement ( 'textarea', 'content', array (
				'label' => '*Content',
				'required' => true,
				'description' => '*Required Fields (max 2000 characters)',
				'style' => 'width: 400px; height: 100px',
				'value' => '',
				'validators' => array(
		        array('NotEmpty', true),
		        array('StringLength', true, array(1, 256)),
		      ),
		) );
		$this->content->getDecorator ( "Description" )->setOption ( "placement", "append" );
	}
}
