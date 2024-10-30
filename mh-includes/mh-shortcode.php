<?php
function mh_board($atts){
	global $mh_board,$paged,$mh_query,$post,$mh_board_link,$board_cat;
	global $mh_board_options;

	$mh_board->board_type = 'shortcode';
	$mh_board_options = get_option('mh_board_options');
	$mh_board_link = get_permalink();
	if(isset($_GET['page_id'])){
		$mh_board_link .= '&';
	}else{
		$mh_board_link .= '';
	}
	extract(shortcode_atts(array(
      'board_cat' => '',
	 ), $atts));
	if($board_cat == '' && isset($_GET['board_cat'])){
		$board_cat = $_GET['board_cat'];
	}
	$category = get_term_by('name',$board_cat,'board_cat');
	if(!$category){
		$category = get_term_by('slug',$board_cat,'board_cat');
	}
	if(is_object($category)){
		$board_cat = $category->slug;
	}
	$type = get_mh_board_type();
	
	if(!$type){
		$type = 'list';
	}
	if(!$paged){
		$paged = empty($_GET['page']) ? '1' : $_GET['page'];
	}
	$board_cat = empty($_GET['board_cat']) ? $board_cat : $_GET['board_cat'];
	wp_reset_query();
	wp_reset_postdata();
	ob_start();
	if(file_exists(STYLESHEETPATH . '/mh_board/mh_board_'.$board_cat.'_'.$type.'.php')){
		require_once(STYLESHEETPATH . '/mh_board/mh_board_'.$board_cat.'_'.$type.'.php');
	}else if(file_exists(STYLESHEETPATH . '/mh_board/mh_board_'.$type.'.php')){
		require_once(STYLESHEETPATH . '/mh_board/mh_board_'.$type.'.php');
	}else{
		require_once($mh_board->plugin_dir.'/templates/core/mh_board_'.$type.'.php');
	}
		
	$html = ob_get_contents();
	ob_end_clean();
	wp_reset_query();
	return $html;
}
function mh_board_edit(){
	$msg = '';
	if(@$_REQUEST['action'] == 'update' && wp_verify_nonce($_REQUEST['_mh_board_nonce'],'mh_board_nonce')){
		$mh_board_update = new MH_Update_Post();
		$user_id        = get_current_user_id() ? get_current_user_id() : 0;
		$tags           = trim( $_POST['post_tag'] );
		$post_title		= $_POST['post_title'];
		$post_content	= $_POST['post_content'];
		$mh_board_update->post_data = array(
			'ID'			=> $_POST['post_id'],
			'post_author'   => $user_id,
			'post_title'    => $post_title,
			'post_content'  => $post_content,
			'post_type'     => 'board',
			'tags_input'    => $tags,
			'post_status'   => 'publish'
		);
		$author = get_post_meta($_POST['post_id'],'guest_info',true);
		$update = false;
		if($author){
			if($_POST['guest_password'] && $_POST['guest_password'] == $author['guest_password']){
				$update = true;
			}else{
				$update = false;
				$msg = "비밀번호를 확인해주세요.";
			}
		}else if(get_current_user_id()){
			$update = true;
		}else{
			$update = true;
		}
		
		if($_POST['post_open'] == 0 && $_POST['post_password']){
			$mh_board_update->post_data['post_password'] = $_POST['post_password'];
		}
		$mh_board_update->post_term = array(
			'terms' => array(intval($_POST['board_cat'])),
			'taxonomy' => 'board_cat'
		);
		$term = get_term_by('id',$_POST['board_cat'],'board_cat');
		if($update){
			if($mh_board_update->update_post()){
				echo "<script type='text/javascript'>location.href='".get_post_type_archive_link('board')."';</script>";
			}else{
				
			}
		}
	}else if(@$_REQUEST['action'] == 'delete' && wp_verify_nonce($_REQUEST['_mh_board_nonce'],'mh_board_nonce')){
		$post_id = $_POST['post_id'];
		$post = get_post($post_id);
		$guest_info = get_post_meta($post_id,'guest_info',true);
		if((get_current_user_id() == $post->post_author && get_current_user_id() > 0) || (isset($_POST['guest_password']) && $_POST['guest_password'] == $guest_info['guest_password'])){
			$args = array(
				'ID' => $post_id,
				'post_status'   => 'trash'
			);
			if(wp_update_post($args)){
				echo "<script type='text/javascript'>location.href='".get_post_type_archive_link('board')."';</script>";
			}
		}else{
			$msg = "비밀번호를 확인해주세요.";
		}
	}
	$categories = get_terms('board_cat',array('orderby'=>'id','order'=>'ASC','hide_empty'=>0));
?>
<script type="text/javascript">
/* <![CDATA[ */
	jQuery(document).ready(function($) {
		$('#post_open').click(function(e){
			$('#post_password').css('display','none')
		});
		$('#post_close').click(function(e){
			$('#post_password').css('display','block')
		});
	});
/* ]]> */
</script>
<div id="mh-board-write" class="content " class="clearfix">
	<?php if($msg):?>
		<div id="board-error" class="board-error"><?php echo $msg;?></div>
	<?php endif;?>
	<?php if(@$_REQUEST['edit_type'] == 'guest' && $_REQUEST['action'] == 'delete' && wp_verify_nonce($_REQUEST['_mh_board_nonce'],'mh_board_nonce')):?>
		<div id="popup">
			<form action="" method="post" id="delete_board"><input type="hidden" name="post_id" value="<?php echo $_POST['post_id'];?>"/>
				<?php if(function_exists('wp_nonce_field'))	wp_nonce_field('mh_board_nonce','_mh_board_nonce');?>
			<h5>삭제 비밀번호</h5>
			<input type="hidden" name="action" value="delete"/>
			<input type="hidden" name="edit_type" value="guest"/>
			<input type="password" name="guest_password" id="guest_password"/>
			<input type="submit" value="삭제"/>
			</form>
		</div>
	<?php else:?>
	<?php
	
	$args= array (
		'p' => $_POST['post_id'],
		'post_type' => array('board')
	);
	$wp_query = new WP_Query($args);?>
	<?php if ( $wp_query->have_posts()  && wp_verify_nonce($_REQUEST['_mh_board_nonce'],'mh_board_nonce')) : ?>
		<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
			<?php $category = wp_get_object_terms(get_the_ID(),'board_cat');?>
			<?php
			$author = get_post_meta(get_the_ID(),'guest_info',true);
			?>
	<form action="" method="post">
		<input type="hidden" name="post_id" value="<?php the_ID();?>">
		<?php if(function_exists('wp_nonce_field'))	wp_nonce_field('mh_board_nonce','_mh_board_nonce');?>
		<table cellpadding="0" cellspacing="0">
			<?php if(sizeof($categories) > 0):?>
			<tr>
				<th>카테고리</th><td><select name="board_cat">
				<?php
				foreach($categories as $category){
					?>
					<option value="<?php echo $category->term_id;?>"><?php echo $category->name;?></option>
					<?php
				} 
				?>
			</select></td>
			</tr>
			<?php endif;//카테고리?>
			<tr>
				<th>제목</th><td><input type="text" name="post_title" class="post_title" tabindex="1" value="<?php the_title(); ?>"></td>
			</tr>
			<tr>
				<th>내용</th><td><?php wp_editor(get_the_content(), 'post_content');?></td>
			</tr>
			<tr>
			<?php
				$tags = get_the_tags();
				$tagvalue = '';
				if($tags){
				
				foreach($tags as $tag){
					$tagvalue .= $tag->name.",";
				}
				$tagvalue = substr($tagvalue,0,strlen($tagvalue)-1);
				}

			?>
				<th>태그</th><td><input type="text" name="post_tag" tabindex="3" value="<?php echo $tagvalue;?>">*콤마(,)로 구분해주세요.</td>
			</tr>
			<tr>
				<th>공개여부</th><td><input type="radio" name="post_open" id="post_open" value="1" checked>전체공개<input type="radio" name="post_open" id="post_close" value="0">비공개</div>
		<div id="post_password" style="display:none;"><label for="password">비밀번호</label><input type="password" name="post_password"></td>
			</tr>
			<?php if($author):?>
			<tr>
				<th>수정비밀번호</th><td><input type="password" name="guest_password"></td>
			</tr>
			<?php endif;?>
		</table>
		<div class="copyright">
		<a href="http://ssamture.net"><img src="http://ssamture.net/ssamturenet.png" border="0"/></a>
	</div>
		<div class="action clearfix">
			<input type="submit" value="수정" class="button">
			<input type="hidden" name="action" value="update" />
		</div>		
	</form>
		<?php endwhile; ?>
	<?php endif;?>
	<?php endif;?>
</div>
<?php	
}
function mh_board_write(){
	
	if(@$_REQUEST['action'] == 'post' && wp_verify_nonce($_REQUEST['_mh_board_nonce'],'mh_board_nonce')){
		$mh_board_write = new MH_Register_Post();
		$user_id        = get_current_user_id() ? get_current_user_id() : 0;
		$tags           = trim( $_POST['post_tag'] );
		$post_title		= $_POST['post_title'];
		$post_content	= $_POST['post_content'];
		$mh_board_write->post_data = array(
			'post_author'   => $user_id,
			'post_title'    => $post_title,
			'post_content'  => $post_content,
			'post_type'     => 'board',
			'tags_input'    => $tags,
			'post_status'   => 'publish',
			'comment_status' => 'open',
		);
		if(isset($_POST['post_parent']) && $_POST['post_parent'] > 0){
			$mh_board_write->post_data['post_parent'] = $_POST['post_parent'];
		}

		if(isset($_POST['post_tag'])){
			$mh_board_write->post_data['tags_input'] = $_POST['post_tag'];
		}
		if($_POST['post_open'] == 0 && $_POST['post_password']){
			$mh_board_write->post_data['post_password'] = $_POST['post_password'];
		}
		if($user_id == 0 && $_POST['guest_name'] && $_POST['guest_email'] && $_POST['guest_password']){
			$guest_info = array(
				'guest_name' => $_POST['guest_name'],
				'guest_email' => $_POST['guest_email'],
				'guest_password' => $_POST['guest_password'],
				'guest_site' => $_POST['guest_site']
			);
			$mh_board_write->post_meta = array(
				'guest_info' => $guest_info
			);
		}
		
		$mh_board_write->post_term = array(
			'terms' => array(intval($_POST['board_cat'])),
			'taxonomy' => 'board_cat'
		);
		$mh_board_write->post_meta['mh_board_notice'] = (int)0;
		$term = get_term_by('id',$_POST['board_cat'],'board_cat');
		if($pid = $mh_board_write->register_post()){
			if($_FILES){
				foreach($_FILES as $file => $array){
					$newupload = mh_board_insert_attachment($file,$pid);
				}
			}
			if(@$_POST['redirect_to']){
				echo "<script type='text/javascript'>location.href='".$_POST['redirect_to']."';</script>";
			}else{
				echo "<script type='text/javascript'>location.href='".get_post_type_archive_link('board')."';</script>";
			}
		}else{
			
		}
	}
	$categories = get_terms('board_cat',array('orderby'=>'id','order'=>'ASC','hide_empty'=>0));
?>
<script type="text/javascript">
/* <![CDATA[ */
	jQuery(document).ready(function($) {
		$('#post_open').click(function(e){
			$('#post_password').css('display','none')
		});
		$('#post_close').click(function(e){
			$('#post_password').css('display','block')
		});
	});
/* ]]> */
</script>
<div id="mh-board-write" class="content " class="clearfix">
	<?php if(wp_verify_nonce(@$_REQUEST['_wpnonce'],'_mh_board_nonce') || wp_verify_nonce(@$_REQUEST['_mh_board_nonce'],'mh_board_nonce')):?>
	<form action="" method="post" id="write_board" enctype="multipart/form-data">
		<input type="hidden" name="action" value="write">
		<input type="hidden" name="redirect_to" value="<?php echo @$_GET['redirect_to'];?>"/>
		<?php if(function_exists('wp_nonce_field'))	wp_nonce_field('mh_board_nonce','_mh_board_nonce');?>
		
		<table cellpadding="0" cellspacing="0">
			<?php if(!is_user_logged_in()):?>
			<input type="hidden" name="write_type" id="write_type" value="guest"/>
			<tr>
				<th>이름</th><td><input type="text" name="guest_name" id="guest_name" tabindex="5"></td>
			</tr>
			<tr>
				<th>이메일</th><td><input type="text" name="guest_email" id="guest_email" tabindex="10"></td>
			</tr>
			<tr>
				<th>비밀번호</th><td><input type="password" name="guest_password" id="guest_password" tabindex="15"></td>
			</tr>
			<tr>
				<th>사이트</th><td>http://<input type="text" name="guest_site" id="guest_site" tabindex="20"></td>
			</tr>
			<?php endif;?>
			<?php if(isset($_GET['post_id']) && $_GET['post_id'] > 0):?>
			<input type="hidden" name="post_parent" value="<?php echo $_GET['post_id'];?>">
			<?php
			$category = wp_get_object_terms($_GET['post_id'],'board_cat');
			?>
			<input type="hidden" name="board_cat" value="<?php echo $category[0]->term_id;?>">
			<?php else:?>
			<?php if(sizeof($categories) > 0):?>
			<tr>
				<th>카테고리</th><td><select name="board_cat" tabindex="25">
				<?php
				foreach($categories as $category){
					?>
					<option value="<?php echo $category->term_id;?>"><?php echo $category->name;?></option>
					<?php
				} 
				?>
			</select></td>
			</tr>
			<?php endif;//카테고리?>
			<?php endif;?>
			<tr>
				<th>제목</th><td><input type="text" name="post_title" class="post_title" tabindex="30"></td>
			</tr>
			<tr>
				<th>내용</th>
				<td>
					<?php wp_editor('', 'post_content',array('media_buttons'=>false,'tabindex'=>35));?>
				</td>
			</tr>
			<tr>
				<th>첨부파일1</th>
				<td><input type="file" name="file1" id="file1" tabindex="40"/></td>
			</tr>
			<tr>
				<th>첨부파일2</th>
				<td><input type="file" name="file2" id="file2" tabindex="45"/></td>
			</tr>
			<tr>
				<th>태그</th><td><input type="text" name="post_tag" tabindex="50">*콤마(,)로 구분해주세요.</td>
			</tr>
			<tr>
				<th>공개여부</th><td><input type="radio" name="post_open" id="post_open" value="1" checked>전체공개<input type="radio" name="post_open" id="post_close" value="0">비공개</div>
		<div id="post_password" style="display:none;"><label for="password">비밀번호</label><input type="password" name="post_password"></td>
			</tr>
		</table>
		<div class="copyright">
		<a href="http://ssamture.net"><img src="http://ssamture.net/ssamturenet.png" border="0"/></a>
	</div>
		<div class="action clearfix">
			<input type="submit" value="글쓰기">
			<input type="hidden" name="action" value="post" />
		</div>		
	</form>
	<?php else:?>
		접근 권한이 없습니다.
	<?php endif;?>
</div>
<?php	
}
?>