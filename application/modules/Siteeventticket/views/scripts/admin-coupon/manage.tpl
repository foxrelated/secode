<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="fleft"><?php echo 'Advanced Events Plugin'; ?></h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<?php if (count($this->navigationGeneral)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
    </div>
<?php endif; ?>

<h3>
    <?php echo 'Manage Event Coupons'; ?>
</h3>

<p>
    <?php echo 'Here, you can see all the Event coupons added by event owners. You can use this page to monitor these coupons and delete offensive ones if necessary. Here, you can also make coupons approved/dis-approved.'; ?>
</p>

<br />

<script type="text/javascript">
    var currentOrder = '<?php echo $this->order ?>';
    var currentOrderDirection = '<?php echo $this->order_direction ?>';
    var changeOrder = function (order, default_direction) {
        if (order == currentOrder) {
            $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
        } else {
            $('order').value = order;
            $('order_direction').value = default_direction;
        }

        $('filter_form').submit();
    }

    function multiDelete()
    {
        return confirm('<?php echo $this->string()->escapeJavascript("Are you sure you want to delete selected event coupons ?") ?>');
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

<div class="admin_search">
    <div class="search">
        <form method="post" class="global_form_box" action="">
            <div>
                <label>
                    <?php echo "Title" ?>
                </label>
                <?php if (empty($this->title)): ?>
                    <input type="text" name="title" /> 
                <?php else: ?>
                    <input type="text" name="title" value="<?php echo $this->title ?>"/>
                <?php endif; ?>
            </div>
            <div>
                <label>
                    <?php echo "Coupon Code" ?>
                </label>
                <?php if (empty($this->coupon_code)): ?>
                    <input type="text" name="coupon_code" /> 
                <?php else: ?>
                    <input type="text" name="coupon_code" value="<?php echo $this->coupon_code ?>"/>
                <?php endif; ?>
            </div>
            <div>
                <label>
                    <?php echo "Owner" ?>
                </label>	
                <?php if (empty($this->owner)): ?>
                    <input type="text" name="owner" /> 
                <?php else: ?> 
                    <input type="text" name="owner" value="<?php echo $this->owner ?>" />
                <?php endif; ?>
            </div>
            <div>
                <label>
                    <?php echo "Event Name" ?>
                </label>
                <?php if (empty($this->siteevent_title)): ?>
                    <input type="text" name="siteevent_title" /> 
                <?php else: ?>
                    <input type="text" name="siteevent_title" value="<?php echo $this->siteevent_title ?>"/>
                <?php endif; ?>
            </div>
            <div style="margin:10px 0 0 10px;">
                <button type="submit" name="search" ><?php echo "Search" ?></button>
            </div>
        </form>
    </div>
</div>

<br />

<div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
</div>

<?php if ($this->paginator->getTotalItemCount()): ?>
    <div class='admin_members_results'>
        <div>
            <?php echo $this->translate(array('%s event coupon found.', '%s event coupons found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
        </div>
        <?php echo $this->paginationControl($this->paginator); ?>
    </div>

    <form id='multidelete_form' class="clr" method="post" action="<?php echo $this->url(array('action' => 'multi-delete')); ?>" onSubmit="return multiDelete()">
        <table class='admin_table seaocore_admin_table' border="0">
            <thead>
                <tr>
                    <th style='width: 1%;' align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
                    <th style='width: 2%;' align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('coupon_id', 'DESC');"><?php echo 'ID'; ?></a></th>
                    <th style='width: 4%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo 'Title'; ?></a></th>
                    <th style='width: 4%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo 'Owner'; ?></a></th>
                    <th style='width: 4%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('siteevent_title', 'ASC');"><?php echo 'Event Name'; ?></a></th>
                    <th style='width: 4%;' align="left"><?php echo 'Coupon Code'; ?></th>
                    <th style='width: 4%;' align="left"><?php echo 'Discount'; ?></th>
                    <th style='width: 4%;' class="admin_table_centered"><?php echo 'Approved'; ?></th>
                    <th style='width: 4%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo 'Starting Date'; ?></a></th>
                    <th style='width: 4%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('end_time', 'DESC');"><?php echo 'Expiration Date'; ?></a></th>
                    <th style='width: 4%;' class='admin_table_options' align="left"><?php echo 'Options'; ?></th>
                </tr>
            </thead>

            <tbody>
                <?php if ($this->paginator->getTotalItemCount()): ?>
                    <?php foreach ($this->paginator as $item): ?>
                        <tr>        
                            <td><input name='delete_<?php echo $item->coupon_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->coupon_id ?>"/></td>
                            <td class="admin_table_centered"><?php echo $item->coupon_id ?></td>

                            <td class='admin_table_bold' title="<?php echo $item->title; ?>"><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), 16); ?></td>

                            <td class='admin_table_bold'><?php echo $this->htmlLink($this->item('user', $item->owner_id)->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($this->user($item->owner_id)->username, 10), array('title' => $item->username, 'target' => '_blank')) ?></td>

                            <td class='admin_table_bold'><?php echo $this->htmlLink($this->item('siteevent_event', $item->event_id)->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->siteevent_title, 16), array('title' => $item->siteevent_title, 'target' => '_blank')) ?></td>
                            <td class='admin_table_bold' title="<?php echo $item->coupon_code; ?>"><?php echo $item->coupon_code; ?></td>
                            <?php if ($item->discount_type == 1): ?>
                                <?php
                                $price = $item->discount_amount;
                                $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                                $view = Zend_Registry::get('Zend_View');
                                $priceStr = $view->locale()->toCurrency($price, $currency, array('precision' => 2));
                                ?>
                                <td class='admin_table_bold' title="<?php echo $item->discount_amount; ?>"><?php echo $priceStr; ?></td>

                            <?php else: ?>
                                <td class='admin_table_bold' title="<?php echo $item->discount_amount; ?>"><?php echo $item->discount_amount . '%'; ?></td>
                            <?php endif; ?>

            <?php if ($item->approved == 1): ?>
                                <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'coupon', 'action' => 'approval', 'id' => $item->coupon_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => 'Make Dis-Approved'))) ?>
                                </td>      
            <?php else: ?>  
                                <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'coupon', 'action' => 'approval', 'id' => $item->coupon_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => 'Make Approved'))) ?>
                                </td> 
            <?php endif; ?>

                            <td><?php echo gmdate('M d,Y', strtotime($item->start_time)) ?></td>
                            <?php $today = date("Y-m-d H:i:s"); ?>
                            <?php if ($item->end_settings == 1 && ($item->end_time >= $today)): ?>
                                <td><?php echo gmdate('M d,Y', strtotime($item->end_time)) ?></td>
                            <?php elseif ($item->end_settings == 0): ?>
                                <td><?php echo 'Never Expires'; ?></td>
                            <?php else: ?>
                                <td><?php echo 'Expired'; ?></td>
            <?php endif; ?>

                            <td class='admin_table_options'>
                                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'coupon', 'action' => 'detail', 'id' => $item->coupon_id), 'details', array('class' => 'smoothbox')) ?>
                                |
                                <?php echo $this->htmlLink(array('route' => 'siteeventticket_coupon', 'action' => 'edit', 'coupon_id' => $item->coupon_id), 'edit', array('target' => '_blank')) ?>	
                                |
            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'coupon', 'action' => 'delete', 'id' => $item->coupon_id), 'delete', array('class' => 'smoothbox')); ?>
                            </td> 
                        </tr>
                    <?php endforeach; ?>
    <?php endif; ?>
            </tbody>
        </table>
        <br />
        <div class='buttons clr mtop10 fleft'>
            <button type='submit'><?php echo 'Delete Selected'; ?></button>
        </div>
    </form>
<?php else: ?>
    <div class="tip">
        <span>
    <?php echo 'No results were found.'; ?>
        </span>
    </div>
<?php endif; ?>

<style type="text/css">
    table.admin_table tbody tr td {
        white-space: nowrap;
    }
    .events{margin-top:15px;}	
</style>