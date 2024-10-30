<?php
global $mh_board,$pagename;
$board_id = get_mh_board_id();
$post = get_post($board_id);
$guest = $guest_info = get_post_meta(get_the_ID(),'guest_info',false);

?>
<div id="mh-board-delete" class="content " class="clearfix">
	<?php if($guest):?>
	<div id="password_check">
		<form id="mh_board_delete" action="" method="post">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<th><?php echo __('Password');?></th>
				<td><input type="password" name="guest_password"/></td>
			</tr>
		</table>
		<div class="btnarea action clearfix">
			<a href="javascript:;" title="<?php echo __('Delete');?>" class="btn button"><?php echo __('Delete');?></a>
			<input type="hidden" name="mh_action" value="delete"  />
		</div>	
		
		</form>
	</div>
	<?php endif;?>
</div>