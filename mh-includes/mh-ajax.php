<?php
/**
 * MH ajax
 */
add_action( 'wp_ajax_mh_delete_attachment', 'mh_delete_attachment' );

function mh_delete_attachment() {
    $action = empty($_POST['action']) ? '' : $_POST['action'];
    if($action == 'mh_delete_attachment'){
        if(get_current_user_id()){
            $attachment_id = $_POST['attachment_id'];
            $post = get_post($attachment_id);
            if($post->post_author == get_current_user_id()){
                if(wp_delete_attachment( $attachment_id )){
                    $msg = __("Deleted attahcment.",'mhboard');
                    echo json_encode(array("success"=>true, "msg"=>$msg));
                }
            }
            

        }
    }
    die();
}
?>