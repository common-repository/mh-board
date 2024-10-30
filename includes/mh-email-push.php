<?php
add_action( 'wp_insert_comment', 'mh_board_email_push' );
function mh_board_email_push(){
	global $post;
	
	if($post->post_author > 0){
		$user_info = get_userdata($post->post_author);
		$author_email = $user_info->user_email;
	}else{
		$author = get_post_meta($post->ID,'guest_info',true);
		$author_email = $author['guest_email'];
	}
	$mh_board_options = get_option('mh_board_options');
	$emailpush = @$mh_board_options['emailpush'];
	$short_link = get_site_url()."/?p=".$post->ID;
	unset($mh_board_options);
	if($emailpush == 'push' && $post->post_type == 'board'){
		add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
		$title = "귀하의 게시물에 새로운 댓글이 등록되었습니다.";
		$content = "<p>귀하의 게시물에 새로운 댓글이 등록되었습니다. <br/></br> 댓글 확인하러 가기 : {$short_link}</p>";
		wp_mail($author_email, $title, $content);
	}
		
	
}
?>