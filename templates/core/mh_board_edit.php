<?php
global $board_cat,$mh_error;
$msg = '';
	
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
<div id="mh-board" class="content " class="clearfix">
	<?php
	if($mh_error->msg != ''){
		echo "<div class='error'><p>{$mh_error->msg}</p></div>";
	}
	?>
	<?php if(@$_REQUEST['edit_type'] == 'guest' && $_REQUEST['action'] == 'delete' && wp_verify_nonce($_REQUEST['_mh_board_nonce'],'mh_board_nonce')):?>
		<div id="popup">
			<form action="" method="post" class="mh_board_form" id="delete_board"><input type="hidden" name="post_id" value="<?php echo $_POST['post_id'];?>"/>
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
		'p' => get_mh_board_id(),
		'post_type' => array('board')
	);
	$wp_query = new WP_Query($args);?>
	<?php if ( $wp_query->have_posts()) : ?>
		<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
		<?php
		//작성자가 회원일 경우 url 점핑인지 체크
		if(get_the_author_meta('ID') > 0 && get_current_user_id() != get_the_author_meta('ID') && !current_user_can('administrator')):
		?>
	<div class='error'><p><?php __('Access Denied','mhboard');?></p></div>
	<?php else:?>
	
			<?php $category = wp_get_object_terms(get_the_ID(),'board_cat');?>
			<?php
			$author = get_post_meta(get_the_ID(),'guest_info',true);
			?>
	<form action="" id="mh_board_edit" class="mh_board_form" method="post"  enctype="multipart/form-data">
		<input type="hidden" name="post_id" value="<?php the_ID();?>">
		<?php if(function_exists('wp_nonce_field'))	wp_nonce_field('mh_board_nonce','_mh_board_nonce');?>
		<table cellpadding="0" cellspacing="0">
			<?php if(sizeof($categories) > 0 && empty($category[0]->term_id)):?>
			<tr>
				<th><?php echo __('Category' ,'mhboard');?></th><td><select name="board_category">
				<?php
				foreach($categories as $category){
					?>
					<option value="<?php echo $category->term_id;?>"><?php echo $category->name;?></option>
					<?php
				} 
				?>
			</select></td>
			</tr>
			<?php else:?>
			<input type="hidden" name="board_category" value="<?php echo $category[0]->term_id;?>"/>
			<?php endif;//카테고리?>
			<tr>
				<th><?php echo __('Title' ,'mhboard');?></th><td><input type="text" name="post_title" class="post_title" tabindex="1" value="<?php if(isset($post_title)){echo $post_title;}else{the_title();} ?>"></td>
			</tr>
			<tr>
				<?php if(empty($post_content)){ $post_content = get_the_content();} ?>
				<th><?php echo __('Content' ,'mhboard');?></th><td><?php wp_editor($post_content, 'post_content',array('media_buttons'=>false,'tabindex'=>35));?></td>
			</tr>
			<?php
			$args = array( 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $post->ID );
			$attachments = get_posts($args);
			?>
			<tr>
				<th><?php echo __('Attachment' ,'mhboard');?></th>
				<td>
					<ul class="mh_attachment">
			<?php
			$attachment_links = '';
			$cnt = 0;
			foreach($attachments as $attachment){
				$_wp_attached_file = get_post_meta($attachment->ID,'_wp_attached_file',true);
				$attachment_ext = end(explode('.', $_wp_attached_file));
				$attachment_links .= "<li><a href='{$attachment->guid}' class='attachment_link' id='attachment-{$attachment->ID}' target='_blank'>{$attachment->post_title}.{$attachment_ext}</a><a href='#' class='button btn fileremove'>"._x('Remove','attachment','mhboard')."</a></li>";	
				$cnt++;		
			}
			echo $attachment_links;
			?>
			<?php if($cnt < 5):?>
				<li><input type="file" name="file1" id="file1" tabindex="40"/><a href="#" class="button btn fileadd"><?php echo _x('Add','attachment','mhboard');?></a></li>
			<?php endif;?>
					</ul>
				</td>
			</tr>
			<tr>
				<th><?php echo __('Status' ,'mhboard');?></th><td><input type="radio" name="post_open" id="post_open" value="1" checked><?php echo __('Public' ,'mhboard');?><input type="radio" name="post_open" id="post_close" value="0"><?php echo __('Private' ,'mhboard');?></div>
		<div id="post_password" style="display:none;"><label for="password"><?php echo __('Password' ,'mhboard');?></label><input type="password" name="post_password"></td>
			</tr>
			<?php if($author):?>
			<tr>
				<th><?php echo __('Edit Password' ,'mhboard');?></th><td><input type="password" name="guest_password"></td>
			</tr>
			<?php endif;?>
		</table>
		<div class="btnarea action clearfix">
			<a href="#edit" id="mh_edit" class="btn button"><?php echo __('Edit','mhboard');?></a>
			<a href="<?php the_permalink();?>" class="btn button"><?php echo __('Cancel','mhboard');?></a>
			<input type="hidden" name="mh_action" value="update" />
		</div>		
	</form>
	<?php endif;?>
		<?php endwhile; ?>

	
		<?php wp_reset_postdata();?>
	<?php endif;?>
	<?php endif;?>
</div>