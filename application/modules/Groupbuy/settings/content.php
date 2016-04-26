<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: content.php
 * @author     Minh Nguyen
 */
return array(
   array(
    'title' => 'Menu Deals',
    'description' => 'Displays menu deals on browse deals page.',
    'category' => 'GroupBuy',
    'type' => 'widget',
    'name' => 'groupbuy.menu-deals',
  ),
  array(
    'title' => 'Listing Deals',
    'description' => 'Displays listing deal on browse deals page.',
    'category' => 'GroupBuy',
    'type' => 'widget',
    'name' => 'groupbuy.listing-deals',
  ),
   array(
    'title' => 'Search Listing Deals',
    'description' => 'Displays search listing deal on listing deals page.',
    'category' => 'GroupBuy',
    'type' => 'widget',
    'name' => 'groupbuy.search-listing-deals',
  ),
  array(
    'title' => 'Featured Deals',
    'description' => 'Displays featured deal on browse deals page.',
    'category' => 'GroupBuy',
    'type' => 'widget',
    'name' => 'groupbuy.featured-deals',
  ),
    array(
    'title' => 'Search Deals',
    'description' => 'Displays search deal on browse deals page.',
    'category' => 'GroupBuy',
    'type' => 'widget',
    'name' => 'groupbuy.search-deals',
  ),
  array(
    'title' => 'Profile Buy Deals',
    'description' => 'Displays buy deals on user profile page.',
    'category' => 'GroupBuy',
    'type' => 'widget',
    'name' => 'groupbuy.profile-buy-deals',
    'defaultParams' => array(
      'title' => 'Bought Deals',
    ),
  ),
  array(
    'title' => 'Profile Sell Deals',
    'description' => 'Displays sell deals on user profile page.',
    'category' => 'GroupBuy',
    'type' => 'widget',
    'name' => 'groupbuy.profile-sell-deals',
     'defaultParams' => array(
      'title' => 'Posted Deals',
    ),
  ),
   array(
    'title' => 'Latest Deals',
    'description' => 'Displays latest deals on home page.',
    'category' => 'GroupBuy',
    'type' => 'widget',
    'name' => 'groupbuy.latest-deals',
    'defaultParams' => array(
      'title' => 'Latest Deals',
    ),
	'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
          )
        ),
        array(
          'Text',
          'max',
           array(
            'label' => 'Number of latest deals show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
  ),
   array(
    'title' => 'Most Rated Deals',
    'description' => 'Displays most rated deals on home page.',
    'category' => 'GroupBuy',
    'type' => 'widget',
    'name' => 'groupbuy.most-rated-deals',
    'defaultParams' => array(
      'title' => 'Most Rated Deals',
    ),
	'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
          )
        ),
        array(
          'Text',
          'max',
           array(
            'label' => 'Number of most rated deals show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
  ),
     array(
    'title' => 'Most Liked Deals',
    'description' => 'Displays most liked deals on home page.',
    'category' => 'GroupBuy',
    'type' => 'widget',
    'name' => 'groupbuy.most-liked-deals',
    'defaultParams' => array(
      'title' => 'Most Liked Deals',
    ),
	'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
          )
        ),
        array(
          'Text',
          'max',
           array(
            'label' => 'Number of most liked deals show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
  ),array(
    'title' => 'Featured Deals',
    'description' => 'Displays featured deals on home page.',
    'category' => 'GroupBuy',
    'type' => 'widget',
    'name' => 'groupbuy.most-great-deals',
    'defaultParams' => array(
      'title' => 'Featured Deals',
    ),
	'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
          )
        ),
        array(
          'Text',
          'max',
           array(
            'label' => 'Number of most great deals show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
  ),
      array(
    'title' => 'Email Subscription',
    'description' => 'Displays email subscription form.',
    'category' => 'GroupBuy',
    'type' => 'widget',
    'name' => 'groupbuy.email-form',
  ),
) ?>