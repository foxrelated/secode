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
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesmusic/externals/styles/styles.css'); ?>

<?php $songs = $this->album->getSongs(); ?>
<div class='sesmusic_upload_form'>
  <?php echo $this->form->render($this) ?>
</div>
<div style="display:none;">
  <?php if (!empty($songs)): ?>
    <ul id="music_songlist">
      <?php foreach ($songs as $song): ?>
      <li id="song_item_<?php echo $song->albumsong_id ?>" class="file file-success">
        <a href="javascript:void(0)" class="song_action_remove file-remove"><?php echo $this->translate('Remove') ?></a>
        <span class="file-name">
          <?php echo $song->getTitle() ?>
        </span>
        <?php /*(<a href="javascript:void(0)" class="song_action_rename file-rename"><?php //echo $this->translate('rename') ?></a>*/ ?>
      </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
<?php $uploadoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.uploadoption', 'myComputer');
if ($uploadoption == 'both' || $uploadoption == 'soundCloud'): ?>
<a href="javascript: void(0);" onclick="return addAnotherOption();" id="addOptionLink" class="addanothersong">
  <i class="fa fa-plus sesbasic_text_light"></i>
  <?php echo $this->translate("Add another option") ?>
</a>
<script type="text/javascript">
 var soundCloudCounter = 1;
 var maxOptions = 100;
  window.addEvent('domready', function() {
    var newdiv = new Element('div',{
      'id' : 'soundContanier'
    }).inject($('options-element'), 'bottom');;

    var soundMiddle = new Element('div',{
      'id' : 'soundMiddle'
    }).inject(newdiv);
   
    var options = <?php echo Zend_Json::encode($this->options) ?>;
    var optionParent = $('options').getParent();

    var addAnotherOption = window.addAnotherOption = function (dontFocus, label) {
			var checkOption = checkAddOptions();
			if(!checkOption){
       return alert(new String('<?php echo $this->string()->escapeJavascript($this->translate("A maximum of %s options are permitted.")) ?>').replace(/%s/, maxOptions));
			 return false;
			}
      var soundId = 'songurl_' + soundCloudCounter;
      var optionElement = new Element('input', {
        'type': 'text',
        'name': 'optionsArray[]',
        'class': 'sesmusic_soundcloud',
        'value': label,
        'id': soundId,
        'onblur': 'songupload(this.id)',
        'onfocus': 'songFocusSave(this.id)',
        'events': {
          'keydown': function(event) {
            if (event.key == 'enter') {
              if (this.get('value').trim().length > 0) {
                addAnotherOption();
                return false;
              } else
                return true;
            } else
              return true;
          }
        }
      });

      if( dontFocus ) {
        optionElement.inject(soundMiddle);
      } else {
        optionElement.inject(soundMiddle).focus();
      }

      new Element('div',{
        'id': 'soundStatus_' + soundCloudCounter,
        'class': 'checkurlstatus',
      }).inject(optionElement, 'after');
      
      var loadingimg = new Element('div', {
        'id' : 'loading_image_'+soundCloudCounter,
        'class' : 'sesmusic_upload_loading',
        'styles': {'display': 'none'},
      }).inject(optionElement, 'after');

      new Element('img', {
       'src' : 'application/modules/Core/externals/images/loading.gif',
      }).inject(loadingimg);
     
      $('addOptionLink').inject(newdiv);
			$('addOptionLink').style.display = 'none';
      soundCloudCounter++;
    }

    if( $type(options) == 'array' && options.length > 0 ) {
      options.each(function(label) {
        addAnotherOption(true, label);
      });
      if( options.length == 1 ) {
        addAnotherOption(true);
      }
    } else {
      addAnotherOption(true);
    }
  });
  
  var songDefaultURL;
  function songFocusSave(id) {
    songDefaultURL = $(id).value;
  }
  
  function songupload(soundId) {
		//check for duplicate url
		var totalSongSelected = document.getElementById('soundMiddle').getElementsByTagName('input');
		for(var i = 0; i < totalSongSelected.length ; i++) 
		{
			if(totalSongSelected[i].id != soundId && document.getElementById(soundId).value != ''){
			 if(totalSongSelected[i].value == document.getElementById(soundId).value){
			 		document.getElementById(soundId).value ='';
					alert('This song url already selected.');
					return false;
			 }
			}
		}
    var id = soundId;
    var song_url = $(id).value;
    if(songDefaultURL == song_url && song_url != '')
      return false;
    if(!song_url)
      return false;
    var idsongURL = id.split('songurl_');
   document.getElementById('loading_image_'+idsongURL[1]).style.display ='';	
    (new Request.JSON({
      url: en4.core.baseUrl + 'sesmusic/index/soundcloudint',
      data: {
        format: 'json',
        'song_url': song_url,
      },
      onSuccess: function(responseJSON) {
         $('loading_image_'+idsongURL[1]).style.display = 'none';
         
         if(responseJSON.file_id) {
           $('soundStatus_' + idsongURL[1]).innerHTML = '<i class="fa fa-check" title="This url is valid"></i>';
					if(!$('remove_'+idsongURL[1])){
            var destroyer = new Element('a', {
              'id' : 'remove_' + idsongURL[1],
              'class': 'removesong',
              'href' : 'javascript:void(0);',
              'html' : en4.core.language.translate('<i class="fa fa-trash" title="Remove This Song"></i>'),
              'events' : {
                'click' : function() {
                  soundDelete(responseJSON.file_id, idsongURL[1]);
                }
              }
            }).inject($('soundStatus_' + idsongURL[1]), 'after');
					}
           $('soundcloudIds').value = $('soundcloudIds').value + responseJSON.file_id + ' ';

					var checkOption = checkAddOptions();
					if(checkOption){
					 $('addOptionLink').style.display = 'block';
					}
         } else {
           $('soundStatus_' + idsongURL[1]).innerHTML = '<i class="fa fa-times" title="This url is invalid"></i>';
         }
				 return false;
      }
			
    })).send();
  	return false;
  }

	function checkAddOptions(){
		var totalSongSelected = document.getElementById('soundMiddle').getElementsByTagName('input');
		if(totalSongSelected.length > 0){
			var totalInputFields = totalSongSelected.length;
			if (totalInputFields >= maxOptions) {
					return false;
			}else{
					return true;
			}
			}else
					return true;
	}
  $('addOptionLink').style.display = 'none';
  function soundDelete(file_id, id) {
  
    if(!file_id)
      return;
    
    if(!id)
      return;
        
    soundcloudIds = $('soundcloudIds').value;
    $('soundcloudIds').value = soundcloudIds.replace(file_id, "");
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'sesmusic/index/soundcloud-song-delete',
      data: {
        format: 'json',
        'file_id': file_id,
      },
      onSuccess: function(responseJSON) {
        $('songurl_' + id).destroy();
        $('soundStatus_' + id).destroy();
        $('remove_' + id).destroy();
      var checkOption = checkAddOptions();  
			if(checkOption)
				$('addOptionLink').style.display = 'block';
      }
    }));   
  }
