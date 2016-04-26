<?php

class Ynfullslider_Widget_SliderContainerController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //GET SLIDER
        $params = $this -> _getAllParams();
        $sliderId = $params['slider_id'];
        $slider = Engine_Api::_()->getItem('ynfullslider_slider', $sliderId);
        if (!$slider) {
            $this->setNoRender();
        }

        if (!$slider->unlimited) {
            $now = new DateTime();
            $validFromDate = new DateTime($slider->valid_from);
            $validToDate = new DateTime($slider->valid_to);
            if ($validFromDate >= $now || $validToDate <= $now) {
                $this->setNoRender();
            }
        }

        $this->view->slider = $slider;
    }
}