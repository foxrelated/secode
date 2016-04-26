<?php

class Ynfullslider_Model_DbTable_Slides extends Engine_Db_Table
{
    protected $_rowClass = 'Ynfullslider_Model_Slide';
    protected $_serializedColumns = array('params');

    public function updateSlideOrder($order)
    {
        foreach ($order as $id => $slide_id) {
            if ($slide_id) {
                $where = array (
                    $this->getAdapter()->quoteInto('slide_id = ?', $slide_id)
                );
                $data = array ('slide_order' => $id);
                $this->update($data, $where);
            }
        }
    }

    public function cloneSlide($slide, $newSliderId = null)
    {
        if (!$slide) {
            return false;
        }

        $slideObj = $slide->toArray();
        // unset id and set container slider for new slide or clone in the same slider
        $slideObj['slide_id'] = null;
        if ($newSliderId)
            $slideObj['slider_id'] = $newSliderId;

        //create new slider
        $newSlide = $this->createRow();
        $newSlide->setFromArray($slideObj);
        $newSlide->save();

        //@TODO: clone elements
        //$slide->cloneElements();

        return true;
    }
}