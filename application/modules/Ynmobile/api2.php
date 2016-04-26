<?php

define('_ENGINE_ERROR_SILENCE', true);
define('_YNMOBILE_API', true);

ini_set('display_errors', '1');
# don't show any errors...
error_reporting(E_ALL | E_STRICT | E_WARNING);
# ...but do log them

ob_start();

// if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
// exit("1");
// }

header('Access-Control-Allow-Headers: token,content-type,en4lang');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Request-Method: GET, POST');
header('content-type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST)) {
        $_POST = (array)json_decode(file_get_contents('php://input'), true);
    };
}

if (isset($_REQUEST['delay']) && $_REQUEST['delay']) {
    sleep($_REQUEST['delay']);
}

function handeShutdown()
{
    if (function_exists('error_get_last')) {
        if (is_array($error = error_get_last()) && $error['type'] == 1) {
            $i = ob_get_level();
            while ($i > 0) {
                ob_clean();
                $i--;
            }
            echo json_encode($error);
            die;
        }
    }
}

register_shutdown_function('handeShutdown');

$application->getBootstrap()->bootstrap('translate');
$application->getBootstrap()->bootstrap('locale');
$application->getBootstrap()->bootstrap('hooks');
$application->getBootstrap()->bootstrap('log');

require_once 'Zend/Json.php';
/**
 * logger.
 */
$logger = new Zend_Log();
$logger->addWriter(new Zend_Log_Writer_Null());


////for production mode, comment this like.
// if(APPLICATION_ENV == 'DEVELOPMENT')
// {
// $logger->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/ynmobile.log'));
// Zend_Registry::set('YNMOBILE_LOG', $logger);
// Zend_Registry::get('YNMOBILE_LOG')->log($_SERVER['REMOTE_ADDR'] .':'. var_export($_REQUEST,1), Zend_Log::DEBUG);
// }


define('TYPE_OF_USER_IMAGE_ICON', 'thumb.icon');
define('TYPE_OF_USER_IMAGE_EVENT', 'thumb.icon');
define('TYPE_OF_USER_IMAGE_NORMAL', 'thumb.normal');
define('TYPE_OF_USER_IMAGE_PROFILE', 'thumb.profile');
$pageURL = 'http';
if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
    $pageURL .= "s";
}
$pageURL .= "://";
$pageURL .= str_replace("/index.php", '', $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
define('NO_USER_ICON', $pageURL . "/application/modules/User/externals/images/nophoto_user_thumb_icon.png");
define('NO_USER_NORMAL', $pageURL . "/application/modules/User/externals/images/nophoto_user_thumb_profile.png");

define('NO_EVENT_ICON', $pageURL . "/application/modules/Event/externals/images/nophoto_event_thumb_icon.png");
define('NO_EVENT_NORMAL', $pageURL . "/application/modules/Event/externals/images/nophoto_event_thumb_normal.png");

define('NO_ALBUM_MAIN', $pageURL . "/application/modules/Ynmobile/externals/images/music_cover_default.png");
define('NO_ALBUM_THUMBNAIL', $pageURL . "/application/modules/Ynmobile/externals/images/nophoto_album_thumb_normal.png");

define('NO_VIDEO_MAIN', $pageURL . "/application/modules/Video/externals/images/video.png");
define('NO_PHOTO_THUMBNAIL', $pageURL . "/application/modules/Ynmobile/externals/images/nophoto_album_thumb_normal.png");

define('NO_GROUP_ICON', $pageURL . "/application/modules/Group/externals/images/nophoto_group_thumb_icon.png");
define('NO_GROUP_NORMAL', $pageURL . "/application/modules/Group/externals/images/nophoto_group_thumb_normal.png");
define('NO_GROUP_PROFILE', $pageURL . "/application/modules/Group/externals/images/nophoto_group_thumb_profile.png");

define('NO_LIST_ICON', $pageURL . "/application/modules/Classified/externals/images/nophoto_classified_thumb_normal.png");
define('NO_LIST_NORMAL', $pageURL . "/application/modules/Classified/externals/images/nophoto_classified_thumb_normal.png");
define('NO_LIST_PROFILE', $pageURL . "/application/modules/Classified/externals/images/nophoto_classified_thumb_profile.png");

/**
 * @var string
 */
define('MSE_TOKEN_KEY', 'token');
define('MSE_TOKEN_KEY_HTTP', 'HTTP_TOKEN');

$sUri = isset($_GET['request']) ? $_GET['request'] : '';

if (substr($sUri, 0, 1) == '/') {
    $sUri = substr($sUri, 1);
}

$requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
$actionType = 1;
$sMethod = 'get';

/**
 * generate data
 */

foreach ($_POST as $key => $value) {
    if (is_string($value)) {
        $_POST[ $key ] = strtr($value, array('>' => '&gt;', '<' => '&lt;'));

        // htmlentities($value, ENT_COMPAT | ENT_HTML401, "UTF-8");
    }
}

$aData = $_GET + $_POST;
$iId = null;

if (preg_match("#^(\w+)\/(\d+)#", $sUri, $matches)) {
    $actionType = 1;
    $sApi = $matches[1];
    $sMethod = strtolower($requestMethod) . 'ByIdAction';
    $iId = $matches[2];
} else
    if (preg_match("#^(\w+)\/(\w+)#", $sUri, $matches)) {
        $actionType = 2;
        $sApi = $matches[1];
        $sMethod = $matches[2];
        $iId = null;
    } else
        if (preg_match("#^(\w+)#", $sUri, $matches)) {
            $actionType = 3;
            $sApi = $matches[1];
            $sMethod = strtolower($requestMethod) . 'Action';
            $iId = null;
        }

$isResful = false;

//check install ynmobile
if (!Engine_Api::_()->hasModuleBootstrap("ynmobile")) {
    echo Zend_Json::encode(array(
        'error_code'    => 1,
        'error_message' => Zend_Registry::get('Zend_Translate')->_("Mobile Application is temporary down!")
    ));
    die;
}

/**
 * condition.
 */
$maps = array(
    // 'advgroup' => 'group',
    // 'ynevent' => 'event',
    // 'ynvideo' => 'video',
    // 'advalbum' => 'photo',
    // 'ynblog' => 'blog',
    'ynforum' => 'forum',
    // 'mp3music' => 'album'
);


foreach ($maps as $from => $to) {
    if (Engine_Api::_()->hasModuleBootstrap($from)) {
        $filename = APPLICATION_PATH . '/application/modules/Ynmobile/3rd/' . Engine_Api::inflect($to) . '.php';
        if (file_exists($filename)) {
            require_once $filename;
        }
    }
}


$api = Engine_Api::_();
$className = "Ynmobile_Api_" . $api->inflect($sApi);
$oApi = null;

if (!class_exists($className)) {
    echo Zend_Json::encode(array(
        'error_code'    => 1,
        'error_message' => Zend_Registry::get('Zend_Translate')->_("This feature is temporary down by site administrators")
    ));
    die;
} else {
    //new api.
    $oApi = new $className();
}

global $token;

function getHeaderValue($key, $defaultValue = null)
{
    define('MSE_TOKEN_KEY', 'token');
    define('MSE_TOKEN_KEY_HTTP', 'HTTP_TOKEN');

    $response = null;
    $httpTemp = strtoupper('http_'. $key);

    if (!empty($_REQUEST[ $key ])) {
        $response = $_REQUEST[ $key ];
    } elseif (isset($_SERVER[ $httpTemp ])) {
        $response = $_SERVER[ $httpTemp ];
    } else if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();

        if (isset($headers[ $key ])) {
            $response = $headers[ $key ];
        }

        $temp = strtolower($key);

        if (isset($headers[ $temp ])) {
            $response = $headers[ $temp ];
        }
    }

    return !empty($response)?$response: $defaultValue;
}

