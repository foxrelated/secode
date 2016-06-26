<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ItemCreate.php 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemenu_Form_Admin_Menu_ItemCreate extends Engine_Form {

    protected $_menuItem;
    protected $_childCount;
    protected $_info_array;
    protected $_moduleArray;
    protected $_navigationObj;
    protected $_navigationArray;
    protected $_isCustom;

    public function setMenuItem($menuItem) {
        $this->_menuItem = $menuItem;
        return $this;
    }

    public function setChildCount($childCount) {
        $this->_childCount = $childCount;
        return $this;
    }

    public function setInfo_array($info_array) {
        $this->_info_array = $info_array;
        return $this;
    }

    public function setModuleArray($moduleArray) {
        $this->_moduleArray = $moduleArray;
        return $this;
    }

    public function setNavigationObj($navigationObj) {
        $this->_navigationObj = $navigationObj;
        return $this;
    }

    public function setNavigationArray($navigationArray) {
        $this->_navigationArray = $navigationArray;
        return $this;
    }

    public function setIsCustom($isCustom) {
        $this->_isCustom = $isCustom;
        return $this;
    }

    public function init() {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $this
                ->setTitle('Create Menu Item')
                ->setAttrib('class', 'global_form_popup')
        ;

        // FOR ROOT MENU SELECTBOX
        $parentMenus = array();
        $getMenuObject = Engine_Api::_()->sitemenu()->getMenuObject(array('menu' => 'core_main', 'enabled' => '1'));
        if (!empty($getMenuObject)) {
            foreach ($getMenuObject as $menuObj) {
                $menuObjParams = $menuObj->params;
                if ($menuObj->id != $this->_menuItem && empty($menuObjParams['root_id'])) {
                    $parentMenus[$menuObj->id] = $menuObj->label;
                }
            }
        }

        //PLACE CONDITION FOR MODULE ID ON CREATION OF MENU ITEM AS CUSTOM OR NOT.
        $data = array();
        if (!empty($this->_navigationObj)) {
            foreach ($this->_navigationObj as $item) {
                $data[$item->id] = $item->label;
            }
        }

        $this->addElement('Text', 'label', array(
            'label' => 'Label',
            'required' => true,
            'allowEmpty' => false,
        ));

        $this->addElement('Text', 'uri', array(
            'label' => 'URL',
            'style' => 'width: 300px',
        ));

        $this->addElement('Text', 'icon', array(
            'label' => 'Icon (Note: Not all menus support icons and the recommended size for uploading icon is 16x16 px.)',
            'style' => 'width: 500px',
        ));

        // ELEMENT show_in_tab TO SELECT WHAT TO SHOW IN MAIN MENU TAB
        $this->addElement('Select', 'show_in_tab', array(
            'label' => 'Show in menu tab',
            'multiOptions' => array(
                '0' => 'Only Label',
                '1' => 'Only Icon',
                '2' => 'Both Label & Icon'
            ),
            'value' => '0',
        ));

        // TO SEND THE DEPTH OF THE CURRENTLY BEING EDITED MENU
        if (!empty($this->_info_array)) {
            $tempListFunName = 'isSubMenuItem(' . $this->_info_array . ');';
        } else {
            $tempListFunName = "isSubMenuItem();";
        }
        $this->addElement('Radio', 'is_submenu', array(
            'label' => 'Do you want to use this menu item as a sub menu item?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No',
            ),
            'value' => '0',
            'onchange' => $tempListFunName,
        ));

        // This element is used to manage the parent menu tabs. It is the Id of the parent of parent tab OR in case of no sub tab it is Id of parent tab. 
        $this->addElement('Select', 'root_id', array(
            'label' => 'Choose main menu',
            'multiOptions' => $parentMenus,
            'value' => !empty($parentMenus['root_id']) ? $parentMenus['root_id'] : 0,
            'onchange' => 'getParentSubMenuItems();',
        ));

        // This element is used to manage the sub sub menu tabs else value will be zero. It is the Id of the parent tab in case of sub tab Otherwise it is empty. 
        $this->addElement('Select', 'parent_id', array(
            'label' => 'Choose parent tab',
            'multiOptions' => array(
            ),
            'decorators' => array(
                array('ViewScript', array(
                        'viewScript' => '_addMenuList.tpl',
                        'class' => 'form element',
                        'childCount' => $this->_childCount,
                        'depth' => $this->_info_array,
                        'flag' => $this->_menuItem
                    ),
                )
            ),
        ));
        $this->parent_id->setRegisterInArrayValidator(false);

        // This element contains messages to be shown according to the depth of the menu item.
        $this->addElement('Dummy', 'message', array(
            'decorators' => array(
                array('ViewScript', array(
                        'viewScript' => '_addMessage.tpl',
                        'class' => 'form element',
                        'depth' => $this->_info_array
                    ),
                )
            ),
        ));

        //This element shows the error message when there are no sub menus of a root menu.
        $this->addElement('Dummy', 'noSubMenuMessage', array(
            'decorators' => array(
                array('ViewScript', array(
                        'viewScript' => '_addNoSubMenuMessage.tpl',
                        'class' => 'form element',
                    ),
                )
            ),
        ));


        // SETTING FOR LAYOUT STARTS HERE
        $type_hierarchy = "<b>Standard Hierarchical Navigation Menu </b> (Displays main menu with respective sub-menus and 3rd level menus in hierarchy.)
" . '<a href="' . $view->layout()->staticBaseUrl . 'application/modules/Sitemenu/externals/images/admin/Advmenus_1.jpg" title="View Screenshot" class="buttonlink sitemenu_icon_view mleft5" target="_blank"></a>';

        $type_sub_list = "<b>Multi Column </b> (Displays main menu with sub-menus in multiple columns with their 3rd level menus.)
" . '<a href="' . $view->layout()->staticBaseUrl . 'application/modules/Sitemenu/externals/images/admin/Advmenus_2.jpg" title="View Screenshot" class="buttonlink sitemenu_icon_view mleft5" target="_blank"></a>';

        $type_content = "<b>Main Menu with Content </b>(Displays main menu with respective content based on chosen logic.) " . '<a href="' . $view->layout()->staticBaseUrl . 'application/modules/Sitemenu/externals/images/admin/Advmenus_3.jpg" target="_blank" title="View Screenshot" class="buttonlink sitemenu_icon_view mleft5"></a>';

        $type_single_column = "<b>Mixed Menu </b>(Displays main menu with sub-menus and their respective content on mouseover.) " . '<a href="' . $view->layout()->staticBaseUrl . 'application/modules/Sitemenu/externals/images/admin/Advmenus_4.jpg" target="_blank" title="View Screenshot" class="buttonlink sitemenu_icon_view mleft5"></a>';


        if (Engine_Api::_()->sitemenu()->isCurrentTheme('luminous')) {

            //This element shows the tip message when siteluminous module is enabled.
            $this->addElement('Dummy', 'lumious_enabled_message', array(
                'decorators' => array(
                    array('ViewScript', array(
                            'viewScript' => '_luminousEnabledMessage.tpl',
                            'class' => 'form element',
                        ),
                    )
                ),
            ));
        }

        $this->addElement('radio', 'menu_item_view_type', array(
            'label' => 'Main Menu View Type',
            'description' => "Please select the view type you want for the Main Menu of your community.",
            'multiOptions' => array(
                '1' => $type_hierarchy,
                '2' => $type_sub_list,
                '3' => $type_content,
                '4' => $type_single_column,
            ),
            'escape' => false,
            'value' => '1',
            'onchange' => 'isMenuItemContent();'
        ));
        //SETTING FOR LAYOUT ENDS HERE    
        //IF THERE ARE STANDARD NAVIGATION OF THE CURRENTLY EDITING MENU ITEM
        if (count($data) > 0) {
            $this->addElement('Radio', 'is_sub_navigation', array(
                'label' => 'Do you want to add standard navigation menu?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'value' => !empty($this->_navigationArray) ? 1 : 0,
                'onchange' => 'showMenuCheckbox();'
            ));

            $this->addElement('MultiCheckbox', 'select_sub_navigation', array(
                'label' => 'Choose main navigation menus to be shown as sub menu.',
                'multiOptions' => $data,
                'value' => $this->_navigationArray,
            ));
        }

        // Content Elements Settings Starts
        if (count($this->_moduleArray) > 0):

            // This element shows the module name which are available in the manage module section.
            $this->addElement('Select', 'content', array(
                'label' => 'Select content module',
                'multiOptions' => $this->_moduleArray,
                'value' => '0',
                'onchange' => 'viewByList(0);',
            ));


            // This element is used to manage the category selectbox. 
            $this->addElement('Select', 'category_id', array(
                'label' => 'Category',
                'multiOptions' => array(
                ),
                'decorators' => array(
                    array('ViewScript', array(
                            'viewScript' => '_addCategoryList.tpl',
                            'class' => 'form element',
                        ),
                    )
                ),
            ));
            $this->category_id->setRegisterInArrayValidator(false);

            $this->addElement('MultiCheckbox', 'viewby', array(
                'label' => 'Popularity Criteria',
                'decorators' => array(
                    array('ViewScript', array(
                            'viewScript' => '_addViewByList.tpl',
                            'class' => 'form element',
                        ),
                    )
                ),
            ));
            $this->viewby->setRegisterInArrayValidator(false);

            $this->addElement('Text', 'content_limit', array(
                'label' => 'How many content item do you want to show?',
                'allowEmpty' => false,
                'validators' => array(
                    array('Int', true),
                    new Engine_Validate_AtLeast(1),
                ),
                'value' => '6'
            ));

            // Category Element Setting
            $this->addElement('Radio', 'is_title_inside', array(
                'label' => 'Do you want "Content Title" to be displayed inside the Grid View?',
                'multiOptions' => array(
                    0 => 'Yes',
                    1 => 'No'
                ),
                'value' => '0',
            ));

            // Category Element Setting
            $this->addElement('Radio', 'is_category', array(
                'label' => 'Do you want to show category with this tab?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => '0',
                'onchange' => 'showCategoryLimit();',
            ));

            $this->addElement('Text', 'category_limit', array(
                'label' => 'How many categories do you want to show?',
                'allowEmpty' => false,
                'validators' => array(
                    array('Int', true),
                    new Engine_Validate_AtLeast(1),
                ),
                'value' => '5',
            ));

            $this->addElement('Text', 'content_height', array(
                'label' => 'Content height',
                'description' => 'Enter height for the content to be shown in the main menu. This setting is applied on the box in which the content is shown. Recommended height is 220',
                'value' => '220',
            ));

        endif;
//             End Content Elements Settings

        $this->addElement('Checkbox', 'target', array(
            'label' => 'Open in a new window?',
            'checkedValue' => '_blank',
            'uncheckedValue' => '',
        ));

        // ELEMENT TO SHOW MENU ITEM TO LOGGED OUT USER OR NOT. WORKS ONLY FOR CUSTOM MENUS
        if ($this->_isCustom):
            $this->addElement('Checkbox', 'show_to_guest', array(
                'label' => 'Do you want to show this menu item to logged out user?',
                'checkedValue' => '1',
                'uncheckedValue' => '0',
                'value' => '1',
            ));
        endif;

        $this->addElement('Checkbox', 'enabled', array(
            'label' => 'Enabled?',
            'checkedValue' => '1',
            'uncheckedValue' => '0',
            'value' => '1',
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Create Menu Item',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array('ViewHelper')
        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    }

}
