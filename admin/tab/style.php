<form method="post" action="options.php">
	<?php settings_fields( 'mh-board-style-options' ); ?>
	<?php $mh_board_style_options = get_option('mh_board_style_options');
	$button_background = $mh_board_style_options['button_background'];
	$button_color = $mh_board_style_options['button_color'];
	?>
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row"><label for="button_background"><?php echo __('Button background color','mhboard');?></label></th>
			<td>Color code: <input name="mh_board_style_options[button_background]" type="text" id="button_background" value="<?php echo $button_background;?>" size="7" maxlength="7">(* <?php echo __('Specifies the background color of the button.' ,'mhboard');?>(ex:#333333))</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="button_color"><?php echo __('Button font color','mhboard');?></label></th>
			<td>Color code: <input name="mh_board_style_options[button_color]" type="text" id="button_color" value="<?php echo $button_color;?>" size="7" maxlength="7">(* <?php echo __('Specifies the background color of the font.' ,'mhboard');?>(ex:#333333))</td>
		</tr>
	</tbody>
	</table>
	<?php submit_button();?>
</form>