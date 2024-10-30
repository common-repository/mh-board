<?php
//글쓰기 링크 
function mh_board_write_link(){
	echo get_mh_board_write_link();
}
function get_mh_board_write_link(){
	global $wp_rewrite;
	if($wp_rewrite->permalink_structure == ''){
		$board_write_link = get_mh_board_list_link().'&type=write';
	}else{
		$board_write_link = get_mh_board_list_link().'/write';
	}
	return $board_write_link;
}
//글쓰기 버튼
function mh_board_write_btn($args = array()){
	global $mh_board;
	$defaults = array(
			'class'	=> '',
			'str'	=> __('Write','mhboard')
		);
	$args = wp_parse_args( $args, $defaults );
	extract($args);
	if($mh_board->current_write_permission()){
	if(empty($mh_board->current_url)):
?>
<a href="<?php echo wp_nonce_url(get_mh_board_write_link(),'_mh_board_nonce');?>&redirect_to=<?php echo urlencode(get_mh_board_list_link());?>" class="btn button<?php echo " {$class}";?>"><?echo $str;?></a>
<?php	
	else:
		?><a href="<?php echo wp_nonce_url($mh_board->current_url,'_mh_board_nonce');?>&write=1&redirect_to=<?php echo urlencode(get_mh_board_list_link());?>" class="btn button<?php echo " {$class}";?>"><?echo $str;?></a><?php
	endif;
	}
}
//글 삭제 버튼
function mh_board_delete_btn($post_id){
	global $mh_board_link;
	if($mh_board_link)
		$board_link = $mh_board_link;
	else
		$board_link = get_pagenum_link();

	if(isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'],home_url())){
		$mh_board_list_link = urlencode($_SERVER['HTTP_REFERER']);
	}
	
	if(strstr($board_link,'?')){
		$board_link .= '&type=delete&ID='.$post_id;
	}else{
		$board_link .= '/'.$post_id.'/delete';
	}
	echo '<a href="'.wp_nonce_url($board_link,'_mh_board_nonce').'&redirect_to='.$mh_board_list_link.'" id="delete_board" class="btn button">'.__('Delete','mhboard').'</a>';
}
//글 수정 버튼
function mh_board_edit_btn($post_id){
	global $mh_board;
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
	echo '<a href="'.$board_link.'" class="btn button">'.__('Edit','mhboard').'</a>';
}
//답변 버튼
function mh_board_reply_btn($post_id){
	global $mh_board;
	global $mh_board_link;
	if(isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'],home_url())){
		$redirect_to = urlencode($_SERVER['HTTP_REFERER']);
	}
	if($mh_board_link)
		$board_link = $mh_board_link;
	else
		$board_link = get_pagenum_link();
	
	if(strstr($board_link,'?')){
		$board_link .= '&type=reply&ID='.$post_id;
	}else{
		$board_link .= '/'.$post_id.'/reply';
	}
	echo '<a href="'.wp_nonce_url($board_link,'_mh_board_nonce').'&redirect_to='.$redirect_to.'" id="reply_board" class="btn button">'.__('Reply','mhboard').'</a>';
}
//리스트 버튼
function mh_board_list_btn(){
	global $mh_board;
	global $wp_rewrite, $post, $pagename;
	if(isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'],home_url()) && sizeof(explode('/',str_replace(home_url(),'',$_SERVER['HTTP_REFERER'])))<2){
		$mh_board_list_link = $_SERVER['HTTP_REFERER'];
	}else if(is_singular('board') && isset($post->ID)){
		$category = wp_get_object_terms(get_the_ID(),'board_cat');
		$term_link = get_term_link($category[0],'board_cat');
		$mh_board_list_link = $term_link;
		//return $post->ID;
	}else if($wp_rewrite->permalink_structure == '' && isset($_GET['page_id']) && is_numeric($_GET['page_id'])){
		$mh_board_list_link = get_permalink($_GET['page_id']);
	}else{
		$pagename = get_query_var('pagename');
		$mh_board_list_link = site_url($pagename);
	}

	echo '<a href="'.$mh_board_list_link.'" class="btn button">'.__('List').'</a>';
}
//검색 폼
function mh_board_search_form(){
	global $wp_rewrite;
	if($wp_rewrite->permalink_structure == ''){
		$action = site_url();
		$hidden = "<input type='hidden' name='page_id' value='{$_GET['page_id']}'/>";
	}else{
		$action = get_mh_board_list_link().'/';
		$hidden = '';
	}
	$q = empty($_GET['q']) ? '' : $_GET['q'];
	$t = empty($_GET['t']) ? '' : $_GET['t'];
?>
<div class="mh_search"><form id="mh_search_frm" action="<?php echo $action;?>" method="get"><select class="search_type" name="t">
			<option value="s"<?php if($t == 's'){echo " selected";}?>><?php echo __('Title',',mhboard');?></option>
			<option value="c"<?php if($t == 'c'){echo " selected";}?>><?php echo __('Content',',mhboard');?></option>
			<option value="sc"<?php if($t == 'sc'){echo " selected";}?>><?php echo __('Title',',mhboard');?>+<?php echo __('Content',',mhboard');?></option>
			<option value="a"<?php if($t == 'a'){echo " selected";}?>><?php echo __('Author',',mhboard');?></option>
		</select><input type="text" name="q" value="<?php echo $q;?>"><?php echo $hidden;?><a href="javascript:;" onclick="" class="button btn"><?php echo __('Search');?></a></form>
</div>
<?php	
}
function mh_board_colgroup($setting,$option){
	if(empty($setting['list'])){
?>
		<col width="35px">
		<?php if(@$option['mh_category'] != 1):?>
		<col width="">
		<?php endif;?>
		<col width="40%">
		<col width="">
		<col width="">
		<col width="">
		<col width="">
		<col width="50px">
<?php
	}else{
		$setting = $setting['list'];
?>
		<?php if(in_array('no',$setting)):?>
		<col width="35px">
		<?php endif;?>
		<?php if(@$option['mh_category'] != 1):?>
		<col width="">
		<?php endif;?>
		<?php if(in_array('title',$setting)):?>
		<col width="">
		<?php endif;?>
		<?php if(in_array('writer',$setting)):?>
		<col width="">
		<?php endif;?>
		<?php if(in_array('date',$setting)):?>
		<col width="">
		<?php endif;?>
		<?php if(in_array('count',$setting)):?>
		<col width="50px">
		<?php endif;?>
		<?php if(in_array('file',$setting)):?>
		<col width="">
		<?php endif;?>
<?php
	}
}
function mh_board_head($setting,$option){
	if(empty($setting['list'])){
?>
		<th class="mh_b_no"><?php echo __('No' ,'mhboard');?></th>
			<?php if(@$option['mh_category'] != 1):?>
				<th class="mh_b_category"><?php echo __('Category' ,'mhboard');?></th>
			<?php endif;?>
			<th class="mh_b_title"><?php echo __('Title' ,'mhboard');?></th>
			<th class="mh_b_author"><?php echo __('Author' ,'mhboard');?></th>
			<th class="mh_b_date"><?php echo __('Date' ,'mhboard');?></th>
			<th class="mh_b_count"><?php echo __('Count' ,'mhboard');?></th>
			<th class="mh_b_file"><?php echo __('File' ,'mhboard');?></th>
<?php
	}else{
		$setting = $setting['list'];
?>
		<?php if(in_array('no',$setting)):?>
		<th class="mh_b_no"><?php echo __('No' ,'mhboard');?></th>
		<?php endif;?>
		<?php if(@$option['mh_category'] != 1):?>
		<th class="mh_b_category"><?php echo __('Category' ,'mhboard');?></th>
		<?php endif;?>
		<?php if(in_array('title',$setting)):?>
		<th class="mh_b_title"><?php echo __('Title' ,'mhboard');?></th>
		<?php endif;?>
		<?php if(in_array('writer',$setting)):?>
		<th class="mh_b_author"><?php echo __('Author' ,'mhboard');?></th>
		<?php endif;?>
		<?php if(in_array('date',$setting)):?>
		<th class="mh_b_date"><?php echo __('Date' ,'mhboard');?></th>
		<?php endif;?>
		<?php if(in_array('count',$setting)):?>
		<th class="mh_b_count"><?php echo __('Count' ,'mhboard');?></th>
		<?php endif;?>
		<?php if(in_array('file',$setting)):?>
		<th class="mh_b_file"><?php echo __('File' ,'mhboard');?></th>
		<?php endif;?>
<?php
	}
}
function is_mh_board_head($type,$setting){
	if(empty($setting['list'])){
		return true;
	}else if(in_array($type,$setting['list'])){
		return true;
	}else{
		return false;
	}
}
function mh_author(){
	$author = get_the_author();
	if($author){
		
	}else{
		$author = get_post_meta(get_the_ID(),'guest_info',true);
		$author = $author['guest_name'];
	}
	echo apply_filters( 'mh_author', $author );
}
//게시판 페이지내비게이션
function mh_pagenavi( $args = array() ){
	global $mh_query;
	$args['items'] = 5;
	$max_page_num = @$mh_query->max_num_pages;
	$current_page_num = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	//$befores = $current_page_num - floor( ( $args['items'] - 1 ) / 2 );
	//$afters = $current_page_num + ceil( ( $args['items'] - 1 ) / 2 );

	$befores = floor(($current_page_num - 1)/$args['items']) * $args['items'];
	$afters = $befores + $args['items'];
	if($current_page_num == $args['items']){
		$befores = 0;
	}

	if ( $max_page_num <= $args['items'] ) {
		$start = 1;
		$end = $max_page_num;
	} elseif ( $befores <= 1 ) {
		$start = 1;
		$end = $args['items'];
	} elseif ( $afters >= $max_page_num ) {
		//$start = $max_page_num - $args['items'] + 1;
		$start = $befores + 1;
		$end = $max_page_num;
	} else {
		$start = $befores + 1;
		$end = $afters;
	}
	if($start >= 2){
		$previous_num = max( 1, $start - 1 );
?>
		<a href="<?php echo get_pagenum_link();?>" class="pre"><<</a>
		<a href="<?php echo get_pagenum_link( $previous_num );?>" class="pre"><</a>
<?php		
	}
	for ( $i = $start; $i <= $end; $i++ ) {
		if ( $i == $current_page_num ) {
			echo "<strong>{$i}</strong>";
		}else{
			?><a href="<?php echo get_pagenum_link($i);?>"><?php echo $i;?></a><?php
		}
	}	
	if($current_page_num != $max_page_num  && $max_page_num > $args['items']){
		$next_num = min( $max_page_num, $end + 1 );
?>
		<a href="<?php echo get_pagenum_link($next_num);?>" class="next">></a>
        <a href="<?php echo get_pagenum_link( $max_page_num );?>" class="next">>></a>
<?php		
	}
?>

<?php
}
add_action('mh_board_header','mh_board_header');
function mh_board_header(){
	$updated = empty($_GET['updated']) ? '' : $_GET['updated'];
	if($updated != ''){
		$str = '';
		switch($updated){
			case 'registered'://글등록
				$str = __('Post has been registered.','mhboard');
				break;
			case 'updated':
				$str = __('Post has been updated.','mhboard');
				break;
			case 'deleted':
				$str = __('Post has been deleted.','mhboard');
				break;
		}
		if($str != '')
			echo "<div class='updated'><p>{$str}</p></div>";
	}
}
class MH_Template{
	function __construct(){
		//add_filter('the_title',array(&$this,'the_title'),10,2);
		//add_filter('the_content',array(&$this,'the_content'));
		//add_action('pre_get_posts', array(&$this,'change_post_per_page'));
	}
	function the_title($title, $ID){
		global $mh_board,$board_cat;
		$post = get_post($ID);

		if(is_tax($mh_board->board_slug.'_cat') && $post->post_type == $mh_board->board_slug){
			$board_cat = get_term_by('slug',$board_cat,'board_cat');
			return $board_cat->name;
		}
		if(is_post_type_archive($mh_board->board_slug) && $post->post_type == $mh_board->board_slug){
			return __('All Boards','mhboard');
		}
		return $title;
	}
	function the_content($content){
		global $mh_board;
		if(is_singular($mh_board->board_slug)){
			return do_shortcode('[mh_board]');
		}
		return $content;
	}
	function change_post_per_page($query){
		global $mh_board;
		
	if($query->is_post_type_archive($mh_board->board_slug) || $query->is_tax($mh_board->board_slug.'_cat')){
			$query->query_vars['posts_per_page'] = 1;
	    	
		}
		
		return $query;
	}
}
new MH_Template;
?>