<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">

    var currentOrder = '<?php echo $this->order ?>';
    var currentOrderDirection = '<?php echo $this->order_direction ?>';
    var changeOrder = function(order, default_direction) {
        if (order == currentOrder) {
            $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
        }
        else {
            $('order').value = order;
            $('order_direction').value = default_direction;
        }
        $('filter_form').submit();
    }

    function multiDelete()
    {
        return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected diaries ?")) ?>');
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

<h2>
    <?php echo $this->translate('Advanced Events Plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>
<h3><?php echo $this->translate('Manage Diaries'); ?></h3>
<p>
    <?php echo $this->translate('This page lists all the diaries your users have created. You can use this page to monitor these diaries and delete offensive ones if necessary. Entering criteria into the filter fields will help you find specific diary entries. Leaving the filter fields blank will show all the diaries on your social network.'); ?>
</p>

<br />

<div class="admin_search">
    <div class="search">
        <form method="post" class="global_form_box" action="">
            <input type="hidden" name="post_search" /> 
            <div>
                <label>
                    <?php echo $this->translate("Owner Name") ?>
                </label>
                <?php if (empty($this->user_name)): ?>
                    <input type="text" name="user_name" /> 
                <?php else: ?>
                    <input type="text" name="user_name" value="<?php echo $this->translate($this->user_name) ?>"/>
                <?php endif; ?>
            </div>

            <div>
                <label>
                    <?php echo $this->translate("Diary Name") ?>
                </label>	
                <?php if (empty($this->diary_name)): ?>
                    <input type="text" name="diary_name" /> 
                <?php else: ?> 
                    <input type="text" name="diary_name" value="<?php echo $this->translate($this->diary_name) ?>" />
                <?php endif; ?>
            </div>

            <div>
                <label>
                    <?php echo $this->translate("Event Name") ?>
                </label>	
                <?php if (empty($this->event_name)): ?>
                    <input type="text" name="event_name" /> 
                <?php else: ?> 
                    <input type="text" name="event_name" value="<?php echo $this->translate($this->event_name) ?>" />
                <?php endif; ?>
                <p class="siteevent_description"><?php echo $this->translate("Diaries having this Event."); ?></p>
            </div>

            <div class="buttons">
                <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
            </div>

        </form>
    </div>
</div>
<br />

<div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
</div>
<br />

<div class='admin_members_results'>
    <?php
    if (!empty($this->paginator)) {
        $counter = $this->paginator->getTotalItemCount();
    }
    if (!empty($counter)):
        ?>
        <div class="">
            <?php echo $this->translate(array('%s event diary found.', '%s event diaries found.', $counter), $this->locale()->toNumber($counter)) ?>
        </div>
    <?php else: ?>
        <div class="tip"><span>
                <?php echo $this->translate("No results were found.") ?></span>
        </div>
    <?php endif; ?> 
</div>
<br />
<?php if (!empty($counter)): ?>

    <form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete')); ?>" onSubmit="return multiDelete()">
        <table class='admin_table'>
            <thead>
                <tr>
                    <th class='admin_table_short' style='width: 1%;' ><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
                    <th style='width: 1%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('diary_id', 'DESC');"><?php echo $this->translate('ID'); ?></a></th>
                    <th style='width: 3%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Title'); ?></a></th>
                    <th style='width: 3%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate('Owner'); ?></a></th>
                    <th style='width: 5%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'ASC');"><?php echo $this->translate('Creation Date'); ?></a></th>  
                    <th style='width: 1%;' align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('total_item', 'DESC');"><?php echo $this->translate('Total Items'); ?></a></th>       
                    <th style='width: 3%;'><?php echo $this->translate("Options") ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->paginator as $item): ?>
                    <tr>
                        <td><input name='delete_<?php echo $item->diary_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->diary_id ?>"/></td>   
                        <td><?php echo $item->getIdentity() ?></td>
                        <td style="white-space: nowrap;"><?php echo $this->htmlLink($item->getHref(), $item->title, array('target' => '_blank')) ?></td> 
                        <td><?php echo $this->htmlLink($this->item('user', $item->owner_id)->getHref(), $this->user($item->owner_id)->getTitle(), array('title' => $this->user($item->owner_id)->getTitle(), 'target' => '_blank')) ?></td>     
                        <td><?php echo gmdate('M d,Y g:i A', strtotime($item->creation_date)) ?></td>
                        <td class="admin_table_centered"><?php echo $item->total_item; ?></td>
                        <td>
                            <?php echo $this->htmlLink($item->getHref(), $this->translate('View'), array('target' => '_blank')) ?> |
                            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteevent', 'controller' => 'diary', 'action' => 'delete', 'diary_id' => $item->getIdentity()), $this->translate("Delete"), array('class' => 'smoothbox')) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br />
        <div class='buttons'>
            <button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
        </div>
    </form>

    <br />
    <div>
        <?php
        echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues,
        ));
        ?>
    </div>
    <br />
<?php endif; ?>