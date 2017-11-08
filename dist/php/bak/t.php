<?php

ini_set('display_errors', 'on');
require_once("config.php");
require_once("Fetch.class.php");


$AG_PREFIX = AG_PREFIX;
$BBIN_PREFIX = BBIN_PREFIX;
$DS_PREFIX = DS_PREFIX;
$H8_PREFIX = H8_PREFIX;

$AG_HASHCODE = AG_HASHCODE;
$BBIN_HASHCODE = BBIN_HASHCODE;
$DS_HASHCODE = DS_HASHCODE;
$H8_HASHCODE = H8_HASHCODE;
$params['username'] = 'ceshihongfa2';
try {
    $f = new Fetch(MY_HTTP_MONEY_HOST . 'getMoney');
    $key = CommonClass::get_key_param(SITE_WEB . $params['username'], 5, 6);
    $paramsb = array('fromKey' => SITE_WEB, 'siteId' => SITE_ID, 'username' => $params['username'], 'key' => $key);
    $res = $f->CheckUsrBalance($paramsb);
    $result = json_decode($res, TRUE);
    echo "main:<br />";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
} catch (Exception $exc) {
    echo "main";
    echo $exc->getTraceAsString();
}


try {
    unset($f);
    $f = new Fetch(PINGTAI_URL);
    $p = array(
        'username' => $AG_PREFIX . $params['username'],
        'password' => AG_PASSWORD,
        'hashcode' => $AG_HASHCODE,
        'keyb' => AG_KEYB,
        'live' => AG_LIVE_TYPE,
        'key' => CommonClass::get_key_param($AG_PREFIX . $params['username'] . AG_PASSWORD . AG_KEYB . date("Ymd"), 7, 3)
    );
    $f->debug = TRUE;
    $data = $f->CheckUsrBalance($p);
    $re = json_decode($data, TRUE);
    echo "ag:<br />";
    echo "<pre>";
    echo "_______________________________________________<br />";
    print_r($p);
    echo "_______________________________________________<br />";
    print_r($re);
    echo "</pre>";
    echo "******************************************<br /><br /><br /><br />";
} catch (Exception $exc) {
    echo "ag";
    echo $exc->getTraceAsString();
}




try {
    unset($f);
    $f = new Fetch(PINGTAI_URL);
    $p = array(
        'username' => $BBIN_PREFIX . $params['username'],
        'password' => BBIN_PASSWORD,
        'hashcode' => $BBIN_HASHCODE,
        'keyb' => BBIN_KEYB,
        'live' => BBIN_LIVE_TYPE,
        'key' => CommonClass::get_key_param($BBIN_PREFIX . $params['username'] . BBIN_PASSWORD . BBIN_KEYB . date("Ymd"), 7, 3)
    );
    $f->debug = TRUE;
    $data = $f->CheckUsrBalance($p);
    $re = json_decode($data, TRUE);
    echo "bbin:<br />";
    echo "<pre>";
    echo "_______________________________________________<br />";
    print_r($p);
    echo "_______________________________________________<br />";
    print_r($re);
    echo "</pre>";

    echo "******************************************<br /><br /><br /><br />";
} catch (Exception $exc) {
    echo "bbin";
    echo $exc->getTraceAsString();
}

try {
    unset($f);
    $f = new Fetch(PINGTAI_URL);
    $p = array(
        'username' => $H8_PREFIX . $params['username'],
        'password' => H8_PASSWORD,
        'hashcode' => $H8_HASHCODE,
        'keyb' => H8_KEYB,
        'live' => H8_LIVE_TYPE,
        'key' => CommonClass::get_key_param($H8_PREFIX . $params['username'] . H8_PASSWORD . H8_KEYB . date("Ymd"), 7, 3)
    );
    $f->debug = TRUE;
    $data = $f->CheckUsrBalance($p);

    $re = json_decode($data, TRUE);
    echo "h8:<br />";
    echo "<pre>";
    echo "_______________________________________________<br />";
    print_r($p);
    echo "_______________________________________________<br />";
    print_r($re);
    echo "</pre>";
} catch (Exception $exc) {
    echo "h8";
    echo $exc->getTraceAsString();    
}

//$sqlmessage = "insert into user_message (content,message_title,datetime,user_id) value ('撸尔山 永久地址 xx.com','撸尔山 永久地址',".time().",$user_id)";
//$this->db->query($sqlmessage);