</script>
<?php endif; ?>
<script type="text/javascript">  

if($('category_id')) {
window.addEvent('domready', function() {

  if ($('category_id').value == 0) {
    $('subcat_id-wrapper').style.display = "none";
    $('subsubcat_id-wrapper').style.display = "none";
  }

  var cat_id = $('category_id').value; 
  if ($('subcat_id')) {
    var subcat = $('subcat_id').value;
  }

  if(subcat == '') {
    $('subcat_id-wrapper').style.display = "none";
  }

  if (subcat == 0) {
    $('subsubcat_id-wrapper').style.display = "none";
  }

  if ($('subsubcat_id')) {
    var subsubcat = $('subsubcat_id').value;
  }

  if ($('module_type'))
    var module_type = $('module_type').value;

  if (cat_id && module_type && !subcat) {
    var temp = window.setInterval(function() {
      ses_subcategory(cat_id, module_type);
      clearInterval(temp);
    }, 2000);
  }
  
  //Check Search Form Only
  var search =  0;
  if($('search_params')) {
    search =  1;
  }

  var e = document.getElementById("subcat_id").length; 
  if (e == 1 && search != 1) {
    $('subcat_id-wrapper').style.display = "none";
  }

  var e = document.getElementById("subsubcat_id").length;
  if (e == 1 && search != 1) {
    $('subsubcat_id-wrapper').style.display = "none";
  }

});
}

//Function for get sub category
function ses_subcategory(category_id, module) {

  temp = 1;
  if (category_id == 0) {
    if ($('subcat_id-wrapper')) {
      $('subcat_id-wrapper').style.display = "none";
      $('subcat_id').innerHTML = '';
    }

    if ($('subsubcat_id-wrapper')) {
      $('subsubcat_id-wrapper').style.display = "none";
      $('subsubcat_id').innerHTML = '';
    }
    return false;
  }

  var url = en4.core.baseUrl + 'sesmusic/index/subcategory/category_id/' + category_id;

  en4.core.request.send(new Request.HTML({
    url: url,
    data: {
    },
    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {

      if ($('subcat_id') && responseHTML) {
        if ($('subcat_id-wrapper')) {
          $('subcat_id-wrapper').style.display = "block";
        }

        $('subcat_id').innerHTML = responseHTML;
      } else {

        if ($('subcat_id-wrapper')) {
          $('subcat_id-wrapper').style.display = "none";
          $('subcat_id').innerHTML = '';
        }

        if ($('subsubcat_id-wrapper')) {
          $('subsubcat_id-wrapper').style.display = "none";
          $('subsubcat_id').innerHTML = '';
        }
      }
    }
  }));
}

