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

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesvideo/externals/styles/styles.css'); ?>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/customscrollbar.css'); ?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js'); ?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/customscrollbar.concat.min.js'); ?>

<?php

  if (APPLICATION_ENV == 'production')

    $this->headScript()

      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.min.js');

  else

    $this->headScript()

      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')

      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')

      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')

      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');

?>

<script type="text/javascript">

  en4.core.runonce.add(function() {

    var tagsUrl = '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>';

    var validationUrl = '<?php echo $this->url(array('module' => 'sesvideo', 'controller' => 'index', 'action' => 'validation'), 'default', true) ?>';

    var validationErrorMessage = "<?php echo $this->translate("We could not find a video there - please check the URL and try again. If you are sure that the URL is valid, please click %s to continue.", "<a href='javascript:void(0);' onclick='javascript:ignoreValidation();'>".$this->translate("here")."</a>"); ?>";

    var checkingUrlMessage = '<?php echo $this->string()->escapeJavascript($this->translate('Checking URL...')) ?>';

    var current_code;

    var ignoreValidation = window.ignoreValidation = function() {

      $('upload-wrapper').style.display = "block";

      $('validation').style.display = "none";

      $('code').value = current_code;

      $('ignore').value = true;

    }

    var autocompleter = new Autocompleter.Request.JSON('tags', tagsUrl, {

      'postVar' : 'text',

      'minLength': 1,

      'selectMode': 'pick',

      'autocompleteType': 'tag',

      'className': 'tag-autosuggest',

      'customChoices' : true,

      'filterSubset' : true,

      'multiple' : true,

      'injectChoice': function(token){

        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});

        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);

        choice.inputValue = token;

        this.addChoiceEvents(choice).inject(this.choices);

        choice.store('autocompleteChoice', token);

      }

    });

  });

</script>

<div class="sesvideo_channel_form sesvideo_channel_edit_form">

	<?php echo $this->form->render($this);?>

</div>

<script type="application/javascript">

 sesJqueryObject('#chanel_create_form_tabs li a').click(function(e){
	 if(sesJqueryObject(this).parent().hasClass('sesvideo_create_channel_tabs_btns'))
	 	return;
	 e.preventDefault();

		var liLength = sesJqueryObject('#chanel_create_form_tabs li');

		for(i=0;i<liLength.length;i++)

			liLength[i].removeClass('active');

		if(onLoad == 'loadedElem'){

			var validationFm = validateForm();

			if(validationFm)

			{

				alert('<?php echo $this->translate("Please fill the red mark fields"); ?>');

				if(typeof objectError != 'undefined'){

				 var errorFirstObject = sesJqueryObject(objectError).parent().parent();

				 sesJqueryObject('html, body').animate({

					scrollTop: errorFirstObject.offset().top

				 }, 2000);

				}

				return false;	

			}

		}

		onLoad = 'loadedElem';

		var className = sesJqueryObject(this).parent().attr('data-url');

		sesJqueryObject('#first_step-wrapper').hide();

		sesJqueryObject('#last_elem-wrapper').hide();

		sesJqueryObject('#first_second-wrapper').hide();

		sesJqueryObject('#first_third-wrapper').hide();

		sesJqueryObject('#'+className+'-wrapper').show();

		sesJqueryObject(this).parent().addClass('active');

 });

 jqueryObjectOfSes("#title").keyup(function(){

		var Text = jqueryObjectOfSes(this).val();

		Text = Text.toLowerCase();

		Text = Text.replace(/[^a-zA-Z0-9]+/g,'-');

		jqueryObjectOfSes("#custom_url").val(Text);        

		jqueryObjectOfSes("#channelurl").html(Text);

});

if(sesJqueryObject('#cover_photo_preview').attr('src')){

 sesJqueryObject('#cover_photo_preview-wrapper').show();

}else

	sesJqueryObject('#cover_photo_preview-wrapper').hide();

