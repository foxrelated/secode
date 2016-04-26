<?php
$songs = $this->album->getSongs();
?>
<div class="headline">
  <h2>
    <?php echo $this->translate('Mp3 Music');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>
<?php echo $this->form->render($this) ?>
<div style="display:none;">
  <?php if (!empty($songs)): ?>
    <ul id="music_songlist">
      <?php foreach ($songs as $song): ?>
      <li id="song_item_<?php echo $song->song_id ?>" class="file file-success">
        <a href="javascript:void(0)" class="song_action_remove file-remove"><?php echo $this->translate('Remove') ?></a>
        <span class="file-name">
          <?php echo $this-> string() -> truncate($song->getTitle(), 30);?>
        </span>
        (<?php echo $song->price; echo $this->translate(' USD'); ?> - <a href="<?php echo 
                    $this->url(array('album_id'=>$song->album_id,'song_id'=>$song->song_id), 'mp3music_edit_song') ?>"><?php echo $this->translate('Edit') ?> </a> )
      </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
<script type="text/javascript">
function checkIt(evt) 
{
    evt = (evt) ? evt : window.event
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        status = '<?php echo $this->string()->escapeJavascript($this->translate("This field accepts numbers only."))?>'
        return false
    }
    status = ""
    return true
}
//<![CDATA[
  en4.core.runonce.add(function()
  {
  	$('upload_album').style.display = 'block';
  	<?php if($this -> album -> type == 2):?>
  		$('track-wrapper').style.display = 'none';
  	<?php else:?>
  		$('upload_desc').style.display = 'none';
  		$('button_upload').style.display = 'none';
  		$('progress').style.display = 'none';
  		<?php if(count($songs)):?>
  			$('track-wrapper').style.display = 'none';
  		<?php else:?>
  				$('upload_album').style.display = 'none';
  		<?php endif;?>
  	<?php endif;?>
    // IMPORT SONGS INTO FORM
    if ($$('#music_songlist li.file').length) {
      $$('#music_songlist li.file').inject($('files'));
      $$('#files li span.file-name').setStyle('cursor', 'move');
      $('files').show()
    }
    // SORTABLE album
    new Sortables('files', {
      contrain: false,
      clone: true,
      handle: 'span',
      opacity: 0.5,
      revert: true,
      onComplete: function(){
        new Request.JSON({
          url: '<?php echo $this->url(array('module'=>'mp3music','controller'=>'album','action'=>'album-sort'), 'default') ?>',
          noCache: true,
          data: {
            'format': 'json',
            'album_id': <?php echo $this->album->album_id ?>,
            'order': this.serialize().toString()
          }
        }).send();
      }
    });
    //$$('#music_songlist > li > span').setStyle('cursor','move');
 
    // REMOVE/DELETE SONG FROM album
    $$('a.song_action_remove').addEvent('click', function()
    {
    var flag = confirm('<?php echo $this->string()->escapeJavascript($this->translate('Are you sure you want to delete this song?')) ?>');
      if(flag == true)
      {
      var song_id  = $(this).getParent('li').id.split(/_/);
          song_id  = song_id[ song_id.length-1 ];

      
      $(this).getParent('li').destroy();
      <?php if($this -> album -> type == 1):?>
     	 	$('track-wrapper').style.display = 'block';
     	 	$('upload_album').style.display = 'none';
      <?php endif;?>
      new Request.JSON({
        url: '<?php echo $this->url(array('module'=>'mp3music','controller'=>'album','action'=>'remove-song-album'), 'default') ?>',
        data: {
          'format': 'json',
          'song_id': song_id,
          'album_id': <?php echo $this->album->album_id ?>
        }
      }).send();
      }
      return false;
    });

});
//]]>
function changeSelect(obj)
{
	var title = document.getElementById('title-label');
	var price = document.getElementById('price-label');
	var des = document.getElementById('description-label');
	var art = document.getElementById('art-label');
	if(obj.value == 1)
	{
		title.innerHTML = '<label class="optional" for="title">'+ '<?php echo $this->string()->escapeJavascript($this->translate("Track Name"))?>' +'</label>';
		price.innerHTML = '<label class="optional" for="price">'+ '<?php echo $this->string()->escapeJavascript($this->translate("Track Price"))?>' +'</label>';
		des.innerHTML = '<label class="optional" for="description">'+ '<?php echo $this->string()->escapeJavascript($this->translate("Track Description"))?>' +'</label>';
		art.innerHTML = '<label class="optional" for="art">'+ '<?php echo $this->string()->escapeJavascript($this->translate("Track Artwork"))?>' +'</label>';
		$('upload_album').style.display = 'none';
		$('track-wrapper').style.display = 'block';
		$('submit-wrapper').style.display = 'block'; 
	}
	else
	{
		title.innerHTML = '<label class="optional" for="title">'+ '<?php echo $this->string()->escapeJavascript($this->translate("Album Name"))?>' +'</label>';
		price.innerHTML = '<label class="optional" for="price">'+ '<?php echo $this->string()->escapeJavascript($this->translate("Album Price"))?>' +'</label>';
		des.innerHTML = '<label class="optional" for="description">'+ '<?php echo $this->string()->escapeJavascript($this->translate("Album Description"))?>' +'</label>';
		art.innerHTML = '<label class="optional" for="art">'+ '<?php echo $this->string()->escapeJavascript($this->translate("Album Artwork"))?>' +'</label>';
		$('track-wrapper').style.display = 'none';
		$('upload_album').style.display = 'block';
		$('submit-wrapper').style.display = 'none';
	}
}
</script>
