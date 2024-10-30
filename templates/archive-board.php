<?php get_header();?>
<?php
do_action('mh_head');
global $mh_board_options;
$mh_board_options = get_option('mh_board_options');
$write = empty($_GET['write']) ? '0' : $_GET['write'];
if($write == 1){
	mhb_get_template_part( 'mhboard','write');
}else{
	mhb_get_template_part( 'mhboard','archive');
}
?>

<?php get_footer();?>