(function($) {
	'use strict';
    var stPluginMenuSwitcher = {
        init: function () {
            /* Init Global Variables*/
            var menu_switcher_status = $('#lang_menu_switcher_status'),
                menu_switcher_save_button = $('#stMenuSwitcherButton');

            /* Check status of Menu Switcher */
            $("#lang_menu_switcher_status").change(function(){
                  if( $(this).is(":checked") ) {
                    $('.st-box-show-hide').fadeIn( 'slow ');
                  } else {
                    $('.st-box-show-hide').fadeOut( 'slow' );
                  }
            });

            /* Internal Links Navigation */
            $('#st-settings-nav-links a').on('click',function (e) {
                e.preventDefault();

                var targetHash = this.hash,
                    offset = 0,
                    $target = $(targetHash);
                var adminBar = $('#wpadminbar');
                if ( adminBar.length !== 0 ) {
                    offset = adminBar.height();
                }

                $('html, body').stop().animate({
                    'scrollTop': $target.offset().top - offset,
                }, 500, 'swing');
            });            
            
            /* Main Setting Script */
            $("#stMenuSwitcherButton").click(function(e) {
                e.preventDefault();
                var select_menu_options = $('#select_menu_options'),
                    position_of_item = $( "input[name='position_of_items']:checked" ).val(),
                    menu_style = $( "input[name='style_of_items']:checked" ).val(),
                    display_flags = $('#display_flags'),
                    display_language = $('#display_language'),
                    display_flags_lbl = $('#display_flags_lbl'),
                    display_language_lbl = $('#display_language_lbl'),
                    $status = 0;
                if ( menu_switcher_status.is(':checked') ) {
                    if( select_menu_options.val() == '' ) {
                        select_menu_options.addClass('st-svg-field-required');
                        return false;
                    } else {
                        select_menu_options.removeClass('st-svg-field-required');
                    }

                    if( ! $( '.st-general-display' ).is(':checked') ) {
                        display_flags.addClass('st-field-required');
                        display_flags_lbl.addClass('st-svg-field-required');
                        display_language.addClass('st-field-required');
                        display_language_lbl.addClass('st-svg-field-required');
                        return false;
                    } else {
                        display_flags.removeClass('st-field-required');
                        display_flags_lbl.removeClass('st-svg-field-required');
                        display_language.removeClass('st-field-required');
                        display_language_lbl.removeClass('st-svg-field-required');
                    }
                     $status = 1;
                }
                
                 var data = {
                      'action': 'st_language_menu_switcher',
                      'menu_switcher_status': $status,
                      'lang_switcher_menu': ( select_menu_options.val() == '') ? '' : select_menu_options.val(),
                      'menu_style': menu_style,
                      'position_of_item': position_of_item,
                      'display_flags': ( display_flags.is(':checked') ) ? 1 : 0,
                      'display_language': ( display_language.is(':checked') ) ? 1 : 0,
                  };

                  stPluginMenuSwitcher.__stLangSwitcherUpdate( data );
            });
        },
        __stLangSwitcherUpdate: function( data ) {
            $.ajax({
                // /wp-admin/admin-ajax.php
                url: stMenuSwitcherObj.admin_ajax_url,
                // Add action and nonce to our collected data
                data: $.extend(
                    {
                        'st_lang_switcher_nonce': stMenuSwitcherObj.st_lang_switcher_nonce,
                    },
                    data
                ),
                beforeSend: function() {
                     $('#js-st-ms-message .before-saving-msg').fadeIn( 'slow ');
                    // $('#js-st-ms-message').append( '<div class="page-content" id="loader">'+ST_Trans_Cart.content+'<img src="' + ST_Trans_Cart.imgsrc + '"/></div>' );
                },
                // Handle the successful result
                success: function( response ) {
                    $('#js-st-ms-message .before-saving-msg').hide();
                    $('#js-st-ms-message .after-saved-msg').fadeIn( 'slow' );
                    $('#js-st-ms-message .after-saved-msg').slideUp( 2500 ).fadeOut( 2500, 'slow' );
                }
            });
        },
    };
    stPluginMenuSwitcher.init();
})(jQuery);
