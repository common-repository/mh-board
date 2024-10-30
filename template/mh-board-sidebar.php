<?php
$categories = get_terms('board-cat',array('orderby'=>'id','order'=>'ASC','hide_empty'=>0));
$redirect_uri = $_SERVER['REDIRECT_URL'];
?>
<ol>
	<li><a href="<?php echo $redirect_uri;?>">전체보기</a></li>
	<ul>
		<?php foreach($categories as $category):?>
		<li><a href="<?php echo $redirect_uri."/?board-cat=".$category->slug;?>"><?php echo $category->name;?></a></li>
		<?php endforeach;?>
	</ul>
</ol>