<?php 
global $mh_board_options;
if(@$mh_board_options['mh_category'] != 1):?>
<div id="menu" class="clearfix">
	<ul>
		<li><a href="<?php echo get_post_type_archive_link('board');?>">전체</a></li>
		<?php
		$categories = @ get_terms('board_cat',array('orderby'=>'id','order'=>'ASC','hide_empty'=>0));
		if(is_array($categories)){
			foreach($categories as $category){
				echo '<li><a href="'.get_term_link($category->slug, 'board_cat').'">'.$category->name.'</a></li>';
			}
		}
		?>			
	</ul>
</div>
<?php endif;?>