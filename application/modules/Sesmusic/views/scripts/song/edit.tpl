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
<script type="text/javascript">
window.addEvent('domready', function() {
  <?php if(empty($this->albumsong->photo_id)): ?>
  if($('song_mainphoto_preview-wrapper'))
  $('song_mainphoto_preview-wrapper').style.display = 'none';
  <?php endif; ?>
  <?php if(empty($this->albumsong->song_cover)): ?>
  if($('song_cover_preview-wrapper'))
  $('song_cover_preview-wrapper').style.display = 'none';
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
window.addEvent('domready', function() {
if($('category_id')) {
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

}
});


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

  var url = en4.core.baseUrl + 'sesmusic/index/subcategory/category_id/' + category_id + '/param/song';

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

  var url = en4.core.baseUrl + 'sesmusic/index/subsubcategory/category_id/' + category_id + '/param/song';

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
</script>
<div class="sesmusic_editsong_form">
  <?php echo $this->form->render(); ?>
</div>
<script type="text/javascript">
  $$('.core_main_sesmusic').getParent().addClass('active');
</script>