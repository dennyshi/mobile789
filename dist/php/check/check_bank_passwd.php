<?php
require_once("../../php/base/__config.php");
    $params['username']     = filter_input(INPUT_POST, 'username');
    $params['oid']          = filter_input(INPUT_POST, 'oid');
    $params['ulevel']       = filter_input(INPUT_POST, 'ulevel');
    $params['agent']        = filter_input(INPUT_POST, 'agent');
    $result = $clientA->check_bank_passwd( $params );
echo CommonClass::ajax_return( $result[0] , $jsonp, $jsonpcallback);

?>



