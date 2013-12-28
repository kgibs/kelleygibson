jQuery(document).ready(function($) {
    'use strict';
    var url = ajax_object.ajaxurl;
    // Profile Photo Upload
    $('#consultant_profile_image_box .fileupload').fileupload({
        url: url,
        formData: [{name: 'action', value: 'consultant_profile_image'}],
        dataType: 'json',
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 5000000, // 5 MB
        dropZone: $('#consultant_profile_image_box')
    }).on('fileuploadprocessalways', function (e, data) {
        var file = data.files[0];
        if (file.error) {
        	$('#consultant_profile_image_box .files-error').html(file.error);
        }
    }).on('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#consultant_profile_image_box .progress .progress-bar').css('width',progress + '%');
    }).on('fileuploaddone', function (e, data) {
    	var file = data.result.files[0];
        $('#consultant_profile_image_box .files img').attr('src', file.thumbnailUrl);
        $('#consultant_profile_image').val(file.name);
        $('#consultant_profile_image_box .progress .progress-bar').css('width','');
        $('#consultant_ad_image_box .consultant-ad-image img').attr('src', file.url);
        $('#consultant_ad_image_box .consultant-ad-image').show();
    }).on('fileuploadfail', function (e, data) {
    	var file = data.result.files[0];
    	$('#consultant_profile_image_box .files-error').html(file.error);
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');

    // Sidebar Photo Upload
    $('#consultant_sidebar_image_box .fileupload').fileupload({
        url: url,
        formData: [{name: 'action', value: 'consultant_profile_image'}],
        dataType: 'json',
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 5000000, // 5 MB
        dropZone: $('#consultant_sidebar_image_box')
    }).on('fileuploadprocessalways', function (e, data) {
        var file = data.files[0];
        if (file.error) {
        	$('#consultant_sidebar_image_box .files-error').html(file.error);
        }
    }).on('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#consultant_sidebar_image_box .progress .progress-bar').css('width',progress + '%');
    }).on('fileuploaddone', function (e, data) {
    	var file = data.result.files[0];
        $('#consultant_sidebar_image_box .files img').attr('src', file.url);
        $('#consultant_sidebar_image').val(file.name);
        $('#consultant_sidebar_image_box .progress .progress-bar').css('width','');
    }).on('fileuploadfail', function (e, data) {
    	var file = data.result.files[0];
    	$('#consultant_sidebar_image_box .files-error').html(file.error);
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');

    // Advertisement Override Image Upload
    $('#consultant_ad_image_box .fileupload').fileupload({
        url: url,
        formData: [{name: 'action', value: 'consultant_ad_image'}],
        dataType: 'json',
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 5000000, // 5 MB
        dropZone: $('#consultant_ad_image_box')
    }).on('fileuploadprocessalways', function (e, data) {
        var file = data.files[0];
        if (file.error) {
        	$('#consultant_ad_image_box .files-error').html(file.error);
        }
    }).on('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#consultant_ad_image_box .progress .progress-bar').css('width',progress + '%');
    }).on('fileuploaddone', function (e, data) {
    	var file = data.result.files[0];
        $('#consultant_ad_image_box .files img').attr('src', file.url);
        $('#consultant_ad_image_box .files').show();
        $('#consultant_ad_image_box .consultant-adspace').hide();
        $('#consultant_ad_image').val(file.name);
        $('#consultant_ad_image_box .progress .progress-bar').css('width','');
        $('#consultant_ad_image_box .remove-button').show();
    }).on('fileuploadfail', function (e, data) {
    	var file = data.result.files[0];
    	$('#consultant_ad_image_box .files-error').html(file.error);
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
    $('#consultant_ad_image_box .remove-button').click(function(){
        $('#consultant_ad_image_box .files').hide();
        $('#consultant_ad_image_box .consultant-adspace').show();
        $('#consultant_ad_image_box .remove-button').hide();
        $('#consultant_ad_image').val('');
    });
});