<?php get_header();?>
<?php
do_action('mh_head');
global $mh_board_options;
$mh_board_options = get_option('mh_board_options');
$edit = empty($_GET['edit']) ? '0' : $_GET['edit'];
if($edit == 1){
	mhb_get_template_part( 'mhboard','edit');
}else{
	mhb_get_template_part( 'mhboard','single');
}
?>

<?php get_footer();?>