<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.isActivate', 0);
if ( empty($isActive) ) {
  return;
}
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Group Profile Albums'),
        'description' => $view->translate('This widget forms the Albums tab on the Group Profile and displays the albums of the Group. It also displays the photos added by the Group visitors other than the owner. It should be placed in the Tabbed Blocks area of the Group Profile.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.sitemobile-photos-sitegroup',
        'defaultParams' => array(
            'title' => 'Photos',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of albums to show)'),
                        'value' => 10,
												'validators' => array(
													array('Int', true),
													array('GreaterThan', true, array(0)),
												),
                    ),
                ),
               array(
                    'Text',
                    'itemCount_photo',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of photos to show in album)'),
                        'value' => 100,
												'validators' => array(
													array('Int', true),
													array('GreaterThan', true, array(0)),
												),
                    ),
                ),
                array(
                    'Radio',
                    'albumsorder',
                    array(
                        'label' => $view->translate('Select the order below to display the albums on your site.'),
                        'multiOptions' => array(
                            1 => 'Newer to older',
                            0 => 'Older to newer'
                        ),
                        'value' => 1,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Group Albums'),
        'description' => $view->translate('Displays the list of Albums from Groups created on your community. This widget should be placed in the widgetized Group Albums group. Results from the Search Group Albums form are also shown here.'),
        'category' => $view->translate('Groups'),
        'type' => 'widget',
        'name' => 'sitegroupalbum.sitegroup-album',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of albums to show)'),
                        'value' => 10,
												'validators' => array(
													array('Int', true),
													array('GreaterThan', true, array(0)),
												),
                    ),
                ),
            ),
        ),
    ),
    array(
			'title' => $view->translate('Group Album View'),
			'description' => $view->translate("This widget should be placed on the Group Album View Page."),
      'category' => $view->translate('Groups'),
			'type' => 'widget',
			'name' => 'sitegroupalbum.album-content',
			'defaultParams' => array(
					'title' => '',
					'titleCount' => true,
			),
			'adminForm' => array(
					'elements' => array(
							array(
									'Radio',
									'photosorder',
									array(
											'label' => $view->translate('Select the order below to display the photos on your site.'),
											'multiOptions' => array(
													1 => 'Newer to older',
													0 => 'Older to newer'
											),
											'value' => 1,
									)
							),
					),
			),
	),
)
?>