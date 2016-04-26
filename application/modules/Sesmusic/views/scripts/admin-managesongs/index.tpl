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
  
  function multiDelete() {
    return confirm("<?php echo $this->translate('Are you sure you want to delete the selected songs?');?>");
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
<h3><?php echo $this->translate("Manage Songs") ?></h3>
<p>
  <?php echo $this->translate('This page lists all of the songs your users have uploaded. You can use this page to monitor these songs and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific song. Leaving the filter fields blank will show all the songs on your social network. <br /> Below, you can also choose any number of songs as Songs of the Day. These songs will be displyed randomly in the "Album / Song / Artist of the Day" widget.') ?>
</p>
<br />
<div class='admin_search sesbasic_search_form'>
  <?php echo $this->formFilter->render($this) ?>
</div>
<br />
<?php $counter = $this->paginator->getTotalItemCount(); ?> 
<?php if( count($this->paginator) ): ?>
  <div class="sesbasic_search_reasult">
    <?php echo $this->translate(array('%s song found.', '%s songs found.', $counter), $this->locale()->toNumber($counter)) ?>
  </div>
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
    <table class='admin_table'>
      <thead>
        <tr>
          <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
          <th class='admin_table_short'><a href="javascript:void(0);" onclick="javascript:changeOrder('albumsong_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate("Title") ?></a></th>
          <th><?php echo $this->translate("Album") ?></th>
          <th><?php echo $this->translate("Owner") ?></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('play_count', 'ASC');"><?php echo $this->translate("Plays") ?></a></th>
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
        <?php $album = Engine_Api::_()->getItem('sesmusic_album', $item->album_id); ?>
        <tr>
          <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->albumsong_id;?>' value="<?php echo $item->albumsong_id; ?>" /></td>
          <td><?php echo $item->albumsong_id ?></td>
          <td><?php echo $this->htmlLink($item->getHref(), $this->translate(Engine_Api::_()->sesbasic()->textTruncation($item->getTitle(),10)), array('title' => $this->translate($item->getTitle()), 'target' => '_blank')) ?></td> 
          <td><?php echo $this->htmlLink($album->getHref(), $this->translate(Engine_Api::_()->sesbasic()->textTruncation($album->getTitle(),10)), array('title' => $this->translate($album->getTitle()), 'target' => '_blank')) ?></td> 
          <td><?php echo $this->htmlLink($item->getOwner()->getHref(), $this->translate(Engine_Api::_()->sesbasic()->textTruncation($item->getOwner()->getTitle(),10)), array('title' => $this->translate($item->getOwner()->getTitle()), 'target' => '_blank')) ?></td>
          <td><?php echo  $item->play_count ?></td>
          <td class="admin_table_centered">
            <?php if($item->featured == 1):?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-managesongs', 'action' => 'featured', 'id' => $item->albumsong_id), $this->htmlImage($baseURL . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Unmark as Featured')))) ?>
            <?php else: ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-managesongs', 'action' => 'featured', 'id' => $item->albumsong_id), $this->htmlImage($baseURL . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Mark Featured')))) ?>
            <?php endif; ?>
          </td>
          <td class="admin_table_centered">
            <?php if($item->sponsored == 1):?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-managesongs', 'action' => 'sponsored', 'id' => $item->albumsong_id), $this->htmlImage($baseURL . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Unmark as Sponsored')))) ?>
            <?php else: ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-managesongs', 'action' => 'sponsored', 'id' => $item->albumsong_id), $this->htmlImage($baseURL . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Mark Sponsored')))) ?>
            <?php endif; ?>
          </td>
          <td class="admin_table_centered">
            <?php if($item->hot == 1):?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-managesongs', 'action' => 'hot', 'id' => $item->albumsong_id), $this->htmlImage($baseURL . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Unmark as Hot')))) ?>
            <?php else: ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-managesongs', 'action' => 'hot', 'id' => $item->albumsong_id), $this->htmlImage($baseURL . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Mark Hot')))) ?>
            <?php endif; ?>
          </td>
          <td class="admin_table_centered">
            <?php if($item->upcoming == 1):?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-managesongs', 'action' => 'upcoming', 'id' => $item->albumsong_id), $this->htmlImage($baseURL . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Unmark as Latest')))) ?>
            <?php else: ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-managesongs', 'action' => 'upcoming', 'id' => $item->albumsong_id), $this->htmlImage($baseURL . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Mark Latest')))) ?>
            <?php endif; ?>
          </td>
          <td class="admin_table_centered">
            <?php if($item->offtheday == 1):?>  
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manage', 'action' => 'oftheday', 'id' => $item->albumsong_id, 'type' => 'sesmusic_albumsong', 'param' => 0), $this->htmlImage($baseURL . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Edit Song of the Day'))), array('class' => 'smoothbox')); ?>
            <?php else: ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-manage', 'action' => 'oftheday', 'id' => $item->albumsong_id, 'type' => 'sesmusic_albumsong', 'param' => 1), $this->htmlImage($baseURL . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Make Song of the Day'))), array('class' => 'smoothbox')) ?>
            <?php endif; ?>
          </td>
          <td>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-managesongs', 'action' => 'view', 'id' => $item->albumsong_id), $this->translate("View Details"), array('class' => 'smoothbox')) ?>
            |
            <?php echo $this->htmlLink($item->getHref(), $this->translate("View"), array('class' => '')); ?>
            |
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'admin-managesongs', 'action' => 'delete', 'id' => $item->albumsong_id), $this->translate("Delete"), array('class' => 'smoothbox')) ?>
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