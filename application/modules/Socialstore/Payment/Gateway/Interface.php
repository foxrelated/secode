<?php

interface Socialstore_Payment_Gateway_Interface {
	
	/**
     * Process payment request
     *
     * @param Socialstore_Payment_Request_Interface $request
     * @throws Socialstore_Payment_Extension
     * @return Socialstore_Payment_Response_Interface
     */
    public function process(Socialstore_Payment_Request_Interface $request);

    /**
     * Check if action is available on gateway level
     *
     * @param string $action
     * @return boolean
     */
    public function isActionAvailable($action);
}
