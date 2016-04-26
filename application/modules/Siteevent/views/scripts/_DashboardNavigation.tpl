<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _DashboardNavigation.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>

<?php
    $siteevent_dashboard_content = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteevent_dashboard_content');

    $siteevent_dashboard_admin = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteevent_dashboard_admin');

    //TICKET EXTENSION - ADDED BECAUSE OF SALES REPORT TAB, SEND $activeMenu IF SET IN REGISTRY
    $activeMenu =  Zend_Registry::isRegistered('siteeventDashboardMenuActive') ? Zend_Registry::get('siteeventDashboardMenuActive') : '';
    $siteevent_dashboard_ticket = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteevent_dashboard_ticket', array(), $activeMenu);
?>

<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css')->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent_dashboard.css');
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
?>

<?php
$isEnabledPackage = Engine_Api::_()->siteevent()->hasPackageEnable();
$siteevent = $this->siteevent;
$viewer = Engine_Api::_()->user()->getViewer();

$params['event_type_title'] = $this->translate('Events');
$params['dashboard'] = $this->translate('Dashboard');
//SET META TITLE
Engine_Api::_()->siteevent()->setMetaTitles($params);
$siteeventEditDashboard = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventedit.dashboard', 1);

if (empty($siteeventEditDashboard))
    return;

if ($this->TabActive != "edit"):?>
    <?php if(!Zend_Controller_Front::getInstance()->getRequest()->getParam('isajax')):?>
        <?php
            $this->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("siteevent_main");
            include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/navigation_views.tpl';
        ?>
    <?php endif;?>
<?php endif;?>

<?php $this->parentTypeItem = $parentTypeItem = Engine_Api::_()->getItem($this->siteevent->parent_type, $this->siteevent->parent_id);?>

<?php if ($this->parentTypeItem->getType() != 'user'): ?>
    <?php
    $this->item = $item = $this->parentTypeItem;
    $shortType = strtolower($item->getShortType());
    $moduleSiteevent = Engine_Api::_()->getDbtable('modules', 'siteevent');
    if ($this->siteevent->getParent()->getType() != 'user') {
        $this->title = $title = ucfirst(Engine_Api::_()->getApi('settings', 'core')->getSetting("language.phrases.$shortType", $shortType));
    }
    ?>
    <?php
    $primaryTableKey = Engine_Api::_()->getItemtable($this->parentTypeItem->getType())->info('primary');
    $tablePrimaryFieldName = $primaryTableKey[1];
    ?>
    <div class="siteevent_viewevents_head">
            <?php echo $this->htmlLink($this->parentTypeItem->getHref(), $this->itemPhoto($this->parentTypeItem, 'thumb.icon', '', array('align' => 'left'))) ?>
            
        <?php if($this->parentTypeItem->getType() != 'sitereview_listing'):?>    
            <div class="fright">
                    <a href='<?php echo $this->url(array($tablePrimaryFieldName => $this->parentTypeItem->getIdentity()), strtolower($this->parentTypeItem->getModuleName()) . '_edit', true) ?>' class='buttonlink seaocore_icon_edit'><?php echo $this->translate('%s Dashboard', $title); ?></a>
            </div>
        <?php else : 
            $listingtype_id = $this->parentTypeItem->listingtype_id?> 
            <div class="fright">
                <a class='buttonlink seaocore_icon_edit' href='<?php echo $this->url(array('action' => 'edit', 'listing_id' => $this->parentTypeItem->listing_id), "sitereview_specific_listtype_$listingtype_id", true) ?>' ><?php echo $this->translate('%s Dashboard', $title); ?></a>
            </div>
      <?php endif;?>
            
        <h2>	
            <?php echo $this->parentTypeItem->__toString() ?>	
            <?php echo $this->translate('&raquo; '); ?>
            <?php echo $this->translate('Events'); ?>
        </h2>
    </div><br />
