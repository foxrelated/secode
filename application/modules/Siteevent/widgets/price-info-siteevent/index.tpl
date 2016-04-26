<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="siteevent_price_info_block <?php if (!empty($this->layout_column)): ?>siteevent_side_widget<?php endif; ?>">
    <div <?php if (empty($this->layout_column)): ?> class="siteevent_review_block"<?php endif; ?>>
        <table>
            <?php foreach ($this->priceInfos as $priceInfo): ?>
                <?php $url = $this->url(array('action' => 'redirect', 'id' => $this->siteevent->getIdentity()), 'siteevent_priceinfo', true) . '?url=' . @base64_encode($priceInfo->url); ?>
                <tr class="<?php echo $this->cycle(array("even", "odd"))->next() ?>" valign="middle">
                    <td class="siteevent_price_info_image">	
                        <?php
                        $imgSrc = null;
                        if ($priceInfo->photo_id):
                            $file = Engine_Api::_()->getItemTable('storage_file')->getFile($priceInfo->photo_id);
                            if ($file):
                                $imgSrc = $file->map();
                            endif;
                        endif;
                        ?>
                        <a href="<?php echo $url; ?>" target="_blank" title="<?php echo $priceInfo->wheretobuy_id == 1 ? $priceInfo->title : $priceInfo->wheretobuy_title; ?>" class="b_medium">
                            <?php if ($imgSrc): ?>
                                <img src='<?php echo $imgSrc ?>' alt="" align="center" />
                            <?php else: ?>
                                <?php echo $priceInfo->wheretobuy_id == 1 ? $priceInfo->title : $priceInfo->wheretobuy_title; ?>
                            <?php endif; ?>
                        </a>
                        <?php if ($this->min_price > 0 && $this->min_price == $priceInfo->price): ?>
                            <span class="siteevent_price_red_tag" title="<?php echo $this->translate("Lowest Price") ?>"></span>
                        <?php endif; ?>
                    </td>

                    <?php if (empty($this->layout_column)): ?>
                        <td class="siteevent_price_contact_info">
                            <?php if ($priceInfo->wheretobuy_id == 1): ?>
                                <?php if ($priceInfo->address): ?>
                                    <span class="address o_hidden clr fleft">
                                        <?php echo $this->htmlLink('https://maps.google.com/?q=' . urlencode($priceInfo->address), '<i class="fleft"></i>', array('target' => '_blank')); ?>	
                                        <span class="o_hidden"><?php echo $priceInfo->address; ?></span>
                                    </span>
                                <?php endif; ?>

                                <?php if ($priceInfo->contact): ?>
                                    <span class="number o_hidden clr fleft">
                                        <i class="fleft"></i>
                                        <span class="o_hidden"><?php echo $priceInfo->contact ?></span>
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                    <?php if ($this->show_price): ?>
                        <td class="siteevent_price_info_value">
                            <?php if ($priceInfo->price > 0): ?>
                                <a href="<?php echo $url; ?>" target="_blank" >
                                    <?php echo $this->locale()->toCurrency($priceInfo->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                    <?php if (empty($this->layout_column)): ?>
                        <td class="siteevent_price_info_view_button">
                            <a href="<?php echo $url; ?>" target="_blank" class="price_see_it_button">
                                <?php echo $this->translate("See It") ?>
                            </a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </table>  
    </div>  
    <?php if ($this->layout_column): ?>
        <?php if (!empty($this->tab_id)): ?>
            <div id="view_all_price_link" class="siteevent_more_link" style="display: block;">
                <a href="<?php echo $this->siteevent->getHref() . '/tab/' . $this->tab_id ?>">
                    <?php echo $this->translate('View all Stores &raquo;') ?>
                </a>
            </div>
        <?php else: ?>
            <div id="view_all_price_link" class="siteevent_more_link" style="display: none;">
                <a href="javascript:void(0);" onclick="viewAllPrice()"><?php echo $this->translate('View all Stores &raquo;') ?></a>
            </div>
        <?php endif; ?>
        <script type="text/javascript">
                en4.core.runonce.add(function() {
                    if ($('main_tabs') && $('main_tabs').getElement('.tab_layout_siteevent_price_info_siteevent')) {
                        $('view_all_price_link').style.display = 'block';
                    }
                });
                function viewAllPrice() {

                    if ($('main_tabs') && $('main_tabs').getElement('.tab_layout_siteevent_price_info_siteevent')) {
                        tabContainerSwitch($('main_tabs').getElement('.tab_layout_siteevent_price_info_siteevent'));
                        location.hash = 'main_tabs';
                    }

                }
        </script>

    <?php elseif ($this->show_price): ?>
        <div class="clr seaocore_txt_light btm_note"><?php echo $this->translate('* The above cost (if any) for the %s is estimated and may slightly vary after including the taxes, manufacturer rebate, shipping cost, or any other sales / promotion on event Stores.', $this->siteevent->getTitle()) ?>
        </div>
    <?php endif; ?>
</div>
