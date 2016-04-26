<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminMixController.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_AdminMixController extends Core_Controller_Action_Admin {

  public function indexAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sugg_admin_main', array(), 'suggestion_admin_main_mix');

    $this->view->form = $form = new Suggestion_Form_Admin_Mix();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
			if( array_key_exists('friend', $values) ){
				$values['user'] = $values['friend'];
			}
      $sugggestion_admin_tab = 'admin_mix_settings';
      if (!empty($values)) {
        foreach ($values as $key => $value) {
          if (($key != 'sugg_mix_wid') && ($key != 'recomended_ajax_enabled')) {
            if (empty($value)) {
              $value = 0;
            }
            $MixinfoTable = Engine_Api::_()->getDbtable('modinfos', 'suggestion');
            $mixinfoName = $MixinfoTable->info('name');
            $mixinfoSelect = $MixinfoTable->select()
                            ->from($mixinfoName, array('recommendation'))
                            ->where('module = ?', $key);
            $mixinfoSelectRes = $mixinfoSelect->query()->fetchAll();
            if (empty($mixinfoSelectRes)) {
              $mixInfo = $MixinfoTable->createRow();
              $mixInfo->module = $key;
              $mixInfo->recommendation = $value;
              $mixInfo->save();
            } else {
              $MixinfoTable->update(array("recommendation" => $value), array("module =?" => $key));
            }
          } else {
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
          }
        }
      }
    }
  }

}
?>