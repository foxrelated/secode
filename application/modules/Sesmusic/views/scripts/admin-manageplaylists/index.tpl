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
<?php include APPLICATION_PATH .  '/application/modules/Sesmusic/views/scripts/dismiss_message.tpl';?>
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
  
  function multiDelete() {
    return confirm("<?php echo $this->translate('Are you sure you want to delete the selected playlists?');?>");
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

<h3><?php echo $this->translate("Manage Playlists") ?></h3>
<p>
  <?php echo $this->translate('This page lists all of the playlists your users have created. You can use this page to monitor these playlists and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific playlist. Leaving the filter fields blank will show all the playlists on your social network. <br /> Below, you can also choose any number of playlists as Playlists of the Day. These playlists will be displyed randomly in the "Album / Song / Artist of the Day" widget.') ?>
</p>
<br />
<div class='admin_search sesbasic_search_form'>
  <?php echo $this->formFilter->render($this) ?>
</div>
<br />
<?php $counter = $this->paginator->getTotalItemCount(); ?> 
<?php if( count($this->paginator) ): ?>
  <div class="sesbasic_search_reasult">
    <?php echo $this->translate(array('%s playlist found.', '%s playlists found.', $counter), $this->locale()->toNumber($counter)) ?>
  </div>
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
    <table class='admin_table'>
      <thead>
        <tr>
          <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
          <th class='admin_table_short'><a href="javascript:void(0);" onclick="javascript:changeOrder('playlist_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate("Title") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('owner_id', 'ASC');"><?php echo $this->translate("Owner") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('song_count', 'ASC');"><?php echo $this->translate("Songs") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('view_count', 'ASC');"><?php echo $this->translate("Views") ?></a></th>          
          <th  align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'ASC');"><?php echo $this->translate("Featured") ?></a></th>
          <th  align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('offtheday', 'ASC');"><?php echo $this->translate("Of the Day") ?></a></th>
          <th><?php echo $this->translate("Date") ?></th>
          <th><?php echo $this->translate("Option") ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->playlist_id;?>' value="<?php echo $item->playlist_id; ?>" /></td>
          <td><?php echo $item->playlist_id ?></td>
          <td><?php echo $this->htmlLink($item->getHref(), $this->translate(Engine_Api::_()->sesbasic()->textTruncation($item->getTitle(),20)), array('title' => $this->translate($item->getTitle()), 'target' => '_blank')) ?></td> 
          <td><?php echo $this->htmlLink($item->getOwner()->getHref(), $this->translate(Engine_Api::_()->sesbasic()->textTruncation($item->getOwner()->getTitle(),20)), array('title' => $this->translate($item->getOwner()->getTitle()), 'target' => '_blank')) ?></td>
          <?php $soungCount = Engine_Api::_()->getDbtable('playlistsongs', 'sesmusic')->playlistSongsCount(array('playlist_id' => $item->playlist_id));  ?>
          <td><?php echo $soungCount ?></td>
          <td><?php echo  $item->view_count ?></td>
          <td class="admin_table_centered">
            <?php if($item->featured == 1):?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manageplaylists', 'action' => 'featured', 'id' => $item->playlist_id), $this->htmlImage($baseURL . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Unmark as Featured')))) ?>
            <?php else: ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manageplaylists', 'action' => 'featured', 'id' => $item->playlist_id), $this->htmlImage($baseURL . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Mark Featured')))) ?>
            <?php endif; ?>
          </td>
          <td class="admin_table_centered">
            <?php if($item->offtheday == 1):?>  
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manage', 'action' => 'oftheday', 'id' => $item->playlist_id, 'type' => 'sesmusic_playlist', 'param' => 0), $this->htmlImage($baseURL . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Edit Playlist of the Day'))), array('class' => 'smoothbox')); ?>
            <?php else: ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manage', 'action' => 'oftheday', 'id' => $item->playlist_id, 'type' => 'sesmusic_playlist', 'param' => 1), $this->htmlImage($baseURL . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Make Playlist of the Day'))), array('class' => 'smoothbox')) ?>
            <?php endif; ?>
          </td>
          <td><?php echo $this->translate($item->creation_date) ?></td>
          <td>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manageplaylists', 'action' => 'view', 'id' => $item->playlist_id), $this->translate("View Details"), array('class' => 'smoothbox')) ?>
            |
            <?php echo $this->htmlLink($item->getHref(), $this->translate("View"), array()); ?>
          |
          <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manageplaylists', 'action' => 'delete', 'id' => $item->playlist_id), $this->translate("Delete"), array('class' => 'smoothbox')); ?>
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
      <?php echo $this->translate("There are no playlists yet.") ?>
    </span>
  </div>
<?php endif; ?>