(function($) {
    'use strict';

    $(function() {

        // My Jobs Tabs
        $('.st-tabs .st-tab-links a').on('click', function(e) {
            var currentAttrValue = $(this).attr('href');
            // Show/Hide Tabs
            $('.st-tabs ' + currentAttrValue).siblings().slideUp(400);
            $('.st-tabs ' + currentAttrValue).delay(400).slideDown(400);
            // Change/remove current tab to active
            $(this).parent('li').addClass('st-active').siblings().removeClass('st-active');
            e.preventDefault();
        });

        $('#st-cart-btn').click(function(e){
            e.preventDefault();
            var postID = document.getElementById('stCartPostID').value,
                data = {},
                responseTxt = '';
            
            $.ajax({
                // /wp-admin/admin-ajax.php
                url: stCartAjaxObejct.admin_ajax_url,
                // Add action and nonce to our collected data
                data: {
                    'action': 'st_translation_cart_ajax',
                    'nonce_security': stCartAjaxObejct.st_cart_nonce,
                    'postID': postID
                },
                beforeSend: function() {
                    $('#st-cart-btn').hide();
                    $('.st-cart-update img').show();
                    $('.st-cart-update p').hide();
    
                },
                success: function( response ) {
                    if( response.data.isResponse ) {
                        $('.st-cart-update img').hide();
                        $('#st-cart-btn').remove();
                        $('.st-cart-update').html(''+stCartAjaxObejct.successResponse );
                        
                    } else {
                        $('.st-cart-update img').hide();
                        $('.st-cart-update p').html( stCartAjaxObejct.errorResponse );
                        $('.st-cart-update p').show();
                        $('#st-cart-btn').show();
                        
                    }   
                }
            });
        });

        $("#st-lang-url-err-msg").click(function() {
            $('.lang-error').css("display", "block");
        });

    });
    

})(jQuery);
