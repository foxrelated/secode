<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _addToDiary.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php

$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css');
?>

<?php $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity(); ?>
<?php if ($viewer_id): ?>
    <?php echo $this->htmlLink(array('route' => "siteevent_diary_general", 'action' => 'add', 'event_id' => $this->item->event_id), $this->translate($this->text), array('class' => "smoothbox $this->classIcon $this->classLink", 'title' => $this->translate('Add to Diary'))) ?>
<?php else: ?>
    <?php

    $urlO = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
    $request_url = explode('/', $urlO);
    empty($request_url['2']) ? $param = 2 : $param = 1;
    $return_url = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://";
    $currentUrl = urlencode($urlO);
    ?> 
    <?php

    $addUrl = $this->url(array('action' => 'add', 'event_id' => $this->item->event_id, 'param' => $param, 'request_url' => $request_url['1']), "siteevent_diary_general") . "?" . "return_url=" . $return_url . $_SERVER['HTTP_HOST'] . $currentUrl;
    echo $this->htmlLink($addUrl, $this->translate($this->text), array('class' => "$this->classIcon $this->classLink", 'title' => $this->translate('Add to Diary')));
    ?>
<?php endif; ?>