$token = getHeaderValue('token','');
$language =  getHeaderValue('en4lang', '');


    /**
 * check if token is exsits.
 */
$oToken = Engine_Api::_()->getDbtable('tokens', 'ynmobile');
if (
    $sApi != 'token'
    && $sMethod != 'languages'
    && $sMethod != 'iphone_css'
    && $sMethod != 'ipad_css'
    && $sMethod != 'android_css'
    && $sMethod != 'phrases'
//    && $sMethod != 'allows'
//    && $sMethod != 'settings'
    && $sMethod != 'download'
    && $sApi != 'user'
    && $sMethod != 'login'
    && $sMethod != 'register'
    && $sMethod != 'forgot'
    && $sMethod != 'sidebar'
    && $sMethod != 'verify_account'
    && $sMethod != 'settings'
    && $sMethod != 'ping'
    && $sMethod != 'signup_term'
    && $sMethod != 'signup_packages'
    && $sMethod != 'add_subscription'
    && $sMethod != 'update_subscription'
    && $sMethod != 'css_build_number'
    && (($sApi . '/' . $sMethod) != 'subscription/detail')
    && (($sApi . '/' . $sMethod) != 'core/settings')
    && (($sApi . '/' . $sMethod) != 'core/left_nav')
) {
    extract($aData, EXTR_SKIP);
    $aResult = $oToken->isValid($token);

    // Is not valid.
    if (count($aResult) > 0) {
        while (ob_get_level()) {
            ob_get_clean();
        }

        echo Zend_Json::encode($aResult);
        ob_end_flush();
        die;
    }
}

