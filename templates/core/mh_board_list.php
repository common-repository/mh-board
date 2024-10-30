<?php
/**
 * 숏코드용 게시판 리스트 템플릿
 */
global $board_cat;
$q = empty($_GET['q']) ? '' : $_GET['q'];
$file = '';

$board_setting = get_board_settings($board_cat);
?>
<div id="mh-board" class="content clearfix">
	<?php do_action('mh_board_header');?>
	<?php 
	$mh_board_options = get_option('mh_board_options');
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
	<?php global $pagename;?>
	<?php mh_board_search_form();?>
	<table class="mh_board_list">
	<colgroup>
		<?php mh_board_colgroup($board_setting,$mh_board_options);?>
	</colgroup>
	<thead>
		<tr class="mh_b_header">		
			<?php mh_board_head($board_setting,$mh_board_options);?>		
		</tr>
	</thead>
	<?php
	$redirect_uri = @$_SERVER['REDIRECT_URL'];
	$args= array (
		'post_type' => array('board'),
		'post_status' => array('publish','private'),
		'posts_per_page'=>5,
		'paged'=>1,
		'orderby' =>'post_date',
		'order' => 'DESC',
		'board_cat'=>	@$board_cat,
		'meta_key'=>'mh_board_notice',
		'meta_value'=>'1',

	);
	$mh_query = new WP_Query($args);
	if(!$board_cat){
		$total = " class='current current-menu-item selected'";
	}
	?>
	<?php
	$afterdate = strtotime('+2 day',strtotime(get_the_date('Y/m/d')));
	$notime = time();
	$new = '';
	if($notime <= $afterdate){
		$new = " <span class='new'>N</span>";
	}
	?>
	<?php if ( $mh_query->have_posts() ) : ?>
		<?php while ( $mh_query->have_posts() ) : $mh_query->the_post(); ?>
			<?php $category = wp_get_object_terms(get_the_ID(),'board_cat');?>
				<tr>
					<?php if(is_mh_board_head('no',$board_setting)):?>
					<td class="mh_b_no"><?php echo __('Notice' ,'mhboard');?></td>
					<?php endif;?>
					<?php if(@$mh_board_options['mh_category'] != 1):?>	
					<td class="mh_b_category"><?php echo $category[0]->name;?></td>
					<?php endif;?>
					<?php if(is_mh_board_head('title',$board_setting)):?>
					<td class="mh_b_title">
					<a href="<?php the_permalink();?>"><?php the_title(); ?>
					<?php if(get_comments_number() > 0){
						echo  "[".get_comments_number()."]";
					}?>
					</a><?php echo $new;?>
				</td>
				<?php endif;?>
				<?php if(is_mh_board_head('writer',$board_setting)):?>
				<td class="mh_b_author"><?php mh_author();?></td>
				<?php endif;?>
				<?php if(is_mh_board_head('date',$board_setting)):?>
				<td class="mh_b_date"><?php echo get_the_date('Y/m/d');?></td>
				<?php endif;?>
				<?php if(is_mh_board_head('count',$board_setting)):?>
				<td class="mh_b_count"><?php echo $mh_board->get_count(get_the_ID());?></td>
				<?php endif;?>
				<?php if(is_mh_board_head('file',$board_setting)):?>
				<td class="mh_b_file"><?php echo $file;?></td>
				<?php endif;?>
				</tr>
		<?php endwhile; ?>
	<?php endif;?>
	<?php
	$redirect_uri = @$_SERVER['REDIRECT_URL'];
	$posts_per_page = empty($mh_board_options['mh_posts_per_page']) ? '10' : $mh_board_options['mh_posts_per_page'];
	$args= array (
		'post_type' => array('board'),
		'post_status' => array('publish','private'),
		'paged'=>$paged,
		'orderby' =>'post_date',
		'order' => 'DESC',
		'board_cat'=>	@$board_cat,
		'post_parent' => 0,
		'posts_per_page'=> $posts_per_page
		//'meta_key'=>'mh_board_notice',
		//'meta_value'=>'0',

	);
	$t = empty($_GET['t']) ? '' : $_GET['t'];
	
	if($q){
		//$args['s'] = $q;
		if($t){
			$p = get_mh_board_search_board_id($t,$q);
			$args['post__in'] = $p;
		}
	}
	global $mh_query;
	$mh_query = new WP_Query($args);
	if(!$board_cat){
		$total = " class='current current-menu-item selected'";
	}
	
	?>
	<?php if ( $mh_query->have_posts() ) : ?>
		<?php while ( $mh_query->have_posts() ) : $mh_query->the_post(); ?>
			<?php $category = wp_get_object_terms(get_the_ID(),'board_cat');?>
			<?php
				$afterdate = strtotime('+2 day',strtotime(get_the_date()));
				$notime = time();
				$new = '';
				if($notime <= $afterdate){
					$new = " <span class='new'>N</span>";
				}
			?>
				<tr>
					
					<?php if(is_mh_board_head('no',$board_setting)):?>
					<td class="mh_b_no"><?php the_board_ID();?></td>
					<?php endif;?>
					<?php if(@$mh_board_options['mh_category'] != 1):?>	
					<td class="mh_b_category"><?php echo $category[0]->name;?></td>
					<?php endif;?>
					<?php if(is_mh_board_head('title',$board_setting)):?>
					<td class="mh_b_title">
					<a href="<?php the_permalink();?>"><?php the_board_title();?>
					<?php if(get_comments_number() > 0){
						echo  "[".get_comments_number()."]";
					}?>
					</a><?php echo $new;?>
				</td>
				<?php endif;?>
				<?php if(is_mh_board_head('writer',$board_setting)):?>
				<td class="mh_b_author"><?php mh_author();?></td>
				<?php endif;?>
				<?php if(is_mh_board_head('date',$board_setting)):?>
				<td class="mh_b_date"><?php echo get_the_date('Y/m/d');?></td>
				<?php endif;?>
				<?php if(is_mh_board_head('count',$board_setting)):?>
				<td class="mh_b_count"><?php echo $mh_board->get_count(get_the_ID());?></td>
				<?php endif;?>
				<?php if(is_mh_board_head('file',$board_setting)):?>
				<td class="mh_b_file"><?php echo $file;?></td>
				<?php endif;?>
				</tr>
				<?php
				$args= array (
					'post_type' => array('board'),
					'post_status' => array('publish','private'),
					'posts_per_page'=>10,
					'orderby' =>'post_date',
					'order' => 'ASC',
					'board_cat'=>	@$board_cat,
					'post_parent' => get_the_ID()
			
				);
				$query = new WP_Query($args);
				if(!$board_cat){
					$total = " class='current current-menu-item selected'";
				}

				?>
				<?php if ( $query->have_posts() ) : ?>
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<?php $category = wp_get_object_terms(get_the_ID(),'board_cat');?>
						<tr>
							<td class="mh_b_no"></td><?php if(@$mh_board_options['mh_category'] != 1):?>	
							<td class="mh_b_category"></td>
						<?php endif;?>
						<td class="mh_b_title"><a href="<?php the_permalink();?>">└ Re:<?php the_title(); ?>[<?php echo  get_comments_number();?>]</a></td><td class="mh_b_author"><?php mh_author();?></td><td class="mh_b_date"><?php echo get_the_date('Y/m/d');?></td><td class="mh_b_count"><?php echo $mh_board->get_count(get_the_ID());?></td>
						</tr>
						<?php endwhile; ?>
					<?php endif;?>
		<?php endwhile; ?>
	<?php else:?>
		<tr>
			<td colspan="5"><?php echo __('No Results','mhboard');?>
		</tr>
	<?php endif;?>
	</table>
	<div class="btnarea">
		<?php mh_board_write_btn();?>
	</div>
	<div class="pagenavi">
	<?php
	mh_pagenavi();
	?>
	</div>
</div>