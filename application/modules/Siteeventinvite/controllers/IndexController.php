<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventinvite
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventinvite_IndexController extends Seaocore_Controller_Action_Standard {

    public function init() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        // PACKAGE BASE PRIYACY START
        $siteeventinviteFbFriendEnabled = Zend_Registry::isRegistered('siteeventinviteFbFriendEnabled') ? Zend_Registry::get('siteeventinviteFbFriendEnabled') : null;
        $event_id = $siteevent_id = $this->_getParam('siteevent_id', null);
        $viewer = Engine_Api::_()->user()->getViewer();

        if (empty($siteeventinviteFbFriendEnabled))
            return $this->_forwardCustom('notfound', 'error', 'core');

        if (!empty($event_id)) {
            $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
            if ($siteevent->getType() !== 'siteevent_event') {
                throw new Event_Model_Exception('This event does not exist.');
            }
            if (!$siteevent->authorization()->isAllowed($viewer, 'invite')) {
                return $this->_forwardCustom('notfound', 'error', 'core');
            }
            
         $occurrence_id = $this->_getParam('occurrence_id', null);
         if(!empty($occurrence_id))
					Zend_Registry::set('occurrence_id', $occurrence_id);
        }
    }

    // Function for view all event which are linked by "suggestion home" widget.
    public function friendseventinviteAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;
        $this->view->isMobile = Engine_Api::_()->seaocore()->isMobile();
        $this->view->inviteevent_id = $inviteevent_id = $this->_getParam('siteevent_id', null);
        $this->view->inviteevent_userid = $inviteevent_userid = $this->_getParam('user_id', null);
        $this->view->occurrence_id = $occurrence_id = $this->_getParam('occurrence_id', null);
        $endDate = $this->view->locale()->toDateTime(Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($inviteevent_id, 'DESC', $occurrence_id));
        $siteeventinviteFriendEventInvite = Zend_Registry::isRegistered('siteeventinviteFriendEventInvite') ? Zend_Registry::get('siteeventinviteFriendEventInvite') : null;
        $currentDate = $this->view->locale()->toDateTime(time());
        if (strtotime($endDate) < strtotime($currentDate))
            return $this->_forwardCustom('notfound', 'error', 'core');
        //SAVING THE INVITED USERS IN DATABASE INVITE TABLE.
        $this->view->success_fbinvite = false;
        $fbinvited = $this->getRequest()->get('redirect_fbinvite', 0);
        if ($fbinvited && $this->getRequest()->isPost()) {
            $facebookInvite = new Seaocore_Api_Facebook_Facebookinvite();
            $facebookInvite->seacoreInvite($this->getRequest()->get('ids'), 'facebook');
            $this->view->success_fbinvite = true;
        }

        if (empty($siteeventinviteFriendEventInvite))
            return;

        // Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_main');
        if (empty($inviteevent_id) || empty($inviteevent_userid)) {
            $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
        }
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $inviteevent_id);
        if ($inviteevent_id) {
            $this->view->siteevent = $siteevent;
            if ($siteevent) {
                Engine_Api::_()->core()->setSubject($siteevent);
            }
        }
