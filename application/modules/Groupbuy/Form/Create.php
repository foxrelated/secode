<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Group Buy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Create.php
 * @author     Minh Nguyen
 */
class Groupbuy_Form_Create extends Engine_Form
{
    public $_error = array();

    public function init()
    {
        $this->setTitle('Post A New Deal')
            ->setDescription("Compose your new deal below, then click 'Post Deal' to publish deal.")
            ->setAttrib('name', 'deals_create');
        $user = Engine_Api::_()->user()->getViewer();
        $user_level = Engine_Api::_()->user()->getViewer()->level_id;
        $translate = Zend_Registry::get('Zend_Translate');
        $this->addElement('Text', 'title', array(
            'label' => 'Deal Name*',
            'allowEmpty' => false,
            'required'=>true,
            'title' => $translate->translate('Name of deal'),
            'description' => 'Name of deal',
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags',
                new Engine_Filter_StringLength(array('max' => '256'))
            )));
        $this->title->getDecorator("Description")->setOption("placement", "append");

        $categories =  Engine_Api::_()->getDbTable('categories','groupbuy')->getMultiOptions('..');
        $this->addElement('Select', 'category_id', array(
            'label' => 'Category*',
            'allowEmpty' => false,
            'required'=>true,
            'multiOptions' => $categories,
            'title' => 'Category which deal belongs to',
        ));

        $this->addElement('select', 'currency', array(
            'label' => 'Default Currency*',
            'description' => 'Select default currency',
            'required'=>true,
            'multiOptions' => Groupbuy_Model_DbTable_Currencies::getMultiOptions(),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.currency', 'USD'),
        ));
        $this->currency->getDecorator("Description")->setOption("placement", "append");

        $this->addElement('select','vat_id', array(
            'label'=>'VAT (%)',
            'multiOptions'=>Groupbuy_Model_DbTable_Vats::getMultiOptions(),
            //'description'=>''
        ));

        $this->vat_id->getDecorator("Description")->setOption("placement", "append");

        $this->addElement('select', 'method', array(
            'label' => 'Payment Method*',
            'allowEmpty' => false,
            'required'=>true,
            'multiOptions' => array(
                0 => "All Methods",
                -1 => "Cash on Delivery Only",
                -2 => "Virtual money Only",

            ),
            'value' => '0',
        ));

        $this->method->getDecorator("Description")->setOption("placement", "append");

        // Gateways
        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
        $gatewaySelect = $gatewayTable -> select() -> where('enabled = ?', 1);
        $gateways = $gatewayTable -> fetchAll($gatewaySelect);

        $gatewayPlugins = array();
        foreach ($gateways as $gateway) {
            $this->method->addMultiOption($gateway->gateway_id, $gateway->title.' Only');
        }


        $this->addElement('Text', 'value_deal',array(
            'label'=>'Value Of Deal*',
            'title' =>  $translate->translate('Deal price is offered in MarketPlace'),
            'description' => 'Deal price is offered in MarketPlace',
            'allowEmpty' => false,
            'required'=>true,
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'value'=>    '1.00',
        ));
        $this->value_deal->getDecorator("Description")->setOption("placement", "append");

        $this->addElement('Text', 'price',array(
            'label'=>'Price*',
            'title' => '',
            'allowEmpty' => false,
            'required'=>true,
            'description' => '',
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'value'=>    '0.00',
        ));
        $this->price->getDecorator("Description")->setOption("placement", "append");

        $this->addElement('Text', 'min_sold',array(
            'label'=>'Minimum Units Sold*',
            'title' => '',
            'allowEmpty' => false,
            'required'=>true,
            'description' => '',
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'value'=>    '1',
        ));
        $this->min_sold->getDecorator("Description")->setOption("placement", "append");

        $this->addElement('Text', 'max_sold',array(
            'label'=>'Maximum Units Sold*',
            'title' => '',
            'allowEmpty' => false,
            'required'=>true,
            'description' => '',
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'value'=> '10',
        ));
        $this->max_sold->getDecorator("Description")->setOption("placement", "append");

