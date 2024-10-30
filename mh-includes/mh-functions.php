<?php
function mh_media_buttons() {
	// If we're using http and the admin is forced to https, bail.
	if ( ! is_ssl() && ( force_ssl_admin() || get_user_option( 'use_ssl' ) )  ) {
		return;
	}

	include_once( ABSPATH . '/wp-admin/includes/media.php' );
	ob_start();
	do_action( 'media_buttons' );

	// Replace any relative paths to media-upload.php
	echo preg_replace( '/([\'"])media-upload.php/', '${1}' . admin_url( 'media-upload.php' ), ob_get_clean() );
}

function mh_board_register_default_page(){
	$mh_board_default_pagel = array(
		'Write'=>'[mh_board_write_form]',
		'Edit'=>'[mh_board_edit_form]',
	);
	foreach($mh_board_default_pagel as $post_title => $post_content){
		$args = array(
			'post_title' =>$post_title, 
			'post_status' => 'publish', 
			'post_type' => 'page',
			'post_author' => 1,
			'ping_status' => get_option('default_ping_status'),
			'comment_status' => 'closed',
			'post_content' => $post_content
		);

		if(!get_page_by_title($post_title)){
		  wp_insert_post( $args );
		}
	}
}
//add_action( 'init', 'mh_board_register_default_page', 0 );
require_once(dirname(__FILE__).'/mh-actions.php');

//add_filter('pre_option_posts_per_page', 'mh_limit_posts_per_page');
function mh_limit_posts_per_page(){
	global $wp_query;
	if ( @$wp_query->query_vars['post_type'][0]=='board'){
        return 10;
    }else{
    	$all_options = wp_load_alloptions();
        return $all_options["posts_per_page"]; // default: 5 posts per page
    }
}
function my_custom_posts_per_page( &$q ) {
    if ( $q->is_archive ) // any archive
    if(@$q->query_vars['post_type'] == 'board'){  //custom post type "faq" archive
    $q->set( 'posts_per_page', 5 );
    }
   	return $q;
}

