<?php
add_action( 'admin_menu', 'mh_board_menu' );
function mh_board_menu(){
	$mh_board_setting = add_submenu_page( 'edit.php?post_type=board', __('MH Board Settings','mhboard'), __('MH Board Settings','mhboard'), 'manage_options', 'mh-board-setting', 'mh_board_settings' );
	add_submenu_page( 'edit.php?post_type=board', __('MH Board Management','mhboard'), __('MH Board Management','mhboard'), 'manage_options', 'mh-board-management', 'mh_board_management' );
	add_action('admin_print_styles-'.$mh_board_setting,'mh_board_admin_styles');
}

/* mh board admin style */

function mh_board_admin_styles(){
	wp_register_style('mh-board-admin-style', plugins_url('/mh-board-admin-style.css', __FILE__),'','0.1' );
	wp_enqueue_style('mh-board-admin-style');
}

add_action('admin_init','mh_board_style_options');
function mh_board_style_options(){
	register_setting( 'mh-board-style-options', 'mh_board_style_options' );
}
function mh_board_style(){
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div><h2>MH Board Style</h2>
	<p class="ssamture_net" style="text-align:right">
	<a href="http://ssamture.net"><img src="http://ssamture.net/ssamturenet.png" border="0"></a>
	</p>
	
</div>
<?php
}
add_action('admin_init','mh_board_register_options');
function mh_board_register_options(){
	register_setting( 'mh-board-options', 'mh_board_options' );
}
/**
 * 게시판 관리
 */
function mh_board_management(){
?>
<?php $tab = empty($_GET['tab']) ? 'list' : $_GET['tab'];?>
<?php 
if($tab == 'list')
	$action_link = "<a href=\"".admin_url('edit.php?post_type=board&page=mh-board-management&tab=new')."\" class=\"add-new-h2\">".__('New Board','mhboard')."</a>";
else
	$action_link = "<a href=\"".admin_url('edit.php?post_type=board&page=mh-board-management')."\" class=\"add-new-h2\">".__('Board List','mhboard')."</a>";
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div><h2><?php echo __('MH Board Management','mhboard');?><?php echo $action_link;?></h2>

	
	<?php require_once(dirname(__FILE__).'/mh-board-category-'.$tab.'.php');?>
</div>
<?php
}
function mh_board_settings(){
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div><h2><?php echo __('MH Board Settings','mhboard');?></h2>
	<p class="ssamture_net" style="text-align:right">
	<a href="http://ssamture.net"><img src="http://ssamture.net/ssamturenet.png" border="0"></a>
	</p>
	<?php
	$tab = empty($_GET['tab']) ? 'general': $_GET['tab'];
	?>
	<div id="nav">
		<h2 class="themes-php">
			<a href="<?php echo admin_url('/edit.php?post_type=board&page=mh-board-setting&tab=general');?>" class="nav-tab<?php if( 'general' == $tab ){echo " nav-tab-active";}?>"><?php echo __('General');?></a>
			<a href="<?php echo admin_url('/edit.php?post_type=board&page=mh-board-setting&tab=style');?>" class="nav-tab<?php if( 'style' == $tab ){echo " nav-tab-active";}?>"><?php echo __('Style');?></a>
			<a href="<?php echo admin_url('/edit.php?post_type=board&page=mh-board-setting&tab=custom-css');?>" class="nav-tab<?php if( 'custom-css' == $tab ){echo " nav-tab-active";}?>"><?php echo __('Custom CSS');?></a>
			<!--<a href="<?php echo admin_url('/edit.php?post_type=board&page=mh-board-setting&tab=permission');?>" class="nav-tab<?php if( 'permission' == $tab ){echo " nav-tab-active";}?>"><?php echo __('Permission');?></a>
			<a href="<?php echo admin_url('/edit.php?post_type=board&page=mh-board-setting&tab=settings');?>" class="nav-tab<?php if( 'settings' == $tab ){echo " nav-tab-active";}?>"><?php echo __('Settings');?></a>-->
			<a href="<?php echo admin_url('/edit.php?post_type=board&page=mh-board-setting&tab=update');?>" class="nav-tab<?php if( 'update' == $tab ){echo " nav-tab-active";}?>"><?php echo __('Update');?></a>
		</h2>
	</div>
	<?php require_once(dirname(__FILE__).'/tab/'.$tab.'.php');?>
</div>
<?php
}
function mh_board_update(){
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div><h2><?php echo __('MH Board Update','mhboard');?></h2>
	<p class="ssamture_net" style="text-align:right">
	<a href="http://ssamture.net"><img src="http://ssamture.net/ssamturenet.png" border="0"></a>
	</p>
	
	
</div>
<?php	
}
?>