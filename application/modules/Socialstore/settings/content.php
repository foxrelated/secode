<?php
defined("_ENGINE") or die("Access denied");

return array(
   array(
    'title' => 'Store Menu',
    'description' => 'Displays store menu on front end.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.main-menu',
  ),

  array(
    'title' => 'My Store Mini Menu',
    'description' => 'Displays my store mini menu on front end.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.menu-mystore-mini',
  ),
  array(
    'title' => 'Store Categories',
    'description' => 'Displays store categories.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.product-categories',
  ),
  array(
    'title' => 'Product Categories',
    'description' => 'Displays product categories of a store.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.store-product-categories',
  	'defaultParams' => array(
      'title' => 'Product Categories',
    ),
  ),
  array(
    'title' => 'Most Rated Stores',
    'description' => 'Displays most rated socialstore.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.most-rated-stores',
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
         
           array(
             'label' => 'Number of Stores show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    'defaultParams' => array(
      'title' => 'Most Rated Stores',
    ),
  ),
  array(
    'title' => 'Most Liked Products',
    'description' => 'Displays most liked products.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.most-like-products',
      'adminForm' => array(
      'elements' => array(
         array(
          'Text',
          'max',
         
           array(
             'label' => 'Number of Products show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    'defaultParams' => array(
      'title' => 'Most Liked Products',
    ),
  ),
  array(
    'title' => 'Most Liked Stores',
    'description' => 'Displays most liked stores.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.most-like-stores',
      'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
         
           array(
             'label' => 'Number of Stores show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    'defaultParams' => array(
      'title' => 'Most Liked Stores',
    ),
  ),
  array(
    'title' => 'Most Commented Stores',
    'description' => 'Displays most commented stores.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.most-commented-stores',
      'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
         
           array(
             'label' => 'Number of Stores show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    'defaultParams' => array(
      'title' => 'Most Commented Stores',
    ),
  ),
  array(
    'title' => 'Recent Stores',
    'description' => 'Displays recent stores.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.recent-stores',
      'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
         
           array(
             'label' => 'Number of Stores show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Hot Stores',
    'description' => 'Displays stores with most sold products.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.hot-stores',
    'defaultParams' => array(
      'title' => 'Hot Stores',
    ),
  ),
  array(
    'title' => 'Top Selling Products',
    'description' => 'Displays top selling products.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.top-sold-products',
      'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
         
           array(
             'label' => 'Number of Products show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    'defaultParams' => array(
      'title' => 'Top Selling Products',
    ),
  ),
  array(
    'title' => 'Most Followed Stores',
    'description' => 'Most followed stores.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.most-followed-stores',
      'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
         
           array(
             'label' => 'Number of Stores show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    'defaultParams' => array(
      'title' => 'Most Followed Stores',
    ),
  ),
  array(
    'title' => 'Payment Menu',
    'description' => 'Payment menu.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.payment-menu',
  ),
    array(
    'title' => 'My Following Stores',
    'description' => 'Display my following stores.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.my-following-stores',
  ),
  /*array(
    'title' => 'My Store',
    'description' => 'Display my store.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.my-store',
  ),*/
  /*array(
    'title' => 'My Products',
    'description' => 'Display my products.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.my-products',
  ),*/
  array(
    'title' => 'My Favourite Products',
    'description' => 'Display my favourite products.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.my-favourite-products',
  ),
  array(
    'title' => 'Random Featured Stores',
    'description' => 'Display featured stores randomly',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.random-featured-stores',
      'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
         
           array(
             'label' => 'Number of Stores show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    'defaultParams' => array(
      'title' => 'Most Featured Stores',
    ),
  ),
  array(
    'title' => 'Profile Followed Stores',
    'description' => 'Display followed stores on member profile.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.profile-followed-stores',
     'defaultParams' => array(
      'title' => 'Followed Stores',
    ),
  ),
  array(
    'title' => 'Profile Liked Stores',
    'description' => 'Display liked stores on member profile.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.profile-like-stores',
    'defaultParams' => array(
      'title' => 'Liked Stores',
    ),
  ),
  /*array(
    'title' => 'Featured Stores',
    'description' => 'Display a featured stores randomly',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.featured-store',
  ),*/
  array(
    'title' => 'Recent Products',
    'description' => 'Displays recent products.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.recent-products',
      'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
         
           array(
             'label' => 'Number of Products show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
  ),  
  array(
    'title' => 'Most Rated Products',
    'description' => 'Displays most rated products.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.most-rated-products',
        'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
         
           array(
             'label' => 'Number of Products show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    'defaultParams' => array(
      'title' => 'Most Rated Products',
    ),
  ),
  array(
    'title' => 'Most Favourite Products',
    'description' => 'Displays most favourite products.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.most-favourite-products',
        'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
         
           array(
             'label' => 'Number of Products show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    'defaultParams' => array(
      'title' => 'Most Favourite Products',
    ),
  ),
  array(
    'title' => 'Most Commented Products',
    'description' => 'Displays most commented products.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.most-commented-products',
        'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
         
           array(
             'label' => 'Number of Products show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
     'defaultParams' => array(
      'title' => 'Most Commented Products',
    ),
  ),
  array(
    'title' => 'Random Featured Products',
    'description' => 'Displays featured products randomly.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.random-featured-products',
        'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
         
           array(
             'label' => 'Number of Products show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    'defaultParams' => array(
      'title' => 'Random Featured Products',
    ),
  ),
  array(
    'title' => 'Profile Favourite Products',
    'description' => 'Displays favourite products on member profile.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.profile-favourite-products',
    'defaultParams' => array(
      'title' => 'Favourited Products',
    ),
  ),
  /*array(
    'title' => 'Profile Wishlist Products',
    'description' => 'Displays wishlist products on member profile.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.profile-wishlist-products',
    'defaultParams' => array(
      'title' => 'Wishlist',
    ),
  ),*/
  array(
    'title' => 'Profile Liked Products',
    'description' => 'Displays liked products on member profile.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.profile-like-products',
    'defaultParams' => array(
      'title' => 'Liked Products',
    ),
  ),
  /*array(
    'title' => 'Featured Products',
    'description' => 'Displays most featured products randomly.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.featured-product'
  ),*/
  array(
    'title' => 'Store Slideshow',
    'description' => 'Displays slideshow of stores.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.store-slideshow',
          'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
         
           array(
             'label' => 'Number of Stores show on slideshow.',
            'value' => 5,
            
          )
        ),
      )
    ),
  ),  
  array(
    'title' => 'Product Slideshow',
    'description' => 'Displays slideshow of products.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.product-slideshow',
        'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
         
           array(
             'label' => 'Number of Products show on slideshow.',
            'value' => 5,
            
          )
        ),
      )
    ),
  ),  
  array(
    'title' => 'Search Products In General',
    'description' => 'Seach Products In General.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.search-product'
  ),   
  array(
    'title' => 'Search Stores In General',
    'description' => 'Search Stores In General.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.search-store'
  ),  
  array(
    'title' => 'Search Products In Store',
    'description' => 'Seach Products In Particular Store.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.search-product-in-store'
  ),
  array(
    'title' => 'Listing Stores',
    'description' => 'Listing Stores.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.listing-stores'
  ),
  array(
    'title' => 'Listing Products',
    'description' => 'Listing Products.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.listing-products'
  ),
  array(
    'title' => 'Listing Products of a Store',
    'description' => 'Listing Products of a particular Store.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.store-listing-products',
  	'defaultParams' => array(
      'title' => 'Listing Products',
    ),
  ),
  array(
  	'title' => 'Product Description',
  	'description' => 'Display Description of Product in widget Product Detail (these two should alway be together)',
  	'category' => 'Store',
  	'type' => 'widget',
  	'name' => 'socialstore.product-description',
    'defaultParams' => array(
      'title' => 'Product Description',
    ),
  ),
  array(
    'title' => 'Main Recent Products',
    'description' => 'Display Recent Products on main columns.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.main-recent-products'
  ),
  array(
    'title' => 'Main Recent Stores',
    'description' => 'Display Recent Store On Main Columns.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.main-recent-stores'
  ),
  array(
    'title' => 'Store Detail',
    'description' => 'Displays detail of a Store.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.store-detail',
  ),
  array(
    'title' => 'Product Detail',
    'description' => 'Displays detail of a Product.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.product-detail',
  ),
  array(
    'title' => 'Store Short Description',
    'description' => 'Displays Store Short Description.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.store-front',
  ),
  array(
    'title' => 'Store Info',
    'description' => 'Displays Store Info.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.store-info',
  ),
  array(
    'title' => "Product's Video",
    'description' => 'Displays Video of the Product if there is any and Video plugin is installed on site.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'socialstore.product-video',
    'defaultParams' => array(
      'title' => "Product's Video",
    ),
  ),
);