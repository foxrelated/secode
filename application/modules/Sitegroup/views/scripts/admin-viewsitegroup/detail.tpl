<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="sitegroup_admin_popup"> 
  <div>
    <h3><?php echo $this->translate('Group Details'); ?></h3>
    <br />
    <table cellpadding="0" cellspacing="0">
      <tr>
        <td>
          <table cellpadding="0" cellspacing="0" width="350">
            <tr>
              <td width="120"><b><?php echo $this->translate('Title:'); ?></b></td>
              <td>
                <?php echo $this->htmlLink($this->item('sitegroup_group', $this->sitegroupDetail->group_id)->getHref(), $this->translate($this->sitegroupDetail->title), array('target' => '_blank')) ?>&nbsp;&nbsp;
              </td>
            <tr>
              <td><b><?php echo $this->translate('Owner:'); ?></b></td>
              <td>
                <?php echo $this->htmlLink($this->sitegroupDetail->getOwner()->getHref(), $this->sitegroupDetail->getOwner()->getTitle(), array('target' => '_blank')) ?>
              </td>
            </tr>

            <?php if ($this->manageAdminEnabled): ?>
              <tr>
                <td><b><?php echo $this->translate('Total Admins:'); ?></b></td>
                <td><?php echo $this->admin_total; ?></td>
              </tr>
            <?php endif; ?>

            <?php //if ($this->sitepageDetail->member_count): ?>
            <?php $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember'); ?>
            <?php if ($sitegroupmemberEnabled): ?>
              <tr>
                <td><b><?php echo $this->translate('Total Members:'); ?></b></td>
                <td><?php echo $this->sitegroupDetail->member_count; ?></td>
              </tr>
            <?php endif; ?>
            <?php //endif; ?>

            <tr>
              <?php if ($this->category_name != '') : ?>
                <td><b><?php echo $this->translate('Category:'); ?></b></td> 
                <td>
                  <?php echo $this->htmlLink($this->url(array('category_id' => $this->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->category_name)), 'sitegroup_general_category'), $this->translate($this->category_name), array('target' => '_blank')) ?>
                </td>	    
              <?php endif; ?>
            </tr>	
            <tr>
              <?php if ($this->subcategory_name != '') : ?>
                <td><b><?php echo $this->translate('Subcategory:'); ?></b></td> 
                <td>
                  <?php echo $this->htmlLink($this->url(array('category_id' => $this->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->category_name), 'subcategory_id' => $this->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->subcategory_name)), 'sitegroup_general_subcategory'), $this->translate($this->subcategory_name), array('target' => '_blank')) ?>
                </td>	    
              <?php endif; ?>
            </tr>
            <tr>
              <?php if ($this->subsubcategory_name != '') : ?>
                <td><b><?php echo $this->translate('3%s Level Category:', "<sup>rd</sup>"); ?></b></td>
                <td>
                  <?php echo $this->htmlLink($this->url(array('category_id' => $this->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->category_name), 'subcategory_id' => $this->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->subcategory_name),'subsubcategory_id' => $this->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->subsubcategory_name)), 'sitegroup_general_subsubcategory'), $this->translate($this->subsubcategory_name), array('target' => '_blank')) ?>
                </td>
              <?php endif; ?>
            </tr>
            <tr>
              <td><b><?php echo $this->translate('Creation Date:'); ?></b></td>
              <td>
                <?php echo $this->translate(gmdate('M d,Y g:i A', strtotime($this->sitegroupDetail->creation_date))); ?>
              </td>
            </tr>
            <tr>
              <td><b><?php echo $this->translate('Approved:'); ?></b></td>
              <td>
                <?php
                if ($this->sitegroupDetail->approved)
                  echo $this->translate('Yes');
                else
                  echo $this->translate("No");
                ?>
              </td>
            </tr>
            <tr>
              <td><b><?php echo $this->translate('Approved Date:'); ?></b></td>
              <td>
                <?php if (!empty($this->sitegroupDetail->aprrove_date)): ?>
                  <?php echo $this->translate(date('M d,Y g:i A', strtotime($this->sitegroupDetail->aprrove_date))); ?>
                <?php else: ?>
                  <?php echo $this->translate('-'); ?>
                <?php endif; ?>
              </td>
            </tr>

            <tr>
              <td><b><?php echo $this->translate('Featured:'); ?></b></td>
              <td> <?php
                if ($this->sitegroupDetail->featured)
                  echo $this->translate('Yes');
                else
                  echo $this->translate("No");?>
              </td>
            </tr>

            <tr>
              <td><b><?php echo $this->translate('Sponsored:'); ?></b></td>
              <td> <?php
                if ($this->sitegroupDetail->sponsored)
                  echo $this->translate('Yes');
                else
                  echo $this->translate("No");?>
              </td>
            </tr>
            <?php $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.price.field', 1); ?>
            <?php if ($this->sitegroupDetail->price && $enablePrice): ?>
              <tr>
                <td><b><?php echo $this->translate('Price:'); ?></b></td>
                <td><?php echo $this->locale()->toCurrency($this->sitegroupDetail->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')) ?></td>
              </tr>
            <?php endif; ?>
            <?php $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.locationfield', 1); ?>
            <?php if ($this->sitegroupDetail->location && $enableLocation): ?>
              <tr>
                <td><b><?php echo $this->translate('Location:'); ?></b></td>
                <td><?php echo $this->sitegroupDetail->location ?></td>
              </tr>
            <?php endif; ?>

            <tr>
              <td><b><?php echo $this->translate('Views:'); ?></b></td>
              <td><?php echo $this->translate($this->sitegroupDetail->view_count); ?> </td>
            </tr>

            <tr>
              <td><b><?php echo $this->translate('Comments:'); ?></b></td>
              <td><?php echo $this->translate($this->sitegroupDetail->comment_count); ?> </td>
            </tr>

            <tr>
              <td><b><?php echo $this->translate('Likes:'); ?></b></td>
              <td><?php echo $this->translate($this->sitegroupDetail->like_count); ?> </td>
            </tr>

            <?php if ($this->isEnabledSitegroupreview && $this->sitegroupDetail->rating > 0): ?>

            <tr>
              <td><b><?php echo $this->translate('Reviews:'); ?></b></td>
              <td><?php echo $this->translate($this->sitegroupDetail->review_count); ?> </td>
            </tr>

              <?php
              $currentRatingValue = $this->sitegroupDetail->rating;
              $difference = $currentRatingValue - (int) $currentRatingValue;
              if ($difference < .5) {
                $finalRatingValue = (int) $currentRatingValue;
              } else {
                $finalRatingValue = (int) $currentRatingValue + .5;
              }
              ?>

              <tr>           
                <td><b><?php echo $this->translate('Rating:'); ?></b></td>
                <td> <?php for ($x = 1; $x <= $this->sitegroupDetail->rating; $x++): ?>
                    <span class="rating_star_generic rating_star" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>"></span>
                  <?php endfor; ?>
                  <?php if ((round($this->sitegroupDetail->rating) - $this->sitegroupDetail->rating) > 0): ?>
                    <span class="rating_star_generic rating_star_half" title="<?php echo $this->sitegroupDetail->rating . $this->translate(' rating'); ?>"></span>
                  <?php endif; ?>
                  <?php if (empty($this->sitegroupDetail->rating))
                  echo $this->translate("-"); ?>
                </td>
              </tr>
              <?php endif; ?>
          </table>
        </td>
        <td align="right">
           <?php echo $this->htmlLink($this->sitegroupDetail->getHref(), $this->itemPhoto($this->sitegroupDetail, 'thumb.icon', '', array('align' => 'right')), array('target' => '_blank')) ?>
        </td>	
      </tr>
    </table>		
    <br />
    <button  onclick='javascript:parent.Smoothbox.close()' ><?php echo $this->translate('Close') ?></button>
  </div>
</div>	

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>