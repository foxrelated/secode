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

        if ($this->_request->isPost() && $form->isValid($_POST)) {

        }

        if ($this->_request->isGet()) {

        }
    }

    /**
     * @return array
     */
    public function getThemeVariables()
    {
        return array(
            'light'                 => 'light_color',
            'stable'                => 'stable_color',
            'positive'              => 'positive_color',
            'calm'                  => 'calm_color',
            'balanced'              => 'balanced_color',
            'energized'             => 'energized_color',
            'assertive'             => 'assertive_color',
            'royal'                 => 'royal_color',
            'dark'                  => 'dark_color',
            'base-background-color' => 'base_background_color',
            'base-color'            => 'base_color',
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
                    'build_number'   => time(),
                    'variables_text' => json_encode($variables),
                    'creation_date'  => date('Y-M-D H:i:s'),
                    'modified_date'  => date('Y-M-D H:i:s'),
                ));

                $themeEntry->save();

            } catch (Exception $ex) {
                exit(var_dump($ex->getMessage()));
            }


        }

        if ($this->_request->isGet()) {

        }
    }
}