        $this->addElement('Text', 'max_bought',array(
            'label'=>'Maximum Units Bought',
            'title' =>  $translate->translate('(per Buyer)'),
            'description' => '(per Buyer - 0 means Unlimited)',
            'allowEmpty' => true,
            'required'=>false,
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'value'=> '0',
        ));
        $this->max_bought->getDecorator("Description")->setOption("placement", "append");


        $allowed_html = Engine_Api::_()->authorization()->getPermission($user_level, 'groupbuy_deal', 'auth_html');

        $editorOptions['plugins'] =  array(
            'table', 'fullscreen', 'media', 'preview', 'paste',
            'code', 'image', 'textcolor'
        );
        $editorOptions['html'] = 1;
        $editorOptions['bbcode'] = 1;
        $editorOptions['mode'] = 'exact';
        $editorOptions['elements'] = 'features, fine_print, description';

        $this->addElement('TinyMce', 'features', array(
            'label' => 'Highlight Features*',
            'required'=>true,
            'editorOptions' => $editorOptions,
            'filters' => array(
                new Engine_Filter_Censor(),
                new Engine_Filter_Html(array('AllowedTags'=>$allowed_html))),
        ));
        $this->addElement('TinyMce', 'fine_print', array(
            'label' => 'The Fine Print*',
            'required'=>true,
            'editorOptions' => $editorOptions,
            'filters' => array(
                new Engine_Filter_Censor(),
                new Engine_Filter_Html(array('AllowedTags'=>$allowed_html))),
        ));
        $this->addElement('TinyMce', 'description', array(
            'label' => 'Description*',
            'required'=>true,
            'editorOptions' => $editorOptions,
            'filters' => array(
                new Engine_Filter_Censor(),
                new Engine_Filter_Html(array('AllowedTags'=>$allowed_html))),
        ));



        $this->addElement('File', 'thumbnail', array(
            'label' => 'Main Photo*',
            'title' =>  $translate->translate('Main image of deal'),
            'required'=>true,
            'description' => 'Main image of deal (jpg, png, gif, jpeg)',
        ));
        $this->thumbnail->getDecorator("Description")->setOption("placement", "append");
        $this->thumbnail->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        // Element: timezone
        $settings = Engine_Api::_()->getApi('settings', 'core');
        // Start time
        $start = new Engine_Form_Element_CalendarDateTime('start_time');
        $start->setLabel("Start Time*");
        $start->setTitle =  $translate->translate('Time to start deal');
        $start->setAllowEmpty(false);
        $start->setRequired(true);
        $this->addElement($start);
        $this->start_time->getDecorator("Description")->setOption("placement", "append");
        // End time
        $end = new Engine_Form_Element_CalendarDateTime('end_time');
        $end->setLabel("End Time*");
        $end->setAllowEmpty(false);
        $end->setRequired(true);
        $this->addElement($end);


        $this->addElement('Text', 'company_name',array(
            'label'=>'Company Name*',
            'description' => '',
            'allowEmpty' => false,
            'required'=>true,
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'value'=>'',
        ));

        // category field
        $this->addElement('Select', 'location_id', array(
            'label' => 'Location*',
            'required'=>true,
            'multiOptions' => Engine_Api::_()->getDbTable('locations','groupbuy')->getMultiOptions('..'),
            'title' => 'Location which deal belongs to',
            'value' => 0
        ));

//        $this->addElement('Text', 'address',array(
//            'label'=>'Address*',
//            'description' => '',
//            'allowEmpty' => false,
//            'filters' => array(
//                new Engine_Filter_Censor(),
//            ),
//            'value'=>'',
//        ));

