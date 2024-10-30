<?php
/**
 * 포스트 업데이트 클래스
 *
 *
 * @category            Core
 * @author              MinHyeong
 * @copyright           Copyright © 2012 ssamture.net
 * @version				1.0
 */
class MH_Update_Post{
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
	function update_post(){
		$post_id = wp_update_post( $this->post_data );
		if($post_id){
			if($this->post_term['terms'] && $this->post_term['taxonomy'])
				wp_set_object_terms( $post_id, $this->post_term['terms'], $this->post_term['taxonomy'] );
			return true;		
		}
		return false;
	}
}
?>