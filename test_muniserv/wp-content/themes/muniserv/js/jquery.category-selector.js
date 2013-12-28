jQuery(document).ready(function($) {
    $('#new_post, #edit_post').on('submit', function(){
        if ($(this).parsley('validate')) {
            $('#consultant_categories option').prop('selected', true);
        }
    });
    
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
    
    $('#cat-add').on('click', function(){        
        if ($('#consultant_categories option').size() < maxcats) {
            $('#consultant_subcatlist').children(':selected').remove().appendTo('#consultant_categories');
            $('#consultant_categories option').prop('selected', false);
        }
    });
    
    $('#consultant_subcatlist').on('dblclick', function() {
        $('#cat-add').trigger('click');
    });
    
    $('#cat-del').on('click', function(){
        $('#consultant_categories').children(':selected').remove();
        $('#consultant_catlist').trigger('change');
    });
    
    $('#consultant_categories').on('dblclick', function() {
        $('#cat-del').trigger('click');
    });
    
    //Trigger catlist change to prepopulate the corresponding subcategories
    $('#consultant_catlist').trigger('change');
    $('#consultant_categories option').prop('selected', false);
});