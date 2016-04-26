<?php

/**
 * @author    Nam Nguyen
 * @copyright YouNet Company
 * @since     4.11
 */

/**
 * Class Ynmobile_AdminThemeController
 */
class Ynmobile_AdminThemesController extends Core_Controller_Action_Admin
{
    /**
     * Setup default navigation
     */
    public function init()
    {
        parent::init();

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('ynmobile_admin_main', array(), 'ynmobile_admin_main_themes');


        $dir = APPLICATION_PATH . '/public/ynmobile/css';

        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, 1)) {
                $this->view->shouldWritable = true;
                $this->view->shouldWritableDirectory = $dir;
            }
        }

        if (!is_writeable($dir)) {
            $this->view->shouldWritable = true;
            $this->view->shouldWritableDirectory = $dir;
        } else {
            $this->view->shouldWritable = false;
        }
    }


    /**
     * List available themes
     */
    public function indexAction()
    {
        $tableThemes = Engine_Api::_()->getDbtable('themes', 'ynmobile');

        $select = $tableThemes
            ->select()
            ->order('modified_date DESC');

        $paging = Zend_Paginator::factory($select);

        $paging->setCurrentPageNumber($this->_request->getParam('page', 1));

        $paging->setItemCountPerPage(10);

        $this->view->paging = $paging;
    }

    /**
     * Edit themes
     *
     * @throws Zend_Form_Exception
     */
    public function editAction()
    {
        $form = new Ynmobile_Form_Admin_Theme();

        $this->view->form = $form;
        $this->view->errorMessage = '';

        $id = (int)$this->_request->getParam('id');
        $table = Engine_Api::_()->getDbtable('themes', 'ynmobile');

        $select = $table
            ->select()
            ->where('theme_id=?', $id);

        $themeEntry = $table->fetchRow($select);

        if (null == $themeEntry) {
            // skip this text
        }

        if ($this->_request->isPost() && $form->isValid($_POST)) {
            $name = $form->getValue('name');
            $variables = $this->getThemeVariableData($_POST);
            $isTest = $_POST['is_test'] ? 1 : 0;

            // generate new theme number to the theme
            $buildNumber = time();
            $previousBuildNumber = $themeEntry->build_number;

            try {
                Engine_Api::_()->ynmobile()->buildCss($variables, $buildNumber);

                $themeEntry->setFromArray(array(
                    'name'           => $name,
                    'is_test'        => $isTest,
                    'variables_text' => json_encode($variables),
                    'build_number'   => $buildNumber
                ));

                $themeEntry->save();

                // set this new build number to be active if the theme is published
                if ($themeEntry->is_publish) {
                    $api = Engine_Api::_()->getApi('settings', 'core');
                    $api->__set('ynmobile.theme_number', $buildNumber);
                }

                // delete previous css file
                Engine_Api::_()->ynmobile()->deleteCss($previousBuildNumber);

                $url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index'), 'admin_default');
                $this->_redirect($url, array('prependBase' => false));

            } catch (Exception $ex) {
                $this->view->errorMessage = $ex->getMessage();
            }
        }

        if ($this->_request->isGet()) {
            $data = (array)json_decode($themeEntry->variables_text, true);

            $data = $this->getFormVariableData($data);
            $data['name'] = $themeEntry->name;


            $form->populate($data);
        }
    }

    /**
     * @return array
     */
    public function getThemeVariables()
    {
        return array(
            'positive'              => 'positive_color',
//            'light'                 => 'light_color',
//            'stable'                => 'stable_color',
//
//            'calm'                  => 'calm_color',
//            'balanced'              => 'balanced_color',
//            'energized'             => 'energized_color',
//            'assertive'             => 'assertive_color',
//            'royal'                 => 'royal_color',
//            'dark'                  => 'dark_color',
//            'base-background-color' => 'base_background_color',
//            'base-color'            => 'base_color',
        );
    }

    /**
     * @param $data
     *
     * @return array
     * @throws new InvalidArgumentExeption
     */
    public function getThemeVariableData($data)
    {

        $response = array();

        foreach ($this->getThemeVariables() as $key => $temp) {
            if (empty($data[ $temp ])) {
                throw new InvalidArgumentException("params [$temp]");
            }
            $response[ $key ] = $data[ $temp ];
        }

        return $response;
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function getFormVariableData($data)
    {
        $response = array();

        foreach ($this->getThemeVariables() as $name => $key) {
            $response[ $key ] = $data[ $name ];
        }

        return $response;

    }

    /**
     * Add new theme
     *
     * @throws Zend_Form_Exception
     */
    public function createAction()
    {

        $form = new Ynmobile_Form_Admin_Theme();

        $this->view->form = $form;
        $this->view->errorMessage = '';


        if ($this->_request->isPost() && $form->isValid($_POST)) {
            $name = $form->getValue('name');
            $variables = $this->getThemeVariableData($_POST);
            $isTest = $_POST['is_test'] ? 1 : 0;

            $themeEntry = Engine_Api::_()->getDbtable('themes', 'ynmobile')->fetchNew();

            // try to generate

            $buildNumber = time();

            try {

                Engine_Api::_()->ynmobile()->buildCss($variables, $buildNumber);

                $themeEntry->setFromArray(array(
                    'name'           => $name,
                    'is_test'        => $isTest,
                    'is_publish'     => 0,
                    'build_number'   => $buildNumber,
                    'variables_text' => json_encode($variables),
                    'creation_date'  => date('Y-M-D H:i:s'),
                    'modified_date'  => date('Y-M-D H:i:s'),
                ));

                $themeEntry->save();

                $url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index'), 'admin_default');
                $this->_redirect($url, array('prependBase' => false));

            } catch (Exception $ex) {
                $this->view->errorMessage = $ex->getMessage();
            }
        }

        if ($this->_request->isGet()) {
        }
    }

    public function deleteAction()
    {
        $this->view->status = 0;
        $id = $this->_request->getParam('id');

        $table = Engine_Api::_()->getDbtable('themes', 'ynmobile');

        $select = $table
            ->select()
            ->where('theme_id=?', (int)$id);

        $theme = $table->fetchRow($select);

        if (!$theme) {
            return;
        }

        if ($theme->is_publish) {
            return;
        }

        // get previous build number to delete file
        $previousBuildNumber = $theme->build_number;

        // Get form
        $this->view->form = $form = new Ynmobile_Form_Admin_ThemeDelete();

        // Check stuff
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $theme->delete();

        // delete previous css file
        Engine_Api::_()->ynmobile()->deleteCss($previousBuildNumber);

        $this->view->form = null;
        $this->view->status = 1;
    }

    public function publishAction()
    {
        $this->view->status = 0;
        $id = $this->_request->getParam('id');

        $table = Engine_Api::_()->getDbtable('themes', 'ynmobile');

        $select = $table
            ->select()
            ->where('theme_id=?', (int)$id);

        $theme = $table->fetchRow($select);

        if (!$theme) {

        }

        // Get form
        $this->view->form = $form = new Ynmobile_Form_Admin_ThemePublish();

        // Check stuff
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $table->update(array('is_publish' => 0), array('theme_id <> ?' => $id));

        $theme->is_publish = 1;
        $theme->save();

        $api = Engine_Api::_()->getApi('settings', 'core');
        $api->__set('ynmobile.theme_number', $theme->build_number);

        $this->view->form = null;
        $this->view->status = 1;
    }
}