<?php endif; ?>
<div class="layout_middle <?php if(Engine_Api::_()->hasModuleBootstrap('spectacular')):?> spectacular_dashboard <?php endif;?>">
    <div class='seaocore_db_tabs'>
        
        <?php if(count($siteevent_dashboard_content)): ?>
            <ul>
                <li class="seaocore_db_head"><h3><?php echo $this->translate("Content"); ?></h3></li>
                <?php
                foreach ($siteevent_dashboard_content as $item):
                    $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                        'reset_params', 'route', 'module', 'controller', 'action', 'type',
                        'visible', 'label', 'href')));
                    if (!isset($attribs['active'])) {
                        $attribs['active'] = false;
                    }
                    ?>
                    <li<?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
                        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <?php if(count($siteevent_dashboard_admin)): ?>
            <ul>
                <li class="seaocore_db_head"><h3><?php echo $this->translate("Admin"); ?></h3></li>
                <?php
                foreach ($siteevent_dashboard_admin as $item):
                    $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                        'reset_params', 'route', 'module', 'controller', 'action', 'type',
                        'visible', 'label', 'href')));
                    if (!isset($attribs['active'])) {
                        $attribs['active'] = false;
                    }
                    ?>
                    <li<?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
                        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
                    </li>
                <?php endforeach; ?>
            </ul>        
        <?php endif; ?>
        
        <?php if(count($siteevent_dashboard_ticket)): ?>
            <ul>
                <li class="seaocore_db_head"><h3><?php echo $this->translate("Ticketing"); ?></h3></li>
                <?php
                foreach ($siteevent_dashboard_ticket as $item):
                    $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                        'reset_params', 'route', 'module', 'controller', 'action', 'type',
                        'visible', 'label', 'href')));
                    if (!isset($attribs['active'])) {
                        $attribs['active'] = false;
                    }
                    ?>
                    <li<?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
                        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <div class="siteevent_dashboard_info clr">
            <div class="siteevent_dashboard_info_image prelative">
                <?php if ($this->siteevent->newlabel): ?>
                    <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                <?php endif; ?>
                <?php if ($this->siteevent->featured == 1): ?>
                    <i class="siteevent_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
                <?php endif; ?>
                <?php echo $this->htmlLink($this->siteevent->getHref(), $this->itemPhoto($this->siteevent, 'thumb.profile')) ?>
            </div>
            <?php if ($this->siteevent->sponsored == 1): ?>
                <div class="siteevent_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505'); ?>">
                    <?php echo $this->translate('SPONSORED'); ?>                 
                </div>
            <?php endif; ?>
           <?php if ($isEnabledPackage): ?>
      <div>
        <b><?php echo $this->translate('Package: ') ?></b>
        <a href='<?php echo $this->url(array("action" => "detail", 'id' => $this->siteevent->package_id), "siteevent_package", true) ?>' onclick="owner(this);return false;" title="<?php echo $this->translate(ucfirst($this->siteevent->getPackage()->title)) ?>"><?php echo $this->translate(ucfirst($this->siteevent->getPackage()->title)); ?></a>
      </div>
  <?php if (!$this->siteevent->getPackage()->isFree()): ?>
        <div>
          <b><?php echo $this->translate('Payment: ') ?></b>
          <?php
          if ($this->siteevent->status == "initial"):
            echo $this->translate("Not made");
          elseif ($this->siteevent->status == "active"):
            echo $this->translate("Yes");
          else:
            echo $this->translate(ucfirst($this->siteevent->status));
          endif;
          ?>
        </div>
  <?php endif ?>
	<div>
		<b><?php echo $this->translate('Status: ') . $this->siteevent->getEventStatus() ?></b>
  </div>
	<?php if (!empty($this->siteevent->approved_date) && !empty($this->siteevent->approved)): ?>
				<div style="color: chocolate">
		<?php echo $this->translate('Approved ') . $this->timestamp(strtotime($this->siteevent->approved_date)) ?>
				</div>
		<?php if ($isEnabledPackage && $this->siteevent->expiration_date && $this->siteevent->expiration_date !== "0000-00-00 00:00:00"): ?>
					<div style="color: green;">
                  <?php
                  $expiry = $this->siteevent->getExpiryDate();
                  if ($expiry !== "Expired" && $expiry !== $this->translate('Never Expires')):
                    echo $this->translate("Expiration Date: ");
                  endif;
                  echo $expiry;
                  ?>
					</div>
		<?php endif; ?>
	<?php endif; ?>
	<?php endif; ?>
          <?php if($isEnabledPackage):?>
            <?php if (Engine_Api::_()->siteeventpaid()->canShowPaymentLink($this->siteevent->event_id)): ?>
                  <div class="tip center mtop5">
                    <span class="db_payment_link">
                      <a href='javascript:void(0);' onclick="submitSession(<?php echo $this->siteevent->event_id ?>)"><?php echo $this->translate('Make Payment'); ?></a>
                      <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), "siteevent_session_payment", true) ?>">
                        <input type="hidden" name="event_id_session" id="event_id_session" />
                      </form>
                    </span>
                  </div>
            <?php endif; ?>
            <?php if (Engine_Api::_()->siteeventpaid()->canShowRenewLink($this->siteevent->event_id)): ?>
                  <div class="tip mtop5">
                    <span style="margin:0px;"> <?php echo $this->translate("Please click "); ?>
                      <a href='javascript:void(0);' onclick="submitSession(<?php echo $this->siteevent->event_id ?>)"><?php echo $this->translate('here'); ?></a><?php echo $this->translate(" to renew event."); ?>
                      <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), "siteevent_session_payment", true) ?>">
                        <input type="hidden" name="event_id_session" id="event_id_session" />
                      </form>
                    </span>
                  </div>
            <?php endif; ?>
          <?php endif;?>
        </div> 
    </div>