        $this -> addElement('Dummy', 'location_map', array(
            'label' => 'Address*',
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_location.tpl',
                    'class' => 'form element',
                )
            )),
        ));

        $this -> addElement('hidden', 'location_address', array(
            'value' => '0',
            'order' => '97'
        ));

        $this -> addElement('hidden', 'lat', array(
            'value' => '0',
            'order' => '98'
        ));

        $this -> addElement('hidden', 'long', array(
            'value' => '0',
            'order' => '99'
        ));

        $this->addElement('Text', 'phone',array(
            'label'=>'Phone Number',
            'description' => '',
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'value'=>'',
        ));
        $this->addElement('Text', 'website',array(
            'label'=>'Your Website',
            'description' => '',
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'value'=>'',
        ));
        // Add subforms
        if( !$this->_item ) {
            $customFields = new Groupbuy_Form_Custom_Fields();
        } else {
            $customFields = new Groupbuy_Form_Custom_Fields(array(
                'item' => $this->getItem()
            ));
        }
        if( get_class($this) == 'Groupbuy_Form_Create' ) {
            $customFields->setIsCreation(true);
        }

        $this->addSubForms(array(
            'fields' => $customFields
        ));


        // View
        $availableLabels = array(
            'everyone'            => 'Everyone',
            'registered'          => 'All Registered Members',
            'owner_network'       => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member'        => 'Friends Only',
            'owner'               => 'Just Me',
        );
        /*
            $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('groupbuy_deal', $user, 'auth_view');
            $options = array_intersect_key($availableLabels, array_flip($options));

            $this->addElement('Select', 'auth_view', array(
              'label' => 'Privacy',
              'title' => 'Who may see this deal?',
              'description' => 'Who may see this deal?',
              'multiOptions' => $options,
              'value' => 'everyone',
            ));
            $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
        */
        $options =(array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('groupbuy_deal', $user, 'auth_comment');
        $options = array_intersect_key($availableLabels, array_flip($options));

        // Comment
        $this->addElement('Select', 'auth_comment', array(
            'label' => 'Comment Privacy',
            'title' =>  $translate->translate('Who may post comments on this deal?'),
            'description' => 'Who may post comments on this deal?',
            'multiOptions' => $options,
            'value' => 'everyone',
        ));
        $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');

        $feep = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.displayfee', 10);
        $freep = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('groupbuy_deal', $user, 'free_display');
        if($freep == 1)
            $feep = 0;
        $this->addElement('Text', 'feep',array(
            'label'=>$translate->translate('Publishing Fee (').Engine_Api::_()->groupbuy()->getDefaultCurrency().')',
            'title' => $translate->translate('You can not edit this field'),
            'description' => 'You can not edit this field',
            'readonly' => 'readonly',
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'value'=>$feep,
        ));
        $this->feep->getDecorator("Description")->setOption("placement", "append");

        $fee = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.fee', 10);
        $free = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('groupbuy_deal', $user, 'free_fee');
        if($free == 1)
            $fee = 0;
        // Init search checkbox
        $this->addElement('Checkbox', 'featured', array(
            'label' => $translate->translate("Feature Your Deal (").$fee." ".Engine_Api::_()->groupbuy()->getDefaultCurrency().")",
            'title' => 'Make your product name feature in Browse Auction',
            'value' => 0,
            'onclick' => 'setFeatured()',
            'checked' => false,
        ));
        $this->addElement('Text', 'total_fee',array(
            'label'=>$translate->translate('Total Fee (').Engine_Api::_()->groupbuy()->getDefaultCurrency().')',
            'description' => '',
            'readonly' => 'readonly',
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'value'=>$feep,
        ));
        $this->addElement('Button', 'execute', array(
            'label' => 'Post Deal',
            'type' => 'button',
            'onclick' => 'this.form.submit(); removeSubmit()',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        // Element: cancel
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage-selling'), 'groupbuy_general', true),
            'onclick' => '',
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        // DisplayGroup: buttons
        $this->addDisplayGroup(array(
            'execute',
            'cancel',
        ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }
};
