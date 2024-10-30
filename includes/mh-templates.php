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
function mh_board_write_btn(){
	global $mh_board;
	if($mh_board->current_write_permission()){
	
?>
<a href="<?php echo wp_nonce_url(get_mh_board_write_link(),'_mh_board_nonce');?>&redirect_to=<?php echo urlencode(get_mh_board_list_link());?>" class="btn button"><?echo __('Write');?></a>
<?php	
	}
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
<div class="mh_search">
	<form id="mh_search_frm" action="<?php echo $action;?>" method="get">
		<select class="search_type" name="t">
			<option value="s"<?php if($t == 's'){echo " selected";}?>><?php echo __('Title',',mhboard');?></option>
			<option value="c"<?php if($t == 'c'){echo " selected";}?>><?php echo __('Content',',mhboard');?></option>
			<option value="sc"<?php if($t == 'sc'){echo " selected";}?>><?php echo __('Title',',mhboard');?>+<?php echo __('Content',',mhboard');?></option>
			<option value="a"<?php if($t == 'a'){echo " selected";}?>><?php echo __('Author',',mhboard');?></option>
		</select>
		<input type="text" name="q" value="<?php echo $q;?>">
		<?php echo $hidden;?>
		<a href="javascript:;" onclick="" class="button btn"><?php echo __('Search');?></a>
	</form>
</div>
<?php	
}
function mh_board_colgroup($setting,$option){
	if(empty($setting['list'])){
?>
		<col width="30px">
		<?php if(@$option['mh_category'] != 1):?>
		<col width="">
		<?php endif;?>
		<col width="40%">
		<col width="">
		<col width="">
		<col width="">
		<col width="">
		<col width="">
<?php
	}else{
		$setting = $setting['list'];
?>
		<?php if(in_array('no',$setting)):?>
		<col width="30px">
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
		<col width="">
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
?>