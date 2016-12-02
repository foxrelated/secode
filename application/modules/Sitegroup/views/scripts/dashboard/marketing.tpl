<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: marketing.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript" >

    function owner(thisobj) {
        var Obj_Url = thisobj.href;

        Smoothbox.open(Obj_Url);
    }
    
    var showFeedDialogue_FB = function (feedurl) {

        var current_window_url_sitegroup = '<?php echo (_ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->url() ?>';
        activityfeedtype = 'facebook';

        if (history.pushState)
            history.pushState({}, document.title, current_window_url_sitegroup);

        var child_window = window.open(feedurl, 'mywindow', 'width=500,height=500');

    }
</script>
<?php if (empty($this->is_ajax)) : ?>
<div class="generic_layout_container layout_middle">
<div class="generic_layout_container layout_core_content">
    <?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/payment_navigation_views.tpl'; ?>

    <div class="layout_middle">
        <?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/edit_tabs.tpl'; ?>
        <div class="sitegroup_edit_content">
            <div class="sitegroup_edit_header">
                <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($this->sitegroup->group_id, $this->sitegroup->owner_id, $this->sitegroup->getSlug()), $this->translate('VIEW_GROUP')) ?>
                <h3><?php echo $this->translate('Dashboard: ') . $this->sitegroup->title; ?></h3>
            </div>
            <div id="show_tab_content">
            <?php endif; ?>
            <?php $showMessage = true; ?>
            <div class="global_form">
                <div>
                    <div>
                        <h3><?php echo $this->translate('Marketing your Group'); ?></h3>
                        <p class="form-description"><?php echo $this->translate('Below are some effective tools to market your Group and increase its popularity.'); ?></p>
                        <div class="sitegroup_marketing mtop10">
                            <ul class="sitegroup_getstarted">
                                <?php $sitegroupcommunityadEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad'); ?>

                                <?php
                                // check if it is upgraded version
                                $updated_ads = 0;
                                $adversion = null;
                                if ($sitegroupcommunityadEnabled) {
                                    $communityadmodulemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('communityad');
                                    $adversion = $communityadmodulemodule->version;
                                    if ($adversion >= '4.1.5') {
                                        $updated_ads = 1;
                                    }
                                }
                                ?>
<?php if ($sitegroupcommunityadEnabled && $updated_ads): ?>
                                    <li><?php $showMessage = false; ?>
                                        <div class="sitegroup_getstarted_num">
    <?php echo $this->htmlLink(array('route' => 'communityad_listpackage', 'type' => 'sitegroup', 'type_id' => $this->group_id), '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/advertise.png" />') ?>

                                        </div>
                                        <div class="sitegroup_getstarted_des">
                                            <b>
                                                <?php $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 'Advertisement') ?>
    <?php echo $this->htmlLink(array('route' => 'communityad_listpackage', 'type' => 'sitegroup', 'type_id' => $this->group_id), $this->translate("Advertise on %s", $site_title)) ?>
                                            </b>
                                            <p><?php echo $this->translate('Popularize your Group on %s with an attractive ad.', $site_title) ?></p>
                                        </div>
                                    </li>
                                <?php endif; ?>

                                <?php $sitegroupinviteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupinvite'); ?>
<?php if ($sitegroupinviteEnabled && !empty($this->enableInvite)): ?>
                                    <li><?php $showMessage = false; ?>
                                        <div class="sitegroup_getstarted_num">
    <?php echo $this->htmlLink(array('route' => 'sitegroupinvite_invite', 'user_id' => $this->viewer_id, 'sitegroup_id' => $this->group_id), '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/friends.png" />') ?>

                                        </div>
                                        <div class="sitegroup_getstarted_des">
                                            <b>
    <?php echo $this->htmlLink(array('route' => 'sitegroupinvite_invite', 'user_id' => $this->viewer_id, 'sitegroup_id' => $this->group_id), $this->translate('Invite &amp; Promote')) ?>
                                            </b>
                                            <p><?php echo $this->translate('Tell your friends, fans and customers about this group and make it popular.') ?></p>
                                        </div>
                                    </li>
                                <?php endif; ?>
