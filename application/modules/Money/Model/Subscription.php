<?php

class Money_Model_Subscription extends Core_Model_Item_Abstract {

    protected $_searchTriggers = false;
    protected $_modifiedTriggers = false;
    protected $_user;
    protected $_gateway;
    protected $_package;
    protected $_statusChanged;

    public function getUser() {
        if (empty($this->user_id)) {
            return null;
        }
        if (null === $this->_user) {
            $this->_user = Engine_Api::_()->getItem('user', $this->user_id);
        }
        return $this->_user;
    }

    public function onPaymentSuccess() {
        $this->_statusChanged = false;
        if (in_array($this->status, array('initial', 'trial', 'pending', 'active'))) {

            // If the subscription is in initial or pending, set as active and
            // cancel any other active subscriptions
            if (in_array($this->status, array('initial', 'pending'))) {
                $this->setActive(true);
                Engine_Api::_()->getDbtable('subscriptions', 'money')
                        ->cancelAll($this->getUser(), 'User cancelled the subscription.', $this);
            }



            // Change status
            if ($this->status != 'active') {
                $this->status = 'active';
                $this->_statusChanged = true;
            }
        }
        $this->save();
        return $this;
    }

    public function setActive($flag = true, $deactivateOthers = null) {
        $this->active = true;

        if ((true === $flag && null === $deactivateOthers) ||
                $deactivateOthers === true) {
            $table = $this->getTable();
            $select = $table->select()
                    ->where('user_id = ?', $this->user_id)
                    ->where('active = ?', true)
            ;
            foreach ($table->fetchAll($select) as $otherSubscription) {
                $otherSubscription->setActive(false);
            }
        }

        $this->save();
        return $this;
    }

    public function onPaymentPending() {
        $this->_statusChanged = false;
        if (in_array($this->status, array('initial', 'trial', 'pending', 'active'))) {
            // Change status
            if ($this->status != 'pending') {
                $this->status = 'pending';
                $this->_statusChanged = true;
            }

            // Downgrade and log out user if active
            if ($this->active) {
                // @todo should we do this?
                // Downgrade user
                $this->downgradeUser();

                // Remove active sessions?
                //Engine_Api::_()->getDbtable('session', 'core')->removeSessionByAuthId($this->user_id);
            }
        }
        $this->save();

        // Check if the member should be enabled
        $user = $this->getUser();
        $user->enabled = true; // This will get set correctly in the update hook
        $user->save();

        return $this;
    }

    public function onPaymentFailure() {
        $this->_statusChanged = false;
        if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'overdue'))) {
            // Change status
            if ($this->status != 'overdue') {
                $this->status = 'overdue';
                $this->_statusChanged = true;
            }

            // Downgrade and log out user if active
            if ($this->active) {
                // Downgrade user
                $this->downgradeUser();

                // Remove active sessions?
                Engine_Api::_()->getDbtable('session', 'core')->removeSessionByAuthId($this->user_id);
            }
        }
        $this->save();


        return $this;
    }

    public function cancel() {
        // Try to cancel recurring payments in the gateway
        if (!empty($this->gateway_id) && !empty($this->gateway_profile_id)) {
            try {
                $gateway = Engine_Api::_()->getItem('money_gateway', $this->gateway_id);
                if ($gateway) {
                    $gatewayPlugin = $gateway->getPlugin();
                    if (method_exists($gatewayPlugin, 'cancelSubscription')) {
                        $gatewayPlugin->cancelSubscription($this->gateway_profile_id);
                    }
                }
            } catch (Exception $e) {
                // Silence?
            }
        }
        // Cancel this row
        $this->active = false; // Need to do this to prevent clearing the user's session
        $this->onCancel();
        return $this;
    }

    public function getGateway() {
        if (empty($this->gateway_id)) {
            return null;
        }
        if (null === $this->_gateway) {
            $this->_gateway = Engine_Api::_()->getItem('money_gateway', $this->gateway_id);
        }
        return $this->_gateway;
    }
    
    public function onCancel()
  {
    $this->_statusChanged = false;
    if( in_array($this->status, array('initial', 'trial', 'pending', 'active', 'overdue', 'cancelled')) ) {
      // Change status
      if( $this->status != 'cancelled' ) {
        $this->status = 'cancelled';
        $this->_statusChanged = true;
      }

      // Downgrade and log out user if active
      if( $this->active ) {
        Engine_Api::_()->getDbtable('session', 'core')->removeSessionByAuthId($this->user_id);
      }
    }
    $this->save();

    
    return $this;
  }
  
   public function didStatusChange()
  {
    return (bool) $this->_statusChanged;
  }
  
  public function clearStatusChanged()
  {
    $this->_statusChanged = null;
    return $this;
  }
  
  public function getPackage()
  {
    if( empty($this->package_id) ) {
      return null;
    }
    if( null === $this->_package ) {
      $this->_package = Engine_Api::_()->getItem('money_package', $this->package_id);
    }
    return $this->_package;
  }

}