<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Widget_ArchivesSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//GET ARCHIVE SITEGROUP OF SITEGROUP USER
    $archiveSitegroup =  Engine_Api::_()->getDbTable('groups', 'sitegroup')->getArchiveSitegroup(null);

		//CALL TO handleArchiveSitegroup ACTION OF SAME CONTROLLER
    $this->view->archive_sitegroup = $this->_handleArchiveSitegroup($archiveSitegroup);

    if (!(count($this->view->archive_sitegroup) > 0)) {
      return $this->setNoRender();
    }

    if (isset($_GET['start_date'])) {
      $this->view->start_date = $_GET['start_date'];
		}
  }

  //ACTION FOR handleArchiveSitegroup
  protected function _handleArchiveSitegroup($results) {

    $localeObject = Zend_Registry::get('Locale');
    $sitegroup_dates = array();
    foreach ($results as $result)
      $sitegroup_dates[] = strtotime($result->creation_date);

    //GET ARCHIVE SITEGROUP
    $time = time();
    $archive_sitegroup = array();

    foreach ($sitegroup_dates as $sitegroup_date) {
      $ltime = localtime($sitegroup_date, TRUE);
      $ltime["tm_mon"] = $ltime["tm_mon"] + 1;
      $ltime["tm_year"] = $ltime["tm_year"] + 1900;

      //LESS THAN A YEAR AGO - MONTHS
      if ($sitegroup_date + 31536000 > $time) {
        $date_start = mktime(0, 0, 0, $ltime["tm_mon"], 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, $ltime["tm_mon"] + 1, 1, $ltime["tm_year"]);
        $label = date('F Y', $sitegroup_date);
        $type = 'month';
      }

      //MORE THAN A YEAR AGO - YEARS
      else {
        $date_start = mktime(0, 0, 0, 1, 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, 1, 1, $ltime["tm_year"] + 1);
        $type = 'year';

        $dateObject = new Zend_Date($sitegroup_date);
        $format = $localeObject->getTranslation('yyyy', 'dateitem', $localeObject);
        if (!$format) {
          $format = $localeObject->getTranslation('y', 'dateitem', $localeObject);
        }
        $label = $dateObject->toString($format, $localeObject);
      }

      if (!isset($archive_sitegroup[$date_start])) {
        $archive_sitegroup[$date_start] = array(
            'type' => $type,
            'label' => $label,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'count' => 1
        );
      } else {
        $archive_sitegroup[$date_start]['count']++;
      }
    }
    return $archive_sitegroup;
  }
}

?>