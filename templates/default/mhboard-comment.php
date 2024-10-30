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
<div class="mh-comment">
	<div id="respond">
	<h3><?php comment_form_title( __('Leave a comment' ,'mhboard'), __('Leave a comment  to %s' ,'mhboard') ); ?></h3>
	<div id="cancel-comment-reply">
	<small><?php cancel_comment_reply_link() ?></small></div>
	<?php if(current_user_can('administrator') && isset($post->post_password)):?>
	<form action="<?php echo home_url('/wp-comments-post.php');?>" method="post">
	<?php else :?>
	<form action="<?php echo home_url('/wp-comments-post.php');?>" method="post">
	<?php endif;?>
	<?php comment_id_fields(); ?>
		<div class="mh-comment-item">
			<div class="comment-content">
				<?php if(!get_current_user_id()):?>
				<?php
				$commenter = wp_get_current_commenter();
				?>
				<p class="comment-form-author"><label for="author"><?php echo __('Name','mhboard');?></label> <span class="required">*</span><input id="author" name="author" type="text" value="<?php echo esc_attr( $commenter['comment_author'] );?>" size="30" aria-required="true"></p>
				<p class="comment-form-email"><label for="email"><?php echo __('Email','mhboard');?></label> <span class="required">*</span><input id="email" name="email" type="text" value="<?php echo esc_attr(  $commenter['comment_author_email'] );?>" size="30" aria-required="true"></p>
				<p class="comment-form-url"><label for="url"><?php echo  __('Website','mhboard');?></label><input id="url" name="url" type="text" value="<?php echo esc_attr( $commenter['comment_author_url'] );?>" size="30"></p>
				<?php endif;?>
				<div class="header">
					<?php if(get_current_user_id()):?>
					<?php echo get_avatar( get_current_user_id(), 55 ); ?>
					<?php else:?>
					<?php echo get_avatar( true, 55 ); ?>
					<?php endif;?>
				</div>
				<p><textarea id="comment" name="comment" aria-required="true"></textarea></p>
				<p class="form-submit clearfix">
					<input name="submit" type="submit" id="submit" value="<?php echo __('Add' ,'mhboard');?>" class="button btn">
					<input type="hidden" name="comment_post_ID" value="<?php the_ID();?>" id="comment_post_ID">
				</p>
			</div>
		</div>
	</form>
	</div>
	<h3><?php echo __('Comments:' ,'mhboard');?><?php echo get_comments_number();?></h3>
		<?php $comments = get_comments( array( 'post_id' => get_the_ID(), 'order' => 'ASC' ));?>
		<?php
			$top_level = array();
			$children_level = array();
			for($i = 0; $i < sizeof($comments); $i++){
				if($comments[$i]->comment_parent == 0){
					$top_level[] = $comments[$i];
				}else{
					$children_level[$comments[$i]->comment_parent][] = $comments[$i];
				}
			}
			unset($comments);
		?>
		<?php foreach($top_level as $comment):?>
			<div class="mh-comment-item" <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
				<div class="comment-content">
					<div class="header">
						<small><?php echo date('Y.m.d G:i:s',strtotime($comment->comment_date));?></small>
						<?php echo get_avatar( $comment, 55 ); ?>
						<strong><?php echo $comment->comment_author;?> </strong>
						<span><?php comment_reply_link(array('reply_text '=>__('Reply','mhboard'),'depth'=>1,'max_depth'=>2,'respond_id'=>'respond'),$comment->comment_ID,get_the_ID());?></span>
					</div>
					<p><?php echo nl2br($comment->comment_content);?></p>
				</div>
				<?php if($children_level[$comment->comment_ID]):?>
				<?php foreach(@$children_level[$comment->comment_ID] as $comment):?>
					<div class="mh-comment-item" <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
						<div class="comment-content">
							<div class="header">
								<small><?php echo date('Y.m.d G:i:s',strtotime($comment->comment_date));?></small>
								<?php echo get_avatar( $comment, 55 ); ?>
								<strong><?php echo $comment->comment_author;?> </strong>
								<span><?php comment_reply_link(array('reply_text '=>__('Reply','mhboard'),'depth'=>0,'max_depth'=>2,'respond_id'=>'respond'),$comment->comment_ID,get_the_ID());?></span>
							</div>
							<p><?php echo nl2br($comment->comment_content);?></p>
						</div>
					</div>
				<?php endforeach;?>
				<?php endif;?>
			</div>
		<?php endforeach;?>
		
		<?php wp_list_comments(  ); ?> 
</div>
<?php endif;?>