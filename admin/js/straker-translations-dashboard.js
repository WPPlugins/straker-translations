(function($) {
    'use strict';
    var stTranslationDashboard = {
         init: function() {

            // Translation Dashboard Select Item for translations
            var checkboxValues = sessionStorage.getItem( 'st_trans_cart' ),
                arrayOfLocalStorage,
                stTransItems = $('.trans_chkbox'),
                stTransItemsLength = stTransItems.length;
            //     $('#stAddToCartBtn').prop( "disabled", true );
            $('.trans_chkbox').change(function(){
                 var checkBox = $(this),
                   postIDs = [],
                   insertion;
                   postIDs.push( this.value );
                 if ( checkBox.prop( 'checked') === true ) {
                     insertion = true;
                 } else {
                     insertion = false;
                 }
                 stTranslationDashboard.__parseLocalStorage( postIDs, insertion );
            });

            $( '#stCheckAllCheckBox' ).change(function(){

                if ( ! stTransItemsLength < 1 ) {
                    
                    var mainSelectAll = $(this),
                        postIDs = [],
                        insertion,
                        allCheckBoxes =  document.querySelectorAll('.trans_chkbox');
                            $.each( allCheckBoxes, function( index, checkbox ) {
                                postIDs.push( checkbox.value );
                            });

                        if ( mainSelectAll.prop( 'checked') === true ) {
                            insertion = true;
                        } else {
                            insertion = false;
                        }
                    stTranslationDashboard.__parseLocalStorage( postIDs, insertion );
                }
            });
            // Repopulate the translation Cart CheckBoxes
            if ( checkboxValues ) {
                arrayOfLocalStorage = JSON.parse ( sessionStorage.getItem( 'st_trans_cart' ) );
                $.each( arrayOfLocalStorage, function( index, checkbox ) {
                    if ( $("#st-order-" + checkbox)[0] ) {
                         $("#st-order-" + checkbox)[0].checked = true;
                    }
        
                });
            }
             
            // Select All Validation
            $("#sp_all_trans_dash").validate({
                rules: {
                'post_types[]': {
                    required: true,
                    minlength: 1
                },
                'post_status[]': {
                    required: true,
                    minlength: 1
                }
                },
                messages: {
                    'post_types[]': {
                        required: "<label class='st-img-copy'>Please select at least one post type.</label><br />"
                },
                'post_status[]': {
                    required: "<label class='st-img-copy'>Please select at least one post status.</label><br />"
                },
                },
                errorPlacement: function(error, element) {
                    error.appendTo('.st-sendAllDiv');
                }
            });
            /**********Language Filter Script************/
            $('#st-translations-filter').on('click',function(e) {

                e.preventDefault();

                var typeFilter = $('select[name="post-type-filter"]').val();
                var statusFilter = $('select[name="post-status-filter"]').val();
                var languageFilterParamater = '';

                $('.drop-down ul li input[type="checkbox"] ').each(function(){

                    if($(this).attr('checked') !== undefined){

                        languageFilterParamater +=  $(this).val()+',';
                    }

                });

                languageFilterParamater = (languageFilterParamater.length > 0 )? '&post-is_translated-filter='+languageFilterParamater.slice(0,-1) : '';

                document.location.href = 'admin.php?page=st-translation&post-type-filter=' + typeFilter + '&post-status-filter=' + statusFilter + languageFilterParamater

            });

            $('#language-filter').on('click', function(){

                $(this).next().toggle();

            });

            $('body').on('click',function(e){

                var drop_down = $('div.multi-select');

                if(!drop_down.is(e.target) && drop_down.has(e.target).length === 0){

                    if($('#language-filter').next().is(':visible')){

                        $('#language-filter').next().hide();
                    }
                }

            });

            $('.drop-down ul li input[type="checkbox"] ').each(function(){

                $(this).on('click',function(){

                    if($(this).val() !== 'all'){

                        var InputValues = $('#language-filter').val();
                        var TL_Text = $(this).parent().text();

                        if($(this).attr('checked') !== undefined){

                            $('#language-filter').val((InputValues.length === 0)? TL_Text : InputValues+', '+TL_Text);

                        }else{

                            var res = InputValues.replace(TL_Text+', ','').replace(', '+TL_Text,'').replace(TL_Text,'');

                            $('#language-filter').val(res);

                            $('.drop-down ul li:first-of-type input').attr('checked',false);
                        }

                    }

                });

            });

            $('.drop-down ul li:first-of-type input').on('click',function(){

                $('#language-filter').val('');

                if($(this).attr('checked')){

                    $(this).parent().parent().find('input').each(function(){

                        if($(this).val() != 'all'){

                            $(this).attr('checked',false);

                            $(this).click();
                        }
                    });

                }else{

                    $(this).parent().parent().find('input').each(function(){

                        if($(this).val() != 'all'){

                            $(this).attr('checked',false);
                        }
                    });
                }
            });
            // Show Select All
            $('.st_show_hide').click(function(){
                $( '.st-sendAllDiv' ).slideToggle();
            });
            // Seletct Post/Page Validation
            $('#sp_trans_dash').submit(function() {
                
                var localSessionStorage = sessionStorage.getItem( 'st_trans_cart' ); 
                if ( ! localSessionStorage ) {
                    $('div.st-lang').css("display", "block");
                    $('#tagline-description').html ( 'Please select at least one post/page' );
                    $( window ).scrollTop( 0 );
                    return false;
                } else {
                    document.getElementById('st_cart_ids').value = JSON.parse( localSessionStorage ).join(',');
                    sessionStorage.removeItem( 'st_trans_cart' );
                    return true;
                }
            });
         },
         __parseLocalStorage: function ( data, insertion ) {
            
             if ( data ) 
             {
                 if ( insertion ) {
                    stTranslationDashboard.__insertFromLocalStorage( data );
                 } else {
                   stTranslationDashboard.__removeFromLocalStorage( data );
                 }
             }
         },
         __insertFromLocalStorage: function( data ) {
            var localSessionStorage = sessionStorage.getItem( 'st_trans_cart' ),
                newArrayLocalStorage,
                uniqueArrayOfLocalStorage,
                arrayOfLocalStorage;
            if( Array.isArray ( data ) ) {
                if ( localSessionStorage ) {
                    arrayOfLocalStorage = JSON.parse ( sessionStorage.getItem( 'st_trans_cart' ) );
                    newArrayLocalStorage = stTranslationDashboard.__dataAndLocalStorageComaprison( data, arrayOfLocalStorage );
                    uniqueArrayOfLocalStorage = newArrayLocalStorage.filter(function(itm, i, newArrayLocalStorage) {
                        return i == newArrayLocalStorage.indexOf(itm);
                    }); 

                    sessionStorage.setItem ( 'st_trans_cart',  JSON.stringify( uniqueArrayOfLocalStorage ) ) ;
                } else {
                    sessionStorage.setItem( 'st_trans_cart' , JSON.stringify( data ) );
                }
            }
         },
         __removeFromLocalStorage: function( data ) {
            var localSessionStorage = sessionStorage.getItem( 'st_trans_cart' ),
                arrayOfLocalStorage,
                newArray;
            if( Array.isArray ( data ) ) {
                if ( localSessionStorage ) {
                    arrayOfLocalStorage = JSON.parse ( sessionStorage.getItem( 'st_trans_cart' ) );         
                     newArray = arrayOfLocalStorage.filter( function(obj) { 
                         return data.indexOf(obj) == -1;
                     });
     
                    if ( newArray.length > 0 ) {
                        sessionStorage.setItem ( 'st_trans_cart',  JSON.stringify( newArray ) ) ;
                    } else {
                        sessionStorage.removeItem( 'st_trans_cart' );
                    }
                } 
            }   
         },
         __dataAndLocalStorageComaprison: function( data,storage ) {
             var i = 0, newArray = [];
             jQuery.grep(data, function(el) {
                 if (jQuery.inArray(el, storage) == -1) {
                     newArray.push(el);
                }
                i++;
            });
            if ( newArray.length > 0 ) {
                var returnArray = storage.concat(newArray);
                return returnArray;
            } else {
                return storage;
            }
         },
    }
    stTranslationDashboard.init();
})(jQuery);



        