<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "/php/base/__config.php");
    $game_id = filter_input(INPUT_GET, 'game_id');
    $get_child_game_type = $clientA->get_game_type($game_id);
//    print_r($get_child_game_type);die;
    $get_child_game_type = json_decode ( $get_child_game_type['info'] );
//    print_r($get_child_game_type);die;
    $get_child_game_list = array();
if( !empty( $get_child_game_type ) ) {
    foreach ($get_child_game_type as $key => $value) {
        $get_child_game_list[$key]['id'] = $value->id;
        $get_child_game_list[$key]['game_name'] = $value->game_name;
    }
}
?>
<?php if( !empty( $get_child_game_list ) ) {
    foreach ($get_child_game_list as $key => $value) {
        ?>
        <option value="<?php echo $value['id']; ?>"><?php echo $value['game_name']; ?></option>
        <?php
    }
}else{ ?>
    <option value="0">暂无</option>
<?php } ?>


