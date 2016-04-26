<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: manage.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php
$listApi = Engine_Api::_()->list();
$expirySettings = $listApi->expirySettings();
$approveDate=null;
if ($expirySettings == 2):
  $approveDate = $listApi->adminExpiryDuration();
endif;
?>

<style type="text/css">
  .list_browse_list_info_expiry{
    color:red;
  }
</style>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/List/externals/styles/style_list.css');?>

<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
   var searchLists = function() {
    if( Browser.Engine.trident ) {
      document.getElementById('filter_form').submit();
    } else {
      $('filter_form').submit();
    }
  }
  en4.core.runonce.add(function(){
    $$('#filter_form input[type=text]').each(function(f) {
      if (f.value == '' && f.id.match(/\min$/)) {
        new OverText(f, {'textOverride':'min','element':'span'});
        //f.set('class', 'integer_field_unselected');
      }
      if (f.value == '' && f.id.match(/\max$/)) {
        new OverText(f, {'textOverride':'max','element':'span'});
        //f.set('class', 'integer_field_unselected');
      }
    });
  });

  window.addEvent('onChangeFields', function() {
    var firstSep = $$('li.browse-separator-wrapper')[0];
    var lastSep;
    var nextEl = firstSep;
    var allHidden = true;
    do {
      nextEl = nextEl.getNext();
      if( nextEl.get('class') == 'browse-separator-wrapper' ) {
        lastSep = nextEl;
        nextEl = false;
      } else {
        allHidden = allHidden && ( nextEl.getStyle('display') == 'none' );
      }
    } while( nextEl );
    if( lastSep ) {
      lastSep.setStyle('display', (allHidden ? 'none' : ''));
    }
  });
</script>
<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
    //'topLevelId' => (int) @$this->topLevelId,
    //'topLevelValue' => (int) @$this->topLevelValue
  ))
?>

<?php include_once APPLICATION_PATH . '/application/modules/List/views/scripts/navigation_views.tpl'; ?>

<div class='layout_right'> <?php echo $this->form->render($this) ?>
   <?php if( count($this->quickNavigation) > 0 ): ?>
    <div class="quicklinks">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->quickNavigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

