<?php
return array(
		array(
	        'title' => 'Browse Menu',
	        'description' => 'Browse menu.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.browse-menu',
	        'isPaginated' => false,
		 ),
		 array(
	        'title' => 'Fundraising for Parent',
	        'description' => 'Campaign for parent detail page',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-for-parent',
	        'isPaginated' => false,
		 ),
		array(
			'title' => 'Campaigns Search',
			'description' => 'Search campaigns',
			'category' => 'Fundraising',
			'type' => 'widget',
			'name' => 'ynfundraising.campaigns-search',
			'isPaginated' => false,
		),
		array(
			'title' => 'Quick Browse Menu',
			'description' => 'Displays a small menu in the fundraising browse page',
			'category' => 'Fundraising',
			'type' => 'widget',
			'name' => 'ynfundraising.browse-menu-quick',
			'isPaginated' => false,
		),
		array(
			'title' => 'Requests Search',
			'description' => 'Search requests',
			'category' => 'Fundraising',
			'type' => 'widget',
			'name' => 'ynfundraising.requests-search',
			'isPaginated' => false,
		),
		array(
			'title' => 'Statistics Search',
			'description' => 'Search statistics',
			'category' => 'Fundraising',
			'type' => 'widget',
			'name' => 'ynfundraising.statistics-search',
			'isPaginated' => false,
		),
		array(
	        'title' => 'Campaigns Tags',
	        'description' => 'Campaigns Tags.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-tagscloud',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => 'Tags',
    		),
    	),
    	array(
	        'title' => 'Statistics',
	        'description' => 'Statistics.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-statistics',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => 'Statistics',
	    	),
    	),
    	array(
	        'title' => 'Top Donors',
	        'description' => 'Top Donors.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-topdonors',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => 'Top Donors',
	    	),
	    	'adminForm'=> array(
		      'elements' => array(
		          array(
		              'Text',
		              'number',
		               array(
		                'label' =>  'Number of donors to display',
		                'value' => '5',
		                'required' => true,
		                'validators' => array(
		                    array('Between',true,array(1,100)),
		                ),
		               ),
		           ),
		       ),
	       ),
    	),
    	array(
	        'title' => 'Featured Campaigns',
	        'description' => 'Featured Campaigns.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-featured',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => 'Featured Campaigns',
	    	),
	    	'adminForm'=> array(
		      'elements' => array(
		          array(
		              'Text',
		              'number',
		               array(
		                'label' =>  'Number of Campaigns to display',
		                'value' => '5',
		                'required' => true,
		                'validators' => array(
		                    array('Between',true,array(1,100)),
		                ),
		               ),
		           ),
		       ),
	       ),
    	),
    	array(
	        'title' => 'Recent Campaigns',
	        'description' => 'Recent Campaigns.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-recent',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => 'Recent Campaigns',
	    	),
	    	'adminForm'=> array(
		      'elements' => array(
		          array(
		              'Text',
		              'number',
		               array(
		                'label' =>  'Number of Campaigns to display',
		                'value' => '8',
		                'required' => true,
		                'validators' => array(
		                    array('Between',true,array(1,100)),
		                ),
		               ),
		           ),
		       ),
	       ),
    	),
    	array(
	        'title' => "Idea Box's Campaigns",
	        'description' => "Idea Box's Campaigns.",
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-ideabox',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => "Idea Box Campaigns",
	      		'titleCount' => true,
	    	),
	    	'adminForm'=> array(
		      'elements' => array(
		          array(
		              'Text',
		              'number',
		               array(
		                'label' =>  'Number of Campaigns to display',
		                'value' => '8',
		                'required' => true,
		                'validators' => array(
		                    array('Between',true,array(1,100)),
		                ),
		               ),
		           ),
		       ),
	       ),
    	),
    	array(
	        'title' => 'Campaign Profile Photo',
	        'description' => 'Campaign Profile Photo.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-profile-photo',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => '',
	    	),
	    ),
	    array(
	        'title' => 'Campaign Profile Options',
	        'description' => 'Campaign Profile Options.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-profile-options',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => '',
	    	),
	    ),
	    array(
	        'title' => 'Campaign Profile Owner',
	        'description' => 'Campaign Profile Owner.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-profile-owner',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => 'Campaign Owner',
	    	),
	    ),
	    array(
	        'title' => 'Campaign Profile Rating',
	        'description' => 'Campaign Profile Rating.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-profile-rating',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => '',
	    	),
	    ),
	    array(
	        'title' => 'Campaign Profile Addthis',
	        'description' => 'Campaign Profile Addthis.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-profile-addthis',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => '',
	    	),
	    ),
	    array(
	        'title' => 'Campaign Profile Info',
	        'description' => 'Campaign Profile Info.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-profile-info',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => '',
	    	),
	    ),
	    array(
	        'title' => 'Campaign Profile Description',
	        'description' => 'Campaign Profile Description.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-profile-description',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => 'Description',
	    	),
	    ),
	    array(
	        'title' => 'Campaign Profile Parent Detail',
	        'description' => 'Campaign Profile Parent Detail.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-profile-parent-detail',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => 'Idea/Trophy Detail',
	    	),
	    ),
	    array(
	        'title' => 'Campaign Fundraising Goal',
	        'description' => 'Campaign Profile fundraising Goal.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-profile-fundraising-goal',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => '',
	    	),
	    ),
	    array(
	        'title' => 'Campaign Profile News',
	        'description' => 'Campaign Profile News.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-profile-news',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => 'News',
	      		'titleCount' => true,
	    	),
	    ),
	    array(
	        'title' => 'Campaign Profile About Me',
	        'description' => 'Campaign Profile About Me.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-profile-aboutme',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => 'About Me',
	    	),
	    ),
	    array(
	        'title' => 'Menu Statistics Chart List',
	        'description' => 'Menu Statistics Chart List.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.menu-statistics-chartlist',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => '',
	    	),
	    ),
	    array(
	        'title' => 'Campaign Profile Donors',
	        'description' => 'Campaign Profile Donors.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-profile-donors',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => 'Thank You, Donors',
	    	),
	    	'adminForm'=> array(
		      'elements' => array(
		          array(
		              'Text',
		              'number',
		               array(
		                'label' =>  'Number of Donors to display',
		                'value' => '5',
		                'required' => true,
		                'validators' => array(
		                    array('Between',true,array(1,100)),
		                ),
		               ),
		           ),
		       ),
	       ),
	    ),
	    array(
	        'title' => 'Campaign Profile Supporters',
	        'description' => 'Campaign Profile Supporters.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.campaigns-profile-supporters',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => 'Supporters',
	    	),
	    	'adminForm'=> array(
		      'elements' => array(
		          array(
		              'Text',
		              'number',
		               array(
		                'label' =>  'Number of Supporters to display',
		                'value' => '9',
		                'required' => true,
		                'validators' => array(
		                    array('Between',true,array(1,100)),
		                ),
		               ),
		           ),
		       ),
	       ),
	    ),
	    array(
	        'title' => 'Profile Campaigns',
	        'description' => 'profile campaigns.',
	        'category' => 'Fundraising',
	        'type' => 'widget',
	        'name' => 'ynfundraising.profile-campaigns',
	        'isPaginated' => false,
	        'defaultParams' => array(
	      		'title' => 'Campaigns',
	      		'titleCount' => true,
	    	),
	    	'adminForm'=> array(
		      'elements' => array(
		          array(
		              'Text',
		              'number',
		               array(
		                'label' =>  'Number of campaigns to display',
		                'value' => '8',
		                'required' => true,
		                'validators' => array(
		                    array('Between',true,array(1,100)),
		                ),
		               ),
		           ),
		       ),
	       ),
    ),
);
?>