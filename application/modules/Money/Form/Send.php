<?php
/**
 * SocialEnginePro
 *
 * @category   Application_Extensions
 * @package    E-money
 * @author     Azim
 */

/**
 * @category   Application_Extensions
 * @package    E-money
 */
class Money_Form_Send extends Engine_Form
{
    protected $_balance;

    public function init()
    {

        $viewer = Engine_Api::_()->user()->getViewer();
        $this->_balance = $balance = Engine_Api::_()->money()->getUserBalance($viewer);
        $this->setTitle('Issue an invoice');

        $localeObject = Zend_Registry::get('Locale');
        $currency = Engine_Api::_()->getApi('settings',
            'core')->getSetting('money.site.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'currencysymbol', $currency);

        $description = $this->getTranslator()->translate('MONEY_FORM_ISSUE');
        $description = vsprintf($description, array($balance.' '.$currencyName));

        $this->setDescription($description);

        $this->setTitle('Send Money')
                ->setAttrib('id', 'messages_compose');

        // init to
        $this->addElement('Text', 'to', array(
            'label' => 'Send To',
            'autocomplete' => 'on'
        ));

        Engine_Form::addDefaultDecorators($this->to);

        // Init to Values
        $this->addElement('Hidden', 'toValues', array(
            'label' => 'Send To',
            'required' => true,
            'allowEmpty' => false,
            'order' => 2,
            'validators' => array(
                'NotEmpty'
            ),
            'filters' => array(
                'HtmlEntities'
            ),
        ));
        Engine_Form::addDefaultDecorators($this->toValues);

        // init title
        $this->addElement('Text', 'amount', array(
            'label' => 'Amount',
            'order' => 3,
            'required' => true,
            'allowEmpty' => false,
            'filters' => array(
                new Engine_Filter_Censor(),
                new Engine_Filter_HtmlSpecialChars(),
            ),
            'validators' => array(
                new Engine_Validate_Callback(array($this, 'validateAmount')),
            ),
        ));
        
        $this->addElement('Textarea', 'body', array(
            'label' => 'Comments',
            'order' => 4
        ));


        // init submit
        $this->addElement('Button', 'submit', array(
            'label' => 'Send',
            'order' => 5,
            'type' => 'submit',
            'ignore' => true
        ));
    }

    public function validateAmount($value) {
        if ($value > $this->_balance) {
            $this->amount->getValidator('Engine_Validate_Callback')->setMessage('Do not exceed your account balance.');
            return false;
        }
        if (!is_numeric($value)) {
            $this->amount->getValidator('Engine_Validate_Callback')->setMessage('Please enter numeric.');
            return false;
        }
        if ($value <= 0) {
            $this->amount->getValidator('Engine_Validate_Callback')->setMessage('Big 0.');
            return false;
        }
        return true;
    }

}