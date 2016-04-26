<?php $item = $this->item;?>
<div class="ynevent-feed-item">  
    <div class="event-date-photo">
        <div class="event-date">                    
            <strong><?php 
            $start_time = strtotime($item -> starttime);
			$oldTz = date_default_timezone_get();
			if($this->viewer() && $this->viewer()->getIdentity())
			{
				date_default_timezone_set($this -> viewer() -> timezone);
			}
			else 
			{
				date_default_timezone_set( $this->locale() -> getTimezone());
			}
            echo date("d", $start_time); ?></strong>
            <?php echo date("M", $start_time);
            date_default_timezone_set($oldTz);?>
        </div>
        <div class="event-photo" style="width: calc(100% - 110px)">
        <?php echo $this->htmlLink($item->getHref(), '<span class="image-thumb" style="background-image: url('.$item->getPhotoUrl().');"></span>', array('class' => 'thumb')) ?>
    	</div>
    </div>
  	<div class="event-info">
	    <div class="event-title">
	      <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
	    </div>
	   
	    <div class="events-members">
	    <?php
	        echo $this->translate('%1$s %2$s',
	        $this->locale()->toDate($startDateObject,  array('size' => 'long')),
	        $this->locale()->toTime($startDateObject)
	      	) ?>
	        <?php 
	        if($item->host)
	        {
	        	if(strpos($item->host,'younetco_event_key_') !== FALSE)
				{
				  	$user_id = substr($item->host, 19, strlen($item->host));
					$user = Engine_Api::_() -> getItem('user', $user_id);
					
					echo $this->translate('host by %1$s',
	              	$this->htmlLink($user->getHref(), $user->getTitle())) ;
				}
				else{
					echo $this->translate('Host by %1$s', $item->host);
				}
			}
			else{
				echo $this->translate('by %1$s',
	              	$this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())) ;
			}
	        ?>
	    </div>
	    <?php if($item->address):?>
	    <div class="event-location">
	       <?php echo $this -> string() -> truncate($item->address, 50);?>
	    </div>
	    <?php endif;?>
	    <div class="event-description">
	        <?php echo $this->string()->truncate(strip_tags($item->description), 100); ?>
	    </div>
  	</div>
</div>