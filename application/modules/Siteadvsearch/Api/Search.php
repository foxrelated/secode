<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteadvsearch_Api_Search extends Core_Api_Search {

    protected $_types;

    public function index(Core_Model_Item_Abstract $item) {
        // Check if not search allowed
        if (isset($item->search) && !$item->search) {
            return false;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        // Get info
        $type = $item->getType();
        $id = $item->getIdentity();
        $title = substr(trim($item->getTitle()), 0, 255);
        $description = substr(trim($item->getDescription()), 0, 255);
        $keywords = substr(trim($item->getKeywords()), 0, 255);
        $hiddenText = substr(trim($item->getHiddenSearchData()), 0, 255);

        // Ignore if no title and no description
        if (!$title && !$description) {
            return false;
        }

        // Check if already indexed
        $table = Engine_Api::_()->getDbtable('search', 'core');
        $select = $table->select()
                ->where('type = ?', $type)
                ->where('id = ?', $id)
                ->limit(1);

        $row = $table->fetchRow($select);

        if ($type == 'sitereview_listing')
            $itemType = 'sitereview_listingtype_' . $item->listingtype_id;
        else
            $itemType = $type;

        if (null === $row) {
            $row = $table->createRow();
            $row->type = $type;
            $row->id = $id;
            if(isset($row->item_type))
            $row->item_type = $itemType;
            if (isset($item->location))
                $row->location = $item->location;
        }
        if(isset($row->item_type))
        $row->item_type = $itemType;
        $row->title = $title;
        $row->description = $description;
        $row->keywords = $keywords;
        $row->hidden = $hiddenText;
        if (isset($item->location))
            $row->location = $item->location;
        $row->save();
    }

}
