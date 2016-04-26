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
<?php $hasPackageEnable = Engine_Api::_()->siteevent()->hasPackageEnable();?>
<script type="text/javascript">
    var currentOrder = '<?php echo $this->order ?>';
    var currentOrderDirection = '<?php echo $this->order_direction ?>';
    var changeOrder = function(order, default_direction) {

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
        return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected events ?")) ?>');
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

<?php if ($this->contentModule == 'sitepage'): ?>
    <h2>
        <?php echo $this->translate('Directory / Pages Plugin'); ?>
    </h2>
<?php elseif ($this->contentModule == 'sitebusiness'): ?>
    <h2>
        <?php echo $this->translate('Directory / Businesses Plugin'); ?>
    </h2>
<?php elseif ($this->contentModule == 'sitegroup'): ?>
    <h2>
        <?php echo $this->translate('Groups / Communities Plugin'); ?>
    </h2>
<?php elseif ($this->contentModule == 'sitestore'): ?>
    <h2>
        <?php echo $this->translate('Stores / Marketplace Plugin'); ?>
    </h2>
<?php elseif (Engine_Api::_()->hasModuleBootstrap('sitereview') && Engine_Api::_()->hasModuleBootstrap('sitereviewlistingtype') && $this->contentModule == 'sitereview'): ?>
    <h2>
        <?php echo $this->translate('Multiple Listing Types - Listing Type Creation Extension'); ?>
    </h2>
<?php elseif (Engine_Api::_()->hasModuleBootstrap('sitereview') && $this->contentModule == 'sitereview'): ?>
    <h2>
        <?php echo $this->translate('Multiple Listing Types Plugin Core (Reviews & Ratings Plugin)'); ?>
    </h2>
<?php else: ?>
    <h2>
        <?php echo $this->translate('Advanced Events Plugin'); ?>
    </h2>
<?php endif; ?>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<h2><?php echo $this->translate('Manage Events'); ?></h2>
<h4><?php echo $this->translate('This page lists all the events your users have posted. You can use this page to monitor these events and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific event entries. Leaving the filter fields blank will show all the event entries on your social network. Here, you can also make events featured / un-featured, sponsored / un-sponsored, new / remove from new, and approve / dis-approve them.'); ?></h4><br />

<div class="admin_search siteevent_admin_event_search">
    <div class="search">
        <form method="post" class="global_form_box" action="" width="100%">

            <div>
                <label>
                    <?php echo $this->translate("Title") ?>
                </label>
                <?php if (empty($this->title)): ?>
                    <input type="text" name="title" /> 
                <?php else: ?>
                    <input type="text" name="title" value="<?php echo $this->translate($this->title) ?>"/>
                <?php endif; ?>
            </div>

            <div>
                <label>
                    <?php echo $this->translate("Owner") ?>
                </label>	
                <?php if (empty($this->owner)): ?>
                    <input type="text" name="owner" /> 
                <?php else: ?> 
                    <input type="text" name="owner" value="<?php echo $this->translate($this->owner) ?>" />
                <?php endif; ?>
            </div>        

            <?php $categories = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1); ?>              
            <div class="form-wrapper" id="category_id-wrapper">
                <div class="form-label" id="category_id-label">
                    <label class="optional" for="category_id"><?php echo $this->translate('Category'); ?></label>
                </div>
                <div class="form-element" id="category_id-element">
                    <select id="category_id" name="category_id" onchange='addOptions(this.value, "cat_dependency", "subcategory_id", 0);'>
                        <option value=""></option>
                        <?php if (count($categories) != 0): ?>
                            <?php
                            $categories_prepared[0] = "";
                            foreach ($categories as $category) {
                                $categories_prepared[$category->category_id] = $category->category_name;
                                ?>
                                <option value="<?php echo $category->category_id; ?>" <?php if ($this->category_id == $category->category_id) echo "selected"; ?>><?php echo $this->translate($category->category_name); ?></option>
                            <?php } ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="form-wrapper" id="subcategory_id-wrapper" style='display:none;'>
                <div class="form-label" id="subcategory_id-label">
                    <label class="optional" for="subcategory_id"><?php echo $this->translate('Sub-Category'); ?></label>
                </div>
                <div class="form-element" id="subcategory_id-element">
                    <select id="subcategory_id" name="subcategory_id" onchange='addOptions(this.value, "subcat_dependency", "subsubcategory_id", 0);'></select>
                </div>
            </div>

            <div class="form-wrapper" id="subsubcategory_id-wrapper" style='display:none;'>
                <div class="form-label" id="subsubcategory_id-label">
                    <label class="optional" for="subsubcategory_id"><?php echo $this->translate('3rd Level Category', "<sup>rd</sup>") ?></label>
                </div>
                <div class="form-element" id="subsubcategory_id-element">
                    <select id="subsubcategory_id" name="subsubcategory_id"  ></select>
                </div>
            </div><br/><br/><br/>
            
            <div class="clr">
              <?php 
                //MAKE THE STARTTIME AND ENDTIME FILTER
                $attributes = array();
                $attributes['dateFormat'] = 'ymd';

                $form = new Engine_Form_Element_CalendarDateTime('starttime');
                $attributes['options'] = $form->getMultiOptions();
                $attributes['id'] = 'starttime';

                $starttime['date'] = $this->starttime;
                $endtime['date'] = $this->endtime;

                echo '<label>From</label><div>';
                echo $this->FormCalendarDateTime('starttime', $starttime, array_merge(array('label' => 'From'), $attributes), $attributes['options'] );
                echo '</div>';
              ?>
            </div>     
            
            <div>
              <?php 
                $form = new Engine_Form_Element_CalendarDateTime('endtime');
                $attributes['options'] = $form->getMultiOptions();              
                $attributes['id'] = 'endtime';
                echo '<label>To</label><div>';
                echo $this->FormCalendarDateTime('endtime', $endtime, array_merge(array('label' => 'To'), $attributes), $attributes['options'] );
                echo '</div>';
              ?>
            </div>             

            <div>
                <label>
                    <?php echo $this->translate("Events Having") ?>	
                </label>
                <select id="" name="review_status">
                    <option value="0" ><?php echo $this->translate("Select") ?></option>
                    <option value="rating_editor" <?php if ($this->review_status == 'rating_editor') echo "selected"; ?> ><?php echo $this->translate("Editor Reviews") ?></option>
                    <option value="rating_users" <?php if ($this->review_status == 'rating_users') echo "selected"; ?> ><?php echo $this->translate("User Reviews") ?></option>
                    <option value="rating_avg" <?php if ($this->review_status == 'rating_avg') echo "selected"; ?> ><?php echo $this->translate("Editor or User Reviews") ?></option> 
                    <option value="both" <?php if ($this->review_status == 'both') echo "selected"; ?> ><?php echo $this->translate("Editor and User Reviews") ?></option> 
                </select>
            </div>      

            <!--PACKAGE NAME-->
        <?php if($hasPackageEnable):?>
            <div>
              <label>
                <?php echo  $this->translate("Package") ?>
              </label>
              <select id="package_id" name="package_id">
                <option value="0" ></option>
                <?php foreach ( $this->packageList as $package): ?>
                <option value="<?php echo $package->package_id ?>" <?php if( $this->package_id == $package->package_id) echo "selected";?> > <?php echo ucfirst($package->title) ?></option>
              <?php  endforeach; ?>
              </select>
            </div>
        <?php endif; ?>
            
            <div>
                <label>
                    <?php echo $this->translate("Featured") ?>	
                </label>
                <select id="" name="featured">
                    <option value="0" ><?php echo $this->translate("Select") ?></option>
                    <option value="2" <?php if ($this->featured == 2) echo "selected"; ?> ><?php echo $this->translate("Yes") ?></option>
                    <option value="1" <?php if ($this->featured == 1) echo "selected"; ?> ><?php echo $this->translate("No") ?></option>
                </select>
            </div>

            <div>
                <label>
                    <?php echo $this->translate("Sponsored") ?>	
                </label>
                <select id="sponsored" name="sponsored">
                    <option value="0"  ><?php echo $this->translate("Select") ?></option>
                    <option value="2" <?php if ($this->sponsored == 2) echo "selected"; ?> ><?php echo $this->translate("Yes") ?></option>
                    <option value="1"  <?php if ($this->sponsored == 1) echo "selected"; ?>><?php echo $this->translate("No") ?></option>
                </select>
            </div>    

            <div>
                <label>
                    <?php echo $this->translate("New") ?>	
                </label>
                <select id="newlabel" name="newlabel">
                    <option value="0"  ><?php echo $this->translate("Select") ?></option>
                    <option value="2" <?php if ($this->newlabel == 2) echo "selected"; ?> ><?php echo $this->translate("Yes") ?></option>
                    <option value="1"  <?php if ($this->newlabel == 1) echo "selected"; ?>><?php echo $this->translate("No") ?></option>
                </select>
            </div>    

            <div>
                <label>
                    <?php echo $this->translate("Approved") ?>	
                </label>
                <select id="sponsored" name="approved">
                    <option value="0" ><?php echo $this->translate("Select") ?></option>
                    <option value="2" <?php if ($this->approved == 2) echo "selected"; ?> ><?php echo $this->translate("Yes") ?></option>
                    <option value="1" <?php if ($this->approved == 1) echo "selected"; ?> ><?php echo $this->translate("No") ?></option>
                </select>
            </div>

            <div>
                <label>
                    <?php echo $this->translate("Status") ?>	
                </label>
                <select id="" name="status">
                    <option value="0" ><?php echo $this->translate("Select") ?></option>
                    <option value="2" <?php if ($this->status == 2) echo "selected"; ?> ><?php echo $this->translate("Cancelled Events") ?></option>
                </select>
            </div>

            <div>
                <label>
                    <?php echo $this->translate("Browse By") ?>	
                </label>
                <select id="" name="eventbrowse">
                    <option value="0" ><?php echo $this->translate("Select") ?></option>
                    <option value="1" <?php if ($this->eventbrowse == 1) echo "selected"; ?> ><?php echo $this->translate("Most Viewed") ?></option>
                    <option value="2" <?php if ($this->eventbrowse == 2) echo "selected"; ?> ><?php echo $this->translate("Most Recent") ?></option>
                </select>
            </div>

            <?php if ($this->contentModule == 'siteevent'): ?>
                <?php
                $contentTypes = Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1));

                $contentTypeArray = array();
                if (!empty($contentTypes)) {
                    $contentTypeArray[] = 'All';
                    $moduleTitle = '';
                    foreach ($contentTypes as $contentType) {
                        if ($contentType['item_title']) {
                            $contentTypeArray['user'] = 'Member Events';
                            $contentTypeArray[$contentType['item_type']] = $contentType['item_title'];
                        } else {
                            if (Engine_Api::_()->hasModuleBootstrap('sitereview') && Engine_Api::_()->hasModuleBootstrap('sitereviewlistingtype')) {
                                $moduleTitle = 'Reviews & Ratings - Multiple Listing Types';
                            } elseif (Engine_Api::_()->hasModuleBootstrap('sitereview')) {
                                $moduleTitle = 'Reviews & Ratings';
                            }
                            $explodedResourceType = explode('_', $contentType['item_type']);
                            if (isset($explodedResourceType[2]) && $moduleTitle) {
                                $listingtypesTitle = Engine_Api::_()->getDbtable('listingtypes', 'sitereview')->getListingRow($explodedResourceType[2])->title_plural;
                                $listingtypesTitle = $listingtypesTitle . ' ( ' . $moduleTitle . ' ) ';
                                $contentTypeArray[$contentType['item_type']] = $listingtypesTitle;
                            } else {
                                $contentTypeArray[$contentType['item_type']] = Engine_Api::_()->getDbtable('modules', 'siteevent')->getModuleTitle($contentType['item_module']);
                            }
                        }
                    }
                }
                ?>

                <?php if (!empty($contentTypeArray)): ?>
                    <div>
                        <label>
                            <?php echo $this->translate("Event Type") ?>	
                        </label>
                        <select name="contentType" id="contentType">
                            <?php foreach ($contentTypeArray as $key => $contentType) : ?>
                                <option value="<?php echo $key; ?>" <?php if ($this->contentType == $key) echo "selected"; ?> label="<?php echo $contentType; ?>"><?php echo $contentType; ?></option>
                    <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            <?php elseif ($this->contentModule == 'sitereview'): ?>
                <?php
                $contentTypes = Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1));
                $contentTypeArray = array();
                $moduleTitle = '';
                if (!empty($contentTypes)) {
                    foreach ($contentTypes as $contentType) {
                        if ($contentType['item_title']) {
                            $contentTypeArray['user'] = 'Member Events';
                            $contentTypeArray[$contentType['item_type']] = $contentType['item_title'];
                        } else {
                            if (Engine_Api::_()->hasModuleBootstrap('sitereview') && Engine_Api::_()->hasModuleBootstrap('sitereviewlistingtype')) {
                                $moduleTitle = 'Reviews & Ratings - Multiple Listing Types';
                            } elseif (Engine_Api::_()->hasModuleBootstrap('sitereview')) {
                                $moduleTitle = 'Reviews & Ratings';
                            }
                            $explodedResourceType = explode('_', $contentType['item_type']);
                            if (isset($explodedResourceType[2]) && $moduleTitle) {
                                $listingtypesTitle = Engine_Api::_()->getDbtable('listingtypes', 'sitereview')->getListingRow($explodedResourceType[2])->title_plural;
                                $listingtypesTitle = $listingtypesTitle . ' ( ' . $moduleTitle . ' ) ';
                                $contentTypeArray[$contentType['item_type']] = $listingtypesTitle;
                            } else {
                                $contentTypeArray[$contentType['item_type']] = Engine_Api::_()->getDbtable('modules', 'siteevent')->getModuleTitle($contentType['item_module']);
                            }
                        }
                    }
                }
                ?>

                <?php if (!empty($contentTypeArray)): ?>
                    <div>
                        <label>
                            <?php echo $this->translate("Event Type") ?>	
                        </label>
                        <select name="contentType" id="contentType">
                            <?php foreach ($contentTypeArray as $key => $contentType) : ?>
                                <option value="<?php echo $key; ?>" <?php if ($this->contentType == $key) echo "selected"; ?> label="<?php echo $contentType; ?>"><?php echo $contentType; ?></option>
                    <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php           

            $hostOptions = array(
                "user" => "Member",
                'siteevent_organizer' => "Other Individual or Organization",
            );
            if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')) {
                $hostOptions = array_merge($hostOptions, array('sitepage_page' => 'Page'));
            }
            if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness')) {
                $hostOptions = array_merge($hostOptions, array('sitebusiness_business' => 'Business'));
            }
            if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup')) {
                $hostOptions = array_merge($hostOptions, array('sitegroup_group' => 'Group'));
            }    
            if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore')) {
                $hostOptions = array_merge($hostOptions, array('sitestore_store' => 'Store'));
            }         
            ?>
            
            <div class="form-wrapper" id="host_type-wrapper">
                <div class="form-label" id="host_type-label">
                    <label class="optional" for="host_type"><?php echo $this->translate('Host Type'); ?></label>
                </div>
                <div class="form-element" id="host_type-element">
                    <select id="host_type" name="host_type" onchange='addHostText(this.value);'>
                        <option value=""></option>
                        <?php foreach ($hostOptions as $key => $hostOption): ?>
                            <option value="<?php echo $key; ?>" <?php if ($this->host_type == $key) echo "selected"; ?>><?php echo $hostOption; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>     
            <?php $showHostText = empty($this->host_type) ? 'none' : 'block'?>
            <div id="host_title" style="display:<?php echo $showHostText;?>;">
                <label>
                    <?php echo $this->translate("Host Name") ?>
                </label>	
                <?php if (empty($this->host_title)): ?>
                    <input type="text" name="host_title" /> 
                <?php else: ?> 
                    <input type="text" name="host_title" value="<?php echo $this->host_title ?>" />
                <?php endif; ?>
            </div>    
            
            <input type="hidden" value='<?php echo $this->contentModule; ?>' name="contentModule" />   
						
            <div class="clear mtop10">
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
    <?php $counter = $this->paginator->getTotalItemCount(); ?>
    <?php if (!empty($counter)): ?>
        <div class="">
            <?php echo $this->translate(array('%s event found.', '%s events found.', $counter), $this->locale()->toNumber($counter)) ?>
        </div>
    <?php else: ?>
        <div class="tip"><span>
            <?php echo $this->translate("No results were found.") ?></span>
        </div>
    <?php endif; ?>
    <?php echo $this->paginationControl($this->paginator); ?>
    <?php //echo $this->paginationControl($this->paginator, null, null, array('query' => $this->formValues, 'pageAsQuery' => true)); ?>
