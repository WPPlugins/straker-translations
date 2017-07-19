(function($) {
	'use strict';
    $(".resx-uploader").on("change", function(e) {

        e.preventDefault();
        var $form = $(e.target),
            formData = new FormData(),
            params = $form.serializeArray(),
            files = $('.resx-uploader').val();
        var file_id = this.id;
        var file_val = $("#" + file_id).val();
        var main_file = $("#" + file_id)[0].files[0]
        var fileExtension = file_val.substr((file_val.lastIndexOf('.') + 1));
        switch (fileExtension) {
            case 'xml':
                var re_response = confirm('This will replace your current translated content.');
                if (re_response) {
                    formData.append('action', 'straker_replace_translated_post');
                    formData.append('resxFile', main_file);
                    formData.append('security', ST_Import_Resx.security);
                    formData.append('post_id', file_id);
                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        dataType: 'json',
                        data: formData,
                        contentType: "application/json; charset=utf-8",
                        processData: false,
                        success: function(response) {

                            if( response.success === true )
                            {
                            $("input#" + file_id).removeClass('fileUpload-error');
                            $("input#" + file_id).addClass('fileUpload-success');
                            if( $('#st-msg .error').length) {
                                $('#st-msg div.message').removeClass( "error" );
                                $('#st-msg div.message').addClass( "updated" );
                                $("#st-msg div.message p").text(response.data);
                            } else {
                                $("#st-msg").append('<div class="message updated"><p>'+response.data+'</p>');
                            }

                            } else
                            {
                            $("input#" + file_id).removeClass('fileUpload-success');
                            $("input#" + file_id).addClass('fileUpload-error');
                            if(! $('#st-msg .error').length) {
                                if( $('#st-msg div.message.updated').length ) {
                                $('#st-msg div.message').removeClass( "updated" );
                                $('#st-msg div.message').addClass( "error" );
                                $("#st-msg div.message p").text(response.data);
                                } else {
                                    $("#st-msg").append('<div class="message error"><p>'+response.data+'</p>');
                                }
                            } else {
                                $("#st-msg div.message p").text(response.data);
                            }
                            }
                        },
                    });
                }
                break;

            default:
                $("input#" + file_id).removeClass('fileUpload-success');
                $("input#" + file_id).addClass('fileUpload-error');
                if(! $('#st-msg .error').length) {
                if( $('#st-msg div.message.updated').length ) {
                    $('#st-msg div.message').removeClass( "updated" );
                    $('#st-msg div.message').addClass( "error" );
                    $("#st-msg div.message p").text('Invalid File Type');
                } else {
                    $("#st-msg").append('<div class="message error"><p>Invalid File Type</p>');
                }
                } else {
                $("#st-msg div.message p").text('Invalid File Type');
                }
                break;
        }
    });
})(jQuery);