//    $isManageAdmin = Engine_Api::_()->siteevent()->isManageAdmin($siteevent, 'edit');
//		if (empty($isManageAdmin)) {
//			$this->view->can_edit = $can_edit = 0;
//		} else {
//			$this->view->can_edit = $can_edit = 1;
//		}
        $this->view->can_edit = $can_edit = 1;
        $webmail_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('eventinvite.show.webmail', false);
        if ($webmail_show)
            $this->view->webmail_show = unserialize($webmail_show);
        else
            $this->view->webmail_show = array(0 => 'gmail', 1 => 'yahoo', 2 => 'window_mail', 3 => 'aol', 4 => 'facebook_mail', 5 => 'linkedin_mail', 6 => 'twitter_mail');
    }

    public function invitepreviewAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_name = $viewer->getTitle();

        $this->view->is_suggenabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');

        $eventinvite_id = $_GET['siteevent_id'];
        $occurrence_id = $_GET['occurrence_id'];
        Zend_Registry::set('occurrence_id', $occurrence_id);
        if ($eventinvite_id) {
            $siteevent = Engine_Api::_()->getItem('siteevent_event', $eventinvite_id);
            if ($siteevent) {
                //Engine_Api::_()->core()->setSubject($siteevent);
            }
        }
        $this->view->siteevent = $siteevent;
        $host = $_SERVER['HTTP_HOST'];
        $base_url = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $host . Zend_Controller_Front::getInstance()->getBaseUrl();
        $inviteUrl = ( _ENGINE_SSL ? 'https://' : 'http://' )
                . $_SERVER['HTTP_HOST']
                . $siteevent->getHref();

        //GETTING THE event PHOTO.
        $file = $siteevent->getPhotoUrl('thumb.normal');
        if (empty($file)) {
            $eventphoto_path = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Registry::get('Zend_View')->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/nophoto_event_thumb_normal.png';
        } else {
            $eventphoto_path = $file;
        }

        $inviteUrl_link = '<table width="100%"><tr valign="top"><td style="border-bottom: 1px solid rgb(204, 204, 204); padding-bottom: 20px;"><div style="float:left;margin-right:15px;"><a href = ' . $inviteUrl . ' target="_blank">' . '<img src="' . $eventphoto_path . '" align="left" style="width: 100px; border: 1px solid rgb(221, 221, 221); padding: 0px;"/>' . '</a>';
        //GETTING NO OF JOINS TO THIS Event.     
        $num_of_join = $siteevent->member_count;

        $this->view->site_title = $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');
        $site_title_linked = '<a href="' . $base_url . '" target="_blank" >' . $site_title . '</a>';
        $event_title_linked = '<a href="' . $inviteUrl . '" target="_blank" >' . $siteevent->title . '</a>';
        $event_link = '<a href="' . $inviteUrl . '" target="_blank" >' . $inviteUrl . '</a>';

        //MAKE THE BODY OF EMAIL



        $body = Engine_Api::_()->siteeventinvite()->getMailBodyOther($siteevent, $event_title_linked,null, $viewer);

        $siteeventModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('siteevent');
        $siteeventVersion = $siteeventModule->version;
        if ($siteeventVersion >= '4.8.8p1') {
          $siteeventVersion = true;
        }
            
        if(Engine_Api::_()->siteevent()->isTicketBasedEvent()) {
            $body = $inviteUrl_link . '</div><div style="overflow:hidden;line-height: 20px;">' . $body . '</div></td></tr><tr><td>';
        }
        else {
            $body = $inviteUrl_link . '<div>' . $this->view->translate(array('%s guest', '%s guests', $num_of_join), $this->view->locale()->toNumber($num_of_join)) . '</div></div><div style="overflow:hidden;line-height: 20px;">' . $body . '</div></td></tr><tr><td>';            
        }

        // Initialize the strings to be sent in the mail
        $type = 'Siteeventinvite_User_Invite';
        $mailTemplateTable = Engine_Api::_()->getDbtable('MailTemplates', 'core');
        $mailTemplate = $mailTemplateTable->fetchRow($mailTemplateTable->select()->where('type = ?', $type));
        if (null === $mailTemplate) {
            return;
        }
        //CHECK IF EMAIL TEMPLATE PLUGIN IS INSTALLED THEN WE WILL NOT MAKE OUR DEFAULT TEMPLATE.
        $sitesitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');
        $template_header = '';
        $template_footer = '';
        $site__template_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.site.title', $site_title);
        if (empty($sitesitemailtemplates)) {
            $site_title_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.title.color', "#ffffff");
            $site_header_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.font.color', "#79b4d4");
            $template_header.= "<table width='98%' cellspacing='0' border='0'><tr><td width='100%' bgcolor='#f7f7f7' style='font-family:arial,tahoma,verdana,sans-serif;padding:40px;'><table width='620' cellspacing='0' cellpadding='0' border='0'>";
            $template_header.= "<tr><td style='background:" . $site_header_color . "; color:$site_title_color;font-weight:bold;font-family:arial,tahoma,verdana,sans-serif; padding: 4px 8px;vertical-align:middle;font-size:16px;text-align: left;' nowrap='nowrap'>" . $site__template_title . "</td></tr><tr><td valign='top' style='background-color:#fff; border-bottom: 1px solid #ccc; border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; font-family:arial,tahoma,verdana,sans-serif; padding: 15px;padding-top:0;' colspan='2'><table width='100%'><tr><td colspan='2'>";
            $template_footer.= "</td></tr></table></td></tr></table></td></tr></td></table></td></tr></table>";
        }
        //if (empty($sitesitemailtemplates)) {       
        $inviter_name = $viewer->getTitle();
        $inviter_link = ( _ENGINE_SSL ? 'https://' : 'http://' )
                . $_SERVER['HTTP_HOST']
                . $viewer->getHref();
        $inviter_name_withlinked = '<a href="' . $inviter_link . '" target="_blank" >' .$inviter_name . '</a>';
        $link = '<a href="' . $base_url . '">' . $base_url . '</a>';




        //$subjectKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_SUBJECT');
        $bodyTextKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_BODY');
        $bodyHtmlKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_BODYHTML');
        $recipientLanguage = $translate = Zend_Registry::get('Zend_Translate')->getLocale();

        // Get body
        $bodyTextTemplate = (string) $this->_translate($bodyTextKey, $recipientLanguage);
        $bodyHtmlTemplate = (string) $this->_translate($bodyHtmlKey, $recipientLanguage);

        if (!$bodyHtmlTemplate && !$bodyTextTemplate) {
            throw new Engine_Exception(sprintf('No body translation available for system email "%s"', $type));
        }

        // Get headers and footers
        $headerPrefix = '_EMAIL_HEADER_' . '';
        $footerPrefix = '_EMAIL_FOOTER_' . '';
        $bodyTextHeader = (string) $this->_translate($headerPrefix . 'BODY', $recipientLanguage);
        $bodyTextFooter = (string) $this->_translate($footerPrefix . 'BODY', $recipientLanguage);
        $bodyHtmlHeader = (string) $this->_translate($headerPrefix . 'BODYHTML', $recipientLanguage);
        $bodyHtmlFooter = (string) $this->_translate($footerPrefix . 'BODYHTML', $recipientLanguage);

        $rParams = array('template_header' => $template_header,
            'template_footer' => $template_footer,
            'inviter_name' => $inviter_name_withlinked,
            'inviter_link' => $inviter_name_withlinked,
            'site_title_linked' => $site_title_linked,
            'event_title_linked' => $event_title_linked,
            'event_link' => $event_link,
            'site_title' => $site_title,
            'body' => $body,
            'event_title' => $siteevent->title,
            'link' => $link,
            'host' => $host,
            'email' => $viewer->email,
            'queue' => true
        );


        // Do replacements
        foreach ($rParams as $var => $val) {
            $raw = trim($var, '[]');
            $var = '[' . $var . ']';
            // Fix nbsp
            $val = str_replace('&amp;nbsp;', ' ', $val);
            $val = str_replace('&nbsp;', ' ', $val);
            // Replace
            $bodyTextTemplate = str_replace($var, $val, $bodyTextTemplate);
            $bodyHtmlTemplate = str_replace($var, $val, $bodyHtmlTemplate);
            $bodyTextHeader = str_replace($var, $val, $bodyTextHeader);
            $bodyTextFooter = str_replace($var, $val, $bodyTextFooter);
            $bodyHtmlHeader = str_replace($var, $val, $bodyHtmlHeader);
            $bodyHtmlFooter = str_replace($var, $val, $bodyHtmlFooter);
        }

        // Do header/footer replacements
        $bodyTextTemplate = str_replace('[header]', $bodyTextHeader, $bodyTextTemplate);
        $bodyTextTemplate = str_replace('[footer]', $bodyTextFooter, $bodyTextTemplate);
        $bodyHtmlTemplate = str_replace('[header]', $bodyHtmlHeader, $bodyHtmlTemplate);
        $bodyHtmlTemplate = str_replace('[footer]', $bodyHtmlFooter, $bodyHtmlTemplate);

        // Check for missing text or html
        if (!$bodyHtmlTemplate) {
            $bodyHtmlTemplate = nl2br($bodyTextTemplate);
        }
        //}
        if (!empty($sitesitemailtemplates)) {
            //$data['template_id'] = $mailTemplate->template_id;
            $data['bodyHtmlTemplate'] = $bodyHtmlTemplate;
            $bodyHtmlTemplate = $this->view->mailtemplate($data);
        }
        $this->view->bodyHtmlTemplate = $bodyHtmlTemplate;
        $this->view->servicetype = $_GET['servicetype'];
    }

    protected function _translate($key, $locale, $noDefault = false) {
        $translate = Zend_Registry::get('Zend_Translate');
        $value = $translate->translate($key, $locale);
        if ($value == $key || '' == trim($value)) {
            if ($noDefault) {
                return false;
            } else {
                $value = $translate->translate($key);
                if ($value == $key || '' == trim($value)) {
                    return false;
                }
            }
        }
        return $value;
    }

    public function invitetositeAction() {
        $invite_friend = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.ltsug');
        $friendsToJoin = $_POST['nonsitemembers'];
        $eventinvite_id = $_POST['inviteevent_id'];
        $eventinvite_userid = $_POST['inviteevent_userid'];

        $this->sendInvites($friendsToJoin, $eventinvite_id, $eventinvite_userid);
        exit();
    }

    public function sendInvites($recipients, $eventinvite_id = null, $eventinvite_userid = null, $invite_message = null) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $translate = Zend_Registry::get('Zend_Translate');
        $message = $this->view->translate(Engine_Api::_()->getApi('settings', 'core')->invite_message);
        $message = trim($message);

        $template_header = '';
        $template_footer = '';
        $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');
        $site__template_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.site.title', $site_title);
        $site_title_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.title.color', "#ffffff");
        $site_header_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.font.color', "#79b4d4");
        $template_header.= "<table width='98%' cellspacing='0' border='0'><tr><td width='100%' bgcolor='#f7f7f7' style='font-family:arial,tahoma,verdana,sans-serif;padding:40px;'><table width='620' cellspacing='0' cellpadding='0' border='0'>";
        $template_header.= "<tr><td style='background:" . $site_header_color . "; color:" . $site_title_color . ";font-weight:bold;font-family:arial,tahoma,verdana,sans-serif; padding: 4px 8px;vertical-align:middle;font-size:16px;text-align: left;' nowrap='nowrap'>" . $site__template_title . "</td></tr><tr><td valign='top' style='background-color:#fff; border-bottom: 1px solid #ccc; border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; font-family:arial,tahoma,verdana,sans-serif; padding: 15px;padding-top:0;' colspan='2'><table width='100%'><tr><td colspan='2'>";
         $inviter_name = $viewer->getTitle();
         $inviter_link = ( _ENGINE_SSL ? 'https://' : 'http://' )
                . $_SERVER['HTTP_HOST']
                . $viewer->getHref();
        $inviter_name_withlinked = '<a href="' . $inviter_link . '" target="_blank" >' .$inviter_name . '</a>';
        $siteeventModHostName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));

        if ($eventinvite_id) {
            $siteevent = Engine_Api::_()->getItem('siteevent_event', $eventinvite_id);
//      if ($siteevent) {
//        Engine_Api::_()->core()->setSubject($siteevent);
//      }
        }
        // $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');
        $host = $_SERVER['HTTP_HOST'];
        $base_url = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $host . Zend_Controller_Front::getInstance()->getBaseUrl();
        $inviteUrl = ( _ENGINE_SSL ? 'https://' : 'http://' )
                . $_SERVER['HTTP_HOST']
                . $siteevent->getHref();

        //GETTING THE Event PHOTO.
        $file = $siteevent->getPhotoUrl('thumb.icon');
        if (empty($file)) {
            $eventphoto_path = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Registry::get('Zend_View')->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/nophoto_list_thumb_normal.png';
        } else {
            $eventphoto_path = $file;
        }

        $inviteUrl_link = '<table><tr valign="top"><td style="color:#999999;font-size:11px;padding-right:15px;"><a href = ' . $inviteUrl . ' target="_blank">' . '<img src="' . $eventphoto_path . '" style="width:100px;"/>' . '</a>';
        //GETTING NO OF LIKES TO THIS Event.
        $num_of_like = Engine_Api::_()->siteevent()->numberOfLike('siteevent_event', $siteevent->event_id);
        $body_message = $inviteUrl_link . $siteevent->title . '<br /> ' . $this->view->translate(array('%s Person Likes This', '%s People Like This', $num_of_like), $this->view->locale()->toNumber($num_of_like));

        $recepients_array = array();

        $site_title_linked = '<a href="' . $base_url . '" target="_blank" >' . $site_title . '</a>';
        $event_title_linked = '<a href="' . $inviteUrl . '" target="_blank" >' . $siteevent->title . '</a>';
        $event_link = '<a href="' . $inviteUrl . '" target="_blank" >' . $inviteUrl . '</a>';
        $isModType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventinvite.set.type', 0);
        if (empty($isModType)) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('siteeventinvite.utility.type', convert_uuencode($siteeventModHostName));
        }

        $inviteOnlySetting = $settings->getSetting('user.signup.inviteonly', 0);
        Engine_Api::_()->siteeventinvite()->setInvitePackages();
        if (is_array($recipients) && !empty($recipients) && !empty($eventinvite_id) && !empty($eventinvite_userid)) {

            $table_message = Engine_Api::_()->getDbtable('messages', 'messages');
            $tableName_message = $table_message->info('name');

            $table_user = Engine_Api::_()->getitemtable('user');
            $tableName_user = $table_user->info('name');

            $table_user_memberships = Engine_Api::_()->getDbtable('membership', 'user');
            $tableName_user_memberships = $table_user_memberships->info('name');

            foreach ($recipients as $recipient) {
                // perform tests on each recipient before sending invite
                $recipient = trim($recipient);

                // watch out for poorly formatted emails
                if (!empty($recipient)) {
                    //FIRST WE WILL FIND IF THIS USER IS SITE MEMBER
                    $select = $table_user->select()
                            ->setIntegrityCheck(false)
                            ->from($tableName_user, array('user_id'))
                            ->where('email = ?', $recipient);
                    $is_site_members = $table_user->fetchAll($select);

                    //NOW IF THIS USER IS SITE MEMBER THEN WE WILL FIND IF HE IS FRINED OF THE OWNER.
                    if (isset($is_site_members[0]) && !empty($is_site_members[0]->user_id) && $is_site_members[0]->user_id != $viewer->user_id) {
                        $contact = Engine_Api::_()->user()->getUser($is_site_members[0]->user_id);

                        // check that user has not blocked the member
                        if (!$viewer->isBlocked($contact)) {
                            $recepients_array[] = $is_site_members[0]->user_id;
                            $is_suggenabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');

                            // IF SUGGESTION PLUGIN IS INSTALLED, A SUGGESTION IS SEND
                            if ($is_suggenabled) {
                                Engine_Api::_()->siteeventinvite()->sendSuggestion($is_site_members[0], $viewer, $siteevent->event_id);
                            }
                            // IF SUGGESTION PLUGIN IS NOT INSTALLED, A NOTIFICATION IS SEND
                            else {
                                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($is_site_members[0], $viewer, $siteevent, 'siteevent_suggested');
                            }
                        }
                    }
                    // BODY OF Event COMPRISING LIKES
                    $body = $inviteUrl_link . '<br/>' . $this->view->translate(array('%s person likes this', '%s people like this', $num_of_like), $this->view->locale()->toNumber($num_of_like)) . '</td><td>';

                    if (!empty($invite_message)) {
                        $body .= $invite_message . "<br />";
                    }
                    $link = '<a href="' . $base_url . '">' . $base_url . '</a>';
                    $template_footer.= "</td></tr></table></td></tr></table></td></tr></td></table></td></tr></table>";

                    // IF THE PERSON IS NOT THE SITE MEMBER
                    if (!isset($is_site_members[0])) {
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($recipient, 'Siteeventinvite_User_Invite', array(
                            'template_header' => $template_header,
                            'template_footer' => $template_footer,
                            'inviter_name' => $inviter_name_withlinked,
                            'inviter_link' => $inviter_name_withlinked,
                            'site_title_linked' => $site_title_linked,
                            'event_title_linked' => $event_title_linked,
                            'event_link' => $event_link,
                            'site_title' => $site_title,
                            'body' => $body,
                            'event_title' => $siteevent->title,
                            'link' => $link,
                            'host' => $host,
                            'email' => $viewer->email,
                            'queue' => true
                        ));
                    }
                    // IF THE PERSON IS A SITE MEMBER
                    else {
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($recipient, 'SITEEVENTINVITE_MEMBER_INVITE', array(
                            'template_header' => $template_header,
                            'template_footer' => $template_footer,
                            'inviter_name' => $inviter_name_withlinked,
                            'inviter_link' => $inviter_name_withlinked,
                            'event_title_linked' => $event_title_linked,
                            'event_link' => $event_link,
                            'site_title' => $site_title,
                            'body' => $body,
                            'event_title' => $siteevent->title,
                            'link' => $link,
                            'host' => $host,
                            'email' => $viewer->email,
                            'queue' => true
                        ));
                    }
                }
            } // end foreach
        } // end if (is_array($recipients) && !empty($recipients))
        return;
    }

    // end public function sendInvites()

    public function inviteusersAction() {
        $this->_helper->layout->setLayout('default-simple');
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->invite_emails = "";
        // Get the current loggedin user information
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->eventinvite_id = $eventinvite_id = $this->_getParam('siteevent_id', null);
        $this->view->occurrence_id = $occurrence_id = $this->_getParam('occurrence_id', null);
        Zend_Registry::set('occurrence_id', $occurrence_id);
        $this->view->eventinvite_userid = $eventinvite_userid = $this->_getParam('user_id', null);
        // Get the current loged in userid
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        if (isset($_POST['submit']) && $_POST['submit']) {
            $invite_emails = $_POST['invite_email'];

            if (empty($invite_emails[0])) {
                $this->view->is_error = $this->view->translate('Please enter email address.');
            }

            $invite_message = $_POST['invite_message'];
            $total_prefill = 1;

            $this->view->invite_emails = $invite_emails;
            $this->view->invite_message = $invite_message;
            $message = trim($invite_message);
            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);
            //Check email is valid or not.
            foreach ($invite_emails as $email) {
                if (!empty($email)) {
                    //Calculate total prefill if invitemail is not empty.
                    $total_prefill = count($invite_emails);
                    if (!$validator->isValid(trim($email))) {
                        $this->view->is_error = $this->view->translate('Please enter valid email address.');
                    }
                }
            }

            if (empty($this->view->is_error)) {
                Engine_Api::_()->getApi('Invite', 'Seaocore')->sendPageInvites($invite_emails, $eventinvite_id, 'siteevent', $invite_message, 'siteevent_event');
                //$this->sendInvites($invite_emails, $eventinvite_id, $eventinvite_userid, $invite_message);
                $this->view->successmessage = $this->view->translate('Your Event invitation(s) were sent successfully.');
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => true,
                    'parentRefresh' => false,
                    'format' => 'smoothbox',
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('<ul class="form-notices" style="margin:0px;"><li style="margin:0px;">Your Event invitation(s) were sent successfully.</li></ul>'))
                ));
                $total_prefill = 1;
            }

            $this->view->total_prefill = $total_prefill;
        }
    }

    public function sendinviteAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;