</div>
<br />

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete')); ?>" onSubmit="return multiDelete()">

        <table class='admin_table seaocore_admin_table' width="100%">
            <thead>
                <tr>
                    <th><input onclick="selectAll()" type='checkbox' class='checkbox'></th>

                    <?php $class = ( $this->order == 'event_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('event_id', 'DESC');"><?php echo $this->translate('ID'); ?></a></th>

    <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"  align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Title'); ?></a></th>

                    <?php if ($this->contentType == 'All' || $this->contentType == 'user' || $this->contentType == 0): ?>
                        <?php $class = ( $this->order == 'username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                        <th class="<?php echo $class ?>"  align="left" ><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate('Owner'); ?></a></th>
                    <?php else : ?>
                        <?php $class = ( $this->order == 'username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                        <th class="<?php echo $class ?>"  align="left" ><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate('Parent Title'); ?></a></th>
                    <?php endif; ?>

                    <?php if(!Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>
                        <?php $class = ( $this->order == 'member_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                        <th class="<?php echo $class ?> admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('member_count', 'DESC');"><?php echo $this->translate('Guests'); ?></a></th>                
                    <?php endif; ?>
                    <?php $class = ( $this->order == 'view_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('view_count', 'DESC');"><?php echo $this->translate('Views'); ?></a></th>

                    <?php $class = ( $this->order == 'comment_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('comment_count', 'DESC');"><?php echo $this->translate('Comments'); ?></a></th>

                    <?php $class = ( $this->order == 'like_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('like_count', 'DESC');"><?php echo $this->translate('Likes'); ?></a></th>

                    <?php $class = ( $this->order == 'review_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('review_count', 'DESC');"><?php echo $this->translate('Reviews'); ?></a></th>
                    <!--PACKAGE ENABLED THEN ADDED SOME MORE COLUMNS-->
                    <?php if($hasPackageEnable):?>
                    <th align="left"  title="<?php echo $this->translate('Package'); ?>" ><?php echo $this->translate('Package')  ?></th>
                    <th align="left"> <?php echo $this->translate('Status'); ?> </th>
                    <th align="left" title="<?php echo $this->translate('Payment'); ?>"><?php echo $this->translate('Payment')  ?></th>
					<?php endif; ?>
                    <?php $class = ( $this->order == 'featured' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> admin_table_centered"  title="<?php echo $this->translate('Featured'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'ASC');"><?php echo $this->translate('F'); ?></a></th>

                    <?php $class = ( $this->order == 'sponsored' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> admin_table_centered" title="<?php echo $this->translate('Sponsored'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('sponsored', 'DESC');"><?php echo $this->translate('S'); ?></a></th>
                    <?php $class = ( $this->order == 'newlabel' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> admin_table_centered"  title="<?php echo $this->translate('New'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('newlabel', 'ASC');"><?php echo $this->translate('N'); ?></a></th>

                    <?php $class = ( $this->order == 'approved' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> admin_table_centered" title="<?php echo $this->translate('Approved'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('approved', 'ASC');"><?php echo $this->translate('A'); ?></a></th>                  
    <?php $class = ( $this->order == 'creation_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate('Creation Date'); ?></a></th>

                    <!--PACKAGE BASED CHECKS-->                   
                    <?php if($hasPackageEnable):?>
						<th align="left" title="<?php echo $this->translate('Package Expiration Date'); ?>"><?php echo $this->translate('Expiration Date')  ?></th>
					<?php endif;?>
                    <th class="<?php echo $class ?>"  class='admin_table_centered'><?php echo $this->translate('Options'); ?></th>
                </tr>
            </thead>

            <tbody>
                <?php if (count($this->paginator) && !empty($this->tempEventCount)): ?>
                    <?php foreach ($this->paginator as $item): ?> 
                        <tr>

                            <td><input name='delete_<?php echo $item->event_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->event_id ?>"/></td>

                            <td><?php echo $item->event_id ?></td>

                            <td class='admin_table_bold' style="white-space:normal;" title="<?php echo $this->translate($item->getTitle()) ?>">
                                <a href="<?php echo $this->url(array('event_id' => $item->event_id, 'slug' => $item->getSlug()), "siteevent_entry_view") ?>"  target='_blank'>
                            <?php echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), 10)) ?></a>
                            </td>

                            <?php if ($this->contentType == 'All' || $this->contentType == 'user' || $this->contentType == 0): ?>
                                <td class='admin_table_bold' title="<?php echo $item->getOwner()->getTitle() ?>"> <?php echo $this->htmlLink($item->getOwner()->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getOwner()->getTitle(), 10), array('target' => '_blank')) ?>
                                </td>
                            <?php else : ?>
                                <?php $parentItem = Engine_Api::_()->getItem($item->getParent()->getType(), $item->getParent()->getIdentity()); ?>
                                <td class='admin_table_bold' title="<?php echo $parentItem->getTitle() ?>"> <?php echo $this->htmlLink($parentItem->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($parentItem->getTitle(), 10), array('target' => '_blank')) ?>
                                </td>
                            <?php endif; ?>
                                
                            <?php if(!Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>
                                <td align="center" class="admin_table_centered"><?php echo $item->member_count ?></td>
                            <?php endif; ?>

                            <td align="center" class="admin_table_centered"><?php echo $item->view_count ?></td>
                            <td align="center" class="admin_table_centered"><?php echo $item->comment_count ?></td>
                            <td align="center" class="admin_table_centered"><?php echo $item->like_count ?></td>
                            <td align="center" class="admin_table_centered"><?php echo $item->review_count ?></td>

         <?php if($hasPackageEnable): ?>
								<td align="left">		<?php  echo $this->htmlLink(
					array('route' => 'admin_default', 'module' => 'siteeventpaid', 'controller' => 'package', 'action' => 'packge-detail', 'id' => $item->package_id), $this->translate(ucfirst(Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getPackage()->title, 10))), array('class' => 'smoothbox','title'=>ucfirst($item->getPackage()->title)));  ?></td>
                <td align="left"><?php echo $item->getEventStatus(); ?></td>
                <td align="center" class="admin_table_centered">
                    <?php if(!$item->getPackage()->isFree()):  ?>
                      <?php if($item->status=="initial"):
                          echo $this->translate("No");
                      elseif($item->status=="active"):
                           echo $this->translate("Yes");
                          else:
                             echo $this->translate(ucfirst($item->status));
                            endif;
                              ?>
                  <?php else:?>
                  <?php echo $this->translate("NA (Free)"); ?>
                  <?php endif ?>
                </td>
          <?php //else:?>
<!--                  <td class="admin_table_centered"><?php //echo '-';?></td>
                  <td class="admin_table_centered"><?php //echo '-';?></td>
                  <td class="admin_table_centered"><?php //echo '-';?></td>-->
         <?php endif; ?>
                            <?php if ($item->featured == 1): ?> 
                                <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteevent', 'controller' => 'general', 'action' => 'featured', 'event_id' => $item->event_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/featured.gif', '', array('title' => $this->translate('Make Un-featured')))) ?></td>
                            <?php else: ?>
                                <td align="center" class="admin_table_centered"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteevent', 'controller' => 'general', 'action' => 'featured', 'event_id' => $item->event_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/unfeatured.gif', '', array('title' => $this->translate('Make Featured')))) ?></td>
                            <?php endif; ?>

                            <?php if ($item->sponsored == 1): ?>
                                <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteevent', 'controller' => 'general', 'action' => 'sponsored', 'event_id' => $item->event_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/sponsored.png', '', array('title' => $this->translate('Make Unsponsored')))); ?></td>
                                <?php else: ?>
                                <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteevent', 'controller' => 'general', 'action' => 'sponsored', 'event_id' => $item->event_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/unsponsored.png', '', array('title' => $this->translate('Make Sponsored')))); ?>
                                <?php endif; ?>   

                            <?php if ($item->newlabel == 1): ?> 
                                <td align="center" class="admin_table_centered"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteevent', 'controller' => 'general', 'action' => 'newlabel', 'event_id' => $item->event_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/icons/new.png', '', array('title' => $this->translate('Remove New Label')))) ?></td>
                            <?php else: ?>
                                <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteevent', 'controller' => 'general', 'action' => 'newlabel', 'event_id' => $item->event_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/icons/new-disable.png', '', array('title' => $this->translate('Set New Label')))) ?></td>
                            <?php endif; ?>

                            <?php if ($item->approved == 1): ?>
                                <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteevent', 'controller' => 'general', 'action' => 'approved', 'event_id' => $item->event_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => $this->translate('Make Dis-Approved')))) ?></td>
                            <?php else: ?>
                                <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteevent', 'controller' => 'general', 'action' => 'approved', 'event_id' => $item->event_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => $this->translate('Make Approved')))) ?></td>
                            <?php endif; ?>              

                            <td><?php echo $this->translate(gmdate('M d,Y, g:i A', strtotime($item->creation_date))) ?></td>                                 
                            <!--PACKAGE BASED CHECKS-->
                            <?php if($hasPackageEnable):?>
                            <td align="left" ><?php echo $item->getExpiryDate() ?></td>
                                  <?php //else: ?>
                            <!--<td class="admin_table_centered"><?php //echo '-'; ?></td>-->
                            <?php endif; ?>
                            
                            <td class='admin_table_options'>
                                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteevent', 'controller' => 'manage', 'action' => 'detail', 'id' => $item->event_id), $this->translate('details'), array('class' => 'smoothbox')) ?> |
                                <a href="<?php echo $this->url(array('event_id' => $item->event_id, 'slug' => $item->getSlug()), "siteevent_entry_view") ?>"  target='_blank'><?php echo $this->translate('view'); ?></a> |
                                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteevent', 'controller' => 'general', 'action' => 'change-owner', 'event_id' => $item->event_id), $this->translate('change owner'), array('class' => 'smoothbox')) ?>
                               <!--PACKAGE BASED CHECKS-->
                              <?php  if ($hasPackageEnable) : ?>
                                  |
                                  <?php echo $this->htmlLink(array('route' => "siteevent_package", 'action' => 'update-package', 'event_id' => $item->event_id), $this->translate('edit package'), array(
                                    'target' => '_blank',
                                  )) ?>   
                                <?php if(Engine_Api::_()->siteeventpaid()->canAdminShowRenewLink($item->event_id)):?> |
                                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteevent', 'controller' => 'general', 'action' => 'renew', 'event_id' => $item->event_id), $this->translate('renew'), array(
                                  'class' => 'smoothbox',
                                  )) ?>
                                <?php endif; ?>
                              <?php endif;?>
                              |
                              <?php echo $this->htmlLink(array('route' => 'siteevent_specific', 'action' => 'edit', 'event_id' => $item->event_id), $this->translate('edit'), array('target' => '_blank')) ?>                             
                                |
                                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteevent', 'controller' => 'general', 'action' => 'delete', 'event_id' => $item->event_id), $this->translate('delete'), array('class' => 'smoothbox')) ?>
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
<?php endif; ?>

