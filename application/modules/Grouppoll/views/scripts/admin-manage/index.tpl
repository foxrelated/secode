<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Groups - Polls Extension'); ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate('This page lists all the group polls posted by the users. Here, you can monitor group polls, delete them, make group polls approve / dis-approve them.');?>
</p>

<br />
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
		return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected group polls ?")) ?>');
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

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>
<br />
<div class='admin_members_results'>
  <div>
    <?php echo $this->translate(array('%s group poll found', '%s group polls found', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
  </div>
  <br />
  <?php echo $this->paginationControl($this->paginator); ?>
</div>
<br />

<form id='multidelete_form' method="post" action="<?php echo $this->url(array('action'=>'multi-delete'));?>" onSubmit="return multiDelete()">
  <table class='admin_table'>
    <thead>
      <tr>
        <th style='width: 1%;' align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
        <th style='width: 1%;' align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('poll_id', 'DESC');"><?php echo $this->translate('ID'); ?></a></th>
        <th style='width: 3%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Title'); ?></a></th>
        <th style='width: 2%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate('Owner');?></a></th>
				<th style='width: 1%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('group_title', 'ASC');"><?php echo $this->translate('Group Name');?></a></th>
        <th style='width: 1%;' align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('approved', 'ASC');"><?php echo $this->translate('Approved'); ?></a></th>
        <th style='width: 1%;' align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('views', 'ASC');"><?php echo $this->translate('Views'); ?></a></th>
         <th style='width: 1%;' align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('vote_count', 'ASC');"><?php echo $this->translate('Votes'); ?></a></th>
        <th style='width: 1%;' align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('comment_count', 'ASC');"><?php echo $this->translate('Comments'); ?></a></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate('Creation Date'); ?></a></th>
        <th style='width: 3%;' class='admin_table_options' align="left"><?php echo $this->translate('Options'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if( count($this->paginator) ): ?>
        <?php foreach( $this->paginator as $item ): ?>
          <tr>        
            <td><input name='delete_<?php echo $item->poll_id;?>' type='checkbox' class='checkbox' value="<?php echo $item->poll_id ?>"/></td>
            <td class="admin_table_centered"><?php echo $item->poll_id ?></td>
            <?php $grouppoll_title = strip_tags($item->title);
                   $grouppoll_title = Engine_String::strlen($grouppoll_title) > 15 ? Engine_String::substr($grouppoll_title, 0, 15) . '..' : $grouppoll_title;
            ?>
            <td class='admin_table_bold'><?php echo $this->htmlLink($item->getHref(), $grouppoll_title, array('title' => $item->title, 'target' => '_blank')) ?></td>
            
            <td class='admin_table_bold'><?php echo $this->htmlLink($this->item('user', $item->owner_id)->getHref()	, $item->truncateOwner($this->user($item->owner_id)->username), array('title' => $item->username, 'target' => '_blank')) ?></td>
            
						<td class='admin_table_bold'><?php echo $this->htmlLink($this->item('group', $item->group_id)->getHref(), $item->truncateGroupTitle($item->group_title), array('title' => $item->group_title, 'target' => '_blank')) ?></td>

           <?php if($item->approved == 1):?>
							<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'grouppoll', 'controller' => 'admin', 'action' => 'approved', 'poll_id' => $item->poll_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Grouppoll/externals/images/grouppoll_approved1.gif', '', array('title'=> $this->translate('Dis-approve group poll')))) ?> 
							</td>       
            <?php else: ?>  
							<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'grouppoll', 'controller' => 'admin', 'action' => 'approved', 'poll_id' => $item->poll_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Grouppoll/externals/images/grouppoll_approved0.gif', '', array('title'=> $this->translate('Approve group poll')))) ?>
							</td>
            <?php endif; ?>
            
            <td align="center" class="admin_table_centered"><?php echo $item->views ?></td>
            <td align="center" class="admin_table_centered"><?php echo $item->vote_count ?></td>
            <td align="center" class="admin_table_centered"><?php echo $item->comment_count ?></td>
            <td align="center"><?php echo $item->creation_date ?></td>
            
            <td class='admin_table_options' align="left">
							 <?php echo $this->htmlLink(array('route' => 'grouppoll_detail_view', 'user_id' => $item->owner_id, 'poll_id' => $item->poll_id), $this->translate('view'), array('target' => '_blank')) ?> 
              </a>
              |
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'grouppoll', 'controller' => 'admin', 'action' => 'delete', 'poll_id' => $item->poll_id), $this->translate('delete'), array(
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