<?php
add_action('admin_init','mh_board_custom_css_options');
function mh_board_custom_css_options(){
	register_setting( 'mh-board-custom-css', 'mh_board_custom_css' );
}
function mh_board_css(){
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div><h2><?php echo __('MH Board Custom CSS','mhboard');?></h2>
	<form method="post" action="options.php">
		<?php settings_fields( 'mh-board-custom-css' ); ?>
		<?php $mh_board_custom_css = get_option('mh_board_custom_css');
		
		?>
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><label for="emailpush"><?php echo __('Custom CSS','mhboard');?></label></th>
				<td><textarea name="mh_board_custom_css" style="width:500px; height:380px;"><?php echo $mh_board_custom_css;?></textarea></td>
			</tr>
		</tbody>
		</table>
		<?php submit_button();?>
	</form>
	<p class="ssamture_net" style="text-align:right">
	<a href="http://ssamture.net"><img src="http://ssamture.net/ssamturenet.png" border="0"></a>
	</p>
</div>
<?php
}

?>