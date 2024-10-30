<?php
require_once(dirname(__FILE__).'/includes/class-mh-board-admin.php');
$mh_board_admin = new MH_Board_Admin();
if(isset($_POST['action']) && $_POST['action'] == 'new'){
	if(isset($_POST['name']) && isset($_POST['slug'])){
		$slug = $_POST['slug'];
		$name = $_POST['name'];
		if($mh_board_admin->board_not_exists($name, $slug)){
			if($mh_board_admin->create_board($name, $slug)){
				$message = __('Board Registered.');
			}
			$message = __('Board already exists.');
		}
		
	}
}
?>
<?php if(isset($message)):?>
<div id="message" class="updated">
	<p><?php echo $message;?></p>
</div>
<?php endif;?>
<form method="post" action="">
	<h3><?php echo __('General');?></h3>
	<table class="form-table">
		<input type="hidden" name="action" value="new"/>
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="board_name"><?php echo __('Board Name','mhboard');?> : </label>
				</th>
				<td>
					<input type="text" name="name" id="board_name">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="board_slug"><?php echo __('Board Slug','mhboard');?> : </label>
				</th>
				<td>
					<?php echo site_url('/');?><input type="text" name="slug" id="board_slug">
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
						<option value="no" selected="selected"><?php echo __('No','mhboard');?></option>
						<option value="title" selected="selected"><?php echo __('Title','mhboard');?></option>
						<option value="writer" selected="selected"><?php echo __('Writer','mhboard');?></option>
						<option value="date" selected="selected"><?php echo __('Date','mhboard');?></option>
						<option value="count" selected="selected"><?php echo __('Count','mhboard');?></option>
						<option value="file"><?php echo __('File','mhboard');?></option>
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
	<?php submit_button();?>
</form>