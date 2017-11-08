<?php
require_once("../base/__config.php");

$t = $_REQUEST;
$params['out_money'] = (int)$t['money'];
$params['real_out_money'] = (int) ($params['out_money'] - $t['gold']);
$gold = $t['gold'];
$params['username'] =  $t['username'];
$params['oid'] = $t['oid'];
$params['ulevel'] = $t['ulevel'];
$params['agent'] = $t['agent'];
$params['site_id'] = SITE_ID;
if( $t['h_promos_free'] > 0 ){
    $params['promos_money'] = $t['h_promos_free'];
}
if( $t['h_replys_free'] > 0  ){
    $params['reply_free_money'] =  $t['h_replys_free'];
}
$params['fee_money'] = $t['h_normal_free'];
$params['company'] =  SITE_ID;

$params['bank_name'] = $t['bank_name'];
$params['province'] =  $t['bank_adress'];
$params['bank_num'] =  $t['bank_account'];
$params['bank_user'] = $t["realname"];
$params['add_ip'] = $ip;
$str_to_log =$t['str_to_log'];

$p['get_password'] = $t['getpass'];
$p['company'] = SITE_ID;
$p['site_id'] = SITE_ID;
$p['username'] = $t['username'];
$ck = $clientA->check_get_pass($p);

if ($ck < 1) {
    $return = array('code' => $objCode->error_getpassword->code, 'error' => 'error -1');
    echo CommonClass::ajax_return($return, $jsonp, $jsonpcallback);
    return false;
}
//判断是否有数据更新，有就不让提现
$f = new Fetch();
$checklist = $clientA->getCheckList($params);

if( $checklist['code'] == $objCode->success_get_checklist->code ){
    $list = $checklist['info'];
    $lastAllMoney = $clientA->getLastMoney(array('site_id' => SITE_ID, 'username' => $params['username'])); //所有平台的余额
    $allfree = 0 ; //稽核手续费
    $alldeleteporoms = 0; //需扣除优惠金额
    $allmoney = 0; //总投注量
    foreach ($list as $i => $v) {//计算稽核是否通过
            $str = $params['site_id'] . $v['username'] . date("Y-m-d", $v['start_time']) . date("Y-m-d", $v['end_time']) . date("H:i:s", $v['start_time']) . date("H:i:s", $v['end_time']);
            $data = array(
                'siteId' => $params['site_id'],
                'username' => $v['username'],
                'betTimeBegin' => date("Y-m-d", $v['start_time']),
                'betTimeEnd' => date("Y-m-d", $v['end_time']),
                'startTime' => date("H:i:s", $v['start_time']),
                'endTime' => date("H:i:s", $v['end_time']),
                'key' => CommonClass::get_key_param($str, 5, 6)
            );
            $result1 = $f->NewPostData(MY_HTTP_CENTER_HOST . 'auditTotalTemp', json_encode($data));
            $result2 = str_replace(array('"[', ']"', '\"', '\\\\', '"{', '}"'), array('[', ']', '"', '\\', '{', '}'), $result1);
            $result = json_decode($result2, TRUE);
            if ($result['returnCode'] == '900000') {
                $sum_money = isset($result['dataList']['totalValidamount']) ? $result['dataList']['totalValidamount'] : 0; //实际投注，总投注

                $allmoney += $sum_money;

                $normal_code = $v['normal_code'] = is_null($v['normal_code']) ? 0 : $v['normal_code']; //常态打码量  （打码量改为有效投注）
                $promos_code = $v['promos_code'] = is_null($v['promos_code']) ? 0 : $v['promos_code']; //优惠打码量
                $save_money = $v['save_money'] = is_null($v['save_money']) ? 0 : $v['save_money']; //存款金额
                $promos_money = $v['promos_money'] = is_null($v['promos_money']) ? 0 : $v['promos_money']; //优惠金额
                if ($promos_money == 0) {
                    $promos_code = 0;
                }
                $extend_code = $v['extend_code'] = is_null($v['extend_code']) ? 0 : $v['extend_code']; //放宽打码量

                $yuecode += $sum_money;
            //常态稽核
            if ($v['normal_code'] > 0) {
                if ($yuecode - $v['normal_code'] + $v['extend_code'] >= 0) {
                    $yuecode = $yuecode - $v['normal_code'] + $v['extend_code']; //有余的打码量累计到下一条稽核
                    $normal_check_status = 1; //通过存款有效投注审核，不需扣除手续费
                    $v['normal_free'] = 0; //通过常存款有效投注审核，不需扣除手续费
                } else {
                    $normal_check_status = -1; //没通过存款有效投注审核，需扣除手续费
                    $allfree += $v['normal_free']; //统计存款有效投注审核手续费
                    $m_m_money = $v['normal_free'];
                }

            }
            //优惠稽核
            if ($list[$i]['promos_code'] > 0) {//综合打码量大于0
                $is_acoss_normal = 0;
                if ($v['normal_code'] > 0 && $normal_check_status == 1) {//通过常态加上常态打码
                    $is_acoss_normal = $v['normal_code'];
                }

                if ($yuecode + $is_acoss_normal - $v['promos_code'] >= 0) {//通过优惠稽核
                    $yuecode = $yuecode + $is_acoss_normal - $v['promos_code'];  //有余的打码量累计到下一条稽核
                    $poroms_check_status = 1; //通过优惠稽核，不需扣除优惠
                } else {
                    if ($lastAllMoney < $promos_ye_check_limit && $lastAllMoney !== FALSE) {//余额优惠稽核不需消耗打码量
                        $poroms_check_status = 1; //通过优惠稽核，不需扣除优惠
                    } else {
                        $alldeleteporoms += $promos_money; //需扣除金额统计
                        $poroms_check_status = -1;

                    }
                }
            } else {
                $poroms_check_status = 1; //通过优惠稽核，不需扣除优惠
            }
        }
    }
    $replyfree = $clientA->getReplyOutFree(array('site_id' => $params['site_id'], 'ulevel' => $ulevel, 'username' => $params['username']));
    $replyhours_str = $replyfree['reply_hours'] . '小时内' . $replyfree['reply_time'] . '次出款。';
    if ($replyfree['reply_fee']) {
        $replyhours_str .= '需扣行政手续费：<span style="color:red">' . $replyfree['reply_fee'] . "</span>";
    } else {
        $replyhours_str .= '不需扣行政手续费';
    }
    $all_free_proms_reply = $allfree + $alldeleteporoms + $replyfree['reply_fee'];
}

