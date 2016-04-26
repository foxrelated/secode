<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
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
table.admin_table thead tr th,
table.admin_table tbody tr td{
	padding:7px 5px;
}
.search form > div
{
	margin:0px 10px 10px 0;
}
.search div input {
  margin-top: 3px;
  width: 130px;
}
.search div select {
	max-width:150px;
}
</style>

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){  

    if( order == currentOrder ) { 
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else { 
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }

	function multiDelete()
	{
		return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected listings ?")) ?>');
	}

	function selectAll()
	{
	  var i;
	  var multidelete_form = $('multidelete_form');
	  var inputs = multidelete_form.elements;
	  for (i = 1; i < inputs.length - 1; i++) {
	    if (!inputs[i].disabled) {
	      inputs[i].checked = inputs[0].checked;
    	}
  	}
	}
</script>

<h2><?php echo $this->translate('Listings / Catalog Showcase Plugin'); ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<h2><?php echo $this->translate('Manage Listings'); ?></h2>
<h4><?php echo $this->translate('This page lists all of the listings your users have posted. You can use this page to monitor these listings and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific listing entries. Leaving the filter fields blank will show all the listing entries on your social network. Here, you can also make listings featured / un-featured, sponsored / un-sponsored and approve / dis-approve them.');?></h4><br />

<div class="admin_search sead_admin_search">
  <div class="search">
    <form method="post" class="global_form_box" action="">

      <div>
	      <label>
	      	<?php echo  $this->translate("Title") ?>
	      </label>
	      <?php if( empty($this->title)):?>
	      	<input type="text" name="title" /> 
	      <?php else: ?>
	      	<input type="text" name="title" value="<?php echo $this->translate($this->title)?>"/>
	      <?php endif;?>
      </div>

      <div>
      	<label>
      		<?php echo  $this->translate("Owner") ?>
      	</label>	
      	<?php if( empty($this->owner)):?>
      		<input type="text" name="owner" /> 
      	<?php else: ?> 
      		<input type="text" name="owner" value="<?php echo $this->translate($this->owner)?>" />
      	<?php endif;?>
      </div>

      <div>
	    	<label>
	      	<?php echo  $this->translate("Featured") ?>	
	      </label>
        <select id="" name="featured">
          <option value="0" ><?php echo $this->translate("Select") ?></option>
          <option value="2" <?php if( $this->featured == 2) echo "selected";?> ><?php echo $this->translate("Yes") ?></option>
          <option value="1" <?php if( $this->featured == 1) echo "selected";?> ><?php echo $this->translate("No") ?></option>
         </select>
      </div>

      <div>
	    	<label>
	      	<?php echo  $this->translate("Sponsored") ?>	
	      </label>
        <select id="sponsored" name="sponsored">
            <option value="0"  ><?php echo $this->translate("Select") ?></option>
          <option value="2" <?php if( $this->sponsored == 2) echo "selected";?> ><?php echo $this->translate("Yes") ?></option>
          <option value="1"  <?php if( $this->sponsored == 1) echo "selected";?>><?php echo $this->translate("No") ?></option>
         </select>
      </div>    
     
      <div>
	    	<label>
	      	<?php echo  $this->translate("Approved") ?>	
	      </label>
        <select id="sponsored" name="approved">
            <option value="0" ><?php echo $this->translate("Select") ?></option>
          <option value="2" <?php if( $this->approved == 2) echo "selected";?> ><?php echo $this->translate("Yes") ?></option>
          <option value="1" <?php if( $this->approved == 1) echo "selected";?> ><?php echo $this->translate("No") ?></option>
         </select>
      </div>
      
      <div>
	    	<label>
	      	<?php echo  $this->translate("Status") ?>	
	      </label>
        <select id="" name="status">
          <option value="0" ><?php echo $this->translate("Select") ?></option>
          <option value="1" <?php if( $this->status == 1) echo "selected";?> ><?php echo $this->translate("Only Open Listings") ?></option>
          <option value="2" <?php if( $this->status == 2) echo "selected";?> ><?php echo $this->translate("Only Close Listings") ?></option>
         </select>
      </div>

      <div>
	    	<label>
	      	<?php echo  $this->translate("Browse By") ?>	
	      </label>
        <select id="" name="listingbrowse">
          <option value="0" ><?php echo $this->translate("Select") ?></option>
          <option value="1" <?php if( $this->listingbrowse == 1) echo "selected";?> ><?php echo $this->translate("Most Viewed") ?></option>
          <option value="2" <?php if( $this->listingbrowse == 2) echo "selected";?> ><?php echo $this->translate("Most Recent") ?></option>
        </select>
      </div>

      <?php  $categories = $this->tableCategory->getCategories(null); ?>
      <?php if(count($categories) > 0) :?>
        <div>
          <label>
            <?php echo  $this->translate("Category") ?>
          </label>
          <select class="list_cat_select" id="" name="category_id" onchange="subcategories(this.value, '', '', '');">
            <option value=""></option>
            <?php if (count($categories) != 0) : ?>
							<?php $categories_prepared[0] = "";
                  foreach ($categories as $category) {
                    $categories_prepared[$category->category_id] = $category->category_name; ?>
                    <option value="<?php echo $category->category_id;?>" <?php if( $this->category_id == $category->category_id) echo "selected";?>><?php echo $this->translate($category->category_name);?></option>
              <?php } ?>
						<?php endif ; ?>
          </select>
        </div>

				<div id="subcategory_backgroundimage" class="cat_loader"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/List/externals/images/loading.gif" /></div>
				<div id="subcategory_id-label">
					<label>
							<?php echo  $this->translate("Subcategory") ?>	
					</label>
					
					<select class="list_cat_select" name="subcategory_id" id="subcategory_id" onchange="changesubcategory(this.value, '')"></select>
				</div>

				<div id="subsubcategory_backgroundimage" class="cat_loader"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/List/externals/images/loading.gif" /></div>
				<div id="subsubcategory_id-label">
					<label>
							<?php echo  $this->translate('3%s Level Category', "<sup>rd</sup>") ?>
					</label>
					<select class="list_cat_select" name="subsubcategory_id" id="subsubcategory_id"></select>
				</div>
      <?php endif;?>

      <div class="clear mtop_10">
        <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
      </div>
    </form>
  </div>
</div>
<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<div class='admin_members_results'>
  <?php $counter = $this->paginator->getTotalItemCount();?>
	<?php if(!empty($counter)): ?>
		<div class="">
			<?php  echo $this->translate(array('%s listing found.', '%s listings found.', $counter), $this->locale()->toNumber($counter)) ?>
		</div>
  <?php else:?>
		<div class="tip"><span>
			<?php  echo $this->translate("No results were found.") ?></span>
		</div>
  <?php endif; ?>
  <?php  echo $this->paginationControl($this->paginator); ?>
</div>
<br />

<?php if( $this->paginator->getTotalItemCount() > 0):?>
	<form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete'));?>" onSubmit="return multiDelete()">

		<table class='admin_table' width="100%">
			<thead>
				<tr>
					<th><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
					<th><a href="javascript:void(0);" onclick="javascript:changeOrder('listing_id', 'DESC');"><?php echo $this->translate('ID'); ?></a></th>
					<th align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Title'); ?></a></th>
					<th align="left" ><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate('Owner');?></a></th>
					<th class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'ASC');"><?php echo $this->translate('Featured'); ?></a></th>
					<th class="admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('sponsored', 'DESC');"><?php echo $this->translate('Sponsored'); ?></a></th>
					<th class="admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate('Creation Date'); ?></a></th>

					<th class="admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('view_count', 'DESC');"><?php echo $this->translate('Views'); ?></a></th>
					<th class="admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('comment_count', 'DESC');"><?php echo $this->translate('Comments'); ?></a></th>

					<th class="admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('like_count', 'DESC');"><?php echo $this->translate('Likes'); ?></a></th>

          <?php if ($approveDate): ?>
            <th align="center" class="admin_table_centered" title=<?php echo "Expired the listing" ?>><?php echo $this->translate('Expired'); ?>
            </th>
          <?php endif; ?>

					<th class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('approved', 'ASC');"><?php echo $this->translate('Approved'); ?></a></th>
					<th class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('closed', 'ASC');"><?php echo $this->translate('Status'); ?></a></th>
					<th class='admin_table_centered'><?php echo $this->translate('Options'); ?></th>
				</tr>
			</thead>

			<tbody>
				<?php if( count($this->paginator) ): ?>
					<?php foreach( $this->paginator as $item ): ?>
						<tr>

							<td><input name='delete_<?php echo $item->listing_id;?>' type='checkbox' class='checkbox' value="<?php echo $item->listing_id ?>"/></td>

							<td><?php echo $this->translate($item->listing_id) ?></td>

							<td class='admin_table_bold' style="white-space:normal;" title="<?php echo $this->translate($item->getTitle()) ?>">
								<a href="<?php echo $this->url(array('user_id' => $item->owner_id, 'listing_id' => $item->listing_id, 'slug' => $item->getSlug()), 'list_entry_view') ?>"  target='_blank'>
								<?php echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(),10)) ?></a>
							</td>

							<td class='admin_table_bold' title="<?php echo $item->getOwner()->getTitle() ?>"> <?php echo $this->htmlLink($item->getOwner()->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getOwner()->getTitle(),10)) ?></td>
													
						<?php if($item->featured == 1):?> 
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'list', 'controller' => 'admin', 'action' => 'featured', 'listing_id' => $item->listing_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/list_goldmedal1.gif', '', array('title' => $this->translate('Make Un-featured')))) ?></td>
							<?php else: ?>
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'list', 'controller' => 'admin', 'action' => 'featured', 'listing_id' => $item->listing_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/list_goldmedal0.gif', '', array('title' => $this->translate('Make Featured')))) ?></td>
							<?php endif; ?>

							<?php if($item->sponsored == 1):?>
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'list', 'controller' => 'admin', 'action' => 'sponsored', 'listing_id' => $item->listing_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/sponsored.png', '', array('title' => $this->translate('Make Unsponsored')))); ?></td>
							<?php else: ?>
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'list', 'controller' => 'admin', 'action' => 'sponsored', 'listing_id' => $item->listing_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/unsponsored.png', '', array('title' => $this->translate('Make Sponsored')))); ?>
							<?php endif; ?>

							<td align="center" class="admin_table_centered"><?php echo $this->translate(gmdate('M d,Y, g:i A',strtotime($item->creation_date))) ?></td>

							<td align="center" class="admin_table_centered"><?php echo $item->view_count ?></td>
							<td align="center" class="admin_table_centered"><?php echo $item->comment_count  ?></td>
							<td align="center" class="admin_table_centered"><?php echo $item->like_count  ?></td>

							<?php if ($approveDate): ?>
								<td align="center" class="admin_table_centered">
									<?php if ($approveDate > $item->approved_date): ?>
										<?php echo "Yes"?>
									<?php else: ?>
										<?php echo "No"?>
									<?php endif; ?>
								</td>
							<?php endif; ?>

							<?php if($item->approved == 1):?>
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'list', 'controller' => 'admin', 'action' => 'approved', 'listing_id' => $item->listing_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/list_approved1.gif', '', array('title' => $this->translate('Make Dis-Approved')))) ?></td>
							<?php else: ?>
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'list', 'controller' => 'admin', 'action' => 'approved', 'listing_id' => $item->listing_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/list_approved0.gif', '', array('title' => $this->translate('Make Approved')))) ?></td>
							<?php endif; ?>

							<?php if($item->closed == 0):?>
								<td align="center" class="admin_table_centered"> <?php echo $this->translate('Open') ?></td>
							<?php else: ?>
								<td align="center" class="admin_table_centered"> <?php echo $this->translate('Closed') ?></td>
							<?php endif; ?>
						
							<td class='admin_table_options'>
								<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'list', 'controller' => 'viewlist', 'action' => 'detail', 'id' => $item->listing_id), $this->translate('details'), array('class' => 'smoothbox')) ?> |
								<a href="<?php echo $this->url(array('user_id' => $item->owner_id, 'listing_id' => $item->listing_id, 'slug' => $item->getSlug()), 'list_entry_view') ?>"  target='_blank'><?php echo $this->translate('view'); ?></a>
								|
								<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'list', 'controller' => 'admin', 'action' => 'delete', 'listing_id' => $item->listing_id), $this->translate('delete'), array(
									'class' => 'smoothbox',
								)) ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<br />
		<div class='buttons'>
			<button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
		</div>
	</form>
