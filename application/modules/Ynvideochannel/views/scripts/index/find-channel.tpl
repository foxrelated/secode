<?php echo $this->partial('_addChannnel.tpl', 'ynvideochannel', array('url' => '', 'keyword' => $this -> keyword))?>
<div>
    <h3><?php echo $this -> keyword?></h3>
</div>
<ul>
    <?php foreach($this -> aChannels as $channel):?>
    <li>
        <img src="<?php echo $channel['video_image']?>" width="200">
        <div><?php echo $channel['title']?></div>
    </li>
    <?php endforeach;?>
</ul>