<?php
$post = get_post($_GET['ID']);
$userdata = get_userdata($post->post_author);

?>
<table cellpadding="0" cellspacing="0" class="board">
	<tr>
		<td colspan="6"><?php echo $post->post_title;?></td>
	</tr>
	<tr>
		<td>글쓴이</td><td><?php echo $userdata->display_name;?></td><td>날짜</td><td><?php echo get_the_date();?></td><td>조회</td><td></td>
	</tr>
	<tr>
		<td colspan="6" class="content"><?php echo $post->post_content;?></td>
	</tr>
</table>
<div id="comments">
	<?php if ( have_comments() ) : ?>
		<header id="comments-header" class="clearfix">
			<h2 id="comments-title">
				<?php
					printf( _n( '0 Comments', '%1$s Comments', get_comments_number(), '2ne1' ),
						number_format_i18n( get_comments_number() ) );
				?>
			</h2>
			<div id="comments-login">
			<?php mh_sns_login_fomr();?>
			</div>
			
		</header>
		
		

		<?php if ( get_comment_pages_count() > 1 ) : // are there comments to navigate through ?>
		<nav id="comment-nav-above">
			<h1 class="assistive-text"><?php _e( 'Comment navigation', 'twentyeleven' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'twentyeleven' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'twentyeleven' ) ); ?></div>
		</nav>
		<?php endif; // check for comment navigation ?>

		<ol class="commentlist">
			<?php
				/* Loop through and list the comments. Tell wp_list_comments()
				 * to use twentyeleven_comment() to format the comments.
				 * If you want to overload this in a child theme then you can
				 * define twentyeleven_comment() and that will be used instead.
				 * See twentyeleven_comment() in twentyeleven/functions.php for more.
				 */
				wp_list_comments( );
			?>
		</ol>
		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below">
			<h1 class="assistive-text"><?php _e( 'Comment navigation', 'twentyeleven' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'twentyeleven' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'twentyeleven' ) ); ?></div>
		</nav>
		<?php endif; // check for comment navigation ?>

	<?php
		/* If there are no comments and comments are closed, let's leave a little note, shall we?
		 * But we don't want the note on pages or post types that do not support comments.
		 */
		elseif ( ! comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="nocomments"><?php _e( 'Comments are closed.', 'twentyeleven' ); ?></p>
	<?php else:?>
		<header id="comments-header" class="clearfix">
			<h2 id="comments-title">
				<?php
					printf( _n( '0 Comments', '%1$s Comments', get_comments_number(), '2ne1' ),
						number_format_i18n( get_comments_number() ) );
				?>
			</h2>
			<div id="comments-login">
			
			</div>
		</header>
		<ol class="commentlist">
			<?php
				/* Loop through and list the comments. Tell wp_list_comments()
				 * to use twentyeleven_comment() to format the comments.
				 * If you want to overload this in a child theme then you can
				 * define twentyeleven_comment() and that will be used instead.
				 * See twentyeleven_comment() in twentyeleven/functions.php for more.
				 */
				wp_list_comments( );
			?>
		</ol>
	<?php endif; ?>

	<?php 
		$refer_type = 'local';
						
		if($temp = get_user_meta(get_current_user_id(),'wsl_user')){
			$refer_type = $temp[0];
			unset($temp);
		}
		
		$avatar_size = 50;
		if ( '0' != $comment->comment_parent )
			$avatar_size = 39;
		if($refer_type == 'local'){
			$img =  get_avatar( $comment, $avatar_size );
		}else{
			$user_photo = get_user_meta(get_current_user_id(),'wsl_user_image');
			
			$img =  "<img alt=\"\" src=\"{$user_photo[0]}\" class=\"avatar avatar-{$avatar_size} photo avatar-default\" height=\"{$avatar_size}\" width=\"{$avatar_size}\">";
		}

		$twitter = '0';
		$facebook = '0';
		if($_SESSION['MH::TWITTER']['hauth_session.Twitter.token.request_token'] && $_SESSION['MH::TWITTER']['hauth_session.Twitter.token.request_token_secret']){
			$twitter = '1';
		}
		if($_SESSION['MH::FACEBOOK']['hauth_session.Facebook.token.access_token']){
			$facebook = '1';
		}
		$twitter = '1';
		$facebook = '1';
		$input_twitter_hidden = '<input type="hidden" name="twitter_on" value="'.$twitter.'"/>';
		$input_facebook_hidden = '<input type="hidden" name="facebook_on" value="'.$facebook.'"/>';
		$comments_args = array(
		'post_id'=>get_the_ID(),
        // change the title of send button 
        'title_reply'=>'',
        // remove "Text or HTML to be displayed after the set of comment fields"
        'comment_notes_after' => '',
        // redefine your own textarea (the comment body)
        'comment_field' => '<div class="comment-form-comment">'.$img.$input_twitter_hidden.$input_facebook_hidden.'<div class="comment_arrow"></div><div class="comment_box"><textarea id="comment" name="comment" aria-required="true"></textarea></div><div></div></div>',
        'label_submit' => 'Submit'
);
comment_form($comments_args); ?>
</div>