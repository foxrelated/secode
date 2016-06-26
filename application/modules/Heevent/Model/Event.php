<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Event.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 25.09.13
 * Time: 11:17
 * To change this template use File | Settings | File Templates.
 */
class Heevent_Model_Event extends Event_Model_Event
{
    protected $_paramsTable = null;
    protected $_tempTable = null;
    protected $_ticketsTable = null;
    protected $_subTable = null;
    protected $_CardTable = null;
    protected $_setEventOrder = null;
    protected $_params = null;
    protected $_locationParams = null;
    protected $_coverParams = null;
    protected $_type = 'event';
    protected $_shortType = 'event';

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->_paramsTable = Engine_Api::_()->getDbTable('params', 'heevent');
        $this->_ticketsTable = Engine_Api::_()->getDbTable('tickets', 'heevent');
        $this->_subTable = Engine_Api::_()->getDbTable('subscriptions', 'heevent');
        $this->_setEventOrder = Engine_Api::_()->getDbTable('subscriptions', 'heevent');
        $this->_CardTable = Engine_Api::_()->getDbTable('cards', 'heevent');
    }

    public function setPhoto($photo)
    {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
//      $fileName = $file;
        } else if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
//      $fileName = $photo->name;
        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
            $file = $tmpRow->temporary();