if( $gold <  $all_free_proms_reply ){
    $return = array('code' => $objCode->fail_draw_money->code, 'error' => 'error -1','gold'=>$gold,'all_free_proms_reply'=>$all_free_proms_reply);
    echo CommonClass::ajax_return($return, $jsonp, $jsonpcallback);
    return false;
}


//调用出款接口
$clientC = new Fetch();
$billno = CommonClass::get_billno(SITE_ID, $params['username'], $params['out_money']);
$key = CommonClass::get_key_param(SITE_WEB . $params['username'] . $billno, 5, 6);
$paramsb = array(
    'fromKey' => SITE_WEB,
    'siteId' => SITE_ID,
    'username' => $params['username'],
    'remitno' => $billno,
    'remit' => $params['out_money'],
    'transType' => 'out',
    'fromKeyType' => '10009',
    'key' => $key,
    'memo' => '会员申请出款,单号：' . $billno
);
$res = $clientC->NewPostData(MY_HTTP_MONEY_HOST . 'transMoney', $paramsb);
$result = json_decode($res, TRUE);
if ($result['code'] == '100000') {//出款扣金额成功
    $params['billno'] = $billno;
    $params['logstr'] = $str_to_log;
//    echo 99999;
//    echo json_encode($params);
    unset($p['company']);
    $return = $clientA->add_take_money_order($params); //添加出款订单~~~~~
//    echo "<br>";
//    print_r($return);
    if ($return['code'] == $objCode->success_take_money_order->code) {
        //$return['data'] = json_decode($return['info'], TRUE);
        unset($return['info']);
    } else {
        $return = array('code' => $objCode->fail_take_money_order->code, 'error' => 'error 1');
    }
} else {//扣除金额失败
    $return = array('code' => $objCode->fail_take_money_order->code, 'error' => $res, 'p' => $paramsb);
}
echo CommonClass::ajax_return($return, $jsonp, $jsonpcallback);



