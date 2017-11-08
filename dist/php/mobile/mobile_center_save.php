<?php
require_once("../base/__config.php");
$t = $_REQUEST ;
$params['username'] = $t['username'];
$params['oid'] =  $t['oid'];
$params['site_id'] = SITE_ID;
$re = $clientA->getUserDetails($params);
$data = $re['info'];
echo json_encode($data);

?>