<div id="thankYou" style="display:none;">
    <div>
        <div id="showMessage_featured" class="siteevent_manage_msg" style="display:none;"><?php echo $this->translate("This event has already been marked as Featured. If you mark it as New, then its Featured marker will be automatically removed. Click on 'OK' button to mark it as New."); ?></div>
        <div id="showMessage_new" class="siteevent_manage_msg" style="display:none;"><?php echo $this->translate("This event has already been marked as New. If you mark it as Featured, then its New marker will be automatically removed. Click on 'OK' button to mark it as Featured."); ?></div>
        <div id="hidden_url" style="display:none;" ></div>
        <br />
        <button onclick="continueSetLabel();"><?php echo $this->translate('Ok'); ?></button> <?php echo $this->translate('or'); ?>
        <a onclick="closeThankYou();" href="javascript:void(0);"> <?php echo $this->translate('cancel'); ?></a></div>
</div>			
</div>

<script type="text/javascript">

    function addOptions(element_value, element_type, element_updated, domready) {

        var element = $(element_updated);
        if (domready == 0) {
            switch (element_type) {
                case 'cat_dependency':
                    $('subcategory_id' + '-wrapper').style.display = 'none';
                    clear($('subcategory_id'));
                    $('subcategory_id').value = 0;

                case 'subcat_dependency':
                    $('subsubcategory_id' + '-wrapper').style.display = 'none';
                    clear($('subsubcategory_id'));
                    $('subsubcategory_id').value = 0;
            }
        }

        if (element_value <= 0)
            return;

        var url = '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'general', 'action' => 'categories'), "admin_default", true); ?>';
        en4.core.request.send(new Request.JSON({
            url: url,
            data: {
                format: 'json',
                element_value: element_value,
                element_type: element_type
            },
            onSuccess: function(responseJSON) {
                var categories = responseJSON.categories;
                var option = document.createElement("OPTION");
                option.text = "";
                option.value = 0;
                element.options.add(option);
                for (i = 0; i < categories.length; i++) {
                    var option = document.createElement("OPTION");
                    option.text = categories[i]['category_name'];
                    option.value = categories[i]['category_id'];
                    element.options.add(option);
                }

                if (categories.length > 0)
                    $(element_updated + '-wrapper').style.display = 'block';
                else
                    $(element_updated + '-wrapper').style.display = 'none';

                if (domready == 1) {
                    var value = 0;
                    if (element_updated == 'category_id') {
                        value = search_category_id;
                    } else if (element_updated == 'subcategory_id') {
                        value = search_subcategory_id;
                    } else {
                        value = search_subsubcategory_id;
                    }
                    $(element_updated).value = value;
                }
            }

        }), {'force': true});
    }

    function clear(element)
    {
        for (var i = (element.options.length - 1); i >= 0; i--) {
            element.options[ i ] = null;
        }
    }

    var search_category_id, search_subcategory_id, search_subsubcategory_id;
    window.addEvent('domready', function() {

        search_category_id = '<?php echo $this->category_id ? $this->category_id : 0 ?>';

        if (search_category_id != 0) {
            search_subcategory_id = '<?php echo $this->subcategory_id ? $this->subcategory_id : 0 ?>';

            addOptions(search_category_id, 'cat_dependency', 'subcategory_id', 1);

            if (search_subcategory_id != 0) {
                search_subsubcategory_id = '<?php echo $this->subsubcategory_id ? $this->subsubcategory_id : 0 ?>';
                addOptions(search_subcategory_id, 'subcat_dependency', 'subsubcategory_id', 1);
            }
        }
    });
    
    function addHostText(host_type) {
        if(host_type == '' || host_type == null) {
            $('host_title').style.display = 'none';
        }
        else {
            $('host_title').style.display = 'block';
        }    
    }

