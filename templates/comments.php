<?php
if(function_exists('mh_socialnuri_form')):
	mh_socialnuri_form();
else:
if(isset($_POST['comment']) && isset($_POST['comment_post_ID']) && $_POST['comment_post_ID'] == get_the_ID()){
	$time = current_time('mysql');

	
	if(!is_user_logged_in()){
		$comment_author = $_POST['author'];
		$comment_author_email = $_POST['email'];
		$comment_author_url = $_POST['url'];
	}else{
		$user_id = get_current_user_id();
		$user_data = get_userdata($user_id);
		$comment_author = $user_data->display_name;
		$comment_author_email = $user_data->user_email;
		$comment_author_url = $user_data->user_url;
	}
	$data = array(
		'comment_post_ID' => get_the_ID(),
		'comment_author' => $comment_author,
		'comment_author_email' => $comment_author_email,
		'comment_author_url' => $comment_author_url,
		'comment_content' => htmlspecialchars($_POST['comment']),
		'comment_type' => '',
		'comment_parent' => 0,
		'user_id' => $user_id,
		'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
		'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
		'comment_date' => $time,
		'comment_approved' => 1,
	);
	wp_insert_comment($data);
}
global $post;
?>
<div class="mh-comment" >
	
	<h3><?php echo __('Comments','mhboard');?> : <?php echo get_comments_number();?></h3>
		<ol class="mh-commentlist">
		<?php $comments = get_comments( array( 'post_id' => get_the_ID(), 'order' => 'ASC' ));?>
		<?php mh_list_comments($comments);?>
		</ol>
	<div id="reply-form">
	<h3><?php echo __('Leave a Reply','mhboard');?></h3><small><a rel="nofollow" id="cancel-comment-reply-link" href="#respond" style="display:none;">Cancel reply</a></small>
	
	<?php if(current_user_can('administrator') && isset($post->post_password)):?>
	<form action="<?php echo site_url('wp-comments-post.php');?>" method="post">
	<?php else :?>
	<form action="<?php echo home_url('/wp-comments-post.php');?>" method="post">
	<?php endif;?>
		<input type="hidden" name="comment_post_ID" value="<?php the_ID();?>" id="comment_post_ID">
		<input type="hidden" name="comment_parent" id="comment_parent" value="0">
		<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI'];?>">
		<div class="mh-comment-item clearfix">
			<div class="comment-content">
				<?php if(!get_current_user_id()):?>
				<?php
				$commenter = wp_get_current_commenter();
				?>
				<div class="comment-form-author lrp5"><label for="author">Name</label> <span class="required">*</span><input id="author" name="author" type="text" value="<?php echo esc_attr( $commenter['comment_author'] );?>" size="30" aria-required="true"></div>
				<div class="comment-form-email lrp5"><label for="email">Email</label> <span class="required">*</span><input id="email" name="email" type="text" value="<?php echo esc_attr(  $commenter['comment_author_email'] );?>" size="30" aria-required="true"></div>
				<div class="comment-form-url lrp5"><label for="url">Website</label><input id="url" name="url" type="text" value="<?php echo esc_attr( $commenter['comment_author_url'] );?>" size="30"></div>
				<?php endif;?>
				<div class="comment-form-section">
				<div class="header">
					<?php if(get_current_user_id()):?>
					<?php echo get_avatar( get_current_user_id(), 55 ); ?>
					<?php else:?>
					<?php echo get_avatar( 0, 55 ); ?>
					<?php endif;?>
				</div>
				<div class="comment"><textarea id="comment" name="comment" aria-required="true"></textarea></div>
				<div class="form-submit">
					<input name="submit" type="submit" id="submit" class="button btn" value="<?php echo __('Add','mhboard');?>" class="button btn">
				</div>
				</div>
			</div>
		</div>
	</form>
	</div>
</div>
<?php endif;?>