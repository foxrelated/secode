<?php

interface Socialstore_Payment_Response_Interface
{
    public function __construct($status);
    public function getStatus();
    public function isSuccess();
}