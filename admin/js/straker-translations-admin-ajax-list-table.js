(function($) {
	'use strict';

var stListTableAjax = {
	init: function() {
		var timer;
		var delay = 500;

		// Pagination links, sortable link
		$('.st-order-tbl-nav-pages a, .st-order-sort-column.sortable a, .st-order-sort-column.sorted a').on('click', function(e) {
			// We don't want to actually follow these links
			e.preventDefault();
			// Simple way: use the URL to extract our needed variables
			var query = this.search.substring( 1 );

			var data = {
				paged: stListTableAjax.__query( query, 'paged' ) || '1',
				order: stListTableAjax.__query( query, 'order' ) || 'asc',
				orderby: stListTableAjax.__query( query, 'orderby' ) || 'post_title',
				'yoast_data_checked': $('#st-yoast-check').is(':checked'),
                'acf_data_checked': $('#st-acf-check').is(':checked'),
			};
			stListTableAjax.update( data );
		});

		$('.st-delete-item').click(function(e) {
			// We don't want to actually follow these links
			e.preventDefault();
			var wpPostsIDs = document.getElementById( 'st_wp_posts_ids' ),
			itemType = this.getAttribute('data-type'),
			totalType = document.getElementsByClassName('st-total-' + itemType),
			totalItems = document.getElementsByClassName('st-total-items'),
			totalSelected = document.getElementsByClassName('st-total-selected'),
			singlePostID = this.id,
			currentRow = $('#tr-'+ this.id),
			totalItemsDel = $('.st-delete-item').length,
			idsString;
		
			$.confirm({
				title: 'Remove Item!',
				content: 'Are you sure you want to remove this item from selection?',
				type: 'red',
				container: 'body',
				closeIcon: true,
				buttons: {
					removeBtn: {
						text: 'Remove',
						btnClass: 'btn-red',
						action: function(){
							if ( totalItemsDel <= 1 ) {
								$.alert({
									title: 'Remove Item!',
									content : 'There must be atleast one item in the selection',
									closeIcon: true
								});
								$('.st-delete-item').remove();
							} else {
								var data = {
									paged: parseInt( $('input[name=paged]').val() ) || '1',
									order: $('input[name=order]').val() || 'asc',
									orderby: $('input[name=orderby]').val() || 'post_title',
									'yoast_data_checked': $('#st-yoast-check').is(':checked'),
									'acf_data_checked': $('#st-acf-check').is(':checked'),
								};
								totalType[0].innerHTML = totalType[0].innerHTML - 1;
								totalSelected[0].innerHTML = totalSelected[0].innerHTML - 1;
								totalItems[0].innerHTML = totalSelected[0].innerHTML;
								idsString = stListTableAjax.__removeID(wpPostsIDs.value, singlePostID );
								wpPostsIDs.value = idsString;
								currentRow.css({"backgroundColor" : "red", "font-weight":"bold"});
								currentRow.slideUp(400,function() {
									currentRow.remove();
								});
								$('input[id="post_page-'+ singlePostID +'"]').remove();
								stListTableAjax.update( data );
							}
						},
					},
					close: function () {
					},
				}
            });

            return false;
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
				stListTableAjax.update( data );
			}, delay);
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

		var wpPostsIDs = document.getElementById( 'st_wp_posts_ids' ),
			wpPostsTypes = document.getElementById('st_wp_query_types');

		$.ajax({
			// /wp-admin/admin-ajax.php
			url: ajaxurl,
			// Add action and nonce to our collected data
			data: $.extend(
				{
					_st_trans_oredr_ajax_nonce: $('#_st_trans_oredr_ajax_nonce').val(),
					st_wp_posts_ids: wpPostsIDs.value,
					st_wp_posts_types: wpPostsTypes.value,
					action: 'st_order_list_table_ajax',
				},
				data
			),
			beforeSend: function() {
				$('.top .bulkactions').append( '<div class="page-content" id="loader">'+WP_Load_POSTs.content+'<img src="' + WP_Load_POSTs.imgsrc + '"/></div>' );
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
				stListTableAjax.init();
			}
		});
	},
	__removeID: function(stringIDs, idToRemove){
		var elements = stringIDs.split(","),
			remove_index = elements.indexOf( idToRemove) ;

		elements.splice( remove_index , 1);
		var result=elements.join(",");
		return result;
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
}

stListTableAjax.init();

})(jQuery);
