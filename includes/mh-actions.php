<?php

add_action( 'template_redirect',       'mh_template_redirect',      10    );

add_action( 'mh_template_redirect', 'mh_screens', 	     6 );

function mh_template_redirect(){
	do_action( 'mh_template_redirect' );
}
function mh_screens() {
	do_action( 'mh_screens' );
}
?>