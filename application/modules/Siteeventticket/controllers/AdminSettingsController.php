<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_AdminSettingsController extends Core_Controller_Action_Admin {

    public function init() {

        //TAB CREATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteeventticket_admin_main_ticket');
    }

    public function indexAction() {           
        //GET NAVIGATION
        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteeventticket_admin_main_ticket', array(), 'siteeventticket_admin_main_settings');

        //FORM GENERATION
        $this->view->form = $form = new Siteeventticket_Form_Admin_Settings_Global();

        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $values = $form->getValues();

            //GENERATE ERROR IF DETAIL STEP YES & NO BUYER DETAIL SELECTED
            if ($values['siteeventticket_detail_step'] == '1' && empty($values['siteeventticket_buyer_details'])) {
                $error = "Please select atleast one buyer detail.";
                $form->addError($error);
                return;
            }

            include APPLICATION_PATH . '/application/modules/Siteeventticket/controllers/license/license2.php';
        }

        $this->updateQuries();
    }

    public function paymentAction() {
        //GET NAVIGATION
        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteeventticket_admin_main_ticket', array(), 'siteeventticket_admin_main_payment');

        //FORM GENERATION
        $this->view->form = $form = new Siteeventticket_Form_Admin_Settings_Payment();

        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $values = $form->getValues();

            //GENERATE ERROR ON NO PAYMENT GATEWAY SELECTED IN PAYMENT TO SELLER CASE
            if ($values['siteeventticket_payment_to_siteadmin'] == '0' && empty($values['siteeventticket_allowed_payment_gateway'])) {
                $error = "Please select atleast one payment gateway.";
                $form->addError($error);
                return;
            }
            
            if ($values['siteeventticket_payment_to_siteadmin'] == '0' && !empty($values['siteeventticket_thresholdnotification']) && $values['siteeventticket_thresholdnotificationamount'] <= 0) {
                $form->addError("Please enter threshold amount to enable email notification for commission bill payment.");
                return;
            }     
            
            if ($values['siteeventticket_payment_to_siteadmin'] == '0' && !empty($values['siteeventticket_thresholdnotification']) && empty($values['siteeventticket_thresholdnotify'])) {
                $form->addError("Please select to whom, notification for commission bill payment will send.");
                return;
            }               

            //UNSET ALL CHECKBOX VALUES BEFORE WE SET NEW VALUES.
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.admin.gateway')) {
                Engine_Api::_()->getApi('settings', 'core')->removeSetting('siteeventticket.admin.gateway');
            }
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.allowed.payment.gateway')) {
                Engine_Api::_()->getApi('settings', 'core')->removeSetting('siteeventticket.allowed.payment.gateway');
            }
            
            include APPLICATION_PATH . '/application/modules/Siteeventticket/controllers/license/license2.php';
        }
    }

    public function taxAction() {
        //GET NAVIGATION
        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteeventticket_admin_main_ticket', array(), 'siteeventticket_admin_main_taxes');

        //FORM GENERATION
        $this->view->form = $form = new Siteeventticket_Form_Admin_Settings_Tax();

        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
          include APPLICATION_PATH . '/application/modules/Siteeventticket/controllers/license/license2.php';
        }
    }

    public function updateQuries() {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1)) {
            $db->delete('engine4_activity_notificationtypes', array('module = ?' => 'siteevent', 'type IN (?)' => array('siteevent_accepted', 'siteevent_confirm_guests', 'siteevent_join', 'siteevent_member', 'siteevent_reject_guests', 'siteevent_request_disapprove', 'siteevent_rsvp_change')));

            $db->update('engine4_activity_actiontypes', array('enabled' => 0), array('module = ?' => 'siteevent', 'type IN (?)' => array('siteevent_join', 'siteevent_leave', 'siteevent_maybe_join', 'siteevent_mid_join', 'siteevent_mid_leave', 'siteevent_mid_maybe')));
        } else {
            $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
("siteevent_accepted", "siteevent", "Your request to join the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id} has been approved.", 0, "", 1),
("siteevent_confirm_guests", "siteevent", "{item:$subject} has confirmed you for the event {item:$object}.", 0, "", 1),
("siteevent_join", "siteevent", "{item:$subject} has joined the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id}.", 0, "", 1),
("siteevent_member", "siteevent", "You are now a guest of the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id}.", 0, "", 1),
("siteevent_reject_guests", "siteevent", "{item:$subject} has not confirmed you for the event {item:$object}.", 0, "", 1),
("siteevent_request_disapprove", "siteevent", "Your request to join the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id} has been rejected.", 0, "", 1),
("siteevent_rsvp_change", "siteevent", "{item:$subject} has changed RSVP for the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id} to {var:$status}.", 0, "", 1);');

            $db->update('engine4_activity_actiontypes', array('enabled' => 1), array('module = ?' => 'siteevent', 'type IN (?)' => array('siteevent_join', 'siteevent_leave', 'siteevent_maybe_join', 'siteevent_mid_join', 'siteevent_mid_leave', 'siteevent_mid_maybe')));
        }
    }

