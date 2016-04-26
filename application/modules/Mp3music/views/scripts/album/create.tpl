
<div class='global_form'>
<?php if($this->upload_message == 0): ?>
<form action="" class="global_form_box" method="POST" id="upload_form">
<div style="color: red"><?php echo $this->translate('Please read the terms of use and agree to check the box below.'); ?></div>
<div style="margin-bottom: 5px;">
<textarea cols="100" rows="10" readonly="readonly">
<?php  $settings = Mp3music_Api_Cart::getSettingsSelling(0);   
        if($settings['upload_message'] != ""):
            echo $settings['upload_message'];
        else:
            echo $this->translate('Only post songs in which they have the right to sell, etc.');
         endif;     ?>
</textarea>
</div>
         <input type="checkbox" id="upload_message" name="upload_message">
         <label style="margin-bottom: 10px"><?php echo $this->translate('I have read and fully agree with the terms.'); ?></label>
         <br/>
         <button type="submit" name="submit"> <?php echo $this->translate('Continue'); ?></button>
</form>         
<?php else: ?>
<?php    $user = Engine_Api::_()->user()->getViewer();
          $max_albums =  Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('mp3music_album', $user, 'max_albums');
         if($max_albums == "")
         {
            $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
             $maselect = $mtable->select()
                ->where("type = 'mp3music_album'")
                ->where("level_id = ?",$user->level_id)
                ->where("name = 'max_albums'");
              $mallow_a = $mtable->fetchRow($maselect);          
              if (!empty($mallow_a))
                $max_albums = $mallow_a['value'];
              else
                 $max_albums = 10;
         }
         $cout_album = Mp3music_Model_Album::getCountAlbums($user);
        if($cout_album < $max_albums):
             echo $this->form->render($this);
        else: ?>
           <div style="color: red; padding-left: 300px;">
                <?php echo $this->translate(array("Sorry! Maximum number of allowed albums : %s album","Sorry! Maximum number of allowed albums : %s albums",$max_albums),$max_albums); ?> 
           </div> 
        <?php endif; ?>
 <?php endif; ?>
</div>
<script type="text/javascript">
function updateTextFields() 
{
  if ($('music_singer_id').selectedIndex > 0) {
    $('other_singer-wrapper').hide();
  } else {
    $('other_singer-wrapper').show();
  }
}
function checkIt(evt) 
{
        evt = (evt) ? evt : window.event
        var charCode = (evt.which) ? evt.which : evt.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46) {
            status = "This field accepts numbers only."
            return false
        }
        status = ""
        return true
    }
function changeSelect(obj)
{
	var title = document.getElementById('title-label');
	var price = document.getElementById('price-label');
	var des = document.getElementById('description-label');
	var art = document.getElementById('art-label');
	if(obj.value == 1)
	{
		title.innerHTML = '<label class="optional" for="title">'+ '<?php echo $this->translate("Track Name")?>' +'</label>';
		price.innerHTML = '<label class="optional" for="price">'+ '<?php echo $this->translate("Track Price")?>' +'</label>';
		des.innerHTML = '<label class="optional" for="description">'+ '<?php echo $this->translate("Track Description")?>' +'</label>';
		art.innerHTML = '<label class="optional" for="art">'+ '<?php echo $this->translate("Track Artwork")?>' +'</label>';
		$('upload_album').style.display = 'none';
		$('track-wrapper').style.display = 'block';
		$('submit-wrapper').style.display = 'block';
	}
	else
	{
		title.innerHTML = '<label class="optional" for="title">'+ '<?php echo $this->translate("Album Name")?>' +'</label>';
		price.innerHTML = '<label class="optional" for="price">'+ '<?php echo $this->translate("Album Price")?>' +'</label>';
		des.innerHTML = '<label class="optional" for="description">'+ '<?php echo $this->translate("Album Description")?>' +'</label>';
		art.innerHTML = '<label class="optional" for="art">'+ '<?php echo $this->translate("Album Artwork")?>' +'</label>';
		$('track-wrapper').style.display = 'none';
		$('upload_album').style.display = 'block';
		$('submit-wrapper').style.display = 'none';
	}
}
</script>