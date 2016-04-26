 <?php
$path = $this->url(array(),'default')."mp3music/album/service?name=getalbum&idplaylist=".$this->playlist->playlist_id."&autoplay=false";    
$path = urlencode ($path);
$flash_player = $this->baseUrl()."/application/modules/Mp3music/externals/swf/simpleplayer.swf";        
 ?>
 <?php if(!count($this->songs)):?>
<div class="tip" style="clear: none; padding-top: 10px;">
  <span>
    <?php echo $this->translate('There are no songs uploaded yet.') ?>
  </span>
</div>
<?php else:?>
 <div class="mp3music_container profile_music_player" style="margin-top: 0px;">
  <div class ="profile-player">
    <audio class="yn-audio-skin mejs" width="100%" src="<?php echo $this->songs[0]['filepath'];?>" type="audio/mp3" controls="controls" preload="none"></audio>
  </div>

  <ul class="song-list mejs-list scroll-pane">
    <?php foreach ($this->songs as $index => $arSong):?>
      <li class="<?php echo $index == 0 ? 'current': '';?>">
          <span class="song_id" style="display: none;"><?php echo $arSong['song_id'];?></span>
          <span class="link"><?php echo $arSong['filepath'];?></span>
					<div class = "mp3music-song-title">
						<span class="song-title">
							<?php if($arSong['price'] == 0):?>
									<span class="song-title"><?php echo ++$index.'. '.$this->string()->truncate($arSong['title'],20);?></span>
							<?php else:?>
									<span class="song-title"><?php echo ++$index.'. '.$this->string()->truncate($arSong['title'],12).$this->translate(" - Preview");?></span>
							<?php endif;?>
						</span>
					</div>
      </li>
    <?php endforeach;?>
  </ul>
</div>
<?php endif; ?>
