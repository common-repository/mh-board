<?php
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