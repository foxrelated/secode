<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">

  var SortablesInstance;
  window.addEvent('load', function() {
    SortablesInstance = new Sortables('menu_list', {
      clone: true,
      constrain: false,
      handle: '.item_label',
      onComplete: function(e) {
        reorder(e);
      }
    });
  });

  var reorder = function(e) {
     var menuitems = e.parentNode.childNodes;
     var ordering = {};
     var i = 1;
     for (var menuitem in menuitems)
     {
       var child_id = menuitems[menuitem].id;

       if ((child_id != undefined))
       {
         ordering[child_id] = i;
         i++;
       }
     }
    ordering['format'] = 'json';

    // Send request
    var url = '<?php echo $this->url(array('action' => 'order')) ?>';
    var request = new Request.JSON({
      'url' : url,
      'method' : 'POST',
      'data' : ordering,
      onSuccess : function(responseJSON) {
      }
    });

    request.send();
  }
</script>

<h2><?php echo "Advanced Search Plugin"; ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render()?>
  </div>
<?php endif; ?>

<h3><?php echo "Manage Modules for Advanced Search"; ?></h3>
    <p><?php echo "Below, you will be able to choose the following related to advance search for the various content modules on your website:"; ?></p><br />
    <?php echo "1) Create Tab on Search Page: The search page enables users to do a universal site-wide search as well as content specific search. Content specific search will be possible for a content type if you enable the Tab for it. If tab is enabled for a content type, then users will be able to do a simple search over its items as well as detailed, advanced search.";?><br />
   <?php echo "2) Add Link in Advanced Search Box: If this is enabled for a content type, then while typing or clicking on the Advanced Search Box, users will see a link like: 'Find all Pages with SEARCH_TERM' in the auto-suggest. Upon clicking this, users will be directed to the content type’s tab on search page with intelligent display of search term’s results for that content." ?><br />
   <?php echo "<b>Note:</b> To enable this for a content type, you must enable “Create Tab on Search Page” for it." ?><br />
   <?php echo "3) Show in Search Results: If this is enabled for a content type, then its items will appear in search results of advanced search box as well as in the results on search page. Thus, this enables you to choose content types over which users will be able to search in advanced search. You can enable search over <a href='http://www.socialengineaddons.com/catalog/1/plugins' target='_blank'>SocialEngineAddOns Plugins</a>, official SocialEngine plugins as well as 3rd-party content plugins." ?><br /><br />
   <?php echo "Drag and Drop the content types to reorder their sequence by assigning higher position to the content types that are more important for your community. You can also add new content type using ‘Add a Content Type’ link. Any 3rd-party plugin can also be added here for advanced search." ?><br />


<br />

<div class="tip">
  <span>
    <?php echo "Note: Recommended number of content types for which 'Add Link in Advanced Search Box' should be enabled is 4. If you enable this for more than 4 content types, then there might be UI issues." ?>
  </span>
</div>

<div>
  <a href="<?php echo $this->url(array('action' =>'add-content')) ?>" class="buttonlink seaocore_icon_add" title="<?php echo 'Add a Content Type';?>"><?php echo 'Add a Content Type';?></a>
</div><br />

