<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: categories.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<script type="text/javascript">
	var previewFileForceOpen;
	var previewFile = function(event)
	{
		event = new Event(event);
		element = $(event.target).getParent('.admin_file').getElement('.admin_file_preview');
		
		if( !element || element.getChildren().length < 1 ) {
			return;
		}

		if( event.type == 'click' ) {
			if( previewFileForceOpen ) {
				previewFileForceOpen.setStyle('display', 'none');
				previewFileForceOpen = false;
			} else {
				previewFileForceOpen = element;
				previewFileForceOpen.setStyle('display', 'block');
			}
		}
		if( previewFileForceOpen ) {
			return;
		}

		var targetState = ( event.type == 'mouseover' ? true : false );
		element.setStyle('display', (targetState ? 'block' : 'none'));
	}

	window.addEvent('load', function() {
		$$('.categories-image-preview').addEvents({
			click : previewFile,
			mouseout : previewFile,
			mouseover : previewFile
		});
		$$('.admin_file_preview').addEvents({
			click : previewFile
		});
	});
</script>

<?php $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl(); ?>
<iframe id='ajaxframe' name='ajaxframe' style='display: none;' src='javascript:false;'></iframe>

<h2><?php echo $this->translate("Listings / Catalog Showcase Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
	<div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
	</div>
<?php endif; ?>

<div class='clear settings'>
  <div class='global_form seaocore_categories_block'>
    <div>
      <div>
        <h3><?php echo $this->translate("Listings Categories") ?></h3>
        <h4 class="description"><?php echo $this->translate("Below, you can add and manage the various categories, sub-categories and 3rd level categories for the Listings on your site. Sub-categories are very useful as they allow you to further categorize and organize the Listings on your site beyond the superficial categories. You can also add icons for categories, sub-categories and 3rd level categories. To do so, click on 'Add' option available against the categories, sub-categories and 3rd level categories. Below, you will be able to edit, delete and see preview of the icons.<br/>Note : To edit categories, sub-categories and 3rd level categories, click on their names.");?></h4>
        <a href='javascript:addcat();' class="buttonlink seaocore_icon_add"><?php echo $this->translate("Add Category") ?></a>
        <br />
        <div id='categories' class="seaocore_admin_cat_wrapper">
          <?php foreach ($this->categories as $value): ?>
            <div id="cat_<?php echo $value['category_id']; ?>" class="seaocore_admin_cat">
              <input type="hidden" id="cat_<?php echo $value['category_id']; ?>_input_count" value="<?php echo $value["count"] ?>">
              <?php $category_name = $this->translate($value['category_name']); ?>
              <?php $link = "<a href='javascript:editcat(" . $value['category_id'] . ", 0, " . $value['count'] . ");' id='cat_" . $value['category_id'] . "_title'>" . $category_name . "</a> [" . $value["count"] . "]"; ?>

							<div class="admin_file">
								<?php echo "<img src='application/modules/List/externals/images/folder_open_yellow.gif' border='0' class='seaocore_subcat_handle handle_cat' ><span id='cat_" . $value['category_id'] . "_span'>$link</span>" ?>

								| <b class="bold"><?php echo $this->translate("Icon: ");?></b>
								<?php if(!empty($value['file_id'])):?>
									<span class="categories-image-preview seaocore_file_preview_wrapper">
										<a href="javascript:void(0);" class="t_normal"><?php echo $this->translate('Preview'); ?></a>
										<span class="admin_file_preview seaocore_file_preview" style="display:none">
											<img alt="" src="<?php echo $this->storage->get($value['file_id'], '')->getPhotoUrl(); ?>" />
										</span>
									</span>
										|
									<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'list', 'controller' => 'settings', 'action' => 'edit-icon', 'category_id' => $value['category_id']), $this->translate('Edit'), array('class' => 'smoothbox')) ?>
										| 
									<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'list', 'controller' => 'settings', 'action' => 'delete-icon', 'category_id' => $value['category_id']), $this->translate('Delete'), array(
										'class' => 'smoothbox',
									)) ?> 

								<?php else:?>
									<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'list', 'controller' => 'settings', 'action' => 'add-icon', 'category_id' => $value['category_id']), $this->translate('Add'), array('class' => 'smoothbox')) ?>
								<?php endif;?>
							</div>

              <?php $subcate = $this->translate("Sub Categories") . " - <a href='javascript:addsubcat(" . $value['category_id'] . ",0)'> " . $this->translate("[Add New]") . "</a>" ?>
              <?php echo "<div class='seaocore_admin_cat_new'>$subcate</div>" ?>
              <?php echo "<br />"; ?>
              <script type="text/javascript">
                window.addEvent('domready', function(){ createSortable("subcats_<?php echo $value['category_id'] ?>", "img.handle_subcat_<?php echo $value['category_id'] ?>"); });
              </script>
              <div id="subcats_<?php echo $value['category_id']; ?>" class="seaocore_sub_cat_wrapper">
                <?php foreach ($value['sub_categories'] as $subcategory): ?>
                  <div id="cat_<?php echo $subcategory['sub_cat_id']; ?>" class="seaocore_sub_cat_list">
                    <input type="hidden" id="cat_<?php echo $subcategory['sub_cat_id']; ?>_input_count" value="<?php echo $subcategory['count'] ?>">
                    <?php $subcatname = $this->translate($subcategory['sub_cat_name']); ?>
                    <?php $subcats = "<a href='javascript:editcat(" . $subcategory["sub_cat_id"] . ", " . $value['category_id'] . ", " . $subcategory["count"] . ");' id='cat_" . $subcategory["sub_cat_id"] . "_title'>$subcatname</a>" ?>

										<div class="admin_file">
											<?php echo "<img src='application/modules/List/externals/images/folder_open_green.gif' border='0' class='seaocore_subcat_handle handle_subcat_" . $value['category_id'] . "'><span id='cat_" . $subcategory["sub_cat_id"] . "_span'>$subcats [" . $subcategory["count"] . "]</span>" ?>

											| <b class="bold"><?php echo $this->translate("Icon: ");?></b>
											<?php if(!empty($subcategory['file_id'])):?>
												<span class="categories-image-preview seaocore_file_preview_wrapper">
													<a href="javascript:void(0);" class="t_normal"><?php echo $this->translate('Preview'); ?></a>
													<span class="admin_file_preview seaocore_file_preview" style="display:none">
														<img alt="" src="<?php echo $this->storage->get($subcategory['file_id'], '')->getPhotoUrl(); ?>" />
													</span>
												</span>

													|
												<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'list', 'controller' => 'settings', 'action' => 'edit-icon', 'category_id' => $subcategory['sub_cat_id']), $this->translate('Edit'), array('class' => 'smoothbox')) ?>
													| 
												<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'list', 'controller' => 'settings', 'action' => 'delete-icon', 'category_id' => $subcategory['sub_cat_id']), $this->translate('Delete'), array(
													'class' => 'smoothbox',
												)) ?> 

											<?php else:?>
												<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'list', 'controller' => 'settings', 'action' => 'add-icon', 'category_id' => $subcategory['sub_cat_id']), $this->translate('Add'), array('class' => 'smoothbox')) ?>
											<?php endif;?>

										</div>       
           
                    <?php $treesubcate = $this->translate('3%s Level Category', "<sup>rd</sup>") . " - <a href='javascript:addtreesubcat(" . $subcategory['sub_cat_id'] . ")'> " . $this->translate("[Add New]") . "</a>" ?>
                    <?php echo "<div class='seaocore_admin_cat_new'>$treesubcate</div>" ?>
                    <script type="text/javascript">
                      <!--
                      window.addEvent('domready', function(){ createSortable("treesubcats_<?php echo $subcategory['sub_cat_id'] ?>", "img.handle_treesubcat_<?php echo $subcategory['sub_cat_id'] ?>"); });
                      //-->
                    </script>
                    <div id="treesubcats_<?php echo $subcategory['sub_cat_id']; ?>" class="seaocore_third_cat_list_wrapper">
                      <?php if(isset($subcategory['tree_sub_cat'])):?>
                        <?php foreach ($subcategory['tree_sub_cat'] as $treesubcategory): ?>
                        <div id="cat_<?php echo $treesubcategory['tree_sub_cat_id']; ?>" class="seaocore_third_cat_list">
                        <input type="hidden" id="cat_<?php echo $treesubcategory['tree_sub_cat_id']; ?>_input_count" value="<?php echo $treesubcategory['count'] ?>">
                          <?php $treesubcatname = $this->translate($treesubcategory['tree_sub_cat_name']); ?>
                          <?php $treesubcats = "<a href='javascript:editcat(" . $treesubcategory["tree_sub_cat_id"] . ", " . $subcategory['sub_cat_id'] . ", " . $treesubcategory["count"] . ");' id='cat_" . $treesubcategory["tree_sub_cat_id"] . "_title'>$treesubcatname</a>" ?>

													<div class="admin_file">
														<?php echo "<img src='application/modules/List/externals/images/folder_open_green.gif' border='0' class='seaocore_subcat_handle handle_treesubcat_" . $subcategory['sub_cat_id'] . "'><span id='cat_" . $treesubcategory["tree_sub_cat_id"] . "_span'>$treesubcats [" . $treesubcategory["count"] . "]</span>" ?>

														| <b class="bold"><?php echo $this->translate("Icon: ");?></b>
														<?php if(!empty($treesubcategory['file_id'])):?>
															<span class="categories-image-preview seaocore_file_preview_wrapper">
																<a href="javascript:void(0);" class="t_normal"><?php echo $this->translate('Preview'); ?></a>
																<span class="admin_file_preview seaocore_file_preview" style="display:none">
																	<img alt="" src="<?php echo $this->storage->get($treesubcategory['file_id'], '')->getPhotoUrl(); ?>" />
																</span>
															</span>
																|
															<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'list', 'controller' => 'settings', 'action' => 'edit-icon', 'category_id' => $treesubcategory['tree_sub_cat_id']), $this->translate('Edit'), array('class' => 'smoothbox')) ?>
																| 
															<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'list', 'controller' => 'settings', 'action' => 'delete-icon', 'category_id' => $treesubcategory['tree_sub_cat_id']), $this->translate('Delete'), array(
																'class' => 'smoothbox',
															)) ?> 

														<?php else:?>
																<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'list', 'controller' => 'settings', 'action' => 'add-icon', 'category_id' => $treesubcategory['tree_sub_cat_id']), $this->translate('Add'), array('class' => 'smoothbox')) ?>
														<?php endif;?>

													</div>
                        </div>
                        <?php endforeach; ?>
                      <?php endif;?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>          
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>    
  </div>
