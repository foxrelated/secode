<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: WidgetController.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitegroup_MobileWidgetController extends Core_Controller_Action_Standard {

  public function indexAction() {

		$content_id = $this->_getParam('content_id');
		$view = $this->_getParam('view');
		$show_container = $this->_getParam('container', true);
		$params = $this->_getAllParams();

    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0)) {

			// Render by content row
			if (null !== $content_id) {

				$contentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitegroup');
				$row = $contentTable->find($content_id)->current();

				if (null !== $row) {
					// Build full structure from children
					$mobilecontentgroup_id = $row->mobilecontentgroup_id;
					$groupTable = Engine_Api::_()->getDbtable('mobileContentgroups', 'sitegroup');
					$content = $contentTable->fetchAll($contentTable->select()->where('mobilecontentgroup_id = ?', $mobilecontentgroup_id));
					$structure = $groupTable->createElementParams($row);
					$children = $groupTable->prepareContentArea($content, $row);
					if (!empty($children)) {
						$structure['elements'] = $children;
					}
					$structure['request'] = $this->getRequest();
					$structure['action'] = $view;

					// Create element (with structure)
					$element = new Engine_Content_Element_Container(array(
											'elements' => array($structure),
											'decorators' => array(
													'Children'
											)
									));

					// Strip decorators
					if (!$show_container) {
						foreach ($element->getElements() as $cel) {
							$cel->clearDecorators();
						}
					}

					$content = $element->render();
					$this->getResponse()->setBody($content);
				} else {
					$contentTable = Engine_Api::_()->getDbtable('mobileadmincontent', 'sitegroup');

					$row = $contentTable->find($content_id)->current();
					$group_id = Engine_Api::_()->sitegroup()->getMobileWidgetizedGroup()->page_id;
					$content = $contentTable->fetchAll($contentTable->select()->where('group_id = ?', $group_id));
					$structure = $contentTable->createElementParams($row);
					$children = $contentTable->prepareContentArea($content, $row);
					if (!empty($children)) {
						$structure['elements'] = $children;
					}
					$structure['request'] = $this->getRequest();
					$structure['action'] = $view;

					// Create element (with structure)
					$element = new Engine_Content_Element_Container(array(
											'elements' => array($structure),
											'decorators' => array(
													'Children'
											)
									));

					// Strip decorators
					if (!$show_container) {
						foreach ($element->getElements() as $cel) {
							$cel->clearDecorators();
						}
					}

					$content = $element->render();
					$this->getResponse()->setBody($content);
        }

				$this->_helper->viewRenderer->setNoRender(true);
				return;
			}

			// Render by widget name
			$mod = $this->_getParam('mod');
			$name = $this->_getParam('name');
			if (null !== $name) {
				if (null !== $mod) {
					$name = $mod . '.' . $name;
				}
				$structure = array(
						'type' => 'widget',
						'name' => $name,
						'request' => $this->getRequest(),
						'action' => $view,
				);

				// Create element (with structure)
				$element = new Engine_Content_Element_Container(array(
										'elements' => array($structure),
										'decorators' => array(
												'Children'
										)
								));

				$content = $element->render();
				$this->getResponse()->setBody($content);

				$this->_helper->viewRenderer->setNoRender(true);
				return;
			}

			$this->getResponse()->setBody('Aw, shucks.');
			$this->_helper->viewRenderer->setNoRender(true);
			return;      

    } else {
			// Render by content row
			if (null !== $content_id) {

				if (Engine_API::_()->sitemobile()->checkMode('mobile-mode')) {
					$contentTable = Engine_Api::_()->getDbtable('content', 'sitemobile');
				} elseif (Engine_API::_()->sitemobile()->checkMode('tablet-mode')) {
					$contentTable = Engine_Api::_()->getDbtable('tabletcontent', 'sitemobile');
				}
				$row = $contentTable->find($content_id)->current();
				if (null !== $row) {
					// Build full structure from children
					$group_id = $row->page_id;
					if (Engine_API::_()->sitemobile()->checkMode('mobile-mode')) {
						$groupTable = Engine_Api::_()->getDbtable('pages', 'sitemobile');
					} elseif (Engine_API::_()->sitemobile()->checkMode('tablet-mode')) {
						$groupTable = Engine_Api::_()->getDbtable('tabletpages', 'sitemobile');
					}
					$content = $contentTable->fetchAll($contentTable->select()->where('page_id = ?', $group_id));
					$structure = $groupTable->createElementParams($row);
					$children = $groupTable->prepareContentArea($content, $row);
					if (!empty($children)) {
						$structure['elements'] = $children;
					}
					$structure['request'] = $this->getRequest();
					$structure['action'] = $view;

					// Create element (with structure)
					$element = new Engine_Content_Element_Container(array(
											'elements' => array($structure),
											'decorators' => array(
													'Children'
											)
									));

					// Strip decorators
					if (!$show_container) {
						foreach ($element->getElements() as $cel) {
							$cel->clearDecorators();
						}
					}

					$content = $element->render();
					$this->getResponse()->setBody($content);
				}

				$this->_helper->viewRenderer->setNoRender(true);
				return;
			}

			// Render by widget name
			$mod = $this->_getParam('mod');
			$name = $this->_getParam('name');
			if (null !== $name) {
				if (null !== $mod) {
					$name = $mod . '.' . $name;
				}
				$structure = array(
						'type' => 'widget',
						'name' => $name,
						'request' => $this->getRequest(),
						'action' => $view,
				);

				// Create element (with structure)
				$element = new Engine_Content_Element_Container(array(
										'elements' => array($structure),
										'decorators' => array(
												'Children'
										)
								));

				$content = $element->render();
				$this->getResponse()->setBody($content);

				$this->_helper->viewRenderer->setNoRender(true);
				return;
			}

			$this->getResponse()->setBody('Aw, shucks.');
			$this->_helper->viewRenderer->setNoRender(true);
			return;
    }
  }

}