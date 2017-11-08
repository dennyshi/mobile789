<?php
session_start();
header("content-Type: text/html; charset=utf-8");
require_once("config.php");
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
    echo "<script>alert('验证码错误！');self.close();</script>";
    exit();
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
    echo "<script>alert('参数错误，请稍后再试！');self.close();</script>";
    exit();
}

$re = $clientA->isLoginUpload($params);
if ($re['code'] != $objCode->is_login_status->code) {//用户已经登出，或异常
    echo "<script>alert('你已登出！');self.close();</script>";
    exit();
}

$limit = $clientA->getSaveLimit(array('site_id' => SITE_ID, 'ulevel' => $params['ulevel']));
if (!empty($limit) && $params['money'] < $limit['online']['limit_min']) {
    echo "<script>alert('存款金额低于最低存款限制(¥{$limit['online']['limit_min']})！');self.close();</script>";
    exit();
}
if (!empty($limit) && $params['money'] > $limit['online']['limit_max']) {
    echo "<script>alert('存款金额高于最高存款限制(¥{$limit['online']['limit_max']})！');self.close();</script>";
    exit();
}
//print_r($params);
$re = $clientA->saveOnLineOrder($params);
if ($re['code'] == $objCode->success_save_online->code) {//在线存款底单提交成功
    //echo $re['info'];
    if(SAVE_TEST){
        $re['info'] = $re['info']."&debug=true";
    }
    header("Location: {$re['info']}");
}else if($re['code'] == $objCode->is_not_allow_save->code){
    echo "<script>alert('{$objCode->is_not_allow_save->message}');self.close();</script>";
}