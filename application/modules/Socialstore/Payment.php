<?php

class Socialstore_Payment
{
    /**
     * Order actions
     */
    const ACTION_INIT   = 'init';
    const ACTION_AUTH   = 'auth';
    const ACTION_SALE   = 'sale';

    /**
     * Transaction actions
     */
    const ACTION_CAPTURE    = 'capture';
    const ACTION_VOID       = 'void';
    const ACTION_REFUND     = 'refund';
    const ACTION_STATUS     = 'status';

    /**
     * Allowed order action
     */
    protected $_orderActions = array(
        self::ACTION_INIT,
        self::ACTION_AUTH,
        self::ACTION_SALE,
    );

    /**
     * Allowed transaction actions
     */
    protected $_transactionActions = array(
        self::ACTION_CAPTURE,
        self::ACTION_VOID,
        self::ACTION_REFUND,
        self::ACTION_STATUS
    );

    /**
     * @var Socialstore_Payment_Options
     */
    protected $_options;

    /**
     * @var Socialstore_Payment_Gateway_Interface
     */
    protected $_gateway;
	
	protected $_paytype;

    /**
     * Class constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }
	
	public function getSandboxMode(){
		return true;
	}

    /**
     * Payment options setter
     *
     * Supported options:
     *  gateway - string
     *  gateway_config - array|Zend_Config
     *  gateway_is_custom - boolean
     *  request_class - string
     *  request_options - array
     *
     * @param array $options
     * @return Socialstore_Payment
     */
    public function setOptions(array $options)
    {
        $this->_options = new Socialstore_Payment_Options($options);
        if ($this->_options->has('gateway')) {
            $this->_gateway = self::factory(
                $this->_options->get('gateway'),
                $this->_options->get('gateway_config'),
                $this->_options->get('gateway_is_custom'),
                $this->getSandboxMode()
            );
        }
        return $this;
    }

    /**
     * Factory to construct payment gateway instance
     *
     * @param string            $code gateway code or class name
     * @param array|Zend_Config $config gateway credentials
     * @param boolean           $custom flag that determine custom class name usage
     * @throws Socialstore_Payment_Exception
     * @return Socialstore_Payment_Gateway_Abstract
     */
    static public function factory($code, $config=null, $custom=false, $sandboxMode =  null)
    {
        if ($custom) {
            $class = $code;
        } else {
            $class = 'Socialstore_Payment_Gateway_'.str_replace(' ', '', ucwords(str_replace('_', ' ', $code)));
        }
		
		if($config == null){
			$config =  Socialstore_Model_DbTable_Gateways::getConfig($code);
		}

		if($sandboxMode === NULL){
			$sandboxMode =  Socialstore_Api_Core::isSandboxMode();
		}
		if ($config == null) {
			return false;
		}		
        return new $class($config, $sandboxMode);
    }

    /**
     * Gateway setter
     *
     * @param Socialstore_Payment_Gateway_Interface $gateway
     * @return Socialstore_Payment
     */
    public function setGateway(Socialstore_Payment_Gateway_Interface $gateway)
    {
        $this->_gateway = $gateway;
        return $this;
    }

    /**
     * Gateway getter
     *
     * @throws Socialstore_Payment_Exception
     * @return Socialstore_Payment_Gateway_Abstract
     */
    public function getGateway()
    {
        if (!$this->_gateway) {
            throw new Socialstore_Payment_Exception('Payment gateway is not defined');
        }
        return $this->_gateway;
    }

    /**
     * Get payment request options
     *
     * @param string $action
     * @return Socialstore_Payment_Request_Interface
     */
    public function getRequest($action)
    {
        if ($this->_options->has('request_class')) {
            $class = $this->_options->get('request_class');
        } else {
            $class = 'Socialstore_Payment_Request';
        }
        Zend_Loader::loadClass($class);
        $request = new $class($action);

        if ($this->_options->has('request_options')) {
            $options = $this->_options->get('request_options');
            if (is_array($options)) {
                $options = new Socialstore_Payment_Options($options);
            }
            if ($options instanceof Socialstore_Payment_Options) {
                $request->setOptions($options);
            }
        }
        return $request;
    }

    /**
     * Process payment request
     *
     * @param $request
     * @return Socialstore_Payment_Response
     */
    public function process(Socialstore_Payment_Request_Interface $request)
    {
        return $this->_gateway->process($request);
    }

    /**
     * Process action on order
     *
     * @param Socialstore_Payment_Order       $order
     * @param Socialstore_Payment_Method_Interface $method
     * @param string                           $action
     * @throws Socialstore_Payment_Exception
     * @return Socialstore_Payment_Response
     */
    public function processOrder($order, $method, $action)
    {
        if (!in_array($action, $this->_orderActions)) {
            throw new Socialstore_Payment_Exception('Not supported order action: '.$action);
        }
        $request = $this->getRequest($action);
        $request->setOrder($order);
        $request->setMethod($method);
        return $this->_gateway->process($request);
    }

    /**
     * Process action on previous transaction
     *
     * @param $transaction
     * @param $action
     * @throws Socialstore_Payment_Exception
     * @return Socialstore_Payment_Response
     */
    public function processTransaction($transaction, $action)
    {
        if (!in_array($action, $this->_transactionActions)) {
            throw new Exception('Not supported transaction action: '.$action);
        }

        $request = $this->getRequest($action);
        $request->setTransaction($transaction);
        return $this->_gateway->process($request);
    }
	
	
}
