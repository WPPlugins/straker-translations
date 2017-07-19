<?php

class Straker_Translations_Cart_Handling {

    /**
	 * The Option name of the Cart.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $cart_option_name    The name of the cart.
	*/

    private static $cart_option_name = Straker_Translations_Config::straker_option_translation_cart;

    public static function add_item_into_cart( $ids ) {

        $cart_option = get_option( self::$cart_option_name );
        if( ! get_option( self::$cart_option_name ) ) {
            update_option( self::$cart_option_name, $ids );
            return true;
        } else {
            update_option( self::$cart_option_name, $cart_option.','.$ids );
            return true;
        }
    }

    public static function remove_item_from_cart( $ids ) {

        if( get_option( self::$cart_option_name ) ) {

            $cart_items = get_option( self::$cart_option_name );

            
            $cart_as_array =  explode(',', get_option(  self::$cart_option_name ) );

            if ( ( $key = array_search( $ids, $cart_as_array ) ) !== false ) {

                unset( $cart_as_array[ $key ] );

                if ( sizeof ( $cart_as_array ) <= 0 ){
                    delete_option( self::$cart_option_name );
                } else {
                    update_option( self::$cart_option_name, implode(',', $cart_as_array ) );
                }
                return true;
            }
        }
    }

    public static function translate_item_langs( $post_id, $target_langs, $post_lang, $post_type, $translation_cart, $text_domain  ) {

        $langs_not_translated = array_diff( $target_langs, $post_lang );
        $langs_cb = '';
        if( false !== $translation_cart && in_array( $post_id, $translation_cart ) ) {
            
            $langs_cb = sprintf('<div class="st-cart-img"><a href="%s"><span st-data-tooltip title="%s"><img class="st-cart-img" src="%s" /></span></a></div>',
               admin_url('admin.php?page=st-translation-cart'),
               __( ucfirst( $post_type ) .' already in the translation cart.', $text_domain ),
               STRAKER_PLUGIN_ABSOLUTE_PATH . '/admin/img/st-cart.png'
            );
            
            return $langs_cb;
          
        } else if ( is_array( $langs_not_translated ) && count( $langs_not_translated ) > 0 && $target_langs !== $post_lang ) {
            
            $transled_text = sprintf( __( 'Translate This %s', $text_domain ) , ucfirst( $post_type )  );
            $langs_cb .= sprintf( '<br /><span class="st-cart-update"><p style="display:none;"></p><button class="button button-primary" id="st-cart-btn">%s</button><input id="stCartPostID" type="hidden" value="%s" /><img style="display:none;" src="%s" /></span>', 
                $transled_text, 
                $post_id,
                STRAKER_PLUGIN_ABSOLUTE_PATH . '/admin/img/loading.gif'
            );

            return $langs_cb;
            
        } else {
            return $langs_cb;
        }
    }
}