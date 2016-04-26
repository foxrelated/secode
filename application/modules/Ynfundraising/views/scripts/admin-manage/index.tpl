<?php
/*
 * Display campaign information
 */
?>
<link type="text/css" href="application/modules/Ynfundraising/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
<script src="application/modules/Ynfundraising/externals/scripts/jquery-1.7.1.min.js"></script>
<script src="application/modules/Ynfundraising/externals/scripts/jquery-ui-1.8.17.custom.min.js"></script>

<script type="text/javascript">
    jQuery.noConflict();
    jQuery(document).ready(function(){
        // Datepicker
        jQuery('#start_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Ynfundraising/externals/images/calendar.png',
            buttonImageOnly: true
        });
        jQuery('#end_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Ynfundraising/externals/images/calendar.png',
            buttonImageOnly: true
        });

    });


</script>

<?php
echo $this->partial('_menu_admin.tpl', array('tab_select' => 'ynfundraising_admin_main_manage'));
?>

<script type="text/javascript">
  en4.core.runonce.add(function(){
		$$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){
			var checked = $(this).checked;
			var checkboxes = $$('td.ynfundraising_check input[type=checkbox]');
			checkboxes.each(function(item){
				item.checked = checked;
			});
		})
  });

  function actionSelected(actionType){
    var checkboxes = $$('td.ynfundraising_check input[type=checkbox]');
    var selecteditems = [];

    checkboxes.each(function(item){
      var checked = item.checked;
      var value = item.value;
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });
    $('action_selected').action = en4.core.baseUrl +'admin/ynfundraising/manage/' + actionType + '-selected';
    $('ids').value = selecteditems;
    $('action_selected').submit();
  }

  function campaign_feature(campaign_id){
            var element = document.getElementById('ynfundraising_content_'+campaign_id);
            var checkbox = document.getElementById('featurecampaign_'+campaign_id);
            var status = 0;

            if(checkbox.checked==true) status = 1;
            else status = 0;
            var content = element.innerHTML;
            new Request.JSON({
              'format': 'json',
              'url' : '<?php echo $this->url(array('module' => 'ynfundraising', 'controller' => 'manage', 'action' => 'feature'), 'admin_default') ?>',
              'data' : {
                'format' : 'json',
                'campaign_id' : campaign_id,
                'status' : status
              },
              'onRequest' : function(){
                  element.innerHTML= "<img style='margin-top:4px;' src='application/modules/Ynfundraising/externals/images/loading.gif'></img>";
              },
              'onSuccess' : function(responseJSON, responseText)
              {
                element.innerHTML = content;
                checkbox = document.getElementById('featurecampaign_'+campaign_id);
                if( status == 1) checkbox.checked=true;
                else checkbox.checked=false;
              }
            }).send();

    }
    function campaign_activated(campaign_id){
            var element = document.getElementById('ynfundraising_activated_'+campaign_id);
            var checkbox = document.getElementById('activatedcampaign_'+campaign_id);
            var status = 0;

            if(checkbox.checked==true) status = 1;
            else status = 0;
            var content = element.innerHTML;
            new Request.JSON({
              'format': 'json',
              'url' : '<?php echo $this->url(array('module' => 'ynfundraising', 'controller' => 'manage', 'action' => 'activated'), 'admin_default') ?>',
              'data' : {
                'format' : 'json',
                'campaign_id' : campaign_id,
                'status' : status
              },
              'onRequest' : function(){
                  element.innerHTML= "<img style='margin-top:4px;' src='application/modules/Ynfundraising/externals/images/loading.gif'></img>";
              },
              'onSuccess' : function(responseJSON, responseText)
              {
                element.innerHTML = content;
                checkbox = document.getElementById('activatedcampaign_'+campaign_id);
                if( status == 1) checkbox.checked=true;
                else checkbox.checked=false;
              }
            }).send();

    }

   function changeOrder(listby, default_direction){
    var currentOrder = '<?php echo $this->formValues['orderby'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
      // Just change direction
      if( listby == currentOrder ) {
        $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
      } else {
        $('orderby').value = listby;
        $('direction').value = default_direction;
      }
      $('filter_form').submit();
    }
