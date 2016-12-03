<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (!empty($this->siteeventcouponDetail)): ?>
    <div class="siteevent_admin_popup" style="margin:10px 10px 0 10px;">
        <?php foreach ($this->siteeventcouponDetail as $item)
             ?>
        <div>
            <h3><?php echo 'Event Coupon Details'; ?></h3>
            <table>
                <tr>
                <tr valign="top">
                    <td width="120"><b><?php echo 'Title:'; ?></b></td>
                    <td>
    <?php echo $this->siteeventcouponDetail->title; ?>&nbsp;&nbsp;
                    </td>
                </tr>
                <tr valign="top">
                    <td width="120"><b><?php echo 'Coupon Code:'; ?></b></td>
                    <td>
    <?php echo $this->siteeventcouponDetail->coupon_code; ?>&nbsp;&nbsp;
                    </td>
                </tr> 
                <tr valign="top">
                    <td width="120"><b><?php echo 'Start Date:'; ?></b></td>
                    <td>
    <?php echo $this->siteeventcouponDetail->start_time; ?>&nbsp;&nbsp;
                    </td>
                </tr>
                <tr valign="top">
                    <td width="120"><b><?php echo 'End Date:'; ?></b></td>
                    <td>
                        <?php if (!empty($this->siteeventcouponDetail->end_settings)): ?>
                            <?php echo $this->siteeventcouponDetail->end_time; ?>&nbsp;&nbsp;
                        <?php else: ?>
                            <?php echo 'Never Expires'; ?>
    <?php endif; ?>
                    </td>
                </tr>

                <tr valign="top">
                    <td width="120"><b><?php echo 'Discount:'; ?></b></td>
                    <td>
                        <?php
                        if (!empty($this->siteeventcouponDetail->discount_type)):

                            $priceStr = Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->siteeventcouponDetail->discount_amount);
                            ?>
                            <span><?php echo $priceStr; ?></span>&nbsp;&nbsp;
                        <?php else: ?>
                            <span><?php echo $this->siteeventcouponDetail->discount_amount . '%'; ?></span>&nbsp;&nbsp;
    <?php endif; ?>
                    </td>
                </tr>

                <?php // if(!empty($this->siteeventcouponDetail->ticket_ids)): ?>
    <!--        <tr valign="top">
                                            <td width="120"><b><?php // echo 'Selected Products:'; ?></b></td>
                <?php // $selected_ticket_ids = explode(',' , $this->siteeventcouponDetail->ticket_ids);?>
                <?php // foreach($selected_ticket_ids as $product_id): ?>
                <?php // $productTitle = Engine_Api::_()->getDbTable('products', 'siteevent')->getProductTitle($product_id);?>
                                            <td>
                <?php // echo $productTitle; ?>&nbsp;&nbsp;
                                            </td>
                <?php // endforeach;?>
                                    </tr>-->
    <?php // endif; ?>
                <tr valign="top">
                    <td width="120"><b><?php echo 'Status:'; ?></b></td>
                    <td>
                        <?php if (empty($this->siteeventcouponDetail->status)): ?>
                            <?php echo 'Disabled'; ?>&nbsp;&nbsp;
                        <?php else: ?>
        <?php echo 'Enabled'; ?>&nbsp;&nbsp;
    <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="120"><b><?php echo 'Approved:'; ?></b></td>
                    <td>
                        <?php if (empty($this->siteeventcouponDetail->approved)): ?>
                            <?php echo 'Dis-Approved'; ?>&nbsp;&nbsp;
                        <?php else: ?>
        <?php echo 'Approved'; ?>&nbsp;&nbsp;
    <?php endif; ?>
                    </td>
                </tr>

                <tr valign="top">
                    <td width="120"><b><?php echo 'Description:'; ?></b></td>
                    <td>
    <?php echo $this->siteeventcouponDetail->description; ?>&nbsp;&nbsp;
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td><br /><button  onclick='javascript:parent.Smoothbox.close()' ><?php echo 'Close' ?></button></td>
                </tr>
                </tr>
            </table>
    <?php if (@$this->closeSmoothbox): ?>
                <script type="text/javascript">
                    TB_close();
                </script>
    <?php endif; ?>
            <style type="text/css">
                td{padding:3px; }
                td b{font-weight:bold;}
            </style>
        </div>
    </div>
<?php endif; ?>