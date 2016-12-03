<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit_tabs.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	$front = Zend_Controller_Front::getInstance();
	$module = $front->getRequest()->getModuleName();
	$controller = $front->getRequest()->getControllerName();
	$action = $front->getRequest()->getActionName();
  $activeMenu='';
  if($module == 'sitepage' && $controller == 'insights' && $action == 'index'){
    $activeMenu='sitepage_dashboard_insights';
  }
?>
<?php $dashboard_navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_dashboard',  array(),$activeMenu); ?>
<?php 
//GET SITEPAGE OBJECT
$sitepage = Engine_Api::_()->getItem('sitepage_page', $this->page_id);

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage_dashboard.css');

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');

$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/core.js');

include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl'; ?>

<?php
$this->headScript()
		->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
		->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
		->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
		->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
		->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
		->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<?php $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.showurl.column', 1); ?>
<?php $edit_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.edit.url', 0); ?>
<style type="text/css">
	.seaocore_db_tabs .selected >a{
		font-weight : bold;
		background-color: transparent;
		color:#444;
	}
</style>
<div class="seaocore_db_tabs">
  <ul class="">
    <?php $count = 0;
      foreach( $dashboard_navigation as $item ):
        $count++;
        $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
        'reset_params', 'route', 'module', 'controller', 'action', 'type',
        'visible', 'label', 'href')));

        if(!isset($attribs['active'])) {
          $attribs['active'] = false;
        }

        if ($module == 'sitepagelikebox' && $controller == 'index' && $action == 'like-box' && $attribs['class'] == 'ajax_dashboard_enabled menu_sitepage_dashboard sitepage_dashboard_marketing') {
					$attribs['active'] = 1;
        } elseif($module == 'sitepagemember' && $controller == 'index' && $action == 'create-announcement' && $attribs['class'] == 'ajax_dashboard_enabled menu_sitepage_dashboard sitepage_dashboard_announcements') {
					$attribs['active'] = 1;
        } elseif($module == 'sitepagemember' && $controller == 'index' && $action == 'edit-announcement' && $attribs['class'] == 'ajax_dashboard_enabled menu_sitepage_dashboard sitepage_dashboard_announcements') {
					$attribs['active'] = 1;
        } elseif($module == 'sitepage' && $controller == 'dashboard' && $action == 'edit-location' && $attribs['class'] == 'menu_sitepage_dashboard sitepage_dashboard_alllocation') {
					$attribs['active'] = 1;
        } elseif($module == 'sitepageintegration' && $controller == 'index' && $action == 'index' && $attribs['class'] == 'ajax_dashboard_enabled menu_sitepage_dashboard sitepage_dashboard_getstarted') {
					$attribs['active'] = 1;
        }
      ?>
			<li<?php echo($attribs['active']?' class="selected"':'')?>>
				<?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
			</li>
    <?php endforeach; ?>
  </ul>

  <div class="dashboard_info">
    <div class="dashboard_info_image">
<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), $this->itemPhoto($sitepage, 'thumb.profile')) ?>
    </div>
    <center>
      <span>
    <?php if ($sitepage->declined == 0): ?>
      <?php if ($sitepage->featured == 1): ?>
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
  <?php endif; ?>
  <?php if ($sitepage->sponsored == 1): ?>
    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
  <?php endif; ?>
  <?php if (empty($sitepage->approved) && empty($sitepage->declined)): ?>
    <?php $approvedtitle = 'Not approved';
    if (empty($sitepage->aprrove_date)): $approvedtitle = "Approval Pending";
    endif; ?>
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_approved0.gif', '', array('class' => 'icon', 'title' => $this->translate($approvedtitle))) ?>
  <?php endif; ?>
  <?php if ($sitepage->closed): ?>
    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>
  <?php endif; ?>
<?php endif; ?>
      <?php if ($sitepage->declined == 1): ?>
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/declined.gif', '', array('class' => 'icon', 'title' => $this->translate('Declined'))) ?>
<?php endif; ?>
      </span>
    </center>

