<?php
/* 카테고리별 권한 설정 */
/* */
add_action('admin_init','mh_board_permission_setting');
function mh_board_permission_setting(){
	$categories = get_terms('board_cat',array('hide_empty'=>0));
	foreach($categories as $category){
		register_setting( 'mh-board-permission-'.$category->term_id, 'mh_board_permission_'.$category->term_id,'mh_board_permission_validate' );
	}
}
function mh_board_permission_validate($input){
	global $wp_roles;

	if (!isset($wp_roles)) {
		$wp_roles = new WP_Roles();
	} 

	$ure_roles = $wp_roles->roles;
	if (is_array($ure_roles)) {
		asort($ure_roles);
	}
	
	foreach($ure_roles as $key => $value){
		$input[$key]['read'] = isset($input[$key]['read']) ? 'on' : 'off';
		$input[$key]['write'] = isset($input[$key]['write']) ? 'on' : 'off';
	}
	$input['guest']['read'] = isset($input['guest']['read']) ? 'on' : 'off';
	$input['guest']['write'] = isset($input['guest']['write']) ? 'on' : 'off';
	return $input;
}
function mh_board_permission(){
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div><h2><?php echo __('MH Board Permission','mhboard');?></h2>
	<p class="ssamture_net" style="text-align:right">
	<a href="http://ssamture.net"><img src="http://ssamture.net/ssamturenet.png" border="0"></a>
	</p>
	<?php
	

	global $wp_roles;

	if (!isset($wp_roles)) {
		$wp_roles = new WP_Roles();
	} 

	$ure_roles = $wp_roles->roles;
	if (is_array($ure_roles)) {
		asort($ure_roles);
	}
	$categories = get_terms('board_cat',array('hide_empty'=>0));
	foreach($categories as $category){
	?>
	<div class="postbox" style="float:left;">
	<form method="post" action="options.php">
	<?php settings_fields( 'mh-board-permission-'.$category->term_id ); ?>
	<h3 style="cursor:default;"><span><?php echo $category->name;?></span></h3>
	<table>
		<tr>
			<th></th>
			<th><?php echo __('Read','mhboard');?></th>
			<th><?php echo __('Write','mhboard');?></th>
		</tr>
	<?php
	$mh_board_per_o = get_option('mh_board_permission_'.$category->term_id);

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
		<tr>
			<td class="role"><?php echo translate_user_role($value['name']);?></td>
			<td><input type="checkbox" name="mh_board_permission_<?php echo $category->term_id;?>[<?php echo $key;?>][read]"<?php if($mh_board_per[$key]['read'] == 'on'){echo ' checked';}?>/></td>
			<td><input type="checkbox" name="mh_board_permission_<?php echo $category->term_id;?>[<?php echo $key;?>][write]"<?php if($mh_board_per[$key]['write'] == 'on'){echo ' checked';}?>/></td>
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
			<td><input type="checkbox" name="mh_board_permission_<?php echo $category->term_id;?>[guest][read]"<?php if($mh_board_per['guest']['read'] == 'on'){echo ' checked';}?>/></td>
			<td><input type="checkbox" name="mh_board_permission_<?php echo $category->term_id;?>[guest][write]"<?php if($mh_board_per['guest']['write'] == 'on'){echo ' checked';}?>/></td>
		</tr>
	</table>
	<?php submit_button();?>
	</form>
	</div>
	<?php
	}
	?>
<?php
}
?>