if(sesJqueryObject('#thumbnail_photo_preview').attr('src')){

 sesJqueryObject('#thumbnail_photo_preview-wrapper').show();

}else

	sesJqueryObject('#thumbnail_photo_preview-wrapper').hide();

 var onLoad = 'firstLoad';

 sesJqueryObject('#chanel_create_form_tabs').children().eq(0).find('a').click();

 sesJqueryObject(document).on('click','.next_elm',function(){

		var id = sesJqueryObject(this).attr('id');

		sesJqueryObject('#'+id+'-click').trigger('click');

 });





 function readImageUrl(input,id) {

    var url = input.value;

    var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();

		if(id == 'cover_photo_preview')

		 var idMsg = 'chanel_cover';

		else

			var idMsg = 'chanel_thumbnail';

    if (input.files && input.files[0] && (ext == "png" || ext == "jpeg" || ext == "jpg")) {

        var reader = new FileReader();

        reader.onload = function (e) {

					 sesJqueryObject('#'+id+'-wrapper').show();

           sesJqueryObject('#'+id).attr('src', e.target.result);

        }

				sesJqueryObject('#'+id+'-wrapper').show();

				sesJqueryObject('#'+idMsg+'-msg').hide();

        reader.readAsDataURL(input.files[0]);

    }else{

				 sesJqueryObject('#'+id+'-wrapper').hide();

				 sesJqueryObject('#'+idMsg+'-msg').show();

				 sesJqueryObject('#'+idMsg+'-msg').html("<br><?php echo $this->translate('Please select png,jpeg,jpg image only.'); ?>");

         sesJqueryObject('#'+idMsg).val('');

		}

  }

	sesJqueryObject('#custom_url').keyup(function(){

		sesJqueryObject('#channelurl').html(sesJqueryObject('#custom_url').val());	

	});

	window.addEvent('domready', function() {

		sesJqueryObject('#channelurl').html(sesJqueryObject('#custom_url').val());

	});

	sesJqueryObject('<span id="chanel_cover-msg" class="sesvideo_error" style="display:none"></span>').insertAfter('#chanel_cover');

	sesJqueryObject('<span id="chanel_thumbnail-msg" class="sesvideo_error" style="display:none"></span>').insertAfter('#chanel_thumbnail');

</script>

<script type="text/javascript">

//Ajax error show before form submit

var error = false;

var objectError ;

var counter = 0;