<?php if($isEnabledPackage):?>
		<?php if (Engine_Api::_()->siteeventpaid()->canShowPaymentLink($this->siteevent->event_id)): ?>
			<div class="siteevent_edit_content o_hidden">
				<div class="tip mbot15">
					<span>
					<?php echo $this->translate("The package for your event requires payment. You have not fulfilled the payment for this event."); ?>
						<a href='javascript:void(0);' onclick="submitSession(<?php echo $this->siteevent->event_id ?>)"><?php echo $this->translate('Make payment now!'); ?></a>
						<form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), "siteevent_session_payment", true) ?>">
							<input type="hidden" name="event_id_session" id="event_id_session" />
						</form>
					</span>
				</div>
			</div>
		<?php endif; ?>

		<?php if (Engine_Api::_()->siteeventpaid()->canShowRenewLink($this->siteevent->event_id)): ?>
			<div class="siteevent_edit_content">
				<div class="tip">
					<span>
			<?php if ($this->siteevent->expiration_date <= date('Y-m-d H:i:s')): ?>
				<?php echo $this->translate("Your package for this event has expired and needs to be renewed.") ?>
			<?php else: ?>
				<?php echo $this->translate("Your package for this event is about to expire and needs to be renewed.") ?>
			<?php endif; ?>
                                            <?php $event_id = $this->siteevent->event_id;?>
			<?php echo $this->translate('%1$Click here%2$s here to renew it.', "<a href='javascript:void(0);' onclick='submitSession( $event_id )'>", '</a>'); ?>
						
						<form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), "siteevent_session_payment", true) ?>">
							<input type="hidden" name="event_id_session" id="event_id_session" />
						</form>
					</span>
				</div>
			</div>
		<?php endif; ?>
