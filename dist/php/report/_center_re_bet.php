<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "/php/base/__config.php");
    $get_live_game = $clientA->get_live_type();
    if( $get_live_game['code'] == $objCode->success_get_game_live_type->code ){
//        $get_live_game = json_decode($get_live_game);
        $live_game_info = json_decode ( $get_live_game['info'] );
    }
    $live_game_list = array();
    foreach( $live_game_info as $key => $value ){
        $live_game_list[$key]['id']         = $value->id ;
        $live_game_list[$key]['live_name']  = $value->live_name ;
    }
?>
        <?php foreach( $live_game_list as $key => $value ){?>
            <option value="<?php echo $value['id']  ;?>"><?php echo $value['live_name'];?></option>
        <?php }?>


