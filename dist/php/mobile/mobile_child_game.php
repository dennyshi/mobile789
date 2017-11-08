<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/php/base/__config.php");
$t = $_REQUEST ;
$game_id = $t['game_id'];
$get_child_game_type = $clientA->get_game_type($game_id);

$get_child_game_type = json_decode ( $get_child_game_type['info'] );

$get_child_game_list = array();

if( !empty( $get_child_game_type ) ) {
    foreach ($get_child_game_type as $key => $value) {
        $get_child_game_list[$key]['id'] = $value->id;
        $get_child_game_list[$key]['game_name'] = $value->game_name;
    }
}
echo json_encode($get_child_game_list);
?>




