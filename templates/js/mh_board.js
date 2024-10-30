jQuery(document).ready(function(){
	jQuery('#mh-board-write form#write_board a#board_write').bind('click',function(){
		var write_type = jQuery('#mh-board-write #write_type');
		var guest_name = jQuery('#mh-board-write #guest_name');
		if(jQuery.trim(guest_name.val()) == "" && write_type.val() == 'guest'){
			alert(mh_board.require_name);
			guest_name.focus();
			return false;
		}
		var guest_email = jQuery('#mh-board-write #guest_email');
		if(jQuery.trim(guest_email.val()) == "" && write_type.val() == 'guest'){
			alert(mh_board.require_email);
			guest_email.focus();
			return false;
		}
		var regEmail = /^[0-9a-zA-z]([-_\.]?[0-9a-zA-z])*@[0-9a-zA-z]([-_\.]?[0-9a-zA-z])*\.[a-zA-Z]{2,3}$/i;
		if(jQuery.trim(guest_email.val()).indexOf("@") == -1 && write_type.val() == 'guest'){
			alert(mh_board.require_emailformat);
			guest_email.focus();
			return false;
		}
		if(!regEmail.test(guest_email.val()) && write_type.val() == 'guest'){
			alert(mh_board.require_emailformat);
			guest_email.focus();
			return false;
		}
		var guest_password = jQuery('#mh-board-write #guest_password');
		if(jQuery.trim(guest_password.val()) == "" && write_type.val() == 'guest'){
			alert(mh_board.require_password);
			guest_password.focus();
			return false;
		}
		var post_title = jQuery('#mh-board-write .post_title');
		if(jQuery.trim(post_title.val()) == ""){
			alert(mh_board.require_title);
			post_title.focus();
			return false;
		}
		jQuery('#mh-board-write form#write_board').submit();
		/*var post_content = jQuery('#mh-board-write .post_content');
		if(jQuery.trim(post_content.val()) == ""){
			alert('내용을 입력해주세요.');
			post_content.focus();
			return false;
		}*/
	});
	jQuery('form#mh_search_frm a').bind('click',function(){
		if(jQuery('form#mh_search_frm input').val() == ''){
			alert(mh_board.require_search);
			jQuery('form#mh_search_frm input').focus();
		}else{
			jQuery('form#mh_search_frm').submit();
		}
		
	});
	jQuery('#mh-board-write form#delete_board').submit(function(){
		if(confirm(mh_board.confirm_delete)){
			return true;
		}else{
			return false;
		}
	});
	jQuery('#mh-board a#delete_board').bind('click',function(){
		if(confirm(mh_board.confirm_delete)){
			return true;
		}else{
			return false;
		}
	});
	jQuery('form#mh_board_edit a#mh_edit').bind('click',function(){
		jQuery('form#mh_board_edit').submit();
		return false;
	});
	jQuery('#mh-cancel').click(function(){
		history.back();
	});
	jQuery('form#mh_board_delete a').bind('click',function(){
		if(jQuery('input[name="guest_password"]').val() == ''){
			alert(mh_board.require_password);
			jQuery('input[name="guest_password"]').focus();
			return false;
		}
		jQuery('form#mh_board_delete').submit();
		return false;
	});
	jQuery('#mh-board table,#mh-board-write table').after('<div class="copyright"><a href="http://ssamture.net">powered by ssamture.net</a></div>');
});
