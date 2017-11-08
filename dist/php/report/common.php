<?php
$php_root = dirname(dirname(__FILE__));
require_once($php_root. "/base/__config.php");
$t = $_REQUEST;
//print_r($t);
$params['username'] = $t['username'];
$params['company'] = SITE_ID;
$params['oid'] = $t['oid'];

$re = $clientA->isLoginUpload($params);
if ($re['code'] != $objCode->is_login_status->code) {//用户已经登出，或异常
    unset($re['info']);
    echo CommonClass::ajax_return($re, $jsonp, $jsonpcallback);
    return false;
}

$key = CommonClass::get_key_param(SITE_WEB . $params['username'], 5, 6);
$paramsb = array(
    'fromKey' => SITE_WEB,
    'siteId' => SITE_ID,
    'username' => $params['username'],
    'key' => $key
);

$paramsb['re_deal_code'] = $t['re_deal_code'];
$paramsb['liveId'] = $t['live_game_id'];
$paramsb['gameKind'] = $t['child_game_id'];

$beginTime = $t['t1'] ? $t['t1'] : date("Y-m-d");
$endTime = $t['t2'] ? $t['t2'] : date("Y-m-d");

$beginTime = date( 'Y-m-d',strtotime($beginTime) );
$endTime = date( 'Y-m-d',strtotime($endTime) );
$t1 = $beginTime;
$t2 = $endTime;

$paramsb['hashCode'] = SITE_HASH_CODE;
$paramsb['page'] = (int) $t['page'];
$paramsb['pageSize'] = 10;
$paramsb['beginTime'] = $beginTime . ' 00:00:00';
$paramsb['endTime'] = $endTime . ' 23:59:59';

if (empty($paramsb['page']) || $paramsb['page'] < 1) {
    $paramsb['page'] = 1;
}

$paramsb['betTimeBegin'] = $beginTime ;
$paramsb['betTimeEnd'] = $endTime ;
$paramsb['gameType'] = "";

$paramsb['userInfoIsDetail'] = 1;
$clientB = new Fetch();
?>

