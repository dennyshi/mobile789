<?php
ini_set("display_errors", "on");
$debug = true;
require_once("../base/__config.php");
$username = filter_input(INPUT_POST, 'name');
$params = array('mobile' => $username, 'company' => SITE_ID, 'ip' => $ip);
$re = $clientA->checkMobile($params);
if($re['code'] == 201009){
    echo 'true';
}else{
    echo 'false';
}