function validateForm(){

		var errorPresent = false;

		sesJqueryObject('#form-upload input, #form-upload select,#form-upload checkbox,#form-upload textarea,#form-upload radio').each(

				function(index){

						var input = sesJqueryObject(this);

						if(sesJqueryObject(this).closest('div').parent().css('display') != 'none' && sesJqueryObject(this).closest('div').parent().find('.form-label').find('label').first().hasClass('required') && sesJqueryObject(this).prop('type') != 'hidden' && sesJqueryObject(this).closest('div').parent().attr('class') != 'form-elements'){	

						  if(sesJqueryObject(this).prop('type') == 'checkbox'){

								value = '';

								if(sesJqueryObject('input[name="'+sesJqueryObject(this).attr('name')+'"]:checked').length > 0) { 

										value = 1;

								};

								if(value == '')

									error = true;

								else

									error = false;

							}else if(sesJqueryObject(this).prop('type') == 'select-multiple'){

								if(sesJqueryObject(this).val() === '' || sesJqueryObject(this).val() == null)

									error = true;

								else

									error = false;

							}else if(sesJqueryObject(this).prop('type') == 'select-one' || sesJqueryObject(this).prop('type') == 'select' ){

								if(sesJqueryObject(this).val() === '')

									error = true;

								else

									error = false;

							}else if(sesJqueryObject(this).prop('type') == 'radio'){

								if(sesJqueryObject("input[name='"+sesJqueryObject(this).attr('name').replace('[]','')+"']:checked").val() === '')

									error = true;

								else

									error = false;

							}else if(sesJqueryObject(this).prop('type') == 'textarea'){

								if(sesJqueryObject(this).val() === '' || sesJqueryObject(this).val() == null)

									error = true;

								else

									error = false;

							}else{

								if(sesJqueryObject(this).val() === '' || sesJqueryObject(this).val() == null)

									error = true;

								else

									error = false;

							}

							if(error){

							 if(counter == 0){

							 	objectError = this;

							 }

								counter++

							}else{

							}

							if(error)

								errorPresent = true;

							error = false;

						}

				}

			);

				

			return errorPresent ;

}

	sesJqueryObject('#form-upload').submit(function(e){

			var customUrlCheck = sesJqueryObject('#custom_url').val();

			if(customUrlCheck != ''){

				var validation = 	sendCheckValidation(e);

				if(validation){

					var validationFm = validateForm();

					if(validationFm)

					{

						alert('<?php echo $this->translate("Please fill the red mark fields"); ?>');

						if(typeof objectError != 'undefined'){

						 var errorFirstObject = sesJqueryObject(objectError).parent().parent();

						 sesJqueryObject('html, body').animate({

							scrollTop: errorFirstObject.offset().top

						 }, 2000);

						}

						return false;	

					}else{

						//sesJqueryObject('#file_multi-wrapper').remove();

						sesJqueryObject('#upload').attr('disabled',true);

						sesJqueryObject('#upload').html('<?php echo $this->translate("Submitting Form ..."); ?>');

						return true;

					}

				}

			}else{

				return true;

			}

	});

  function showSubCategory(cat_id,selectedId) {

		var selected;

		if(selectedId != ''){

			var selected = selectedId;

		}

    var url = en4.core.baseUrl + 'sesvideo/index/subcategory/category_id/' + cat_id;

    new Request.HTML({

      url: url,

      data: {

				'selected':selected

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

    }).send(); 

  }

	function showSubSubCategory(cat_id,selectedId,isLoad) {

		if(cat_id == 0){

			if ($('subsubcat_id-wrapper')) {

				$('subsubcat_id-wrapper').style.display = "none";

				$('subsubcat_id').innerHTML = '';

      }	

			return false;

		}

		var selected;

		if(selectedId != ''){

			var selected = selectedId;

		}

    var url = en4.core.baseUrl + 'sesvideo/index/subsubcategory/subcategory_id/' + cat_id;

    (new Request.HTML({

      url: url,

      data: {

				'selected':selected

      },

      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {

        if ($('subsubcat_id') && responseHTML) {

          if ($('subsubcat_id-wrapper')) {

            $('subsubcat_id-wrapper').style.display = "block";

          }

          $('subsubcat_id').innerHTML = responseHTML;

					// get category id value 

				

        } else {

          if ($('subsubcat_id-wrapper')) {

            $('subsubcat_id-wrapper').style.display = "none";

            $('subsubcat_id').innerHTML = '';

          }

        }

      }

    })).send();  

  }

 sesJqueryObject(document).ready(function(){
			<?php if(isset($_GET['tab']) && $_GET['tab'] == 'add_videos'){ ?>
					sesJqueryObject('#save_third-click').trigger('click');
			<?php } ?>
			sesJqueryObject('#remove_chanel_cover-label').hide();

			sesJqueryObject('#remove_chanel_thumbnail-label').hide();

			sesJqueryObject('#delete-label').hide();

			sesJqueryObject('#upload-label').hide();

			sesJqueryObject('<span id="chanel_cover-msg" class="sesvideo_error" style="display:none"></span>').insertAfter('#chanel_cover');

			sesJqueryObject('<span id="chanel_thumbnail-msg" class="sesvideo_error" style="display:none"></span>').insertAfter('#chanel_thumbnail');

 })

 var checkURL = true;

	function sendCheckValidation(e){

		var valueField = sesJqueryObject('#custom_url').val();

		if(!valueField)

			return;

		var url = en4.core.baseUrl + 'sesvideo/chanel/checkurl';

    new Request.HTML({

				url: url,

				data: {

					'data':valueField,

					'chanel_id':"<?php echo $this->chanel->chanel_id ; ?>",

				},

				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {

					if(responseHTML == 1){

						sesJqueryObject('#custom_url').css('border','');

						sesJqueryObject('.msg').css('color','');

						sesJqueryObject('.msg').html('');

						checkURL = true;

						return true;

					}else{

						sesJqueryObject('#save-first-click').trigger('click');

						sesJqueryObject('#custom_url').css('border','1px solid red');

						sesJqueryObject('.msg').html('<i class="fa fa-close" title="Unavilable"></i>');

						sesJqueryObject('html, body').animate({

							scrollTop: sesJqueryObject('#shortURL-wrapper').position().top },

							1000

						 );

						checkURL = false;

						if(typeof e != 'undefined'){

							if(!checkValidation()){

								e.preventDefault();

								return false;	

							}	

						}

					} 

				}

    }).send();	

	}

	sesJqueryObject('#custom_url').blur(function(){

		sendCheckValidation();

	});

	sesJqueryObject("#custom_url").keypress(function(){

   checkURL = false;

	})

	function checkValidation(){

		if(!checkURL && $('shortURL-wrapper')){

			sesJqueryObject('#save-first-click').trigger('click');

			sesJqueryObject('html, body').animate({

				scrollTop: sesJqueryObject('#shortURL-wrapper').position().top },

				1000

			 );

			 document.getElementById('custom_url').focus();

			return false;

		}else{

		 var ids = '';

			$$('#added_manage_videos > li').each(function(el) {

				ids = ids+(el.get('id').match(/\d+/)[0])+',';

			});

			sesJqueryObject('#video_ids').val(ids);

			return true;

		}

	}	

	if($('shortURL-label'))

		$('shortURL-label').innerHTML = "<?php echo $this->translate('Shortcut URL'); ?>";

	//get videos for chanel as per first select option in manage videos.

	getChanelVideos();

	function getChanelVideos(){

		var url = en4.core.baseUrl + 'sesvideo/chanel/manage-videos';

		new Request.HTML({

				url: url,

				data: {

					'is_chanel':true,

					'chanel_id':"<?php echo $this->chanel->chanel_id ; ?>"

				},

				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {

					sesJqueryObject('#added_manage_videos').html(responseHTML);

				var ischange = false;

				if(sesJqueryObject('#first_third-wrapper').css('display') == 'none'){

					sesJqueryObject('#first_third-wrapper').css('visibility','hidden').css('position','absolute');

					sesJqueryObject('#first_third-wrapper').css('display','block');

					ischange = true;

				}

					var totalHeight = sesJqueryObject('#added_manage_videos').height();

					if(totalHeight > 310){

							sesJqueryObject('.added_manage_videos').css('height','310px');	

						}else{

							sesJqueryObject('.added_manage_videos').css('height',totalHeight+'px');	

						}

					if(ischange){

							sesJqueryObject('#first_third-wrapper').css('visibility','').css('position','');

							sesJqueryObject('#first_third-wrapper').css('display','none');

							ischange = true;

					}

					getVideos(sesJqueryObject('#manage_videos').val());

				}

    }).send();

	}

	

	function getVideos(valueField){

		var url = en4.core.baseUrl + 'sesvideo/chanel/manage-videos';

		new Request.HTML({

				url: url,

				data: {

					'data':valueField,

				},

				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {

					sesJqueryObject('#manage_videos_data').html(responseHTML);

					disableSelectedVideos();

				}

    }).send();

	}

	function disableSelectedVideos(){

		 var ids = [];

		 $$('#added_manage_videos > li').each(function(el) {

           var id = el.get('id').match(/\d+/)[0];

						sesJqueryObject('#manage_videos_data').find('#videoId-'+id).addClass('overlay_video_added');

     });

	}

	sesJqueryObject(document).on('click','.add-video-manage',function(){

			sesJqueryObject(sesJqueryObject(this).parent().parent()).clone().appendTo(sesJqueryObject('#added_manage_videos'));

			sesJqueryObject(this).parent().parent().addClass('overlay_video_added');

			sesJqueryObject('#added_manage_videos').find('#videoId-'+sesJqueryObject(this).attr('data-url')).find('.sesvideo_grid_thumb').find('a').removeClass('add-video-manage');		

			sesJqueryObject('#added_manage_videos').find('#videoId-'+sesJqueryObject(this).attr('data-url')).find('.sesvideo_grid_thumb').find('a').addClass('selected-manage-video');

			sesJqueryObject("<span class='delete_selected_video'><a class='delete-selected' href='javascript:;'><i class='fa fa-close'></i></a></span>").insertAfter(sesJqueryObject('#added_manage_videos').find('#videoId-'+sesJqueryObject(this).attr('data-url')).find('.sesvideo_grid_thumb').find('.sesvideo_thumb_nolightbox'));			

			sesJqueryObject('#delete_video_ids').val(sesJqueryObject('#delete_video_ids').val().replace(sesJqueryObject(this).attr('data-url')+' ',''));

			var totalHeight = sesJqueryObject('#added_manage_videos').height();

			if(totalHeight > 310){

					sesJqueryObject('.added_manage_videos').css('height','310px');	

			}else{

					sesJqueryObject('.added_manage_videos').css('height',totalHeight+'px');	

			}

	});

	

	sesJqueryObject(document).on('click','.delete_selected_video',function(){

		var parentElement = sesJqueryObject(this).parent().parent();

		var dataId = parentElement.attr('id');

		var dataid = dataId.replace('videoId-','');

		sesJqueryObject('#manage_videos_data').find('#'+dataId).removeClass('overlay_video_added');

		var getDeleteIds = sesJqueryObject('#delete_video_ids').val();

		sesJqueryObject('#delete_video_ids').val(sesJqueryObject('#delete_video_ids').val().replace(dataid+' ','')+dataid+' ');

		parentElement.remove();

		var totalHeight = sesJqueryObject('#added_manage_videos').height();

		if(totalHeight > 310){

				sesJqueryObject('.added_manage_videos').css('height','310px');	

		}else{

				sesJqueryObject('.added_manage_videos').css('height',totalHeight+'px');	

		}

	});

  window.addEvent('domready', function() {

	var sesdevelopment = 1;

	<?php if(isset($this->category_id) && $this->category_id != 0){ ?>

			<?php if(isset($this->subcat_id)){$catId = $this->subcat_id;}else $catId = ''; ?>

      showSubCategory($('category_id').value,'<?php echo $catId; ?>','yes');

   <?php  }else{?>

	 if( $('subcat_id-wrapper'))

	  $('subcat_id-wrapper').style.display = "none";

	 <?php } ?>

	 <?php if(isset($this->subsubcat_id)){ ?>

    if (<?php echo isset($this->subcat_id) && intval($this->subcat_id)>0 ? $this->subcat_id : 'sesdevelopment' ?> == 0) {

		if($('subsubcat_id-wrapper'))

     $('subsubcat_id-wrapper').style.display = "none";

    } else {

			changeSes = true;

			<?php if(isset($this->subsubcat_id)){$subsubcat_id = $this->subsubcat_id;}else $subsubcat_id = ''; ?>

      showSubSubCategory('<?php echo $this->subcat_id; ?>','<?php echo $this->subsubcat_id; ?>','yes');

    }

	 <?php }else{?>

	 		if( $('subsubcat_id-wrapper'))

	 		 $('subsubcat_id-wrapper').style.display = "none";

	 <?php } ?>

  });

</script> 