</script>

<?php
$dateFormat = $this->locale()->useDateLocaleFormat();
$calendarFormatString = trim(preg_replace('/\w/', '$0/', $dateFormat), '/');
$calendarFormatString = str_replace('y', 'Y', $calendarFormatString);
?>
<script type="text/javascript">
    seao_dateFormat = '<?php echo $this->locale()->useDateLocaleFormat(); ?>';
    var showMarkerInDate = "<?php echo $this->showMarkerInDate ?>";
    en4.core.runonce.add(function()
    {
        en4.core.runonce.add(function init()
        {
            monthList = [];
            myCal = new Calendar({'start_cal[date]': '<?php echo $calendarFormatString; ?>', 'end_cal[date]': '<?php echo $calendarFormatString; ?>'}, {
                classes: ['event_calendar'],
                pad: 0,
                direction: 0
            });
        });
    });

    var cal_starttime_onHideStart = function() {
        if (showMarkerInDate == 0)
            return;
        var cal_bound_start = seao_getstarttime(document.getElementById('startdate-date').value);
        // check end date and make it the same date if it's too
        cal_endtime.calendars[0].start = new Date(cal_bound_start);
        // redraw calendar
        cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
        cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
    }
    var cal_endtime_onHideStart = function() {
        if (showMarkerInDate == 0)
            return;
        var cal_bound_start = seao_getstarttime(document.getElementById('endtime-date').value);
        // check start date and make it the same date if it's too
        cal_starttime.calendars[0].end = new Date(cal_bound_start);
        // redraw calendar
        cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
        cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);
    }

    en4.core.runonce.add(function() {
        cal_starttime_onHideStart();
        cal_endtime_onHideStart();
    });

    window.addEvent('domready', function() {
        if ($('starttime-minute') && $('endtime-minute')) {
            $('starttime-minute').destroy();
            $('endtime-minute').destroy();
        }
        if ($('starttime-ampm') && $('endtime-ampm')) {
            $('starttime-ampm').destroy();
            $('endtime-ampm').destroy();
        }
        if ($('starttime-hour') && $('endtime-hour')) {
            $('starttime-hour').destroy();
            $('endtime-hour').destroy();
        }

        if ($('calendar_output_span_starttime-date')) {
            $('calendar_output_span_starttime-date').style.display = 'none';
        }

        if ($('calendar_output_span_endtime-date')) {
            $('calendar_output_span_endtime-date').style.display = 'none';
        }

        if ($('starttime-date')) {
            $('starttime-date').setAttribute('type', 'text');
        }

        if ($('endtime-date')) {
            $('endtime-date').setAttribute('type', 'text');
        }

    });
</script>