// verify token at first.
if ($token) {
    $aToken = $oToken->getToken($token);

    // defined('VIEWER_TOKEN') or define('VIEWER_TOKEN',$aToken['token_id']);    

    if ($aToken && isset($aToken['user_id'])) {

        $iViewerId = (int)$aToken['user_id'];

        if (!$iViewerId) {
            echo Zend_Json::encode(array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_("Invalid token!"),
                'error_debug'   => $service,
            ));
            exit;
        }

        $oViewer = Engine_Api::_()->user()->getUser($iViewerId);


        if (!$oViewer || !$oViewer->enabled) {
            $service = strtolower($sApi . "/" . $sMethod);

            if (false == in_array($service, array(
                    'subscription/add_subscription',
                    'subscription/update_subscription',
                    'subscription/packages',
                    'subscription/current_package',
                    'subscription/signup_packages',
                    'subscription/detail',
                    "core/settings",
                    'user/verify_account',
                ))
            ) {
                while (ob_get_level()) {
                    ob_get_clean();
                }

                echo Zend_Json::encode(array(
                    'error_code'    => 1,
                    'error_message' => Zend_Registry::get('Zend_Translate')->_("This user is disabled!"),
                    'error_debug'   => $service,
                ));
                die;
            }
        }
        Engine_Api::_()->user()->setViewer($oViewer);

    }
}

// setup language
if(true){
    // Make sure it's valid
    try {

        // Try to pull from various sources
        $viewer   = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
        $locale = null;

        if($language){
            try{
                $locale =  Zend_Locale::findLocale($language);
            }catch(Exception $ex){
                $locale = null;
//                exit("{error_msg: 'Locale not find'}");
            }
        }


        if(empty($locale)){
            if( $viewer->getIdentity() ) {
                $locale = $viewer->locale;
                $language = $viewer->language;
                $timezone = $viewer->timezone;
                Zend_Registry::set('timezone', $timezone);
            }
        }

        if($locale && $language){

            header('language:'. $language);
            header('locale:'. $locale);

            // Make sure it's valid
            try {
                $locale = Zend_Locale::findLocale($locale);
            } catch( Exception $e ) {
                $locale = 'en_US';
            }

            $localeObject =  Zend_Registry::get('Locale');

            $defaultLanguage  = Engine_Api::_()->getApi('settings', 'core')->core_locale_locale;

            $localeLanguage = $localeObject->getLanguage();

            $translate = Zend_Registry::get('Zend_Translate');

            $ls = array($locale, $language, $localeLanguage, $defaultLanguage, 'en');

            foreach ($ls as $l){
                if ( $translate->isAvailable($l) ) {
                    $translate->setLocale($l);
                    break;
                }
            }
        }


    } catch (Zend_Exception $e) {
        // use default language.
        exit(json_encode(array('err'=> $e->getMessage())));
    }
}

if (!method_exists($oApi, $sMethod)) {

    while (ob_get_level()) {
        ob_get_clean();
    }

    echo Zend_Json::encode(array(
        'error_code'    => 1,
        'error_message' => Zend_Registry::get('Zend_Translate')->_("This feature is temporary down by site administrators")
    ));
    die;
}

$ItemMaps = array(
    'advgroup' => array(
        'group'           => 'group',
        'group_album'     => 'advgroup_album',
        'group_category'  => 'advgroup_category',
        'group_list'      => 'advgroup_list',
        'group_list_item' => 'advgroup_list_item',
        'group_photo'     => 'advgroup_photo',
        'group_post'      => 'advgroup_post',
        'group_topic'     => 'advgroup_topic',
    ),
    'ynevent'  => array(),
    'ynvideo'  => array(),
    'advalbum' => array(
        'album'       => 'advalbum_album',
        'album_photo' => 'advalbum_photo',
        'photo'       => 'advalbum_photo',
    ),
    'ynforum'  => array(
        'forum'           => 'ynforum_forum',
        'forum_category'  => 'ynforum_category',
        'forum_container' => 'ynforum_container',
        'forum_post'      => 'ynforum_post',
        'forum_signature' => 'ynforum_signature',
        'forum_topic'     => 'ynforum_topic',
        'forum_list'      => 'ynforum_list',
        'forum_list_item' => 'ynforum_list_item',
    ),
    'mp3music' => array(// 'music_playlist'  => 'mp3music_album',
    ),
);

foreach ($ItemMaps as $from => $to) {
    if (Engine_Api::_()->hasModuleBootstrap($from)) {
        if (isset($aData['sItemType']) && isset($aData['sItemType']) != '') {
            foreach ($to as $originalItem => $newItem) {
                if ($aData['sItemType'] == $originalItem) {
                    $aData['sItemType'] = $newItem;
                }
            }
        }
    }
}


try {

    // fix issue of conflict with http mode.
    Zend_Controller_Front::getInstance()->setRequest(new Zend_Controller_Request_Http());

    $aResult = $oApi->{$sMethod}($aData, $iId);

    while (ob_get_level()) {
        ob_get_clean();
    }

    ob_start();
    $content = Zend_Json::encode($aResult);

    ob_get_clean();

    echo $content;
    exit(0);
} catch (Exception $ex) {

    while (ob_get_level()) {
        ob_get_clean();
    }

    echo Zend_Json::encode(array(
        'error_code'    => 500,
        'error_message' => Zend_Registry::get('Zend_Translate')->_("Could not fetch data from server"),
        'error_debug'   => $ex->getMessage(),
        'error_line'    => $ex->getLine(),
        'error_file'    => $ex->getFile(),
    ));
}
