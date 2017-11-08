<?php
ini_set("display_errors", "on");
$debug = true;
require_once("../base/__config.php");
$username = filter_input(INPUT_POST, 'name');
$params = array('username' => $username, 'company' => SITE_ID, 'ip' => $ip);
$re = $clientA->checkUser($params);
if($re['code'] == 201005){
    echo 'true';
}else{
    echo 'false';
}