//        if (!$this->_helper->requireSubject('siteevent_event')->isValid())
//            return;
        // @todo auth
        // Prepare data
        $viewer = Engine_Api::_()->user()->getViewer();
        $event_id = $this->_getParam('siteevent_id', null);
        $this->view->event = $event = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $occurrence_id = $this->_getParam('occurrence_id', null);
        Zend_Registry::set('occurrence_id', $occurrence_id);
        // Not posting
        if (!$this->getRequest()->isPost()) {
            return;
        }

        // Process
        $table = $event->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $friends = $this->getRequest()->getPost();

            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $friendsToJoin = array();
            foreach ($friends as $flag => $friend_id) {

                if (strpos($flag, 'check_') !== FALSE) {
                    $friend = Engine_Api::_()->getItem('user', $friend_id);
                    $friendsToJoin[] = $friend->email . '#' . $friend->displayname;
                    $friendsToJoinTasks[] = $friend_id;
                    $event->membership()->addMember($friend)
                            ->setResourceApproved($friend);

                    //$notifyApi->addNotification($friend, $viewer, $event, 'siteevent_invite', array('occurrence_id' => $occurrence_id));
                }
            }

            if (!empty($friendsToJoin)) {
                //SEND MAIL NOTIFICATION AS WELL
                if(Count($friendsToJoin) > 40) {    
                    $inviteTable = Engine_Api::_()->getDbtable('invites', 'seaocore');    
                    foreach($friendsToJoinTasks as $friend_id) {    
                        $inviteTable->insert(array(
                            'resource_type' => 'siteevent_event',
                            'resource_id' => $event->event_id,
                            'recipient_id' => $friend_id,
                            'creation_time' => new Zend_Db_Expr('NOW()'),
                            'inviter_id' => $viewer->getIdentity(),
                            'occurrence_id' => $occurrence_id
                        )); 
                    }
                }
                else {
                    Engine_Api::_()->getApi('Invite', 'Seaocore')->sendPageInvites($friendsToJoin, $event->event_id, 'siteevent', '', 'siteevent_event');  
                }
            }
                
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Members invited')),
                    'layout' => 'default-simple',
                    'parentRefresh' => true,
        ));
    }

    //GET THE FRIENDS LIST TO BE INVITED
    public function inviteFriendsAction() {
        $this->view->search_true = false;
        // Set variables for JS. when open popups.              
        $modParem = 'modContentId';
        if (!empty($_GET['task'])) {
            $getTask = $_GET['task'];
        }
        if (!empty($_GET['selected_checkbox'])) {
            $getSelectCheckbox = $_GET['selected_checkbox'];
        }
        if (!empty($_GET['page'])) {
            $getPage = $_GET['page'];
        }
        if (!empty($_GET['searchs'])) {
            $getSearch = $_GET['searchs'];
        }
        if (!empty($_GET['show_selected'])) {
            $getShowSelected = $_GET['show_selected'];
        }
        if (!empty($_GET['action_id'])) {
            $getActionId = $_GET['action_id'];
        }
        if (!empty($_GET['selected_friend_flag'])) {
            $this->view->selectedFriendFlag = $_GET['selected_friend_flag'];
        }
        // Assign variables for resolving log error.
        if (empty($getTask)) {
            $getTask = null;
        }
        if (empty($getSelectCheckbox)) {
            $getSelectCheckbox = null;
        }
        if (empty($getPage)) {
            $getPage = null;
        }
        if (empty($getSearch)) {
            $getSearch = null;
        }
        if (empty($getShowSelected)) {
            $getShowSelected = null;
        }
        if (empty($getActionId)) {
            $getActionId = null;
        }
        $veiw = $this->openPopupContent($getTask, $getSelectCheckbox, $getPage, $getSearch, $getShowSelected, $getActionId);

        foreach ($veiw as $key => $value) {
            $this->view->$key = $value;
        }
    }

    public function openPopupContent($getTask, $getSelectCheckbox, $getPage, $getSearch, $getShowSelected, $getActionId) {

        //THIS IS WHEN DO SOME ACTIVITY ON THE SUGGESTION PAGE.
        if (!empty($getTask)) {
            $view['search_true'] = true;
        }
        if (!empty($getSelectCheckbox)) {
            $view['selected_checkbox'] = $getSelectCheckbox;
            $getSelectCheckbox = trim($getSelectCheckbox, ',');
            $modStrId_array = explode(",", $getSelectCheckbox);
            $view['friends_count'] = @COUNT($modStrId_array);
        } else {
            $view['selected_checkbox'] = '';
            $view['friends_count'] = $selected_friend_count = 0;
        }

        $view['page'] = $page = !empty($getPage) ? $getPage : 1;
        $view['search'] = $search = !empty($getSearch) ? $getSearch : '';

        $view['siteevent_id'] = $this->_getParam("siteevent_id", 0);
        $view['siteevent'] = Engine_Api::_()->getItem('siteevent_event', $view['siteevent_id']);
        $view['occurrence_id'] = $this->_getParam("occurrence_id", 0);
        //IF THE REQUEST IS FOR SHOWING ONLY SELECTED FRIENDS.
        if (!empty($getShowSelected)) {
            $search = '';
            $view['show_selected'] = $selected_friend_show = 1;
            $modId_array = $modStrId_array;
        }
        //IF THE REQUEST IS FOR SHOWING ALL FRIENDS.
        else {
            $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $sugg_popups_limit = 0;

            // $modName would be 'friend' only when "Add a Friend popup" & "Few friend popup"
            $view['suggest_user'] = $view['members'] = $fetch_member_myfriend = Engine_Api::_()->siteeventinvite()->user_first_level_friend($this->_getParam("siteevent_id", 0), $this->_getParam("occurrence_id", 0), $search);


            if (!empty($page)) {
                $fetch_member_myfriend->setCurrentPageNumber($page);
            }
            $fetch_member_myfriend->setItemCountPerPage(40);

            $modId_array = array();

            foreach ($fetch_member_myfriend as $modRow) { 
                $modId_array[] = $modRow['resource_id'];
            }
            $view['show_selected'] = $selected_friend_show = 0;
        }
        $view['mod_combind_path'] = $modId_array;

        //HERE WE ARE CHECKING IF THE REQUEST IS FOR ONLY SHOW SELECTED FRIENDS THEN WE WILL MAKE PAGINATION OF USER OBJECT OTHERWISE WE WILL SIMPLY USER FETCHALL QUERY.
        if (!empty($modId_array)) {

            $tempSelectedFriend = '';
            if (!empty($modId_array) && !empty($modStrId_array)) {
                foreach ($modId_array as $values) {
                    if (in_array($values, $modStrId_array)) {
                        $tempSelectedFriend .= ',' . $values;
                    }
                }
            }

            $view['tempSelectedFriend'] = $tempSelectedFriend;
            $view['suggest_user_id'] = $modId_array;
            if ($selected_friend_show) {
                $view['suggest_user'] = $view['members'] = $selected_friends = Engine_Api::_()->siteeventinvite()->selected_users_information($modId_array, $selected_friend_show, '');
                $selected_friends->setCurrentPageNumber($page);
                $selected_friends->setItemCountPerPage(40);
            }
        }
        if (!empty($_GET['getArray'])) {
            $view['getArray'] = $_GET['getArray'];
        } else {
            $view['getArray'] = array();
        }
        return $view;
    }

}

?>
