<?php

class Ynmobile_Helper_Ynlisting_Photo extends Ynmobile_Helper_AlbumPhoto{
	function getYnmobileApi(){
       return Engine_Api::_()->getApi('ynlisting','ynmobile');
   	}	
}
