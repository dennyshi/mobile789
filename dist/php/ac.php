<?php

//$tmp = file_get_contents('php://input') ;
//print_r($tmp);
ini_set("display_errors", "on");
$debug = true;
require_once("base/__config.php");
$t = $_REQUEST;
if( empty($t) ){
    $t = $_POST;
}
//$username = filter_input(INPUT_GET, 'username');
//print_r($username);die;
switch ($action) {
    case 'register'://会员注册

        if (CAN_REGISTER == 4 || (CAN_REGISTER == 2 && SITE_TYPE == 1) || (CAN_REGISTER == 3 && SITE_TYPE == 2)) {
            $re = array('fail_to_reg_not_notallow' => $objCode->fail_to_reg->code);
            break;
        }

        $username = $t['username'];
        $mobile = $t['phone'];
        $password = $t['password'];
        $agent = $t['agent'];
        $url = $t['url'];


        // $username = strtolower(filter_input(INPUT_POST, 'username'));
        // $mobile = filter_input(INPUT_POST, 'phone');
        // $password = filter_input(INPUT_POST, 'password');
        // $url = filter_input(INPUT_POST, 'url');//获取url,用来判断是否有代理或者上级会员
        $result = explode("?",$url);
        foreach( $result as $key => $value ){
            if( stripos(  $value, "intr"  )  !== false ){
                $intr = $value ;
            }
            if( stripos( $value , "sp"  )  !== false ){
                $sp = $value ;

            }
        }
        if(  !empty( $intr )  ){
            $intr = explode( "=" , $intr );
            $intr = $intr[1];
        }
        if(  !empty( $sp )  ){
            $sp = explode( "=" , $sp );
            $sp = $sp[1];
        }

        $params['url'] = $url;
        $truename = $username;
        $passwd = $password;
        $agree = 1;

        if (
                empty($username) ||
                empty($truename) ||
                empty($mobile) ||
                empty($password) ||
                empty($passwd) ||
                empty($agree) ||
                $password != $passwd
        ) {
            $re = array('code' => $objCode->fail_to_reg->code,'msg'=>"注册失败,信息输入不能为空");
            break;
        }
        if( !CommonClass::check_username_str($username, 6, 12) ){
            $re = array('code' => $objCode->fail_to_reg->code,'msg'=>"注册失败,用户名必须为6到12位");
            break;
        }
        if( !CommonClass::check_password_str($password, 6, 12) ){
            $re = array('code' => $objCode->fail_to_reg->code,'msg'=>"注册失败,密码必须为6到12位");
            break;
        }
        if( !CommonClass::check_mobile_str($mobile) ){
            $re = array('code' => $objCode->fail_to_reg->code,'msg'=>"注册失败,手机号码输入不符合要求");
            break;
        }

        //验证用户名存在
        $data1 = array('username' => $username, 'company' => SITE_ID, 'ip' => $ip);
        $re1 = $clientA->checkUser($data1);
        if ($re1['code'] == $objCode->is_have_username->code) {
            $re = array('code' => $objCode->fail_to_reg->code,'msg'=>'注册失败,用户名称已经存在');
            break;
        }
        //验证真实姓名存在
        $data2 = array('realname' => $truename, 'company' => SITE_ID, 'ip' => $ip);
        $re2 = $clientA->checkTrueName($data2);
        if ($re2['code'] == $objCode->is_have_truename->code) {
            $re = array('code' => $objCode->fail_to_reg->code);
            break;
        }
        //验证手机号码存在
        $data3 = array('mobile' => $mobile, 'company' => SITE_ID, 'ip' => $ip);
        $re3 = $clientA->checkMobile($data3);
        if ($re3['code'] == $objCode->is_have_mobile->code) {
            $re = array('code' => $objCode->fail_to_reg->code,'msg'=>'注册失败,手机号码已注册');
            break;
        }

        $pwd = CommonClass::get_md5_pwd($password);
        $params = array(
            'username' => $username,
            'userpass' => $pwd,
            'phone' => $mobile,
            'mobile' => $mobile,
            'realname' => $truename,
            'company' => SITE_ID,
            'loginip' => $ip,
            'last_ip' => $ip,
            'ip' => $ip,
            'url'=> $url,
            'site_type' => SITE_TYPE//主副站修改
        );
        if (!empty($intr)) {
            $params['agid'] = $intr;
        }
        //彩票手机端注册推广码
        if (!empty($agent)) {
            $params['agid'] = $agent;
        }
        if (!empty($sp)) {
            $params['sp'] = $sp;
        }
        if (!empty($email)) {
            $params['email'] = $email;
        }
        if (!empty($qq)) {
            $params['qq'] = $qq;
        }
        $params['host'] = $host;
//        print_r($params);die;
        $re = $clientA->userRegister($params);
        if ($re['code'] == $objCode->success_to_reg_and_login->code) {
            $re['data'] = json_decode($re['info'], TRUE);
            $re['data']['last_login_time'] = date("y-m-d H:i:s");
            $re['data']['supers'] = $re['data']['super'];
            $v_k = 'VIP' . $re['data']['uvip'];
            $re['data']['uvipname'] = $config['api_config'][$v_k];
            unset($re['data']['super']);

            $pa['siteId'] = SITE_ID;
            $pa['username'] = $username;
            $pa['agents'] = $re['data']['agent'];
            $pa['world'] = $re['data']['world'];
            $pa['corprator'] = $re['data']['corprator'];
            $pa['superior'] = $re['data']['supers'];
            $pa['company'] = 'admin';

            $qianzhui = rand(100,999);
            $houzhui  = rand(1000,9999);
            $key = md5($pa['username'].$pa['siteId']);
            $pa['key'] = $qianzhui.$key.$houzhui;

            $demo_user = array('1' => '11', '2' => '21');
            $site_type = SITE_TYPE; //主副站修改
            $f = new Fetch();
            if ($re['data']['user_type'] == $demo_user[$site_type]) {//测试会员给钱包中心提交会员信息
                $result = $f->NewPostData(MY_HTTP_MONEY_HOST_TEST . 'setMemberData', $pa);
                $result = $f->NewPostData(MY_HTTP_CENTER_HOST . 'setMemberData', json_encode($pa), 2);
            } else {
                $result = $f->NewPostData(MY_HTTP_MONEY_HOST . 'setMemberData', $pa); //正式会员给钱包中心提交会员信息
                $result = $f->NewPostData(MY_HTTP_CENTER_HOST . 'setMemberData', json_encode($pa), 2); //正式会员给报表中心提交会员信息
            }
            $re['param'] = $pa;
            $re['result'] = $result;
        } else {
            $re['code'] = $objCode->fail_to_reg->code;
            $re['msg'] = "注册失败";
            $re['data'] = json_decode($re['info'], TRUE);
        }
        break;
    case 'login'://登录
        $username = $t['username'];
        if (empty($username)) {
            $re['code'] = $objCode->fail_to_login_error->code;
            $re['data'] = $t;
            $re['tips'] = 'username empty';
            break;
        }
        $password = $t['password'];
        // $password = filter_input(INPUT_POST, 'password');
        if (empty($password)) {
            $re['code'] = $objCode->fail_to_login_error->code;
            $re['tips'] = 'password empty';
            break;
        }
        if ($username == -1) {//试玩
            $re = $clientA->testPlayLogin(array('company' => SITE_ID, 'ip' => $ip, 'user_type' => SITE_TYPE)); //主副站修改
            $re['tips'] = 'cao empty';
        } else {
            $pwd = CommonClass::get_md5_pwd($password);
            $vcode = filter_input(INPUT_POST, 'varcode');
            //页面没有输入验证码的地方顾此处不需要验证 所以改为0 否则改为1
            if (NEED_CODE == 0) {//需要验证码
                session_start();
                if ($vcode != (isset($_SESSION['vcode_session']) ? $_SESSION['vcode_session'] : null)) {
                    $re['code'] = $objCode->fail_to_login_error_code->code;
                    $re['tips'] = 'NEED_CODE empty';
                    break;
                }
            }
            $params = array('username' => $username, 'company' => SITE_ID, 'userpass' => $pwd, 'ip' => $ip, 'user_type' => SITE_TYPE,'hul_is_mobile'=>1); //主副站修改
            $os = CommonClass::get_user_os();
            $params['hul_mobile_type'] = $os;
            $params['hul_domain'] = $_SERVER['HTTP_HOST'];

//            $params = array('username' => 'aabb5566', 'company' => SITE_ID, 'userpass' => 'bbaa5566', 'ip' => $ip, 'user_type' => SITE_TYPE); //主副站修改
//             echo json_encode($params);die;
            $re = $clientA->login($params);
        }
        if ($re['code'] == $objCode->success_to_login->code) {

            include_once 'base/clientGetObj.php';
            $os = new clientGetObj();
            $par['site_id'] = SITE_ID;
            $par['username'] = $username;
            $par['os'] = $os->getOS();
            $par['browse'] = $os->getBrowse();
            $par['ip'] = $ip;
            $par['addtime'] = time();
            $clientA->recordClient($par);

            $re['data'] = json_decode($re['info'], TRUE);
            $re['data']['last_login_time'] = date("y-m-d H:i:s");
            $re['data']['supers'] = $re['data']['super'];
            $v_k = 'VIP' . $re['data']['uvip'];
            $re['data']['uvipname'] = $config['api_config'][$v_k];

            setcookie('username',$username);
            setcookie( 'oid', $re['data']['oid'] );
            unset($re['data']['super']);
        }
        break;
    case 'getnotice'://获取公告信息
        $params['company'] = SITE_ID;
        $params['note_type'] = $t['note_type'];
        $re = $clientA->getNotice($params);
        if ($re['code'] == $objCode->success_get_notice->code) {
            $re['data'] = array('notice' => $re['info']);
        }
        break;
    case 'getnoticehistory'://获取历史公告信息
        $params['company'] = SITE_ID;
        $params['note_type'] = filter_input(INPUT_POST, 'note_type');
        $re = $clientA->getNoticeHistory($params);
        if ($re['code'] == $objCode->success_get_history_notice->code) {
            $re['data'] = array('notice' => json_decode($re['info']));
        }
        break;
    case 'getcountmessage'://获取短信息未读条数
        $params['company'] = SITE_ID;
        $params['username'] = strtolower(filter_input(INPUT_POST, 'username'));
        $re['count'] = $clientA->getCountMessage($params);
        break;
    case 'logout'://登出
        $username = strtolower(filter_input(INPUT_POST, 'username'));
        $params = array('username' => $username, 'company' => SITE_ID, 'ip' => $ip);
        $re = $clientA->logout($params);
        break;
    case "getbanks"://获取银行列表
        $re = $clientA->getAllBankInfo();
        if ($re['code'] == $objCode->success_get_banks->code) {
            $re['data'] = array('banks' => json_decode($re['info']));
        }
        break;
    case "protect"://获取维护状态
        $tag = filter_input(INPUT_POST, 'tag');
        $re = $clientA->getProtectStatus(SITE_ID, $tag);
        $allregister = 1; //允许注册
        if (CAN_REGISTER == 4 || (CAN_REGISTER == 2 && SITE_TYPE == 1) || (CAN_REGISTER == 3 && SITE_TYPE == 2)) {
            $allregister = 2; //关闭注册功能
        }
        if ($re['code'] == $objCode->success_get_weihu->code) {
            $re['data'] = array('protect' => $re['info'], 'vcode' => NEED_CODE, 'allregister' => $allregister);
        }
        break;
    case 'changepassword'://修改密码
        $username = strtolower($t['username']);
        $ypassword = $t['ypassword'];
        $password = $t['password'];
        $oid =  $t['oid'];
        $user_type =$t['user_type']; //主副站修改
        $pwd = CommonClass::get_md5_pwd($ypassword);
        $newpwd = CommonClass::get_md5_pwd($password);
        $params = array('username' => $username, 'userpass' => $pwd, 'newuserpass' => $newpwd, 'company' => SITE_ID, 'oid' => $oid, 'ip' => $ip, 'user_type' => $user_type); //主副站修改
        $re = $clientA->updatePassword($params);
        break;
    case 'changegetpassword'://修改出款密码
        $username = strtolower($t['username']);
        $ypassword = $t['ypassword'];
        $password = $t['password'];
        $oid = $t['oid'];
        $user_type = $t['user_type']; //主副站修改
        $newpwd = CommonClass::get_md5_pwd($password);
        $params = array('username' => $username,'ypassword' => $ypassword, 'newuserpass' => $newpwd, 'company' => SITE_ID, 'oid' => $oid, 'ip' => $ip, 'user_type' => $user_type); //主副站修改
        $re = $clientA->update_get_password($params);
        break;
    case 'getbankset'://获取收款银行信息
        $params['username'] = filter_input(INPUT_POST, 'username');
        $params['oid'] = filter_input(INPUT_POST, 'oid');
        $params['ulevel'] = filter_input(INPUT_POST, 'ulevel');
        $params['site_id'] = SITE_ID;
        $re = $clientA->getBanksByUserLevel($params);
        if ($re['code'] == $objCode->success_get_bank_set->code) {
            $re['data'] = array('bankset' => json_decode($re['info']));
        }
        break;
    case 'getbillno'://获取收款银行信息
        $params['username'] = filter_input(INPUT_POST, 'username');
        $params['oid'] = filter_input(INPUT_POST, 'oid');
        $params['site_id'] = SITE_ID;
        $re = $clientA->getBillno($params);
        if ($re['code'] == $objCode->success_get_billno->code) {
            $re['data'] = array('billno' => $re['info']);
        }
        break;
    case 'getgetmoneyuserdetail'://获取收款银行信息
        $params['username'] = filter_input(INPUT_POST, 'username');
        $params['oid'] = filter_input(INPUT_POST, 'oid');
        $params['site_id'] = SITE_ID;
        $agent = filter_input(INPUT_POST, 'agent');
        $isAlow = $clientA->isAlowSaveTakeFromAgent(SITE_ID, $agent); //判断允许存取款
        if ($isAlow == 2) {//不允许存取款
            echo CommonClass::ajax_return(array('code' => $objCode->is_not_allow_save->code), $jsonp, $jsonpcallback);
            exit();
        }

        $re = $clientA->getGetMoneyUserDetail($params);
        $re['data'] = json_decode($re['info']);
        break;
    case 'savebankorder'://提交存款订单
        $ulevel = filter_input(INPUT_GET, 'ulevel');
        $params['username'] = strtolower(filter_input(INPUT_GET, 'username'));
        $params['site_id'] = SITE_ID;
        $params['ip'] = $ip;
        $params['bank_set_id'] = filter_input(INPUT_GET, 'in_bank_set_id'); //收款人配置id
        $params['bank_id'] = filter_input(INPUT_GET, 'out_bank_id');   //存款人银行id
        $params['card_user'] = filter_input(INPUT_GET, 'user');        //存款人真实姓名
        $params['pay_type'] = filter_input(INPUT_GET, 'type');         //支付类型
        $params['pay_ip'] = $ip;
        $params['money'] = filter_input(INPUT_GET, 'money');
        $params['mark'] = filter_input(INPUT_GET, 'mark');
        $params['billno'] = CommonClass::get_billno(SITE_ID,$params['username']);
        $params['oid'] = filter_input(INPUT_GET, 'oid');
//        $re = $params;break;
        if (
            empty($params['billno']) ||
            empty($params['card_user']) ||
            empty($params['money']) ||
            empty($params['pay_type']) ||
            empty($params['bank_id']) ||
            empty($params['bank_set_id']) ||
            empty($params['mark']) ||
            $params['money'] < 0 ||
            !CommonClass::check_money($params['money'])
        ) {
            $re = array('code' => $objCode->fail_save_onbank->code);
            break;
        }
        $data['username']   = $params['username'];
        $data['money']      = $params['money'];
        $data['site_id']    = SITE_ID;
        $result = $clientA->is_again_recharge($data);
        if( $result['code'] == $objCode->post_bill_copy->code  ){
            $re = $result;
            break;
        }

        $limit = $clientA->getSaveLimit(array('site_id' => SITE_ID, 'ulevel' => $ulevel));
        if (!empty($limit) && $params['money'] < $limit['bank']['limit_min']) {
            $re = array('code' => $objCode->fail_save_less_than_limit->code, 'data' => array('bank' => $limit['bank']));
            break;
        }
        if (!empty($limit) && $params['money'] > $limit['bank']['limit_max']) {
            $re = array('code' => $objCode->fail_save_more_than_limit->code, 'data' => array('bank' => $limit['bank']));
            break;
        }
        $re = $clientA->saveBankOrder(json_encode($params));
        break;
    case 'getprize'://获取奖金信息
        $params['site_id'] = SITE_ID;
        $params['username'] = strtolower(filter_input(INPUT_POST, 'username'));
        $params['oid'] = filter_input(INPUT_POST, 'oid');
        $re = $clientA->getBonusList($params);
        if ($re['code'] == $objCode->get_prizes->code) {
            $datap = json_decode($re['info'], TRUE);
            //$re['data'] = array('prizes' => json_decode($re['info']));
            if ($datap) {
                $etime = time();
                foreach ($datap as $i => $v) {
                    $datap[$i]['add_time'] = date("Y-m-d H:i:s", $v['add_time']);
                    $datap[$i]['end_time'] = date("Y-m-d H:i:s", $v['end_time']);
                    if ($v['status'] < 3) {
                        if ($v['end_time'] < time()) {
                            $datap[$i]['status'] = 4;
                            $clientA->changeBonusStatus(array('id' => $v['id'], 'status' => 4));
                            break;
                        }
                        $clientB = new HproseSwooleClient(MY_TCP_CENTER_HOST); //小温
                        $str = SITE_ID . $v['username'] . date("Y-m-d", $v['add_time']) . date("Y-m-d", $etime) . date("H:i:s", $v['add_time']) . date("H:i:s", $etime);
                        $data = array(
                            'siteId' => SITE_ID,
                            'username' => $v['username'],
                            'betTimeBegin' => date("Y-m-d", $v['add_time']),
                            'betTimeEnd' => date("Y-m-d", $etime),
                            'startTime' => date("H:i:s", $v['add_time']),
                            'endTime' => date("H:i:s", $etime),
                            'key' => CommonClass::get_key_param($str, 5, 6)
                        );
                        //$result1 = $clientB->auditTotal(json_encode($data));
                        $result1 = $clientB->auditTotalTemp(json_encode($data));
                        $result2 = str_replace(array('"[', ']"', '\"', '\\\\', '"{', '}"'), array('[', ']', '"', '\\', '{', '}'), $result1);
                        $result = json_decode($result2, TRUE);
                        if ($result['returnCode'] == '900000') {
                            $usecodes = $clientA->getBonusUsecodes(array('site_id' => SITE_ID, 'username' => $v['username'], 'add_time' => $v['add_time'])); //统计已使用的打码量
                            $sum_money = isset($result['dataList']['totalValidamount']) ? $result['dataList']['totalValidamount'] : 0; //实际投注，总投注
                            if ($v['bonus_money_code'] <= ($sum_money - $usecodes)) {//达到打码量
                                if ($v['status'] == 1) {//从冻结（未激活）转态转为解冻状态（激活）
                                    $clientA->changeBonusStatus(array('id' => $v['id'], 'status' => 2));
                                    $datap[$i]['status'] = 2;
                                }
                            }
                        }
                    }
                }
            }
            $re['data']['prizes'] = $datap;
        }
        break;
    case 'getprizesout':
        $params['site_id'] = SITE_ID;
        $params['username'] = strtolower(filter_input(INPUT_POST, 'username'));
        $params['oid'] = filter_input(INPUT_POST, 'oid');
        $res = $clientA->isLoginUpload($params);
        if ($res['code'] != $objCode->is_login_status->code) {//用户已经登出，或异常
            unset($res['info']);
            echo CommonClass::ajax_return($res, $jsonp, $jsonpcallback);
            return false;
        }
        $session = json_decode($res['info'], TRUE);


        $id = filter_input(INPUT_POST, 'id');
        $one = $clientA->getBonusOne($id);
        if ($one['code'] == $objCode->get_prizes->code) {
            $par = json_decode($one['info'], TRUE);

            $etime = time();
            $clientB = new HproseSwooleClient(MY_TCP_CENTER_HOST); //
            $str = SITE_ID . $par['username'] . date("Y-m-d", $par['add_time']) . date("Y-m-d", $etime) . date("H:i:s", $par['add_time']) . date("H:i:s", $etime);
            $data = array(
                'siteId' => SITE_ID,
                'username' => $par['username'],
                'betTimeBegin' => date("Y-m-d", $par['add_time']),
                'betTimeEnd' => date("Y-m-d", $etime),
                'startTime' => date("H:i:s", $par['add_time']),
                'endTime' => date("H:i:s", $etime),
                'key' => CommonClass::get_key_param($str, 5, 6)
            );
            $result1 = $clientB->auditTotal(json_encode($data));
            $result2 = str_replace(array('"[', ']"', '\"', '\\\\', '"{', '}"'), array('[', ']', '"', '\\', '{', '}'), $result1);
            $result = json_decode($result2, TRUE);
            if ($result['returnCode'] == '900000') {
                $usecodes = $clientA->getBonusUsecodes(array('site_id' => SITE_ID, 'username' => $par['username'], 'add_time' => $par['add_time'])); //统计已使用的打码量

                $sum_money = is_null($result['dataList']['totalValidamount']) ? 0 : $result['dataList']['totalValidamount']; //实际投注，总投注
                $par['can_use_codes'] = $sum_money - $usecodes;
                $par['sum_codes'] = $sum_money;
                if ($par['bonus_money_code'] <= ($sum_money - $usecodes)) {//达到打码量
                    if (substr($session['user_type'], -1) == 1) {//主副站修改
                        $clientC = new HproseSwooleClient(MY_TCP_MONEY_HOST_TEST); //光光
                    } else {
                        $clientC = new HproseSwooleClient(MY_TCP_MONEY_HOST); //光光
                    }
                    $key = CommonClass::get_key_param(SITE_WEB . $par['username'] . 'prize' . $par['id'], 5, 6);
                    $paramsb = array(
                        'fromKey' => SITE_WEB,
                        'siteId' => SITE_ID,
                        'username' => $par['username'],
                        'remitno' => 'prize' . $par['id'],
                        'remit' => $par['bonus_money'],
                        'transType' => 'in',
                        'fromKeyType' => '10047',
                        'key' => $key,
                        'memo' => '会员提出奖金'
                    );
                    $res = $clientC->transMoney(json_encode($paramsb));
                    $rkresult = json_decode($res, TRUE);
                    if ($rkresult['code'] != '100000') {//存款添加失败
                        $re = array("code" => $objCode->fail_take_bonus->code, 'data' => '存款失败', $rkresult); //失败
                    } else {
                        $re = $clientA->takeBonusOut($par['id']);
                    }
                } else {
                    $re = array("code" => 5, 'sum_codes' => $sum_money, 'can_use_codes' => $par['can_use_codes'], 'result' => $result, 'params' => $data);
                }
            } else {//失败
                $re = array("code" => $objCode->fail_take_bonus->code, 'data' => '打码量查询失败', $result);
            }
        } else {//失败
            $re = array("code" => $objCode->fail_take_bonus->code, 'ID错误');
        }
        break;
    case 'saveuserdetial'://绑定银行卡

        $params['realname'] = $t['truename'];
        $params['site_id'] = SITE_ID;
        $params['username'] = strtolower($t['username']);
        $params['oid'] = $t['oid'];
        $params['bank_id'] = $t['bank_id'];
        $params['bank_address'] = $t['bank_address'];
        $params['bank_account'] = $t['bank_account'];
        $params['bank_password'] = $t['bank_password'];
//        echo json_encode($params);die;break;

        if (
                empty($params['bank_id']) ||
                empty($params['bank_address']) ||
                empty($params['bank_account']) ||
                empty($params['bank_password']) ||
                (!CommonClass::check_password_str($params['bank_password'], 4, 12) && $params['bank_password'] != '******')
        ) {
            $re = array('code' => $objCode->fail_connect_bank->code, 'data' => 'not enigth');
            break;
        }
        if ($params['bank_password'] == '******') {
            unset($params['bank_password']);
        } else {
            $params['bank_password'] = CommonClass::get_md5_pwd($params['bank_password']);
        }
        $re = $clientA->connectBankAccount($params);
        break;
    case 'changeuserdetail'://更改会员基本资料
        $params['site_id'] = SITE_ID;
        $params['username'] = strtolower($t['username']);
        $params['oid'] = $t['oid'];
        $params['ty'] = $t['ty'];
        $params['chgvalue'] =$t['chgvalue'];
        $re = $clientA->changeUserDetails($params);
        break;
    case 'getsaveonlinebanks'://获取线上存款供选择银行列表
        $params['site_id'] = SITE_ID;
        $params['username'] = strtolower(filter_input(INPUT_POST, 'username'));
        $params['oid'] = filter_input(INPUT_POST, 'oid');
        $params['ulevel'] = filter_input(INPUT_POST, 'ulevel');

        $re = $clientA->getSaveOnlineConfig($params);
        if ($re['code'] == $objCode->success_get_saveonline_banks->code) {
            $banks = json_decode($re['info']);
            $re['data'] = array('banks' => $banks);
        }
        break;
    case 'submitsaveonline'://线上存款提交订单
        session_start();
        $params['site_id'] = SITE_ID;
        $params['username'] = strtolower(filter_input(INPUT_POST, 'username'));
        $params['oid'] = filter_input(INPUT_POST, 'oid');
        $params['ulevel'] = filter_input(INPUT_POST, 'ulevel');
        $params['money'] = filter_input(INPUT_POST, 'money');
        $params['bank_code'] = filter_input(INPUT_POST, 'bank_id');
        $params['vcode'] = filter_input(INPUT_POST, 'vcode');
        $params['add_time'] = time();
        $params['user_ip'] = $ip;

        if ($_SESSION['vcode_session'] != $params['vcode'] || empty($params['vcode'])) {//验证码错误
            $re['code'] = $objCode->error_vcode->code;
            break;
        }
        unset($_SESSION['vcode_session']);
        session_destroy();
        //信息不全，提交订单失败
        if (
                empty($params['bank_code']) ||
                empty($params['money']) ||
                empty($params['username']) ||
                empty($params['oid']) ||
                empty($params['ulevel']) ||
                !CommonClass::check_money($params['money']) ||
                $params['money'] <= 0
        ) {
            $re = array('code' => $objCode->fail_save_online->code);
            break;
        }

        $limit = $clientA->getSaveLimit(array('site_id' => SITE_ID, 'ulevel' => $params['ulevel']));
        if (!empty($limit) && $params['money'] < $limit['online']['limit_min']) {
            $re = array('code' => $objCode->fail_save_less_than_limit->code, 'data' => array('online' => $limit['online']));
            break;
        }
        if (!empty($limit) && $params['money'] > $limit['online']['limit_max']) {
            $re = array('code' => $objCode->fail_save_more_than_limit->code, 'data' => array('online' => $limit['online']));
            break;
        }

        $re = $clientA->saveOnLineOrder($params);
        if ($re['code'] == $objCode->success_save_online->code) {//在线存款底单提交成功
            //跳入第三方支付接口
            $re['data'] = array('gurl' => $re['info']);
        }
        break;
    case 'dsgame':
        $gt = filter_input(INPUT_POST, 'gt');
        $pt = filter_input(INPUT_POST, 'pt');
        $pt = empty($pt) ? 1 : $pt;
        $re = $clientA->getDSGame($gt, $pt);
        if ($re) {
            $re = json_decode($re, TRUE);
            if (isset($re['total'])) {
                $re['code'] = 1;
            } else {
                $re['code'] = 2;
            }
        } else {
            $re['code'] = 3;
        }
        break;
    /**     * 图片站部分******************************************** */
    case 'getlogo'://获取logo
        $re = $clientA->imgSearch(IMG_SITE_ID, 'logo');
        if ($re['code'] == $objCode->success_get_img_con->code) {//获取图片站信息成功
            $re['data'] = json_decode($re['info']);
        }
        break;
    case 'getrotate'://获取轮播图
        $re = $clientA->imgSearch(IMG_SITE_ID, 'rotate');
        if ($re['code'] == $objCode->success_get_img_con->code) {//获取图片站信息成功
            $re['data'] = json_decode($re['info']);
        }
        break;
    case 'getpromotion'://获取优惠活动信息
        $re = $clientA->imgSearch(IMG_SITE_ID, 'promotion');
        if ($re['code'] == $objCode->success_get_img_con->code) {//获取图片站信息成功
            $re['data'] = json_decode($re['info']);
        }
        break;
    case 'getnewstag'://获取关于我们等文案所有标题
        $re = $clientA->imgSearch(IMG_SITE_ID, 'newstag');
        if ($re['code'] == $objCode->success_get_img_con->code) {//获取图片站信息成功
            $re['data'] = json_decode($re['info']);
        }
        break;
    case 'getnews'://获取关于我们等文案
        $tag = filter_input(INPUT_POST, 'tag');
        if (!$tag) {
            $tag = filter_input(INPUT_GET, 'tag');
        }
        $re = $clientA->imgSearch(IMG_SITE_ID, 'news', $tag);
        if ($re['code'] == $objCode->success_get_img_con->code) {//获取图片站信息成功
            $re['data'] = json_decode($re['info']);
        }
        break;
    case 'getpopnotice'://获取弹跳公告
        $re = $clientA->imgSearch(IMG_SITE_ID, 'notice');
        if ($re['code'] == $objCode->success_get_img_con->code) {//在线存款底单提交成功
            $re['data'] = json_decode($re['info']);
        }
        break;

    /**     * 限额部分******************************************** */
    case 'getlimittypeone'://获取限额平台
        $re = $clientA->getLimitTypeOne();
        if ($re['code'] == $objCode->success_get_limit->code) {
            $re['data'] = json_decode($re['info']);
        }
        break;
    case 'getlimittypetwo'://获取限额大类
        $type_one_id = filter_input(INPUT_POST, 'type_one_id');
        $re = $clientA->getLimitTypeTwo($type_one_id);
        if ($re['code'] == $objCode->success_get_limit->code) {
            $re['data'] = json_decode($re['info']);
        }
        break;
    case 'getlimitdata'://获取限额信息
        $type_two_id = filter_input(INPUT_POST, 'type_two_id');
        $re = $clientA->getLimitData($type_two_id, SITE_ID);
        if ($re['code'] == $objCode->success_get_limit->code) {
            $re['data'] = json_decode($re['info']);
        }
        break;

    /*     * *完善资料部分******************************************** */
    case 'getuserdetail':
        $params['username'] = filter_input(INPUT_POST, 'username');
        $params['oid'] = filter_input(INPUT_POST, 'oid');
        $params['site_id'] = SITE_ID;
        $re = $clientA->getUserDetails($params);
        $re['data'] = json_decode($re['info'], TRUE);
        break;
    case 'updateuserdetail':
        $data['realname'] = filter_input(INPUT_POST, 'realname');
        $data['oid'] = filter_input(INPUT_POST, 'oid');
        $data['register_address'] = filter_input(INPUT_POST, 'register_address');
        $data['email'] = filter_input(INPUT_POST, 'email');
        $data['qq'] = filter_input(INPUT_POST, 'qq');
        $data['wechat'] = filter_input(INPUT_POST, 'wechat');
        $data['date_of_birth'] = strtotime(filter_input(INPUT_POST, 'date_of_birth'));
        $data['bank_account'] = filter_input(INPUT_POST, 'bank_account');
        if (!empty($data['realname'])) {
            $params['realname'] = $data['realname'];
        }
        if (!empty($data['oid'])) {
            $params['oid'] = $data['oid'];
        }
        if (!empty($data['email'])) {
            $params['email'] = $data['email'];
        }
        if (!empty($data['qq'])) {
            $params['qq'] = $data['qq'];
        }
        if (!empty($data['wechat'])) {
            $params['wechat'] = $data['wechat'];
        }
        if (!empty($data['bank_address'])) {
            $params['bank_address'] = $data['bank_address'];
        }
        if (!empty($data['bank_account'])) {
            $params['bank_account'] = $data['bank_account'];
        }
        if (!empty($data['mobile'])) {
            $params['mobile'] = $data['mobile'];
        }
        $params['site_id'] = SITE_ID;
        $params['username'] = filter_input(INPUT_POST, 'username');
        $re = $clientA->updateUserDetails($params);
        break;
    //推广红利部分
    case 'getspreadpromos':
        $params['username'] = filter_input(INPUT_POST, 'username');
        $params['oid'] = filter_input(INPUT_POST, 'oid');
        $params['site_id'] = SITE_ID;
        $params['page'] = filter_input(INPUT_POST, 'page');
        $params['count'] = filter_input(INPUT_POST, 'count');
        $params['start'] = ($params['page'] - 1) * $params['count'];
        $params['start_time'] = filter_input(INPUT_POST, 'start_time');
        $params['end_time'] = filter_input(INPUT_POST, 'end_time');
        if (empty($params['start_time'])) {
            $params['start_time'] = 0;
        } else {
            $params['start_time'] = strtotime($params['start_time']);
        }
        if (empty($params['end_time'])) {
            $params['end_time'] = time();
        } else {
            $params['end_time'] = strtotime($params['end_time']);
        }
        $re = $clientA->getSpreadPromos($params);
        if ($re['code'] == $objCode->success_get_spread_promos->code) {
            $re['data'] = json_decode($re['info'], TRUE);
        }
        break;

    default:
        session_start();
        echo $_SESSION['test'];
        exit();
        try {
            $params = array('site_id' => 1, 'ulevel' => 1, 'ip' => $ip, 'money' => 1000, 'username' => 'ceshi997', 'pay_type' => 'online');
            $re = $clientA->addUserCheck($params);
            echo "<pre>";
            print_r($re);
            echo "</pre>";
        } catch (Exception $e) {
            echo "<pre>";
            print_r($e);
            echo "</pre>";
        }
        break;
}
echo CommonClass::ajax_return($re, $jsonp, $jsonpcallback);
?>