</script>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("YNFUNDRAISING_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>
<br/>

<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>

<br />
<?php if( count($this->paginator) ): ?>
<div style="overflow: auto">
<table class='admin_table'>
  <thead>
    <tr>
      <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('campaign_id', 'DESC')"><?php echo $this->translate("ID") ?></a></th>
      <th>
      <a href="javascript:void(0);" onclick = "javascript:changeOrder('title', 'ASC')"><?php echo $this->translate("Title") ?></a></th>
      <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('owner_title', 'DESC')"><?php echo $this->translate("Owner") ?></a></th>
      <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('is_featured', 'DESC')"><?php echo $this->translate("Featured") ?></a></th>
      <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('activated', 'DESC')"><?php echo $this->translate("Activated") ?></a></th>
      <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('creation_date', 'DESC')"><?php echo $this->translate("Creation Date") ?></a></th>
      <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('parent_type', 'DESC')"><?php echo $this->translate("Type") ?></a></th>
      <th><?php echo $this->translate("Trophy/Idea") ?></th>
      <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('status', 'DESC')"><?php echo $this->translate("Status") ?></a></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td><?php echo $item->getIdentity() ?></td>
        <td><?php echo $item->getTitle() ?></td>
        <td><?php echo $item->getOwner()->getTitle() ?></td>
        <td>
          <div id='ynfundraising_content_<?php echo $item->campaign_id; ?>' style ="text-align: center;" >
              <?php if($item->is_featured): ?>
                <input type="checkbox" id='featurecampaign_<?php echo $item->getIdentity(); ?>' onclick="campaign_feature(<?php echo $item->getIdentity(); ?>,this)" checked />
              <?php else: ?>
               <input type="checkbox" id='featurecampaign_<?php echo $item->getIdentity(); ?>' onclick="campaign_feature(<?php echo $item->getIdentity(); ?>,this)" />
              <?php endif; ?>
          </div>
        </td>
        <td>
          <div id='ynfundraising_activated_<?php echo $item->campaign_id; ?>' style ="text-align: center;" >
              <?php if($item->activated): ?>
                <input type="checkbox" id='activatedcampaign_<?php echo $item->getIdentity(); ?>' onclick="campaign_activated(<?php echo $item->getIdentity(); ?>,this)" checked />
              <?php else: ?>
               <input type="checkbox" id='activatedcampaign_<?php echo $item->getIdentity(); ?>' onclick="campaign_activated(<?php echo $item->getIdentity(); ?>,this)" />
              <?php endif; ?>
          </div>
        </td>
        <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
        <td><?php echo $this->translate(ucfirst($item->parent_type)) ?></td>
        <td>
        <?php
        if ($item->parent_type != 'user') {
			$object_item = Engine_Api::_()->getApi('core', 'ynfundraising')->getItemFromType($item);
			if($object_item) {
				$title = $object_item->getTitle();
				echo $this->htmlLink($object_item->getHref(), Engine_Api::_()->ynfundraising()->shortenTitle($object_item->getTitle()), array('title'=>$object_item->getTitle()));
			}
			else {
					echo $this->translate("Deleted");
				}
		}
		else {
			echo $this->translate("None");
		}
        ?>
        </td>
        <td>
        <?php
		echo $this->translate(ucfirst($item->status));
        ?>
        </td>
        <td>
          <?php echo $this->htmlLink($item->getHref(), $this->translate('View')) ?>
          |
          <?php echo $this->htmlLink(
                  array('route' => 'ynfundraising_extended', 'controller' => 'campaign', 'action' => 'view-statistics-chart', 'campaign_id' => $item->getIdentity()),
                  $this->translate('Statistics'),
                  array('class' => ''))
          ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<br />

<div class='buttons'>

</div>

<form id='action_selected' method='post' action=''>
  <input type="hidden" id="ids" name="ids" value=""/>
</form>

<br/>
<div>
   <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>
</div>
<?php elseif($this->formValues['title'] ||
		$this->formValues['start_date'] ||
		$this->formValues['end_date'] ||
		$this->formValues['type'] ||
		$this->formValues['status'] ||
		$this->formValues['featured'] ):
?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any campaigns that match your search criteria.');?>
      </span>
    </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There is no campaign.") ?>
    </span>
  </div>
<?php endif; ?>