//      $fileName = $tmpRow->name;
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
//      $fileName = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
//      $fileName = $photo;
        } else {
            throw new User_Model_Exception('invalid argument passed to setPhoto');
        }

        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_id' => $this->getIdentity(),
            'parent_type' => 'event'
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(1092, 301)
            ->write($path . '/m_' . $name)
            ->destroy();

        // Resize image (2xtile)
        $image = Engine_Image::factory();
        $image->open($file);
        $defRatio = 4 / 3;
        $imgRatio = $image->width / $image->height;
        $size = array('w' => $image->width, 'h' => $image->height);
        $x = 0;
        $y = 0;
        if ($defRatio < $imgRatio) {
            $size['w'] = $image->height * $defRatio;
            $x = ($image->width - $size['w']) / 2;
        } else {
            $size['h'] = $image->width / $defRatio;
            $y = ($image->height - $size['h']) / 2;
        }
        $image->resample($x, $y, $size['w'], $size['h'], 480, 360)
            ->write($path . '/p_' . $name)
            ->destroy();

        // Resize image (tile)
        $image = Engine_Image::factory();
        $image->open($file);

        $image->resample($x, $y, $size['w'], $size['h'], 240, 180)
            ->write($path . '/in_' . $name)
            ->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);
        $image->width / $image->height;
        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
            ->write($path . '/is_' . $name)
            ->destroy();

        // Heevent
        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/p_' . $name, $params);
        $iIconNormal = $storage->create($path . '/in_' . $name, $params);
        $iSquare = $storage->create($path . '/is_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
//    $iMain->bridge($iProfile, 'thumb.2xtile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
//    $iMain->bridge($iIconNormal, 'thumb.tile');
        $iMain->bridge($iSquare, 'thumb.icon');

        // Remove temp files
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        @unlink($path . '/in_' . $name);
        @unlink($path . '/is_' . $name);

        // Update row
        $this->modified_date = date('Y-m-d H:i:s');
        $this->photo_id = $iMain->file_id;
        $this->save();

        // Add to album
        $viewer = Engine_Api::_()->user()->getViewer();
        $photoTable = Engine_Api::_()->getItemTable('event_photo');
        $eventAlbum = $this->getSingletonAlbum();
        $photoItem = $photoTable->createRow();
        $photoItem->setFromArray(array(
            'event_id' => $this->getIdentity(),
            'album_id' => $eventAlbum->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'file_id' => $iMain->getIdentity(),
            'collection_id' => $eventAlbum->getIdentity(),
            'user_id' => $viewer->getIdentity(),
        ));
        $photoItem->save();

        return $this;
    }

    public function getParams()
    {
        if (!$this->_params)
            $this->_params = $this->_paramsTable->getEventParams($this->getIdentity());
        return $this->_params;
    }

    public function setParams(array $params)
    {
        $this->_paramsTable->setEventParams($this->getIdentity(), $params);
        return $this;
    }
    public function setTemp(array $params)
    {
        $this->_tempTable->setEventTemp( $params);
        return $this;
    }

    public function setTickets(array $ticket)
    {
        $this->_ticketsTable->setEventTickets($this->getIdentity(), $ticket);
        return $this;
    }


    public function getTickets($event_id)
    {
        $this->_ticketsTable->getEventTickets($event_id);
        return $this;
    }
    public function getTemp($event_id,$user_id)
    {
        $this->_tempTable->getEventTemp($event_id,$user_id);
        return $this;
    }

    public function getLocationParams()
    {
        if (!$this->_locationParams) {
            $this->_locationParams = Zend_Json::decode($this->getParams()->location_params);
        }
        return $this->_locationParams;
    }

    public function getCoverParams()
    {
        if (!$this->_coverParams) {
            $this->_coverParams = Zend_Json::decode($this->getParams()->cover_params);
        }
        if ($this->_coverParams === null)
            $this->_coverParams = array();

        return $this->_coverParams;
    }

    public function getMapZoom()
    {
        $params = $this->getLocationParams();
        return @$params['map_zoom'];
    }

    public function getCoverBgStyle()
    {
        $params = $this->getCoverParams();
        $style = '';
        $pref = 'background-';
        foreach ($params as $key => $param) {
            $style .= ($pref . $key . ': ' . $param . "; ");
        }
        return $style;
    }

    public function getRichContent()
    {
        $view = new Zend_View();
        $scriptPath = APPLICATION_PATH
            . DIRECTORY_SEPARATOR
            . "application"
            . DIRECTORY_SEPARATOR
            . "modules"
            . DIRECTORY_SEPARATOR
            . 'Heevent'
            . DIRECTORY_SEPARATOR
            . 'views'
            . DIRECTORY_SEPARATOR
            . 'scripts';
        $EngineHelperPath = 'Engine/View/Helper/';


        $eventPaymantCheck = $this->_ticketsTable->getEventTicketCount($this);
        $Card_ticket = $this->_CardTable->getEventCardsCount($this->getIdentity())->count;
        $Card_of = $eventPaymantCheck->ticket_count;
        $eventPrice = $this->_ticketsTable->getEventTickets($this)->ticket_price;

        $of = false;
        if ($Card_of && is_numeric($Card_of)) {
            $of = true;

            if ($Card_of == -1) {
                $restrictions = false;
            } else {
                $restrictions = $Card_of;
            }

            if ($eventPrice == -1) {
                $free = false;
            } else {
                $free = $eventPrice;
            }
        }
        $checkTicketMax = $this->_CardTable->getUserCountBuy($this->getIdentity())->count;
        $view->setScriptPath($scriptPath);
        $view->addHelperPath($EngineHelperPath, implode('_', explode('/', $EngineHelperPath)));
        $view->assign('event', $this);
        $view->assign('of', $of);
        if ($of) {
            $view->assign('restrictions', $restrictions);
            $view->assign('free', $free);
            $view->assign('maxTicket', $checkTicketMax);
            $view->assign('eventPrice', $eventPrice);
            $view->assign('card_ticket', $Card_ticket);

        }

        $view->assign('format', Zend_Controller_Front::getInstance()->getRequest()->getParam('format'));
        return $view->render('_richContent.tpl');
    }

    public function isPast()
    {
        return strtotime($this->endtime) < time();
    }
  public function getEventDescription()
  {
    $translate = Zend_Registry::get('Zend_View');
    $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
    $html = '';
    $html .= $translate->translate('Buy Event');
    return $html;
  }
  public function getPrice()
  {
    return $this->_ticketsTable->getEventTickets($this)->ticket_price;
  }
  public function getCouponsCount()
  {
    $Card_of =$this->_ticketsTable->getEventTicketCount($this)->ticket_count;
    $Card_ticket = $this->_subTable->getEventSubCount($this)->subscription_id;
    if($Card_of-$Card_ticket<=0){
      return false;
    }else{
      return true;
    }
  }
  public function getEventStatusPayment()
  {
    $eventPaymantCheck = $this->_ticketsTable->getEventTicketCount($this);
    $Card_of = $eventPaymantCheck->ticket_count;
    if ($Card_of && is_numeric($Card_of)) {
      return true;
    }
    return false;
  }
  public function getCardCode($id){
    $length = 10;
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789bcdefghijklmnopqrstuvwxyz0123456789';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
      $index = rand(0, $count - 1);
      $result .= mb_substr($chars, $index, 1);
    }

    return $id.'-'.$result;
  }
  public function getCurentPrice($price){
    $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
     return  '<span class="heevent-price"><span id="price_tag">' . $price .'</span> '.$currency. '</span>';
  }
    public function getCurentPriceList($price){
        $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
        return   $price .'  '.$currency;
    }
}