//add_filter('parse_query', 'my_custom_posts_per_page');
function mh_get_board_write_link($post_id = '',$par = ''){
	global $wpdb;
	$board_link =  get_post_type_archive_link('board');
	$write_link = '';
	if(strstr($board_link, '?')){
		$write_link .= $board_link . '&write=1';
	}else{
		$write_link .= $board_link . '?write=1';
		
	}
	if($post_id > 0){
		$write_link .= '&board_id='.$post_id;
	}
	return $write_link;

	if($link = get_option('mh_board_write_link')){
		return $link;
	}else if($result = $wpdb->get_results("select ID,guid from {$wpdb->prefix}posts where post_type = 'page' and post_content like '%[mh_board_write_form]%' and post_status = 'publish'")){
		if(get_option('permalink_structure') == ''){
			update_option('mh_board_write_link',home_url('?page_id='.$result[0]->ID));
			return home_url('?page_id='.$result[0]->ID);
		}else{
			update_option('mh_board_write_link',$result[0]->guid);
			return $result[0]->guid;	
		}
		
	}else{
		mh_board_register_default_page();
		return '/write';
	}
}
function mh_get_board_edit_link(){
	global $wpdb,$post;
	$board_link =  get_permalink();
	$category = wp_get_object_terms(get_the_ID(),'board_cat');
	$link = '';
	if(strstr($board_link, '?')){
		$link = $board_link . '&edit=1';	
	}else{
		$link = $board_link . '?edit=1';
	}
	
	if($category[0]->slug){
		$link .= '&board_cat='.$category[0]->slug;
	}
	return $link;
	if($link = get_option('mh_board_edit_link')){
		return $link;
	}else if($result = $wpdb->get_results("select ID,guid from {$wpdb->prefix}posts where post_type = 'page' and post_content like '%[mh_board_edit_form]%' and post_status = 'publish'")){
		if(get_option('permalink_structure') == ''){
			update_option('mh_board_edit_link',home_url('?page_id='.$result[0]->ID));
			return home_url('?page_id='.$result[0]->ID);	
		}else{
			update_option('mh_board_edit_link',$result[0]->guid);
			return $result[0]->guid;	
		}
	}else{
		mh_board_register_default_page();
		return '/edit';
	}
}
function mh_get_board_link_by_board_cat($board_cat = ''){
	if($board_cat){
		return get_term_link(intval($board_cat),'board_cat');
	}else{
		return get_post_type_archive_link('board');
	}

}
function mh_update_post_author($post_id , $author = 0){
	global $wpdb;
	if($wpdb->query("update {$wpdb->prefix}posts set post_author = $author where ID = $post_id")){
		return true;
	}
	return false;
}
add_action('admin_notices','mh_board_notice');
function mh_board_notice(){
	if (function_exists('curl_init')) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, MH_BOARD_UPDATE_URL);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 600);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
		$data = curl_exec($ch);
		$data = simplexml_load_string($data);
		
		curl_close($ch);
	} else {
		// curl library is not installed so we better use something else
		$xml = @wp_remote_get(MH_BOARD_UPDATE_URL);
		$data = @simplexml_load_string($xml['body']);
	}
	if(is_object($data)){
	$is_update = false;
	if($data->version != MH_BOARD_VERSION){
		$is_update = true;
	}
	if($data->notice_status == 1){
		echo "<div class=\"updated\"><p>{$data->notice_content}</p></div>";
	}
	if (! current_user_can('manage_options') || $is_update == false ) 
        return;
	echo "<div class=\"updated\"><p>[".__('MH Board notice','mhboard')."] ".__('Installed version:','mhboard').MH_BOARD_VERSION." | ".__('Current version','mhboard').":{$data->version}</p>";
	echo "<p>".__('Read more:','mhboard')." <a href='{$data->download}'>$data->download</a></p></div>";
	}

}
//short code board 
function mh_board_view_link($post_id){
	echo get_mh_board_view_link($post_id);
}
function get_mh_board_view_link($post_id){
	global $mh_board_link;
	if($mh_board_link)
		$board_link = $mh_board_link;
	else
		$board_link = get_pagenum_link();
	
	if(strstr($board_link,'?')){
		$board_link .= '&type=view&ID='.$post_id;
	}else{
		$board_link .= '/'.$post_id;
	}
	return $board_link;
}
function mh_board_reply_link($post_id, $board_cat = ''){
	global $mh_board_link;
	if($mh_board_link)
		$board_link = $mh_board_link;
	else
		$board_link = get_pagenum_link();
	
	if(strstr($board_link,'?')){
		$board_link .= '&type=reply&ID='.$post_id;
	}else{
		$board_link .= '/'.$post_id.'/reply';
	}
	echo $board_link;
}
function mh_board_edit_link($post_id, $board_cat = ''){
	global $mh_board_link;
	if($mh_board_link)
		$board_link = $mh_board_link;
	else
		$board_link = get_pagenum_link();
	
	if(strstr($board_link,'?')){
		$board_link .= '&type=edit&ID='.$post_id;
	}else{
		$board_link .= '/'.$post_id.'/edit';
	}
	echo $board_link;
}
function mh_board_delete_link($post_id, $board_cat = ''){
	global $mh_board_link;
	if($mh_board_link)
		$board_link = $mh_board_link;
	else
		$board_link = get_pagenum_link();
	
	if(strstr($board_link,'?')){
		$board_link .= '&type=delete&ID='.$post_id;
	}else{
		$board_link .= '/'.$post_id.'/delete';
	}
	echo $board_link;
}

