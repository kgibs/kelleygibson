jQuery(document).ready(function($) {
    $('.clr-crit button').click(function(){
        var elname = $(this).attr('rel');
        if (elname != '' && elname != 'alpha')
            $('#'+elname).val('');
        $('#con-search-form').submit();
    });
});