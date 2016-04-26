<?php

class Ynmobile_Helper_Ynlisting_Album extends Ynmobile_Helper_Album{

    function getYnmobileApi(){
        return Engine_Api::_()->getApi('ynlisting','ynmobile');
    }

    function field_imgIcon(){
        $this->_field_img('thumb.normal','imgIcon');
    }

    function field_imgNormal(){
        $this->_field_img('thumb.normal','sPhotoUrl');
    }

    function field_imgFull(){
        $this->_field_img('','sFullPhotoUrl');
    }

    function field_imgProfile(){
        $this->_field_img('thumb.normal','sProfilePhotoUrl');
    }

    function _field_img($type, $key){

        if($this->entry->photo_id){
            $url = $this->entry->getPhotoUrl($type);
            $this->data[$key] =  $url?$this->finalizeUrl($url):$this->getNoImg($type);
        }else if ($photo =  $this->entry->getFirstCollectible()){
            $url = 	$photo->getPhotoUrl($type);
            $this->data[$key] =  $url?$this->finalizeUrl($url):$this->getNoImg($type);
        }else {
            $this->data[$key] =  $this->getNoImg($type);
        }
    }

    function field_canComment(){
        $this->data['bShowComment'] = 1;
        $this->data['bCanComment'] =  1;
        $this->data['bCanLike'] = 1;
    }
}
