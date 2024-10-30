<?php
global $mh_board,$pagename,$mh_error;
$board_id = get_mh_board_id();
$post = get_post($board_id);
$guest = $guest_info = get_post_meta(get_the_ID(),'guest_info',false);

?>
<div id="mh-board" class="content " class="clearfix">
	<?php if($guest):?>
	<?php
	if($mh_error->msg != ''){
		echo "<div class='error'><p>{$mh_error->msg}</p></div>";
	}
	?>
	<div id="password_check">
		<form id="mh_board_delete" class="mh_board_form" action="" method="post">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<th><?php echo __('Password');?></th>
				<td><input type="password" name="guest_password"/></td>
			</tr>
		</table>
		<div class="btnarea action clearfix">
			<a href="javascript:;" title="<?php echo __('Delete','mhboard');?>" class="btn button"><?php echo __('Delete','mhboard');?></a>
			<input type="hidden" name="mh_action" value="delete"/>
		</div>	
		
		</form>
	</div>
	<?php else:?>
	<div class='error'><p><?php echo __('Access Denied','mhboard');?></p></div>
	<?php endif;?>
</div>