<?php if (!empty($this->enableSendUpdate)): ?>
                                    <li><?php $showMessage = false; ?>
                                        <div class="sitegroup_getstarted_num">
    <?php echo $this->htmlLink(array('route' => 'sitegroup_like', 'group_id' => $this->group_id, 'action' => 'send-update'), '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/message48.png" />', array('onclick' => 'owner(this);return false')) ?>
                                        </div>
                                        <div class="sitegroup_getstarted_des">
                                            <b>
    <?php echo $this->htmlLink(array('route' => 'sitegroup_like', 'group_id' => $this->group_id, 'action' => 'send-update'), $this->translate('Send an Update'), array('onclick' => 'owner(this);return false')) ?>
                                            </b>
                                            <p><?php echo $this->translate('Send updates to people who have liked your Group.') ?></p>
                                        </div>
                                    </li>
                                <?php endif; ?>
<?php if (0 && !empty($this->enableFoursquare)): ?>
                                    <li><?php $showMessage = false; ?>
                                        <div class="sitegroup_getstarted_num">
    <?php echo $this->htmlLink(array('route' => 'sitegroup_dashboard', 'group_id' => $this->group_id, 'action' => 'foursquare'), '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/foursquare.png" />', array('onclick' => 'owner(this);return false')) ?>
                                        </div>
                                        <div class="sitegroup_getstarted_des">
                                            <b>
    <?php echo $this->htmlLink(array('route' => 'sitegroup_dashboard', 'group_id' => $this->group_id, 'action' => 'foursquare'), $this->translate("'Save to foursquare' Button"), array('onclick' => 'owner(this);return false')) ?>
                                            </b>
                                            <p><?php echo $this->translate('This button will enable visitors on your Group to add your place or tip to their foursquare To-Do List.') ?></p>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                <?php $sitegrouplikebox_isActivate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegrouplikebox.isActivate', null); ?>
