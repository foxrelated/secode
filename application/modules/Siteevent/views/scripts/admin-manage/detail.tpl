<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="global_form_popup siteevent_event_details_view">
    <h3><?php echo $this->translate('Event Details'); ?></h3>
    <div class="top clr">
        <?php echo $this->htmlLink($this->siteeventDetail->getHref(), $this->itemPhoto($this->siteeventDetail, 'thumb.icon'), array('target' => '_blank')); ?>
        <?php echo $this->htmlLink($this->siteeventDetail->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($this->siteeventDetail->getTitle(), 19), array('target' => '_blank', 'title' => $this->siteeventDetail->getTitle())) ?>
    </div>
    <table class="clr">
        <tr>
            <td width="200"><b><?php echo $this->translate('Title :'); ?></b></td>
            <td><?php echo $this->translate($this->siteeventDetail->title); ?>&nbsp;&nbsp;</td>
        <tr >
            <td><b><?php echo $this->translate(' 	Owner :'); ?></b></td>
            <td><?php echo $this->translate($this->siteeventDetail->getOwner()->getTitle()); ?></td>
        </tr>

        <?php if ($this->siteeventDetail->category_id) : ?>
            <tr>

                <?php $category_id = $this->siteeventDetail->category_id; ?>
                <?php $category = Engine_Api::_()->getItem('siteevent_category', $category_id); ?>
                <?php $categoryName = $category->category_name; ?>
                <?php $categorySlug = $category->getCategorySlug() ?>

                <td><b><?php echo $this->translate('Category:'); ?></b></td> 
                <td>
                    <?php echo $this->htmlLink($this->url(array('category_id' => $category_id, 'categoryname' => $categorySlug), Engine_Api::_()->siteevent()->getCategoryHomeRoute()), $this->translate($categoryName), array('target' => '_blank')) ?>
                </td>	    

            </tr>	
            <?php if ($this->siteeventDetail->subcategory_id) : ?>
                <tr>

                    <?php $subcategory_id = $this->siteeventDetail->subcategory_id; ?>
                    <?php $subcategory = Engine_Api::_()->getItem('siteevent_category', $subcategory_id); ?>
                    <?php $subcategoryName = $subcategory->category_name; ?>
                    <?php $subcategorySlug = $subcategory->getCategorySlug() ?>
                    <td><b><?php echo $this->translate('Subcategory:'); ?></b></td> 
                    <td>
                        <?php echo $this->htmlLink($this->url(array('category_id' => $category_id, 'categoryname' => $categorySlug, 'subcategory_id' => $subcategory_id, 'subcategoryname' => $subcategorySlug), 'siteevent_general_subcategory'), $this->translate($subcategoryName), array('target' => '_blank')) ?>
                    </td>	    

                </tr>
                <tr>
                    <?php if ($this->siteeventDetail->subsubcategory_id) : ?>
                        <?php $subsubcategory_id = $this->siteeventDetail->subsubcategory_id; ?>
                        <?php $subsubcategory = Engine_Api::_()->getItem('siteevent_category', $subsubcategory_id); ?>
                        <?php $subsubCategoryName = $subsubcategory->category_name; ?>
                        <?php $subsubcategorySlug = $subsubcategory->getCategorySlug() ?>
                        <td><b><?php echo $this->translate('3%s Level Category:', "<sup>rd</sup>"); ?></b></td>
                        <td>
                            <?php echo $this->htmlLink($this->url(array('category_id' => $category_id, 'categoryname' => $categorySlug, 'subcategory_id' => $subcategory_id, 'subcategoryname' => $subcategorySlug, 'subsubcategory_id' => $subsubcategory_id, 'subsubcategoryname' => $subsubcategorySlug), 'siteevent_general_subsubcategory'), $this->translate($subsubCategoryName), array('target' => '_blank')) ?>
                        </td>
                    <?php endif; ?>
                </tr>    
            <?php endif; ?>
        <?php endif; ?>

        <tr>
            <td><b><?php echo $this->translate('Featured :'); ?></b></td>
            <td>
                <?php
                if ($this->siteeventDetail->featured)
                    echo $this->translate('Yes');
                else
                    echo $this->translate("No");
                ?>
            </td>
        </tr>

        <tr>
            <td><b><?php echo $this->translate('Sponsored :'); ?></b></td>
            <td> <?php
                if ($this->siteeventDetail->sponsored)
                    echo $this->translate('Yes');
                else
                    echo $this->translate("No");
                ?>
            </td>
        </tr>

        <tr>
            <td><b><?php echo $this->translate('Creation Date :'); ?></b></td>
            <td>
                <?php echo $this->translate(gmdate('M d,Y, g:i A', strtotime($this->siteeventDetail->creation_date))); ?>
            </td>
        </tr>

        <tr>
            <td><b><?php echo $this->translate('Last Modified Date :'); ?></b></td>
            <td>
                <?php echo $this->translate(gmdate('M d,Y, g:i A', strtotime($this->siteeventDetail->modified_date))); ?>
            </td>
        </tr>      

        <tr>
            <td><b><?php echo $this->translate('Approved :'); ?></b></td>
            <td>
                <?php
                if ($this->siteeventDetail->approved)
                    echo $this->translate('Yes');
                else
                    echo $this->translate("No");
                ?>
            </td>
        </tr>

        <tr>
            <td><b><?php echo $this->translate('Approved on DATE :'); ?></b></td>
            <td>
                <?php if (!empty($this->siteeventDetail->approved_date)): ?>
                    <?php echo $this->translate(date('M d,Y, g:i A', strtotime($this->siteeventDetail->approved_date))); ?>
                <?php else: ?>
                    <?php echo $this->translate('-'); ?>
                <?php endif; ?>
            </td>
        </tr>

        <?php if ($this->siteeventDetail->price > 0): ?>
            <tr>
                <td><b><?php echo $this->translate('Price :'); ?></b></td>
                <td><?php echo $this->siteeventDetail->price ?></td>
            </tr>
         <?php else:?>
						<tr>
							<td><b><?php echo $this->translate('Price :'); ?></b></td>
							<td><?php echo $this->translate("FREE"); ?></td>
            </tr>
        <?php endif; ?>

        <tr>
            <td><b><?php echo $this->translate('Added in number of Diaries:'); ?></b></td>
            <td><?php echo Engine_Api::_()->getDbTable('diarymaps', 'siteevent')->getDiariesEventCount($this->siteeventDetail->event_id) ?></td>
        </tr>     

        <?php if ($this->siteeventDetail->location): ?>
            <tr>
                <td><b><?php echo $this->translate('Location :'); ?></b></td>
                <td><?php echo $this->siteeventDetail->location ?></td>
            </tr>
        <?php endif; ?>

        <tr>
            <td><b><?php echo $this->translate('Views :'); ?></b></td>
            <td><?php echo $this->translate($this->siteeventDetail->view_count); ?> </td>
        </tr>

        <tr>
            <td><b><?php echo $this->translate('Comments :'); ?></b></td>
            <td><?php echo $this->translate($this->siteeventDetail->comment_count); ?> </td>
        </tr>

        <?php if(!Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>
            <tr>
                <td><b><?php echo $this->translate('Guests :'); ?></b></td>
                <td><?php echo $this->translate($this->siteeventDetail->member_count); ?> </td>
            </tr>
        <?php endif; ?>

        <tr>
            <td><b><?php echo $this->translate('Likes :'); ?></b></td>
            <td><?php echo $this->translate($this->siteeventDetail->like_count); ?> </td>
        </tr>

        <tr>
            <td><b><?php echo $this->translate('Reviews :'); ?></b></td>
            <td><?php echo $this->siteeventDetail->review_count; ?> </td>
        </tr>
        <tr>           
            <td><b><?php echo $this->translate('Average Rating :'); ?></b></td>
            <td>
                <?php if ($this->siteeventDetail->rating_avg > 0): ?>
                    <?php echo $this->ShowRatingStarSiteevent($this->siteeventDetail->rating_avg, 'user', 'small-star'); ?>
                <?php else: ?>
                    ---
                <?php endif; ?>
            </td>
        </tr>     
        <tr>           
            <td><b><?php echo $this->translate('Editor Rating :'); ?></b></td>
            <td>
                <?php if ($this->siteeventDetail->rating_editor > 0): ?>
                    <?php echo $this->ShowRatingStarSiteevent($this->siteeventDetail->rating_editor, 'editor', 'small-star'); ?>
                <?php else: ?>
                    ---
                <?php endif; ?>
            </td>
        </tr>

        <tr>           
            <td><b><?php echo $this->translate('User Rating :'); ?></b></td>
            <td>
                <?php if ($this->siteeventDetail->rating_users > 0): ?>
                    <?php echo $this->ShowRatingStarSiteevent($this->siteeventDetail->rating_users, 'user', 'small-star'); ?>
                <?php else: ?>
                                    ---
                <?php endif; ?>
            </td>
        </tr>

    </table>
    <br />
    <button  onclick='javascript:parent.Smoothbox.close()' ><?php echo $this->translate('Close') ?></button>
</div>

<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
            TB_close();
    </script>
<?php endif; ?>