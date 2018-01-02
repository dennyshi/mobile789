<?php
ini_set("display_errors", "on");
$debug = true;
require_once("../base/__config.php");
$agent_id = filter_input(INPUT_POST, 'agent_numb');
$params = array('agent_id' => $agent_id, 'site_id' => SITE_ID);
//print_r($params);
$re = $clientA->check_agent_id($params);
$res = json_decode($re,true);
if($res['code'] == "100000"){
    echo 'true';
}else{
    echo 'false';
}

