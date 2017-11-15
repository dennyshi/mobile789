<?php
require_once("base/__config.php");
$t = $_REQUEST;
     // print_r($t);
$params['username'] = $t['username'];
$params['company'] = SITE_ID;
$params['oid'] = $t['oid'];
$re = $clientA->isLoginUpload($params);

if ($re['code'] != $objCode->is_login_status->code) {//用户已经登出，或异常
    unset($re['info']);
    echo CommonClass::ajax_return($re, $jsonp, $jsonpcallback);
    return false;
}
$lives = [
    'ag' => 2,
    'bb' => 11,
    'gg' => 12,
    'mg' => 13,
    'pt' => 14,
    'sa' => 15,
    'cf' => 16,
    'ww' => 17,

    'main'=>0,
    'changemoney'=>0
];
$f = new Fetch();
$keyp = CommonClass::get_key_param($params['username'].GAME_PASSWORD.GAME_KEYB.date("Ymd"), 7, 3);
$paramsp = array( 'siteId' => SITE_ID, 'username' => $params['username'], 'live'=>$lives[$action],'password'=>GAME_PASSWORD,'isDemo'=> CommonClass::is_test_user($params['username']),'key'=>$keyp);
switch ($action) {
    case 'main':
        $key = CommonClass::get_key_param(SITE_WEB . $params['username'], 5, 6);
        $paramsb = array('fromKey' => SITE_WEB, 'siteId' => SITE_ID, 'username' => $params['username'], 'key' => $key);
        $res = $f->NewPostData(MY_HTTP_MONEY_HOST.'getMoney', $paramsb);
        $result = json_decode($res, TRUE);
        if ($result['code'] == '100000') {
            $return = array(
                'code' => $objCode->success_to_get_main_money->code,
                'data' => array('money' => substr(sprintf("%.5f", $result['data']['money']), 0, -3))
            );
        } else {
            $return = array(
                'code' => $objCode->fail_to_get_main_money->code,
                'data' => $result,
                'url' => MY_HTTP_MONEY_HOST . 'getMoney'
            );
        }
        break;
    case 'ag':
    case 'bb':
    case 'pt':
    case 'mg':
    case 'gg':
    case 'sa':
    case 'cf':
    case 'ww':
    // $f->debug();
    // print_r($paramsp);
        $res = $f->NewPostData(PINGTAI_URL.'queryBalance',$paramsp);
        // echo "<br>";
        // echo 1111111;
        // print_r($res);die;
        $return = json_decode($res,TRUE);
        $return['p'] = $paramsp;
        $return['p']['pre'] = $params['username'].GAME_PASSWORD.GAME_KEYB.date("Ymd");
        break;
    case 'changemoney':
        $keyp = CommonClass::get_key_param($params['username'].GAME_PASSWORD.GAME_KEYB.date("Ymd"), 4, 1);
        $paramsp['key'] = $keyp;
        $params['site_id'] = SITE_ID;
        $params['cout'] = $cout = $t['cout'];
        $params['cin'] = $cin = $t['cin'];
        $params['money'] = $money = $t['money'];
        if($money < 0 || !CommonClass::check_money($money)){
            $return = array('code' => -1);
            break;
        }
        $ct = $cout."_".$cin;
        $change_type_arr = ['ms_ag','ms_bb','ms_pt','ms_mg','ms_gg','ms_sa','ms_cf','ms_ww','ag_ms','bb_ms','pt_ms','mg_ms','gg_ms','sa_ms','cf_ms','ww_ms'];
        if (!in_array($ct, $change_type_arr)) {
            $return = array('code' => $objCode->fail_change_money->code);
            break;
        }
        $re = $clientA->addChangeMoney($params); //添加转账注单

        if ($re['code'] == $objCode->success_change_money->code) {
            $billno = $re['info'];
            //开始调取转账接口
            $paramsp['billno'] = $billno;
            $paramsp['credit'] = $money;
            $paramsp['operator'] = $params['username'];

            switch ($ct) {
                case 'ms_ag':
                case 'ms_bb':
                case 'ms_pt':
                case 'ms_mg':
                case 'ms_gg':
                case 'ms_sa':
                case 'ms_cf':
                case 'ms_ww':
                    $paramsp['live'] = $lives[$cin];
                    $paramsp['type'] = 'IN';
                    $paramsp['transMethod'] = $cin;
                    if($cin == 'bb'){
                        $paramsp['transMethod'] = 'bbin';
                        $paramsp['billno'] = '1000'.substr($billno,4);
                    }
                    $res = $f->NewPostData(PINGTAI_URL.'transfer ',$paramsp);
                    break;
                case 'ag_ms':
                case 'bb_ms':
                case 'pt_ms':
                case 'mg_ms':
                case 'gg_ms':
                case 'sa_ms':
                case 'cf_ms':
                case 'ww_ms':
                    $paramsp['live'] = $lives[$cout];
                    $paramsp['type'] = 'OUT';
                    $paramsp['transMethod'] = $cout;
                    if($cout == 'bb'){
                        $paramsp['transMethod'] = 'bbin';
                        $paramsp['billno'] = '1001'.substr($billno,4);
                    }
                    $res = $f->NewPostData(PINGTAI_URL.'transfer ',$paramsp);
                    break;
                case '12345':
                    //资金归集
                    $ar['live'] = '99999';
                    $ar['transMethod'] = 'balanceTotal';
                    unset($ar['credit']);
                    break;
            }
            if ($res) {
                $re = json_decode($res, TRUE);
                if ($re['status'] == '10000') {
                    $return = array(
                        'code' => $objCode->success_change_money->code
                    );
                } else {
                    $return = array('code' => $objCode->fail_change_money->code);
                }
                $return['java'] = $re;
                $return['p'] = $paramsp;
            } else {
                $return = array('code' => $objCode->fail_change_money->code);
            }
        }
        break;
}
echo CommonClass::ajax_return($return, $jsonp, $jsonpcallback);
