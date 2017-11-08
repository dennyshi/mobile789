<?php
ini_set("display_errors", "on");
require_once("config.php");
$clientB = new HproseSwooleClient(MY_TCP_CENTER_HOST); //小温
switch ($action) {
    case 'getgametype':
        $re = $clientA->getGameType();
        if ($re['code'] == $objCode->success_get_game_type->code) {
            $re['data'] = array('gametype' => json_decode($re['info']));
        }
        unset($re['info']);
        echo CommonClass::ajax_return($re, $jsonp, $jsonpcallback);
        break;
    case 'getrecord':
        $class = filter_input(INPUT_POST, 'clas');
        //$time = filter_input(INPUT_POST, 'game_time');
        $username = filter_input(INPUT_POST, 'username');
        $cl = explode("__", $class);
        $siteid = SITE_ID;
        $liveid = str_replace(array("+", "active", " "), array("", "", ""), $cl[2]);
        $gameKind = $cl[1];
        $betTimeBegin = date("Y-m-d");
        $betTimeEnd = date("Y-m-d");
        $starTime = "00:00:00";
        $endTime = "23:59:59";
        $str = $siteid . $username . $liveid . $gameKind . $betTimeBegin . $betTimeEnd . $starTime . $endTime;
        $key = "dsasf" . md5($str) . 'dserft';
        $params = array(
            'siteId' => $siteid,
            'username' => $username,
            'liveId' => $liveid,
            'gameKind' => $gameKind,
            'betTimeBegin' => $betTimeBegin,
            'betTimeEnd' => $betTimeEnd,
            'startTime' => $starTime,
            'endTime' => $endTime,
            'key' => $key
        );
//        if($username == 'hfcshuiyuan2'){
//            echo "<pre>";
//            print_r($params);
//            echo "</pre>";
//        }
        
        
        
        $result = $clientB->betTotalByUser(json_encode($params));
        $result = str_replace(array('"[', ']"', '\"', '\\\\', '"{', '}"'), array('[', ']', '"', '\\', '{', '}'), $result);
        echo $result;
        break;
    case 'getrecorddetail':
        $class = filter_input(INPUT_POST, 'clas');
        $class2 = filter_input(INPUT_POST, 'gn_clas');
        $time = filter_input(INPUT_POST, 'game_time');
        $username = filter_input(INPUT_POST, 'username');
        $page = filter_input(INPUT_POST, 'page');
        $pageLimit = filter_input(INPUT_POST, 'page_limit');

        $cl = explode("__", $class);
        $siteid = SITE_ID;
        $liveid = str_replace(array("+", "active", " "), array("", "", ""), $cl[2]);
        $gameKind = $cl[1];

        $cl2 = explode("__", $class2);
        $gameType = $cl2[1];
        if ($time == 1) {
            $betTimeBegin = date("Y-m-d");
            $betTimeEnd = date("Y-m-d");
        } else {
            $betTimeBegin = trim($gameType);
            $betTimeEnd = trim($gameType);
            $gameType = '';
        }

        $starTime = "00:00:00";
        $endTime = "23:59:59";

        $str = $siteid . $username . $liveid . $gameKind . $gameType . $betTimeBegin . $betTimeEnd . $starTime . $endTime . $page . $pageLimit;
        //echo $str;
        $key = "pdrft" . md5($str) . 'jhsefb';
        $params = array(
            'siteId' => $siteid,
            'username' => $username,
            'liveId' => $liveid,
            'gameKind' => $gameKind,
            'gameType' => $gameType,
            'betTimeBegin' => $betTimeBegin,
            'betTimeEnd' => $betTimeEnd,
            'startTime' => $starTime,
            'endTime' => $endTime,
            'page' => $page,
            'pageLimit' => $pageLimit,
            'key' => $key
        );
        if ($time == 2) {
            unset($params['gameType']);
        }
        //print_r($params);
        $result = $clientB->listDetailReport(json_encode($params));
        $res = json_decode($result,TRUE);
        if($res['returnCode'] == 900000){
            if(!empty($res['dataList'])){
                foreach($res['dataList'] as $i=>$list){
                    if(isset($list['gameType'])){
                        if($list['gameType'] == 3001){//波音牌型分解
                            $res['dataList'][$i]['carddetail'] = CommonClass::disassemble_poker_bbin($list['card']);
                            $zx = explode(",", $list['result']);
                            $res['dataList'][$i]['result'] = '庄'.$zx[0].'点,闲'.$zx[1].'点';
                        }else if($list['gameType'] == 41001){
                            $res['dataList'][$i]['liveMemberReportDetails'] = CommonClass::amount_bet($res['dataList'][$i]['liveMemberDetails']);
                            $res['dataList'][$i]['pokerList'] = CommonClass::disassemble_poker($res['dataList'][$i]['pokerListArr']);
                            $res['dataList'][$i]['bankResult'] = CommonClass::disassemble_type($res['dataList'][$i]['bankResultArr']);
                        }
                    }
                }
            }
        }
        $res['params'] = $params;
        //$result = str_replace(array('"[', ']"', '\"', '\\\\', '"{', '}"'), array('[', ']', '"', '\\', '{', '}'), $result);
        echo json_encode($res);
        break;
    case 'getrecordhistory'://历史记录，按天统计 
        $class = filter_input(INPUT_POST, 'clas');
        //$time = filter_input(INPUT_POST, 'game_time');
        $username = filter_input(INPUT_POST, 'username');
        $cl = explode("__", $class);
        $siteid = SITE_ID;
        $liveid = str_replace(array("+", "active", " "), array("", "", ""), $cl[2]);
        $gameKind = $cl[1];

        $betTimeBegin = date("Y-m-d", time() - 3600 * 24 * 8);
        $betTimeEnd = date("Y-m-d", time() - 3600 * 24);
        $starTime = "00:00:00";
        $endTime = "23:59:59";
        $str = $siteid . $username . $liveid . $gameKind . $betTimeBegin . $betTimeEnd . $starTime . $endTime;

        $key = "dsasf" . md5($str) . 'dserft';
        $params = array(
            'siteId' => $siteid,
            'username' => $username,
            'liveId' => $liveid,
            'gameKind' => $gameKind,
            'betTimeBegin' => $betTimeBegin,
            'betTimeEnd' => $betTimeEnd,
            'startTime' => $starTime,
            'endTime' => $endTime,
            'key' => $key
        );
        $result = $clientB->betTotalByDay(json_encode($params));
        $result = str_replace(array('"[', ']"', '\"', '\\\\', '"{', '}"'), array('[', ']', '"', '\\', '{', '}'), $result);
        $r = json_decode($result,TRUE);
        $r['post_arr'] = $params;
        $result = json_encode($r);
        echo $result;
        break;
    case 'getrecordtotal'://今天历史报表，按天统计 
        $username = filter_input(INPUT_POST, 'username');
        $siteid = SITE_ID;
        $istoday = filter_input(INPUT_POST, 'istoday');
        if($istoday == 1){//今日投注报表
            $betTimeBegin = date("Y-m-d");
            $betTimeEnd = date("Y-m-d");
        }else{//历史7天投注报表
            $betTimeBegin = date("Y-m-d", time() - 3600 * 24 * 7);
            $betTimeEnd = date("Y-m-d", time() - 3600 * 24);
        }
        $starTime = "00:00:00";
        $endTime = "23:59:59";
        $str = $siteid . $username . $betTimeBegin . $betTimeEnd . $starTime . $endTime;

        $key = "dsasf" . md5($str) . 'dserft';
        $params = array(
            'siteId' => $siteid,
            'username' => $username,
            'betTimeBegin' => $betTimeBegin,
            'betTimeEnd' => $betTimeEnd,
            'startTime' => $starTime,
            'endTime' => $endTime,
            'key' => $key
        );
        $result = $clientB->betTotalByDay(json_encode($params));
        $result = str_replace(array('"[', ']"', '\"', '\\\\', '"{', '}"'), array('[', ']', '"', '\\', '{', '}'), $result);
        $result = $clientA->getUserReturnWater($result,SITE_ID,$username,$istoday);
        //$result =  json_decode($result,TRUE);
        //$result['params'] = $params;
        //echo json_encode($result);
        echo $result;
        break;
    case 'getmoneycheck'://出款稽核
        if ($jsonp == 'jsonp') {  //后台远程调用
            $params['username'] = filter_input(INPUT_GET, 'username');
            $params['ulevel'] = $ulevel = filter_input(INPUT_GET, 'ulevel');
            $params['site_id'] = filter_input(INPUT_GET, 'site_id');
        } else {
            $params['username'] = filter_input(INPUT_POST, 'username');
            $params['ulevel'] = $ulevel = filter_input(INPUT_POST, 'ulevel');
            $params['site_id'] = SITE_ID;
        }
        session_start();
        unset($_SESSION['all_need_free']);
        unset($_SESSION['check_free']);
        unset($_SESSION['check_poroms_free']);
        unset($_SESSION['check_reply_free']);
        unset($_SESSION['take_money_max']);
        unset($_SESSION['take_money_min']);
        $checklist = $clientA->getCheckList($params);
        if ($checklist['code'] == $objCode->success_get_checklist->code) {
            $lastAllMoney = $clientA->getLastMoney(array('site_id'=>SITE_ID,'username'=>$params['username']));//所有平台的余额
             
            $list = json_decode($checklist['info']['list'], TRUE);
            $allfree = 0; //稽核手续费
            $alldeleteporoms = 0; //需扣除优惠金额
            $allmoney = 0; //总投注量
            if (!empty($list[0])) {
                $yuecode = 0; //有余的打码量
                //$yh_yuecode = 0; //优惠有余的打码量
                
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
                    //$result1 = $clientB->auditTotal(json_encode($data));
                    $result1 = $clientB->auditTotalTemp(json_encode($data));
                    $result2 = str_replace(array('"[', ']"', '\"', '\\\\', '"{', '}"'), array('[', ']', '"', '\\', '{', '}'), $result1);
                    $result = json_decode($result2, TRUE);
                    if ($result['returnCode'] == '900000') {
                        $list[$i]['sum_money'] = isset($result['dataList']['totalValidamount'])?$result['dataList']['totalValidamount']:0; //实际投注，总投注

                        $allmoney += $list[$i]['sum_money'];

                        $list[$i]['live_money'] = isset($result['dataList']['liveValidamount'])?$result['dataList']['liveValidamount']:0; //实际投注，视讯投注
                        $list[$i]['sports_money'] = isset($result['dataList']['sportValidamount'])?$result['dataList']['sportValidamount']:0; //实际投注，体育投注
                        $list[$i]['lotto_money'] = isset($result['dataList']['lottoValidamount'])?$result['dataList']['lottoValidamount']:0; //实际投注，彩票投注
                        $list[$i]['game_money'] = isset($result['dataList']['jilvValidamount'])?$result['dataList']['jilvValidamount']:0; //实际投注，几率投注

                        $list[$i]['normal_code'] = $v['normal_code'] = is_null($v['normal_code']) ? 0 : $v['normal_code'];//常态打码量
                        $list[$i]['promos_code'] = $v['promos_code'] = is_null($v['promos_code']) ? 0 : $v['promos_code'];//优惠打码量
                        $list[$i]['save_money'] = $v['save_money'] = is_null($v['save_money']) ? 0 : $v['save_money'];//存款金额
                        $list[$i]['promos_money'] = $v['promos_money'] = is_null($v['promos_money']) ? 0 : $v['promos_money'];//优惠金额
                        if($list[$i]['promos_money'] == 0){
                            $list[$i]['promos_code'] = 0;
                        }
                        $list[$i]['extend_code'] = $v['extend_code'] = is_null($v['extend_code']) ? 0 : $v['extend_code'];//放宽打码量

                        $list[$i]['p_is_ok'] = '<span style="color:green"> V </span>';
                        $list[$i]['p_is_m_money'] = '<span style="color:green"> 否 </span>';
                        $list[$i]['p_m_money'] = 0;

                        $list[$i]['m_is_ok'] = '<span style="color:green"> V </span>';
                        $list[$i]['m_is_m_money'] = '<span style="color:green"> 否 </span>';
                        $list[$i]['m_m_money'] = 0;
                        
                        $yuecode += $list[$i]['sum_money'];
                        //常态稽核
                        if ($v['normal_code'] > 0) {
                            if ($yuecode - $v['normal_code'] + $v['extend_code'] >= 0) {
                                $yuecode = $yuecode - $v['normal_code'] + $v['extend_code']; //有余的打码量累计到下一条稽核
                                $list[$i]['normal_check_status'] = 1; //通过常态稽核，不需扣除手续费
                                $list[$i]['normal_free'] = 0; //通过常态稽核，不需扣除手续费
                            } else {
                                $list[$i]['normal_check_status'] = -1; //没通过常态稽核，需扣除手续费
                                $allfree += $list[$i]['normal_free']; //统计常态稽核手续费
                                //$yuecode += $list[$i]['sum_money']; //有余的打码量累计到下一条稽核
                                
                                $list[$i]['m_is_ok'] = '<span style="color:red"> X </span>';
                                $list[$i]['m_is_m_money'] = '<span style="color:red"> '.$list[$i]['normal_free'].' </span>';
                                $list[$i]['m_m_money'] = $list[$i]['normal_free'];
                            }
                        } else {//不需稽核，不需扣除手续费
                            $list[$i]['normal_free'] = 0;
                            $list[$i]['normal_check_status'] = 1;
                            //$yuecode += $list[$i]['sum_money']; //有余的打码量累计到下一条稽核
                        }
                        //优惠稽核
                        if ($list[$i]['promos_code'] > 0) {//综合打码量大于0
                            $is_acoss_normal = 0;
                            if($v['normal_code'] > 0 && $list[$i]['normal_check_status'] == 1){//通过常态加上常态打码
                                $is_acoss_normal = $v['normal_code'];
                            }
                            
                            if ($yuecode + $is_acoss_normal - $v['promos_code'] >= 0) {//通过优惠稽核
                                $yuecode = $yuecode + $is_acoss_normal - $v['promos_code'];  //有余的打码量累计到下一条稽核
                                $list[$i]['poroms_check_status'] = 1; //通过优惠稽核，不需扣除优惠
                            } else {
                                if($lastAllMoney < $list[$i]['promos_ye_check_limit'] && $lastAllMoney !== FALSE){//余额优惠稽核不需消耗打码量
                                    //$yuecode = $yuecode - $v['promos_code'];  //有余的打码量累计到下一条稽核
                                    $list[$i]['poroms_check_status'] = 1; //通过优惠稽核，不需扣除优惠
                                }else{
                                    $alldeleteporoms += $list[$i]['promos_money']; //需扣除金额统计
                                    //$yuecode += $list[$i]['sum_money']; //有余的打码量累计到下一条稽核
                                    $list[$i]['poroms_check_status'] = -1;

                                    $list[$i]['p_is_ok'] = '<span style="color:red"> X </span>';
                                    $list[$i]['p_is_m_money'] = '<span style="color:red"> '.$list[$i]['promos_money'].' </span>';
                                    $list[$i]['p_m_money'] = $list[$i]['promos_money'];
                                }
                            }
                        } else {
                            $list[$i]['poroms_check_status'] = 1; //通过优惠稽核，不需扣除优惠
                            //$yuecode += $list[$i]['sum_money']; //有余的打码量累计到下一条稽核
                        }
                    } else {
                        $re = array('code' => $objCode->fail_get_checklist->code, 'error' => 'report error -_-!', 'result' => $result, 'request' => $data);
                        echo CommonClass::ajax_return($re, $jsonp, $jsonpcallback);
                        exit();
                    }
                    $list[$i]['start_time'] = date("y-m-d H:i:s", $list[$i]['start_time']);
                    $list[$i]['end_time'] = date("y-m-d H:i:s", $list[$i]['end_time']);
                }
            }
            $checklist['data']['list'] = $list;
            $checklist['data']['allfree'] = $allfree; //稽核手续费
            $checklist['data']['alldeleteporoms'] = $alldeleteporoms; //稽核扣除优惠
            $checklist['data']['allmoney'] = $allmoney; //总投注量
            $replyfree = $clientA->getReplyOutFree(array('site_id' => $params['site_id'], 'ulevel' => $ulevel, 'username' => $params['username']));
            $checklist['data']['replyhours'] = $replyfree['reply_hours'];
            $checklist['data']['replytime'] = $replyfree['reply_time'];
            $checklist['data']['take_money_max'] = $replyfree['take_money_max'];
            $checklist['data']['take_money_min'] = $replyfree['take_money_min'];
            if ($replyfree['reply_fee']) {
                $checklist['data']['replyfree'] = $replyfree['reply_fee'];
            } else {
                $checklist['data']['replyfree'] = 0;
            }
            $checklist['data']['all_free_proms_reply'] = $allfree + $alldeleteporoms + $replyfree['reply_fee'];
            //$checklist['data']['all_free_proms_reply'] = $allfree + $alldeleteporoms;
            if ($jsonp != 'jsonp') {  //后台远程调用 
                $_SESSION['all_need_free'] = $checklist['data']['all_free_proms_reply']; //总需扣除金额
                $_SESSION['check_free'] = $checklist['data']['allfree']; //常态稽核费用
                $_SESSION['check_poroms_free'] = $checklist['data']['alldeleteporoms']; //优惠稽核需扣除金额
                $_SESSION['check_reply_free'] = $checklist['data']['replyfree']; //重复出款超次数需手续费
                $_SESSION['take_money_max'] = $checklist['data']['take_money_max']; //最大出款限额
                $_SESSION['take_money_min'] = $checklist['data']['take_money_min']; //最小出款限额
            }
            $checklist['data']['check_time'] = date("Y-m-d H:i:s");
        }
        unset($checklist['info']);
        echo CommonClass::ajax_return($checklist, $jsonp, $jsonpcallback);
        
        break;
    case "getmoneytake":
        session_start();
        if (isset($_SESSION['take_money_max'])) {
            $data['need_free'] = $_SESSION['all_need_free']; //总需扣除金额
            $data['normal_free'] = $_SESSION['check_free']; //常态稽核费用
            $data['poroms_free'] = $_SESSION['check_poroms_free']; //优惠稽核需扣除金额
            $data['reply_free'] = $_SESSION['check_reply_free']; //重复出款超次数需手续费
            $data['money_max'] = $_SESSION['take_money_max']; //最大出款限额
            $data['money_min'] = $_SESSION['take_money_min']; //最小出款限额

            $params['username'] = filter_input(INPUT_POST, 'username');
            $params['oid'] = filter_input(INPUT_POST, 'oid');
            $params['site_id'] = SITE_ID;
            $datar = $clientA->getGetMoneyUserDetail($params);
            if ($datar['code'] == $objCode->success_get_getmoney_userdetial->code) {
                $info = json_decode($datar['info'], TRUE);
                $address = $info['bank_name'] . " -- " . $info['bank_adress'] . " -- " . $info['bank_account'] . " -- " . $info['realname'];
                $data['address'] = $address;
                $return = array('data' => $data, 'code' => 90000);
            } else {
                $return = array('code' => $datar['code']);
            }
        } else {
            $return = array('code' => 90001);
        }
        echo CommonClass::ajax_return($return, $jsonp, $jsonpcallback);
        break;

    case "takesubmit":
        session_start();
        if (isset($_SESSION['take_money_max'])) {
            $need_free = $_SESSION['all_need_free']; //总需扣除金额
            $normal_free = $_SESSION['check_free']; //常态稽核费用
            $poroms_free = $_SESSION['check_poroms_free']; //优惠稽核需扣除金额
            $reply_free = $_SESSION['check_reply_free']; //重复出款超次数需手续费
            $money_max = $_SESSION['take_money_max']; //最大出款限额
            $money_min = $_SESSION['take_money_min']; //最小出款限额

            $params['out_money'] = filter_input(INPUT_POST, 'money');
            $params['real_out_money'] = filter_input(INPUT_POST, 'gold');
            $params['username'] = filter_input(INPUT_POST, 'username');
            $params['oid'] = filter_input(INPUT_POST, 'oid');
            $params['ulevel'] = filter_input(INPUT_POST, 'ulevel');
            $params['agent'] = filter_input(INPUT_POST, 'agent');
            $get_password = filter_input(INPUT_POST, 'get_password');
            $params['site_id'] = SITE_ID;
            $params['promos_money'] = $poroms_free;
            $params['fee_money'] = $normal_free;
            $params['reply_free_money'] = $reply_free;
            $account = filter_input(INPUT_POST, 'account');
            $accounts = explode('--', $account);
            if (
                    empty($get_password) ||
                    $params['out_money'] > $money_max ||
                    $params['out_money'] < $money_min ||
                    $params['real_out_money'] < 0 ||
                    number_format($params['real_out_money'],2) != number_format($params['out_money'] - $need_free ,2) ||
                    !is_array($accounts)
            ) {//不符合条件的金额，非法提交的
                $return = array('code' => $objCode->fail_take_money_order->code, 'error' => 'error 0','tips'=>$params,'nf'=>$need_free);
                session_destroy(); //清掉session
                echo CommonClass::ajax_return($return, $jsonp, $jsonpcallback);
                break;
            }

            //$get_password = CommonClass::get_md5_pwd($get_password);
            $isRight = $clientA->isGetPasswordRight(array('company' => SITE_ID, 'username' => $params['username'], 'get_password' => $get_password)); //判断出款密码是否正确
            if ($isRight != 1) {//出款密码错误
                echo CommonClass::ajax_return(array('code' => $objCode->error_getpassword->code, 'pwd' => $get_password), $jsonp, $jsonpcallback);
                break;
            }
            
            $isAlow = $clientA->isAlowSaveTakeFromAgent(SITE_ID, $params['agent']); //判断允许存取款
            if ($isAlow == 2) {//不允许存取款
                echo CommonClass::ajax_return(array('code' => $objCode->is_not_allow_save->code), $jsonp, $jsonpcallback);
                break;
            }
            
            session_destroy(); //清掉session

            $params['bank_name'] = trim($accounts[0]);
            $params['province'] = trim($accounts[1]);
            $params['bank_num'] = trim($accounts[2]);
            $params['bank_user'] = trim($accounts[3]);
            $params['add_ip'] = $ip;

            //调用出款接口
            $clientC = new HproseSwooleClient(MY_TCP_MONEY_HOST); //光光  
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
            
            $res = $clientC->transMoney(json_encode($paramsb));
            $result = json_decode($res, TRUE);
            if ($result['code'] == '100000') {//出款扣金额成功
                $params['billno'] = $billno;
                $logstrs = filter_input(INPUT_POST, 'str_to_log');
//                $x = explode(';', $logstrs);
//                if(is_array($x)){
//                    foreach($x as $i=>$v){
//                        $params['logstr'.$i] = $v;
//                    }
//                }
                $params['logstr'] = $logstrs;
                $return = $clientA->addTakeMoneyOrder($params); //添加出款订单~~~~~
                if ($return['code'] == $objCode->success_take_money_order->code) {
                    $return['data'] = json_decode($return['info'], TRUE);
                    unset($return['info']);
                } else {
                    $return = array('code' => $objCode->fail_take_money_order->code, 'error' => 'error 1');
                }
            } else {//扣除金额失败
                $return = array('code' => $objCode->fail_take_money_order->code, 'error' => 'error 2');
            }
        } else {
            $return = array('code' => $objCode->fail_take_money_order->code, 'error' => 'error 3');
        }
        echo CommonClass::ajax_return($return, $jsonp, $jsonpcallback);
        break;
    default:


        break;
}
