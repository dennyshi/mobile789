<?php
    require_once("../../php/base/__config.php");
    $data['username'] = filter_input( INPUT_POST ,'username');
    $data['site_id'] = SITE_ID;
    if( $action == "update_read_message_time" ){
        $data['time'] = time();
        $result = $clientA->update_read_time($data);
    }elseif( $action == "get_read_message_time" ){
        $result = $clientA->get_read_message_time($data);
    }

//    print_r($result);die;
    echo CommonClass::ajax_return( $result, $jsonp,$jsonpcallback);
?>


