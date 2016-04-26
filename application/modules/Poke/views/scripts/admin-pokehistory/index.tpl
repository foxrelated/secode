<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: index.tpl 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
?>
<script type="text/javascript">
  en4.core.runonce.add(function(){$$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){ $$('input[type=checkbox]').set('checked', $(this).get('checked', false)); })});

  var delectSelected =function(){
    var checkboxes = $$('input[type=checkbox]');
    var selecteditems = [];

    checkboxes.each(function(item, index){
      var checked = item.get('checked', false);
      var value = item.get('value', false);
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });

    $('ids').value = selecteditems;
    $('delete_selected').submit();
  }
  
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
   
</script>
<h2><?php echo $this->translate('Pokes Plugin') ?></h2>
	<div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>

<p>
  <b><?php echo $this->translate("This page lists all the pokings done between members.") ?></b><br /><br />
</p> 

<div class="admin_search">
  <div class="search">
    <form method="post" class="global_form_box" action="">
      <div>
	      <label>
	      	<?php echo  $this->translate("Who Poked") ?>
	      </label>
	      <?php if( empty($this->sender)):?>
	      	<input type="text" name="sender" /> 
	      <?php else: ?>
	      	<input type="text" name="sender" value="<?php echo $this->translate($this->sender)?>"/>
	      <?php endif;?>
      </div>
      <div>
      	<label>
      		<?php echo  $this->translate("Who was Poked") ?>
      	</label>	
      	<?php if( empty($this->receiver)):?>
      		<input type="text" name="receiver" /> 
      	<?php else: ?> 
      		<input type="text" name="receiver" value="<?php echo $this->translate($this->receiver)?>" />
      	<?php endif;?>
      </div>

      <div>
	    	<label>
	      	<?php echo  $this->translate("Status") ?>	
	      </label>
        <select id="isexpire" name="isexpire">
       		<option value="0" ><?php echo $this->translate("Select") ?></option>
          <option value="1" <?php if( $this->isexpire == 1) echo "selected";?> ><?php echo $this->translate("Not hidden by users") ?></option>
          <option value="2" <?php if( $this->isexpire == 2) echo "selected";?> ><?php echo $this->translate("Hidden by users") ?></option>
        </select>
        
      </div>
      <div style="margin:10px 10px 0;">
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
  <?php $counter=$this->paginator->getTotalItemCount(); if(!empty($counter)): ?>
  <div class="">
    <?php  echo $this->translate(array('%s pokes found.', '%s pokes found.', $counter), $this->locale()->toNumber($counter)) ?>
  </div>
  
  <?php endif; ?>
  
  
</div><br />
<?php if( count($this->paginator) ): ?>
<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
     	<?php if($this->order_direction == 'ASC'):?>
        <th class='admin_table_short'><a href="javascript:void(0);" onclick="javascript:changeOrder('pokeuser_id', 'DESC');"><?php echo $this->translate('ID'); ?></a></th>
      <?php else: ?>
        <th class='admin_table_short'><a href="javascript:void(0);" onclick="javascript:changeOrder('pokeuser_id', 'ASC');"><?php echo $this->translate('ID'); ?></a></th>     
      <?php endif;?>
      <?php if($this->order_direction == 'ASC'):?>
        <th align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('SENDER.displayname', 'ASC');"><?php echo $this->translate('Who Poked'); ?></a></th>
      <?php else: ?>
         <th align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('SENDER.displayname', 'DESC');"><?php echo $this->translate('Who Poked'); ?></a></th>     
      <?php endif;?>
      <?php if($this->order_direction == 'ASC'):?>
        <th align="left" ><a href="javascript:void(0);" onclick="javascript:changeOrder('RECEIVER.displayname', 'ASC');"><?php echo $this->translate('Who was Poked');?></a></th>
      <?php else: ?>
         <th align="left" ><a href="javascript:void(0);" onclick="javascript:changeOrder('RECEIVER.displayname', 'DESC');"><?php echo $this->translate('Who was Poked');?></a></th>       
      <?php endif;?>
      <?php if($this->order_direction == 'ASC'):?>
        <th align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('isexpire', 'ASC');"><?php echo $this->translate('Status'); ?></a></th>
      <?php else: ?>
        <th align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('isexpire', 'DESC');"><?php echo $this->translate('Status'); ?></a></th>
      <?php endif;?>
      <?php if($this->order_direction == 'ASC'):?>
        <th align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('created', 'ASC');"><?php echo $this->translate('Poking Date'); ?></a></th>
      <?php else: ?>
       <th align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('created', 'DESC');"><?php echo $this->translate('Poking Date'); ?></a></th>
      <?php endif;?>
    	<th style="text-align:left;"><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
	<tbody>
		<?php foreach( $this->paginator as $item): ?>
	    <tr> 
        <td><input type='checkbox' class='checkbox' value="<?php echo $item->pokeuser_id  ?>"/></td>
        <td><?php echo $item->pokeuser_id  ?></td>
        <td class='admin_table_bold' title="<?php echo $item->sname;?>">
          <a href= "<?php echo $this->baseUrl().'/profile/'. $item->susername;?>" target="_blank"><?php echo  Engine_Api::_()->poke()->turncation($item->sname, Engine_Api::_()->getApi('settings', 'core')->poke_title_turncation);?> </a>
        </td>
				<td class='admin_table_bold' title="<?php echo $item->rname;?>"><a href= "<?php echo $this->baseUrl().'/profile/'.$item->rusername;?>" target="_blank"><?php echo  Engine_Api::_()->poke()->turncation($item->rname, Engine_Api::_()->getApi('settings', 'core')->poke_title_turncation);?> </a></td>
        <td><?php if($item->isexpire == 1): echo "Shown"; elseif($item->isexpire == 2):  echo "Hidden"; endif;  ?></td>
				<td><?php echo date("F j, Y, g:i a",$item->created)  ?></td>
				<td><?php echo $this->htmlLink(array('route' => 'default', 'module' => 'poke', 'controller' => 'admin-pokehistory', 'action' => 'delete', 'id' => $item->pokeuser_id),$this->translate("delete"),array('class' => 'smoothbox')) ?></td>
       </tr>    
    <?php endforeach; ?>       
	</tbody>
</table><br />             

<div class='buttons'>
  <button onclick="javascript:delectSelected();" type='submit'>
    <?php echo $this->translate("Delete Selected") ?>
  </button>
</div>  

<form id='delete_selected' method='post' action='<?php echo $this->url(array('action' =>'deleteselected')) ?>'>
  <input type="hidden" id="ids" name="ids" value=""/>
</form>
<br/>
<br/>
<div>
  <?php echo $this->paginationControl($this->paginator); ?>
</div>
<?php else: ?>
  <div class="tip">
    <span>
    <?php if($this->searchFlag):?>
    
     <?php echo $this->translate("No poke entries were found matching the search criteria.") ?>
      <?php else:?>
       <?php echo $this->translate("No pokes have been done by your members yet.") ?>
       <?php endif; ?>
    </span>
  </div>
<?php endif; ?>

