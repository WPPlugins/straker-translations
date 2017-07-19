(function($) {
    'use strict';
    var stTranslationCart = {
        start: function() {
            var timer,
                delay = 500;
            		// Pagination links, sortable link
            $('.st-order-tbl-nav-pages a, .st-order-sort-column.sortable a, .st-order-sort-column.sorted a').on('click', function(e) {
                // We don't want to actually follow these links
                e.preventDefault();
                // Simple way: use the URL to extract our needed variables
                var query = this.search.substring( 1 );

                var data = {
                    paged: stTranslationCart.__query( query, 'paged' ) || '1',
                    order: stTranslationCart.__query( query, 'order' ) || 'asc',
                    orderby: stTranslationCart.__query( query, 'orderby' ) || 'post_title',
                    'yoast_data_checked': $('#st-yoast-check').is(':checked'),
                    'acf_data_checked': $('#st-acf-check').is(':checked'),
                };
                stTranslationCart.update( data );
            });
            // Page number input
            $('.st-trans-order-paged').on('keyup', function(e) {
                if ( 13 == e.which )
                    e.preventDefault();

                // This time we fetch the variables in inputs
                var data = {
                    paged: parseInt( $('input[name=paged]').val() ) || '1',
                    order: $('input[name=order]').val() || 'asc',
                    orderby: $('input[name=orderby]').val() || 'post_title',
                    'yoast_data_checked': $('#st-yoast-check').is(':checked'),
                    'acf_data_checked': $('#st-acf-check').is(':checked'),
                };
                window.clearTimeout( timer );
                timer = window.setTimeout(function() {
                    stTranslationCart.update( data );
                }, delay);
            });
            $('.st-delete-cart-item').click(function(e){
                // We don't want to actually follow these links
                e.preventDefault();
                var postID = this.id,
                    noOfItems = document.getElementsByClassName('st-total-items'),
                    currentRow = $('#tr-'+ this.id),
                    totalItems = $('.st-delete-cart-item').length;

                $.confirm({
                    title: 'Remove Item!',
                    content: 'Are you sure you want to remove this item from cart?',
                    type: 'red',
                    container: 'body',
                    closeIcon: true,
                    buttons: {
                        removeBtn: {
                            text: 'Remove',
                            btnClass: 'btn-red',
                            action: function(){
                                $.ajax({

                                    url: stCartAjaxObejct.admin_ajax_url,
                                    // Add action and nonce to our collected data
                                    data: {
                                        'action': 'st_translation_cart_remove_item_ajax',
                                        'nonce_security': stCartAjaxObejct.st_cart_nonce,
                                        'postID': postID,
                                    },
                                    success: function( response ) {
                                        if( response.data.isResponse ) {
                                            var updatedData = {
                                                paged: parseInt( $('input[name=paged]').val() ) || '1',
                                                order: $('input[name=order]').val() || 'asc',
                                                orderby: $('input[name=orderby]').val() || 'post_title',
                                                'yoast_data_checked': $('#st-yoast-check').is(':checked'),
                                                'acf_data_checked': $('#st-acf-check').is(':checked'),
                                            };
                                            noOfItems[0].innerHTML = noOfItems[0].innerHTML - 1;
                                            currentRow.css({"backgroundColor" : "red", "font-weight":"bold"});
                                            currentRow.slideUp(500,function() {
                                                currentRow.remove();
                                            });
                                            $('input[id="post_page-'+ postID +'"]').remove();
                                            stTranslationCart.update( updatedData );
                                            if ( totalItems <= 1 ){
                                                $('#submit').remove();
                                                $('#clear_trans_cart_btn').remove();
                                            }
                                        }
                                    }
                                });
                            },
                        },
                        close: function () {
                        }
                    }
                });
                return false;
            });
            // Show Yoast Data Image
            $('#st-yoast-check').click(function() {
                if( $(this).is(':checked')) {
                    $('.column-st_post_yoast_data').show();
                    $('.st_yoast_img').show();
                } else {
                    
                    $('.column-st_post_yoast_data').hide();
                    $('.st_yoast_img').hide();
                }
            });
            // Show ACF Data Image
            $('#st-acf-check').click(function() {

                if( $(this).is(':checked')) {
                    $('.column-st_post_acf_data').show();
                    $('.st_acf_img').show();
                    
                } else {
                    $('.column-st_post_acf_data').hide();
                    $('.st_acf_img').hide();
                }
            });
            if ( ! $('#st-yoast-check').is(':checked') ) {
                $('.column-st_post_yoast_data').hide();
            } 

            if( !  $('#st-acf-check').is(':checked') ) {
                $('.column-st_post_acf_data').hide();
            }
            // Request Quote Language at least one language selector
            $("#st_translation_request_quote").validate({
                rules: {
                    'tl[]': {
                        required: true,
                        minlength: 1
                    },
                    name: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    }
                },
                messages: {
                    'tl[]': {
                        required: "Please select at least one language.<br />"
                    },
                    name: {
                        required: "Name field is required.<br />"
                    },
                    email: {
                        required: "Email field is required.<br />",
                        email:"Please enter a valid email address.<br />"
                    },
                },
                errorPlacement: function(error, element) {
                    error.appendTo('#tagline-description');
                }
            });
        },
        update: function( data ) {
            
            $.ajax({
                // /wp-admin/admin-ajax.php
                url: ST_Trans_Cart.admin_ajax_url,
                // Add action and nonce to our collected data
                data: $.extend(
                    {
                        '_st_trans_cart_oredr_ajax_nonce': $('#_st_trans_cart_oredr_ajax_nonce').val(),
                        'action': 'st_cart_order_list_table_ajax',
                    },
                    data
                ),
                beforeSend: function() {
                    $('.top .bulkactions').append( '<div class="page-content" id="loader">'+ST_Trans_Cart.content+'<img src="' + ST_Trans_Cart.imgsrc + '"/></div>' );
                },
                // Handle the successful result
                success: function( response ) {
                    $('.top .bulkactions #loader').remove();
                    var response = $.parseJSON( response );

                    // Add the requested rows
                    if ( response.rows.length )
                        $('#the-list').html( response.rows );
                    // Update column headers for sorting
                    if ( response.column_headers.length )
                        $('thead tr, tfoot tr').html( response.column_headers );
                    // Update pagination for navigation
                    if ( response.pagination.bottom.length )
                        $('.tablenav.top .tablenav-pages').html( $(response.pagination.top).html() );
                    if ( response.pagination.top.length )
                        $('.tablenav.bottom .tablenav-pages').html( $(response.pagination.bottom).html() );

                    // Init back our event handlers
                    stTranslationCart.start();
                }
            });
        },
        __query: function( query, variable ) {
		var vars = query.split("&");
            for ( var i = 0; i <vars.length; i++ ) {
                var pair = vars[ i ].split("=");

                if ( pair[0] == variable )
                    return pair[1];
            }
            return false;
        },
    };
    stTranslationCart.start();

})(jQuery);