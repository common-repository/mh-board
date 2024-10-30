<?php
global $board_cat;
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
		'p' => get_mh_board_id(),
		'post_type' => array('board')
	);
	$wp_query = new WP_Query($args);?>
	<?php if ( $wp_query->have_posts()) : ?>
		<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
			<?php $category = wp_get_object_terms(get_the_ID(),'board_cat');?>
			<?php
			$author = get_post_meta(get_the_ID(),'guest_info',true);
			?>
	<form action="" id="mh_board_edit" method="post">
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
				<th><?php echo __('Content' ,'mhboard');?></th><td><?php wp_editor($post_content, 'post_content');?></td>
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
				<th><?php echo __('Tags' ,'mhboard');?></th><td><input type="text" name="post_tag" tabindex="3" value="<?php echo $tagvalue;?>">*<?php echo __('Comma separated' ,'mhboard');?></td>
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
		<div class="action clearfix">
			<a href="#edit" id="mh_edit" class="btn button"><?php echo __('Edit','mhboard');?></a>
			<input type="hidden" name="mh_action" value="update" />
		</div>		
	</form>
		<?php endwhile; ?>
	<?php endif;?>
	<?php endif;?>
</div>