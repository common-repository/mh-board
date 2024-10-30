<?php $categories = get_terms('board_cat',array('hide_empty'=>0));?>
<p>
	<label for="board_category"><?php echo __('Board Category');?> : </label>
	<select name="board_category" id="board_category">
	<?php foreach($categories as $category):?>
		<option value="<?php echo $category->term_id;?>"><?php echo $category->name;?></option>
	<?php endforeach;?>
	</select>
</p>