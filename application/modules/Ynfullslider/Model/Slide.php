<?php

class Ynfullslider_Model_Slide extends Core_Model_Item_Abstract {

    /*----- Properties -----*/
    protected $_parent_type = 'ynfullslider_slider';
    protected $_defaultParams = array(
        'background_option' => 0, // 0: color, 1: image, 2: video
        'background_image_url' => '',
        'background_size' => 'cover',
        'background_repeat' => 'repeat',
        'background_position' => 'left top',
        'slide_background_color' => '#555555',
        'video_file_id' => '',
        'loop' => 1,
        'autoplay' => 0,
        'muted' => 0,
    );
    protected $_layerClasses = array(
        'text' => 'Ynfullslider_Model_Layer_Text',
        'button' => 'Ynfullslider_Model_Layer_Text',
        'image' => 'Ynfullslider_Model_Layer_Text',
        'video' => 'Ynfullslider_Model_Layer_Text'
    );

    public function setParams($params = array())
    {
        $this->params = array_merge($this->getParams(), array_intersect_key($params, $this->getParams()));
    }

    public function getParams()
    {
        if (isset($this->params) && $this->params)
            $params = array_merge($this->_defaultParams, $this->params);
        else
            $params = $this->_defaultParams;
        // get video url
        $params['background_video_url'] = '';
        if ($params['background_option'] == 2 && $params['video_file_id']) {
            $storage_file = Engine_Api::_() -> getItem('storage_file', $params['video_file_id']);
            if ($storage_file)
                $url = $storage_file->map();
//                $host = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
//                $proto = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off") ? 'https' : 'http';
//                $port = (isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80);
//                $uri = $proto . '://' . $host;
//                if ((('http' == $proto) && (80 != $port)) || (('https' == $proto) && (443 != $port)))
//                {
//                    $uri .= ':' . $port;
//                }
//                $url = $uri . '/' . ltrim($url, '/');
                $params['background_video_url'] = $url;
        }
        return $params;
    }

    public function cloneSlide()
    {
        $table = $this->getTable();
        $table->cloneSlide($this);

        //INCREASE SLIDE COUNT
        $slider = $this->getParentSlider();
        $slider->slide_count += 1;
        $slider->save();
    }

    public function getThumbnailStyle($getLargeSize)
    {
        $params = $this->getParams();
        if ($params['background_option'] == 0) {
            return 'background-color:' . $params['slide_background_color'];
        } else {
            $photoUrl = $this->getPhotoUrl($getLargeSize ? '' : 'thumb.profile');
            if ($photoUrl)
                return 'background-image: url(' . $photoUrl . ')';
            else
                return 'background:' . $params['slide_background_color'];
        }
    }

    public function getParentSlider()
    {
        return Engine_Api::_()->getItem('ynfullslider_slider', $this->slider_id);
    }

    public function getLayers()
    {
        $slideData = json_decode($this->slide_elements);
        $elementsOrder = json_decode($this->elements_order);
        $slideElements = array();
        foreach ($elementsOrder as $element) {
            if (isset($slideData->$element))
                $slideElements[] = $slideData->$element;
        }
        return $slideElements;
    }
}