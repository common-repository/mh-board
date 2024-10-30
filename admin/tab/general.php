<form method="post" action="options.php">
		<?php settings_fields( 'mh-board-options' ); ?>
		<?php $mh_board_options = get_option('mh_board_options');
		$emailpush = empty($mh_board_options['emailpush']) ? '' : $mh_board_options['emailpush'];
		$mh_comment = empty($mh_board_options['mh_comment']) ? '' : $mh_board_options['mh_comment'];
		$mh_link = empty($mh_board_options['mh_link']) ? '' : $mh_board_options['mh_link'];
		$mh_guestwrite = empty($mh_board_options['mh_guestwrite']) ? '' : $mh_board_options['mh_guestwrite'];
		$mh_category = empty($mh_board_options['mh_category']) ? '' : $mh_board_options['mh_category'];
		$mh_replypost = empty($mh_board_options['mh_replypost']) ? '' : $mh_board_options['mh_replypost'];
		$mh_posts_per_page = empty($mh_board_options['mh_posts_per_page']) ? '10' : $mh_board_options['mh_posts_per_page'];
		
		if($mh_link == 1){
			delete_option('mh_board_write_link');				
			delete_option('mh_board_edit_link');
		}
		?>
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><label for="emailpush"><?php echo __('Email Push', 'mhboard');?></label></th>
				<td><?php echo __('Used:', 'mhboard');?> <input name="mh_board_options[emailpush]" type="checkbox" id="emailpush" value="push" <?php if($emailpush == 'push'){echo " checked";}?>>(* <?php echo __('When people leave comments to the author, we will notify you by email.','mhboard');?>)</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="emailpush"><?php echo __('MH Board Comment', 'mhboard');?></label></th>
				<td><?php echo __('Used:', 'mhboard');?> <input name="mh_board_options[mh_comment]" type="checkbox" id="mh_comment" value="1" <?php if($mh_comment == '1'){echo " checked";}?>>(* <?php echo __('Use the comments in the MH Board template.','mhboard');?>)</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="mh_guestwrite"><?php echo __('Guest writing', 'mhboard');?></label></th>
				<td><?php echo __('Used:', 'mhboard');?> <input name="mh_board_options[mh_guestwrite]" type="checkbox" id="mh_guestwrite" value="1" <?php if($mh_guestwrite == '1'){echo " checked";}?>>(* <?php echo __('To be a guest writing.','mhboard');?>)</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="mh_category"><?php echo __('Categories Hide', 'mhboard');?></label></th>
				<td><?php echo __('Used:', 'mhboard');?> <input name="mh_board_options[mh_category]" type="checkbox" id="mh_category" value="1" <?php if($mh_category == '1'){echo " checked";}?>></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="mh_replypost"><?php echo __('Use reply', 'mhboard');?></label></th>
				<td><?php echo __('Used:', 'mhboard');?> <input name="mh_board_options[mh_replypost]" type="checkbox" id="mh_replypost" value="1" <?php if($mh_replypost == '1'){echo " checked";}?>></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="posts_per_page"><?php echo __('Posts per page', 'mhboard');?></label></th>
				<td><?php echo __('Used:', 'mhboard');?> <input name="mh_board_options[mh_posts_per_page]" type="text" id="mh_posts_per_page" value="<?php echo $mh_posts_per_page;?>">(* <?php echo __('Allows you to specify the number to be displayed per page.','mhboard');?>)</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="posts_per_page"><?php echo __('Default Category', 'mhboard');?></label></th>
				<td>
					<select name="mh_board_options[mh_default_category]">
						<?php
							$mh_default_category = $mh_board_options['mh_default_category'];
							$board_cats = get_terms('board_cat',array('hide_empty'=>0));
						foreach($board_cats as $board_cat):?>
						<option value="<?php echo $board_cat->term_id;?>"<?php if($mh_default_category == $board_cat->term_id){echo " selected";}?>>
							<?php echo $board_cat->name;?>
						</option>
						<?php endforeach;?>
					</select>
				(* <?php echo __('Allows you to specify the number to be displayed per page.','mhboard');?>)</td>
			</tr>
		</tbody>
		</table>
		<?php submit_button();?>
	</form>