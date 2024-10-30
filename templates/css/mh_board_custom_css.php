<?php
header("Content-type: text/css");
require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-load.php');
$mh_board_style_options = get_option('mh_board_style_options');
?>
#mh-board .button,
#mh-board-write .button{
<?php
if(isset($mh_board_style_options['button_background'])){
	echo "background-color:{$mh_board_style_options['button_background']};";
}
if(isset($mh_board_style_options['button_color'])){
	echo "color:{$mh_board_style_options['button_color']};";
}
?>
}
<?php 
$mh_board_custom_css = get_option('mh_board_custom_css');
if($mh_board_custom_css){
	echo $mh_board_custom_css;
}
?>