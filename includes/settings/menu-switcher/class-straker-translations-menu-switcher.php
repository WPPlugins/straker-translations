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

class Straker_Translations_Menu_Switcher {

	/** 
	 * private static variable for option table
	 */
	private static $switcher_option_name = Straker_Translations_Config::straker_option_menu_switcher;
	private static $straker_all_languages;
	private static $straker_default_language;
	private static $straker_target_languages;
	private static $ST_LINK;

	/**
	 * Save Langauge Switcher Settings
	 *
	 * This ajax based method save the menu language swither settings in the database option.
	 *
	 * @since    1.0.0
	 */
	public static function save_switcher_settings() {

		check_ajax_referer( 'st-lang-switcher-nonce', 'st_lang_switcher_nonce' );
		$menu_status = isset( $_REQUEST['menu_switcher_status'] ) ? sanitize_text_field( $_REQUEST['menu_switcher_status'] ) : false;
		$switcher_menu = isset( $_REQUEST['lang_switcher_menu'] ) ? sanitize_text_field( $_REQUEST['lang_switcher_menu'] ) : false;
		$item_postion = isset( $_REQUEST['position_of_item'] ) ? sanitize_text_field( $_REQUEST['position_of_item'] ) : false;
		$switcher_style = isset( $_REQUEST['menu_style'] ) ? sanitize_text_field( $_REQUEST['menu_style'] ) : false;
		$display_flags = sanitize_text_field( $_REQUEST['display_flags'] );
		$display_language = sanitize_text_field( $_REQUEST['display_language'] );
		$st_language_switcher = Straker_Translations_Config::straker_option_menu_switcher;
		$display_seetings_array = array();

		$st_lang_switcher_option_data	= array(
				'status' => $menu_status,
				'switcher_menu' => $switcher_menu,
				'item_postion' 	=> $item_postion,
				'switcher_style' => $switcher_style,
				'display_flags' => $display_flags,
				'display_language' => $display_language

		);

		if ( ! get_option( $st_language_switcher ) ) {
			add_option( $st_language_switcher, $st_lang_switcher_option_data );
			wp_send_json_success( array( 'isResponse' => true ) );
			wp_die();
		} else {
			update_option( $st_language_switcher, $st_lang_switcher_option_data );
			wp_send_json_success( array( 'isResponse' => true ) );
			wp_die();
		}
	}

	public static function generate_language_switcher_menu_item ( $menu_args, $items ) {

		$menu_response = self::compare_menu_id( $menu_args );
		$swither_settings = self::menu_swither_option();
		if ( $swither_settings['status'] && true == $menu_response['response'] ) {

			self::$straker_all_languages = Straker_Language::get_default_and_target_languages();
			self::$straker_default_language = Straker_Language::get_default_language();
			self::$straker_target_languages   = Straker_Language::get_added_language();
			self::$ST_LINK	= new Straker_Link();
			$lang_url = '';
			
			foreach ( self::$straker_all_languages as $key ) {

				if( $key['code'] == self::$straker_default_language['code'] ) {
					$lang_url = esc_url( self::$ST_LINK->straker_default_home() );
				} else {
					$lang_url = esc_url( self::$ST_LINK->straker_locale_home( $key['wp_locale'] ) );
				}
				$menu_item_list[] = new Straker_Translations_Menu_List_Item( $swither_settings, $key['code'], $key['native_name'], $lang_url, $menu_response['menu_classes'] );
			}
			if ( 'first' == $swither_settings['item_postion'] ) {
				return array_merge( $menu_item_list, $items );
			} else {
				return array_merge( $items, $menu_item_list );
			}
		} else {
			return false;
		}
	}

	private static function compare_menu_id( $menu ) {

		$menu = (object) $menu;
		$menu_id = array();
		$response = 0;
		$menu_classes = '';

		if( isset( $menu->menu ) ){
			if( is_object( $menu->menu ) && isset( $menu->menu->term_id ) ) {
				$menu_id = $menu->menu->term_id;
			} elseif( ! is_object( $menu->menu ) ) {
				$menu_id = $menu->menu;
			}
			$menu_classes = $menu->menu_class;
		}
		if ( self::menu_swither_option() ) {
				 $swither_settings = self::menu_swither_option();
			if (  $swither_settings['switcher_menu'] == $menu_id ) {
				return array( 'response' => 1 , 'menu_classes' => $menu_classes );
			}
		}
		return array( 'response' => $response );
	}

	private static function menu_swither_option() {
		
		if ( get_option( self::$switcher_option_name ) ) {
			return get_option( self::$switcher_option_name );
		} else {
			return false;
		}
	}

}
