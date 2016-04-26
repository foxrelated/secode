<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Seshtmlbackground
 * @package    Seshtmlbackground
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: content.php 2015-10-22 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
$arrayGallery = array();
$results = Engine_Api::_()->getDbtable('galleries', 'seshtmlbackground')->getGallery(array('fetchAll' => true));
if (count($results) > 0) {
  foreach ($results as $gallery)
    $arrayGallery[$gallery['gallery_id']] = $gallery['gallery_name'];
}
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $banner_options[] = '';
    $path = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
    foreach ($path as $file) {
      if ($file->isDot() || !$file->isFile())
        continue;
      $base_name = basename($file->getFilename());
      if (!($pos = strrpos($base_name, '.')))
        continue;
      $extension = strtolower(ltrim(substr($base_name, $pos), '.'));
      if (!in_array($extension, array('gif', 'jpg', 'jpeg', 'png')))
        continue;
      $banner_options['public/admin/' . $base_name] = $base_name;
    }
    $fileLink = $view->baseUrl() . '/admin/files/';
return array(
		array(
        'title' => 'SES - HTML5 Background - HTML5 Videos & Photos Background',
        'description' => 'This widget displays video / image slideshow as chosen by you from the "Manage Slides" section of HTML5 Videos & Photos Background Plugin.',
        'category' => 'SES - HTML5 Videos & Photos Background Plugin',
        'type' => 'widget',
        'name' => 'seshtmlbackground.slideshow',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'gallery_id',
                    array(
                        'label' => 'Choose the HTML5 Background to be shown in this widget.',
                        'multiOptions' => $arrayGallery,
                        'value' => 1,
                    )
                ),
                array(
                    'Select',
                    'full_width',
                    array(
                        'label' => 'Do you want to show this HTML5 Background in full width?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Select',
                    'logo',
                    array(
                        'label' => 'Do you want to show logo in this HTML5 Background?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No'
                        ),
                        'value' => 1,
                    )
                ),
								array(
                    'Select',
                    'logo_url',
                    array(
                        'label' => 'Do you want to show different logo in this HTML5 Background,this setting only work if you select Yes in above setting and leave blank if you want to show same logo as site logo?',
                        'multiOptions' => $banner_options,
                    )
                ),
                array(
                    'Select',
                    'main_navigation',
                    array(
                        'label' => 'Do you want to show Main Navigation Menu in this HTML5 Background?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No'
                        ),
                        'value' => 1,
                    )
                ),
								array(
                    'Select',
                    'mini_navigation',
                    array(
                        'label' => 'Do you want to show Mini Navigation Menu on this HTML5 Background?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No'
                        ),
                        'value' => 1,
                    )
                ),
								array(
                    'Select',
                    'autoplay',
                    array(
                        'label' => 'Do you want Videos and Photos to autoplay in this HTML5 Background?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No'
                        ),
                        'value' => 1,
                    )
                ),
								array(
                    'Select',
                    'thumbnail',
                    array(
                        'label' => 'Do you want to show thumbnails of videos and photos in this HTML5 Background?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No'
                        ),
                        'value' => 1,
                    )
                ),
								array(
                    'Select',
                    'searchEnable',
                    array(
                        'label' => 'Do you want to show AJAX based Global Search in this HTML5 Background?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter the height of this HTML5 Background (in pixels).',
                        'value' => 583,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
								array(
                    'Text',
                    'autoplaydelay',
                    array(
                        'label' => 'Enter the transition delay time for the photos to be displayed in this HTML background. [This setting will not effect on videos.]',
                        'value' => 5000,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                array(
                    'Text',
                    'signupformtopmargin',
                    array(
                        'label' => 'Enter the margin from top of Signup Form to be shown in this HTML5 Background (in pixels).',
                        'value' => 60,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
            ),
        ),
    
		)
);
?>