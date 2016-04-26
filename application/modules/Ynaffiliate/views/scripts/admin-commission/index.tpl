<style type="text/css">
    .ynaff_thead > tr > th{
        font-size: 8pt;
        text-align: left;

    }
    div select {
        margin-top: 0px!important;
    }
</style>

<script type="text/javascript">
    en4.core.runonce.add(function(){
        $$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){
            var checked = $(this).checked;
            var checkboxes = $$('td.checkbox input[type=checkbox]');
            checkboxes.each(function(item){
                item.checked = checked;
            });
        })
    });

    function actionSelected(actionType){
        var checkboxes = $$('td.checkbox input[type=checkbox]');
        var selecteditems = [];

        checkboxes.each(function(item){
            var checked = item.checked;
            var value = item.value;
            if (checked == true && value != 'on'){
                selecteditems.push(value);
            }
        });
        $('action_selected').action = en4.core.baseUrl +'admin/ynaffiliate/commission/' + actionType + '-selected';
        $('ids').value = selecteditems;
        $('action_selected').submit();
    }
</script>


<h2><?php echo $this->translate("Affiliate Plugin") ?></h2>

<?php echo $this->content()->renderWidget('ynaffiliate.admin-main-menu') ?>

<p>
    <?php echo $this->translate("YNAFFILIATE_VIEWS_SCRIPTS_ADMINMANAGE_COMMISSION_DESCRIPTION") ?>
</p>
<br />


<?php 
$baseCurrency = Engine_Api::_()->getApi('settings', 'core')->payment['currency'];

echo $this->translate('Current Currency: ');?>
<?php echo $baseCurrency; ?>
<br />
<?php 
$points_convert_rate = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.pointrate', 1);
echo $this->translate('Points conversion rate: 1 %1$s = %2$s point(s)', $baseCurrency, $points_convert_rate);?>

<br />
<br />
<div class='admin_search'>
    <?php echo $this->form->render($this); ?>
</div>

<br/>
<br/>
<?php
if (count($this->paginator) > 0):
$baseCurrency = Engine_Api::_()->getApi('settings', 'core')->payment['currency'];
?>
<div class="admin_table_form">
    <table class='admin_table'>
        <thead class="ynaff_thead">
        <tr>
            <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
            <th><?php echo $this->translate("Client Name") ?></th>
            <th><?php echo $this->translate("Affiliate Name") ?></th>
            <th><?php echo $this->translate("Purchased Type") ?></th>
            <th><?php echo $this->translate("Purchased Date") ?></th>
            <th><?php echo $this->translate("Purchased Currency") ?></th>
            <th><?php echo $this->translate("Purchased Amount") ?></th>
            <th><?php echo $this->translate("Commission Rate") ?></th>
            <th><?php echo $this->translate("Commission Amount") ?></th>
            <th><?php echo $this->translate("Points") ?></th>
            <th><?php echo $this->translate("Status") ?></th>
            <th><?php echo $this->translate("Reason") ?></th>
            <th><?php echo $this->translate("Actions") ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($this->paginator as $item): ?>
        <tr>
            <!--   <td><input type='checkbox' class='checkbox' value=""/></td>-->
            <td class="checkbox"><input type='checkbox' class='checkbox' value="<?php echo $item->commission_id ?>"/></td>
            <td><?php echo Engine_Api::_()->getItem('user', $item->from_user_id); ?></td>
            <td><?php echo Engine_Api::_()->getItem('user', $item->user_id); ?></td>
            <td><?php
            $Rules = new Ynaffiliate_Model_DbTable_Rules;
            $rules = $Rules->getRuleById($item->rule_id);
                echo $this->translate($rules->rule_title);
                ?>
            </td>
            <td><?php echo $this->locale()->toDateTime($item->purchase_date); ?></td>
            <td><?php echo $item->purchase_currency; ?></td>
            <td><?php echo round($item->purchase_total_amount, 2); ?></td>
            <td><?php
            if ($item->commission_type == 0) {
                echo $item->commission_rate . '%';
                } else {
                echo $item->commission_rate . ' ' . $item->purchase_currency;
                }
                ?>
            </td>
            <td><?php echo round($item->commission_amount, 2); ?></td>
            <td><?php echo round($item->commission_points, 2); ?></td>
            <td><?php echo $this->translate(ucfirst($item->approve_stat)); ?></td>
            <td><?php echo $item->reason; ?></td>
            <td><?php
                if ($item->purchase_currency != $baseCurrency && $item->commission_points == 0) :
                echo $this->translate('Currency Pair not exists!');
                else :
                if ($item->approve_stat == 'waiting') :
                ?>
                <a href="<?php echo $this->url(array('action' => 'accept', 'id' => $item->commission_id)) ?>" class="smoothbox"><?php echo $this->translate('Accept ') ?></a>|
                <a href="<?php echo $this->url(array('action' => 'deny', 'id' => $item->commission_id)) ?>" class="smoothbox"><?php echo $this->translate(' Deny') ?></a>
                <?php else :
                    if ($item->approve_stat == 'delaying') : ?>
                        <a href="<?php echo $this->url(array('action' => 'reject', 'id' => $item->commission_id)) ?>" class="smoothbox"><?php echo $this->translate('Reject') ?></a>
                    <?php else :
                         echo $this->translate('N/A');
                    endif;
                endif;
                endif;
                ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<br />
<!-- Page Changes  -->
<div>
    <?php
      echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => false,
    'query' => $this->formValues,
    ));
    ?>
</div>

<div class='buttons'>
    <button onclick="javascript:actionSelected('accept');" type='button'>
        <?php echo $this->translate("Accept Selected") ?>
    </button>

    <button onclick="javascript:actionSelected('deny');" type='button'>
        <?php echo $this->translate("Deny Selected") ?>
    </button>
</div>

<form id='action_selected' method='post' action=''>
    <input type="hidden" id="ids" name="ids" value=""/>
</form>

<br/>

<?php else: ?>
<div class="tip">
      <span>
   <?php echo $this->translate("There are no commission yet.") ?>
      </span>
</div>
<?php endif; ?>

<style type="text/css">

    .admin_search {
        max-width: 100% !important;
    }
</style>
<style type="text/css">
    table.admin_table thead tr th {
        white-space: normal !important;
    }
    .tabs > ul > li {
        display: block;
        float: left;
        margin: 2px;
        padding: 5px;
    }
    .tabs > ul {
        display: table;
        height: 65px;
    }
    .tabs > ul > li > a{
        white-space:nowrap!important;
    }
    .search div label{
        margin-bottom: 5px;
    }
    .search form > div{
        margin-left: 0px;
        margin-right: 10px;
        margin-bottom: 10px;
    }
    .f1 button[type="submit"]{
        margin-top: 17px;
    }
</style>
