<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Adsettings.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class sitegroup_Form_Admin_Adsettings extends Engine_Form {

  public function init() {

    $enable_ads = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
    if (!$enable_ads) {
      $this->addElement('Dummy', 'note', array(
          'description' => '<div class="tip"><span>' . sprintf(Zend_Registry::get('Zend_Translate')->_('This plugin provides deep integration for advertising using the "%1$sAdvertisements / Community Ads Plugin%2$s". Please install and enable this plugin to configure settings for the various ad positions and widgets available. If you do not have this plugin yet, click here to view its details and purchase it: %1$shttp://www.socialengineaddons.com/socialengine-advertisements-community-ads-plugin%2$s.'), '<a href="http://www.socialengineaddons.com/socialengine-advertisements-community-ads-plugin" target="_blank">', '</a>') . '</span></div>',
          'decorators' => array(
              'ViewHelper', array(
                  'description', array('placement' => 'APPEND', 'escape' => false)
          ))
      ));
    }

    $enable_ads = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
    if ($enable_ads) {
      $this
              ->setTitle('Ad Settings')
              ->setDescription('This plugin provides seamless integration with the "Advertisements / Community Ads Plugin". Attractive advertising can be done using the many available, well designed ad positions in this plugin. Below, you can configure the settings for the various ad positions and widgets.');
      $this->addElement('Radio', 'sitegroup_adpreview', array(
          'label' => 'Sample Ad Widget',
           'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('Do you want to show a Sample Ad of a Group on its profile in the Info widget? (This widget will only be visible to group admins and will tempt them to create an Ad for their Group. Click %s to preview this widget.)'), '<a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'application/modules/Sitegroup/externals/images/ad_preview.png\');"></a>'),
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adpreview', 1),
      ));
      $this->sitegroup_adpreview->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

      $this->addElement('Radio', 'sitegroup_adcreatelink', array(
          'label' => 'Advertise your Group Widget',
          'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('Do you want to show the Advertise your Group widget on Group Profile in the Info widget? (This widget will only be visible to group admins and it displays a catchy phrase to tempt them to create an Ad for their Group. It also has a link to Create an Ad. Click %s to preview this widget.)'), '<a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'application/modules/Sitegroup/externals/images/ad_content.png\');">here</a>'),
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adcreatelink', 1),
      ));
      $this->sitegroup_adcreatelink->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

      $this->addElement('Radio', 'sitegroup_communityads', array(
          'label' => 'Community Ads in this plugin',
          'description' => 'Do you want to show community ads in the various positions available in this plugin? (Below, you will be able to choose for every individual position. If you do not want to show ads in a particular position, then please enter the value "0" for it below.). If you have enabled Packages for Groups from Global Settings, then for each package, you can configure if community ads display should be enabled on their Groups (You can use this to give extra privileges to Groups of special / paid packages by making them free of ads display.).',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onclick' => 'showads(this.value)',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1),
      ));

//       if ((Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum') || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
//         $lightbox_photos = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.photolightbox.show', 1);
//         if (!empty($lightbox_photos)) {
//           $this->addElement('Radio', 'sitegroup_lightboxads', array(
//               'label' => 'Ads in Photos Lightbox',
//               'description' => 'Do you want to show ads in ajax lightbox for viewing photos of albums of groups?',
//               'multiOptions' => array(
//                   1 => 'Yes',
//                   0 => 'No'
//               ),
//               'onclick' => 'showlightboxads(this.value)',
//               'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.lightboxads', 1),
//           ));
// 
//           $this->addElement('Radio', 'sitegroup_adtype', array(
//               'label' => 'Type of Ads in Photos Lightbox',
//               'description' => 'Select the type of ads you want to show in the ajax lightbox for viewing photos of albums of groups.',
//               'multiOptions' => array(
//                   3 => 'All',
//                   2 => 'Sponsored Ads',
//                   1 => 'Featured Ads',
//                   0 => 'Both Sponsored and Featured Ads'
//               ),
//               'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adtype', 3),
//           ));
//         }
//       }

      $this->addElement('Text', 'sitegroup_admylikes', array(
          'label' => 'Groups I Like Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in group i like group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.admylikes', 3),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitegroup_adnotewidget', array(
          'label' => 'Group Profile Notes Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in group profile notes widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adnotewidget', 3),
      ));

      $this->addElement('Text', 'sitegroup_adnoteview', array(
          'label' => 'Group Notes View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group note\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adnoteview', 3),
      ));

      $this->addElement('Text', 'sitegroup_adnotebrowse', array(
          'label' => 'Group Notes Browse Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group note\'s browse group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adnotebrowse', 3),
      ));

      $this->addElement('Text', 'sitegroup_adnotecreate', array(
          'label' => 'Group Note\'s Create Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group note\'s create group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adnotecreate', 3),
      ));

      $this->addElement('Text', 'sitegroup_adnoteedit', array(
          'label' => 'Group Note\'s Edit Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group note\'s edit group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adnoteedit', 3),
      ));

      $this->addElement('Text', 'sitegroup_adnotedelete', array(
          'label' => 'Group Note\'s Delete Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group note\'s delete group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adnotedelete', 1),
      ));


      $this->addElement('Text', 'sitegroup_adnoteaddphoto', array(
          'label' => 'Group Note\'s Add Photos Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group note\'s add photos group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adnoteaddphoto', 1),
      ));

      $this->addElement('Text', 'sitegroup_adnoteeditphoto', array(
          'label' => 'Group Note\'s Edit Photo Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group note\'s edit photo group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adnoteeditphoto', 5),
      ));

      $this->addElement('Text', 'sitegroup_adnotesuccess', array(
          'label' => 'Group Note\'s Creation Success Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group note\'s creation success group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adnotesuccess', 1),
      ));
    }

    if ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup'))) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitegroup_adeventwidget', array(
          'label' => 'Group Profile Events Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in group profile events widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adeventwidget', 3),
      ));

      $this->addElement('Text', 'sitegroup_adeventview', array(
          'label' => 'Group Event\'s View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group event\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adeventview', 2),
      ));

      $this->addElement('Text', 'sitegroup_adeventbrowse', array(
          'label' => 'Group Event\'s Browse Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group event\'s browse group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adeventbrowse', 2),
      ));

      $this->addElement('Text', 'sitegroup_adeventcreate', array(
          'label' => 'Group Event\'s Create Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group event\'s create group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adeventcreate', 2),
      ));

      $this->addElement('Text', 'sitegroup_adeventedit', array(
          'label' => 'Group Event\'s Edit Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group event\'s edit group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adeventedit', 2),
      ));

      $this->addElement('Text', 'sitegroup_adeventdelete', array(
          'label' => 'Group Event\'s Delete Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group event\'s delete group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adeventdelete', 1),
      ));
        $this->addElement('Text', 'sitegroup_adeventaddphoto', array(
          'label' => 'Group Event\'s Add Photos Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group event\'s add photos group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adeventaddphoto', 1),
      ));

      $this->addElement('Text', 'sitegroup_adeventeditphoto', array(
          'label' => 'Group Event\'s Edit Photo Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group event\'s edit photo group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adeventeditphoto', 5),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitegroup_adalbumwidget', array(
          'label' => 'Group Profile Albums Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in group profile albums widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumwidget', 3),
      ));

      $this->addElement('Text', 'sitegroup_adalbumview', array(
          'label' => 'Group Album\'s Browse Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group album\'s browse group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumview', 3),
      ));

      $this->addElement('Text', 'sitegroup_adalbumbrowse', array(
          'label' => 'Group Album\'s View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group album\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumbrowse', 3),
      )); 

      $this->addElement('Text', 'sitegroup_adalbumcreate', array(
          'label' => 'Group Album\'s Create Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group album\'s create group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumcreate', 2),
      ));

      $this->addElement('Text', 'sitegroup_adalbumeditphoto', array(
          'label' => 'Group Album\'s Edit Photos Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group album\'s edit photos group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumeditphoto', 3),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitegroup_addicussionwidget', array(
          'label' => 'Group Profile Discussions Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in group profile discussions widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.addicussionwidget', 3),
      ));

      $this->addElement('Text', 'sitegroup_addiscussionview', array(
          'label' => 'Group Discussion\'s View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group dicussion\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.addiscussionview', 2),
      ));

      $this->addElement('Text', 'sitegroup_addiscussioncreate', array(
          'label' => 'Group Discussion\'s Create Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group dicussion\'s create group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.addiscussioncreate', 2),
      ));

      $this->addElement('Text', 'sitegroup_addiscussionreply', array(
          'label' => 'Group Discussion\'s Post Reply Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a dicussion\'s post reply group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.addiscussionreply', 2),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitegroup_addocumentwidget', array(
          'label' => 'Group Profile Documents Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in group profile documents widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.addocumentwidget', 3),
      ));

      $this->addElement('Text', 'sitegroup_addocumentview', array(
          'label' => 'Group Document\'s View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a Group document\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.addocumentview', 2),
      ));

      $this->addElement('Text', 'sitegroup_addocumentbrowse', array(
          'label' => 'Group Document\'s Browse Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a Group document\'s browse group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.addocumentbrowse', 2),
      ));

      $this->addElement('Text', 'sitegroup_addocumentcreate', array(
          'label' => 'Group Document\'s Create Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a Group document\'s create group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.addocumentcreate', 4),
      ));

      $this->addElement('Text', 'sitegroup_addocumentedit', array(
          'label' => 'Group Document\'s Edit Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a Group document\'s edit group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.addocumentedit', 4),
      ));

      $this->addElement('Text', 'sitegroup_addocumentdelete', array(
          'label' => 'Group Document\'s Delete Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a Group document\'s delete group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.addocumentdelete', 1),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitegroup_advideowidget', array(
          'label' => 'Group Profile Videos Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in group profile videos widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.advideowidget', 3),
      ));

      $this->addElement('Text', 'sitegroup_advideoview', array(
          'label' => 'Group Video\'s View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group video\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.advideoview', 2),
      ));

      $this->addElement('Text', 'sitegroup_advideobrowse', array(
          'label' => 'Group Video\'s Browse Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group video\'s browse group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.advideobrowse', 2),
      ));

      $this->addElement('Text', 'sitegroup_advideocreate', array(
          'label' => 'Group Video\'s Create Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group video\'s create group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.advideocreate', 2),
      ));

      $this->addElement('Text', 'sitegroup_advideoedit', array(
          'label' => 'Group Video\'s Edit Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group video\'s edit group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.advideoedit', 2),
      ));

      $this->addElement('Text', 'sitegroup_advideodelete', array(
          'label' => 'Group Video\'s Delete Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group video\'s delete group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.advideodelete', 1),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitegroup_adpollwidget', array(
          'label' => 'Group Profile Polls Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in group profile polls widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adpollwidget', 3),
      ));

      $this->addElement('Text', 'sitegroup_adpollview', array(
          'label' => 'Group Poll\'s View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group poll\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adpollview', 2),
      ));

      $this->addElement('Text', 'sitegroup_adpollbrowse', array(
          'label' => 'Group Poll\'s Browse Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group poll\'s browse group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adpollbrowse', 2),
      ));

      $this->addElement('Text', 'sitegroup_adpollcreate', array(
          'label' => 'Group Poll\'s Create Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group poll\'s create group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adpollcreate', 2),
      ));

      $this->addElement('Text', 'sitegroup_adpolldelete', array(
          'label' => 'Group Poll\'s Delete Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group poll\'s delete group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adpolldelete', 1),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitegroup_admemberwidget', array(
          'label' => 'Group Profile Members Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in group profile members widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.admemberwidget', 3),
      ));
      $this->addElement('Text', 'sitegroup_admemberbrowse', array(
          'label' => 'Group Members Browse Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group member\'s browse group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.admemberbrowse', 3),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitegroup_adreviewwidget', array(
          'label' => 'Group Profile Reviews Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in group profile reviews widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adreviewwidget', 3),
      ));

      $this->addElement('Text', 'sitegroup_adreviewcreate', array(
          'label' => 'Group Review\'s Create Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group review\'s create group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adreviewcreate', 2),
      ));

      $this->addElement('Text', 'sitegroup_adreviewedit', array(
          'label' => 'Group Review\'s Edit Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group review\'s edit group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adreviewedit', 2),
      ));

      $this->addElement('Text', 'sitegroup_adreviewdelete', array(
          'label' => 'Group Review\'s Delete Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group review\'s delete group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adreviewdelete', 1),
      ));

      $this->addElement('Text', 'sitegroup_adreviewview', array(
          'label' => 'Group Reviews Browse Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group review\'s browse group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adreviewview', 2),
      ));

      $this->addElement('Text', 'sitegroup_adreviewbrowse', array(
          'label' => 'Group Reviews View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group review\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adreviewbrowse', 2),
      ));  

    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitegroup_adofferwidget', array(
          'label' => 'Group Profile Offers Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in group profile offers widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adofferwidget', 3),
      ));

      $this->addElement('Text', 'sitegroup_adoffergroup', array(
          'label' => 'Group Offers Browse Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on group offers browse group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adoffergroup', 2),
      ));

      $this->addElement('Text', 'sitegroup_adofferlist', array(
          'label' => 'Group Offers List Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on group offers list group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adofferlist', 2),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitegroup_adformwidget', array(
          'label' => 'Group Profile Form Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in group profile form widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adformwidget', 3),
      ));

      $this->addElement('Text', 'sitegroup_adformcreate', array(
          'label' => 'Form Add Question Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on form\'s add question group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adformcreate', 3),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupinvite') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitegroup_adinvite', array(
          'label' => 'Invite Friends Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on invite friends group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adinvite', 1),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitegroup_adbadgeview', array(
          'label' => 'Badges View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a badge\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adbadgeview', 2),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitegroup_adlocationwidget', array(
          'label' => 'Group Profile Map Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in group profile map widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adlocationwidget', 3),
      ));

      $this->addElement('Text', 'sitegroup_adoverviewwidget', array(
          'label' => 'Group Profile Overview Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in group profile overview widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adoverviewwidget', 0),
      ));

      $this->addElement('Text', 'sitegroup_adinfowidget', array(
          'label' => 'Group Profile Info Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in group profile info widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adinfowidget', 3),
      ));

      $this->addElement('Text', 'sitegroup_adclaimview', array(
          'label' => 'Claim Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on claim group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adclaimview', 2),
      ));

      $this->addElement('Text', 'sitegroup_adtagview', array(
          'label' => 'Browse Tags Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on browse tags group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adtagview', 1),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitegroup_admusicwidget', array(
          'label' => 'Group Profile Music Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in group profile music widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.admusicwidget', 3),
      ));

      $this->addElement('Text', 'sitegroup_admusicview', array(
          'label' => 'Group Music View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group music view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.admusicview', 3),
      ));

      $this->addElement('Text', 'sitegroup_admusicbrowse', array(
          'label' => 'Group Music Browse Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group music browse group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.admusicbrowse', 3),
      ));

      $this->addElement('Text', 'sitegroup_admusiccreate', array(
          'label' => 'Group Music Create Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group music create group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.admusiccreate', 3),
      ));

      $this->addElement('Text', 'sitegroup_admusicedit', array(
          'label' => 'Group Music Edit Group',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a group music edit group?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.admusicedit', 3),
      ));
    }

	  //START FOR INRAGRATION WORK WITH OTHER PLUGIN.
    $sitegroupintegrationModEnabled = Engine_Api::_()->getDbtable('modules',
     'core')->isModuleEnabled('sitegroupintegration');
    if(!empty($sitegroupintegrationModEnabled)&& Engine_Api::_()->getDbtable('modules',
      'core')->isModuleEnabled('communityad')) {
			$mixSettingsResults = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitegroupintegration'
	    )->getIntegrationItems();
			foreach($mixSettingsResults as $modNameValue) {

// 				if(strstr($modNameValue['resource_type'], 'sitereview_listing')) {
// 					$item_title = 'Products';
// 				} else {
// 					$item_title = $modNameValue["item_title"];
// 				}
        $item_title = $modNameValue["item_title"];
				$this->addElement('Text', "sitegroup_ad_" . $modNameValue['resource_type']. '_' .$modNameValue['listingtype_id'], array(
					'label' => "Group Profile " . $item_title . " Widget",
					'maxlenght' => 3,
					'description' => "How many ads will be shown in group profile " .  $item_title . "   widget?",
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting("sitegroup.ad." . $modNameValue['resource_type'].".".$modNameValue['listingtype_id'] , 3),
				));
			}
	  }
    //END FOR INRAGRATION WORK WITH OTHER PLUGIN.
	  
	  //START AD WORK FOR TWITTER
    $sitegrouptwitterModEnabled = Engine_Api::_()->getDbtable('modules',
     'core')->isModuleEnabled('sitegrouptwitter');
		if(!empty($sitegrouptwitterModEnabled)&& Engine_Api::_()->getDbtable('modules',
				'core')->isModuleEnabled('communityad')) {
			$this->addElement('Text', 'sitegroup_adtwitterwidget', array(
					'label' => 'Group Profile Twitter Widget',
					'maxlenght' => 3,
					'description' => 'How many ads will be shown in group profile twitter widget?',
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adtwitterwidget', 3),
			));
		}
		//END AD WORK FOR TWITTER

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
      ));
    }
  }

}

?>