<?php

class Socialstore_Payment_Request_Requirements
{
    const TEST_ORDER        = 'order';
    const TEST_TRANSACTION  = 'transaction';
    const TEST_METHOD       = 'method';
    const TEST_OPTIONS      = 'options';

    /**
     * Array of requirements per action
     *
     * @var array
     */
    protected $_requirements;

    /**
     * Class constructor
     *
     * @param array $requirements
     */
    public function __construct($requirements = array())
    {
        $this->_requirements = $requirements;
    }

    /**
     * Get requirements by action
     *
     * @param string $action
     * @return array
     */
    public function get($action)
    {
        if (isset($this->_requirements[$action])) {
            return $this->_requirements[$action];
        } elseif (isset($this->_requirements['_default'])) {
            return $this->_requirements['_default'];
        }
        return array();
    }

    /**
     * Add requirements for specific action
     *
     * @param string $action action code
     * @param array  $requirements requirements
     * @return Socialstore_Payment_Request_Requirements
     */
    public function add($action, array $requirements)
    {
        if (isset($this->_requirements[$action])) {
            $this->_requirements[$action] = array_merge($this->_requirements[$action], $requirements);
        } else {
            $this->_requirements[$action] = $requirements;
        }
        return $this->_requirements;
    }

    /**
     * Define order requirements per action(s)
     *
     * @param string|array $action
     * @param string|array $requirement
     * @return Socialstore_Payment_Request_Requirements
     */
    public function setOnOrder($action, $requirement)
    {
        $this->_set($action, self::TEST_ORDER, $requirement);
        return $this;
    }

    /**
     * Define transaction requirements per action(s)
     *
     * @param string|array $action
     * @param integer      $requirement
     * @return Socialstore_Payment_Request_Requirements
     */
    public function setOnTransaction($action, $requirement)
    {
        $this->_set($action, self::TEST_TRANSACTION, $requirement);
        return $this;
    }

    /**
     * Define method requirements per action(s)
     *
     * @param string|array  $action
     * @param array|boolean $requirement
     * @return Socialstore_Payment_Request_Requirements
     */
    public function setOnMethod($action, $requirement)
    {
        $this->_set($action, self::TEST_METHOD, $requirement);
        return $this;
    }

    /**
     * Define options requirements per action(s)
     *
     * @param string|array $action
     * @param array        $requirement
     * @return Socialstore_Payment_Request_Requirements
     */
    public function setOnOptions($action, $requirement)
    {
        $this->_set($action, self::TEST_OPTIONS, $requirement);
        return $this;
    }

    /**
     * Set requirement per action(s)
     *
     * @param string|array $action
     * @param string       $requirementType
     * @param mixed        $requiremets
     * @return Socialstore_Payment_Request_Requirements
     */
    protected function _set($actions, $requirementType, $requiremets)
    {
        if (!is_array($actions)) {
            $actions = array($actions);
        }
        foreach ($actions as $action) {
            $this->_requirements[$action][$requirementType] = $requiremets;
        }
        return $this;
    }
    /**
     * Validate request data based on request action requirements
     *
     * @param Socialstore_Payment_Request_Interface $requirements
     * @return array|true error messages array or true if validation was success
     */
    public function validate(Socialstore_Payment_Request_Interface $request)
    {
        $messages = array();
        $requirements = $this->get($request->getAction());
        foreach ($requirements as $key => $value) {
            if (!$value) {
                continue;
            }
            $result = $this->_validate($key, $value, $request);
            if (true !== $result) {
                if (is_array($result)) {
                    $messages += $result;
                } else {
                    $messages[] = $result;
                }
            }
        }
        if (empty($messages)) {
            return true;
        }
        return $messages;
    }

    /**
     * Validate conditions of particular test
     *
     * @param string                  $test test code
     * @param mixed                   $condition test conditions
     * @param Socialstore_Payment_Request $request original request
     * @return true|string
     */
    protected function _validate($test, $condition, $request)
    {
        $result = true;
        switch ($test) {
            case self::TEST_ORDER:
                $result = $this->_validateOrder($request, $condition);
                break;
            case self::TEST_TRANSACTION:
                $result = $this->_validateTransaction($request, $condition);
                break;
            case self::TEST_METHOD:
                $result = $this->_validateMethod($request, $condition);
                break;
            case self::TEST_OPTIONS:
                $result = $this->_validateOptions($request, $condition);
                break;
            default:
                break;
        }
        return $result;
    }

    /**
     * Validate request order
     *
     * @param Socialstore_Payment_Request $request original request
     * @param string|array            $condition
     * @return true|array array of error messages in case of wrong validation
     */
    protected function _validateOrder($request, $condition)
    {
        $result = true;
        if (!$request->getOrder()) {
            $result = 'Request order is not defined';
        }
        return $result;
    }

    /**
     * Validate request transaction
     *
     * @param Socialstore_Payment_Request $request original request
     * @param string|array            $condition
     * @return true|string
     */
    protected function _validateTransaction($request, $condition)
    {
        $result = true;
        if (!$request->getTransaction()) {
            $result = 'Request transaction is not defined';
        }
        return $result;
    }

    /**
     * Validate request payment method
     *
     * @param Socialstore_Payment_Request $request original request
     * @param string|array            $condition
     * @return true|string
     */
    protected function _validateMethod($request, $condition)
    {
        $result = true;
        $method = $request->getMethod();
		
        if ($method) {
            if (is_array($condition)) {
                $result = sprintf('Payment method must be instance of "%s"', implode(',', $condition));
                foreach ($condition as $class) {
                    if ($method instanceof $class) {
                        $result = true;
                        break;
                    }
                }
            }
        } else {
            if ($condition) {
                $result = 'Request payment method is not defined';
            }
        }
        return $result;
    }

    /**
     * Validate request options
     *
     * @param Socialstore_Payment_Request $request original request
     * @param string|array            $condition
     * @return true|string
     */
    protected function _validateOptions($request, $condition)
    {
        $result = true;
        $options = $request->getOptions();
        if ($options) {
            if (is_array($condition)) {
                $keys = array();
                foreach ($condition as $key) {
                    if (!$options->has($key)) {
                        $keys[] = $key;
                    }
                }
                if (!empty($keys)) {
                    $result = sprintf('Request options must have "%s" key(s)', implode(',', $keys));
                }
            }
        } else {
            $result = 'Request options are not defined';
        }
        return $result;
    }
}