<?php if (!empty($this->enableLikeBox) && !empty($sitegrouplikebox_isActivate)): ?>
                                    <li><?php $showMessage = false; ?>
                                        <div class="sitegroup_getstarted_num">
    <?php echo $this->htmlLink(array('route' => 'sitegrouplikebox_general', 'group_id' => $this->group_id, 'action' => 'like-box'), '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/likebox.png" />', array()) ?>
                                        </div>
                                        <div class="sitegroup_getstarted_des">
                                            <b>
    <?php echo $this->htmlLink(array('route' => 'sitegrouplikebox_general', 'group_id' => $this->group_id, 'action' => 'like-box'), $this->translate("Promote this Group on your external blogs or websites"), array()) ?>
                                            </b>
                                            <p><?php echo $this->translate("Attract people to your Group and gain popularity by using attractive embeddable badge of your Group which can be shared across the web.", Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title')) ?></p>
                                        </div>
                                    </li>
                                <?php endif; ?>
<?php if ($this->enabletwitter): ?>
                                    <li><?php $showMessage = false; ?>
                                        <div class="sitegroup_getstarted_num">
    <?php echo $this->htmlLink(array('route' => 'sitegroup_dashboard', 'group_id' => $this->group_id, 'action' => 'twitter'), '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitegrouptwitter/externals/images/twitter.png" />', array('onclick' => 'owner(this);return false')) ?>
                                        </div>
                                        <div class="sitegroup_getstarted_des">
                                            <b>
    <?php echo $this->htmlLink(array('route' => 'sitegroup_dashboard', 'group_id' => $this->group_id, 'action' => 'twitter'), $this->translate("Add Twitter Profile Widget"), array('onclick' => 'owner(this);return false')) ?>
                                            </b>
                                            <p><?php echo $this->translate('Attract people and involve them in your conversation on Twitter by displaying your recent tweets.') ?></p>
                                        </div>
                                    </li>
                                <?php endif; ?>

                                <?php
                                $advfeedmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('advancedactivity');

                                if (!empty($this->fblikebox_id) || (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postfbgroup', 1) && !empty($advfeedmodule) && !empty($advfeedmodule->enabled) && $advfeedmodule->version > '4.2.5' )):
                                    ?>
                                    <li><?php $showMessage = false; ?>
                                        <div class="sitegroup_getstarted_num">
    <?php echo $this->htmlLink(array('route' => 'sitegroup_dashboard', 'group_id' => $this->group_id, 'action' => 'facebook', 'fblikebox_id' => $this->fblikebox_id), '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup-facebook.png" />', array('onclick' => 'owner(this);return false')) ?>
                                        </div>
                                        <div class="sitegroup_getstarted_des">
                                            <b>
            <?php
                                                $settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.facebook');
                                                $Api_facebook = Engine_Api::_()->getApi('facebook_Facebookinvite', 'seaocore');
                                                $facebook_userfeed = $Api_facebook->getFBInstance();

                                                if (!empty($settings['appid']) && !empty($settings['secret'])) {
                                                    $FBloginURL = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()
                                                                    ->assemble(array('module' => 'seaocore', 'controller' => 'auth', 'action' => 'facebook'), 'default', true) . '?' . http_build_query(array('redirect_urimain' => urlencode(( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->url() . '?redirect_fb=1'), 'user_managed_groups' => true));

                                                    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('facebook.enable', Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable == 'publish' ? 1 : 0)) {
                                                        $session = new Zend_Session_Namespace();

                                                        $session_userfeed = $facebook_userfeed;
                                                        $fbLoginUrlFinal = '';
                                                        if (!empty($facebook_userfeed)) {

                                                            $fbLoginUrlFinal = '';
                                                            $checksiteIntegrate = true;
                                                            $facebookCheck = new Seaocore_Api_Facebook_Facebookinvite();
                                                            $fb_checkconnection = $facebookCheck->checkConnection(null, $facebook_userfeed);

                                                            if ($session_userfeed && $fb_checkconnection) {
                                                                //$session->fb_checkconnection = true;
                                                                $core_fbenable = Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable;
                                                                $enable_socialdnamodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
                                                                if (('publish' == $core_fbenable || 'login' == $core_fbenable || $enable_socialdnamodule) && (!$fb_checkconnection)) {
                                                                    $checksiteIntegrate = false;
                                                                } else {
                                                                    try {
                                                                        if (!isset($session->fb_canread)) {
                                                                 $permissions = $facebook_userfeed->api("/me/permissions");
                                                                            if (!$facebookCheck->checkPermission('user_managed_groups', $permissions)) {
                                                                                $session->fb_can_managegroups = false;
                                                                            } else {
                                                                                $session->fb_can_managegroups = true;
                                                                            }
                                                                        }
                                                                        
                                                                        if (!$session->fb_can_managegroups) {
                                                                            $checksiteIntegrate = false;
                                                                        }
                                                                    } catch (Exception $e) {
                                                                        $checksiteIntegrate = false;
                                                                    }
                                                                }
                                                            }
                                                            if (!$session_userfeed || !$fb_checkconnection || !$checksiteIntegrate) {
                                                                $fbLoginUrlFinal = $FBloginURL;
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>
                                                <?php if (empty($fbLoginUrlFinal)): ?>
                                                     <?php echo $this->htmlLink(array('route' => 'sitegroup_dashboard', 'group_id' => $this->group_id, 'action' => 'facebook', 'fblikebox_id' => $this->fblikebox_id), $this->translate("Link your Group to Facebook"), array('onclick' => 'owner(this);return false')) ?>
    <?php else: ?>
                                                    <?php echo $this->htmlLink("javascript:void(0);", $this->translate("Link your Group to Facebook"), array('onclick' => 'showFeedDialogue_FB(\'' . $fbLoginUrlFinal . '\')')) ?>
                                                <?php endif; ?>
                                            </b>
                                            <p><?php
                                                $description_fblikebox = '';
                                                if (!empty($advfeedmodule) && !empty($advfeedmodule->enabled) && $advfeedmodule->version > '4.2.5' && !empty($this->fblikebox_id))
                                                    $description_fblikebox = "Publish your Group's updates to your Facebook Group and show your Facebook Group Like Box on your Group.";

                                                else if (!empty($this->fblikebox_id))
                                                    $description_fblikebox = "Show your Facebook Group Like Box on your Group.";


                                                else if (!empty($advfeedmodule) && !empty($advfeedmodule->enabled) && $advfeedmodule->version > '4.2.5')
                                                    $description_fblikebox = "Publish your Group's updates to your Facebook Group.";
                                                echo $this->translate($description_fblikebox)
                                                ?></p>
                                        </div>
                                    </li>
<?php endif; ?>		

                                            <?php if ($showMessage): ?>
                                    <li>
                                        <div class="tip">
                                            <span>
                                                <?php if (Engine_Api::_()->sitegroup()->hasPackageEnable()): ?>
                                                    <?php echo $this->translate("In this package not any effective tools available to market your Group."); ?>
    <?php else: ?>
        <?php echo $this->translate("For this level user not any effective tools available to market your Group."); ?>
                                    <?php endif; ?>
                                            </span>
                                        </div>
                                    </li>
<?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
<?php if (empty($this->is_ajax)) : ?>
            </div>
	  </div>
  </div>
</div>
  </div>
<?php endif; ?>
