<?php
/**
 * 포스트 등록 클래스
 *
 *
 * @category            Core
 * @author              MinHyeong
 * @copyright           Copyright © 2012 ssamture.net
 * @version				1.0
 */
class MH_Register_Post{
	var $post_type = 'post';
	var $post_data = array();
	var $post_meta = array();
	/**
	 * array(
	 * 	'terms'=>inval(terms id),
	 * 	'taxonomy = taxonomy
	 * );
	 */
	var $post_term = array();
	function __construct(){

	}
	function register_post(){
		$post_id = wp_insert_post( $this->post_data );
		if($post_id){
			if($this->post_term['terms'] && $this->post_term['taxonomy']){
				wp_set_object_terms( $post_id, $this->post_term['terms'], $this->post_term['taxonomy'] );
			}
			if($this->post_meta ){
				foreach($this->post_meta as $meta_key => $meta_value){
					update_post_meta($post_id,$meta_key,$meta_value);
				}
			}
			return $post_id;		
		}
		return false;
	}
}
?>