<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: content.php 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
  array (
    'title' => $view->translate('Pokes'),
    'description' => $view->translate('Displays the pokes received by members. Members can poke back from here, or remove pokes. You are suggested to place this widget on the Member Home Page.'),
    'category' => 'Pokes',
    'type' => 'widget',
    'name' => 'poke.list-pokeusers',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Pokes',),
		'adminForm' => array(
		'elements' => array(
				array(
						'Radio',
						'user_photo',
						array(
								'label' => $view->translate('Do you want to display the photo of the member who poked?'),
								'multiOptions' => array(
										'1' => 'yes',
										'0' => 'no',
								),
								'value' => 1,
						)
				),
        array(
           'hidden',
           'nomobile',
           array(
             'label' => ''
           )
         ),
			),
		),
  ),
    array (
    'title' => $view->translate('Top Pokers'),
    'description' => $view->translate('Shows the users who have poked others the most, and the number of times they have poked others. You can choose the number of entries to show.'),
    'category' => 'Pokes',
    'type' => 'widget',
    'name' => 'poke.list-toppokers',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Top Pokers'),
    ), 
    array (
    'title' => $view->translate('Most Poked'),
    'description' => $view->translate('Shows the users who have been poked most number of times and the number of times they have been poked. You can choose the number of entries to show.'),
    'category' => 'Pokes',
    'type' => 'widget',
    'name' => 'poke.list-mostpokers',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Most Poked')
		),
    array (
    'title' => $view->translate('Recently Poked'),
    'description' => $view->translate('Shows the users who have been poked recently, and the person who has poked them. You can choose the number of entries to show.'),
    'category' => 'Pokes',
    'type' => 'widget',
    'name' => 'poke.list-recentpoked',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Recently Poked'),
    ),
    array (
    'title' => $view->translate('Poked Recently'),
    'description' => $view->translate('Shows the users who have poked others recently, and the person they have poked. You can choose the number of entries to show.'),
    'category' => 'Pokes',
    'type' => 'widget',
    'name' => 'poke.list-pokedrecently',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Poked Recently'),
    )    
)  
?>