/*function mh_board_list_link($args = array()){
	$board_link = $_SERVER['HTTP_REFERER'];
	if(strstr($board_link,'?')){
		foreach($args as $key => $value){
			$board_link .= '&'.$key.'='.$value;
		}
	}else{
		$board_link .= '?';
		foreach($args as $key => $value){
			$board_link .= $key.'='.$value.'&';
		}
	}
	return $board_link;
}*/
add_filter('wp_handle_upload_prefilter','mh_board_replace_filename');
function mh_board_replace_filename($file){

	$file['name'] = str_replace('.php','.php.txt',$file['name']);
    $file['name'] = str_replace('.html','.html.txt',$file['name']);
    $file['name'] = str_replace('.htm','.htm.txt',$file['name']);
    $file['name'] = str_replace('.exe','.exe.txt',$file['name']);
    

	return $file;
}
//파일 업로드 관련
function mh_board_insert_attachment($file_handler,$post_id,$setthumb='false') {
	// check to make sure its a successful upload
	if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');

	$attach_id = media_handle_upload( $file_handler, $post_id );
	update_post_meta($post_id,'attach_'.$attch_id,$_FILES[$file_handler]['name']);
	if ($setthumb) update_post_meta($post_id,'_thumbnail_id',$attach_id);
	return $attach_id;
}
//get recent board
function get_recent_mh_board($board_cat = '', $posts_per_page = 5 , $slug = null){
	$category = get_term_by('name',$board_cat,'board_cat');
	if(!$category){
		$category = get_term_by('slug',$board_cat,'board_cat');
	}
	
	$args= array (
		'post_type' => array('board'),
		'post_status' => array('publish'),
		'posts_per_page'=>$posts_per_page,
		'paged'=>1,
		'orderby' =>'post_date',
		'order' => 'DESC',
		'board_cat'=> $category->slug,
		'post_parent'=>0

	);
	
	$mh_query = new WP_Query($args);?>
	<h4><?php echo $category->name;?></h4>
	<?php if(isset($slug)):?>
	<a href="<?php echo site_url($slug);?>" class="com_more">+더보기</a>
	<?php else:?>
	<a href="<?php echo get_term_link($category->slug,'board_cat');?>" class="com_more">+더보기</a>
	<?php endif;?>
	<?php if ( $mh_query->have_posts() ) : ?>
	
	<ul>
	<?php while ( $mh_query->have_posts() ) : $mh_query->the_post(); ?>
	<?php
	$afterdate = strtotime('+2 day',strtotime(get_the_date('Y/m/d')));
	$notime = time();
	$new = '';
	if($notime <= $afterdate){
		$new = " <img src=\"".plugins_url('/templates/images/new.gif',dirname(__FILE__))."\" alt=\"new\" align=\"absmiddle\"/>";
	}
	if(isset($slug))
		$permalink = site_url($slug.'/'.get_the_ID());
	else
		$permalink = get_permalink();
	?>
		<li><a href="<?php echo $permalink;?>"><?php the_board_title(); ?>
						<?php if(get_comments_number() > 0){
							echo  "[".get_comments_number()."]";
						}?>
						</a><?php echo $new;?></li>
	<?php endwhile; ?>
	</ul>
		
		
	<?php endif;?><?php
}
function mh_get_user_role(){
	global $user_role;
	if($user_role){
		return $user_role;
	}
	if($user_role = get_userdata(get_current_user_id())){
		$user_role = $user_role->roles[0];
	}else{
		$user_role = 'guest';
	}
	return $user_role; 
}
add_action('mh_board_read_permission','mh_board_read_permission');
function mh_board_read_permission(){
	global $post,$mh_board_options;
	if(isset($_GET['ID'])){
		$post_id = $_GET['ID'];
	}else{
		$post_id = $post->ID;
	}
	$category =@ wp_get_object_terms($post_id,'board_cat');

	$mh_board_per_o = get_option('mh_board_permission_'.$category[0]->term_id);
	$mh_board_options['permission']  = true;
	if($mh_board_per_o[mh_get_user_role()]['read'] == 'off'){
		$mh_board_options['permission']  = false;
	}
}
add_action('mh_board_write_permission','mh_board_write_permission');
function mh_board_write_permission(){
	global $post,$mh_board_options,$board_cat;
	
	if(isset($_GET['board_cat'])){
		$board_cat = $_GET['board_cat'];
	}
	if(isset($_GET['board_cat']) || isset($board_cat)){
		
		$category =@ get_term_by('slug',$board_cat,'board_cat');

		$mh_board_per_o = get_option('mh_board_permission_'.$category->term_id);
		$mh_board_options['permission'] = true;
		if($mh_board_per_o[mh_get_user_role()]['write'] == 'off'){
			$mh_board_options['permission'] = false;
		}
	}
}
add_action('mh_screens','mh_board_action');
function mh_board_action(){
	global $mh_error;
	$mh_error = new stdClass;
	$mh_type = get_mh_board_type();
	if(empty($_REQUEST['mh_action'])){
		if($mh_type != 'delete')
			return '';
	}
	if($mh_type == 'delete' && wp_verify_nonce(@$_REQUEST['_wpnonce'],'_mh_board_nonce')){
		$board_id = get_mh_board_id();
		$current_user_id = get_current_user_id();
		$post = get_post($board_id);
		if(isset($_GET['redirect_to'])){
			$pagename = $_GET['redirect_to'];
		}else if(empty($_GET['page_id'])){
			$pagename = '/'.get_query_var('pagename');
		}else{
			$pagename = '/?page_id='.$_GET['page_id'];
		}
		
		
		if($post->post_author == $current_user_id && $current_user_id > 0){
			$args = array(
				'ID' => $board_id,
				'post_status'   => 'trash'
			);
			if(wp_update_post($args)){
				//echo "<script type='text/javascript'>location.href='".$pagename."';</script>";
				wp_redirect($pagename);die();
			}
		}

	}
	if(@$_REQUEST['mh_action'] == 'post' && wp_verify_nonce($_REQUEST['_mh_board_nonce'],'mh_board_nonce')){ //글쓰기 관련
		$mh_board_write = new MH_Register_Post();
		$user_id        = get_current_user_id() ? get_current_user_id() : 0;
		$tags           = trim( $_POST['board_tag'] );
		$post_title		= $_POST['board_title'];
		$post_content	= $_POST['board_content'];
		$mh_board_write->post_data = array(
			'post_author'   => $user_id,
			'post_title'    => $post_title,
			'post_content'  => $post_content,
			'post_type'     => 'board',
			'tags_input'    => $tags,
			'post_status'   => 'publish',
			'comment_status' => 'open',
		);
		if(isset($_POST['board_parent']) && $_POST['board_parent'] > 0){
			$mh_board_write->post_data['post_parent'] = $_POST['board_parent'];
		}

		if(isset($_POST['board_tag'])){
			$mh_board_write->post_data['tags_input'] = $_POST['board_tag'];
		}
		if($_POST['board_open'] == 0 && $_POST['board_password']){
			$mh_board_write->post_data['post_password'] = $_POST['board_password'];
		}
		if($user_id == 0 && $_POST['guest_name'] && $_POST['guest_email'] && $_POST['guest_password']){
			$guest_info = array(
				'guest_name' => htmlspecialchars($_POST['guest_name']),
				'guest_email' => htmlspecialchars($_POST['guest_email']),
				'guest_password' => htmlspecialchars($_POST['guest_password']),
				'guest_site' => htmlspecialchars($_POST['guest_site'])
			);
			$mh_board_write->post_meta = array(
				'guest_info' => $guest_info
			);
		}
		
		$mh_board_write->post_term = array(
			'terms' => array(intval($_POST['board_category'])),
			'taxonomy' => 'board_cat'
		);
		$mh_board_write->post_meta['mh_board_notice'] = (int)0;
		$term = get_term_by('id',$_POST['board_category'],'board_cat');

		if($pid = $mh_board_write->register_post()){
			$registered = true;
			if(isset($_FILES[0]['name'])){
				foreach($_FILES as $file => $array){
					$newupload = mh_board_insert_attachment($file,$pid);
					if(is_object($newupload)){
						$mh_error->msg = $newupload->errors['upload_error'][0];
						$args = array(
							'ID' => $pid,
							'post_status'   => 'trash'
						);
						wp_update_post($args);
						$registered = false;
						break;
					}
				}
			}
			if($registered && isset($_POST['mh_redirect_to'])){
				if(stristr($_POST['mh_redirect_to'], '?') === FALSE){
					wp_redirect($_POST['mh_redirect_to'].'?updated=registered');
				}else{
					wp_redirect($_POST['mh_redirect_to'].'&updated=registered');
				}
				die();
			}else if($registered){
				if(stristr(get_permalink($pid), '?') === FALSE){
					wp_redirect(get_permalink($pid).'?updated=registered');
				}else{
					wp_redirect(get_permalink($pid).'&updated=registered');
				}
				die();
			}
		}else{
			
		}
	}else if(@$_REQUEST['mh_action'] == 'update' && wp_verify_nonce($_REQUEST['_mh_board_nonce'],'mh_board_nonce')){
		//print_r($_POST);
		$old_post = get_post($_POST['post_id']);
		if($old_post->post_author > 0 && get_current_user_id() != $old_post->post_author && !current_user_can('administrator')):
			$mh_error->msg = __("Access Denied",'mhboard');
		else:
			$mh_board_update = new MH_Update_Post();
			$user_id        = get_current_user_id() ? get_current_user_id() : 0;
			$tags           = trim( $_POST['post_tag'] );
			$post_title		= empty($_POST['post_title']) ? '' : $_POST['post_title'];
			$post_content	= empty($_POST['post_content']) ? '' : $_POST['post_content'];
			$mh_board_update->post_data = array(
				'ID'			=> $_POST['post_id'],
				'post_author'   => $user_id,
				'post_title'    => $post_title,
				'post_content'  => $post_content,
				'post_type'     => 'board',
				'tags_input'    => $tags,
				'post_status'   => 'publish'
			);
			$author = get_post_meta($_POST['post_id'],'guest_info',true);
			$update = false;
			if($author){
				if($_POST['guest_password'] && $_POST['guest_password'] == $author['guest_password']){
					$update = true;
				}else{
					$update = false;
					$mh_error->msg = "비밀번호를 확인해주세요.";
				}
			}else if(get_current_user_id()){
				$update = true;
			}else{
				$update = true;
			}
			
			if($_POST['post_open'] == 0 && $_POST['post_password']){
				$mh_board_update->post_data['post_password'] = $_POST['post_password'];
			}
			$mh_board_update->post_term = array(
				'terms' => array(intval($_POST['board_category'])),
				'taxonomy' => 'board_cat'
			);
			$term = get_term_by('id',$_POST['board_category'],'board_cat');
			if(isset($_POST['delete_attachment'])){
				foreach($_POST['delete_attachment'] as $attachment_id){
					$post = get_post($attachment_id);
					if($post->post_author == get_current_user_id()){
		                if(wp_delete_attachment( $attachment_id )){

		                }
		            }
				}
			}
			if(isset($_FILES[0]['name'])){
				foreach($_FILES as $file => $array){
					$newupload = mh_board_insert_attachment($file,$_POST['post_id']);
				}
			}
			if($update){
				if($mh_board_update->update_post()){
					
					if(stristr(get_permalink($_POST['post_id']), '?') === FALSE){
						wp_redirect(get_permalink($_POST['post_id']).'?updated=updated');
					}else{
						wp_redirect(get_permalink($_POST['post_id']).'&updated=updated');
					}
					die();
				}else{
					
				}
			}
		endif;
	}else if((@$_REQUEST['mh_action'] == 'delete' && wp_verify_nonce($_REQUEST['_mh_board_nonce'],'mh_board_nonce')) || $mh_type == 'delete'){
		$post_id = get_mh_board_id();
		$post = get_post($post_id);
		$guest_info = get_post_meta($post_id,'guest_info',true);
		global $mh_board;
		
		if((get_current_user_id() == $post->post_author && get_current_user_id() > 0) || (isset($_POST['guest_password']) && $_POST['guest_password'] == $guest_info['guest_password'])){
			$args = array(
				'ID' => $post_id,
				'post_status'   => 'trash'
			);
			if(wp_update_post($args)){
				if(isset($mh_type)){
					global $pagename;
					echo "<script type='text/javascript'>location.href='".site_url($pagename)."';</script>";
					if(stristr(site_url($pagename), '?') === FALSE){
						wp_redirect(site_url($pagename).'?updated=deleted');
					}else{
						wp_redirect(site_url($pagename).'&updated=deleted');
					}
					die();
				}else{
					if(stristr(get_post_type_archive_link('board'), '?') === FALSE){
						wp_redirect(get_post_type_archive_link('board').'?updated=deleted');
					}else{
						wp_redirect(get_post_type_archive_link('board').'&updated=deleted');
					}
					die();
				}
				
			}
		}else{
			$mh_error->msg = "비밀번호를 확인해주세요.";
		}
	}
}
function the_board_ID(){
	echo get_the_board_ID();
}
function get_the_board_ID(){
	global $mh_query, $boardid;
	if(empty($boardid)){
		$posts_per_page = $mh_query->query_vars['posts_per_page'];
		$paged = get_query_var('paged');
		if($paged == 0){
			$paged = 1;
		}
		
		$total_count = $mh_query->found_posts;
		$boardid = $total_count - ($posts_per_page * ($paged - 1));
	}
	
	return $boardid--;
}
function the_board_title(){
	global $post;
	$title = $post->post_title;
	if(strlen($post->post_password) > 0){
		$title = "[비밀글]".$title;
	}
	echo $title;
}
function mh_comment($comment, $args, $depth){
	echo 'asdfasdf';
	print_r($comment);
}
add_action( 'wp_enqueue_scripts', 'mh_scripts_styles' );
function mh_scripts_styles(){
	wp_enqueue_script( 'comment-reply' );
}
//이전 글 링크
function the_before_board_link($args = array()){
	global $mh_board, $wpdb,$pagename;
	$term_id = $mh_board->current_board_cat;
	$post_id = $mh_board->current_board_id;
	$defaults = array(
			'html'	=> 'tr',
			'col'	=> 5
		);
	$args = wp_parse_args( $args, $defaults );
	extract($args);
	$sql = "select P.ID, P.post_title from {$wpdb->prefix}posts P, {$wpdb->prefix}term_taxonomy TT, {$wpdb->prefix}term_relationships TR where P.post_type = 'board' and P.ID < $post_id and TT.term_taxonomy_id = TR.term_taxonomy_id and P.ID = TR.object_id and TT.term_id = $term_id and P.post_status = 'publish' and P.post_parent = 0 order by P.ID desc limit 1";

	if($results = $wpdb->get_results($sql)){
		$board_id = $results[0]->ID;
		$board_title = $results[0]->post_title;
		switch($html){
			case 'tr':
		
		?><tr><th><?php echo __('Previous','mhboard');?></th><td colspan="<?php echo $col;?>"><a href="<?php echo get_permalink($board_id);?>" title="<?php echo __('Previous','mhboard');?>"><?php echo get_the_title($board_id);?></a></td><?php
			break;
			case 'li':
			?><li><span><?php echo __('Previous','mhboard');?></span><a href="<?php echo get_permalink($board_id);?>" title="<?php echo __('Previous','mhboard');?>"><?php echo get_the_title($board_id);?></a></li><?php
			break;
		}
	}
	
}
function the_after_board_link($args = array()){
	global $mh_board, $wpdb,$pagename;
	$term_id = $mh_board->current_board_cat;
	$post_id = $mh_board->current_board_id;
	$defaults = array(
			'html'	=> 'tr',
			'col'	=> 5
		);
	$args = wp_parse_args( $args, $defaults );
	extract($args);
	$sql = "select P.ID from {$wpdb->prefix}posts P, {$wpdb->prefix}term_taxonomy TT, {$wpdb->prefix}term_relationships TR where P.post_type = 'board' and P.ID > $post_id and TT.term_taxonomy_id = TR.term_taxonomy_id and P.ID = TR.object_id and TT.term_id = $term_id and P.post_status = 'publish' and P.post_parent = 0  order by P.ID asc limit 1";

	if($results = $wpdb->get_results($sql)){
		$board_id = $results[0]->ID;
		switch($html){
			case 'tr':
		
		?><tr><th><?php echo __('Next','mhboard');?></th><td colspan="<?php echo $col;?>"><a href="<?php echo get_permalink($board_id);?>" title="<?php echo __('Next','mhboard');?>"><?php echo get_the_title($board_id);?></a></td><?php
			break;
			case 'li':
			?><li><span><?php echo __('Next','mhboard');?></span><a href="<?php echo get_permalink($board_id);?>" title="<?php echo __('Next','mhboard');?>"><?php echo get_the_title($board_id);?></a></li><?php
			break;
		}
	}
}
function is_board_view(){
	$board_id = get_mh_board_id();

	if($board_id > 0){
		return true;
	}
	return false;
}
function get_board_settings($slug){
	$settings = get_option('mh_'.$slug.'_settings',array());

	$defaults = array(
		'list'				=> array('no','title','writer','date','count'),
		'type'				=> 'normal',
		'default_content'	=> ''
	);

	$settings = wp_parse_args( $settings, $defaults );

	return $settings;
}
//게시판 아이디 가져오기(post_id)
function get_mh_board_id(){
	global $wp_rewrite, $post,$mh_board;
	if(is_singular('board') && isset($mh_board->ID)){
		return $mh_board->ID;
	}else if($wp_rewrite->permalink_structure == '' && isset($_GET['ID']) && is_numeric($_GET['ID'])){
		return $_GET['ID'];
	}else{
		$ID = get_query_var('board_id');
		return $ID;
	}
}
//게시판 요청 타입 가져오기
function get_mh_board_type(){
	global $wp_rewrite,$mh_board;
	if(isset($_GET['write']) && $_GET['write'] == 1){
		$type = 'write';
	}else if(is_singular( $mh_board->board_slug )){
		$type = empty($_GET['type']) ? 'view' : $_GET['type'];
	}else if($wp_rewrite->permalink_structure == ''){
		$type = empty($_GET['type']) ? 'list' : $_GET['type'];
	}else{
		$type = get_query_var('mh_type');
		
	}
	return $type;
}
function get_mh_board_list_link(){
	global $wp_rewrite,$mh_board;
	if(isset($mh_board->current_url) && $mh_board->current_url != ''){
		$board_list_link = $mh_board->current_url;
	}else if($wp_rewrite->permalink_structure == ''){
		$board_list_link = site_url('?page_id='.$_GET['page_id']);
	}else{
		$pagename = get_query_var('pagename');
		$board_list_link = site_url('/'.$pagename);
	}
	return $board_list_link;
}
function get_current_mh_board_term(){
	global $wp_rewrite;
	if($wp_rewrite->permalink_structure == ''){
		$post = get_post($_GET['page_id']);
		$pagename = $post->post_name;
	}else{
		$pagename = get_query_var('pagename');
	}
	$term = get_term_by('slug',$pagename,'board_cat');

	return $term;
}
function get_mh_board_search_board_id($type,$q){
	global $wpdb;
	$p = array(0);
	switch($type){
		case 's'://제목 검색
			$sql = "select ID from {$wpdb->prefix}posts where post_type = 'board' and post_status = 'publish' and post_title like '%$q%'";
			if($results = $wpdb->get_results($sql)){
				foreach($results as $r){
					$p[] = $r->ID;
				}
			}
			break;
		case 'c'://내용 검색
			$sql = "select ID from {$wpdb->prefix}posts where post_type = 'board' and post_status = 'publish' and post_content like '%$q%'";
			if($results = $wpdb->get_results($sql)){
				foreach($results as $r){
					$p[] = $r->ID;
				}
			}
			break;
		case 'sc'://제목 및 내용 검색
			$sql = "select ID from {$wpdb->prefix}posts where post_type = 'board' and post_status = 'publish' and (post_content like '%$q%' or post_title like '%$q%')";
			if($results = $wpdb->get_results($sql)){
				foreach($results as $r){
					$p[] = $r->ID;
				}
			}

			break;
		case 'a'://작성자
			$sql = "select ID from {$wpdb->prefix}posts where post_type = 'board' and post_status = 'publish' and (post_author in (select ID from {$wpdb->prefix}users where user_login like '%$q%' or display_name like '%$q%') or ID in (select post_id from {$wpdb->prefix}postmeta where meta_key = 'guest_info' and meta_value like '%$q%')) ";
			if($results = $wpdb->get_results($sql)){
				foreach($results as $r){
					$p[] = $r->ID;
				}
			}
			break;
	}
	return $p;
}
?>