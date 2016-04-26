<?php

class Ynfullslider_Model_Slider extends Core_Model_Item_Abstract {

    /*----- Properties -----*/
    protected $_parent_type = 'user';
    protected $_parent_is_owner = true;
    protected $_defaultParams = array(
        'width_option' => 0,
        'spacing_top' => 20,
        'spacing_bottom' => 20,
        'max_height' => 350,
        'no_of_slides_per_page' => 1,
        'delay_time' => 5000,
        'shuffle' => 1,
        'random_transition' => 1,
        'transition_id' => 1,
        'transition_duration' => 500,
        'navigator_id' => 1,
        'navigator_color' => '#555555',
        'background_option' => 0,
        'background_image_url' => '',
        'background_image_repeat' => 'repeat',
        'background_image_size' => 'auto',
        'background_image_position' => 'left top',
        'background_color' => '#929000',
        'background_shadow_id' => '1',
        'background_border_color' => '#555555',
        'background_border_width' => '1',
        'background_border_style' => 'solid',
    );

    public function getSlides($params = array(), $includeHidden = true)
    {
        $table = Engine_Api::_()->getDbTable('slides', 'ynfullslider');
        $select = $table -> select() -> where('slider_id = ?', $this->slider_id)->order('slide_order ASC')->order('slide_id ASC');

        if (!$includeHidden) {
            $select->where('show_slide = 1');
        }

        // Process search params
        if ( isset($params['title']) ) {
            $select->where('title LIKE ?', '%'.$params['title'].'%');
        }

        return $table->fetchAll($select);
    }


    public function getSlideCount()
    {
        return count($this->getSlides());
    }

    public function setParams($params = array())
    {
        $this->params = array_merge($this->getParams(), array_intersect_key($params, $this->getParams()));
    }

    public function getParams()
    {
        if (isset($this->params) && $this->params)
            return array_merge($this->_defaultParams, $this->params);
        else
            return $this->_defaultParams;
    }

    public function getThumbnailStyle()
    {
        $params = $this->getParams();
        if ($params['background_option']) {
            $photoUrl = $this->getPhotoUrl('thumb.profile');
            if ($photoUrl)
                return 'background-image: url(' . $photoUrl . ')';
            else
                return 'background-image: url(' . $params['background_image_url'] . ')';
        } else {
            return 'background-color:' . $params['background_color'];
        }
    }

    public function cloneSlider($newSliderTitle = null)
    {
        $table = $this->getTable();
        $table->cloneSlider($this, $newSliderTitle);
    }


    public function cloneSlides($sliderId = null)
    {
        $slides = $this->getSlides();
        if (!count($slides)) return;

        $slideTable = Engine_Api::_()->getDbTable('slides', 'ynfullslider');
        foreach ($slides as $slide){
            $slideTable->cloneSlide($slide, $sliderId);
        }
    }

    public function deleteAllSlides() {
        $slides = $this->getSlides(array(), true);
        foreach ($slides as $slide) {
            $slide->delete();
        }
    }
    public function getCurrentState(){
        $now = new DateTime();
        $validToDate = new DateTime($this->valid_to);
        $validFromDate = new DateTime($this->valid_from);
        $state = 'ongoing';
        if ($now < $validFromDate && !$this->unlimited)
            $state = 'upcomming';
        else if($now > $validToDate && !$this->unlimited)
            $state = 'expired';
        return $state;
    }
}