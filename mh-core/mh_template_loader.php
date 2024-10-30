<?php
/**
 * Templates are in the 'templates' folder.
 *
 *
 * @category            Core
 * @author              MinHyeong
 * @copyright           Copyright © 2012 ssamture.net
 * @version				1.0
 */
class MH_Templates_Loader{
	var $plugin_path; //플러그인 경로
	var $post_type; //템플릿 적용할 커스텀 포스트 타입
	var $templates_path; //템플릿 디렉토리 URL
	function mh_templates_loader($post_type,$templates_path = 'templates'){
		$this->plugin_path = dirname(dirname(__FILE__));
		$this->post_type = $post_type;
		$this->templates_path = '/'.$templates_path.'/';
		
		add_filter('template_include',array(&$this,'mh_template_loader'));
	}
	function mh_template_loader( $template ){
		global $board_cat;

		if( is_single() && get_post_type() == $this->post_type){
			$template = locate_template( array('single-'.$this->post_type.'.php',$this->templates_path.'single-'.$this->post_type.'.php'));
			if(!$template){
				$template = $this->plugin_path.$this->templates_path.'single-'.$this->post_type.'.php';
			}
		}else if(is_post_type_archive($this->post_type)){
			$template = locate_template( array('archive-'.$this->post_type.'.php',$this->templates_path.'archive-'.$this->post_type.'.php'));
			if(!$template){
				$template = $this->plugin_path.$this->templates_path.'archive-'.$this->post_type.'.php';
			}
		}else if(is_tax($this->post_type.'_cat') || isset($board_cat)){
			$template = locate_template( array('archive-'.$this->post_type.'.php',$this->templates_path.'archive-'.$this->post_type.'.php'));
			if(!$template){
				$template = $this->plugin_path.$this->templates_path.'archive-'.$this->post_type.'.php';
			}
		}
		return $template;
	}
}
function mhb_get_template_part( $slug, $name = null ){
	
	if ( !current_theme_supports( 'mhboard' ) )
		load_template( mhb_get_theme_compat_dir() . '/' . $slug . '-' . $name . '.php', false );

	// Current theme supports bbPress to proceed as usual
	else
		get_template_part( $slug, $name );
}
function mhb_get_theme_compat_dir(){
	global $board_template;
	return dirname(dirname(__FILE__)).$board_template->templates_path.'/default';
}
?>
