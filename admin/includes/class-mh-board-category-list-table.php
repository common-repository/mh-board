<?php
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class MH_Board_Category_List_Table extends WP_List_Table {
	function __construct(){
		parent::__construct( array(
			'plural'	=> 'boards',
			'singular'	=> 'board'
		) );
	}

	function prepare_items(){
		global $wp_error;
		$columns = $this->get_columns();
		$hidden = array();
        $sortable = $this->get_sortable_columns();
        
        if($this->current_action()){
        	$post_id = empty($_REQUEST['post_id']) ? '' : $_REQUEST['post_id'];
        	if(is_numeric($post_id)){
        		if($this->current_action()  == 'trash'){
        			wp_trash_post($post_id);
        			$wp_error->message = __('Board moved to the Trash.');
        		}else if($this->current_action() == 'restore'){
        			wp_publish_post($post_id);
        			$wp_error->message = __('Board restored from the Trash.');
        		}
        	}
        }
        
        $this->_column_headers = array($columns, $hidden, $sortable);

		$data = get_terms('board_cat',array('hide_empty'=>0));

		$data = array_slice($data,(($current_page-1)*$per_page),$per_page);

		$this->items = $data;
	}

	function get_columns(){
		$columns = array(
			'name'		=> __('Name'),
			'slug'		=> __('Slug'),
			'link'		=> __('Link'),
			'count'		=> __('Count')
		);
		return $columns;
	}

	function column_default($item, $column_name){
        switch($column_name){
			case 'name':
				return $this->column_title($item);
            case 'slug':	
            	return $item->slug;
			case 'link':
				$post = get_page_by_path($item->slug);
				if(isset($post->post_status) && $post->post_status == 'publish'){
					return sprintf('<a href="%s" target="_blank">%s<a/>',get_mh_board_link($item->slug),get_mh_board_link($item->slug));	
				}else{
					return '-';
				}
				
			case 'count':
				return $item->count;
            default:
                return print_r($item,true); //Show the whole array for troubleshooting
        }
    }

    function column_title($item){
    	$post = get_page_by_path($item->slug);
    	if(isset($post->ID)){
    		if($post->post_status == 'trash'){
    			$name = $item->name.' - <b>'.__('Trash').'</b>';
    			$actions = array(
		            'restore'	=> sprintf('<a href="edit.php?post_type=board&page=%s&action=%s&post_id=%s">%s</a>',$_REQUEST['page'],'restore',$post->ID,__('Restore')),
		        );
    		}else{
    			$name = $item->name;
    			$actions = array(
		            'admin'	=> sprintf('<a href="edit.php?post_type=board&page=%s&tab=%s&ID=%s">%s</a>',$_REQUEST['page'],'edit',$item->term_id,__('Edit')),
		            'trash'	=> sprintf('<a href="edit.php?post_type=board&page=%s&action=%s&post_id=%s">%s</a>',$_REQUEST['page'],'trash',$post->ID,__('Trash')),
        			'view'	=> sprintf('<a href="'.get_mh_board_link($item->slug).'" target="_blank">%s</a>',__('View'))
		        );
		        
    		}
    	}else{
    		$name = $item->name;
    		$actions = array(
	            'admin'      => sprintf('<a href="edit.php?post_type=board&page=%s&tab=%s&ID=%s">%s</a>',$_REQUEST['page'],'edit',$item->term_id,__('Edit')),
	        );
    	}
    	return sprintf('%1$s <span style="color:silver"></span>%2$s',
            /*$1%s*/ $name,
            /*$2%s*/ $this->row_actions($actions)
        );
    }

    function get_post_count($term_id){

    }
    function column_slug($item){
    	return $item->slug;
    }
}
?>