<?php	           		
	echo $this->partial('_contest-large-list.tpl', 'yncontest', array(
		'items'     => $this->items,
		'height'     => $this->height,
		'width'     => $this->width,  
		'browseby' => 'endingsoon_contest',	
		'limit' => $this->limit	        		
	));
?>