//ACTION FOR TICKETS PRINT FORMAT
    public function formatAction() {
        //GET NAVIGATION
        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteeventticket_admin_main_ticket', array(), 'siteeventticket_admin_main_format');

        //FORM GENERATION
        $this->view->form = $form = new Siteeventticket_Form_Admin_Settings_Format();

        // Get language
        $this->view->language = $language = preg_replace('/[^a-zA-Z_-]/', '.', $this->_getParam('language', 'en'));
        if (!Zend_Locale::isLocale($language)) {
            $form->removeElement('submit');
            return $form->addError('Please select a valid language.');
        }

        $content = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.format.bodyhtml.default');
        $language = str_replace("_", ".", $language);
        
        $previousFont = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.dompdffont', 'serif');
        
        // Check method/valid
        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            
            $values = $form->getValues();
            
            //RESET TO DEFAULT
            if ( isset($_POST['default']) && Engine_Api::_()->getApi('settings', 'core')->getSetting("siteeventticket_format_bodyhtml_" . $values['language']) ) {
              Engine_Api::_()->getApi('settings', 'core')->removeSetting("siteeventticket_format_bodyhtml_" . $values['language']);
              Engine_Api::_()->getApi('settings', 'core')->setSetting("siteeventticket_format_bodyhtml_" . $values['language'], $content);
            } else {
              foreach ( $values as $key => $value ) {
                if ( $key != 'submit' && $key != 'default' && $key != 'dummy_text' && $key != 'siteeventticket_adsimage' && $key != 'siteeventticket_dompdffont') {
                  if ( empty($value) ) {
                    $value = $content;
                  }
                  Engine_Api::_()->getApi('settings', 'core')->setSetting("siteeventticket_format_" . $key . "_" . $values['language'], $value);
                } elseif ( $key == 'siteeventticket_adsimage' || $key == 'siteeventticket_dompdffont') {
                  Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
                }
              }
            }
        }
        
        $newFont = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.dompdffont', 'serif');
        
        if(!empty($newFont) && !empty($previousFont) && $newFont != $previousFont) {
            $this->changeTicketPdfFont(array('previousFont' => $previousFont, 'newFont' => $newFont));
        }
        
        $bodyHTML = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.format.bodyhtml.' . $language, $content);
        $form->populate(array(
            'language' => $language,
            'bodyhtml' => $bodyHTML,
        ));
    }
    
    public function changeTicketPdfFont($fontsArray = array()) {
        
        $file = APPLICATION_PATH . '/application/libraries/dompdf/dompdf_config.inc.php';
        @chmod($file, 0777);
        $fileContent = file_get_contents($file);
        
        $definePreviousFont = 'def("DOMPDF_DEFAULT_FONT", "'.$fontsArray['previousFont'].'");';
        $defineNewFont = 'def("DOMPDF_DEFAULT_FONT", "'.$fontsArray['newFont'].'");';
        $fileContent = str_replace($definePreviousFont, $defineNewFont, $fileContent);
        @file_put_contents($file, $fileContent);        
    }

    public function saveFormatPreviewAction() {

        Engine_Api::_()->getApi('settings', 'core')->setSetting('siteeventticket.tinymcecontentpreview', $_POST['tinyMceContent']);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('siteeventticket.adsimagepreview', $_POST['adsimagepreview']);

        $this->_helper->layout->disableLayout(true);
    }
    
    public function showAttachmentPreviewAction() {

        if ( !Engine_Api::_()->hasModuleBootstrap('sitemailtemplates') || !file_exists('application/libraries/dompdf/dompdf_config.inc.php') ) {
          return $this->_forward('notfound');
        }

        require_once("application/libraries/dompdf/dompdf_config.inc.php");
        
        $html = $this->view->action("print-preview", 'admin-settings', 'siteeventticket', array());
        //$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        // If gzip enabled
        if ( file_exists(APPLICATION_PATH_SET . DS . 'cache.php') ) {
          $cache_array = include APPLICATION_PATH_SET . DS . 'cache.php';
          if ( isset($cache_array['frontend']['core']['gzip']) && !empty($cache_array['frontend']['core']['gzip']) ) {
              
            if ( ob_get_level() ) {
              ob_end_clean();
            }

            // If you want to open PDF in browser then set it 0, If need to download then set 1.
            $Attachment = 0;
            $filename = 'dompdf.pdf';
            $filepath = APPLICATION_PATH . '/temporary/' . $filename;
            $dompdf = new DOMPDF();
            $dompdf->load_html($html);
            $dompdf->render();

            $pdf = $dompdf->output(); // gets the PDF as a string

            @file_put_contents($filepath, $pdf); // save the pdf file on server
            // Prepare to open PDF OR to download
            $attach = (isset($Attachment) && $Attachment) ? "attachment" : "inline";
            header("Content-type: application/pdf");
            header("Content-Disposition: $attach; filename=\"$filename\"");
            header('Content-Transfer-Encoding: binary');
            header('Accept-Ranges: bytes');
            @readfile($filepath);
            exit();
          }
        }

        $dompdf = new DOMPDF();
        @$dompdf->load_html($html);
        @$dompdf->render();
        $showPreview = @$dompdf->stream("dompdf.pdf", array('Attachment' => 0));
        echo $showPreview;
        die;
      }    

    //ACTION TO TAKE PRINT OF INVOICE
    public function printPreviewAction() {

        if (!Engine_Api::_()->hasModuleBootstrap('sitemailtemplates') || !file_exists('application/libraries/dompdf/dompdf_config.inc.php')) {
            return $this->_forward('notfound');
        }

        $this->_helper->layout->setLayout('default-simple');

        // FETCH SITE LOGO OR TITLE
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_pages')->where('name = ?', 'header')->limit(1);

        $info = $select->query()->fetch();
        if (!empty($info)) {
            $page_id = $info['page_id'];

            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_content', array("params"))
                    ->where('page_id = ?', $page_id)
                    ->where("name LIKE '%core.menu-logo%'")
                    ->limit(1);
            $info = $select->query()->fetch();
            $params = json_decode($info['params']);

            if (!empty($params) && !empty($params->logo)) {
                $this->view->logo = $params->logo;
            }
        }

        $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');

        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Siteevent/View/Helper', 'Siteevent_View_Helper');

        //NEW ADDED
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $local_language = $view->locale()->getLocale()->__toString();
        $local_language = explode('_', $local_language);

        $bodyHTML = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.tinymcecontentpreview');

        $terms_of_use = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";

        $terms_of_use = Engine_Api::_()->seaocore()->seaocoreTruncateText($terms_of_use, 700);
        $ads_image = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.adsimagepreview');
        $ads_image = !empty($ads_image) ? "<img alt='' src='$ads_image' style='width:285px;height:400px;'>" : '';

        $bodyHTML .= '<div style="width: 650px; padding: 15px 0; font-family: arial; margin:0 auto;"><table style="width:100%;"><tr><td style="text-align: left; width: 325px; float: left; box-sizing: border-box; height: 380px; vertical-align: top;"><div style=" overflow:hidden;box-sizing: border-box;padding: 10px;height: 380px;width: 325px;border: 2px solid rgb(204, 204, 204);border-radius: 10px;color: #535353; font-size: 14px; line-height: 21px;">[terms_of_use]</div></td><td style="text-align: left; width: 300px; overflow: hidden; box-sizing: border-box; float: right; padding-left: 15px;height: 380px; vertical-align: top;">[ads_image]</td></tr><tr><td style="text-align: right; font-size: 24px; padding: 20px 0px;" colspan="2"><div style="float: right; font-size: 20px !important;border-radius: 10px;"><b>[site_logo]</b></div></td></tr></table></div>';

        $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium');

        $occurrence_starttime = date("Y-m-d", time() + 1);
        $creation_date = date("Y-m-d", time());
        
        if ($datetimeFormat == 'full') {
            $occurrence_date_time = $view->locale()->toDate($occurrence_starttime, array('size' => $datetimeFormat));
        }
        else {
            $occurrence_date_time = $view->locale()->toDate($occurrence_starttime, array('format' => 'EEEE')) . ', ' . $view->locale()->toDate($occurrence_starttime, array('size' => $datetimeFormat));
        }
        
        $occurrence_date_time = $occurrence_date_time . " - " . $view->locale()->toEventTime($occurrence_starttime, array('size' => $datetimeFormat));
        
        $ticket_date = $view->locale()->toDate($creation_date, array('size' => $datetimeFormat));
        $ticket_date_time = $ticket_date . " - " . $view->locale()->toEventTime($creation_date, array('size' => $datetimeFormat));
        $placehoders = array("[event_name]", "[event_date_time]", "[event_location]", "[event_venue]", "[ticket_date_time]", "[terms_of_use]", "[ads_image]");

        $commonValues = array('Event Title', $occurrence_date_time, 'Manhattan, NY 10036, United States', 'Times Square', $ticket_date_time, $terms_of_use, $ads_image);

        $this->view->bodyHTML = str_replace($placehoders, $commonValues, $bodyHTML);
    }

}