<div class="seaocore_admin_order_list">
	 <div class="list_head">
     <div style="width:20%">
	     <?php echo "Content Module";?>
	   </div>
	   <div style="width:20%">
	     <?php echo "Title";?>
	  </div>
	   <div style="width:10%" class="admin_table_centered">
	     <?php echo "Create Tab on Search Page";?>
	   </div>   
	   <div style="width:20%" class="admin_table_centered">
	     <?php echo "Add Link in Advanced Search Box";?>
	   </div>   
    <div style="width:10%" class='admin_table_centered'>
	    <?php echo "Show in Search Results";?>
	   </div>
	   <div style="width:10%">
	     <?php echo "Options";?>
	   </div>
	 </div>
	 <ul id='menu_list'>
		  <?php foreach ($this->paginator as $item) : ?>
			   <?php if($item->resource_type == 'feedback'):?>
				    <?php $version = Engine_Api::_()->getApi('core','siteadvsearch')->getModuleVersion($item->resource_type);?>
				    <?php if($version < '4.8.0'):?>
					     <?php continue;?>
				    <?php endif;?>
			   <?php endif;?>
			   <?php if($item->resource_type == 'document'):?>
				    <?php $version = Engine_Api::_()->getApi('core','siteadvsearch')->getModuleVersion($item->resource_type);?>
				    <?php if($version < '4.8.0'):?>
					     <?php continue;?>
				    <?php endif;?>
			   <?php endif;?>
			   <li id="content_<?php echo $item->resource_type ?>" class="admin_table_bold item_label">
				    <input type='hidden'  name='order[]' value='<?php echo $item->content_id; ?>'>
        <div style="width:20%;" class='admin_table_bold'>
          <?php if(!empty($item->listingtype_id)):?>
            <?php echo 'Multiple Listing Types'; ?>
          <?php else:?>
            <?php echo $item->module_title; ?>
          <?php endif;?>
        </div>
        <div style="width:20%;">
          <b><?php echo $item->resource_title; ?></b>
        </div>
				    <div style="width:10%;" class='admin_table_centered'>
                                        <?php if($item->resource_type == 'sitehashtag_hashtag'):?>
                                        <span title="This feature is not applicable for Hashtag Plugin">NA</span>
                                        <?php  else:?>
					     <?php echo ( $item->content_tab ? $this->htmlLink(array('route' => 'siteadvsearch_admin_general','action' => 'show-tab','content_id' => $item->content_id,'show' => 0), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Siteadvsearch/externals/images/enabled1.gif',
'', array('title' => 'Disable filtering over this content type')), array())  :
$this->htmlLink(array('route' => 'siteadvsearch_admin_general','action' => 'show-tab','content_id' => $item->content_id,'show' => '1'),
$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Siteadvsearch/externals/images/enabled0.gif', '', array('title' =>
'Enable filtering over this content type'))) ) ?>
                                        <?php endif;?>
				    </div>
				    <div style="width:20%;" class='admin_table_centered'>
					     <?php if($item->content_tab && $item->main_search):?>
						      <?php echo $this->htmlLink(array('route' => 'siteadvsearch_admin_general','action' => 'show-content-search','content_id' => $item->content_id,'show' => 0), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Siteadvsearch/externals/images/enabled1.gif',
'', array('title' => 'Disable content type from auto suggest')), array());?>
					     <?php else:?>
						      <?php echo $this->htmlLink(array('route' => 'siteadvsearch_admin_general','action' => 'show-content-search','content_id' => $item->content_id,'show' => '1'),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Siteadvsearch/externals/images/enabled0.gif', '', array('onclick' => "showMessage('".$item->content_tab."', '".$item->content_id."'); return false;",'title' =>
'Enable content type from auto suggest')));?>
					     <?php endif;?>
				    </div>
        <div style="width:10%;" class='admin_table_centered'>
                    <?php if($item->resource_type == 'sitehashtag_hashtag'):?>
                                        <span title="This feature is not applicable for Hashtag Plugin">NA</span>
                                        <?php  else:?>
					     <?php if($item->enabled):?>
						      <?php echo $this->htmlLink(array('route' => 'siteadvsearch_admin_general','action' => 'enable-search','content_id' => $item->content_id,'show' => 0), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Siteadvsearch/externals/images/enabled1.gif',
'', array('title' => 'Disable content type from search result')), array());?>
					     <?php else:?>
            <?php echo $this->htmlLink(array('route' => 'siteadvsearch_admin_general','action' => 'enable-search','content_id' => $item->content_id,'show' => 1), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Siteadvsearch/externals/images/enabled0.gif',
    '', array('title' => 'Enable content type from search result')), array());?>
          <?php endif;?>
                                 <?php endif;?>
				    </div>
				    <div style="width:10%;">          
					     <a href='javascript:void(0);' onclick="editContentType('<?php echo $item->content_id;?>');"><?php echo "Edit" ?></a>
					     <?php if(empty($item->default)):?>
						    | <a href='<?php echo $this->url(array('action' => 'delete-content','content_id' => $item->content_id), 'siteadvsearch_admin_general', true) ?>' class="smoothbox"><?php echo "Delete"; ?>
						</a>
					     <?php endif;?>
				    </div>
			   </li>
		  <?php endforeach; ?>
	 </ul>
</div>

<script type="text/javascript">
	function showMessage(content_tab, content_id) {
    if(content_tab == 0) {
      alert('You can enable the content type to be displayed to users in the main search box after enabhe content type from "Enabled" option.');
      return;
    }
    else {
      window.location.href= en4.core.baseUrl+'admin/siteadvsearch/manage/show-content-search/content_id/'+content_id + '/show/' + 1;
    }
	}
 
 function editContentType(content_id) {
   var url = en4.core.baseUrl+'admin/siteadvsearch/manage/edit-content/content_id/'+content_id;
   Smoothbox.open(url);
 }
 
</script>