<?php
/**
 * SocialEngine
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Widget_ProductRecentController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {

        $table = Engine_Api::_()->getDbtable('products', 'store');
        $products = $table->getRecent(31);
		
        $this->view->products = $products;

        // Do not render if nothing to show
        if ( count($products) <= 0 ) {
            return $this->setNoRender();
        }
    }
}