<div class='layout_middle'>
  <?php $list_approved = Zend_Registry::get('list_approved');
  	$renew_date= date('Y-m-d', mktime(0, 0, 0, date("m"), date('d', time()) + (Engine_Api::_()->getApi('settings', 'core')->getSetting('list.renew.email', 2))));?>
  <?php if ($this->current_count >= $this->quota && !empty($this->quota)): ?>
	  <div class="tip"> 
	  	<span><?php echo $this->translate('You have already created the maximum number of listings allowed. If you would like to create a new listing, please delete an old one first.'); ?> </span> 
	  </div>
	  <br/>
  <?php endif; ?>
  
  <?php if ($this->paginator->getTotalItemCount() > 0): ?>
	  <ul class="seaocore_browse_list">
	    <?php foreach ($this->paginator as $item): ?>
		    <li>
		      <div class='seaocore_browse_list_photo'>
		        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?> 
		      </div>
		      <div class='seaocore_browse_list_options'>

						<?php if ($this->can_edit): ?>
							<a href='<?php echo $this->url(array('action' => 'edit', 'listing_id' => $item->listing_id), 'list_specific', true) ?>' class='buttonlink icon_lists_edit'><?php if(!empty($list_approved)){ echo $this->translate('Edit Listing'); }else { echo $this->translate($this->listing_manage); } ?></a>
						<?php endif; ?>

		        <?php if ($this->allowed_upload_photo): ?>
							<a href='<?php echo $this->url(array('listing_id' => $item->listing_id), 'list_albumspecific', true) ?>' class='buttonlink icon_lists_photo_new'><?php echo $this->translate('Add Photos'); ?></a>
		        <?php endif; ?>

		        <?php if ($this->allowed_upload_video): ?>
							<a href='<?php echo $this->url(array('listing_id' => $item->listing_id), 'list_videospecific', true) ?>' class='buttonlink icon_lists_video_new'><?php if(!empty($list_approved)){ echo $this->translate('Add Videos'); }else { echo $this->translate($this->listing_manage); } ?></a>
           <?php endif; ?>
          
           <?php if($item->draft == 0 && $this->can_edit) echo $this->htmlLink(array('route' => 'list_specific', 'action' => 'publish', 'listing_id' => $item->listing_id), $this->translate('Publish Listing'), array(
						'class' => 'buttonlink smoothbox icon_list_publish')) ?> 
 
		        <?php if (!$item->closed && $this->can_edit): ?>
							<a href='<?php echo $this->url(array('action' => 'close', 'listing_id' => $item->listing_id), 'list_specific', true) ?>' class='buttonlink icon_lists_close'><?php echo $this->translate('Close Listing'); ?></a>
		        <?php elseif($this->can_edit): ?>
							<a href='<?php echo $this->url(array('action' => 'close', 'listing_id' => $item->listing_id), 'list_specific', true) ?>' class='buttonlink icon_lists_open'><?php echo $this->translate('Open Listing'); ?></a>
		        <?php endif; ?>

						<?php if($this->can_delete): ?>
							<a href='<?php echo $this->url(array('action' => 'delete', 'listing_id' => $item->listing_id), 'list_specific', true) ?>' class='buttonlink icon_lists_delete'><?php echo $this->translate('Delete Listing'); ?></a>
						<?php endif; ?>
          </div>
          
		      <div class='seaocore_browse_list_info'>
		        <div class='seaocore_browse_list_info_title'>
	          	<span>
               <?php if (empty($item->approved)): ?>
                <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/list_approved0.gif', '', array('class' => 'icon', 'title' => $this->translate('Not approved'))) ?>
               <?php endif; ?>
               
               <?php if (!empty($item->sponsored)): ?>
                <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
               <?php endif; ?>
               <?php if (!empty($item->featured)): ?>
                <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/list_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
               <?php endif; ?>
              <?php if ($item->closed): ?>
	           	 <img alt="close" src='<?php echo $this->layout()->staticBaseUrl?>application/modules/List/externals/images/close.png'/>
	            <?php endif; ?>
	            </span>
		          <h3> 
		          	<?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
		          </h3>	
		        </div>
            <?php if (($item->rating > 0) && $this->ratngShow): ?>
	            <div class='seaocore_browse_list_info_date clear'>
	              <span class="list_rating_star" title="<?php echo $item->rating.$this->translate(' rating'); ?>">
	              <span class="clear">
	                <?php for ($x = 1; $x <= $item->rating; $x++): ?>
	                <span class="rating_star_generic rating_star" ></span>
	                <?php endfor; ?>
	                <?php if ((round($item->rating) - $item->rating) > 0): ?>
	                <span class="rating_star_generic rating_star_half" ></span>
	                <?php endif; ?>
	              </span>
	                </span>
	            </div>
						<?php endif; ?>
						
		        <div class='seaocore_browse_list_info_date clear'>
		        	<?php echo $this->timestamp(strtotime($item->creation_date)) ?> - <?php echo $this->translate('posted by'); ?> 
		        	<?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>,
              <?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>,
							<?php echo $this->translate(array('%s review', '%s reviews', $item->review_count), $this->locale()->toNumber($item->review_count)) ?>,
              <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>,
              <?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
		        </div>
            
            <?php if($approveDate && $approveDate > $item->approved_date):?>
            <div class="list_browse_list_info_expiry">
              <?php echo $this->translate('Expired');?>
            </div>
            <?php elseif($expirySettings == 1):?> 
              <div class="seaocore_browse_list_info_date clear">
								<?php $current_date = date("Y-m-d i:s:m", time());?>
               <?php if(!empty($item->end_date)  && $item->end_date !='0000-00-00 00:00:00'):?>
								<?php if($item->end_date >= $current_date):?>
									<?php echo $this->translate('End Date:'); ?>
									<span><?php echo $this->translate( gmdate('M d, Y', strtotime($item->end_date))) ?></span>
								<?php else:?>
									<?php echo $this->translate('End Date:'); ?>
									<span class="list_browse_list_info_expiry"><?php echo $this->translate('Expired');?></span>
									<?php echo $this->translate('(You can edit the end date to make the listing live again.)');?>
								<?php endif;?>
                <?php endif;?>
              </div>
            <?php endif; ?>

            <?php if(!empty($item->location)): ?>
	            <div class='seaocore_browse_list_info_date'>
	              <?php  echo $this->translate($item->location); ?>
	              &nbsp;-
								<b><?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $item->listing_id, 'resouce_type' => 'list_listing'), $this->translate("Get Directions"), array('class' => 'smoothbox')); ?></b>
	            </div>
            <?php endif; ?>
             
		        <div class='seaocore_browse_list_info_blurb'>
		          <?php
                echo substr(strip_tags($item->body), 0, 350);
                if (strlen($item->body) > 349)
                  echo "...";
		          ?>
		        </div>
		      </div>
		    </li>
	    <?php endforeach; ?>
	  </ul>
  <?php elseif ($this->search): ?>
	  <div class="tip"> 
	  	<span> 
	  		<?php if(!empty($list_approved)){ echo $this->translate('You do not have any listing that match your search criteria.'); }else { echo $this->translate($this->listing_manage_msg); } ?> 
	  	</span> 
	  </div>
  <?php else: ?>
	  <div class="tip"> 
	  	<span> 
	  		<?php if(!empty($list_approved)){ $message =  $this->translate('You do not have any listings.'); }else { $message =  $this->translate($this->listing_manage_msg); } ?>
	  		 <?php $posting = '<a href="'.$this->url(array('action'=>'create'), 'list_general').'">' . 'posting' .  '</a>'; ?>
       <?php echo $this->translate('%s Get started by %s a new listing.',$message,  $posting); ?>
	    </span>
	  </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, null); ?>
