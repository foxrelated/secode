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
<?php $baseURL = $this->layout()->staticBaseUrl; ?>
<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){
    // Just change direction
    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }
  
  
  
  
if($('category_id')) {

  window.addEvent('domready', function() {
    if ($('category_id').value == 0) {
      if($('subcat_id-wrapper'))
       $('subcat_id-wrapper').style.display = "none";
      if($('subsubcat_id-wrapper'))
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

    if($('subcat_id')) {
      var e = document.getElementById("subcat_id").length; 
      if (e == 1 && search != 1) {
        $('subcat_id-wrapper').style.display = "none";
      }
    }

    if($('subcat_id')) {
      var e = document.getElementById("subsubcat_id").length;
      if (e == 1 && search != 1) {
        $('subsubcat_id-wrapper').style.display = "none";
      }
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

</script>

<script type="text/javascript">

  function multiDelete() {
    return confirm("<?php echo $this->translate('Are you sure you want to delete the selected albums?');?>");
  }

  function selectAll() {
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

<?php include APPLICATION_PATH .  '/application/modules/Sesmusic/views/scripts/dismiss_message.tpl';?>

<h3><?php echo $this->translate("Manage Music Albums") ?></h3>
<p><?php echo $this->translate('This page lists all of the music albums your users have created. You can use this page to monitor these music albums and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific music album. Leaving the filter fields blank will show all the music albums on your social network. <br /> Below, you can also choose any number of music albums as Music Albums of the Day. These music albums will be displyed randomly in the "Album / Song / Artist of the Day" widget.'); ?></p>
<br />

<div class='admin_search sesbasic_search_form'>
  <?php echo $this->formFilter->render($this) ?>
</div>
<br />

<?php $counter = $this->paginator->getTotalItemCount(); ?> 
<?php if( count($this->paginator) ): ?>
  <div class="sesbasic_search_reasult">
    <?php echo $this->translate(array('%s music album found.', '%s music albums found.', $counter), $this->locale()->toNumber($counter)) ?>
  </div>
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
    <table class='admin_table'>
      <thead>
        <tr>
          <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
          <th class='admin_table_short'><a href="javascript:void(0);" onclick="javascript:changeOrder('album_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate("Title") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('owner_id', 'ASC');"><?php echo $this->translate("Owner") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('song_count', 'ASC');"><?php echo $this->translate("Songs") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'ASC');"><?php echo $this->translate("Featured") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('sponsored', 'ASC');"><?php echo $this->translate("Sponsored") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('hot', 'ASC');"><?php echo $this->translate("Hot") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('upcoming', 'ASC');"><?php echo $this->translate("Latest") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('offtheday', 'ASC');"><?php echo $this->translate("Of the Day") ?></a></th>
          <th><?php echo $this->translate("Options") ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->album_id;?>' value="<?php echo $item->album_id; ?>" /></td>
          <td><?php echo $item->album_id ?></td>
          <td><?php echo $this->htmlLink($item->getHref(), $this->translate(Engine_Api::_()->sesbasic()->textTruncation($item->getTitle(),16)), array('title' => $item->getTitle(), 'target' => '_blank')) ?></td>
          <td><?php echo $this->htmlLink($item->getOwner()->getHref(), $this->translate(Engine_Api::_()->sesbasic()->textTruncation($item->getOwner()->getTitle(),16)), array('title' => $this->translate($item->getOwner()->getTitle()), 'target' => '_blank')) ?></td>
          <td><?php echo  $item->song_count ?></td>
          <td class="admin_table_centered">
            <?php if($item->featured == 1):?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manage', 'action' => 'featured', 'id' => $item->album_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Unmark as Featured')))) ?>
            <?php else: ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manage', 'action' => 'featured', 'id' => $item->album_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Mark Featured')))) ?>
            <?php endif; ?>
          </td>
          <td class="admin_table_centered">
            <?php if($item->sponsored == 1):?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manage', 'action' => 'sponsored', 'id' => $item->album_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Unmark as Sponsored')))) ?>
            <?php else: ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manage', 'action' => 'sponsored', 'id' => $item->album_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Mark Sponsored')))) ?>
            <?php endif; ?>          
          </td>
          <td class="admin_table_centered">
            <?php if($item->hot == 1):?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manage', 'action' => 'hot', 'id' => $item->album_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Unmark as Hot')))) ?>
            <?php else: ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manage', 'action' => 'hot', 'id' => $item->album_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Mark Hot')))) ?>
          <?php endif; ?>
          </td>
          <td class="admin_table_centered">
            <?php if($item->upcoming == 1):?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manage', 'action' => 'upcoming', 'id' => $item->album_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Unmark as Latest')))) ?>
            <?php else: ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manage', 'action' => 'upcoming', 'id' => $item->album_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Mark Latest')))) ?>
            <?php endif; ?>
          </td>
          <td class="admin_table_centered">
            <?php if($item->offtheday == 1):?>  
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manage', 'action' => 'oftheday', 'id' => $item->album_id, 'type' => 'sesmusic_album', 'param' => 0), $this->htmlImage($baseURL . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Edit Music Album of the Day'))), array('class' => 'smoothbox')); ?>
            <?php else: ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manage', 'action' => 'oftheday', 'id' => $item->album_id, 'type' => 'sesmusic_album', 'param' => 1), $this->htmlImage($baseURL . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Make Music Album of the Day'))), array('class' => 'smoothbox')) ?>
            <?php endif; ?>
          </td>
          <td>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manage', 'action' => 'view', 'id' => $item->album_id), $this->translate("View Details"), array('class' => 'smoothbox')) ?>
            |
            <?php echo $this->htmlLink($item->getHref(), $this->translate("View"), array('class' => '')); ?>
            |
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manage', 'action' => 'delete', 'id' => $item->album_id), $this->translate("Delete"), array('class' => 'smoothbox')) ?>
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
  <br/>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no music albums created by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>