<?php if (Engine_Api::_()->sitepage()->hasPackageEnable()): ?>
      <div>
        <b><?php echo $this->translate('Package: ') ?></b>
        <a href='<?php echo $this->url(array("action" => "detail", 'id' => $sitepage->package_id), 'sitepage_packages', true) ?>' onclick="owner(this);return false;" title="<?php echo $this->translate(ucfirst($sitepage->getPackage()->title)) ?>"><?php echo $this->translate(ucfirst($sitepage->getPackage()->title)); ?></a>
      </div>
  <?php if (!$sitepage->getPackage()->isFree()): ?>
        <div>
          <b><?php echo $this->translate('Payment: ') ?></b>
          <?php
          if ($sitepage->status == "initial"):
            echo $this->translate("Not made");
          elseif ($sitepage->status == "active"):
            echo $this->translate("Yes");
          else:
            echo $this->translate(ucfirst($sitepage->status));
          endif;
          ?>
        </div>
  <?php endif ?>
<?php endif ?>
    <div>
      <b><?php echo $this->translate('Status: ') . Engine_Api::_()->sitepage()->getPageStatus($sitepage) ?></b>
    </div>
<?php if (!empty($sitepage->aprrove_date)): ?>
      <div style="color: chocolate">
  <?php echo $this->translate('Approved ') . $this->timestamp(strtotime($sitepage->aprrove_date)) ?>
      </div>
  <?php if (Engine_Api::_()->sitepage()->hasPackageEnable()): ?>
        <div style="color: green;">
    <?php
    $expiry = Engine_Api::_()->sitepage()->getExpiryDate($sitepage);
    if ($expiry !== "Expired" && $expiry !== $this->translate('Never Expires'))
      echo $this->translate("Expiration Date: ");
    echo $expiry;
    ?>
        </div>
  <?php endif; ?>
<?php endif ?>


<?php if (Engine_Api::_()->sitepage()->canShowPaymentLink($sitepage->page_id)): ?>
      <div class="tip center mtop5">
        <span class="db_payment_link">
          <a href='javascript:void(0);' onclick="submitSession(<?php echo $sitepage->page_id ?>)"><?php echo $this->translate('Make Payment'); ?></a>
          <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitepage_session_payment', true) ?>">
            <input type="hidden" name="page_id_session" id="page_id_session" />
          </form>
        </span>
      </div>
<?php endif; ?>
<?php if (Engine_Api::_()->sitepage()->canShowRenewLink($sitepage->page_id)): ?>
      <div class="tip mtop5">
        <span style="margin:0px;"> <?php echo $this->translate("Please click "); ?>
          <a href='javascript:void(0);' onclick="submitSession(<?php echo $sitepage->page_id ?>)"><?php echo $this->translate('here'); ?></a><?php echo $this->translate(' to renew page.'); ?>
          <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitepage_session_payment', true) ?>">
            <input type="hidden" name="page_id_session" id="page_id_session" />
          </form>
        </span>
      </div>
<?php endif; ?>
  </div>
</div>
<?php if (Engine_Api::_()->sitepage()->canShowPaymentLink($sitepage->page_id)): ?>
  <div class="sitepage_edit_content">
    <div class="tip">
      <span>
  <?php echo $this->translate('The package for your Page requires payment. You have not fulfilled the payment for this Page.'); ?>
        <a href='javascript:void(0);' onclick="submitSession(<?php echo $sitepage->page_id ?>)"><?php echo $this->translate('Make payment now!'); ?></a>
        <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitepage_session_payment', true) ?>">
          <input type="hidden" name="page_id_session" id="page_id_session" />
        </form>
      </span>
    </div>
  </div>
