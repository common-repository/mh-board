<?php

/**
 * The mh-board Plugin
 *
 * mh-board is board with a twist from the creators of WordPress.
 *
 * @package mh-board
 * @subpackage Main
 */

/**
 * Plugin Name: MH Board
 * Plugin URI:  http://ssamture.net
 * Description: MH Board is bulletin board for WordPress.
 * Author:      MinHyeong Lim
 * Author URI:  http://ssamture.net
 * Version:     1.3.2.1
 * Text Domain: mhboard
 * Domain Path: /mh-languages/
 */
define('MH_BOARD_VERSION','1.3.2.1');
define('MH_BOARD_UPDATE_URL','http://ssamture.net/mh_board.xml');
define('MH_BOARD_DB_VERSION','201401071442');
ini_set('memory_limit', -1);
load_plugin_textdomain( 'mhboard', false, dirname( plugin_basename( __FILE__ ) ) . '/mh-languages' );
require_once(dirname(__FILE__).'/mh-includes/class-mhboard.php');
require_once(dirname(__FILE__).'/mh-includes/mh-functions.php');
require_once(dirname(__FILE__).'/mh-includes/mh-templates.php');
require_once(dirname(__FILE__).'/mh-includes/mh-post-type.php');
require_once(dirname(__FILE__).'/mh-includes/mh-email-push.php');
require_once(dirname(__FILE__).'/mh-includes/mh-permalink.php');
require_once(dirname(__FILE__).'/mh-includes/mh-comment-template.php');
require_once(dirname(__FILE__).'/admin/mh-board-option-page.php');
require_once(dirname(__FILE__).'/mh-includes/mh_core_load.php');
require_once(dirname(__FILE__).'/templates/widgets/mh_widgets.php');

$board_template = new MH_Templates_Loader('board');
global $mh_board;

$GLOBALS['mh_board'] = new MHBoard();

add_action('wp_enqueue_scripts','mh_board_styles');
function mh_board_styles(){
	wp_register_style('mh-board-style', plugins_url('/templates/css/mh_board.css', __FILE__),'','1.3.2' );
	wp_enqueue_style('mh-board-style');
	wp_register_style('mh-board-custom-style', plugins_url('/templates/css/mh_board_custom_css.php', __FILE__),'','0.1' );
	wp_enqueue_style('mh-board-custom-style');
}
add_action('wp_enqueue_scripts','mh_board_scripts');
function mh_board_scripts(){
	wp_register_script('mh-board-script', plugins_url('/templates/js/mh_board.min.js', __FILE__),array('jquery'),'1.3.2'  );
	wp_enqueue_script('mh-board-script');
	$mh_translation = array(
						'require_name' => __( 'Please enter your name.','mhboard' ),
						'require_title' => __('Please enter the title.','mhboard'),
						'require_email'	=> __('Please enter your e-mail.','mhboard'),
						'require_emailformat'	=> __('Please check the e-mail format.','mhboard'),
						'require_password'		=> __('Please enter the password.','mhboard'),
						'require_search'		=> __('Please enter a search term.','mhboard'),
						'confirm_delete'		=> __('Are you sure you want to delete?','mhboard'),
						'add_file_input'		=> _x('Add','attachment','mhboard'),
						'remove_file_input'		=> _x('Remove','attachment','mhboard'),
						'file_add_max'			=> __('Possible to add up to 5','mhboard')
					);
	wp_localize_script( 'mh-board-script', 'mh_board', $mh_translation );
	
}
add_action('admin_menu','mh_remove_menu');
function mh_remove_menu(){
	remove_submenu_page( 'edit.php?post_type=board','post-new.php?post_type=board' );
	remove_submenu_page( 'edit.php?post_type=board','edit-tags.php?taxonomy=board_cat&amp;post_type=board' );
}
add_action( 'add_meta_boxes', 'mh_board_meta_boxes' );

function mh_board_meta_boxes() {
	add_meta_box( 'mh_board_notice', __('notice','mhboard'), 'mh_board_notice_meta_box', 'board', 'side', 'default');
}
function mh_board_notice_meta_box($post){
	$mh_board_notice = get_post_meta($post->ID, 'mh_board_notice', true) ;
	?>
	<div>
		<p class="form-field">
			<label for="notice"><?php echo __('notice','mhboard');?></label>
			<input type="checkbox" name="notice" value="1"<?php if($mh_board_notice == 1){echo "checked";}?>/>
		</p>
	</div>
	<?php
}
add_action( 'save_post', 'mh_board_save_post' );
function mh_board_save_post( $post_id ){
	global $post;
	if( @$post->post_type == 'board' ){
		$notice = empty($_POST['notice']) ? 0 : (int) $_POST['notice'];
		update_post_meta($post_id, 'mh_board_notice', $notice);
		if(get_post_meta($post_id,'guest_info',true)){
			mh_update_post_author($post_id);
		}
	}
}
//add_action( 'generate_rewrite_rules', 'mhb_generate_rewrite_rules', 10 );
function mhb_generate_rewrite_rules( $wp_rewrite ){
	do_action_ref_array( 'mhb_generate_rewrite_rules', array( &$wp_rewrite ));
}
// 전체 검색에 MH Board 도 검색 가능하도록 추가
function mh_board_search_filter($query){
	if(!is_admin() && $query->is_search){
		$query->set('post_type',array('post','board'));
	}
	return $query;
}
add_filter('pre_get_posts','mh_board_search_filter');
?>
