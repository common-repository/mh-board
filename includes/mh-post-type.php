<?php
/**
 * 커스텀포스트타입관련
 *
 *
 * DISCLAIMER
 *
 *
 * @package             MHBoard
 * @category            includes
 * @author              MinHyeong Lim
 * @copyright           Copyright © 2012 ssamture.net
 * @license             http://ssamture.net
 */

add_action( 'init', 'mh_register_post_type');
function mh_register_post_type(){
	// Board labels
	$board['labels'] = array(
		'name'               => __( 'Boards',                   'mhboard' ),
		'menu_name'          => __( 'Boards',                   'mhboard' ),
		'singular_name'      => __( 'Board',                    'mhboard' ),
		'all_items'          => __( 'All Boards',               'mhboard' ),
		'add_new'            => __( 'New Board',                'mhboard' ),
		'add_new_item'       => __( 'Create Board', 	        'mhboard' ),
		'edit'               => __( 'Edit',                     'mhboard' ),
		'edit_item'          => __( 'Edit Board',               'mhboard' ),
		'new_item'           => __( 'New Board',                'mhboard' ),
		'view'               => __( 'View Board',               'mhboard' ),
		'view_item'          => __( 'View Board',               'mhboard' ),
		'search_items'       => __( 'Search Boards',            'mhboard' ),
		'not_found'          => __( 'No boards found',          'mhboard' ),
		'not_found_in_trash' => __( 'No boards found in Trash', 'mhboard' ),
		'parent_item_colon'  => __( 'Parent Board:',            'mhboard' )
	);

	// Board rewrite
	$board['rewrite'] = array(
		'slug'       => 'board',
		'with_front' => false
	);

	// Board supports
	$board['supports'] = array(
		'title',
		'editor',
		'revisions',
	);

	// Board filter
	$mhb_cpt['board'] = apply_filters( 'mhb_register_board_post_type', array(
		'labels'              => $board['labels'],
		'supports'            => $board['supports'],
		'description'         => __( 'MH Board', 'mhboard' ),
		'capability_type'     => array( 'board', 'boards' ),
		'menu_position'       => 6,
		'exclude_from_search' => true,
		'show_in_nav_menus'   => true,
		'public'              => true,
		'show_ui'             => true,
		'can_export'          => true,
		'hierarchical'        => true,
		'query_var'           => true,
		//'taxonomies'		  => array('post_tag'),
		'supports'			  => array('title','editor','author','comments','trackbacks')	
	) );

	// Register Forum content type
	//register_post_type( 'board', $mhb_cpt['board'] );
	register_post_type( 'board', array(
		'labels' => $board['labels'],
		'exclude_from_search' => false,
		'show_in_nav_menus'   => true,
		'public'              => true,
		'show_ui'             => true,
		'can_export'          => true,
		'hierarchical'        => true,
		'query_var'           => true,
		'has_archive'         => 'board',
		'rewrite' => $board['rewrite'],
		'menu_icon' => plugins_url('/icon.png',dirname(__FILE__)),
		//'taxonomies'		  => array('post_tag'),
		'supports' => array( 'title','editor','author','comments' ),
		'menu_position' => 4
	) );
	register_taxonomy('board_cat','board',	array( 
		'hierarchical' => true, 
		'label' => __('Board Category','mhboard'),
		'show_ui' => false,
		'query_var' => true,
		'rewrite' => array('slug' => 'board_cat'),
		'singular_label' => 'Board Category'
		)
	);
}

add_filter('manage_edit-board_columns', 'mh_board_edit_order_columns');

function mh_board_edit_order_columns($columns) {

	global $post;

    $columns = array();

    $columns["cb"]			= "<input type=\"checkbox\" />";

	$columns["ID"]			= __("no",'mhboard');
	$columns['board_cat']	= __('category','mhboard');
	$columns['title']		= __('title','mhboard');
	$columns['comments']	= __('comment','mhboard');
	$columns['mhauthor']	= __('author','mhboard');
	$columns['date']		= __('date','mhboard');
	$columns['count']		= __('count','mhboard');

    return $columns;
}
add_action('manage_board_posts_custom_column', 'mh_board_custom_columns', 2);

function mh_board_custom_columns($column) {

    global $post,$mh_board;
    
    switch ($column) {
        case "ID" :
            echo $post->ID;
            break;
		case "board_cat":
			$category = wp_get_object_terms($post->ID,'board_cat');
			echo $category[0]->name;
			break;
        case "mhauthor" :
			if($post->post_author == 0){
				$guest_info = get_post_meta($post->ID,'guest_info',true);
				$email = $guest_info['guest_email'];
				$author = $guest_info['guest_name'];
			}else{
				$user_data = get_userdata($post->post_author);
				$author = $user_data->display_name; 
				$email = $user_data->user_email;
			}
			echo $author.'('.$email.')';
            break;
        case "count":
			echo $mh_board->get_count($post->ID);
			break;
    }
}
add_filter('manage_edit-board_sortable_columns', 'mh_board_sortable_columns');
function mh_board_sortable_columns(){
	return array(
		'ID' =>'ID'	,
		'title' => 'title'
	);
}
add_filter("posts_orderby", "mh_board_orderby_filter", 10, 2);

function mh_board_orderby_filter($orderby, &$query){
    global $wpdb;
    //figure out whether you want to change the order
    if (get_query_var("post_type") == "board") {
         return "$wpdb->posts.ID DESC";
    }
    return $orderby;
 }
?>