<?php
require_once(dirname(__FILE__).'/includes/class-mh-board-admin.php');
$mh_board_admin = new MH_Board_Admin();
if(isset($_POST['action']) && $_POST['action'] == 'edit'){
	if(isset($_POST['name']) && isset($_POST['slug'])){
		$slug = $_POST['slug'];
		$name = $_POST['name'];
		if($mh_board_admin->edit_board($name, $slug)){
			$message = __('Board Updated.');
		}else{
			$message = __('Board already exists.');
		}
		
	}
}
$term_id = empty($_GET['ID']) ? '' : $_GET['ID'];
$terms = get_term_by('id',$term_id,'board_cat');
$post = get_page_by_path($terms->slug);
$postID = empty($post->ID) ? 0 :  $post->ID;
$settings = get_board_settings($terms->slug);
?>
<?php if(isset($message)):?>
<div id="message" class="updated">
	<p><?php echo $message;?></p>
</div>
<?php endif;?>
<form method="post" action="">
	<h3><?php echo __('General');?></h3>
	<table class="form-table">
		<input type="hidden" name="action" value="edit"/>
		<input type="hidden" name="term_id" value="<?php echo $term_id;?>"/>
		<input type="hidden" name="post_id" value="<?php echo $postID;?>"/>
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="board_name"><?php echo __('Board Name','mhboard');?> : </label>
				</th>
				<td>
					<input type="text" name="name" id="board_name" value="<?php echo $terms->name;?>"/>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="board_slug"><?php echo __('Board Slug','mhboard');?> : </label>
				</th>
				<td>
					<?php 
					$permalink = get_option('permalink_structure');

					if(empty($permalink)){
						the_mh_board_link($terms->slug);
						?><input type="hidden" name="slug" id="board_slug" value="<?php echo $terms->slug;?>"/><?php
					}else{
						?><?php echo site_url('/');?><input type="text" name="slug" id="board_slug" value="<?php echo $terms->slug;?>"/><?php
					}
					?>
				</td>
			</tr>
		</tbody>
	</table>
	<h3><?php echo __('Advanced');?></h3>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="board_list"><?php echo __('Set List','mhboard');?> : </label>
				</th>
				<td>
					<select name="board_list[]" id="board_list" multiple="multiple" style="width: 300px; height: 100px;">
						<option value="no"<?php if(in_array('no',$settings['list'])){echo " selected";}?>><?php echo __('No');?></option>
						<option value="title"<?php if(in_array('title',$settings['list'])){echo " selected";}?>><?php echo __('Title');?></option>
						<option value="writer"<?php if(in_array('writer',$settings['list'])){echo " selected";}?>><?php echo __('Writer');?></option>
						<option value="date"<?php if(in_array('date',$settings['list'])){echo " selected";}?>><?php echo __('Date');?></option>
						<option value="count"<?php if(in_array('count',$settings['list'])){echo " selected";}?>><?php echo __('Count');?></option>
						<option value="file"<?php if(in_array('file',$settings['list'])){echo " selected";}?>><?php echo __('File');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="board_type"><?php echo __('Board Type','mhboard');?></label>
				</th>
				<td>
					<select name="board_type" id="board_type">
						<option value="normal"><?php echo __('Normal','mhboard');?></option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="board_default_memo"><?php echo __('Default Content','mhboard');?> : </label>
				</th>
				<td>
					<textarea name="board_default_memo" id="board_default_memo" style="width: 300px; height: 200px;"></textarea>
				</td>
			</tr>
		</tbody>
	</table>
	<h3><?php echo __('Permission','mhboard');?></h3>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th style="color:#fff; background:#000;"><?php echo __('Role');?></th>
				<th style="color:#fff; background:#000;"><?php echo __('Read','mhboard');?></th>
				<th style="color:#fff; background:#000;"><?php echo __('Write','mhboard');?></th>
			</tr>
	<?php
	global $wp_roles;

	if (!isset($wp_roles)) {
		$wp_roles = new WP_Roles();
	} 

	$ure_roles = $wp_roles->roles;
	if (is_array($ure_roles)) {
		asort($ure_roles);
	}
	$mh_board_per_o = get_option('mh_board_permission_'.$terms->term_id);
	foreach($ure_roles as $key => $value):?>
	<?php
		if(sizeof($mh_board_per_o) > 0 ){
		$mh_board_per[$key]['read'] = empty($mh_board_per_o[$key]['read'])? 'on' : $mh_board_per_o[$key]['read'];
		$mh_board_per[$key]['write'] = empty($mh_board_per_o[$key]['write'])? 'on' : $mh_board_per_o[$key]['write'];
		}else{
		$mh_board_per[$key]['read'] = empty($mh_board_per_o[$key]['read'])? 'on' : $mh_board_per_o[$key]['read'];
		$mh_board_per[$key]['write'] = empty($mh_board_per_o[$key]['write'])? 'on' : $mh_board_per_o[$key]['write'];
		}
	?>
		<tr valign="top">
			<td class="role"><?php echo translate_user_role($value['name']);?></td>
			<td><input type="checkbox" name="mh_board_permission_<?php echo $terms->term_id;?>[<?php echo $key;?>][read]"<?php if($mh_board_per[$key]['read'] == 'on'){echo ' checked';}?>/></td>
			<td><input type="checkbox" name="mh_board_permission_<?php echo $terms->term_id;?>[<?php echo $key;?>][write]"<?php if($mh_board_per[$key]['write'] == 'on'){echo ' checked';}?>/></td>
		</tr>
	<?php endforeach;?>
	<?php
		if(sizeof($mh_board_per_o) > 0 ){
		$mh_board_per['guest']['read'] = empty($mh_board_per_o['guest']['read'])? 'on' : $mh_board_per_o['guest']['read'];
		$mh_board_per['guest']['write'] = empty($mh_board_per_o['guest']['write'])? 'on' : $mh_board_per_o['guest']['write'];
		}else{
		$mh_board_per['guest']['read'] = empty($mh_board_per_o['guest']['read'])? 'on' : $mh_board_per_o['guest']['read'];
		$mh_board_per['guest']['write'] = empty($mh_board_per_o['guest']['write'])? 'on' : $mh_board_per_o['guest']['write'];
		}
	?>
		<tr>
			<td class="role"><?php echo __('guest','mhboard');?></td>
			<td><input type="checkbox" name="mh_board_permission_<?php echo $terms->term_id;?>[guest][read]"<?php if($mh_board_per['guest']['read'] == 'on'){echo ' checked';}?>/></td>
			<td><input type="checkbox" name="mh_board_permission_<?php echo $terms->term_id;?>[guest][write]"<?php if($mh_board_per['guest']['write'] == 'on'){echo ' checked';}?>/></td>
		</tr>
			
		</tbody>
	</table>
	<?php submit_button();?>
</form>