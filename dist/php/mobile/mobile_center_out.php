<?php
ini_set("display_errors", "on");
require_once("../base/__config.php");
$t = $_REQUEST ;

$params['username'] = $t['username'];
$params['site_id'] = SITE_ID;
$params['oid'] = $t['oid'];
$params['ulevel'] = $t['ulevel'];
$agent =$t['agent'];
$isAlow = $clientA->isAlowSaveTakeFromAgent(SITE_ID, $agent); //判断允许存取款

$re = $clientA->getGetMoneyUserDetail($params);
$data_user_info = $re['info'];

$f = new Fetch();
$info = array();

if( $isAlow == 2 ){
    $info['code'] = 113061;
    echo json_encode($info);exit;

}elseif( $re['code'] == $objCode->success_get_getmoney_userdetial->code ) {


    $checklist = $clientA->getCheckList($params);
//    echo 666666;
//                echo json_encode($checklist);
//die;
    if ( ($checklist['code'] == $objCode->success_get_checklist->code) && (!empty($checklist['info'])) ) {
        $lastAllMoney = $clientA->getLastMoney(array('site_id' => SITE_ID, 'username' => $params['username'])); //所有平台的余额
//        echo "<br>";
//        print_r($lastAllMoney);die;
        $list = $checklist['info'];

        $all_normal_free = 0; //稽核手续费
        $all_promotion_free = 0; //需扣除优惠金额
        $all_bet_money = 0; //总投注量

        if (!empty($list[0])) {
            $yuecode = 0; //有余的有效投注
            $count = 0; //计数
            foreach ($list as $i => $v) {//计算稽核是否通过
                $count += 1;
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
//
                if ($result['returnCode'] == '900000') {
                    $bet_sum_money = isset($result['dataList']['totalValidamount']) ? $result['dataList']['totalValidamount'] : 0; //实际投注，总投注

                    $all_bet_money += $bet_sum_money;

                    $list[$i]['live_money'] = isset($result['dataList']['liveValidamount']) ? $result['dataList']['liveValidamount'] : 0; //实际投注，视讯投注
                    $list[$i]['sports_money'] = isset($result['dataList']['sportValidamount']) ? $result['dataList']['sportValidamount'] : 0; //实际投注，体育投注
                    $list[$i]['lotto_money'] = isset($result['dataList']['lottoValidamount']) ? $result['dataList']['lottoValidamount'] : 0; //实际投注，彩票投注
                    $list[$i]['game_money'] = isset($result['dataList']['jilvValidamount']) ? $result['dataList']['jilvValidamount'] : 0; //实际投注，几率投注

                    $normal_bet = $v['normal_code'] = is_null($v['normal_code']) ? 0 : $v['normal_code']; //常态有效投注  （有效投注改为有效投注）
                    $promos_bet = $v['promos_code'] = is_null($v['promos_code']) ? 0 : $v['promos_code']; //优惠有效投注
                    $save_money = $v['save_money'] = is_null($v['save_money']) ? 0 : $v['save_money']; //存款金额
                    $promos_money = $v['promos_money'] = is_null($v['promos_money']) ? 0 : $v['promos_money']; //优惠金额
                    if ($promos_money == 0) {
                        $promos_code = 0;
                    }
                    $extend_bet = $v['extend_code'] = is_null($v['extend_code']) ? 0 : $v['extend_code']; //放宽有效投注
                    $promotion_is_ok = 'V';
                    $promotion_is_kouchu_money = '否';
                    $promotion_kouchu_money = 0;

                    $normal_is_ok = 'V';
                    $normal_is_kouchu_money = '0';
                    $normal_kouchu_money = 0;
                    $yuecode += $bet_sum_money;

                    //常态稽核
                    if ($v['normal_code'] > 0) {

                        if ($yuecode - $v['normal_code'] + $v['extend_code'] >= 0) {
                            $yuecode = $yuecode - $v['normal_code'] + $v['extend_code']; //有余的有效投注累计到下一条稽核
                            $normal_check_status = 1; //通过存款有效投注审核，不需扣除手续费
                            $v['normal_free'] = 0; //通过常存款有效投注审核，不需扣除手续费
                        } else {
                            $normal_check_status = -1; //没通过存款有效投注审核，需扣除手续费
                            $all_normal_free += $v['normal_free']; //统计存款有效投注审核手续费

                            $normal_is_ok = 'X';
                            $normal_is_kouchu_money = (int)$v['normal_free'];
                            $normal_kouchu_money = $v['normal_free'];
                        }
                    } else {//不需稽核，不需扣除手续费
                        $v['normal_free'] = 0;
                        $normal_check_status = 1;
                    }
                    //优惠稽核
                    if ($list[$i]['promos_code'] > 0) {//综合有效投注大于0
                        $is_acoss_normal = 0;
                        if ($v['normal_code'] > 0 && $normal_check_status == 1) {//通过常态加上常态打码
                            $is_acoss_normal = $v['normal_code'];
                        }

                        if ($yuecode + $is_acoss_normal - $v['promos_code'] >= 0) {//通过优惠稽核
                            $yuecode = $yuecode + $is_acoss_normal - $v['promos_code'];  //有余的有效投注累计到下一条稽核
                            $poroms_check_status = 1; //通过优惠稽核，不需扣除优惠
                        } else {
                            if ($lastAllMoney < $promos_ye_check_limit && $lastAllMoney !== FALSE) {//余额优惠稽核不需消耗有效投注
                                $poroms_check_status = 1; //通过优惠稽核，不需扣除优惠
                            } else {
                                $all_promotion_free += $promos_money; //需扣除金额统计
                                $poroms_check_status = -1;

                                $promotion_is_ok = 'X';
                                $promotion_is_kouchu_money = $promos_money;
                                $promotion_kouchu_money = $promos_money;
                            }
                        }
                    } else {
                        $poroms_check_status = 1; //通过优惠稽核，不需扣除优惠
                    }
                    $all_kouchu_free = $promotion_kouchu_money + $v['normal_free'];

                }
                $info['data'][$i]['start_time'] = date("Y-m-d H:i", $v['start_time']);
                $info['data'][$i]['end_time'] = date("Y-m-d H:i", $v['end_time']);
                $info['data'][$i]['save_money'] = $save_money;
                $info['data'][$i]['promos_money'] = $promos_money;
                $info['data'][$i]['bet_sum_money'] = $bet_sum_money;
                $info['data'][$i]['extend_bet'] = $extend_bet;
                $info['data'][$i]['normal_bet'] = $normal_bet;
                $info['data'][$i]['normal_is_ok'] = $normal_is_ok;
                $info['data'][$i]['normal_is_kouchu_money'] = $normal_is_kouchu_money;
                $info['data'][$i]['promos_bet'] = $promos_bet;
                $info['data'][$i]['promotion_is_ok'] = $promotion_is_ok;
                $info['data'][$i]['promotion_kouchu_money'] = $promotion_kouchu_money;
                $info['data'][$i]['all_kouchu_free'] = $all_kouchu_free;

            }
            $info['code'] = 10000;
            $info['sum_info']['count'] = $count;
            $info['sum_info']['all_normal_free'] = $all_normal_free;
            $info['sum_info']['all_promotion_free'] = $all_promotion_free;
            $info['sum_info']['all_free'] = $all_promotion_free + $all_normal_free;

//        print_r($data);die;
            $info['bank_info']['bank_name'] = $data_user_info['bank_name'];
            $info['bank_info']['realname'] = $data_user_info['realname'];
            $info['bank_info']['bank_account'] = $data_user_info['bank_account'];
            $info['bank_info']['bank_adress'] = $data_user_info['bank_adress'];

            $replyfree = $clientA->getReplyOutFree(array('site_id' => $params['site_id'], 'ulevel' => $ulevel, 'username' => $params['username']));
            $replyhours_str = $replyfree['reply_hours'] . '小时内' . $replyfree['reply_time'] . '次出款。';
//        if ($replyfree['reply_fee']) {
//            $replyhours_str .= '需扣行政手续费：<span style="color:red">' . $replyfree['reply_fee'] . "</span>";
//        } else {
//            $replyhours_str .= '不需扣行政手续费';
//        }
            $all_free = $all_normal_free + $all_promotion_free + $replyfree['reply_fee'];
//        $check_time = date("Y-m-d H:i:s");
            $all_free_str = $all_free;
            $info['sum_info']['all_free_str'] = $all_free_str;
            $info['sum_info']['take_money_max'] = $replyfree['take_money_max'];
            $info['sum_info']['take_money_min'] = $replyfree['take_money_min'];
            $info['sum_info']['reply_fee'] = $replyfree['reply_fee'];
        }
        $info['code'] = 201025;
        echo json_encode($info);
    }else{
        //无稽核信息
        $info['code'] = 20000;          //此code是指无稽核信息
        $info['sum_info']['count'] = "";
        $info['sum_info']['all_normal_free'] = "";
        $info['sum_info']['all_promotion_free'] = "";
        $info['sum_info']['all_free'] = "";
        $info['bank_info']['bank_name'] = $data_user_info['bank_name'];
        $info['bank_info']['realname'] = $data_user_info['realname'];
        $info['bank_info']['bank_account'] = $data_user_info['bank_account'];
        $info['bank_info']['bank_adress'] = $data_user_info['bank_adress'];
        $info['sum_info']['all_free_str'] = "";
        $info['sum_info']['take_money_max'] = "";
        $info['sum_info']['take_money_min'] = "";
        $info['sum_info']['reply_fee'] = "";
        echo json_encode($info);
    }
}else{
    $info['code'] = 211026;
    $info['sum_info']['count'] = "";
    $info['sum_info']['all_normal_free'] = "";
    $info['sum_info']['all_promotion_free'] = "";
    $info['sum_info']['all_free'] = "";
    $info['bank_info']['bank_name'] = $data_user_info['bank_name'];
    $info['bank_info']['realname'] = $data_user_info['realname'];
    $info['bank_info']['bank_account'] = $data_user_info['bank_account'];
    $info['bank_info']['bank_adress'] = $data_user_info['bank_adress'];
    $info['sum_info']['all_free_str'] = "";
    $info['sum_info']['take_money_max'] = "";
    $info['sum_info']['take_money_min'] = "";
    $info['sum_info']['reply_fee'] = "";
    echo json_encode($info);
}

?>
