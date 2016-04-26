<?php

class Winkgreeting_ComposeController extends Core_Controller_Action_User
{

  public function winkAction()
  {
    $recipient = Engine_Api::_()->getItem('user', (int) $this->_getParam('id'));
	
	$this->sendWink((int) $this->_getParam('id'));
    $this->view->form = $form = new Winkgreeting_Form_Confirmwink();
    $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
		  'format'=> 'smoothbox',
		  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Wink has been sent.')),
    ));	
	
	/*return $this->_forward('success', 'utility', 'core', array(
	      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Wink has been sent.')),
	      'redirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('id' => $recipient->username), 'user_profile', true),
    ));*/
  }
  
  public function greetingAction()
  {
    $recipient = Engine_Api::_()->getItem('user', (int) $this->_getParam('id'));
	
	$this->sendGreeting((int) $this->_getParam('id'));	
    $this->view->form = $form = new Winkgreeting_Form_Confirmgreeting();
    $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
		  'format'=> 'smoothbox',
		  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Greeting has been sent.')),
    ));	
	/*return $this->_forward('success', 'utility', 'core', array(
	      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Greeting has been sent.')),
	      'redirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('id' => $recipient->username), 'user_profile', true),
    ));*/
  }  
  
  public function confirmwinkAction()
  {
    // Make form
    $this->view->form = $form = new Winkgreeting_Form_Confirmwink();

	if ( $this->getRequest()->isPost() ) {
	  $this->sendWink((int) $this->_getParam('id'));
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
		  'format'=> 'smoothbox',
		  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Wink has been sent.')),
	  ));	  
	}  
  }

  public function confirmgreetingAction()
  {
    // Make form
    $this->view->form = $form = new Winkgreeting_Form_Confirmgreeting();
	
	if ( $this->getRequest()->isPost() ) {
	  $this->sendGreeting((int) $this->_getParam('id'));
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
		  'format'=> 'smoothbox',
		  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Greeting has been sent.')),
	  ));
	}  	
  }
  
  public function sendWink($id)
  {
    $recipient = Engine_Api::_()->getItem('user', $id);
	
    // Get params
    $multi = $this->_getParam('multi');
    $to = $id;
    $viewer = Engine_Api::_()->user()->getViewer();
    $toObject = null;

    // Build
    $isPopulated = false;
    if( !empty($to) && (empty($multi) || $multi == 'user') ) {
      $multi = null;
      // Prepopulate user
      $toUser = Engine_Api::_()->getItem('user', $to);
      if( $toUser instanceof User_Model_User &&
          (!$viewer->isBlockedBy($toUser) && !$toUser->isBlockedBy($viewer)) &&
          isset($toUser->user_id)) {
        $toObject = $toUser;
        $isPopulated = true;
      } else {
        $multi = null;
        $to = null;
      }
    } else if( !empty($to) && !empty($multi) ) {
      // Prepopulate group/event/etc
      $item = Engine_Api::_()->getItem($multi, $to);
      // Potential point of failure if primary key column is something other
      // than $multi . '_id'
      $item_id = $multi . '_id';
      if( $item instanceof Core_Model_Item_Abstract &&
          isset($item->$item_id) && (
            $item->isOwner($viewer) ||
            $item->authorization()->isAllowed($viewer, 'edit')
          )) {
        $toObject = $item;
        $isPopulated = true;
      } else {
        $multi = null;
        $to = null;
      }
    }	

    // Process
    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer = Engine_Api::_()->user()->getViewer();

      // Create conversation
      $sender_photo = $viewer->getPhotoUrl('thumb.icon');
      if (!$sender_photo) $sender_photo = '/application/modules/User/externals/images/nophoto_user_thumb_icon.png';
      
      $recipient_photo = $recipient->getPhotoUrl('thumb.icon');
      if (!$recipient_photo) $recipient_photo = '/application/modules/User/externals/images/nophoto_user_thumb_icon.png';
      // Main params
      $defaultParams = array(
        'host' => $_SERVER['HTTP_HOST'],
        'email' => $recipient->email,
        'date' => time(),
        'recipient_title' => $recipient->getTitle(),
        'recipient_link' => $recipient->getHref(),
        'recipient_photo' => $recipient_photo,
        'sender_title' => $viewer->getTitle(),
        'sender_link' => $viewer->getHref(),
        'sender_photo' => $sender_photo,
        'object_title' => '',//$object->getTitle(),
        'object_link' => '/messages/inbox',
        'object_photo' => '',//$object->getPhotoUrl('thumb.icon'),
        'object_description' => '',//$object->getDescription(),
      );
	  
    // Verify mail template type
    $mailTemplateTable = Engine_Api::_()->getDbtable('MailTemplates', 'core');
    $mailTemplate = $mailTemplateTable->fetchRow($mailTemplateTable->select()->where('type = ?', 'notify_wink_new'));

    if( !is_object($mailTemplate) ) {
      return;
    }	  
    // Build subject/body
    $translate = Zend_Registry::get('Zend_Translate');

    $subjectKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_SUBJECT');
    $bodyTextKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_BODY');
    $bodyHtmlKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_BODYHTML');

      // params
      $rParams = $defaultParams;

      // Check recipient
      if( $recipient instanceof Core_Model_Item_Abstract ) {
        $isMember = true;

        // Detect email and name
        $recipientEmail = $recipient->email;
        $recipientName = $recipient->getTitle();

        // Detect language
        if( !empty($rParams['language']) ) {
          $recipientLanguage = $rParams['language'];
        } else if( !empty($recipient->language) ) {
          $recipientLanguage = $recipient->language;
        } else {
          $recipientLanguage = $translate->getLocale();
        }
        if( !Zend_Locale::isLocale($recipientLanguage) ||
            $recipientLanguage == 'auto' ||
            !in_array($recipientLanguage, $translate->getList()) ) {
          $recipientLanguage = $translate->getLocale();
        }

        // add automatic params
        $rParams['email'] = $recipientEmail;
        $rParams['language'] = $recipientLanguage;
        $rParams['recipient_email'] = $recipientEmail;
        $rParams['recipient_title'] = $recipientName;
        $rParams['recipient_link'] = $recipient->getHref();
        $rParams['recipient_photo'] = $recipient->getPhotoUrl('thumb.normal');
        
      } else if( is_string($recipient) ) {
        $isMember = false;
        
        // Detect email and name
        if( strpos($recipient, ' ') !== false ) {
          $parts = explode(' ', $recipient, 2);
          $recipientEmail = $parts[0];
          $recipientName = trim($parts[1], ' <>');
        } else {
          $recipientEmail = $recipient;
          $recipientName = '';
        }

        // Detect language
        if( !empty($rParams['language']) ) {
          $recipientLanguage = $rParams['language'];
        //} else if( !empty($recipient->language) ) {
        //  $recipientLanguage = $recipient->language;
        } else {
          $recipientLanguage = $translate->getLocale();
        }
        if( !Zend_Locale::isLocale($recipientLanguage) ||
            $recipientLanguage == 'auto' ||
            !in_array($recipientLanguage, $translate->getList()) ) {
          $recipientLanguage = $translate->getLocale();
        }

        // add automatic params
        $rParams['email'] = $recipientEmail;
        $rParams['recipient_email'] = $recipientEmail;
        $rParams['recipient_title'] = $recipientName;
        $rParams['recipient_link'] = '';
        $rParams['recipient_photo'] = '';

      } else {
        continue;
      }

      // Get subject and body
      $subjectTemplate  = (string) $this->_translate($subjectKey,  $recipientLanguage);
      $bodyTextTemplate = (string) $this->_translate($bodyTextKey, $recipientLanguage);
      $bodyHtmlTemplate = (string) $this->_translate($bodyHtmlKey, $recipientLanguage);

      if( !($subjectTemplate) ) {
        //throw new Engine_Exception(sprintf('No subject translation available for system email "%s"', 'notify_wink_new'));
		$subjectTemplate  = (string) $this->_translate($subjectKey,  'en');
      }
      if( !$bodyHtmlTemplate && !$bodyTextTemplate ) {
        //throw new Engine_Exception(sprintf('No body translation available for system email "%s"', 'notify_wink_new'));
		$bodyTextTemplate = (string) $this->_translate($bodyTextKey, 'en');
		$bodyHtmlTemplate = (string) $this->_translate($bodyHtmlKey, 'en');
      }

      // Get headers and footers
      $headerPrefix = '_EMAIL_HEADER_' . ( $isMember ? 'MEMBER_' : '' );
      $footerPrefix = '_EMAIL_FOOTER_' . ( $isMember ? 'MEMBER_' : '' );
      
      $subjectHeader  = (string) $this->_translate($headerPrefix . 'SUBJECT',   $recipientLanguage);
      $subjectFooter  = (string) $this->_translate($footerPrefix . 'SUBJECT',   $recipientLanguage);
      $bodyTextHeader = (string) $this->_translate($headerPrefix . 'BODY',      $recipientLanguage);
      $bodyTextFooter = (string) $this->_translate($footerPrefix . 'BODY',      $recipientLanguage);
      $bodyHtmlHeader = (string) $this->_translate($headerPrefix . 'BODYHTML',  $recipientLanguage);
      $bodyHtmlFooter = (string) $this->_translate($footerPrefix . 'BODYHTML',  $recipientLanguage);
      
      // Do replacements
      foreach( $rParams as $var => $val ) {
        $raw = trim($var, '[]');
        $var = '[' . $var . ']';
        //if( !$val ) {
        //  $val = $var;
        //}
        // Fix nbsp
        $val = str_replace('&amp;nbsp;', ' ', $val);
        $val = str_replace('&nbsp;', ' ', $val);
        // Replace
        $subjectTemplate  = str_replace($var, $val, $subjectTemplate);
        $bodyTextTemplate = str_replace($var, $val, $bodyTextTemplate);
        $bodyHtmlTemplate = str_replace($var, $val, $bodyHtmlTemplate);
        $subjectHeader    = str_replace($var, $val, $subjectHeader);
        $subjectFooter    = str_replace($var, $val, $subjectFooter);
        $bodyTextHeader   = str_replace($var, $val, $bodyTextHeader);
        $bodyTextFooter   = str_replace($var, $val, $bodyTextFooter);
        $bodyHtmlHeader   = str_replace($var, $val, $bodyHtmlHeader);
        $bodyHtmlFooter   = str_replace($var, $val, $bodyHtmlFooter);
      }

      // Do header/footer replacements
      $subjectTemplate  = str_replace('[header]', $subjectHeader, $subjectTemplate);
      $subjectTemplate  = str_replace('[footer]', $subjectFooter, $subjectTemplate);
      $bodyTextTemplate = str_replace('[header]', $bodyTextHeader, $bodyTextTemplate);
      $bodyTextTemplate = str_replace('[footer]', $bodyTextFooter, $bodyTextTemplate);
      $bodyHtmlTemplate = str_replace('[header]', $bodyHtmlHeader, $bodyHtmlTemplate);
      $bodyHtmlTemplate = str_replace('[footer]', $bodyHtmlFooter, $bodyHtmlTemplate);

      // Check for missing text or html
      if( !$bodyHtmlTemplate ) {
        $bodyHtmlTemplate = nl2br($bodyTextTemplate);
      } else if( !$bodyTextTemplate ) {
        $bodyTextTemplate = strip_tags($bodyHtmlTemplate);
      }
	  
      $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
        $viewer,
        $recipient,
        $subjectTemplate,
        $bodyTextTemplate
      );

      // Send notifications
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
          $recipient,
          $viewer,
          $conversation,
          'wink_new'
        );
		
		//if use Advanced Mail
	    $table = Engine_Api::_()->getDbtable('recipients', 'messages');
        $sql = "SHOW TABLES LIKE '%advancedmail_ads%'";
        $table_name = $table->getAdapter()->query($sql)->fetch();
		if ($table_name) {
    	  // Get current row ADD TO ADS params
	  	    $table = Engine_Api::_()->getDbtable('ads', 'advancedmail');
		    $select = $table->select()
		      ->where('user_id = ?', $recipient->getIdentity())
			  ->where('conversation_id = ?', $conversation->conversation_id)
		      ->limit(1);

		    $row = $table->fetchRow($select);
		    // Save
		    if( null == $row )
		    {
		      $row = $table->createRow();
		      $row->user_id = $recipient->getIdentity();
		      $row->conversation_id = $conversation->conversation_id;
			  $row->params = 0;	  
		    }
			else {
		      $row->params = 0;
			}  
		    $row->save();
			
		    $select = $table->select()
		      ->where('user_id = ?', $viewer->getIdentity())
			  ->where('conversation_id = ?', $conversation->conversation_id)
		      ->limit(1);

		    $row = $table->fetchRow($select);
		    // Save
		    if( null == $row )
		    {
		      $row = $table->createRow();
		      $row->user_id = $viewer->getIdentity();
		      $row->conversation_id = $conversation->conversation_id;
			  $row->params = 0;	  
		    }
			else {
		      $row->params = 0;
			}  
		    $row->save();
	  }
	  	
      // Increment messages counter
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

      // Commit
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      echo $e;die();
      throw $e;
    }  
  }  
  
  public function sendGreeting($id)
  {
    $recipient = Engine_Api::_()->getItem('user', $id);
	
    // Get params
    $multi = $this->_getParam('multi');
    $to = $id;
    $viewer = Engine_Api::_()->user()->getViewer();
    $toObject = null;

    // Build
    $isPopulated = false;
    if( !empty($to) && (empty($multi) || $multi == 'user') ) {
      $multi = null;
      // Prepopulate user
      $toUser = Engine_Api::_()->getItem('user', $to);
      if( $toUser instanceof User_Model_User &&
          (!$viewer->isBlockedBy($toUser) && !$toUser->isBlockedBy($viewer)) &&
          isset($toUser->user_id)) {
        $toObject = $toUser;
        $isPopulated = true;
      } else {
        $multi = null;
        $to = null;
      }
    } else if( !empty($to) && !empty($multi) ) {
      // Prepopulate group/event/etc
      $item = Engine_Api::_()->getItem($multi, $to);
      // Potential point of failure if primary key column is something other
      // than $multi . '_id'
      $item_id = $multi . '_id';
      if( $item instanceof Core_Model_Item_Abstract &&
          isset($item->$item_id) && (
            $item->isOwner($viewer) ||
            $item->authorization()->isAllowed($viewer, 'edit')
          )) {
        $toObject = $item;
        $isPopulated = true;
      } else {
        $multi = null;
        $to = null;
      }
    }	

    // Process
    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer = Engine_Api::_()->user()->getViewer();

      // Create conversation
	  
      $sender_photo = $viewer->getPhotoUrl('thumb.icon');
      if (!$sender_photo) $sender_photo = '/application/modules/User/externals/images/nophoto_user_thumb_icon.png';
      
      $recipient_photo = $recipient->getPhotoUrl('thumb.icon');
      if (!$recipient_photo) $recipient_photo = '/application/modules/User/externals/images/nophoto_user_thumb_icon.png';
      // Main params
      $defaultParams = array(
        'host' => $_SERVER['HTTP_HOST'],
        'email' => $recipient->email,
        'date' => time(),
        'recipient_title' => $recipient->getTitle(),
        'recipient_link' => $recipient->getHref(),
        'recipient_photo' => $recipient_photo,
        'sender_title' => $viewer->getTitle(),
        'sender_link' => $viewer->getHref(),
        'sender_photo' => $sender_photo,
        'object_title' => '',//$object->getTitle(),
        'object_link' => '/messages/inbox',
        'object_photo' => '',//$object->getPhotoUrl('thumb.icon'),
        'object_description' => '',//$object->getDescription(),
      );
	  
    // Verify mail template type
    $mailTemplateTable = Engine_Api::_()->getDbtable('MailTemplates', 'core');
    $mailTemplate = $mailTemplateTable->fetchRow($mailTemplateTable->select()->where('type = ?', 'notify_greeting_new'));
    if( !is_object($mailTemplate) ) {
      return;
    }	  

    // Build subject/body
    $translate = Zend_Registry::get('Zend_Translate');

    $subjectKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_SUBJECT');
    $bodyTextKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_BODY');
    $bodyHtmlKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_BODYHTML');

      // params
      $rParams = $defaultParams;

      // Check recipient
      if( $recipient instanceof Core_Model_Item_Abstract ) {
        $isMember = true;

        // Detect email and name
        $recipientEmail = $recipient->email;
        $recipientName = $recipient->getTitle();

        // Detect language
        if( !empty($rParams['language']) ) {
          $recipientLanguage = $rParams['language'];
        } else if( !empty($recipient->language) ) {
          $recipientLanguage = $recipient->language;
        } else {
          $recipientLanguage = $translate->getLocale();
        }
        if( !Zend_Locale::isLocale($recipientLanguage) ||
            $recipientLanguage == 'auto' ||
            !in_array($recipientLanguage, $translate->getList()) ) {
          $recipientLanguage = $translate->getLocale();
        }

        // add automatic params
        $rParams['email'] = $recipientEmail;
        $rParams['language'] = $recipientLanguage;
        $rParams['recipient_email'] = $recipientEmail;
        $rParams['recipient_title'] = $recipientName;
        $rParams['recipient_link'] = $recipient->getHref();
        $rParams['recipient_photo'] = $recipient->getPhotoUrl('thumb.normal');
        
      } else if( is_string($recipient) ) {
        $isMember = false;
        
        // Detect email and name
        if( strpos($recipient, ' ') !== false ) {
          $parts = explode(' ', $recipient, 2);
          $recipientEmail = $parts[0];
          $recipientName = trim($parts[1], ' <>');
        } else {
          $recipientEmail = $recipient;
          $recipientName = '';
        }

        // Detect language
        if( !empty($rParams['language']) ) {
          $recipientLanguage = $rParams['language'];
        //} else if( !empty($recipient->language) ) {
        //  $recipientLanguage = $recipient->language;
        } else {
          $recipientLanguage = $translate->getLocale();
        }
        if( !Zend_Locale::isLocale($recipientLanguage) ||
            $recipientLanguage == 'auto' ||
            !in_array($recipientLanguage, $translate->getList()) ) {
          $recipientLanguage = $translate->getLocale();
        }

        // add automatic params
        $rParams['email'] = $recipientEmail;
        $rParams['recipient_email'] = $recipientEmail;
        $rParams['recipient_title'] = $recipientName;
        $rParams['recipient_link'] = '';
        $rParams['recipient_photo'] = '';

      } else {
        continue;
      }

      // Get subject and body
      $subjectTemplate  = (string) $this->_translate($subjectKey,  $recipientLanguage);
      $bodyTextTemplate = (string) $this->_translate($bodyTextKey, $recipientLanguage);
      $bodyHtmlTemplate = (string) $this->_translate($bodyHtmlKey, $recipientLanguage);

      if( !($subjectTemplate) ) {
        //throw new Engine_Exception(sprintf('No subject translation available for system email "%s"', $type));
		$subjectTemplate  = (string) $this->_translate($subjectKey,  'en');
      }
      if( !$bodyHtmlTemplate && !$bodyTextTemplate ) {
        //throw new Engine_Exception(sprintf('No body translation available for system email "%s"', $type));
        $bodyTextTemplate = (string) $this->_translate($bodyTextKey, 'en');
        $bodyHtmlTemplate = (string) $this->_translate($bodyHtmlKey, 'en');		
      }

      // Get headers and footers
      $headerPrefix = '_EMAIL_HEADER_' . ( $isMember ? 'MEMBER_' : '' );
      $footerPrefix = '_EMAIL_FOOTER_' . ( $isMember ? 'MEMBER_' : '' );
      
      $subjectHeader  = (string) $this->_translate($headerPrefix . 'SUBJECT',   $recipientLanguage);
      $subjectFooter  = (string) $this->_translate($footerPrefix . 'SUBJECT',   $recipientLanguage);
      $bodyTextHeader = (string) $this->_translate($headerPrefix . 'BODY',      $recipientLanguage);
      $bodyTextFooter = (string) $this->_translate($footerPrefix . 'BODY',      $recipientLanguage);
      $bodyHtmlHeader = (string) $this->_translate($headerPrefix . 'BODYHTML',  $recipientLanguage);
      $bodyHtmlFooter = (string) $this->_translate($footerPrefix . 'BODYHTML',  $recipientLanguage);
      
      // Do replacements
      foreach( $rParams as $var => $val ) {
        $raw = trim($var, '[]');
        $var = '[' . $var . ']';
        //if( !$val ) {
        //  $val = $var;
        //}
        // Fix nbsp
        $val = str_replace('&amp;nbsp;', ' ', $val);
        $val = str_replace('&nbsp;', ' ', $val);
        // Replace
        $subjectTemplate  = str_replace($var, $val, $subjectTemplate);
        $bodyTextTemplate = str_replace($var, $val, $bodyTextTemplate);
        $bodyHtmlTemplate = str_replace($var, $val, $bodyHtmlTemplate);
        $subjectHeader    = str_replace($var, $val, $subjectHeader);
        $subjectFooter    = str_replace($var, $val, $subjectFooter);
        $bodyTextHeader   = str_replace($var, $val, $bodyTextHeader);
        $bodyTextFooter   = str_replace($var, $val, $bodyTextFooter);
        $bodyHtmlHeader   = str_replace($var, $val, $bodyHtmlHeader);
        $bodyHtmlFooter   = str_replace($var, $val, $bodyHtmlFooter);
      }

      // Do header/footer replacements
      $subjectTemplate  = str_replace('[header]', $subjectHeader, $subjectTemplate);
      $subjectTemplate  = str_replace('[footer]', $subjectFooter, $subjectTemplate);
      $bodyTextTemplate = str_replace('[header]', $bodyTextHeader, $bodyTextTemplate);
      $bodyTextTemplate = str_replace('[footer]', $bodyTextFooter, $bodyTextTemplate);
      $bodyHtmlTemplate = str_replace('[header]', $bodyHtmlHeader, $bodyHtmlTemplate);
      $bodyHtmlTemplate = str_replace('[footer]', $bodyHtmlFooter, $bodyHtmlTemplate);

      // Check for missing text or html
      if( !$bodyHtmlTemplate ) {
        $bodyHtmlTemplate = nl2br($bodyTextTemplate);
      } else if( !$bodyTextTemplate ) {
        $bodyTextTemplate = strip_tags($bodyHtmlTemplate);
      }	  
	  
      $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
        $viewer,
        $recipient,
        $subjectTemplate,
        $bodyTextTemplate
      );

      // Send notifications
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
          $recipient,
          $viewer,
          $conversation,
          'greeting_new'
        );

		//if use Advanced Mail
	    $table = Engine_Api::_()->getDbtable('recipients', 'messages');
        $sql = "SHOW TABLES LIKE '%advancedmail_ads%'";
        $table_name = $table->getAdapter()->query($sql)->fetch();
		if ($table_name) {
    	  // Get current row ADD TO ADS params
	  	    $table = Engine_Api::_()->getDbtable('ads', 'advancedmail');
		    $select = $table->select()
		      ->where('user_id = ?', $recipient->getIdentity())
			  ->where('conversation_id = ?', $conversation->conversation_id)
		      ->limit(1);

		    $row = $table->fetchRow($select);
		    // Save
		    if( null == $row )
		    {
		      $row = $table->createRow();
		      $row->user_id = $recipient->getIdentity();
		      $row->conversation_id = $conversation->conversation_id;
			  $row->params = 0;	  
		    }
			else {
		      $row->params = 0;
			}  
		    $row->save();
			
		    $select = $table->select()
		      ->where('user_id = ?', $viewer->getIdentity())
			  ->where('conversation_id = ?', $conversation->conversation_id)
		      ->limit(1);

		    $row = $table->fetchRow($select);
		    // Save
		    if( null == $row )
		    {
		      $row = $table->createRow();
		      $row->user_id = $viewer->getIdentity();
		      $row->conversation_id = $conversation->conversation_id;
			  $row->params = 0;	  
		    }
			else {
		      $row->params = 0;
			}  
		    $row->save();
	  }

      // Increment messages counter
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

      // Commit
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      echo $e;die();
      throw $e;
    }
  }  

  protected function _translate($key, $locale, $noDefault = false)
  {
    $translate = Zend_Registry::get('Zend_Translate');
    $value = $translate->translate($key, $locale);
    if( $value == $key || '' == trim($value) ) {
      if( $noDefault ) {
        return false;
      } else {
        $value = $translate->translate($key);
        if( $value == $key || '' == trim($value) ) {
          return false;
        }
      }
    }
    return $value;
  }  
}
