<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Mail.php 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemailtemplates_Api_Mail extends Core_Api_Mail {

  protected $_enabled;
  protected $_queueing;
  protected $_transport;
  protected $_log;

  public function __construct() {
    $this->_enabled = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.enabled', true);
    $this->_queueing = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.queueing', true);
  }

  public function send(Zend_Mail $mail) {
    $check_settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemailtemplates.check.setting', 0);
    if ($check_settings) {
      $data = array();
      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $bodyHtmlTemplate = quoted_printable_decode($mail->getBodyHtml(true));
      $data['bodyHtmlTemplate'] = $bodyHtmlTemplate;

      $bodyHtmlTemplate = $view->mailtemplate($data);
      $mail->setBodyHtml($bodyHtmlTemplate);
      $bodyHtmlTemplate = quoted_printable_decode($mail->getBodyText(true));
      $data['bodyHtmlTemplate'] = $bodyHtmlTemplate;
      $bodyHtmlTemplate = $view->mailtemplate($data);
      $mail->setBodyText($bodyHtmlTemplate);
    }

    if ($this->_enabled) {

      if ($this->_queueing) {

        //SINGLE
        if (count($mail->getRecipients()) <= 1) {
          $mailTable = Engine_Api::_()->getDbtable('mail', 'core');
          $mailTable->insert(array(
              'type' => 'zend',
              'body' => serialize($mail),
              'recipient_count' => count($mail->getRecipients()),
          ));
        }

        //MULTI
        else {
          $recipients = $mail->getRecipients();
          $mailClone = clone $mail;
          $mailClone->clearRecipients();

          //INSERT MAIN
          $mailTable = Engine_Api::_()->getDbtable('mail', 'core');
          $mail_id = $mailTable->insert(array(
              'type' => 'zend',
              'body' => serialize($mailClone),
              'recipient_count' => count($recipients),
              'recipient_total' => count($recipients),
              'creation_time' => date("Y-m-d H:i:s"),
                  ));

          //INSERT RECIPIENTS
          $mailRecipientsTable = Engine_Api::_()->getDbtable('mailRecipients', 'core');
          foreach ($recipients as $oneRecipient) {
            if ($oneRecipient instanceof Core_Model_Item_Abstract) {
              $mailRecipientsTable->insert(array(
                  'mail_id' => $mail_id,
                  'user_id' => $oneRecipient->user_id,
              ));
            } else if (is_string($oneRecipient)) {
              $mailRecipientsTable->insert(array(
                  'mail_id' => $mail_id,
                  'email' => $oneRecipient,
              ));
            }
          }
        }
      } else {

        $mailClone = clone $mail;
        $mailClone->clearRecipients();
        $mailClone->addTo($mailClone->getFrom());
        foreach ($mail->getRecipients() as $oneRecipient) {
          if ($oneRecipient instanceof Core_Model_Item_Abstract && !empty($oneRecipient->email)) {
            $mailClone->addBcc($oneRecipient->email);
          } else if (is_string($oneRecipient)) {
            $mailClone->addBcc($oneRecipient);
          }
          //SEND IN BATCHES 30
          if (30 <= count($mailClone->getRecipients())) {
            $this->sendRaw($mailClone);
            $mailClone->clearRecipients();
            $mailClone->addTo($mailClone->getFrom());
          }
        }
        if (1 <= count($mailClone->getRecipients())) {
          $this->sendRaw($mailClone);

          //LOGGING IN DEV MODE
          if ('development' == APPLICATION_ENV) {
            $this->getLog()->log(sprintf('[%s] %s <- %s', 'Zend', join(', ', $mailClone->getRecipients()), $mailClone->getFrom()), Zend_Log::DEBUG);
          }
        }
      }
    }
    return $this;
  }

  //SYSTEM
  public function sendSystemRaw($recipient, $type, array $params = array()) {
    //VERIFY MAIL TEMPLATE TYPE
    $mailTemplateTable = Engine_Api::_()->getDbtable('MailTemplates', 'core');
    $mailTemplate = $mailTemplateTable->fetchRow($mailTemplateTable->select()->where('type = ?', $type));
    if (null === $mailTemplate) {
      return;
    }

    //VERIFY RECIPIENT(S)
    if (!is_array($recipient) && !($recipient instanceof Zend_Db_Table_Rowset_Abstract)) {
      $recipient = array($recipient);
    }
    $recipients = array();
    foreach ($recipient as $oneRecipient) {
      if (!$this->_validateRecipient($oneRecipient)) {
        throw new Engine_Exception(get_class($this) . '::sendSystem() requires an item, an array of items with an email, or a string email address.');
      }
      $recipients[] = $oneRecipient;
    }

    //GET ADMIN INFO
    if (empty($params['sender_email'])) {
      $fromAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.from', 'admin@' . $_SERVER['HTTP_HOST']);
    } else {
      $mailExist = strstr($params['sender_email'], 'Email:');
      $sender_mail = '';
      if (isset($params['email']))
        $sender_mail = '(' . $params['email'] . ')';
      $mailString = strstr($params['sender_email'], $sender_mail);
      if (!empty($mailExist)) {
        $email = explode('Email:', $params['sender_email']);
        $fromAddress = $email[1];
      } elseif (!empty($mailString)) {
        $email = explode($sender_mail, $params['sender_email']);
        $fromAddress = $email[0];
      } else {
        $fromAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.from', 'admin@' . $_SERVER['HTTP_HOST']);
      }
    }

    if (empty($params['sender_name'])) {
      $fromName = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.name', 'Site Admin');
    } else {
      $viewerName = explode('Name:', $params['sender_name']);
      $fromName = $viewerName[1];
    }

    $params['admin_email'] = $fromAddress;
    $params['admin_title'] = $fromName;

    //BUILD SUBJECT BODY
    $translate = Zend_Registry::get('Zend_Translate');

    $subjectKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_SUBJECT');
    $bodyTextKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_BODY');
    $bodyHtmlKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_BODYHTML');

    //SEND TO EACH RECIPIENT
    foreach ($recipients as $recipient) {

      //COPY PARAMS
      $rParams = $params;

      //SEE IF THEY'RE ACTUALLY A MEMBER
      if (is_string($recipient)) {
        $user = Engine_Api::_()->getItemTable('user')->fetchRow(array('email LIKE ?' => $recipient));
        if (null !== $user) {
          $recipient = $user;
        }
      }

      //CHECK RECIPIENT
      if ($recipient instanceof Core_Model_Item_Abstract) {
        $isMember = true;

        //DETECT EMAIL AND NAME
        $recipientEmail = $recipient->email;
        $recipientName = $recipient->getTitle();

        //DETECT LANGUAGE
        if (!empty($rParams['language'])) {
          $recipientLanguage = $rParams['language'];
        } else if (!empty($recipient->language)) {
          $recipientLanguage = $recipient->language;
        } else {
          $recipientLanguage = $translate->getLocale();
        }
        if (!Zend_Locale::isLocale($recipientLanguage) ||
                $recipientLanguage == 'auto' ||
                !in_array($recipientLanguage, $translate->getList())) {
          $recipientLanguage = $translate->getLocale();
        }

        //ADD AUTOMATIC PARAMS
        $rParams['email'] = $recipientEmail;
        $rParams['language'] = $recipientLanguage;
        $rParams['recipient_email'] = $recipientEmail;
        $rParams['recipient_title'] = $recipientName;
        $rParams['recipient_link'] = $recipient->getHref();
        $rParams['recipient_photo'] = $recipient->getPhotoUrl('thumb.normal');
      } else if (is_string($recipient)) {
        $isMember = false;

        //DETECT EMAIL AND NAME
        if (strpos($recipient, ' ') !== false) {
          $parts = explode(' ', $recipient, 2);
          $recipientEmail = $parts[0];
          $recipientName = trim($parts[1], ' <>');
        } else {
          $recipientEmail = $recipient;
          $recipientName = '';
        }

        //DETECT LANGUAGE
        if (!empty($rParams['language'])) {
          $recipientLanguage = $rParams['language'];
          //} else if( !empty($recipient->language) ) {
          //$recipientLanguage = $recipient->language;
        } else {
          $recipientLanguage = $translate->getLocale();
        }
        if (!Zend_Locale::isLocale($recipientLanguage) ||
                $recipientLanguage == 'auto' ||
                !in_array($recipientLanguage, $translate->getList())) {
          $recipientLanguage = $translate->getLocale();
        }

        //ADD AUTOMATIC PARAMS
        $rParams['email'] = $recipientEmail;
        $rParams['recipient_email'] = $recipientEmail;
        $rParams['recipient_title'] = $recipientName;
        $rParams['recipient_link'] = '';
        $rParams['recipient_photo'] = '';
      } else {
        continue;
      }

      //GET SUBJECT AND BODY
      $bodyHtmlTemplate = '';
      $subjectTemplate = (string) $this->_translate($subjectKey, $recipientLanguage);
      $bodyTextTemplate = (string) $this->_translate($bodyTextKey, $recipientLanguage);
      $bodyHtmlTemplate .= (string) $this->_translate($bodyHtmlKey, $recipientLanguage);

      if (!($subjectTemplate)) {
        throw new Engine_Exception(sprintf('No subject translation available for system email "%s"', $type));
      }
      if (!$bodyHtmlTemplate && !$bodyTextTemplate) {
        throw new Engine_Exception(sprintf('No body translation available for system email "%s"', $type));
      }

      //GET HEADERS AND FOOTERS
      if ($mailTemplate->type == 'SITEMAILTEMPLATES_CONTACTS_EMAIL_NOTIFICATION') {
        if ($params['member_type'] == 'member') {
          $headerPrefix = '_EMAIL_HEADER_MEMBER_';
          $footerPrefix = '_EMAIL_FOOTER_MEMBER_';
        } elseif ($params['member_type'] == 'non-member') {
          $headerPrefix = '_EMAIL_HEADER_';
          $footerPrefix = '_EMAIL_FOOTER_';
        }
      } else {
        $headerPrefix = '_EMAIL_HEADER_' . ( $isMember ? 'MEMBER_' : '' );
        $footerPrefix = '_EMAIL_FOOTER_' . ( $isMember ? 'MEMBER_' : '' );
      }

      $subjectHeader = (string) $this->_translate($headerPrefix . 'SUBJECT', $recipientLanguage);
      $subjectFooter = (string) $this->_translate($footerPrefix . 'SUBJECT', $recipientLanguage);
      $bodyTextHeader = (string) $this->_translate($headerPrefix . 'BODY', $recipientLanguage);
      $bodyTextFooter = (string) $this->_translate($footerPrefix . 'BODY', $recipientLanguage);
      $bodyHtmlHeader = (string) $this->_translate($headerPrefix . 'BODYHTML', $recipientLanguage);
      $bodyHtmlFooter = (string) $this->_translate($footerPrefix . 'BODYHTML', $recipientLanguage);

      //DO REPLACEMENTS
      foreach ($rParams as $var => $val) {
        $raw = trim($var, '[]');
        $var = '[' . $var . ']';
        //FIX NBSP
        $val = str_replace('&amp;nbsp;', ' ', $val);
        $val = str_replace('&nbsp;', ' ', $val);
        //REPLACE
        $subjectTemplate = str_replace($var, $val, $subjectTemplate);
        $bodyTextTemplate = str_replace($var, $val, $bodyTextTemplate);
        $bodyHtmlTemplate = str_replace($var, $val, $bodyHtmlTemplate);
        $subjectHeader = str_replace($var, $val, $subjectHeader);
        $subjectFooter = str_replace($var, $val, $subjectFooter);
        $bodyTextHeader = str_replace($var, $val, $bodyTextHeader);
        $bodyTextFooter = str_replace($var, $val, $bodyTextFooter);
        $bodyHtmlHeader = str_replace($var, $val, $bodyHtmlHeader);
        $bodyHtmlFooter = str_replace($var, $val, $bodyHtmlFooter);
      }

      //DO HEADER/FOOTER REPLACEMENTSREPLACEMENTS
      $subjectTemplate = str_replace('[header]', $subjectHeader, $subjectTemplate);
      $subjectTemplate = str_replace('[footer]', $subjectFooter, $subjectTemplate);

      if ($mailTemplate->type != 'SITEMAILTEMPLATES_CONTACTS_EMAIL_NOTIFICATION') {
        $htmlofHeader = htmlspecialchars($bodyTextHeader);

        $value = strcmp($bodyTextHeader, $htmlofHeader);

        if (empty($value)) {
          $bodyTextTemplate = str_replace('[header]', $bodyTextHeader, $bodyTextTemplate);
        } else {
          $bodyTextTemplate = str_replace('[header]', $bodyTextHeader, $bodyTextTemplate);
        }
        $bodyTextTemplate = str_replace('[header]', $bodyTextHeader, nl2br($bodyTextTemplate));
        $bodyTextTemplate = str_replace('[footer]', nl2br($bodyTextFooter), $bodyTextTemplate);
      } else {
        $bodyTextTemplate = str_replace('[header]', $bodyTextHeader, $bodyTextTemplate);
        $bodyTextTemplate = str_replace('[footer]', $bodyTextFooter, $bodyTextTemplate);
        
      }
      $bodyHtmlTemplate = str_replace('[header]', $bodyHtmlHeader, $bodyHtmlTemplate);
      $bodyHtmlTemplate = str_replace('[footer]', $bodyHtmlFooter, $bodyHtmlTemplate);

      //CHECK FOR MISSING TEXT OR HTML
      if (!$bodyHtmlTemplate) {
        if ($mailTemplate->type != 'SITEMAILTEMPLATES_CONTACTS_EMAIL_NOTIFICATION')
          $bodyHtmlTemplate = ($bodyTextTemplate);
        else
          $bodyHtmlTemplate = nl2br($bodyTextTemplate);
      }
      if (!$bodyTextTemplate) {
        $bodyTextTemplate = $bodyHtmlTemplate;
      }
      $bodyTextTemplate = strip_tags($bodyTextTemplate);

      //SEND
      if ($subjectKey == '_EMAIL_NOTIFY_ADMIN_USER_SIGNUP_SUBJECT') {
        //THE SIGNUP NOTIFICATION IS EMAILED TO THE FIRST SUPERADMIN BY DEFAULT
        $users_table = Engine_Api::_()->getDbtable('users', 'user');
        $recipientEmail = $users_table->select()
                ->from($users_table->info('name'), 'email')
                ->where('level_id = ?', 1)
                ->where('enabled >= ?', 1)
                ->limit(1)
                ->query()
                ->fetchColumn();
      }
      $check_settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemailtemplates.check.setting', 0);

      if ((!empty($mailTemplate->enable_template) && !empty($check_settings)) || ($mailTemplate->type == 'SITEMAILTEMPLATES_CONTACTS_EMAIL_NOTIFICATION')) {

        $data = array();
        $data['bodyHtmlTemplate'] = $bodyHtmlTemplate;
        if ($mailTemplate->type == 'SITEMAILTEMPLATES_CONTACTS_EMAIL_NOTIFICATION') {
          $data['template_id'] = $params['template_id'];
          $data['type'] = $type;
        } else {
          $data['template_id'] = $mailTemplate->template_id;
        }
        $data['mailtemplate_id'] = $mailTemplate->mailtemplate_id;
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $bodyHtmlTemplate = $view->mailtemplate($data);
      }
      $mail = $this->create()
              ->addTo($recipientEmail, $recipientName)
              ->setFrom($fromAddress, $fromName)
              ->setSubject($subjectTemplate)
              ->setBodyHtml($bodyHtmlTemplate)
              ->setBodyText($bodyTextTemplate);
      
      if(!empty($params['ticket_attachment'])) {
        $filecontents = file_get_contents($params['ticket_attachment']);
        $attachment = $mail->createAttachment($filecontents);
        $fileNamesArray = explode('/', $params['ticket_attachment']);
        $fileName = end($fileNamesArray);
        $fileName = !empty($fileName) ? $fileName : 'ticket.pdf';
        $attachment->filename = $fileName;  
      }

      $this->sendRaw($mail);

      //LOGGING IN DEV MODE
      if ('development' == APPLICATION_ENV) {
        $this->getLog()->log(sprintf('[%s] (%s) %s <- %s', 'System', $type, join(', ', $mail->getRecipients()), $mail->getFrom()), Zend_Log::DEBUG);
      }
    }

    return $this;
  }

  /**
   * Get profile types array
   *
   */
  public function getProfileTypes($count = 0) {

    //GET EXISTING PROFILE TYPES
    $fieldType = 'user';
    $optionsData = Engine_Api::_()->getApi('core', 'fields')->getFieldsOptions($fieldType);
    $topLevelField_field_id = 1;
    $topLevelOptions = array();
    $topLevelOptions[0] = Zend_Registry::get('Zend_Translate')->_('Everyone');
    foreach ($optionsData->getRowsMatching('field_id', $topLevelField_field_id) as $option) {
      $topLevelOptions[$option->option_id] = $option->label;
    }

    if (!empty($count)) {
      return Count($topLevelOptions);
    }

    //RETURN PROFILE TYPES
    return $topLevelOptions;
  }

  /**
   * Get member levels array
   *
   */
  public function getNetworks() {

    //GET NETWORK TABLE
    $tableNetwork = Engine_Api::_()->getDbtable('networks', 'network');

    //MAKE QUERY
    $select = $tableNetwork->select()
            ->from($tableNetwork->info('name'), array('network_id', 'title'))
            ->order('title');
    $result = $tableNetwork->fetchAll($select);

    $everyone = Zend_Registry::get('Zend_Translate')->_('Everyone');

    //MAKE DATA ARRAY
    $networksOptions = array('0' => $everyone);
    foreach ($result as $value) {
      $networksOptions[$value->network_id] = $value->title;
    }

    //RETURN
    return $networksOptions;
  }

  /**
   * Get member levels array
   *
   */
  public function getMemberLevels() {

    //GET AUTHORIZATION TABLE
    $levelsTable = Engine_Api::_()->getDbtable('levels', 'authorization');
    $levelsSelect = $levelsTable->select()->where('type !=?', 'public');
    $levels = $levelsTable->fetchAll($levelsSelect);

    $everyone = Zend_Registry::get('Zend_Translate')->_('Everyone');

    $levels_prepared = array('0' => $everyone);
    foreach ($levels as $level) {
      $levels_prepared[$level->getIdentity()] = $level->getTitle();
    }

    reset($levels_prepared);

    //RETURN
    return $levels_prepared;
  }

  /**
   * Plugin which return the error, if Siteadmin not using correct version for the plugin.
   *
   */
  public function isModulesSupport() {

    $modArray = array(
        'sitepage' => '4.2.4',
        'sitepageadmincontact' => '4.2.4',
        'sitebusiness' => '4.2.4',
        'sitebusinessadmincontact' => '4.2.4',
        'birthday' => '4.2.4'
    );
    $finalModules = array();
    foreach ($modArray as $key => $value) {
      $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($key);
      if (!empty($isModEnabled)) {
        $getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule($key);
        $isModSupport = $this->checkVersion($getModVersion->version, $value);
        if (empty($isModSupport)) {
          $finalModules[] = $getModVersion->title;
        }
      }
    }
    return $finalModules;
  }
    private function checkVersion($databaseVersion, $checkDependancyVersion) {
        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0)
            return -1;

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr))
            $count = count($fArr);
        else
            $count = count($sArr);

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue)
                    return 1;
                elseif ($fValue < $sValue)
                    return 0;
                else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else
                        continue;
                }
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue)
                    return 1;
                elseif ($fsArr[0] < $sValue)
                    return 0;
                else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0])
                    return 1;
                elseif ($fValue < $ssArr[0])
                    return 0;
                else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0])
                    return 1;
                elseif ($fsArr[0] < $ssArr[0])
                    return 0;
                else {
                    if ($fsArr[1] > $ssArr[1])
                        return 1;
                    elseif ($fsArr[1] < $ssArr[1])
                        return 0;
                    else {
                        return -1;
                    }
                }
            }
        }
    }
}