<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: chanel.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php include APPLICATION_PATH .  '/application/modules/Sesvideo/views/scripts/dismiss_message.tpl';?>
<script type="text/javascript">
function multiDelete()
{
  return confirm("<?php echo $this->translate("Are you sure you want to delete the selected videos?") ?>");
}
function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>
<h3><?php echo $this->translate("Manage Channels") ?></h3>
<p>
	<?php echo $this->translate('This page lists all of the channels your users have created. You can use this page to monitor these channels and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific channel. Leaving the filter fields blank will show all the channels on your social network. <br /> Below, you can also choose any number of channels as Channels of the Day. These channels will be displayed randomly in the "Videos / Channels / Playlists of the Day" widget.'); ?>
</p>
<br />
<div class='admin_search sesbasic_search_form'>
  <?php echo $this->formFilter->render($this) ?>
</div>
<br />
<?php if( count($this->paginator) ): ?>
  <div class="sesbasic_search_reasult">
    <?php echo $this->translate(array('%s channel found.', '%s channels found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
  </div>
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
        <th class='admin_table_short'>ID</th>
        <th><?php echo $this->translate("Title") ?></th>
        <th><?php echo $this->translate("Owner") ?></th>
        <th align="center"><?php echo $this->translate("Total Videos") ?></th>
        <th align="center"><?php echo $this->translate('Featured') ?></th>
        <th align="center"><?php echo $this->translate('Sponsored') ?></th>
        <th align="center"><?php echo $this->translate('Hot') ?></th>
        <th align="center"><?php echo $this->translate('Verified') ?></th>
        <th><?php echo $this->translate("Of the Day") ?></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->chanel_id;?>' value='<?php echo $item->chanel_id ?>' /></td>
          <td><?php echo $item->getIdentity() ?></td>
          <td><?php echo  $this->htmlLink($item->getHref(), $item->getTitle()); ?></td>
          <td><?php echo $this->htmlLink($item->getHref(), $item->getOwner()); ?></td>
          <td class="admin_table_centered"><?php echo $item->countVideos(); ?></td>
          <td class="admin_table_centered"><?php echo $item->is_featured == 1 ?   $this->htmlLink(
                array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'admin-manage', 'action' => 'feature-sponsored', 'id' => $item->chanel_id,'status' =>0,'category' =>'featured','param'=>'chanels'),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Unmark as Featured')))) : $this->htmlLink(
                array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'admin-manage', 'action' => 'feature-sponsored', 'id' => $item->chanel_id,'status' =>1,'category' =>'featured','param'=>'chanels'),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Mark Featured')))) ; ?></td>
            <td class="admin_table_centered"><?php echo $item->is_sponsored == 1 ? $this->htmlLink(
                array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'admin-manage', 'action' => 'feature-sponsored', 'id' => $item->chanel_id,'status' =>0,'category' =>'sponsored','param'=>'chanels'),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Unmark as Sponsored')))) : $this->htmlLink(
                array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'admin-manage', 'action' => 'feature-sponsored', 'id' => $item->chanel_id,'status' =>1,'category' =>'sponsored','param'=>'chanels'),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Mark Sponsored'))))  ; ?></td>
             
              <td class="admin_table_centered"><?php echo $item->is_hot == 1 ? $this->htmlLink(
                array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'admin-manage', 'action' => 'hot', 'id' => $item->chanel_id,'status' =>0,'type'=>'chanel'),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Unmark as Hot')))) : $this->htmlLink(
                array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'admin-manage', 'action' => 'hot', 'id' => $item->chanel_id,'status' =>1,'type'=>'chanel'),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Mark Hot'))))  ; ?></td>   
             <td class="admin_table_centered"><?php echo $item->is_verified == 1 ? $this->htmlLink(
                array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'admin-manage', 'action' => 'verified', 'id' => $item->chanel_id,'status' =>0),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Click to unverified this chanel')))) : $this->htmlLink(
                array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'admin-manage', 'action' => 'verified', 'id' => $item->chanel_id,'status' =>1,),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Click to verified this chanel'))))  ; ?></td>
          <td class="admin_table_centered">
          		<?php if(strtotime($item->endtime) < strtotime(date('Y-m-d')) && $item->offtheday == 1){ 
            			Engine_Api::_()->getDbtable('chanels', 'sesvideo')->update(array(
                      'offtheday' => 0,
                      'starttime' =>'',
                      'endtime' =>'',
                    ), array(
                      "chanel_id = ?" => $item->chanel_id,
                    ));
                    $itemofftheday = 0;
             }else
             	$itemofftheday = $item->offtheday; ?>
            <?php if($itemofftheday == 1):?>  
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'admin-manage', 'action' => 'oftheday', 'id' => $item->chanel_id, 'type' => 'sesvideo_chanel', 'param' => 0), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Edit Channel of the Day'))), array('class' => 'smoothbox')); ?>
            <?php else: ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'admin-manage', 'action' => 'oftheday', 'id' => $item->chanel_id, 'type' => 'sesvideo_chanel', 'param' => 1), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Make Channel of the Day'))), array('class' => 'smoothbox')) ?>
            <?php endif; ?>
          </td>
          <td>
          	<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'admin-manage', 'action' => 'view', 'type'=> 'sesvideo_chanel', 'id' => $item->chanel_id), $this->translate("View Details"), array('class' => 'smoothbox')) ?>
            |
            <a href="<?php echo $item->getHref(); ?>"><?php echo $this->translate("View") ?></a>
            |
            <?php echo $this->htmlLink(
                array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'admin-manage', 'action' => 'delete-chanel', 'id' => $item->chanel_id),
                $this->translate("Delete"),
                array('class' => 'smoothbox')) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <br />

  <div class='buttons'>
    <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
  </div>
  </form>

  <br />

  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>

