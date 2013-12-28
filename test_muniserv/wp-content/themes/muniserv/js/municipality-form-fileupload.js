jQuery(document).ready(function($) {
    'use strict';
    var url = ajax_object.ajaxurl;
    // Profile Photo Upload
    $('#municipality_profile_image_box .fileupload').fileupload({
        url: url,
        formData: [{name: 'action', value: 'municipality_profile_image'}],
        dataType: 'json',
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 5000000, // 5 MB
        dropZone: $('#municipality_profile_image_box')
    }).on('fileuploadprocessalways', function (e, data) {
        var file = data.files[0];
        if (file.error) {
        	$('#municipality_profile_image_box .files-error').html(file.error);
        }
    }).on('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#municipality_profile_image_box .progress .progress-bar').css('width',progress + '%');
    }).on('fileuploaddone', function (e, data) {
    	var file = data.result.files[0];
        $('#municipality_profile_image_box .files img').attr('src', file.url);
        $('#municipality_profile_image').val(file.name);
        $('#municipality_profile_image_box .progress .progress-bar').css('width','');
    }).on('fileuploadfail', function (e, data) {
    	var file = data.result.files[0];
    	$('#municipality_profile_image_box .files-error').html(file.error);
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
});