<?php endif;?>

<script type="text/javascript">
	var subcategories = function(category_id, sub, subcatname, subsubcate)
	{
    if($('subcategory_backgroundimage'))
			$('subcategory_backgroundimage').style.display = 'block';
    if($('subcategory_id'))
			$('subcategory_id').style.display = 'none';
    if($('subcategory_id-label'))
			$('subcategory_id-label').style.display = 'none';
    if($('subsubcategory_id'))
			$('subsubcategory_id').style.display = 'none';
    if($('subsubcategory_id-label'))
			$('subsubcategory_id-label').style.display = 'none';
    changesubcategory(sub,subsubcate)
	  var url = '<?php echo $this->url(array('action' => 'sub-category'), 'list_general', true);?>';
		en4.core.request.send(new Request.JSON({      	
			 url : url,
			data : {
				format : 'json',
				category_id_temp : category_id
				
			},
			onSuccess : function(responseJSON) {
        if($('subcategory_backgroundimage'))
					$('subcategory_backgroundimage').style.display = 'none';				
				clear('subcategory_id');				
	    	var  subcatss = responseJSON.subcats;
	      addOption($('subcategory_id')," ", '0');
        for (i=0; i< subcatss.length; i++) {
         addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
           $('subcategory_id').value = sub;
        }				
				if(category_id == 0) {
					clear('subcategory_id');
          if($('subcategory_id'))
          $('subcategory_id').style.display = 'none';
          if($('subcategory_id-label'))
          $('subcategory_id-label').style.display = 'none';
          if($('subsubcategory_id'))
          $('subsubcategory_id').style.display = 'none';
          if($('subsubcategory_id-label'))
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

	var cat = '<?php echo $this->category_id ?>';
	if(cat != '') {
		var sub = '<?php echo $this->subcategory_id; ?>';
		var subcatname = "<?php echo $this->subcategory_name; ?>";
    var subsubcate = '<?php echo $this->subsubcategory_id; ?>';
		subcategories(cat, sub, subcatname,subsubcate);
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
    function changesubcategory(subcatid,subsubcate) {
      if($('buttons-wrapper')) {
		  	$('buttons-wrapper').style.display = 'none';
			}
      if(subcatid != 0)
      $('subsubcategory_backgroundimage').style.display = 'block';
      var url = '<?php echo $this->url(array('action' => 'subsub-category'), 'list_general', true);?>';
      var request = new Request.JSON({
        url : url,
        data : {
          format : 'json',
          subcategory_id_temp : subcatid
        },
        onSuccess : function(responseJSON) {
          $('subsubcategory_backgroundimage').style.display = 'none';
  	  		if($('buttons-wrapper')) {
				  	$('buttons-wrapper').style.display = 'block';
					}

          clear('subsubcategory_id');
          var  subsubcatss = responseJSON.subsubcats;

          addSubOption($('subsubcategory_id')," ", '0');
          for (i=0; i< subsubcatss.length; i++) {
            addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
            $('subsubcategory_id').value = subsubcate;
          }
        }
      });
      request.send();
    }
  if($('subcategory_id'))
  $('subcategory_id').style.display = 'none';
  if($('subcategory_id-label'))
	$('subcategory_id-label').style.display = 'none';
  if($('subsubcategory_id'))
	$('subsubcategory_id').style.display = 'none';
  if($('subsubcategory_id-label'))
	$('subsubcategory_id-label').style.display = 'none';
</script>