<?php
	$redirect_uri = $_SERVER['REDIRECT_URL'];
	$paged = empty($_GET['page']) ? '1' : $_GET['page'];
	$args= array (
		'post_type' => array('board'),
		'post_status' => 'publish',
		'posts_per_page'=>10,
		'paged'=>$paged,
		'orderby' =>'post_date',
		'order' => 'DESC',
		'board-cat'=>@$_GET['board-cat']
	);
	$wp_query = new WP_Query($args);
	?>
	<table cellpadding="0" cellspacing="0" class="board">
	<tr class="mh_b_header">
		<th width="70px">번호</th><th>제목</th><th>글쓴이</th><th>날짜</th><th width="70px">조회</th>
	</tr>
	<?php
	?>
	<?php if ( $wp_query->have_posts() ) : ?>
		<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
				<tr>
					<td><?php the_ID();?></td><td class="title"><a href="<?php the_permalink();?>"><?php the_title(); ?></a></td><td><?php the_author();?></td><td><?php echo get_the_date();?></td><td><?php echo $this->get_count(get_the_ID());?></td>
				</tr>
		<?php endwhile; ?>
	<?php endif;
	?>
	</table>
	<div class="button"><a href="<?php echo $redirect_uri;?>/?t=write">글쓰기</a></div>
	<?php
	mh_pagenavi();
	?>
