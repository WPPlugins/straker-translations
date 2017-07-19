<?php

class Straker_Util
{
    public function __construct()
    {
    }

    public static function get_meta_by_key_value($key, $value)
    {
        global $wpdb;

        $meta = $wpdb->get_results('SELECT * FROM `'.$wpdb->postmeta."` WHERE meta_key='".esc_sql($key)."' AND meta_value ='".esc_sql($value)."'");

        if (is_array($meta) && !empty($meta) && isset($meta[0])) {
            $meta = $meta[0];
        }
        if (is_object($meta)) {
            return $meta->post_id;
        } else {
            return false;
        }
    }

    public static function get_meta_by_value($value)
    {
        global $wpdb;
        $meta = $wpdb->get_results("SELECT * FROM `".$wpdb->postmeta."` WHERE `meta_key` LIKE '".Straker_Translations_Config::straker_meta_default."%' AND `meta_value` ='".esc_sql($value)."'");

        if (is_array($meta) && !empty($meta) && isset($meta[0])) {
            $aMeta = array();
            foreach ($meta as $key => $value) {
                $aLang = array();
                $aLang['post_id'] = $value->post_id;
                $aLang['default_id'] = $value->meta_value;
                $aPost = get_post_meta($value->post_id, Straker_Translations_Config::straker_meta_locale);
                $langMeta = Straker_Language::straker_language_meta( 'code', $aPost[0] );
                $aLang['name'] = $langMeta['name'];
                $aLang['code'] = $aPost[0];
                array_push($aMeta, $aLang);
            }

            return $aMeta;
        } else {
            return false;
        }
    }

    public static function get_meta_by_key($key)
    {
        global $wpdb;

        $meta = $wpdb->get_results('SELECT * FROM `'.$wpdb->postmeta."` WHERE meta_key='".esc_sql($key)."'");
        if (is_array($meta) && !empty($meta) && isset($meta[0])) {
            $aMeta = array();
            foreach ($meta as $key => $value) {
                $aPost = array();
                $aPost['post_id'] = $value->post_id;
                $aPost['meta_value'] = $value->meta_value;
                array_push($aMeta, $aPost);
            }

            return $aMeta;
        } else {
            return false;
        }
    }

    public static function get_meta_by_post_id($value)
    {
        global $wpdb;
        $meta = $wpdb->get_results('SELECT * FROM `'.$wpdb->postmeta."` WHERE `meta_key` LIKE 'straker_locale' AND `post_id` ='".esc_sql($value)."'");

        if (is_array($meta) && !empty($meta) && isset($meta[0])) {
            $aMeta = array();
            foreach ($meta as $key => $value) {
                $aLang = array();
                $aLang['post_id'] = $value->post_id;
                $aLang['default_id'] = $value->meta_value;
                $aPost = get_post_meta($value->post_id, Straker_Translations_Config::straker_meta_locale);
                $aLang['code'] = $aPost[0];
                array_push($aMeta, $aLang);
            }

            return $aMeta;
        } else {
            return false;
        }
    }

    public static function get_post_permalink_structure($post_type)
    {
        $structure = '';
        if (is_string($post_type)) {
            $pt_object = get_post_type_object($post_type);
        } else {
            $pt_object = $post_type;
        }
        if (!empty($pt_object->rewrite['slug'])) {
            $structure = $pt_object->rewrite['slug'];
        } else {
            $structure = $pt_object->name;
        }

        return $structure;
    }

    public static function get_post_date_front($post_type)
    {
        $structure = self::get_post_permalink_structure($post_type);
        $front = '';
        preg_match_all('/%.+?%/', $structure, $tokens);
        $tok_index = 1;
        foreach ((array) $tokens[0] as $token) {
            if ('%post_id%' == $token && ($tok_index <= 3)) {
                $front = '/date';
                break;
            }
            ++$tok_index;
        }
        return $front;
    }
    public static function get_all_post_types_names()
    {
      $args 		 = array('public' => true,'_builtin' => false);
      $post_types    = get_post_types( $args, 'names' );
      $builtin_types = array('post' => 'post','page' =>'page' );
      return array_merge($post_types,$builtin_types);
    }

    public static function get_translated_post_meta( $post_id, $meta_default ) {
      $wp_query_args = array(
        'post_type'  => get_post_type( $post_id ),
        'meta_query' => array(
          array(
            'key'     => $meta_default,
            'value'   => $post_id,
            'compare' => '=',
          )
        )
      );
      $query = new WP_Query( $wp_query_args );
      if( $query->have_posts() ) {
        while( $query->have_posts() ) {
          $query->the_post();
          $id = get_the_ID();
          return $id;
        }
      }
      return false;
    }

    public static function get_lang_meta_into_array( $meta_array, $lang_code ) {

        $return_array = array();
        if ( ! empty($meta_array ) && is_array( $return_array ) ) {
            foreach ( $meta_array as $key ) {
                if( $key['code'] == $lang_code ) {
                    $return_array['source_id'] = $key['default_id'];
                    $return_array['target_id'] = $key['post_id'] ;
                }
            }
            return  $return_array;
        } else {
            return false;
        }  
    }
  
}