</div>

<script type="text/javascript">
	$('filter_form').getElement('.browselists_criteria').addEvent('keypress', function(e){   
		if( e.key != 'enter' ) return;
		searchLists();
	});

	var getProfileType = function(category_id) {
		var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('profilemaps', 'list')->getMapping()); ?>;
		for(i = 0; i < mapping.length; i++) {
			if(mapping[i].category_id == category_id)
				return mapping[i].profile_type;
		}
		return 0;
	}

  var form;

	if($('filter_form')) {
		var form = document.getElementById('filter_form');
	} else if($('filter_form_category')){
		var form = document.getElementById('filter_form_category');
	}

  var subcategories = function(category_id, sub, subcatname, subsubcat)
  {
      
    if($('category_id') && form.elements['category_id']){
      form.elements['category_id'].value = '<?php echo $this->category_id?>';
    }
    if($('subcategory_id') && form.elements['subcategory_id']){
      form.elements['subcategory_id'].value = '<?php echo $this->subcategory_id?>';
    }
    if($('subsubcategory_id') && form.elements['subsubcategory_id']){
      form.elements['subsubcategory_id'].value = '<?php echo $this->subsubcategory_id?>';
    }
    if(category_id != '' && form.elements['category_id']){
      form.elements['category_id'].value = category_id;
    }
    if(category_id != 0) {
      if(sub == '')
     subsubcat = 0;
      changesubcategory(sub, subsubcat);
    }
    
  	var url = '<?php echo $this->url(array('action' => 'sub-category'), 'list_general', true);?>';
    en4.core.request.send(new Request.JSON({      	
      url : url,
      data : {
        format : 'json',
        category_id_temp : category_id
      },
      onSuccess : function(responseJSON) {
      	clear('subcategory_id');
        var  subcatss = responseJSON.subcats;        
        addOption($('subcategory_id')," ", '0');
        for (i=0; i< subcatss.length; i++) {
          addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);  
          $('subcategory_id').value = sub;
          form.elements['subcategory'].value = $('subcategory_id').value;
        	form.elements['categoryname'].value = subcatss[i]['categoryname_temp'];
          form.elements['category'].value = category_id;
          form.elements['subcategory_id'].value = $('subcategory_id').value;
          if(form.elements['subsubcategory'])
          form.elements['subsubcategory'].value = subsubcat;
          if(form.elements['subsubcategory_id'])
          form.elements['subsubcategory_id'].value = subsubcat;
        }

        if(subcatss.length == 0) {
	      	form.elements['categoryname'].value = 0;
        }
        
        if(category_id == 0) {
          clear('subcategory_id');
          clear('subsubcategory_id');
          $('subcategory_id').style.display = 'none';
          $('subcategory_id-label').style.display = 'none';
          $('subsubcategory_id').style.display = 'none';
          $('subsubcategory_id-label').style.display = 'none';
        }
      }
    }));    
  };

  function clear(ddName)
  { 
    for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
    { 
      document.getElementById(ddName).options[ i ]=null; 	      
    } 
  }

  function addOption(selectbox,text,value )
  {
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;			
    if(optn.text != '' && optn.value != '') {
      $('subcategory_id').style.display = 'block';
      $('subcategory_id-label').style.display = 'block';
      selectbox.options.add(optn);
    } 
    else {
      $('subcategory_id').style.display = 'none';
      $('subcategory_id-label').style.display = 'none';
      selectbox.options.add(optn);
    }
  }
  
	var changesubcategory = function(subcatid, subsubcat)
	{
		var url = '<?php echo $this->url(array('action' => 'subsub-category'), 'list_general', true);?>';
		var request = new Request.JSON({
			url : url,
			data : {
				format : 'json',
				subcategory_id_temp : subcatid
			},
			onSuccess : function(responseJSON) {
				clear('subsubcategory_id');
				var  subsubcatss = responseJSON.subsubcats;
				addSubOption($('subsubcategory_id')," ", '0');
				for (i=0; i< subsubcatss.length; i++) {
					addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
					$('subsubcategory_id').value = subsubcat;
					if(form.elements[' subsubcategory_id'])
					form.elements[' subsubcategory_id'].value = $('subsubcategory_id').value;
					if(form.elements[' subsubcategory'])
					form.elements['subsubcategory'].value = $('subsubcategory_id').value;
					if($('subsubcategory_id')) {
						$('subsubcategory_id').value = subsubcat;
					}
				}

				if(subcatid == 0) {
					clear('subsubcategory_id');
					if($('subsubcategory_id-label'))
					$('subsubcategory_id-label').style.display = 'none';
				}
			}
		});
		request.send();
	};

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

  var cat = '<?php echo $this->category_id ?>';

  if(cat != '') {
    var sub = '<?php echo $this->subcategory_id; ?>';
    var subcatname = '<?php echo $this->subcategory_name; ?>';
    var subsubcat = '<?php echo $this->subsubcategory_id; ?>';
    subcategories(cat, sub, subcatname,subsubcat);
  }
  
  function show_subcat(cat_id) 
  {		
    if(document.getElementById('subcat_' + cat_id)) {
      if(document.getElementById('subcat_' + cat_id).style.display == 'block') {		
        document.getElementById('subcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = './application/modules/List/externals/images/plus16.gif';
      } 
      else if(document.getElementById('subcat_' + cat_id).style.display == '') {			
        document.getElementById('subcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = './application/modules/List/externals/images/plus16.gif';
      }
      else {			
        document.getElementById('subcat_' + cat_id).style.display = 'block';
        document.getElementById('img_' + cat_id).src = './application/modules/List/externals/images/minus16.gif';
      }		
    }
  }
</script>