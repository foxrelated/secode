<?php

class Socialstore_Model_Address extends Core_Model_Item_Abstract{
	public function getStreet(){
		return $this->street;
	}
	
	public function getStreet2(){
		return $this->street2;
	}
	
	public function getCity(){
		return $this->city;
	}
	
	public function getRegion(){
		return $this->region;
	}
	
	public function getCountry(){
		return $this->country;
	}
	public function getCompany(){
		return $this->company;
	}
	public function getPostcode(){
		return $this->postcode;
	}
	
	public function getEmail(){
		return $this->email;
	}
	
	public function getFullName(){
		return sprintf('%s %s ', $this->firstname, $this->lastname);
	}
	
	public function getFirstName(){
		return $this->firstname;
	}
	
	public function getLastName(){
		return $this->lastname;
	}
	
	public function getPhone(){
		return $this->phone;
	}
	
	public function getFax(){
		return $this->fax;
	}
}
