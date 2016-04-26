<?php

class Ynfullslider_Model_DbTable_Sliders extends Engine_Db_Table
{
    protected $_rowClass = 'Ynfullslider_Model_Slider';
    protected $_serializedColumns = array('params');

    public function getSliderPaginator($params = array())
    {
        return Zend_Paginator::factory($this->getSliderSelect($params));
    }

    public function getSliderSelect($params = array())
    {
        $select = $this->select();

        // Process search params
        if ( isset($params['title']) ) {
            $select->where('title LIKE ?', '%'.$params['title'].'%');
        }

        // Process filter params
        if ( isset($params['status']) && $params['status']) {
            if ($params['status'] == 'upcoming')
                $select->where("valid_from > FROM_UNIXTIME(?) AND unlimited = 0", time());
            else if ($params['status'] == 'expired')
                $select->where("valid_to < FROM_UNIXTIME(?) AND unlimited = 0", time());
            else
                $select->where("(valid_to > FROM_UNIXTIME(?) AND valid_from < FROM_UNIXTIME(?)) OR unlimited = 1", time(), time());
        }
        $select->order('slider_id DESC');

        return $select;
    }

    public function cloneSlider($slider = null, $newSliderTitle = null)
    {
        if (!$slider) {
            return false;
        }

        $sliderObj = $slider->toArray();
        // unset id and set title for cloned slider, if no title is set, old title will be used
        $sliderObj['slider_id'] = null;
        if ($newSliderTitle)
            $sliderObj['title'] = $newSliderTitle;

        //create new slider
        $newSlider = $this->createRow();
        $newSlider->setFromArray($sliderObj);
        $newSlider->save();

        // clone slides and put in new slider
        $slider->cloneSlides($newSlider->getIdentity());

        return true;
    }

    // GET ONGOING SLIDERS
    public function getActiveSliders()
    {
        $activeSliderIds = array();
        $contentTable = Engine_Api::_()->getDbTable('content', 'core');
        $contentRowset = $contentTable->fetchAll($contentTable->select()->where('name = ?', 'ynfullslider.slider-container'));
        foreach ($contentRowset as $content) {
            $params = $content->params;
            if (isset($params['slider_id'])) {
                $activeSliderIds[] = $params['slider_id'];
            }
        }
        return $activeSliderIds;
    }
}