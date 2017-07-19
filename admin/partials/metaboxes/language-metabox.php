<?php

/**
 * Provide an metabox in posts and pages.
 *
 * This file is used to show the metabox.
 *
 * @link       https://www.strakertranslations.com
 * @since      1.0.0
 */
?>
  <div class="wrap">
  <?php
    $post_meta = get_post_custom( $post->ID );
    $screen    = get_current_screen();
    $post_meta_locale = Straker_Translations_Config::straker_meta_locale;
    $post_straker_target = ( get_post_meta( $post->ID, Straker_Translations_Config::straker_meta_target ) ) ? explode(',', get_post_meta( $post->ID, Straker_Translations_Config::straker_meta_target, true ) ) : array();
    $translation_cart = ( false !== get_option(  Straker_Translations_Config::straker_option_translation_cart ) ) ? explode(',', get_option(  Straker_Translations_Config::straker_option_translation_cart ) ) : false ;
    $default_language = $this->straker_default_language['code'];
    $post_language = isset( $post_meta[ $post_meta_locale ] ) ? esc_attr( $post_meta[ $post_meta_locale ][0] ) : false;
    $post_meta_default = Straker_Translations_Config::straker_meta_default.''.Straker_Language::get_single_shortcode( $post_language );
    $source_post_id = isset( $post_meta[ $post_meta_default ] ) ? esc_attr( $post_meta[ $post_meta_default ][0]) : false;
    $source_post_meta = get_post_meta( $source_post_id, $post_meta_locale, true );
    $translated_posts = Straker_Util::get_meta_by_value( $post->ID );
    $post_type	= get_post_type( $post->ID );
    $lang_meta = Straker_Language::straker_language_meta('code', $post_language );

    // Nonce to verify intention later
    wp_nonce_field( 'straker_save_lang_meta', 'straker_save_lang_meta_nonce' );

    if( $screen->action != 'add' && ! empty( $post_meta[ $post_meta_locale ][0] ) ) {
      
      printf( '<p class="st-tl-post-lang"><label for="post-lang">%s<label for="post-lang"><img src="%s" /><span> %s</span></p>', 
        __( 'Language: ', $this->plugin_name ),
        $this->flags_path . $post_language.'.png',
        $lang_meta['name']
      );
      
      if ( $source_post_id ) {

        printf( '<p class="st-tl-post-lang"> <label for="post-lang">%s</label>', __( 'Original: ', $this->plugin_name ) );
        
        if( $source_post_meta ) {
          
          $source_lang_name = Straker_Language::straker_language_meta( 'code', $source_post_meta );
          printf( '<img src="%s"  st-data-tooltip title="%s" />&nbsp;', 
             $this->flags_path . $source_post_meta.'.png',
            $source_lang_name['name']
          );
        
        }
        
        printf( '<a href="%s" target="_blank">%s</a></p>', 
          get_edit_post_link( $source_post_id ), 
          get_the_title( $source_post_id ) 
        );
      
      }
      
      if ( $translated_posts ) {
        $non_translted_langs = Straker_Translations_Cart_Handling::translate_item_langs( $post->ID, $this->straker_site_languages['tl'], $post_straker_target, $post_type, $this->straker_translation_cart, $this->plugin_name );

        if ( ! empty( $non_translted_langs ) ) {
           echo $non_translted_langs;
        }
        
        printf( '<p> <label for="post-lang">%s</label><br />', __('<strong> Translations</strong>:', $this->plugin_name ) );
        foreach ( $translated_posts as $value ) {
          printf( 
            '<br /><img src="%s" st-data-tooltip title="%s" /> &nbsp;', 
            $this->flags_path . $value['code'].'.png', 
            $value['name'] 
          );
          printf( 
            '<a style="position:relative;top:-7px;" href="%s" target="_blank">%s</a>', 
            get_edit_post_link( $value['post_id'] ), 
            get_the_title( $value['post_id'] ) 
          ); 
        } 
        echo '</p>';
      } else {
        if ( ! $source_post_id && $post_language == $default_language ) {
          $non_translted_langs = Straker_Translations_Cart_Handling::translate_item_langs( $post->ID, $this->straker_site_languages['tl'], $post_straker_target, $post_type, $this->straker_translation_cart, $this->plugin_name );
          echo $non_translted_langs;
        }
      }
    } else {
      
      printf( '<p><label for="post-lang">%s</label> <select name="st_lang_select" id="st_lang_select"> <option value="" selected="selected">%s</option>', 
        __( 'Language:', $this->plugin_name ), 
        __( 'Select Language', $this->plugin_name )
      );
      
      foreach ( $this->straker_def_targ_langs as $val ) {
        printf( '<option value="%s">%s</option>', $val['code'], $val['name'] );
      }
      
      printf( '</select><div class="error" id="st-message" style="display:none;"><p>%s</p></div></p>', 
        __( 'Please select the language of the page.', $this->plugin_name )
      );
    }
    echo '</div>';