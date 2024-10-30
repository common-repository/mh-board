<?php
global $wp_error;
require_once(dirname(__FILE__).'/includes/class-mh-board-category-list-table.php');
$mh_board_category_list_table = new MH_Board_Category_List_Table();
$mh_board_category_list_table->prepare_items();
?>
<?php if(isset($wp_error->message)):?>
<div id="message" class="updated">
	<p><?php echo $wp_error->message;?></p>
</div>
<?php endif;?>
<?php
$mh_board_category_list_table->display();
?>