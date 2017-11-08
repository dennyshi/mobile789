<?php

ini_set("display_errors", "on");
require_once("base/__config.php");
$t = $_REQUEST;
//$data = file_get_contents('php://input') ;
//$t = json_decode($data,true);
$params['isMobile'] = "1";
$params['username'] = $t['username'];
$params['company'] = SITE_ID;
$params['oid'] = $t['oid'];
$action = $t['action'];

$re0 = $clientA->isLoginUpload($params);
if (empty($params['oid']) || empty($params['username']) || empty($params['company']) || $re0['code'] != $objCode->is_login_status->code) {//用户已经登出，或异常
    $data['code'] = $objCode->is_login_status->code;
    $data['url'] = "";
    $data['msg'] = "用户未登录，请先登录";
//    echo "<script>alert('您还没登录，请先登录！'); self.close();</script>";
    return $data;
}
$get_protect_status = $clientA->getProtectStatus(SITE_ID, $action);
if( $get_protect_status['info']['status'] == 2 ){
    $data['code'] = "113057";
    $data['url'] = "";
    $data['msg'] = "游戏正在维护";
    return $data;
//    echo "<script>alert('游戏正在维护！'); self.close();</script>";
//    return false;
}
$lives = [
    'ag' => 2,
    'bb' => 11,
    'gg' => 12,
    'mg' => 13,
    'pt' => 14,
    'sa' => 15,
    'main' => 0,

    'changemoney' => 0
];
$f = new Fetch();
$keyp = CommonClass::get_key_param($params['username'] . GAME_PASSWORD . GAME_KEYB . date("Ymd"), 6, 9);
$ac = substr($action, 0, 2);
$paramsp = array('siteId' => SITE_ID, 'username' => $params['username'], 'live' => $lives[$ac], 'password' => GAME_PASSWORD, 'isDemo' => CommonClass::is_test_user($params['username']), 'key' => $keyp);
switch ($action) {
    case 'ag_live':
        $paramsp['gameType'] = 0;
        break;
    case 'ag_game':
        $paramsp['gameType'] = 8;
        break;
    case 'ag_fish':
        $paramsp['gameType'] = 6;
        break;
    case 'bb_live':
        $paramsp['page_site'] = 'live';
        break;
    case 'bb_game':
        $paramsp['page_site'] = 'game';
        break;
    case 'bb_sport':
        $paramsp['page_site'] = 'ball';
        break;
    case 'bb_lotto':
        $paramsp['page_site'] = 'Ltlottery';
        break;
    case 'pt_live':
        break;
    case 'pt_game':
        break;
    case 'pt_fish':
        break;
    case 'mg_live':
        $paramsp['gameType'] = "mg_live";
        break;
    case 'mg_game':
        $paramsp['gameType'] = "mg_game";
        break;
    case 'gg_game':
        break;
    case 'gg_fish':
        break;
    case 'sa_live':
        break;
    case 'sa_game':
        break;
    case 'sa_lotto':
        break;
}
if( ($paramsp['live'] == '13')||($paramsp['live'] == '14') ){
    $data['code'] = "10002";
    $data['url'] = "";
    $data['msg'] = "暂未开放手机端登录大厅";
//    echo "<script>alert('您还没登录，请先登录！'); self.close();</script>";
    return $data;
}


$r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
if (strstr($r, 'essage') === FALSE && $ac == 'bb') {
    $data['code'] = "10000";
    $data['url'] = $r;
    $data['msg'] = "BBIN平台登录地址获取成功";
    return $data;
}
if (strstr($r, 'essage') === FALSE && $ac == 'ag') {
    $data['code'] = "10000";
    $data['url'] = $r;
    $data['msg'] = "AG平台登录地址获取成功";
    return $data;
}
if ( ( !empty( $r ) ) && $ac == 'gg') {
    $data['code'] = "10000";
    $data['url'] = $r;
    $data['msg'] = "GG平台登录地址获取成功";
    return $data;
}

$res = json_decode($r, TRUE);
if (isset($res['result']) || (isset($res['status']) && $res['status'] == 10000)) {
//    echo "<script>location.href='{$res['message']}';</script>";
    $data['code'] = "10000";
    $data['url'] = $res['message'];
    $data['msg'] = "平台登录地址获取成功";
    return $data;
} else {
    $data['data'] = $params;
    $data['code'] = "10001";
    $data['url'] = $r;
    $data['msg'] = "平台登录地址获取失败";
    return $data;
//    print_r($r);
}

