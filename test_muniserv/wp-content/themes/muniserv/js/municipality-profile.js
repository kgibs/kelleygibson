jQuery(document).ready(function($) {
    /*
     * VALIDATION
     */
    $('#new_post,#edit_post').parsley();
    
    /*
     * RESTRICT TEXTBOX MAXLENGTH
     */
    $('textarea[maxlength]').keyup(function(){
		var el = $(this);
		var limit = parseInt(el.attr('maxlength'));
		if(el.val().length > limit) {
			el.val(el.val().substr(0, limit));
		}
	});
  
});