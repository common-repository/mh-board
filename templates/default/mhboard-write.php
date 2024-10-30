<?php
global $mh_board,$mh_board_options;
add_filter('wp_tilte','mhb_wp_title',10, 3);
function mhb_wp_title($title = '', $sep = '&raquo;', $seplocation = '' ){
	return apply_filters( 'mhb_wp_title', __('Write','mhboard'), $sep, $seplocation );
}
if(isset($_GET['board_cat'])){
	do_action('mh_board_write_permission');
	if(!$mh_board_options['permission']){
		echo __('Access Denied','mhboard');
		return false;
	}
}

$mh_default_category = $mh_board_options['mh_default_category'];

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
		<input type="hidden" name="redirect_to" value="<?php echo @$_GET['redirect_to'];?>"/>
		<?php if(function_exists('wp_nonce_field'))	wp_nonce_field('mh_board_nonce','_mh_board_nonce');?>
		
		<table cellpadding="0" cellspacing="0">
			<?php if(!is_user_logged_in()):?>
			<input type="hidden" name="write_type" id="write_type" value="guest"/>
			<tr>
				<th><?php echo __('Name' ,'mhboard');?></th><td><input type="text" name="guest_name" id="guest_name" tabindex="5"></td>
			</tr>
			<tr>
				<th><?php echo __('E-mail' ,'mhboard');?></th><td><input type="text" name="guest_email" id="guest_email" tabindex="10"></td>
			</tr>
			<tr>
				<th><?php echo __('Password' ,'mhboard');?></th><td><input type="password" name="guest_password" id="guest_password" tabindex="15"></td>
			</tr>
			<tr>
				<th><?php echo __('Site' ,'mhboard');?></th><td>http://<input type="text" name="guest_site" id="guest_site" tabindex="20"></td>
			</tr>
			<?php endif;?>
			<?php if(isset($_GET['board_id']) && $_GET['board_id'] > 0):?>
			<input type="hidden" name="board_parent" value="<?php echo $_GET['board_id'];?>">
			<?php
			$category = wp_get_object_terms($_GET['board_id'],'board_cat');
			?>
			<input type="hidden" name="board_category" value="<?php echo $category[0]->term_id;?>">
			<?php elseif(isset($_GET['board_cat'])):?>
			<?php
			$category = get_term_by('slug',$_GET['board_cat'],'board_cat');
			?>
			<input type="hidden" name="board_category" value="<?php echo $category->term_id;?>">
			<?php else:?>
			<?php if(sizeof($categories) > 0):?>
			<tr>
				<th><?php echo __('Category' ,'mhboard');?></th><td><select name="board_category" tabindex="25">
				<?php
				foreach($categories as $category){
					$mh_board_per_o = get_option('mh_board_permission_'.$category->term_id);
					
					if($mh_board_per_o[mh_get_user_role()]['write'] == 'on'):
					?>
					<option value="<?php echo $category->term_id;?>"<?php if($mh_default_category == $category->term_id){echo " selected";}?>><?php echo $category->name;?></option>
					<?php endif;
				} 
				?>
			</select></td>
			</tr>
			<?php endif;//카테고리?>
			<?php endif;?>
			<tr>
				<th><?php echo __('Title' ,'mhboard');?></th><td><input type="text" name="board_title" class="post_title" tabindex="30"></td>
			</tr>
			<tr>
				<th><?php echo __('Content' ,'mhboard');?></th>
				<td>
					<?php wp_editor('', 'board_content',array('media_buttons'=>false,'tabindex'=>35));?>
				</td>
			</tr>
			<tr>
				<th><?php echo __('Attachment' ,'mhboard');?>#1</th>
				<td><input type="file" name="file1" id="file1" tabindex="40"/></td>
			</tr>
			<tr>
				<th><?php echo __('Attachment' ,'mhboard');?>#2</th>
				<td><input type="file" name="file2" id="file2" tabindex="45"/></td>
			</tr>
			<tr>
				<th><?php echo __('Tags' ,'mhboard');?></th><td><input type="text" name="board_tag" tabindex="50">*<?php echo __('Comma separated' ,'mhboard');?></td>
			</tr>
			<tr>
				<th><?php echo __('Status' ,'mhboard');?></th><td><input type="radio" name="board_open" id="post_open" value="1" checked><?php echo __('Public' ,'mhboard');?><input type="radio" name="board_open" id="post_close" value="0"><?php echo __('Private' ,'mhboard');?></div>
		<div id="post_password" style="display:none;"><label for="password"><?php echo __('Password' ,'mhboard');?></label><input type="password" name="board_password"></td>
			</tr>
		</table>
		<div class="copyright">
		<a href="http://ssamture.net"><img src="http://ssamture.net/ssamturenet.png" border="0"/></a>
	</div>
		<div class="action clearfix">
			<input type="submit" value="<?php echo __('Write' ,'mhboard');?>" class="button">
			<input type="button" value="<?php echo __('Cancel' ,'mhboard');?>" class='button' id="mh-cancel"/>
			<input type="hidden" name="mh_action" value="post" />
		</div>		
	</form>
	<?php else:?>
		<?php echo __('Access Denied.' ,'mhboard');?>
	<?php endif;?>
</div>