</div>

<script type="text/javascript">
  function createSortable(divId, handleClass) 
  {
    new Sortables($(divId), {handle:handleClass, onComplete: function() { changeorder(this.serialize(), divId); }});
  }

  Sortables.implement({
    serialize: function(){
      var serial = [];
      this.list.getChildren().each(function(el, i){
        serial[i] = el.getProperty('id');
      }, this);
      return serial;
    }
  });

  window.addEvent('domready', function(){	createSortable('categories', 'img.handle_cat'); });

  //THIS FUNCTION ADDS A CATEGORY INPUT TO THE Listings
  function addcat() 
  {
    var catarea = $('categories');
    var newdiv = document.createElement('div');
    newdiv.id = 'cat_new';
    newdiv.className="seaocore_admin_cat";
    newdiv.innerHTML ='<div><img src="application/modules/List/externals/images/folder_open_yellow.gif" border="0" class="seaocore_subcat_handle handle_cat"><span id="cat_new_span"><input type="text" id="cat_new_input" maxlength="100" onBlur="savecat(\'new\', \'\', \'\')" onkeypress="return noenter_cat(\'new\', event)"></span></div>';
    catarea.appendChild(newdiv);
    var catinput = $('cat_new_input');
    catinput.focus();
  }

  //THIS FUNCTION RUNS THE APPROPRIATE SAVING ACTION
  function savecat(catid, oldcat_title, cat_dependency, subcat_dependency)
  { 
    var catinput = $('cat_'+catid+'_input'); 
    if(catinput.value == "" && catid == "new") {		
      removecat(catid);
    } 
    else if(catinput.value == "" && catid != "new") {

			if(cat_dependency == 0) {
				Smoothbox.open('<?php echo $baseurl ?>'+'/admin/list/settings/mapping-category/catid/'+catid+'/cat_dependency/'+cat_dependency+'/cat_title/'+encodeURIComponent(catinput.value)+'/subcat_dependency/'+subcat_dependency+'/oldcat_title/'+oldcat_title);
			}
			else {
				if(confirm("<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete this category? NOTE: If you are deleting a main category, all subcategories and 3rd level categories will be deleted as well.")); ?>" )) {
					$('ajaxframe').src = '<?php echo $baseurl ?>'+'/admin/list/settings/categories?task=savecat&cat_id='+catid+'&cat_dependency='+cat_dependency+'&cat_title='+encodeURIComponent(catinput.value)+'&subcat_dependency='+subcat_dependency;
				} else {
					savecat_result(catid, catid, oldcat_title, subcat_dependency);
				}
			}
    } 
    else {		
      $('ajaxframe').src = '<?php echo $baseurl ?>'+'/admin/list/settings/categories?task=savecat&cat_id='+catid+'&cat_dependency='+cat_dependency+'&cat_title='+encodeURIComponent(catinput.value)+'&subcat_dependency='+subcat_dependency;
    }
  }

  //THIS FUNCTION REMOVES A CATEGORY FROM THE Listings
  function removecat(catid) {
    var catdiv = $('cat_'+catid); 
    var catarea = catdiv.parentNode;
    catarea.removeChild(catdiv);
  }

  function savecat_result(old_catid, new_catid, cat_title, cat_dependency, subcat_dependency)
  {
    var count;
    if($('cat_'+old_catid+'_input_count') == null) {
      count = 0;
    } 
    else {
      count = $('cat_'+old_catid+'_input_count').value;
    }
    var catinput = $('cat_'+old_catid+'_input'); 
    var catspan = $('cat_'+old_catid+'_span'); 
    var catdiv = $('cat_'+old_catid); 
    catdiv.id = 'cat_'+new_catid;
    catspan.id = 'cat_'+new_catid+'_span';
    catspan.innerHTML = '<a href="javascript:editcat(\''+new_catid+'\', \''+cat_dependency+'\', \''+count+'\');" id="cat_'+new_catid+'_title">'+cat_title+'</a>';

		if(old_catid == 'new') {

      catspan.innerHTML = catspan.innerHTML + " [" + count + "]";
			var icon_element = 	'<a class="smoothbox t_normal" href="<?php echo $this->url(array('module' => 'list', 'controller' => 'settings', 'action' => 'add-icon'),"admin_default") ?>/category_id/'+new_catid+'"><?php echo $this->translate('Add');?></a>' + ' <b class="bold"><?php echo $this->translate('Icon: ') ?></b>' + '<b> | </b>';

      Elements.from(icon_element).inject(catspan, 'after');
		}
		else {
			catspan.innerHTML = 	catspan.innerHTML + " [" + count + "]";
		}

    if(old_catid == 'new') {
      if(cat_dependency == 0) {
        catdiv.innerHTML += '<div class="seaocore_admin_cat_new"><?php echo $this->translate('Sub Categories')?> - <a href="javascript:addsubcat(\''+new_catid+'\', \''+cat_dependency+'\');">[Add New]</a></div>';
        var subcatdiv = document.createElement('div');
        subcatdiv.id = 'subcats_'+new_catid;
        subcatdiv.style.cssText = 'padding-left: 20px;';
        catdiv.appendChild(subcatdiv);
        createSortable('categories', 'img.handle_cat');
      }
      else if(subcat_dependency == 0 && cat_dependency!=0) {

        catdiv.innerHTML += '<div class="seaocore_admin_cat_new"><?php echo $this->translate('3%s Level Category', "<sup>rd</sup>")?> - <a href="javascript:addtreesubcat(\''+new_catid+'\', \''+cat_dependency+'\');">[Add New]</a></div>';
        var treesubcatdiv = document.createElement('div');
        treesubcatdiv.id = 'treesubcats_'+new_catid;
        treesubcatdiv.style.cssText = 'padding-left: 20px;';
        catdiv.appendChild(treesubcatdiv);
        createSortable('categories', 'img.handle_cat');
      }
      else {
        createSortable('subcats_'+cat_dependency, 'img.handle_subcat_'+cat_dependency);
      }
    }
		Smoothbox.bind(catdiv);
  }

  //THIS FUNCTION CHANGES THE ORDER OF ELEMENTS
  function changeorder(listorder, divId) 
  {
    $('ajaxframe').src = '<?php echo $baseurl ?>'+'/admin/list/settings/categories?task=changeorder&listorder='+listorder+'&divId='+divId;
  }

  //THIS FUNCTION PREVENTS THE ENTER KEY FROM SUBMITTING THE FORM
  function noenter_cat(catid, e) 
  { 
    if (window.event) keycode = window.event.keyCode;
    else if (e) keycode = e.which;
    if(keycode == 13) {
      var catinput = $('cat_'+catid+'_input'); 
      catinput.blur();
      return false;
    }
  }

  function addsubcat(catid, subcatdependency)
  {
    var catarea = $('subcats_'+catid);
    var newdiv = document.createElement('div');
    newdiv.id = 'cat_new';
    newdiv.style.cssText = 'padding-left: 20px;';
    if(catarea.nextSibling) { 
      var thisdiv = catarea.nextSibling;
      while(thisdiv.nodeName != "DIV") { if(thisdiv.nextSibling) { thisdiv = thisdiv.nextSibling; } else { break; } }
      if(thisdiv.nodeName != "DIV") { next_catid = "new"; } else { next_catid = thisdiv.id.substr(4); }
    } else {
      next_catid = 'new';
    }
    newdiv.innerHTML = '<div><img src="application/modules/List/externals/images/folder_open_green.gif" border="0" class="seaocore_subcat_handle handle_subcat_'+catid+'"><span id="cat_new_span"><input type="text" id="cat_new_input" maxlength="100" onBlur="savecat(\'new\', \'\', \''+catid+'\', \''+subcatdependency+'\')" onkeypress="return noenter_cat(\'new\', event)"></span></span></div>';
    catarea.appendChild(newdiv);
    var catinput = $('cat_new_input');
    catinput.focus();
  }

  function addtreesubcat(catid, subcat_dependancy)
  {
    var catarea = $('treesubcats_'+catid);
    var newdiv = document.createElement('div');
    newdiv.id = 'cat_new';
    newdiv.style.cssText = 'padding-left: 20px;margin-bottom:10px';
    if(catarea.nextSibling) {
      var thisdiv = catarea.nextSibling;
      while(thisdiv.nodeName != "DIV") { if(thisdiv.nextSibling) { thisdiv = thisdiv.nextSibling; } else { break; } }
      if(thisdiv.nodeName != "DIV") { next_catid = "new"; } else { next_catid = thisdiv.id.substr(4); }
    } else {
      next_catid = 'new';
    }
    newdiv.innerHTML = '<div><img src="application/modules/List/externals/images/folder_open_green.gif" border="0" class="seaocore_subcat_handle handle_treesubcat_'+catid+'"><span id="cat_new_span"><input type="text" id="cat_new_input" maxlength="100" onBlur="savecat(\'new\', \'\', \''+catid+'\', \''+catid+'\')" onkeypress="return noenter_cat(\'new\', event)"></span></span></div>';
    catarea.appendChild(newdiv);
    var catinput = $('cat_new_input');
    catinput.focus();
  }

  function editcat(catid, cat_dependency, count) 
  {
    var catspan = $('cat_'+catid+'_span'); 
    var cattitle = $('cat_'+catid+'_title');
    var replacedcattitle = cattitle.innerHTML.replace(/'/g, "&amp;#039;");
    var parsecattitle = replacedcattitle.replace(/"/g, "&amp;#039;");
    catspan.innerHTML = '<input type="text" id="cat_'+catid+'_input" maxlength="100" onBlur="savecat(\''+catid+'\', \''+parsecattitle+'\', \''+cat_dependency+'\')" onkeypress="return noenter_cat(\''+catid+'\', event)" >' ;
    catspan.innerHTML = 	catspan.innerHTML + " [" + count + "]";
    var catinput = $('cat_'+catid+'_input');
    catinput.value=cattitle.innerHTML;
    catinput.focus();			
  }
</script>
