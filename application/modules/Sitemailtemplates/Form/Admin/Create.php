<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemailtemplates_Form_Admin_Create extends Engine_Form {

    public function init() {

        $this
                ->setTitle('Create New Template')
                ->setDescription('Create a new Template here. Below, you will be able to configure and customize the design of the email template based on various parameters. You can also send sample emails to yourself to see the template design.');

        $siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1);
        $email = Engine_API::_()->seaocore()->getSuperAdminEmailAddress();

        $font_fmily_list = array("Andale Mono" => "Andale Mono", "Arial" => "Arial", "Arial Black" => "Arial Black", "Book Antiqua" => "Book Antiqua", "Comic Scan MS" => "Comic Scan MS", "Courier New" => "Courier New", "Georgia" => "Georgia", "Helvetica" => "Helvetica", "Impact" => "Impact", "Symbol" => "Symbol", "Tahoma" => "Tahoma", "Terminal" => "Terminal", "Times New Roman" => "Times New Roman", "Trebuchet MS" => "Trebuchet MS", "Verdana" => "Verdana", "Webdings" => "Webdings", "Wingdings" => "Wingdings", "Century Gothic" => "Century Gothic");

        $this->addElement('Text', 'template_title', array(
            'label' => 'Template Name',
            'description' => 'Enter the name of this Template. This name is only for your indicative purpose, and will not be displayed to users.',
            'required' => 'true',
            'maxLength' => 128,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
        )));

        $this->addElement('Radio', 'show_title', array(
            'label' => 'Show Site Title',
            'description' => "Do you want to display the site title in the email template header? (Selecting ‘Yes’ over here will enable you to enter the title for your site.)",
            'multioptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showSiteTitle(this.value)',
            'value' => 1,
        ));

        $this->addElement('Text', 'site_title', array(
            'label' => 'Site Title',
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags',
                new Engine_Filter_StringLength(array('max' => '63'))
            ),
            'value' => $siteTitle
        ));

        $this->addElement('Select', 'sitetitle_fontsize', array(
            'label' => 'Site Title Font Size',
            'description' => 'Select the font size of the site title.',
            'multiOptions' => array(
                '8' => '8',
                '9' => '9',
                '10' => '10',
                '11' => '11',
                '12' => '12',
                '13' => '13',
                '14' => '14',
                '15' => '15',
                '17' => '17',
                '19' => '19',
                '21' => '21',
                '23' => '23',
                '25' => '25',
                '27' => '27',
                '29' => '29',
                '31' => '31',
                '33' => '33',
                '35' => '35',
            ),
            'value' => 17,
        ));

        $this->addElement('Select', 'sitetitle_fontfamily', array(
            'label' => 'Site Title Font Family',
            'description' => 'Select the font family of the site title.',
            'multiOptions' => $font_fmily_list,
            'value' => 'Arial',
        ));

        $this->addElement('Select', 'sitetitle_location', array(
            'label' => 'Site Title Position',
            'description' => 'Where should Site Title appear in the email template?',
            'multiOptions' => array(
                'header' => 'Header',
                'body' => 'Body'
            ),
            'value' => 'header',
        ));

        $this->addElement('Select', 'sitetitle_position', array(
            'label' => 'Site Title Alignment',
            'description' => 'Where do you want to display the title of your site?',
            'multiOptions' => array(
                'left' => 'Left',
                'right' => 'Right',
                'center' => 'Center'
            ),
            'value' => 'left',
        ));

        //VALUE FOR ENABLE/DISABLE PACKAGE
        $this->addElement('Radio', 'show_icon', array(
            'label' => 'Show Site Icon / Logo',
            'description' => 'Do you want to display the site icon / logo in the email template header? (Selecting ‘Yes’ over here will enable you to choose the icon / logo for your site.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showlogoOptions(this.value)',
            'value' => 1,
        ));

        // Get available files (Icon for activity Feed).
        $logoOptions = array('application/modules/Sitemailtemplates/externals/images/web.png' => 'Default Icon', 'application/modules/Sitemailtemplates/externals/images/default-logo.png' => 'Default Logo');
        $imageExtensions = array('gif', 'jpg', 'jpeg', 'png');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
        foreach ($it as $file) {
            if ($file->isDot() || !$file->isFile())
                continue;
            $basename = basename($file->getFilename());
            if (!($pos = strrpos($basename, '.')))
                continue;
            $ext = strtolower(ltrim(substr($basename, $pos), '.'));
            if (!in_array($ext, $imageExtensions))
                continue;
            $logoOptions['public/admin/' . $basename] = $basename;
        }

        $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_("You have not
     uploaded an image for site logo. Please upload an image.") . "</span></div>";
        $URL = $view->baseUrl() . "/admin/files";
        $click = '<a href="' . $URL . '" target="_blank">over here</a>';
        $customBlocks = sprintf("Upload an icon / logo for your website %s. The recommended dimensions of this icon / logo are between 20 x 20 px to 200 x 100 px. (Once you upload a new icon / logo at the link mentioned, then refresh this page to see its preview below after selection.)", $click);

        if (!empty($logoOptions)) {
            $this->addElement('Select', 'img_path', array(
                'label' => 'Choose Site Icon / Logo',
                'description' => $customBlocks,
                'multiOptions' => $logoOptions,
                'onchange' => "updateTextFields(this.value)",
            ));
            $this->getElement('img_path')->getDecorator('Description')->setOptions(array('placement' =>
                'PREPEND', 'escape' => false));
        }

        $logo_photo = 'application/modules/Sitemailtemplates/externals/images/web.png';
        if (!empty($logo_photo)) {
            $photoName = $view->baseUrl() . '/' . $logo_photo;
            $description = "<img src='$photoName' style='max-height:100px;' />";
        }
        //VALUE FOR LOGO PREVIEW.
        $this->addElement('Dummy', 'logo_photo_preview', array(
            'label' => 'Site Icon / Logo Preview',
            'description' => $description,
        ));

        $this->logo_photo_preview
                ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

        $this->addElement('Select', 'sitelogo_location', array(
            'label' => 'Site Icon / Logo Position',
            'description' => 'Where should site icon / logo appear in the email template?',
            'multiOptions' => array(
                'header' => 'Header',
                'body' => 'Body'
            ),
            'value' => 'header',
        ));

        $this->addElement('Select', 'sitelogo_position', array(
            'label' => 'Site Icon / Logo Alignment',
            'description' => 'Where do you want to display the icon / logo of your site?',
            'multiOptions' => array(
                'left' => 'Left',
                'right' => 'Right',
                'center' => 'Center'
            ),
            'value' => 'left',
        ));

        //VALUE FOR ENABLE/DISABLE PACKAGE
        $this->addElement('Radio', 'show_tagline', array(
            'label' => 'Show Tag Line',
            'description' => 'Do you want to display the tag line in the email template header? (Selecting ‘Yes’ over here will enable you to enter the tag line of your site.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showtaglineptions(this.value)',
            'value' => 1,
        ));

        $this->addElement('Text', 'tagline_title', array(
            'label' => 'Tag Line',
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags',
                new Engine_Filter_StringLength(array('max' => '121'))
            ),
            'value' => 'There\'s a lot to do here!',
        ));

        $this->addElement('Select', 'tagline_fontsize', array(
            'label' => 'Tag Line Text Font Size',
            'description' => 'Select the font size of the tag line text.',
            'multiOptions' => array(
                '8' => '8',
                '9' => '9',
                '10' => '10',
                '11' => '11',
                '12' => '12',
                '13' => '13',
                '14' => '14',
                '15' => '15',
                '17' => '17',
                '19' => '19',
                '21' => '21',
                '23' => '23',
                '25' => '25',
                '27' => '27',
                '29' => '29',
                '31' => '31',
                '33' => '33',
                '35' => '35',
            ),
            'value' => 11,
        ));

        $this->addElement('Select', 'tagline_fontfamily', array(
            'label' => 'Tag Line Text Font Family',
            'description' => 'Select the font family of the tag line text.',
            'multiOptions' => $font_fmily_list,
            'value' => 'Arial',
        ));

        $this->addElement('Select', 'tagline_location', array(
            'label' => 'Tag Line Position',
            'description' => 'Where should tag line appear in the email template?',
            'multiOptions' => array(
                'header' => 'Header',
                'body' => 'Body',
                'above_header' => 'Above Header'
            ),
            'value' => 'Header',
        ));

        $this->addElement('Select', 'tagline_position', array(
            'label' => 'Tag Line Alignment',
            'description' => 'Where do you want to display the tag line of your site?',
            'multiOptions' => array(
                'left' => 'Left',
                'right' => 'Right',
                'center' => 'Center'
            ),
            'value' => 'right',
        ));

        $this->addElement('Text', 'header_bgcol', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowHeader.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Select', 'header_outpadding', array(
            'label' => 'Email Template Header Padding',
            'description' => 'Select the padding of the header background of email template. (This value will define the space between the header border and the header content.)',
            'multiOptions' => array(
                '0' => '0',
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '8' => '8',
                '9' => '9',
                '10' => '10',
                '11' => '11',
                '12' => '12',
                '13' => '13',
                '14' => '14',
                '15' => '15'
            ),
            'value' => 10,
            'disableTranslator' => 'true'
        ));

        $this->addElement('Text', 'header_titlecolor', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowTitle.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Text', 'header_tagcolor', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowtaglineTitle.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Text', 'header_bottomcolor', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowheaderbottomBorder.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Select', 'header_bottomwidth', array(
            'label' => 'Email Template Header Bottom Border width',
            'description' => 'Select the width of the header bottom border of email template.',
            'multiOptions' => array(
                '0' => '0',
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '8' => '8',
                '9' => '9',
                '10' => '10'
            ),
            'value' => 1,
            'disableTranslator' => 'true'
        ));

        $this->addElement('Text', 'footer_bottomcol', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowfooterBorder.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Select', 'footer_bottomwidth', array(
            'label' => 'Email Template Footer Border width',
            'description' => 'Select the width of the footer border of email template.',
            'multiOptions' => array(
                '0' => '0',
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '8' => '8',
                '9' => '9',
                '10' => '10'
            ),
            'value' => 1,
            'disableTranslator' => 'true'
        ));

        $this->addElement('Text', 'lr_bordercolor', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowBgcolor.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Select', 'lr_bottomwidth', array(
            'label' => 'Email Template Left and Right Border width',
            'description' => 'Select the width of the left and right border of the email template.',
            'multiOptions' => array(
                '0' => '0',
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '8' => '8',
                '9' => '9',
                '10' => '10'
            ),
            'value' => 1,
            'disableTranslator' => 'true'
        ));

        $this->addElement('Text', 'body_outerbgcol', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowouterBgcolor.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Text', 'body_innerbgcol', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowinnerBgcolor.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Text', 'signature_bgcol', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowsignatureBgcolor.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Checkbox', 'active_template', array(
            'label' => 'Yes, activate this email template for all outgoing emails.(Note: At any time only one template can be activated. Thus, if you activate this template, then the current activated template will be deactivated.)',
            'value' => 0,
            'description' => 'Activate This Template for All Emails',
        ));

        //SEND TEST EMAIL
        $this->addElement('Checkbox', 'testemail_demo', array(
            'label' => 'Send me a test email.',
            'value' => 0,
            'description' => 'Test Email',
            'onclick' => 'showOption(this.checked)'
        ));

        //EMAIL ID FOR TESTING
        $this->addElement('Text', 'testemail_admin', array(
            'label' => 'Email ID for Testing',
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('EmailAddress', true),
            ),
            'value' => $email
        ));

        $this->testemail_admin->getValidator('NotEmpty')->setMessage('Please enter a valid email address.', 'isEmpty');
        $this->testemail_admin->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);

        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
