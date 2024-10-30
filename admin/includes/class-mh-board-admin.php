<?php
class MH_Board_Admin{
	function __construct(){
	}
	function board_not_exists($name, $slug){
		
		if(!get_term_by('name',$name,'board_cat') && !get_term_by('slug',$slug,'board_cat') && !get_page_by_path($slug)){
			return true;
		}
		return false;
	}
	function create_board($name, $slug){
		if($term_id = wp_insert_term($name, 'board_cat',array('slug'=>$slug))){
			$post = array(
				'post_title'	=> $name,
				'post_name'		=> $slug,
				'post_type'		=> 'page',
				'post_status'	=> 'publish',
				'post_content'	=> '[mh_board board_cat="'.$slug.'"]',
				'comment_status'	=> 'closed'
			);

			if(wp_insert_post($post)){
				$settings = array(
						'list'				=> $_POST['board_list'],
						'type'				=> $_POST['board_type'],
						'default_content'	=> $_POST['board_default_content']
					);
				$settings = serialize($settings);
				update_option('mh_'.$slug.'_settings',$settings);
				return true;
			}else{
				wp_delete_term($term_id);
				return false;
			}
		}
		return false;
	}
	function edit_board($name, $slug){
		global $wpdb;
		$term_id = empty($_POST['term_id']) ? '' : $_POST['term_id'];
		$post_id = empty($_POST['post_id']) ? '' : $_POST['post_id'];
		if(!is_numeric($term_id)){
			return false;
		}
		$term = get_term_by('id',$term_id,'board_cat');
		if($term->name != $name){
			if(get_term_by('name',$name,'board_cat')){
				return false;
			}
		}
		if($term->slug != $slug){
			if(get_term_by('slug',$slug,'board_cat')){
				return false;
			}
		}
		if($post_id > 0){
			$post = get_post($post_id);
			if(isset($post->post_name) && $post->post_name != $slug){
				if(get_page_by_path($slug)){
					return false;
				}
			}
		}else{
			$sql = "select ID from {$wpdb->prefix}posts where post_type = 'page' and (post_status = 'publish' or post_status = 'trash') and post_content like '%mh_board%' and (post_content like '%{$term->name}%' or post_content like '%{$term->slug}%')";
	
			if($result = $wpdb->get_results($sql)){
				$post = get_post($result[0]->ID);
				$post_id = $post->ID;
			}
		}
		if($term = wp_update_term($term_id, 'board_cat',array('name'=>$name,'slug'=>$slug)) ){
			if(!is_numeric($post_id) && empty($post)){
				$post = array(
					'post_title'	=> $name,
					'post_name'		=> $slug,
					'post_type'		=> 'page',
					'post_status'	=> 'publish',
					'post_content'	=> '[mh_board board_cat="'.$slug.'"]',
					'comment_status'	=> 'closed'
				);
				if(wp_insert_post($post)){
					$settings = array(
						'list'				=> $_POST['board_list'],
						'type'				=> $_POST['board_type'],
						'default_content'	=> $_POST['board_default_content']
					);
					$settings = serialize($settings);
					update_option('mh_'.$slug.'_settings',$settings);
					return true;
				}
			}else{
				$post = array(
					'ID'			=> $post->ID,
					'post_title'	=> $name,
					'post_name'		=> $slug,
					'post_type'		=> 'page',
					'post_status'	=> 'publish',
					'post_content'	=> '[mh_board board_cat="'.$slug.'"]'
				);
				if(wp_update_post($post)){
					$settings = array(
						'list'				=> $_POST['board_list'],
						'type'				=> $_POST['board_type'],
						'default_content'	=> $_POST['board_default_content']
					);

					$this->update_board_permission();
					//$settings = serialize($settings);
					update_option('mh_'.$slug.'_settings',$settings);
					return true;
				}
			}
		}
		return false;


	}
	/**
	 * 게시판 권한 저장
	 */
	function update_board_permission(){
		global $wp_roles;
		if (!isset($wp_roles)) {
			$wp_roles = new WP_Roles();
		} 

		$ure_roles = $wp_roles->roles;
		if (is_array($ure_roles)) {
			asort($ure_roles);
		}
		
		
		if(isset($_POST['term_id']) && is_numeric($_POST['term_id'])){
			$input = &$_POST['mh_board_permission_'.$_POST['term_id']];
			foreach($ure_roles as $key => $value){
				$input[$key]['read'] = isset($input[$key]['read']) ? 'on' : 'off';
				$input[$key]['write'] = isset($input[$key]['write']) ? 'on' : 'off';
			}
			$input['guest']['read'] = isset($input['guest']['read']) ? 'on' : 'off';
			$input['guest']['write'] = isset($input['guest']['write']) ? 'on' : 'off';
			
			update_option( 'mh_board_permission_'.$_POST['term_id'], $input );
		}
	}
	/**
	 * 게시판설정 저장
	 */
	function update_board_settings(){
		//print_r($_POST);
	}
}
?>