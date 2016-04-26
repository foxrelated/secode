<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: edit.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $songs = $this->playlist->getSongs(); ?>

<?php echo $this->form->render($this) ?>

<div style="display:none;">
  <?php if (!empty($songs)): ?>
    <ul id="music_songlist">
      <?php foreach ($songs as $song): ?>
      <li id="song_item_<?php echo $song->playlistsong_id ?>" class="file file-success">
        <a href="javascript:void(0)" class="song_action_remove file-remove"><?php echo $this->translate('Remove') ?></a>
        <span class="file-name">
          <?php echo $song->getTitle() ?>
        </span>
      </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function(){
    
    $('demo-status').style.display = 'none';

    //IMPORT SONGS INTO FORM
    if ($$('#music_songlist li.file').length) {
      $$('#music_songlist li.file').inject($('demo-list'));
      //$$('#demo-list li span.file-name').setStyle('cursor', 'move');
      $('demo-list').show()
    }
    
    //REMOVE/DELETE SONG FROM PLAYLIST
    $$('a.song_action_remove').addEvent('click', function(){
      var song_id  = $(this).getParent('li').id.split(/_/);
          song_id  = song_id[ song_id.length-1 ];
      
      $(this).getParent('li').destroy();
      new Request.JSON({
        url: '<?php echo $this->url(array('module'=> 'sesmusic' ,'controller'=>'playlist','action'=>'delete-playlistsong'), 'default') ?>',
        data: {
          'format': 'json',
          'playlistsong_id': song_id,
          'playlist_id': <?php echo $this->playlist->playlist_id ?>
        }
      }).send();
      return false;
    });
});
</script>
<script type="text/javascript">
  $$('.core_main_sesmusic').getParent().addClass('active');
</script>