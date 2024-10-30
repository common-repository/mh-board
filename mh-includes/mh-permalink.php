<?php
function the_mh_board_link($slug = ''){
	echo get_mh_board_link($slug);
}
function get_mh_board_link($slug = ''){
	global $wpdb;
	/*$permalink = get_option('permalink_structure');

	if(empty($permalink)){
		$terms = get_term_by('name',$slug,'board_cat');
		if(!$terms){
			$terms = get_term_by('slug',$slug,'board_cat');
		}
		$sql = "select ID from {$wpdb->prefix}posts where post_type = 'page' and (post_status = 'publish' or post_status = 'trash') and post_content like '%mh_board%' and (post_content like '%{$terms->name}%' or post_content like '%{$terms->slug}%')";
		
		if($result = $wpdb->get_results($sql)){
			return get_permalink($result[0]->ID);
		}
	}else{
		return site_url($slug);
	}*/
	$terms = get_term_by('name',$slug,'board_cat');
	if(!$terms){
		$terms = get_term_by('slug',$slug,'board_cat');
	}
	$sql = "select ID from {$wpdb->prefix}posts where post_type = 'page' and (post_status = 'publish' or post_status = 'trash') and post_content like '%mh_board%' and (post_content like '%{$terms->name}%' or post_content like '%{$terms->slug}%')";
	
	if($result = $wpdb->get_results($sql)){
		return get_permalink($result[0]->ID);
	}
}

?>