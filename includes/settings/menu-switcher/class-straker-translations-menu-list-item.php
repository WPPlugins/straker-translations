<?php

/**
 * Menu Switcher Class
 *
 * @link       https://www.strakertranslations.com
 * @since      1.0.0
 *
 * @package    Straker_Translations
 * @subpackage Straker_Translations/includes
 */

class Straker_Translations_Menu_List_Item {

    public $ID;                           // The term_id if the menu item represents a taxonomy term.
    public $attr_title;                   // The title attribute of the link element for this menu item.
    public $classes = array();            // The array of class attribute values for the link element of this menu item.
    public $db_id;                        // The DB ID of this item as a nav_menu_item object, if it exists (0 if it doesn't exist).
    public $description;                  // The description of this menu item.
    public $menu_item_parent;             // The DB ID of the nav_menu_item that is this item's menu parent, if any. 0 otherwise.
    public $object = 'st_ms_menu_item'; // The type of object originally represented, such as "category," "post", or "attachment."
    public $object_id;                    // The DB ID of the original object this menu item represents, e.g. ID for posts and term_id for categories.
    public $post_parent;                  // The DB ID of the original object's parent object, if any (0 otherwise).
    public $post_title;                   // A "no title" label if menu item represents a post that lacks a title.
    public $post_name;                   // A "no title" label if menu item represents a post that lacks a title.
    public $target;                       // The target attribute of the link element for this menu item.
    public $title;                        // The title of this menu item.
    public $type = 'st_ms_menu_item';   // The family of objects originally represented, such as "post_type" or "taxonomy."
    public $type_label;                   // The singular label used to describe this type of menu item.
    public $url;                          // The URL to which this menu item points.
    public $xfn;                          // The XFN relationship expressed in the link of this menu item.
    public $_invalid = false;             // Whether the menu item represents an object that no longer exist
    public $is_parent;                    // For drop down to set this item as parent
    public $post_type = 'nav_menu_item';

      /**
     * WPML_LS_Menu_Item constructor.
     * @param array  $language
     * @param string $item_content
     */
    public function __construct( $swither_settings, $lang_code, $lang_name, $lang_url, $menu_classes ) {
        
        $this->create_menu_item_object( $swither_settings, $lang_code, $lang_name, $lang_url, $menu_classes );
    }

    /**
     * @param array  $lang
     * @param string $item_content
     */
    private function create_menu_item_object( $swither_settings, $lang_code, $lang_name, $lang_url, $menu_classes ) {
        $straker_default_lang     =  Straker_Language::get_default_language();
        $if_default_lang          = ( $straker_default_lang['code'] == $lang_code ) ? true : false;
        $check_if_dropdown        = ( 'dropdown' == $swither_settings['switcher_style'] ) ? true : false;
        $parent_db_id             = ( $check_if_dropdown && $if_default_lang ) ? $swither_settings['switcher_menu'] . '-' . $straker_default_lang['short_code'] : '';
        $display_lang_name        = ( $swither_settings['display_language'] ) ? $lang_name : '';
        $flag_img                 = sprintf( '<img src="%s" alt="%s" /> ', STRAKER_PLUGIN_ABSOLUTE_PATH. '/assets/img/flags/'.$lang_code.'.png', $lang_code );
        $display_flag             = ( $swither_settings['display_flags'] ) ? $flag_img : '';
        $this->ID                 = isset( $swither_settings['switcher_menu'] ) ? $swither_settings['switcher_menu'] : null;
        $this->object_id          = isset( $swither_settings['switcher_menu'] ) ? $swither_settings['switcher_menu'] : null;
        $this->db_id              = ( $check_if_dropdown && $if_default_lang ) ? $parent_db_id : $swither_settings['switcher_menu'];
        $this->attr_title         = $lang_name;
        $this->title              = $display_flag . $display_lang_name;
        $this->url                = $lang_url;
        $this->post_name          = $lang_code;
        $this->menu_item_parent   = ( $check_if_dropdown && ! $if_default_lang ) ? $swither_settings['switcher_menu'] . '-' . $straker_default_lang['short_code'] : '';
        $this->is_parent          = ( $check_if_dropdown && $if_default_lang ) ? true : false;
        $this->classes            = ( $check_if_dropdown && $if_default_lang ) ?  explode( ' ','menu-item menu-item-has-children') : explode( ' ','menu-item');
        
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function __get( $property ) {
        return isset( $this->{$property} ) ? $this->{$property} : null;
    }

}