<?php endif;?>
<script type="text/javascript">

    en4.core.runonce.add(function() {
        var element = $(event.target);
        if (element.tagName.toLowerCase() == 'a') {
            element = element.getParent('li');
        }
    });

    if ($$('.ajax_dashboard_enabled')) {
        en4.core.runonce.add(function() {
            $$('.ajax_dashboard_enabled').addEvent('click', function(event) {
                var element = $(event.target);
                event.stop();
                var ulel = this.getParent('ul');
                $('global_content').getElement('.siteevent_dashboard_content').innerHTML = '<div class="seaocore_content_loader"></div>';
                ulel.getElements('li').removeClass('selected');

                if (element.tagName.toLowerCase() == 'a') {
                    element = element.getParent('li');
                }

                element.addClass('selected');
                showAjaxBasedContent(this.href);
            });
        });
    }

    function showAjaxBasedContent(url) {

        if (history.pushState) {
            history.pushState({}, document.title, url);
        } else {
            window.location.hash = url;
        }

        en4.core.request.send(new Request.HTML({
            url: url,
            'method': 'get',
            data: {
                format: 'html',
                'isajax': 1
            }, onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                $('global_content').innerHTML = responseHTML;
                Smoothbox.bind($('global_content'));
                en4.core.runonce.trigger();
                if (window.InitiateAction) {
                    InitiateAction();
                }
                 if(SmoothboxSEAO){
                                                    SmoothboxSEAO.bind($('global_content'));
                                                }
            }
        }));
    }

    var requestActive = false;
    window.addEvent('load', function() {
        InitiateAction();
    });

    var InitiateAction = function() {
        formElement = $$('.global_form')[0];
        if (typeof formElement != 'undefined') {
            formElement.addEvent('submit', function(event) {
                if (typeof submitformajax != 'undefined' && submitformajax == 1) {
                    submitformajax = 0;
                    event.stop();
                    Savevalues();
                }
            })
        }
    }

    var Savevalues = function() {
        if (requestActive)
            return;

        requestActive = true;
        var pageurl = $('global_content').getElement('.global_form').action;

        currentValues = formElement.toQueryString();
        $('show_tab_content_child').innerHTML = '<div class="seaocore_content_loader"></div>';
        if (typeof page_url != 'undefined') {
            var param = (currentValues ? currentValues + '&' : '') + 'isajax=1&format=html&page_url=' + page_url;
        }
        else {
            var param = (currentValues ? currentValues + '&' : '') + 'isajax=1&format=html';
        }

        var request = new Request.HTML({
            url: pageurl,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                $('global_content').innerHTML = responseHTML;
                 if(SmoothboxSEAO){
                                                    SmoothboxSEAO.bind($('global_content'));
                                                }
                InitiateAction();
                requestActive = false;
            }
        });
        request.send(param);
    }
    
function submitSession(id) {
	document.getElementById("event_id_session").value=id;
	document.getElementById("setSession_form").submit();
}

function owner(thisobj) {
	var Obj_Url = thisobj.href;
	Smoothbox.open(Obj_Url);
}
</script>
<?php if (Engine_Api::_()->siteevent()->hasTicketEnable()):?>
<script type="text/javascript">
  
var ShowDashboardEventContent = function (EventUrl, show_url, edit_url, event_id, tab_id) {
          $('siteevent_manage_order_content').innerHTML = '<center><img src="' + en4.core.staticBaseUrl + 'application/modules/Siteeventticket/externals/images/spinner_temp.gif" /></center>';
          var request = new Request.HTML({
            'url': EventUrl,
            'method': 'POST',
            'data': {
              'format': 'html',
              'showDashboardEventContent':1,
              'is_ajax': 1
            },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {

              $('siteevent_manage_order_content').innerHTML = responseHTML;

              en4.core.runonce.trigger();
            }

          });

          request.send();
        };

var manage_event_dashboard = function (id, actionName, controller, tempURL) {
  new Fx.Scroll(window).start(0, $('global_wrapper').getCoordinates().top);
  // IT'S THE VARIABLE WHICH SEND TO SITEEVENT CONTROLLERS FOR GETTING REQUIRED RESULT ACCORDING TO REQUEST. WHERE 'actionName' and 'controller' IS THE VARIABLE, WHICH HAVE THE INFORMATION OF SITEEVENT PLUGIN CONTROLLERS AND ACTION.
  var tempEventDeshboardUrl = en4.core.baseUrl + 'siteeventticket/' + controller + '/' + actionName + '/' + 'event_id/<?php echo $this->event_id ?>/menuId/' + id;
  ShowDashboardEventContent(tempEventDeshboardUrl, '', '', '<?php echo $this->event_id ?>', id);
};

var back_to_active_tab = function () {
  new Fx.Scroll(window).start(0, $('global_wrapper').getCoordinates().top);

  var tempEventDeshboardUrl = $$('.seaocore_db_tabs .selected a')[0].href;

  ShowDashboardEventContent(tempEventDeshboardUrl, '', '', '<?php echo $this->event_id ?>');
};
</script>
<?php endif;?>