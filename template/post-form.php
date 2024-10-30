<script type="text/javascript">
/* <![CDATA[ */
	jQuery(document).ready(function($) {
		$('#post_format').val($('#post-types a.selected').attr('id'));
		$('#post-types a').click(function(e) {
			$('.post-input').hide();
			$('#post-types a').removeClass('selected');
			$(this).addClass('selected');
			if ($(this).attr('id') == 'post') {
				$('#posttitle').val("<?php echo esc_js( __('Post Title', 'p2') ); ?>");
			} else {
				$('#posttitle').val('');
			}
			$('#postbox-type-' + $(this).attr('id')).show();
			$('#post_format').val($(this).attr('id'));
			return false;
		});
		$('#post_open').click(function(e){
			$('#post_password').css('display','none')
		});
		$('#post_close').click(function(e){
			$('#post_password').css('display','block')
		});
	});
/* ]]> */
</script>
<?php
	if($_REQUEST['action'] == 'post'){
		$user           = wp_get_current_user();
		$user_id        = $user->ID;
		$tags           = trim( $_POST['post_tag'] );
		$post_title		= $_POST['post_title'];
		$post_content	= $_POST['post_content'];
		$post_id = wp_insert_post( array(
			'post_author'   => $user_id,
			'post_title'    => $post_title,
			'post_content'  => $post_content,
			'post_type'     => 'board',
			'tags_input'    => $tags,
			'post_status'   => 'publish'
		) );
		if($post_id){
			wp_set_object_terms( $post_id, array(intval($_POST['board-cat'])), 'board-cat' );
			$redirect_uri = $_SERVER['REDIRECT_URL'];
			$category = get_term(intval($_POST['board-cat']),'board-cat');
			echo "<script type='text/javascript'>location.href='{$redirect_uri}?board-cat={$category->slug}';</script>";
		}
		
	}
	$categories = get_terms('board-cat',array('orderby'=>'id','order'=>'ASC','hide_empty'=>0));

?>
<div id="postbox">
	<form action="" method="post">
		<div>
			<label for="category">카테고리</label>
			<select name="board-cat">
				<?php
				foreach($categories as $category){
					?>
					<option value="<?php echo $category->term_id;?>"><?php echo $category->name;?></option>
					<?php
				} 
				?>
			</select>
		</div>
		<div><label for="title">제목</label><input type="text" name="post_title"></div>
		<textarea class="expand70-200" name="post_content" id="post_content" tabindex="1" rows="4" cols="60"></textarea>
		<div><label for="tag">태그</label><input type="text" name="post_tag"></div>
		<div><label for="open">공개설정</label><input type="radio" name="post_open" id="post_open" value="1" checked>전체공개<input type="radio" name="post_open" id="post_close" value="0">비공개</div>
		<div id="post_password" style="display:none;"><label for="password">비밀번호</label><input type="password" name="post_password"></div>
		<div><input type="submit" value="글쓰기"></div>
		<input type="hidden" name="action" value="post" />
		<?php wp_nonce_field( 'new-post' ); ?>
	</form>
</div>