jQuery(document).ready(function($) {
    /*
     * VALIDATION
     */
    $('#new_post,#edit_post').parsley();
    
    /*
     * CATEGORY SELECTOR
     */
    // Select chosen categories on submit
    $('#new_post,#edit_post').on('submit', function(){
        if ($(this).parsley('validate')) {
            $('#consultant_categories option').prop('selected', true);
        }
    });
    
    // Populate subcategories for chosen category
    $('#consultant_catlist').on('change', function(){
		var subcats = eval("cats.c" + $(this).val() + ".subcats");
		if(subcats) {
            var selSubcatlist = $('#consultant_subcatlist');
            selSubcatlist.empty();
            //Compile subcat list and don't show any that are already selected
            $.each(subcats, function(index, value){
                if ($('#consultant_categories option[value="'+value.id+'"]').length == 0) {
                    selSubcatlist.append($('<option>', { 
                        value: value.id,
                        text : value.name
                    }));
                }
            });
		}
    });
    
    // Add subcategory to chosen list provided list isn't already at the max allowed
    $('#cat-add').on('click', function(){        
        if ($('#consultant_categories option').size() < maxcats) {
            $('#consultant_subcatlist').children(':selected').remove().appendTo('#consultant_categories');
            $('#consultant_categories option').prop('selected', false);
        }
    });
    // Double-click add functionality
    $('#consultant_subcatlist').on('dblclick', function() {
        $('#cat-add').trigger('click');
    });
    // Remove subcategory from chosen list
    $('#cat-del').on('click', function(){
        $('#consultant_categories').children(':selected').remove();
        $('#consultant_catlist').trigger('change');
    });
    // Double-click remove functionality
    $('#consultant_categories').on('dblclick', function() {
        $('#cat-del').trigger('click');
    });
    
    // Trigger catlist change on load to prepopulate the corresponding subcategories of default category and deselect current chosen categories
    $('#consultant_catlist').trigger('change');
    $('#consultant_categories option').prop('selected', false);
    /*
     * END CATEGORY SELECTOR
     */
    
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
    
    /*
     * ADVERTISEMENT PREVIEW
     */
    $('#consultant_title, #consultant_ad_title').on('keyup', function(){
        var adTitle = $('#consultant_ad_image_box .consultant-ad-title');
        if($('#consultant_ad_title').val() != '') {
            adTitle.html($('#consultant_ad_title').val());
        } else {
            adTitle.html($('#consultant_title').val());
        }
    });
    $('#consultant_tagline, #consultant_ad_subtitle').on('keyup', function(){
        var adSubTitle = $('#consultant_ad_image_box .consultant-ad-subtitle');
        if($('#consultant_ad_subtitle').val() != '') {
            adSubTitle.html($('#consultant_ad_subtitle').val());
        } else {
            adSubTitle.html($('#consultant_tagline').val());
        }
    });
    $('#consultant_description, #consultant_ad_blurb').on('keyup', function(){
        var adBlurb = $('#consultant_ad_image_box .consultant-ad-blurb');
        var blurbOverride = $('#consultant_ad_blurb');
        if(blurbOverride.val() != '') {
            if(blurbOverride.val().length > 230) { 
                blurbOverride.val(blurbOverride.val().slice(0, 230)); 
            } else {
                adBlurb.html(snippet(strip_tags(blurbOverride.val()), 230));
            }
        } else {
            adBlurb.html(snippet(strip_tags($('#consultant_description').val()), 230));
        }
    });
    /*
     * END ADVERTISEMENT PREVIEW
     */
    
    /*
    * Gets a snippet from a longer text based on length and won't break up words.
    * @return string text
    */
    function snippet(text,length,tail) {
       length = typeof length !== 'undefined' ? length : 64;
       tail = typeof tail !== 'undefined' ? tail : '...';
       text = $.trim(text);
       if(text.length > length) {
           for(var i=1;text.charAt(length-i)!=' ';i++) {
               if(i == length) {
                   return text.substr(0,length) + tail;
               }
           }
           text = text.substr(0,length-i+1) + tail;
       }
       return text;
    }
    /*
    * Strips all html tags from the text
    * @return string text
    */
    function strip_tags (input, allowed) {
        allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
        var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
            commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
        return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
            return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
        });
    }
});