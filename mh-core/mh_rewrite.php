<?php
/**
 * MHBoard Shortcode url rewrite rule
 *
 * @since MHBoard 1.3
 * @author MinHyeong Lim
 *
 */

class MH_Rewrite{
	function __construct(){
		add_action('init',array(&$this, 'mh_board_rewrite'));
		add_action('query_vars',array(&$this, 'mh_board_query_vars'));
		add_filter('post_type_link', array(&$this, 'board_permalink'), 1, 3);
	}
	function mh_board_rewrite(){
		global $wp_rewrite, $mh_board;

		$board_type = $mh_board->get_board_types();
		
		foreach($board_type as $slug){
			//$wp_rewrite->add_permastruct($slug, '/'.$slug.'/%board_id%', false);
			add_rewrite_rule($slug.'/([0-9]+)$','index.php?pagename='.$slug.'&board_id=$matches[1]&mh_type=view','top');
			add_rewrite_rule($slug.'/write/?','index.php?pagename='.$slug.'&mh_type=write','top');
			add_rewrite_rule($slug.'/([0-9]+)/edit$','index.php?pagename='.$slug.'&board_id=$matches[1]&mh_type=edit','top');
			add_rewrite_rule($slug.'/([0-9]+)/delete$','index.php?pagename='.$slug.'&board_id=$matches[1]&mh_type=delete','top');
			add_rewrite_rule($slug.'/([0-9]+)/reply$','index.php?pagename='.$slug.'&board_id=$matches[1]&mh_type=reply','top');
		}
	}
	function mh_board_query_vars($vars){
		$vars[] = "mh_type";
		$vars[] = "board_id";
		return $vars;
	}
	function board_permalink($post_link, $id = 0, $leavename){
		global $wp_rewrite;
		$post = &get_post($id);
		if ( is_wp_error( $post ) )
			return $post;

		if($post->post_type == 'board'){
			
			$board_cat = wp_get_object_terms($post->ID,'board_cat');
			if($wp_rewrite->permalink_structure == '' && isset($board_cat[0]->slug)){
				$post_link = get_mh_board_link($board_cat[0]->slug).'&ID='.$post->ID.'&type=view';
			}else if(isset($board_cat[0]->slug)){
				$post_link = home_url(user_trailingslashit('/'.$board_cat[0]->slug.'/'.$post->ID));
			}
			
		}
		return $post_link;	
	}
}
$mh_rewrite = new MH_Rewrite();
/*
add_action( 'init', 'mh_rewrite_flush' );
function mh_rewrite_flush(){
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
	global $wp_query;
	
}
add_action( 'query_vars','mh_board_query_vars' );
function mh_board_query_vars( $vars ){
	$vars[] = "mh_type";
	$vars[] = "board_id";
	return $vars;
}
add_filter( 'rewrite_rules_array', 'mh_board_url_rewrite_rule' );
function mh_board_url_rewrite_rule($rules){
	
	$mh_rules = array();
	$mh_rules['(.+)/([0-9]+)$'] = 'index.php?pagename=$matches[1]&board_id=$matches[2]&mh_type=view';
	$mh_rules['(.+)/write$'] = 'index.php?pagename=$matches[1]&mh_type=write';
	$mh_rules['(.+)/([0-9]+)/edit$'] = 'index.php?pagename=$matches[1]&board_id=$matches[2]&mh_type=edit';
	$mh_rules['(.+)/([0-9]+)/delete$'] = 'index.php?pagename=$matches[1]&board_id=$matches[2]&mh_type=delete';
	$mh_rules['(.+)/([0-9]+)/reply$'] = 'index.php?pagename=$matches[1]&board_id=$matches[2]&mh_type=reply';
	
	return $mh_rules + $rules;
}
add_filter( 'pre_get_posts','mh_pre_get_posts');
function mh_pre_get_posts($query){
	$post_types = get_post_types('','names');
    $types = array();
    foreach($post_types as $type => $v){
        if($type != 'board'){
            $types[] = $type;
        }
    }
    if(isset($query->query_vars['post_type']) && !in_array($query->query_vars['post_type'],$types) && isset($query->query_vars['page'])){
	//if(isset($query->query_vars['post_type']) && !in_array($query->query_vars['post_type'],array('page','post','attachment')) && isset($query->query_vars['page'])){
		unset($query->query_vars['name']);
		unset($query->query_vars['page']);
		unset($query->query['name']);
		$query->query_vars['p'] = $query->query_vars[$query->query_vars['post_type']];
		unset($query->query_vars[$query->query_vars['post_type']]);

	}
	return $query;
}*/
//add_filter( 'sanitize_title','mh_sanitize_title',1,3);
function mh_sanitize_title($title,$raw_title, $context){
	if($context == 'query'){
		$title = '';
	}
	return $title;
}
?>