//Function for get sub sub category
function sessubsubcat_category(category_id, module) {

  if (category_id == 0) {
    if ($('subsubcat_id-wrapper')) {
      $('subsubcat_id-wrapper').style.display = "none";
      $('subsubcat_id').innerHTML = '';
    }
    return false;
  }

  var url = en4.core.baseUrl + 'sesmusic/index/subsubcategory/category_id/' + category_id;

  en4.core.request.send(new Request.HTML({
    url: url,
    data: {
    },
    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if ($('subsubcat_id') && responseHTML) {
        if ($('subsubcat_id-wrapper'))
          $('subsubcat_id-wrapper').style.display = "block";
        $('subsubcat_id').innerHTML = responseHTML;
      } else {
        if ($('subsubcat_id-wrapper')) {
          $('subsubcat_id-wrapper').style.display = "none";
          $('subsubcat_id').innerHTML = '';
        }
      }
    }
  }));

}
  

  //<![CDATA[
  en4.core.runonce.add(function(){

    //$('save-wrapper').inject($('art-wrapper'), 'after');

    // IMPORT SONGS INTO FORM
    if ($$('#music_songlist li.file').length) {
      $$('#music_songlist li.file').inject($('demo-list'));
      $$('#demo-list li span.file-name').setStyle('cursor', 'move');
      $('demo-list').show()
    }
    
    // SORTABLE PLAYLIST
    new Sortables('demo-list', {
      contrain: false,
      clone: true,
      handle: 'span',
      opacity: 0.5,
      revert: true,
      onComplete: function(){
        new Request.JSON({
          url: '<?php echo $this->url(array('controller'=>'album','action'=>'sort'), 'sesmusic_extended') ?>',
          noCache: true,
          data: {
            'format': 'json',
            'album_id': <?php echo $this->album->album_id ?>,
            'order': this.serialize().toString()
          }
        }).send();
      }
    });
    
    // RENAME SONG
    $$('a.song_action_rename').addEvent('click', function(){
      var origTitle = $(this).getParent('li').getElement('.file-name').get('text')
          origTitle = origTitle.substring(0, origTitle.length-6);
      var newTitle  = prompt('<?php echo $this->translate('What is the title of this song?') ?>', origTitle);
      var song_id   = $(this).getParent('li').id.split(/_/);
          song_id   = song_id[ song_id.length-1 ];

      if (newTitle && newTitle.length > 0) {
        newTitle = newTitle.substring(0, 60);
        $(this).getParent('li').getElement('.file-name').set('text', newTitle);
        new Request({
          url: '<?php echo $this->url(array('controller'=>'song','action'=>'rename'), 'sesmusic_extended') ?>',
          data: {
            'format': 'json',
            'albumsong_id': song_id,
            'album_id': <?php echo $this->album->album_id ?>,
            'title': newTitle
          }
        }).send();
      }
      return false;
    });

    //REMOVE/DELETE SONG FROM PLAYLIST
    $$('a.song_action_remove').addEvent('click', function(){
      var song_id  = $(this).getParent('li').id.split(/_/);
          song_id  = song_id[ song_id.length-1 ];
      
      $(this).getParent('li').destroy();
      new Request.JSON({
        url: '<?php echo $this->url(array('controller'=>'song','action'=>'delete'), 'sesmusic_extended') ?>',
        data: {
          'format': 'json',
          'albumsong_id': song_id,
          'album_id': <?php echo $this->album->album_id ?>
        }
      }).send();

      return false;
    });

});
//]]>
</script>

<script type="text/javascript">
window.addEvent('domready', function() {
  <?php if(empty($this->album->photo_id)): ?>
    if($('musicalbum_main_preview-wrapper'))
    $('musicalbum_main_preview-wrapper').style.display = 'none';
  <?php endif; ?>
  <?php if(empty($this->album->album_cover)): ?>
    if($('musicalbum_cover_preview-wrapper'))
    $('musicalbum_cover_preview-wrapper').style.display = 'none';
  <?php endif; ?>
});
//Show choose image 
function showReadImage(input,id) {
  var url = input.value; 
  var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
  if (input.files && input.files[0] && (ext == "png" || ext == "jpeg" || ext == "jpg" || ext == 'PNG' || ext == 'JPEG' || ext == 'JPG')){
    var reader = new FileReader();
    reader.onload = function (e) {
      $(id+'-wrapper').style.display = 'block';
      $(id).setAttribute('src', e.target.result);
    }
    $(id+'-wrapper').style.display = 'block';
    reader.readAsDataURL(input.files[0]);
  }
}
  $$('.core_main_sesmusic').getParent().addClass('active');
</script>