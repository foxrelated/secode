<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php if( $this->form ): ?>
  <div class="sesbasic_browse_search">
    <?php echo $this->form->render($this) ?>
  </div>
<?php endif ?>

<?php
$base_url = $this->layout()->staticBaseUrl;
$this->headScript()
->appendFile($base_url . 'externals/autocompleter/Observer.js')
->appendFile($base_url . 'externals/autocompleter/Autocompleter.js')
->appendFile($base_url . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($base_url . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">
  //Take refrences from "/application/modules/Blog/views/scripts/index/create.tpl"
  en4.core.runonce.add(function() {
    var searchAutocomplete = new Autocompleter.Request.JSON('title_song', "<?php echo $this->url(array('module' => 'sesmusic', 'controller' => 'index', 'action' => 'search', 'actonType' => 'browse', 'sesmusic_commonsearch' => 'sesmusic_albumsong'), 'default', true) ?>", {
      'postVar': 'text',
      'delay' : 250,      
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'customChoices': true,
      'filterSubset': true,
      'multiple': false,
      'className': 'sesbasic-autosuggest',
      'injectChoice': function(token) {
        var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id': token.label});
        new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
    contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
      //document.getElementById('album_id').value = selected.retrieve('autocompleteChoice').id;
    });
  });
</script>
<script type="text/javascript">
var title_name = document.getElementById("title_song");
title_song.addEventListener("keydown", function (e) {
    if (e.keyCode === 13) {  //checks whether the pressed key is "Enter"
        this.form.submit();
    }
});
if($('category_id')) {

  window.addEvent('domready', function() {
  
    if ($('category_id').value == 0) {
      $('subcat_id-wrapper').style.display = "none";
      $('subsubcat_id-wrapper').style.display = "none";
    }
    
    var cat_id = $('category_id').value;
    if ($('subcat_id')) 
      var subcat = $('subcat_id').value;
    
    if(subcat == '')
      $('subcat_id-wrapper').style.display = "none";
    
    if (subcat == 0)
      $('subsubcat_id-wrapper').style.display = "none";
    
    if ($('subsubcat_id'))
      var subsubcat = $('subsubcat_id').value;

    if (cat_id)
      ses_subcategory(cat_id);
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

  var request = new Request.HTML({
    url: en4.core.baseUrl + 'sesmusic/index/subcategory/category_id/' + category_id,
    data: {
    },
    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {

      if ($('subcat_id') && responseHTML) {
        if ($('subcat_id-wrapper')) {
          $('subcat_id-wrapper').style.display = "block";
        }

        $('subcat_id').innerHTML = responseHTML;
        <?php if(isset($_GET['subcat_id']) && $_GET['subcat_id']): ?>
        $('subcat_id').value = '<?php echo $_GET["subcat_id"] ?>';
        sessubsubcat_category('<?php echo $_GET["subcat_id"] ?>');
        <?php endif; ?>
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
  }).send();
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

  var request = new Request.HTML({
    url: en4.core.baseUrl + 'sesmusic/index/subsubcategory/category_id/' + category_id,
    data: {
    },
    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if ($('subsubcat_id') && responseHTML) {
        if ($('subsubcat_id-wrapper'))
          $('subsubcat_id-wrapper').style.display = "block";
        $('subsubcat_id').innerHTML = responseHTML;
        <?php if(isset($_GET['subsubcat_id']) && $_GET['subsubcat_id']): ?>
          $('subsubcat_id').value = '<?php echo $_GET["subsubcat_id"] ?>';
        <?php endif; ?>
      } else {
        if ($('subsubcat_id-wrapper')) {
          $('subsubcat_id-wrapper').style.display = "none";
          $('subsubcat_id').innerHTML = '';
        }
      }
    }
  }).send();

} 
</script>
