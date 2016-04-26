<?php
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');   	
	  
?>
<?php    
    echo $this->partial('application/modules/Mp3music/views/scripts/music_browse.tpl','mp3music',array('module'=>'mp3music','browse'=>$this))?>