<?php else: ?>
  <br />
  <div class="tip">
    <span>
      <?php echo $this->translate("There are currently no channels posted by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>
<script>
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
          if ($('subcat_id')) {
            $('subcat_id').parentNode.style.display = "inline-block";
          }
          $('subcat_id').innerHTML = responseHTML;
        } else {
          if ($('subcat_id')) {
            $('subcat_id').parentNode.style.display = "none";
            $('subcat_id').innerHTML = '';
          }
					 if ($('subsubcat_id')) {
            $('subsubcat_id').parentNode.style.display = "none";
            $('subsubcat_id').innerHTML = '';
          }
        }
      }
    }).send(); 
  }
	function showSubSubCategory(cat_id,selectedId) {
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
          if ($('subsubcat_id')) {
            $('subsubcat_id').parentNode.style.display = "inline-block";
          }
          $('subsubcat_id').innerHTML = responseHTML;
					// get category id value 
        } else {
          if ($('subsubcat_id')) {
            $('subsubcat_id').parentNode.style.display = "none";
            $('subsubcat_id').innerHTML = '';
          }
        }
      }
    })).send();  
 }
	var sesdevelopment = 1;
	<?php if(isset($this->category_id) && $this->category_id != 0){ ?>
			<?php if(isset($this->subcat_id) && $this->subcat_id != 0){$catId = $this->subcat_id;}else $catId = ''; ?>
      showSubCategory('<?php echo $this->category_id ?>','<?php echo $catId; ?>');
   <?php  }else{?>
	  $('subcat_id').parentNode.style.display = "none";
	 <?php } ?>
	 <?php if(isset($this->subsubcat_id) && $this->subsubcat_id != 0){ ?>
      showSubSubCategory('<?php echo $this->subcat_id; ?>','<?php echo $this->subsubcat_id; ?>');
	 <?php }else{?>
	 		 $('subsubcat_id').parentNode.style.display = "none";
	 <?php } ?>
</script>