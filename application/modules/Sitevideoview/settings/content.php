<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideoview
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2012-06-028 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    array(
      'title' => 'Profile Videos',
      'description' => 'Displays a member\'s videos on their profile.',
      'category' => 'Video Viewer Extension',
      'type' => 'widget',
      'name' => 'sitevideoview.profile-videos',
      'isPaginated' => true,
      'defaultParams' => array(
          'title' => 'Videos',
        ),
      'requirements' => array(
          'subject' => 'user',
        ),
    ),
    array(
      'title' => 'Popular Videos',
      'description' => 'Displays a list of most viewed videos.',
      'category' => 'Video Viewer Extension',
      'type' => 'widget',
      'name' => 'sitevideoview.list-popular-videos',
      'isPaginated' => true,
      'defaultParams' => array(
          'title' => 'Popular Videos',
        ),
      'requirements' => array(
          'no-subject',
        ),
      'adminForm' => array(
        'elements' => array(
          array('Radio', 'popularType',
            array(
              'label' => 'Popular Type',
              'multiOptions' => array(
                  'rating' => 'Rating',
                  'view' => 'Views',
                  'comment' => 'Comments',
              ),
              'value' => 'view',
            )
          ),
        )
      ),
    ),
    array(
      'title' => 'Recent Videos',
      'description' => 'Displays a list of recently uploaded videos.',
      'category' => 'Video Viewer Extension',
      'type' => 'widget',
      'name' => 'sitevideoview.list-recent-videos',
      'isPaginated' => true,
      'defaultParams' => array(
          'title' => 'Recent Videos',
      ),
      'requirements' => array(
          'no-subject',
      ),
      'adminForm' => array(
        'elements' => array(
          array(
            'Radio',
            'recentType',
            array(
              'label' => 'Recent Type',
              'multiOptions' => array(
                  'creation' => 'Creation Date',
                  'modified' => 'Modified Date',
              ),
              'value' => 'creation',
            )
          ),
        )
      ),
    ),
   )
?>