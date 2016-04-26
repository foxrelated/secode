<?php
return array(

    array(
        'title' => 'Advanced Container',
        'name' => 'ynidea.container-advanced',
        'category' => 'Ideas',
        'type' => 'widget',
        'version' => '4.01',
        'canHaveChildren' => true,
        'isPaginated' => false,
        'childAreaDescription' => 'Any other blocks you drop inside it will become in a row.',
        'adminForm' => array('elements' => array(
                array(
                    'Text',
                    'title',
                    array('label' => 'Title', )
                ),
                array(
                    'Text',
                    'separate_width',
                    array('label' => 'Sepecify width of each block from left to right, separate by ";". example: 100px;200px', )
                ),
                array(
                    'Text',
                    'padding_width',
                    array(
                        'label' => 'Padding each widget',
                        'value' => '10px',
                    )
                ),
            )),
    ),
    array(
        'title' => 'Browse Menu',
        'description' => 'Browse menu.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.browse-menu',
        'isPaginated' => false,
    ),
    array(
        'title' => 'Ideas Search Box',
        'description' => 'Search Ideas.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.ideas-searchbox',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'Search Box',
    	),
    ),
    array(
        'title' => 'Trophy Search Box',
        'description' => 'Search Ideas.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.trophy-searchbox',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'Search Box',
    	),
    ),
    array(
        'title' => 'Tags Cloud',
        'description' => 'Tags Cloud.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.ideas-tagscloud',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'Tags Cloud',
    	),
    ),
    array(
        'title' => 'Statistics',
        'description' => 'Statistics.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.ideas-statistics',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'Statistics',
    	),
    ),
    array(
        'title' => 'Cycle: Latest Trophies',
        'description' => 'Cycle latest & current trophies.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.cycle-trophies',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'Latest Trophies',
    	),
        'adminForm'=> array(
	      'elements' => array(
	          array(
	              'Text',
	              'number',
	               array(
	                'label' =>  'Number of trophies to display',
	                'value' => '3',
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
        'title' => 'Cycle: Featured Ideas',
        'description' => 'Featured Ideas.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.cycle-ideas',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'Featured Ideas',
    	),
        'adminForm'=> array(
	      'elements' => array(
	          array(
	              'Text',
	              'number',
	               array(
	                'label' =>  'Number of ideas to display',
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
        'title' => 'Newest Ideas',
        'description' => 'Latest Ideas.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.ideas-latest',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'Newest',
    	),
    	'adminForm'=> array(
	      'elements' => array(
	          array(
	              'Text',
	              'number',
	               array(
	                'label' =>  'Number of ideas to display',
	                'value' => '15',
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
        'title' => 'Updated Ideas',
        'description' => 'Updated Ideas.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.ideas-updated',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'Updated',
    	),
    	'adminForm'=> array(
	      'elements' => array(
	          array(
	              'Text',
	              'number',
	               array(
	                'label' =>  'Number of ideas to display',
	                'value' => '15',
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
        'title' => 'Selected & Realized Ideas',
        'description' => 'Selected Ideas.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.ideas-selected',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'Selected & Realized',
    	),
    	'adminForm'=> array(
	      'elements' => array(
	          array(
	              'Text',
	              'number',
	               array(
	                'label' =>  'Number of ideas to display',
	                'value' => '15',
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
        'title' => 'Top Score Ideas',
        'description' => 'Top Score Ideas.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.ideas-topscore',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'Top Score',
    	),
    	'adminForm'=> array(
	      'elements' => array(
	          array(
	              'Text',
	              'number',
	               array(
	                'label' =>  'Number of ideas to display',
	                'value' => '15',
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
        'title' => 'Top Average Ideas',
        'description' => 'Top Average Ideas.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.ideas-topaverage',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'Top Average',
    	),
    	'adminForm'=> array(
	      'elements' => array(
	          array(
	              'Text',
	              'number',
	               array(
	                'label' =>  'Number of ideas to display',
	                'value' => '15',
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
        'title' => 'Ideas with Awards',
        'description' => 'Awards Ideas.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.ideas-withawards',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'With Awards',
    	),
    	'adminForm'=> array(
	      'elements' => array(
	          array(
	              'Text',
	              'number',
	               array(
	                'label' =>  'Number of ideas to display',
	                'value' => '15',
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
        'title' => 'My Trophies',
        'description' => 'My Trophies.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.my-Trophies',
        'isPaginated' => false,
    ),
    array(
        'title' => 'My Ideas',
        'description' => 'My Ideas.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.my-ideas',
        'isPaginated' => false,
    ),
    array(
        'title' => 'List Trophies',
        'description' => 'List Trophies.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.list-trophies',
        'isPaginated' => false,
    ),
    array(
        'title' => 'View All Ideas',
        'description' => 'View All Ideas.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.view-all-ideas',
        'isPaginated' => false,
    ),
    array(
        'title' => 'Idea Profile Authors',
        'description' => 'Idea profile authors.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.ideas-profile-authors',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'Authors',
    	),
    ),
    array(
        'title' => 'Idea Profile Awards',
        'description' => 'Idea profile awards.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.ideas-profile-awards',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'Awards',
    	),
    ),
    array(
        'title' => 'Idea Profile Description',
        'description' => 'Idea profile description.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.ideas-profile-description',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'Description',
    	),
    ),
    array(
        'title' => 'Idea Profile Info',
        'description' => 'Idea profile info.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.ideas-profile-info',
        'isPaginated' => false,
    ),
    array(
        'title' => 'Idea Profile Options',
        'description' => 'Idea profile options.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.ideas-profile-options',
        'isPaginated' => false,
    ),
    array(
        'title' => 'Idea Profile Photo',
        'description' => 'Idea profile photo.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.ideas-profile-photo',
        'isPaginated' => false,
    ),
    array(
        'title' => 'Idea Profile Voting Box',
        'description' => 'Idea profile voting box.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.ideas-profile-voting-box',
        'isPaginated' => false,
    ),
    array(
        'title' => 'Trophy Profile Judges',
        'description' => 'Trophy profile judges.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.trophy-profile-judges',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'Judges',
    	),
    ),
    array(
        'title' => 'Trophy Profile Awards',
        'description' => 'Trophy profile awards.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.trophy-profile-awards',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'Awards',
    	),
    ),
    array(
        'title' => 'Trophy Profile Nominees',
        'description' => 'Trophy profile nominees.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.trophy-profile-nominees',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'Nominees',
    	),
    ),
    array(
        'title' => 'Trophy Profile Info',
        'description' => 'Trophy profile info.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.trophy-profile-info',
        'isPaginated' => false,
    ),
    array(
        'title' => 'Trophy Profile Options',
        'description' => 'Trophy profile options.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.trophy-profile-options',
        'isPaginated' => false,
    ),
    array(
        'title' => 'Trophy Profile Photo',
        'description' => 'Idea profile photo.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.trophy-profile-photo',
        'isPaginated' => false,
    ),
    array(
        'title' => 'Profile Ideas',
        'description' => 'profile ideas.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.profile-ideas',
        'isPaginated' => false,
        'defaultParams' => array(
      		'title' => 'Ideas',
      		'titleCount' => true,
    	),
    ),
    
	array(
        'title' => 'List Categories',
        'description' => 'Displays a list of categories.',
        'category' => 'Ideas',
        'type' => 'widget',
        'name' => 'ynidea.list-categories',
        'defaultParams' => array(
            'title' => 'Categories',
        ),
    ),
);
?>