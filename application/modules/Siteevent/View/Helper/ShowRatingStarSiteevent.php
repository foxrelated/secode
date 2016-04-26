<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ShowRatingStar.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_View_Helper_ShowRatingStarSiteevent extends Zend_View_Helper_Abstract {

    /**
     * Assembles action string
     * 
     * @return string
     */
    public function showRatingStarSiteevent($rating, $type = 'user', $sizeType = 'small-star', $html_title = null, $showNonZero = false, $userRatings = true) {

        if (!$showNonZero) {

            if ($rating <= 0)
                return;
        }

        $ratingSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2);
        if ((empty($ratingSetting) || ($ratingSetting == 2 && $type == 'editor') || $ratingSetting == 1 && $type == 'user') && $userRatings) {
            return;
        }

        $title_type = $type;
        if ($type == 'overall') {
            $type = 'user';
        }
        $rating_half_star_class = '';
        $rating_star_class = '';

        $rating = sprintf("%.1f", $rating);

        if ($sizeType == 'small-star') {
            if ($type == 'editor') {
                $rating_star_class = "rating_star_r";
                $rating_half_star_class = "rating_star_half_r";
            } else if ($type == 'user' || $type == 'visitor') {
                $rating_star_class = "rating_star_y";
                $rating_half_star_class = "rating_star_half_y";
            } else if ($type == 'overall') {
                $rating_star_class = "rating_star_b";
                $rating_half_star_class = "rating_star_half_b";
            }
        } else {
            switch ($rating) {
                case 0:
                    $rating_star_class = '';
                    break;
                case $rating <= .5:
                    $rating_star_class = 'halfstar';
                    break;
                case $rating <= 1:
                    $rating_star_class = 'onestar';
                    break;
                case $rating <= 1.5:
                    $rating_star_class = 'onehalfstar';
                    break;
                case $rating <= 2:
                    $rating_star_class = 'twostar';
                    break;
                case $rating <= 2.5:
                    $rating_star_class = 'twohalfstar';
                    break;
                case $rating <= 3:
                    $rating_star_class = 'threestar';
                    break;
                case $rating <= 3.5:
                    $rating_star_class = 'threehalfstar';
                    break;
                case $rating <= 4:
                    $rating_star_class = 'fourstar';
                    break;
                case $rating <= 4.5:
                    $rating_star_class = 'fourhalfstar';
                    break;
                case $rating <= 5:
                    $rating_star_class = 'fivestar';
                    break;
            }

            if ($sizeType == 'small-box') {
                $rating_star_class = 'sr-rating-box-small ' . ((!empty($rating_star_class)) ? $rating_star_class . '-small-box' : '');
                if ($type == 'editor') {
                    $rating_star_class = "sr-es-box-small " . $rating_star_class;
                } else if ($type == 'user' || $type == 'visitor') {
                    $rating_star_class = "sr-us-box-small " . $rating_star_class;
                } else if ($type == 'overall') {
                    $rating_star_class = "sr-as-box-small " . $rating_star_class;
                }
            } else if ($sizeType == 'big-box') {
                $rating_star_class .= 'rating-box ' . ((!empty($rating_star_class)) ? $rating_star_class . '-box' : '');
                if ($type == 'editor') {
                    $rating_star_class = "sr-es-box " . $rating_star_class;
                } else if ($type == 'user' || $type == 'visitor') {
                    $rating_star_class = "sr-us-box " . $rating_star_class;
                } else if ($type == 'overall') {
                    $rating_star_class = "sr-us-box " . $rating_star_class;
                }
            } else if ($sizeType == 'big-star') {
                if ($type == 'editor') {
                    $rating_star_class = "siteevent_es_rating " . $rating_star_class;
                } else if ($type == 'user' || $type == 'visitor') {
                    $rating_star_class = "siteevent_us_rating " . $rating_star_class;
                } else if ($type == 'overall') {
                    $rating_star_class = "siteevent_as_rating " . $rating_star_class;
                }
            }
        }

        $data = array('title_type' => $title_type, 'html_title' => $html_title, 'rating' => round($rating, 2), 'sizeType' => $sizeType, 'rating_star_class' => $rating_star_class, 'rating_half_star_class' => $rating_half_star_class);
        return $this->view->partial('_showRatingStar.tpl', 'siteevent', $data);
    }

}