<?php endif; ?>
<?php if (Engine_Api::_()->sitepage()->canShowRenewLink($sitepage->page_id)): ?>
  <div class="sitepage_edit_content">
    <div class="tip">
      <span>
  <?php if ($sitepage->expiration_date <= date('Y-m-d H:i:s')): ?>
    <?php echo $this->translate("Your package for this Page has expired and needs to be renewed.") ?>
  <?php else: ?>
    <?php echo $this->translate("Your package for this Page is about to expire and needs to be renewed.") ?>
  <?php endif; ?>
  <?php echo $this->translate(" Click "); ?>
        <a href='javascript:void(0);' onclick="submitSession(<?php echo $sitepage->page_id ?>)"><?php echo $this->translate('here'); ?></a><?php echo $this->translate(' to renew it.'); ?>
        <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitepage_session_payment', true) ?>">
          <input type="hidden" name="page_id_session" id="page_id_session" />
        </form>
      </span>
    </div>
  </div>
<?php endif; ?>


<script type="text/javascript">

en4.core.runonce.add(function() {
var element = $(event.target);
				if( element.tagName.toLowerCase() == 'a' ) {
					element = element.getParent('li');
				}
				
				//element.addClass('<?php //echo $class ?>');
});
		
	if($$('.ajax_dashboard_enabled')) {
		en4.core.runonce.add(function() {
			$$('.ajax_dashboard_enabled').addEvent('click',function(event) {
				var element = $(event.target);
				var show_url = '<?php echo $show_url; ?>';
				var edit_url = '<?php echo $edit_url; ?>';
				var page_id = '<?php echo $this->page_id; ?>';
				event.stop();
				var href = this.href; 
				var ulel=this.getParent('ul');
				$('show_tab_content').innerHTML = '<center><img src="'+en4.core.staticBaseUrl+'application/modules/Sitepage/externals/images/spinner_temp.gif" /></center>'; 
				ulel.getElements('li').removeClass('selected');
				
				if( element.tagName.toLowerCase() == 'a' ) {
					element = element.getParent('li');
				}
				
				element.addClass('selected');
				if (history.pushState) {
					history.pushState( {}, document.title, href );
				}
				
				var request = new Request.HTML({
					'url' : href,
					'method' : 'get',
					'data' : {
						'format' : 'html',
						'is_ajax' : 1
											
					},
					onSuccess :  function(responseTree, responseElements, responseHTML, responseJavaScript)  {
			/*      if (Show_Tab_Selected) {
							$('id_'+ Show_Tab_Selected).set('class', '');
							Show_Tab_Selected = PageId;
						}*/	
					// $('id_' + PageId).set('class', 'selected');
							
						$('show_tab_content').innerHTML = responseHTML; 

                       if($('show_tab_content').getElement('.layout_middle'))
                                                $('show_tab_content').innerHTML = $('show_tab_content').getElement('.layout_middle').innerHTML;
						if (window.InitiateAction) {
							InitiateAction ();
						}

						if (($type(show_url) && show_url == 1) && ($type(edit_url) && edit_url == 1)) {
							ShowUrlColumn(page_id);
						}
						if (window.activ_autosuggest) { 
							activ_autosuggest ();
						}
						
						var e4 = $('page_url_msg-wrapper');
						if($('page_url_msg-wrapper'))
							$('page_url_msg-wrapper').setStyle('display', 'none');
							
						if(typeof cat != 'undefined' && typeof subcatid != 'undefined' && typeof subcatname != 'undefined' && typeof subsubcatid != 'undefined') {
							subcategory(cat, subcatid, subcatname,subsubcatid);
						}

						if (document.getElementById("category_name")) {
							$('category_name').focus();
						}
						en4.core.runonce.trigger();
                                                
                                                if(SmoothboxSEAO){
                                                    SmoothboxSEAO.bind($('show_tab_content'));
                                                }
					}
				});
				request.send();
			});
		});
	}
	
  var Show_Tab_Selected = "<?php echo $this->sitepages_view_menu; ?>";
  function submitSession(id) {
    document.getElementById("page_id_session").value=id;
    document.getElementById("setSession_form").submit();
  }

  function owner(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }
  
  //WORK FOR CLOSING THE FACEBOOK POPUP WHILE LINKING FACEBOOK PAGE
  if (window.opener!= null) {
  
    <?php if (!empty($_GET['redirect_fb'])) : ?>
                window.opener.location.reload(false);
               close();
             
    <?php endif; ?>
}
</script>