<?php

// get available category for widgets contain video
$sliderTable = Engine_Api::_() -> getDbTable('sliders', 'ynfullslider');
$sliders = $sliderTable->fetchAll($sliderTable->getSliderSelect());
$slidersOptions = array();
if (count($sliders)) {
    foreach ($sliders as $item)
    {
        $slidersOptions[$item['slider_id']] = $item->getTitle();
    }
} else {
    $slidersOptions[0] = 'None';
}

return array(
    array(
        'title' => 'Slider Container',
        'description' => 'Displays a slider on front page',
        'category' => 'YN - Fullslider',
        'type' => 'widget',
        'name' => 'ynfullslider.slider-container',
        'defaultParams' => array(
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'slider_id',
                    array(
                        'label' => 'Select a Slider',
                        'multiOptions' => $slidersOptions,
                    )
                ),
            )
        ),
    ),
);