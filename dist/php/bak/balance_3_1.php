<?php
//error_reporting(0);
//ini_set('display_errors', 'on');
require_once("config.php");
require_once("Fetch.class.php");

$params['username'] = filter_input(INPUT_POST, 'username');
$params['company'] = SITE_ID;
$params['oid'] = filter_input(INPUT_POST, 'oid');
$re = $clientA->isLoginUpload($params);
if ($re['code'] != $objCode->is_login_status->code) {//用户已经登出，或异常
    unset($re['info']);
    echo CommonClass::ajax_return($re, $jsonp, $jsonpcallback);
    return false;
}
$session = json_decode($re['info'],TRUE);
if(substr($session['user_type'], -1) == 1){//主副站修改
    $AG_PREFIX = AG_PREFIX_TEST;
    $BBIN_PREFIX = BBIN_PREFIX_TEST;
    $DS_PREFIX = DS_PREFIX_TEST;
    $H8_PREFIX = H8_PREFIX_TEST;
    
    $AG_HASHCODE = AG_HASHCODE_TEST;
    $BBIN_HASHCODE = BBIN_HASHCODE_TEST;
    $DS_HASHCODE = DS_HASHCODE_TEST;
    $H8_HASHCODE = H8_HASHCODE_TEST;
    $MY_HTTP_MONEY_HOST = MY_HTTP_MONEY_HOST_TEST;
    $MY_TCP_MONEY_HOST = MY_TCP_MONEY_HOST_TEST;
}else{
    $AG_PREFIX = AG_PREFIX;
    $BBIN_PREFIX = BBIN_PREFIX;
    $DS_PREFIX = DS_PREFIX;
    $H8_PREFIX = H8_PREFIX;
    
    $AG_HASHCODE = AG_HASHCODE;
    $BBIN_HASHCODE = BBIN_HASHCODE;
    $DS_HASHCODE = DS_HASHCODE;
    $H8_HASHCODE = H8_HASHCODE;
    $MY_HTTP_MONEY_HOST = MY_HTTP_MONEY_HOST;
    $MY_TCP_MONEY_HOST = MY_TCP_MONEY_HOST;
}
switch ($action) {
    case 'main':
        //$clientB = new HproseSwooleClient($MY_TCP_MONEY_HOST); //光光
        //define('MY_HTTP_MONEY_HOST', 'http://119.9.70.177:18080/kg-money-api/');
        $f = new Fetch($MY_HTTP_MONEY_HOST.'getMoney');
        
        $key = CommonClass::get_key_param(SITE_WEB . $params['username'], 5, 6);
        $paramsb = array('fromKey' => SITE_WEB, 'siteId' => SITE_ID, 'username' => $params['username'], 'key' => $key);
        //$res = $clientB->getMoney(json_encode($paramsb));
        $res = $f->CheckUsrBalance($paramsb);
        $result = json_decode($res, TRUE);
        if ($result['code'] == '100000') {
            $return = array(
                'code' => $objCode->success_to_get_main_money->code,
                'data' => array('money' => sprintf("%.2f", $result['data']['money']))
            );
        } else {
            $return = array(
                'code' => $objCode->fail_to_get_main_money->code,
                'data' => $result,
                'url'=>$MY_HTTP_MONEY_HOST.'getMoney'
            );
        }
        break;
    case 'ag':
        $f = new Fetch(PINGTAI_URL);
        $p = array(
            'username' => $AG_PREFIX . $params['username'],
            'password' => AG_PASSWORD,
            'hashcode' => $AG_HASHCODE,
            'keyb' => AG_KEYB,
            'live' => AG_LIVE_TYPE,
            'key' => CommonClass::get_key_param($AG_PREFIX . $params['username'] . AG_PASSWORD . AG_KEYB . date("Ymd"), 7, 3)
        );
        $data = $f->CheckUsrBalance($p);
        if ($data) {
            $re = json_decode($data, TRUE);
            if ($re['status'] == '10000') {
                $return = array(
                    'code' => $objCode->success_to_get_ag_money->code,
                    'data' => array('money' => sprintf("%.2f", $re['balance']))
                );
            } else {
//                $return = array('code' => $objCode->fail_to_get_ag_money->code);
                $return = array('code' => $objCode->fail_to_get_ag_money->code,'data'=>$re,'p'=>$p,'url'=>PINGTAI_URL);
            }
        } else {
//            $return = array('code' => $objCode->fail_to_get_ag_money->code);
            $return = array('code' => $objCode->fail_to_get_ag_money->code,'data'=>$data,'p'=>$p,'url'=>PINGTAI_URL);
        }
        break;
    case 'bin':
        $f = new Fetch(PINGTAI_URL);
        $p = array(
            'username' => $BBIN_PREFIX . $params['username'],
            'password' => BBIN_PASSWORD,
            'hashcode' => $BBIN_HASHCODE,
            'keyb' => BBIN_KEYB,
            'live' => BBIN_LIVE_TYPE,
            'key' => CommonClass::get_key_param($BBIN_PREFIX . $params['username'] . BBIN_PASSWORD . BBIN_KEYB . date("Ymd"), 7, 3)
        );
//        $f->debug = TRUE;
        $data = $f->CheckUsrBalance($p);
        if ($data) {
            $re = json_decode($data, TRUE);
            if ($re['status'] == '10000') {
                $return = array(
                    'code' => $objCode->success_to_get_bin_money->code,
                    'data' => array('money' => sprintf("%.2f", $re['balance']))
                );
            } else {
//                $return = array('code' => $objCode->fail_to_get_bin_money->code);
                $return = array('code' => $objCode->fail_to_get_bin_money->code,'data'=>$re,'p'=>$p,'url'=>PINGTAI_URL);
            }
        } else {
//            $return = array('code' => $objCode->fail_to_get_bin_money->code);
            $return = array('code' => $objCode->fail_to_get_bin_money->code,'data'=>$data,'p'=>$p,'url'=>PINGTAI_URL);
        }
        break;
    case 'h8':
        $f = new Fetch(PINGTAI_URL);
        $p = array(
            'username' => $H8_PREFIX . $params['username'],
            'password' => H8_PASSWORD,
            'hashcode' => $H8_HASHCODE,
            'keyb' => H8_KEYB,
            'live' => H8_LIVE_TYPE,
            'key' => CommonClass::get_key_param($H8_PREFIX . $params['username'] . H8_PASSWORD . H8_KEYB . date("Ymd"), 7, 3)
        );
//        $f->debug = TRUE;
        $data = $f->CheckUsrBalance($p);
        if ($data) {
            $re = json_decode($data, TRUE);
            if ($re['status'] == '10000') {
                $return = array(
                    'code' => $objCode->success_to_get_h8_money->code,
                    'data' => array('money' => sprintf("%.2f", $re['balance']))
                );
            } else {
//                $return = array('code' => $objCode->fail_to_get_h8_money->code);
                $return = array('code' => $objCode->fail_to_get_h8_money->code,'data'=>$re,'p'=>$p,'url'=>PINGTAI_URL);
            }
        } else {
//            $return = array('code' => $objCode->fail_to_get_h8_money->code);
            $return = array('code' => $objCode->fail_to_get_h8_money->code,'data'=>$data,'p'=>$p,'url'=>PINGTAI_URL);
        }
        break;
    case 'ds':
        $return = array(
            'code' => $objCode->success_to_get_ds_money->code,
            'data' => array('money' => 103.25)
        );
        break;
    case 'xyb'://获取有效幸运币
        $params['username'] = filter_input(INPUT_POST, 'username');
        $params['company'] = SITE_ID;
        
        $str = $params['company'] . $params['username'] . date("Y-m-d") . date("Y-m-d") . '00:00:00' . '23:59:59';
        $data = array(
            'siteId' => $params['company'],
            'username' => $params['username'],
            'betTimeBegin' => date("Y-m-d"),
            'betTimeEnd' => date("Y-m-d"),
            'startTime' => '00:00:00',
            'endTime' => '23:59:59',
            'key' => CommonClass::get_key_param($str, 5, 6)
        );
        $result1 = $clientB->auditTotalTemp(json_encode($data));
        $result2 = str_replace(array('"[', ']"', '\"', '\\\\', '"{', '}"'), array('[', ']', '"', '\\', '{', '}'), $result1);
        $result = json_decode($result2, TRUE);
        $xyb = 0;
        if ($result['returnCode'] == '900000') {
            $allcode = isset($result['dataList']['totalValidamount'])?$result['dataList']['totalValidamount']:0; //实际投注，总投注
            $allbi = round($allcode * XYB_RATE);
            if($allbi > 0){
                $xf = $clientA->countUsedXingyunBi($params);//已消费的幸运币
            }
            $xyb = $allbi - $xf;//有效幸运币
        }
        echo ($xyb >= 0) ? $xyb : 0;
        break;
    case 'all'://查询所有月
        $clientB = new HproseSwooleClient($MY_TCP_MONEY_HOST); //光光
        $key = CommonClass::get_key_param(SITE_WEB . $params['username'], 5, 6);
        $paramsb = array('fromKey' => SITE_WEB, 'siteId' => SITE_ID, 'username' => $params['username'], 'key' => $key);
        $res = $clientB->getMoney(json_encode($paramsb));
        $result = json_decode($res, TRUE);
        $mainmoney = 0;
        if ($result['code'] == '100000') {
            $mainmoney = sprintf("%.2f", $result['data']['money']);
        }
        break;
    case 'changemoney':
        $params['site_id'] = SITE_ID;
        $params['cout'] = $cout = filter_input(INPUT_POST, 'cout');
        $params['cin'] = $cin = filter_input(INPUT_POST, 'cin');
        $params['money'] = $money = filter_input(INPUT_POST, 'money');
        if (
                $cout == $cin ||
                ($cout != 1 && $cout != 2 && $cout != 3 && $cout != 4 && $cout != 12) ||
                ($cin != 1 && $cin != 2 && $cin != 3 && $cin != 4 && $cin != 34) ||
                !CommonClass::check_money($money) ||
                $money <= 0
        ) {
            $return = array('code' => $objCode->fail_change_money->code);
            break;
        }
        unset($params['oid']);
        $re = $clientA->addChangeMoney($params); //添加转账注单
        if ($re['code'] == $objCode->success_change_money->code) {
            $billno = $re['info'];

            //开始调取转账接口
            $ct = '' . $cout . $cin;
            $ar['action'] = 'transfer';
            $ar['billno'] = $billno;
            $ar['credit'] = $money;
            $ar['fromKey'] = SITE_WEB;
            //$ar['uppername'] = UP_NAME;
            switch ($ct) {
                case '12':
                    //从DS主账户转到ag
                    $ar['type'] = 'IN';
                    $ar['hashcode'] = $AG_HASHCODE;
                    $ar['username'] = $AG_PREFIX . $params['username'];
                    $ar['password'] = AG_PASSWORD;
                    $ar['keyb'] = AG_KEYB;
                    $ar['live'] = AG_LIVE_TYPE;
                    $ar['key'] = CommonClass::get_key_param($AG_PREFIX . $params['username'] . AG_PASSWORD . AG_KEYB . date("Ymd"), 4, 1);
                    //$ar['fromKeyType'] = '20002';
                    //$ar['fromKeyType'] = '20002';
                    break;
                case '13':
                    //从DS主账户转到bbin
                    $ar['type'] = 'IN';
                    $ar['hashcode'] = $BBIN_HASHCODE;
                    $ar['username'] = $BBIN_PREFIX . $params['username'];
                    $ar['password'] = BBIN_PASSWORD;
                    $ar['keyb'] = BBIN_KEYB;
                    $ar['live'] = BBIN_LIVE_TYPE;
                    $ar['key'] = CommonClass::get_key_param($BBIN_PREFIX . $params['username'] . BBIN_PASSWORD . BBIN_KEYB . date("Ymd"), 4, 1);
                    //$ar['fromKeyType'] = '20001';
//                    $ar['fromKeyType'] = '2000';
                    break;
                case '14':
                    //从DS主账户转到h8
                    $ar['type'] = 'IN';
                    $ar['hashcode'] = $H8_HASHCODE;
                    $ar['username'] = $H8_PREFIX . $params['username'];
                    $ar['password'] = H8_PASSWORD;
                    $ar['keyb'] = H8_KEYB;
                    $ar['live'] = H8_LIVE_TYPE;
                    $ar['key'] = CommonClass::get_key_param($H8_PREFIX . $params['username'] . H8_PASSWORD . H8_KEYB . date("Ymd"), 4, 1);
                    //$ar['fromKeyType'] = '20003';
                    //$ar['fromKeyType'] = '2000';
                    break;
                case '21':
                    //从ag转到DS主账户
                    $ar['type'] = 'OUT';
                    $ar['hashcode'] = $AG_HASHCODE;
                    $ar['username'] = $AG_PREFIX . $params['username'];
                    $ar['password'] = AG_PASSWORD;
                    $ar['keyb'] = AG_KEYB;
                    $ar['live'] = AG_LIVE_TYPE;
                    $ar['key'] = CommonClass::get_key_param($AG_PREFIX . $params['username'] . AG_PASSWORD . AG_KEYB . date("Ymd"), 4, 1);
                    //$ar['fromKeyType'] = '20005';
                    //$ar['fromKeyType'] = '2000';
                    break;
                case '23':
                    //从ag转到bbin
                    $ar['type'] = 'OUT';
                    $ar['hashcode'] = $AG_HASHCODE;
                    $ar['username'] = $AG_PREFIX . $params['username'];
                    $ar['password'] = AG_PASSWORD;
                    $ar['keyb'] = AG_KEYB;
                    $ar['live'] = AG_BBIN_LIVE_TYPE;
                    $ar['key'] = CommonClass::get_key_param($AG_PREFIX . $params['username'] . AG_PASSWORD . AG_KEYB . date("Ymd"), 4, 1);
                    //$ar['fromKeyType'] = '20005';
                    //$ar['fromKeyType'] = '2000';
                    break;
                case '24':
                    //从ag转到H8
                    $ar['type'] = 'OUT';
                    $ar['hashcode'] = $AG_HASHCODE;
                    $ar['username'] = $AG_PREFIX . $params['username'];
                    $ar['password'] = AG_PASSWORD;
                    $ar['keyb'] = AG_KEYB;
                    $ar['live'] = AG_H8_LIVE_TYPE;
                    $ar['key'] = CommonClass::get_key_param($AG_PREFIX . $params['username'] . AG_PASSWORD . AG_KEYB . date("Ymd"), 4, 1);
                    //$ar['fromKeyType'] = '20005';
                    //$ar['fromKeyType'] = '2000';
                    break;
                case '31':
                    //从bbin转到DS主账户
                    $ar['type'] = 'OUT';
                    $ar['hashcode'] = $BBIN_HASHCODE;
                    $ar['username'] = $BBIN_PREFIX . $params['username'];
                    $ar['password'] = BBIN_PASSWORD;
                    $ar['keyb'] = BBIN_KEYB;
                    $ar['live'] = BBIN_LIVE_TYPE;
                    $ar['key'] = CommonClass::get_key_param($BBIN_PREFIX . $params['username'] . BBIN_PASSWORD . BBIN_KEYB . date("Ymd"), 4, 1);
                    //$ar['fromKeyType'] = '20004';
                    //$ar['fromKeyType'] = '2000';
                    break;
                case '32':
                    //从bbin转到ag
                    $ar['type'] = 'OUT';
                    $ar['hashcode'] = $BBIN_HASHCODE;
                    $ar['username'] = $BBIN_PREFIX . $params['username'];
                    $ar['password'] = BBIN_PASSWORD;
                    $ar['keyb'] = BBIN_KEYB;
                    $ar['live'] = BBIN_AG_LIVE_TYPE;
                    $ar['key'] = CommonClass::get_key_param($BBIN_PREFIX . $params['username'] . BBIN_PASSWORD . BBIN_KEYB . date("Ymd"), 4, 1);
                    //$ar['fromKeyType'] = '20004';
                    //$ar['fromKeyType'] = '2000';
                    break;
                case '34':
                    //从bbin转到ag
                    $ar['type'] = 'OUT';
                    $ar['hashcode'] = $BBIN_HASHCODE;
                    $ar['username'] = $BBIN_PREFIX . $params['username'];
                    $ar['password'] = BBIN_PASSWORD;
                    $ar['keyb'] = BBIN_KEYB;
                    $ar['live'] = BBIN_H8_LIVE_TYPE;
                    $ar['key'] = CommonClass::get_key_param($BBIN_PREFIX . $params['username'] . BBIN_PASSWORD . BBIN_KEYB . date("Ymd"), 4, 1);
                    //$ar['fromKeyType'] = '20004';
                    //$ar['fromKeyType'] = '2000';
                    break;
                case '41':
                    //从h8转到main
                    $ar['type'] = 'OUT';
                    $ar['hashcode'] = $H8_HASHCODE;
                    $ar['username'] = $H8_PREFIX . $params['username'];
                    $ar['password'] = H8_PASSWORD;
                    $ar['keyb'] = H8_KEYB;
                    $ar['live'] = H8_LIVE_TYPE;
                    $ar['key'] = CommonClass::get_key_param($H8_PREFIX . $params['username'] . H8_PASSWORD . H8_KEYB . date("Ymd"), 4, 1);
                    //$ar['fromKeyType'] = '20004';
                    //$ar['fromKeyType'] = '2000';
                    break;
                case '42':
                    //从h8转到ag
                    $ar['type'] = 'OUT';
                    $ar['hashcode'] = $H8_HASHCODE;
                    $ar['username'] = $H8_PREFIX . $params['username'];
                    $ar['password'] = H8_PASSWORD;
                    $ar['keyb'] = H8_KEYB;
                    $ar['live'] = H8_AG_LIVE_TYPE;
                    $ar['key'] = CommonClass::get_key_param($H8_PREFIX . $params['username'] . H8_PASSWORD . H8_KEYB . date("Ymd"), 4, 1);
                    //$ar['fromKeyType'] = '20004';
                    //$ar['fromKeyType'] = '2000';
                    break;
                case '43':
                    //从h8转到bbin
                    $ar['type'] = 'OUT';
                    $ar['hashcode'] = $H8_HASHCODE;
                    $ar['username'] = $H8_PREFIX . $params['username'];
                    $ar['password'] = H8_PASSWORD;
                    $ar['keyb'] = H8_KEYB;
                    $ar['live'] = H8_BBIN_LIVE_TYPE;
                    $ar['key'] = CommonClass::get_key_param($H8_PREFIX . $params['username'] . H8_PASSWORD . H8_KEYB . date("Ymd"), 4, 1);
                    //$ar['fromKeyType'] = '20004';
                    //$ar['fromKeyType'] = '2000';
                    break;
                case '1234':
                    //资金归集
                    $ar['type'] = 'OUT';
                    $ar['username'] = $AG_PREFIX . $params['username'];
                    $ar['hashcode'] = $AG_HASHCODE;
                    $ar['bbinhashcode'] = $BBIN_HASHCODE;
                    $ar['h8hashcode'] = $H8_HASHCODE;
                    $ar['password'] = AG_PASSWORD;
                    $ar['key'] = CommonClass::get_key_param($AG_PREFIX . $params['username'] . AG_PASSWORD . AG_KEYB . date("Ymd"), 4, 1);
                    //$ar['fromKeyType'] = '2000';
                    $ar['billno'] = $billno;
                    $ar['live'] = 20;
                    unset($ar['credit']);
                    break;
            }
            if ($ar['key']) {
                $f = new Fetch(PINGTAI_URL);
                //$f->debug = TRUE;
                $data = $f->Transfer($ar);
                //print_r($data);
            }
            if ($data) {
                $re = json_decode($data, TRUE);
                if ($re['status'] == '10000') {
                    $return = array(
                        'code' => $objCode->success_change_money->code
                    );
                } else {
                    $return = array('code' => $objCode->fail_change_money->code);
                }
            } else {
                $return = array('code' => $objCode->fail_change_money->code);
            }
        }
        break;
    case 'sys800':
       $clientB = new HproseSwooleClient($MY_TCP_MONEY_HOST); //光光
        $beginTime = filter_input(INPUT_POST, 'beginTime');
        $endTime = filter_input(INPUT_POST, 'endTime');
        $fromKeyType = filter_input(INPUT_POST, 'fromKeyType');
        if($fromKeyType == 1){
            $fromKeyType = '';
            for($i=10001;$i<=10067;$i++){
                $fromKeyType .= $i.',';
            }
            $fromKeyType = trim($fromKeyType, ',');
        }else if($fromKeyType == 2){
            $fromKeyType = '60000,60001,';
            for($i=20001;$i<=20009;$i++){
                $fromKeyType .= $i.',';
            }
            $fromKeyType = trim($fromKeyType, ',');
        }else if($fromKeyType == 3){
            $fromKeyType = '2000,2001,50001,50002,';
            for($i=30001;$i<=30012;$i++){
                $fromKeyType .= $i.',';
            }
            $fromKeyType = trim($fromKeyType, ',');
        } 
        $page = filter_input(INPUT_POST, 'page');
        $pageSize = filter_input(INPUT_POST, 'pageSize');
        $key = CommonClass::get_key_param(SITE_WEB.$params['username'], 5, 6);
        $paramsb = array(
            'fromKey' => SITE_WEB,
            'siteId' => SITE_ID,
            'username' => $params['username'],
            'key' => $key
        );
        if(!empty($beginTime)){
            $paramsb['beginTime'] = $beginTime;
        }
        if(!empty($endTime)){
            $paramsb['endTime'] = $endTime;
        }
        if (!empty($fromKeyType)) {
            $paramsb['fromKeyType'] = $fromKeyType;
        } else {
            $fromKeyType = '';
            for ($i = 10001; $i <= 10052; $i++) {
                $fromKeyType .= $i . ',';
            }

            for ($i = 20001; $i <= 20009; $i++) {
                $fromKeyType .= $i . ',';
            }
            $fromKeyType = trim($fromKeyType, ',');
        }
        if(!empty($page)){
            $paramsb['page'] = $page;
        }
        if(!empty($pageSize)){
            $paramsb['pageSize'] = $pageSize;
        }
        $paramsb['userInfoIsDetail'] = 1;
        //print_r($paramsb);
        try {
            $res = $clientB->memberMoneyLog(json_encode($paramsb));
        } catch (Exception $e) {
            $error=$e->getMessage();
        }
        $ret = json_decode($res, TRUE);
        if($ret['code'] == '100000'){
            $return['data'] = array();
            if($ret['data']){
                foreach($ret['data'] as $i=>$v){
                    $return['data'][$i]['mtime'] = date("Y-m-d H:i:s",$v['createTime']/1000);
                    $return['data'][$i]['mtype'] = isset($formKeyType[$v['fromKeyType']])?$formKeyType[$v['fromKeyType']]:'测试数据';
                    $return['data'][$i]['mnowmoney'] = sprintf("%.2f", $v['afterMoney']);
                    if($v['transType'] == 'in') {
                        $return['data'][$i]['mgold'] = sprintf("%.2f", $v['remit']);
                    }else{
                        $return['data'][$i]['mgold'] = '<span style="color:red;"> -'.sprintf("%.2f", $v['remit']).'</span>';
                    }
                    $return['data'][$i]['mnote'] = $v['memo'];
                    if(strstr($v['memo'], '操作者')){
                        $mn = explode('(', $v['memo']);
                        if(isset($mn[1])){
//                            $return['data'][$i]['mnote'] = substr($mn[1], 0, -1);
                            $return['data'][$i]['mnote'] = str_replace(')', '', $mn[1]);
                        }
                    }
                    $return['data'][$i]['billno'] = $v['transId'];
                }
            }
            if(($ret['pagation']['totalNumber']%$ret['pagation']['pageSize']) != 0){
                $ret['pagation']['allPage'] = intval(floor($ret['pagation']['totalNumber']/$ret['pagation']['pageSize'])) + 1;
            }else{
                $ret['pagation']['allPage'] = intval(floor($ret['pagation']['totalNumber']/$ret['pagation']['pageSize']));
            }
            $return['pagation'] = $ret['pagation'];
            $return['code'] = $objCode->success_get_money_logs->code;
            $return['re'] = $ret;
            $return['pra'] = $paramsb;
            $return['error']=$error;
        }else{
            $return = array('code'=>$objCode->fail_get_money_logs->code,'error'=>$error);
        }   
        break;
}
echo CommonClass::ajax_return($return, $jsonp, $jsonpcallback);

