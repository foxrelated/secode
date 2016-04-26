<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: edit.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php $videos = $this->playlist->getVideos(); ?>

<?php echo $this->form->render($this) ?>

<div style="display:none;">
  <?php if (!empty($videos)): ?>
    <ul id="sesvideo_playlist">
      <?php foreach ($videos as $video): 
      	$videoMain = Engine_Api::_()->getItem('video', $video->file_id); 
      ?>
      <li id="song_item_<?php echo $video->playlistvideo_id ?>" class="file file-success">
        <a href="javascript:void(0)" class="video_action_remove file-remove"><?php echo $this->translate('Remove') ?></a>
        <span class="file-name">
          <?php echo $videoMain->getTitle() ?>
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
    if ($$('#sesvideo_playlist li.file').length) {
      $$('#sesvideo_playlist li.file').inject($('demo-list'));
      //$$('#demo-list li span.file-name').setStyle('cursor', 'move');
      $('demo-list').show()
    }
    
    //REMOVE/DELETE SONG FROM PLAYLIST
    $$('a.video_action_remove').addEvent('click', function(){
      var video_id  = $(this).getParent('li').id.split(/_/);
          video_id  = video_id[ video_id.length-1 ];
      
      $(this).getParent('li').destroy();
      new Request.JSON({
        url: '<?php echo $this->url(array('module'=> 'sesvideo' ,'controller'=>'playlist','action'=>'delete-playlistvideo'), 'default') ?>',
        data: {
          'format': 'json',
          'playlistvideo_id': video_id,
          'playlist_id': <?php echo $this->playlist->playlist_id ?>
        }
      }).send();
      return false;
    });
});
</script>