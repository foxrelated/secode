<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if(!empty($this->sitestoreurlenabled) && !empty($this->show_url) && !empty($this->edit_url)):?>
	<script type="text/javascript">

		window.addEvent('domready', function() { 
		ShowUrlColumn("<?php echo $this->store_id; ?>");
		});

    sm4.core.language.addData({
      "Check Availability":"<?php echo $this->string()->escapeJavascript($this->translate("Check Availability"));?>"
   });

	//<![CDATA[
		window.addEvent('load', function()
		{
			$('store_url_address').innerHTML = $('store_url_address').innerHTML.replace('STORE-NAME', '<span id="store_url_address_text">STORE-NAME</span>');
			$('short_store_url_address').innerHTML = $('short_store_url_address').innerHTML.replace('STORE-NAME', '<span id="short_store_url_address_text">STORE-NAME</span>');

			$('store_url').addEvent('keyup', function()
			{
				var text = 'STORE-NAME';
				if( this.value != '' )
				{
					text = this.value;
				}
				$('store_url_address_text').innerHTML = text;
        $('short_store_url_address_text').innerHTML = text;
			});
			// trigger on store-load
			if ($('store_url').value.length)
					$('store_url').fireEvent('keyup');
		});
	//]]>
	</script>
<?php endif;?>

<?php if (empty($this->is_ajax)) : ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>

	<div class="layout_middle">
		<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/edit_tabs.tpl'; ?>
		<?php
		$this->headScript()
						->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
						->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
						->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
						->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
		?>
		<?php
			/* Include the common user-end field switching javascript */
			echo $this->partial('_jsSwitch.tpl', 'fields', array(
				//'topLevelId' => (int) @$this->topLevelId,
				//'topLevelValue' => (int) @$this->topLevelValue
			))
		?>

		<div class="sitestore_edit_content">
			<div class="sitestore_edit_header">
			  <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestore->store_id, $this->sitestore->owner_id, $this->sitestore->getSlug()),$this->translate('View Store')) ?>
				<h3><?php echo $this->translate('Dashboard: ').$this->sitestore->title; ?></h3>
			</div>
			<div id="show_tab_content">
		<?php endif; ?>

<?php  echo $this->form->render(); ?>
<?php if (empty($this->is_ajax)) : ?>	
	    </div>
	  </div>	
  </div>
<?php endif; ?>


