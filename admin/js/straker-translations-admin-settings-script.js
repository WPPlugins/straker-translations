(function($) {
	'use strict';
    var stPluginSeetingScript = {
        init: function () {
            /* Settings Langugae Management Tab */
            $("#lang_mang_bulk_action").validate({
                ignore: "",
                rules: {
                    action: {
                        require_from_group: [2, '.st-lang-spost']
                    },
                    st_soruce_post : {
                         require_from_group: [2, '.st-lang-spost']
                    }
                },
                messages: {
                    action: {
                        require_from_group: 'Please select language and source post. If you just want to change language then select No Soucre Post option from Source Post. <br />',
                    },
                    st_soruce_post: {
                        require_from_group: 'Please select language and source post. If you just want to change language then select No Soucre Post option from Source Post. <br />',
                    },
                },
                errorPlacement: function( error, element ) {
                    if (error) {
                        $('#errr-msg').show();
                        $(".st-error").html( error );
                    } else {
                         $('#errr-msg').hide();
                        return true;
                    }
                }
            });

            // Language Management Bulk Filter
            $("#lang_mang_bulk_filter_action").validate({
                rules: {
                    ignore: "",
                    '.filters_lang_type': {
                        required: true,
                        minlength: 1
                    },
                    st_post_type_filter:
                    {
                        require_from_group: [1, '.filters_lang_type']
                    },
                    st_lang_filter:
                    {
                        require_from_group: [1, '.filters_lang_type']
                    },
                    groups:
                    {
                        checks: "st_post_type_filter st_lang_filter"
                    },

                },
                 messages: {
                    st_post_type_filter:{
                        require_from_group: "Please select filter by post types or language.<br />",
                    },
                    st_lang_filter:{
                        require_from_group: "Please select filter by post types or language.<br />",
                    },
                },
                errorPlacement: function( error, element ) {
                    if (error) {
                        $('#errr-msg').show();
                        $(".st-error").html( error );                             
                        
                    } else {
                        $('#errr-msg').hide();
                        return true;
                    }
                }
            });
            // URL Setttings Validate
            $("#url_settings").validate({
                ignore: "",
                rules: {
                    'url[]': {
                        required: function() {
                            return $('#rewrite_type_domain:checked').val() == 'domain';
                        }
                    }
                },
                errorPlacement: function(error, element) {
                    if (error) {
                        $('#errr-msg').css("display", "block");
                        $('.st-error').css("display", "block");
                    } else {
                        return true;
                    }
                }
            });
            // Language Setttings Validate
            $("#language_settings").validate({
                ignore: "",
                rules: {
                    sl: {
                        required: true
                    },
                    'tl[]': {
                        required: true,
                        minlength: 1
                    }
                },
                errorPlacement: function(error, element) {
                    if (error) {
                        $('#errr-msg').css("display", "block");
                        $('.st-error').css("display", "block");
                        $( window ).scrollTop( 0 );
                    } else {
                        return true;
                    }
                }
            });
            $("#rewrite_type_code").click(function() {
                var re_type = $(this).val();
                if ($(this).is(":checked") && re_type == "code") {
                    $(".st-tl-lang").attr("hidden", true );
                    $(".st-url-structure").removeAttr("hidden", true );
                }
            });

            $("#rewrite_type_domain").click(function() {
                var re_type = $(this).val();
                if ($(this).is(":checked") && re_type == "domain") {
                    $(".st-tl-lang").removeAttr("hidden");
                    $(".st-url-structure").attr("hidden", true);
                }
            });

            $("#rewrite_type_none").click(function() {
                var re_type = $(this).val();
                if ($(this).is(":checked") && re_type == "none") {
                    $(".st-tl-lang").attr("hidden", true);
                    $(".st-url-structure").attr("hidden", true);
                }
            });
            // ShortCode Settings
            $("#st_shortcode_settings").validate({
                ignore: "",
                rules: {
                'tl[]': {
                    required: true,
                    minlength: 1
                },
                '.display_flag_lang': {
                    required: true,
                    minlength: 1
                },
                display_flags:
                {
                    require_from_group: [1, '.display_flag_lang']
                },
                display_langs:
                {
                    require_from_group: [1, '.display_flag_lang']
                },
                },
                groups:
                {
                checks: "display_flags display_langs"
                },
                messages: {
                'tl[]': {
                    required: "Please select at least one language.<br />",
                },
                '.display_flag_lang':{
                    required: "Please select display flag or display language.<br />",
                },
                display_langs:{
                    require_from_group: "Please select display flag or display language.<br />",
                },
                },
                errorPlacement: function(error, element) {
                    error.appendTo('#tagline-description');
                }
            });

            // Language Management 
            $(":checkbox.st-lang-manag").on("change", function(e) {
                var st_lang_manag;
                
                if ($.cookie('st_lang_manag')) {
                    st_lang_manag = $.cookie('st_lang_manag');
                    $.removeCookie('st_lang_manag', {path: '/'});
                } else {
                    st_lang_manag = {};
                }

                $(":checkbox.st-lang-manag").each(function(i,v) {
                    st_lang_manag[v.value] = v.checked;
                });

                var date = new Date();
                var minutes = 30;
                date.setTime(date.getTime() + (minutes * 60 * 1000));
                $.cookie('st_lang_manag', st_lang_manag, {
                    expires: date,
                    path: '/'
                });
            });

            // Source Language DropDown
            $('#langDropdown').ddslick({
                width: 300,
                imagePosition: "left",
                onSelected: function(data) {
                    if (data.selectedIndex > 0) {
                        $('#sl').val(data.selectedData.value);
                    } else {
                        $('#sl').val("");
                    }
                }
            });
            // Copy ShortCode from ShortCode Settings Tab
            var clipboard = new Clipboard('.st-cb-cp');
            clipboard.on('success', function(e) {
                $("#st-copied").show().delay(500).fadeOut();
            });
            
            clipboard.on('error', function(e) {
                $('#st-shortcode').addClass('selectText');
                $("#st-copied").show().delay(2500).fadeOut();
                $("#st-copied").html('Press Ctrl/Cmd+C to copy');
            });

            $.cookie.json = true;
            var st_lang_manag = $.cookie('st_lang_manag');
            if (st_lang_manag) {
                Object.keys(st_lang_manag).forEach(function(element) {

                    var checked = st_lang_manag[element];
                    $("#st-lang-manag-" + element).prop('checked', checked);
                });
            }
        },
    };
    stPluginSeetingScript.init();
})(jQuery);
