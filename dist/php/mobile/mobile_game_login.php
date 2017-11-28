<?php
ini_set("display_errors", "on");
require_once("../base/__config.php");
$tmp = $_REQUEST;
// print_r($tmp);die;
//$tmp = $_GET;
//print_r($t);
//print_r($tmp);
$params['username'] = $_COOKIE['username'];
$params['company'] = SITE_ID;
$params['oid'] = $_COOKIE['oid'];
$params['isMobile'] = "1";
$params['code'] = (string)$tmp['code'];
$action = $tmp['action'];
$isMobile = $params['isMobile'];
$params['domain'] = $_SERVER['HTTP_HOST'];
//print_r($action);
// print_r($params);die;
//echo 222;
//echo "<br>";


if( stripos( $params['username'], 'shiwan' ) == false  ){
    $re0 = $clientA->isLoginUpload($params);
//    echo 3333333;
//    print_r($params);
//    echo "<br>";
//    print_r($re0);
    if (empty($params['oid']) || empty($params['username']) || empty($params['company']) || $re0['code'] != $objCode->is_login_status->code) {//用户已经登出，或异常
//        print_r($params);
//        echo 5555555;
//        print_r($re0);
        echo "<script>alert('您还没登录，请先登录！'); self.close();</script>";
        return false;
//        die();
    }
}

$get_protect_status = $clientA->getProtectStatus(SITE_ID, $action);
if( $get_protect_status['info']['status'] == 2 ){
    echo "<script>alert('游戏正在维护，敬请期待！'); self.close();</script>";
//    $code = 113057 ;        //游戏正在维护
    return false;
}
//echo 7777;
$lives = [
    'ag' => 2,
    'bb' => 11,
    'gg' => 12,
    'mg' => 13,
    'pt' => 14,
    'sa' => 15,
    'cf' => 16,
    'ww' => 17,
    'nn' => 18,
    'main' => 0,

    'changemoney' => 0
];
$f = new Fetch();
$keyp = CommonClass::get_key_param($params['username'] . GAME_PASSWORD . GAME_KEYB . date("Ymd"), 6, 9);
$ac = substr($action, 0, 2);
$paramsp = array('siteId' => SITE_ID, 'username' => $params['username'], 'live' => $lives[$ac], 'password' => GAME_PASSWORD, 'isDemo' => CommonClass::is_test_user($params['username']), 'key' => $keyp,'isMobile'=>'1','loginUrl'=>$params['domain']);
//echo 88888;die;
switch ($action) {
    case 'ag_live':
        $paramsp['gameType'] = 0;
        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
        if(strstr($r, 'http')){
            header("Location: $r");
        }
        break;
    case 'ag_game':
        $paramsp['gameType'] = 8;
        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
        if(strstr($r, 'http')){
            header("Location: $r");
        }
        break;
    case 'ag_fish':
        $paramsp['gameType'] = 6;
        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
        if(strstr($r, 'http')){
            header("Location: $r");
        }
        break;
    case 'bb_live':
        $paramsp['page_site'] = 'live';
//        print_r($paramsp);
        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
        print_r($r);
//        if(strstr($r, 'http')){
//            header("Location: $r");
//        }
        break;
    case 'bb_game':
        $paramsp['page_site'] = 'game';
        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
        print_r($r);
//        if(strstr($r, 'http')){
//            header("Location: $r");
//        }
        break;
    case 'bb_sport':
        $paramsp['page_site'] = 'ball';
        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
        print_r($r);
//        if(strstr($r, 'http')){
//            header("Location: $r");
//        }
        break;
    case 'bb_lotto':
        $paramsp['page_site'] = 'Ltlottery';
        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
//        if(strstr($r, 'http')){
//            header("Location: $r");
//        }
        print_r($r);
        break;
    case 'pt_live':
    case 'pt_game':
    case 'pt_fish':
        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
        $res = json_decode($r, TRUE);
        $pt_username = $res['username'];
        $pt_password= $res['password'];

//    echo "<script>location.href='".PT_GAME_HALL."/index.php/home/index/index/first/".$pt_username."/second/".$pt_password."';</script>";
        echo "<script>location.href='/lobby.html?first=".$pt_username."&second=".$pt_password."';</script>";
        break;
    case 'mg_live':
        $paramsp['gameType'] = "mg_live";
        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
        if(strstr($r, 'http')){
            header("Location: $r");
        }
    break;
    case 'mg_game':
        $paramsp['gameType'] = "mg_game";
        $paramsp['gameCode'] = $params['code'];
        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
        if(strstr($r, 'http')){
            header("Location: $r");
        }
//        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
////        print_r($paramsp);die;
//        if( $paramsp['live'] == '13' ){
//            echo "<script>location.href='".PT_GAME_HALL."/index.php/home/index/mg_login/siteId/".$paramsp['siteId']."/password/".$paramsp['password']."/username/".$paramsp['username']."/isDemo/".$paramsp['isDemo']."/key/".$paramsp['key']."/isMobile/".$isMobile."';</script>";
//            exit;
//            return false;
//        }
        break;
    case 'gg_game':
//        print_r($paramsp);
        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
        if(strstr($r, 'http')){
            header("Location: $r");
        }
        break;
    case 'gg_fish':
        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
        if(strstr($r, 'http')){
            header("Location: $r");
        }
        break;
    case 'sa_live':
        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
        print_r($r);
        break;
    case 'sa_game':
        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
        if(strstr($r, 'http')){
            print_r($r);
        }
        break;
    case 'sa_lotto':
        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
        if(strstr($r, 'http')){
            print_r($r);
        }
        break;
    case 'nn':
        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
        if(!empty($r)){
            $url = NN_LOTTO_DOMAIN.$r;
            header("Location: $url");
        }else{
            $result['params'] = $paramsp;
        }
        break;
    case 'cf_sport':
        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
        if(strstr($r, 'http')){
            header("Location: $r");
        }
        break;
    case 'ww_sport':
        $r = $f->NewPostData(PINGTAI_URL . 'login', $paramsp);
        if(strstr($r, 'http')){
            header("Location: $r");
        }
        break;
}
return false;