<script type="text/javascript">
  if($('subcategory_id-wrapper'))
    $('subcategory_id-wrapper').style.display = 'block';
  if($('subcategory_id-label'))
    $('subcategory_id-label').style.display = 'block';
  if($('subsubcategory_id-wrapper'))
    $('subsubcategory_id-wrapper').style.display = 'block';
  if($('subsubcategory_id-label'))
    $('subsubcategory_id-label').style.display = 'block';
		var subcatid = '<?php echo $this->subcategory_id; ?>';

  var submitformajax = 0;
	var show_subcat = 1;
	<?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.category.edit', 0) && !empty($this->sitestore->category_id)) :?>
		 show_subcat = 0;
	<?php endif;	?>
	
	  sm4.core.runonce.add(function()
	  {
       checkDraft();

	    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
	      'postVar' : 'text',	
	      'minLength': 1,
	      'selectMode': 'pick',
	      'autocompleteType': 'tag',
	      'className': 'tag-autosuggest',
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
	  var subcategory = function(category_id, subcatid, subcatname,subsubcatid)
		{
      changesubcategory(subcatid, subsubcatid);
			if($('#buttons-wrapper')) {
		  	$('#buttons-wrapper').css('display','none');
			}
			if(subcatid == '')
			if($('#subcategory_id-wrapper'))
			$('#subcategory_id-wrapper').css('display','block');
      
			var url = '<?php echo $this->url(array('action' => 'subcategory'), 'sitestore_general', true);?>';
			sm4.core.request.send(new Request.JSON({      	
		    url : url,
		    data : {
		      format : 'json',
		      category_id_temp : category_id
				},
		    onSuccess : function(responseJSON) { 
		    	if($('#buttons-wrapper')) {
				  	$('#buttons-wrapper').css('display','block');
					}
		    	clear('subcategory_id');
		    	var  subcatss = responseJSON.subcats;
		      addOption($('subcategory_id')," ", '0');
          for (i=0; i< subcatss.length; i++) {
            addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
            if(show_subcat == 0) {
              if($('subcategory_id'))
              $('subcategory_id').disabled = 'disabled';
              if($('subsubcategory_id'))
              $('subsubcategory_id').disabled = 'disabled';
            }
            if($('subcategory_id')) {
              $('subcategory_id').value = subcatid;
            }
          }
						
          if(category_id == 0) {
            clear('subcategory_id');
            if($('subcategory_id'))
            $('subcategory_id').style.display = 'none';
            if($('subcategory_id-label'))
            $('subcategory_id-label').style.display = 'none';
          }
		    }
			  }), {
          "force":true
        });
		};

    var changesubcategory = function(subcatid, subsubcatid)
		{
			if($('#buttons-wrapper')) {
		  	$('#buttons-wrapper').css('display' ,'none');
			}
			if(subsubcatid == '')
			if($('subsubcategory_id-wrapper'))
			$('subsubcategory_id-wrapper').style.display = 'block';
			var url = '<?php echo $this->url(array('action' => 'subsubcategory'), 'sitestore_general', true);?>';
   		var request = new Request.JSON({
		    url : url,
		    data : {
		      format : 'json',
		      subcategory_id_temp : subcatid
				},
		    onSuccess : function(responseJSON) {
		    	if($('#buttons-wrapper')) {
				  	$('3buttons-wrapper').css('display', 'block');
					}
		    	clear('subsubcategory_id');
		    	var  subsubcatss = responseJSON.subsubcats;          
		      addSubOption($('subsubcategory_id')," ", '0');
          for (i=0; i< subsubcatss.length; i++) {
           
            addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
            if($('subsubcategory_id')) {
              $('subsubcategory_id').value = subsubcatid;
            }
          }
		    }
			});
      request.send();
		};

		function clear(ddName)
		{ 
			if(document.getElementById(ddName)) {
			   for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
			   { 
			      document.getElementById(ddName).options[ i ]=null; 			      
			   } 
			}
		}
		function addOption(selectbox,text,value )
		{
			var optn = document.createElement("OPTION");
			optn.text = text;
			optn.value = value;			
			if(optn.text != '' && optn.value != '') {
				if($('subcategory_id'))
				$('subcategory_id').style.display = 'block';
				if($('subcategory_id-label'))
				$('subcategory_id-label').style.display = 'block';
				selectbox.options.add(optn);
			} 
			else {
				if($('subcategory_id'))
				$('subcategory_id').style.display = 'none';
				if($('subcategory_id-label'))
				$('subcategory_id-label').style.display = 'none';
				selectbox.options.add(optn);
			}
		}
	
		var cat = '<?php echo $this->category_id ?>';
		if(cat != '') {		
			 subcatid = '<?php echo $this->subcategory_id; ?>';
       subsubcatid = '<?php echo $this->subsubcategory_id; ?>';
			 var subcatname = '<?php echo $this->subcategory_name; ?>';			 
			 subcategory(cat, subcatid, subcatname,subsubcatid);
		}

		window.addEvent('domready', function() {      
			//var e4 = $('store_url_msg-wrapper');
			if($('store_url_msg-wrapper'))
			$('store_url_msg-wrapper').setStyle('display', 'none');
		});

  function checkDraft(){
    if($('draft')){
      if($('draft').value==0) {
        $("search-wrapper").style.display="none";
        $("search").checked= false;
      } else{
        $("search-wrapper").style.display="block";
        $("search").checked= true;
      }
    }
  }


    function addSubOption(selectbox,text,value )
    {
      var optn = document.createElement("OPTION");
      optn.text = text;
      optn.value = value;
      if(optn.text != '' && optn.value != '') {
        $('subsubcategory_id').style.display = 'block';
         if($('subsubcategory_id-wrapper'))
          $('subsubcategory_id-wrapper').style.display = 'block';
         if($('subsubcategory_id-label'))
          $('subsubcategory_id-label').style.display = 'block';
        selectbox.options.add(optn);
      } else {
        $('subsubcategory_id').style.display = 'none';
         if($('subsubcategory_id-wrapper'))
          $('subsubcategory_id-wrapper').style.display = 'none';
         if($('subsubcategory_id-label'))
          $('subsubcategory_id-label').style.display = 'none';
        selectbox.options.add(optn);
      }

    }
</script>