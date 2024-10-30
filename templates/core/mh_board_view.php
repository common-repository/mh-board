<?php
/**
 * 숏코드용 게시판 뷰 템플릿
 */
global $mh_board,$pagename,$mh_error;
if(isset($_GET['page_id']) && empty($pagename)){
	$pagename = '?page_id='.$_GET['page_id'];
}

do_action('mh_board_read_permission');
if(!$mh_board_options['permission']){
	echo __('Access Denied','mhboard');
	return false;
}
?>

<?php $mh_board_options = get_option('mh_board_options');?>
	<div id="mh-board">
	<?php do_action('mh_board_header');?>
	<?php $mh_board_options = get_option('mh_board_options');
	if(@$mh_board_options['mh_category'] != 1):?>
	<div id="menu" class="clearfix">
		<ul>
			<li><a href="<?php echo $mh_board_link;?>">전체</a></li>
			<?php
			$categories = @ get_terms('board_cat',array('orderby'=>'id','order'=>'ASC','hide_empty'=>0));
			if(is_array($categories)){
				foreach($categories as $category){
					echo '<li><a href="'.$mh_board_link.'board_cat='.$category->slug.'">'.$category->name.'</a></li>';
				}
			}
			?>			
		</ul>
	</div>
	<?php endif;?>
	<table cellpadding="0" cellspacing="0" class="mh_board_view">
	<?php
	$args= array (
		'p'=>get_mh_board_id(),
		'post_type' => array('board'),
		'post_status' => array('publish','private'),
		'posts_per_page'=>5,
		'paged'=>1,
		'orderby' =>'post_date',
		'order' => 'DESC',
		'board_cat'=>	$board_cat,

	);
	$mh_query = new WP_Query($args);
	?>
	<?php if ( $mh_query->have_posts() ) : ?>
		<?php while ( $mh_query->have_posts() ) : $mh_query->the_post(); ?>
			<?php $category =@ wp_get_object_terms(get_the_ID(),'board_cat');?>
			<?php
				$mh_board->current_board_id = get_the_ID();
				$mh_board->current_board_cat = $category[0]->term_id;
				$mh_board->board_template = 'shortcode';
				$author = get_the_author();
				if($author){
					$user_data = get_userdata(get_the_author_meta('ID'));
					$site = $user_data->user_url;
					$email = $user_data->user_email;
				}else{
					$guest_info = get_post_meta(get_the_ID(),'guest_info',true);
					$author = $guest_info['guest_name'];
					$action = 'guest';
					$email = $guest_info['guest_email'];
					$site = $guest_info['guest_site'];
				}				
			?>
			<tr class="thead">
				<th><?php echo __('Title' ,'mhboard');?></th><td colspan="5"><?php the_board_title();?></td>
			</tr>
			<tr>
				<th><?php echo __('Author' ,'mhboard');?></th><td><?php echo $author;?><?php if($site){?>(<a href="http://<?php echo $site;?>"><?php echo "http://".$site;?></a>)<?php }?></td><th><?php echo __('Count' ,'mhboard');?></th><td><?php echo $mh_board->get_count(get_the_ID());?></td><th><?php echo __('Date' ,'mhboard');?></th><td><?php echo get_the_date('Y/m/d');?></td>
			</tr>
			<?php
			$args = array( 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $post->ID );
			$attachments = get_posts($args);
			if($attachments){
			?>
			<tr>
				<th><?php echo __('Attachment' ,'mhboard');?></th>
				<td colspan="5" class="attachment_name">
			<?php
			}
			foreach($attachments as $attachment){
				$_wp_attached_file = get_post_meta($attachment->ID,'_wp_attached_file',true);
				$attachment_ext = end(explode('.', $_wp_attached_file));
			?>
			<a href="<?php echo $attachment->guid;?>" target="_blank"><?php echo $attachment->post_title.'.'.$attachment_ext;?></a><br/>
			<?php
			}
			if($attachments){
			?>
				</td>
			</tr>
			<?php			
			}
			?>
			<tr>
			<td colspan="6" class='content'>
				<?php if(current_user_can('administrator') && isset($post->post_password)):?>
					<?php echo nl2br($post->post_content);?>
				<?php else :?>
					<?php the_content();?></td>
				<?php endif;?>
			</tr>
		<?php endwhile; ?>
	<?php endif;
	?>
		<?php the_before_board_link();?>
		<?php the_after_board_link();?>
	</table>
	<div class="btnarea action clearfix">
		<div class="alignleft">
			
		</div>
		<div class="alignright clearfix">
			<?php if(is_admin()):?>
				<a href="<?php echo mh_get_board_write_link();?>" class="button"><?echo __('Write');?></a>
			<?php endif;?>
			<?php mh_board_list_btn();?>
			
			<?php if((is_user_logged_in() && get_current_user_id() == get_the_author_meta('ID')) || $action == 'guest'):?>
				<?php mh_board_edit_btn(get_the_ID());?>
				<?php mh_board_delete_btn(get_the_ID());?>
			<?php endif;?>
			<?php if($post->post_parent == 0 && @$mh_board_options['mh_replypost'] == 1):
			$redirect_to = @$_SERVER['REQUEST_URI'];
			?>
			<?php mh_board_reply_btn(get_the_ID());?>
			<?php endif;?>
		</div>

		
	</div>
	<div class="pagenavi">
	<?php
	mh_pagenavi();
	?>
	</div>
	<?php 
	
	$mh_comment = @$mh_board_options['mh_comment'];
	$short_link = get_site_url()."/?p=".$post->ID;
	unset($mh_board_options);
	if($mh_comment){
		require_once(dirname(dirname(dirname(__FILE__))).'/templates/comments.php');	
	}else{
		comments_template('',true);
	}
	?>
	</div>
